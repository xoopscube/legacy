<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

// Xupdate class object
require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

if (!defined('XOOPS_ROOT_PATH')) exit();

class Xupdate_ModulesIniDadaSet
{
	// language file mapping array [from => to]
	private $lang_mapping = array(
			'japanese' => 'ja_utf8'
			);
	
	public $Xupdate  ;	// Xupdate instance
	public $Func ;	// Functions instance

	public $storeHand;
	public $modHand;

	public $stores ;
	private $approved = array() ;
	private $master = array();
	private $allCallers = array('module', 'theme', 'package', 'preload');
	private $cacheTTL = 300; // 5min
	private $itemArrayKeys = array(
		'id',
		'dirname',
		'trust_dirname',
		'version',
		'license',
		'required',
		'last_update',
		'target_key',
		'target_type',
		'replicatable',
		'description',
		'addon_url',
		'detail_url',
		'options',
		'isactive',
		'hasupdate',
		'contents');
	
	
	private $mTagModule;
	
	protected $mSiteObjects = array();
	protected $mSiteItemArray = array();

	public function __construct() {

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		//$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance
		
		$root =& XCube_Root::getSingleton();
		$mAsset =& $root->mContext->mModule->mAssetManager;
		$this->mTagModule = $root->mContext->mModuleConfig['tag_dirname'];
		$this->storeHand =& $mAsset->getObject('handler', 'Store', false);
		$this->modHand = array(
			'module' => $mAsset->getObject('handler', 'ModuleStore', false),
			'theme' => $mAsset->getObject('handler', 'ThemeStore', false),
			'preload' => $mAsset->getObject('handler', 'PreloadStore', false));
		$this->modHand['package'] = $this->modHand['module'];
	}

	public function execute( $callers, $checkonly = false )
	{
		//データの自動作成と削除
		
		$cacheCheckFile = $this->storeHand->getCacheCheckFile();
		$cacheCheckStr = @file_get_contents($cacheCheckFile);
		if ( (!$checkonly && $cacheCheckStr === 'bg_ok')
			|| @ filemtime($cacheCheckFile) + $this->cacheTTL > time() && $cacheCheckStr === ($checkonly? 'bg_ok' : 'ok')
		) {
			return;
		}
		touch($cacheCheckFile);
		
		$root =& XCube_Root::getSingleton();
		$org_lang = $language = $root->mContext->getXoopsConfig('language');
		if (isset($this->lang_mapping[$language])) {
			$language = $this->lang_mapping[$language];
		}
		$downloadDirPath = $this->Xupdate->params['temp_path'];
		$realDirPath = realpath($downloadDirPath);

		// Get store master from xoopscube.net
		$json_url = $root->mContext->mModuleConfig['stores_json_url'];
		$json_fname = 'stores_json.ini.php';
		
		if ($json_url === 'http://xoopscube.net/uploads/xupdatemaster/stores_json.txt') {
			$json_url = 'http://xoopscube.net/uploads/xupdatemaster/stores_json_V1.txt';
		}
		
		$downloadedFilePath = '';
		$stores = array();
		$this->Func->_downloadFile( 'stores_master', $json_url, $json_fname, $downloadedFilePath, $this->cacheTTL );
		if ($downloadedFilePath && ! $stores_json = @ file_get_contents($downloadedFilePath)) {
			// for url fetch failure
			$stores_json = @ file_get_contents(XOOPS_TRUST_PATH . '/modules/xupdate/include/settings/stores.txt');
		}
		if (!$stores = @ json_decode($stores_json, true)) {
			$stores = array();
		} else {
			if (isset($stores['stores'])) {
				$stores_orgin = $stores;
				// stores_json_V1
				// array('stores' => storesArray, 'categories' => categoriesArray)
				$stores = $stores['stores'];
			}
		}
		
		// load my stores ini
		$mystores = array();
		if (is_file(XOOPS_TRUST_PATH.'/settings/xupdate_mystores.ini')) {
			$mystores = @ parse_ini_file(XOOPS_TRUST_PATH.'/settings/xupdate_mystores.ini', true);
		}
		if ($mystores) {
			$stores = array_merge($stores, $mystores);
		}
		//echo('<pre>');var_dump($stores);exit;
		
		// set stores
		$this->stores = array();
		foreach($stores as $store) {
			$this->stores[(int)$store['sid']] = $store;
		}
		//echo('<pre>');var_dump($this->stores);exit;
		
		$multiData = array();
		if (! is_array($callers)) {
			if ($callers === 'package' || $callers === 'all') {
				$callers = $this->allCallers;
			} else {
				$callers = array($callers);
			}
		}
		
		foreach($callers as $caller) {
			$this->_setmStoreObjects( $caller );
	
			foreach($this->stores as $store){
				if ( $store['contents'] !== $caller ) {
					continue;
				}
				$downloadUrl = $store['addon_url'];
				//adump($store);
				switch ($store['contents']){
					case 'theme':
						$target_key = 'themes.ini';
						$tempFilename = 'themes'.(int)$store['sid'].'.ini.php';
						$contents = 'themes';
						break;
					case 'package':
						$target_key = 'package.ini';
						$tempFilename = 'package'.(int)$store['sid'].'.ini.php';
						$contents = 'package';
						break;
					case 'preload':
						$target_key = 'preload.ini';
						$tempFilename = 'preload'.(int)$store['sid'].'.ini.php';
						$contents = 'preload';
						break;
					case 'module':
					default:
						$target_key = 'modules.ini';
						$tempFilename = 'modules'.(int)$store['sid'].'.ini.php';
						$contents = 'modules';
				}
	
				$multiData[] = array(
								'sid'                => $store['sid'],
								'target_key'         => $target_key,
								'downloadUrl'        => $downloadUrl,
								'tempFilename'       => $tempFilename,
								'downloadedFilePath' => '',
								'noRedirect'         => true,
								'caller'             => $caller );
				
				$_dirname = dirname($downloadUrl);
				$_filename = basename($downloadUrl);
				list($_basename) = explode('.', $_filename, 2);
				$downloadLangUrl = $_dirname.'/'.$_basename.'/language/'.$language.'/'.$_filename;
				$tempLangFilename = 'lang_'.$language.'_'.$contents.(int)$store['sid'].'.ini.php';
				
				$multiData[] = array(
								'sid'                => $store['sid'],
								'target_key'         => $target_key,
								'downloadUrl'        => $downloadLangUrl,
								'tempFilename'       => $tempLangFilename,
								'downloadedFilePath' => '',
								'noRedirect'         => true,
								'isLang'             => true );
				
			}
		}
		//echo('<pre>');var_dump($multiData);exit;
		
		$use_mb_convert = function_exists('mb_convert_encoding');
		if ($this->Func->_multiDownloadFile($multiData, $this->cacheTTL)) {
			foreach($multiData as $i => $res) {
				if (isset($res['isLang'])) {
					continue;
				}
				if (file_exists($res['downloadedFilePath'])){
					$downloadedFilePath = $res['downloadedFilePath'];
					if ($items = @ parse_ini_file($downloadedFilePath, true)) {
						$caller = $res['caller'];
						$isPackage = ($caller === 'package');
						$lngKey = $i + 1;
						if (file_exists($multiData[$lngKey]['downloadedFilePath'])){
							$items_lang = @ parse_ini_file($multiData[$lngKey]['downloadedFilePath'], true);
						}
						if (! $items_lang) {
							$items_lang = array();
						}
						$sid = (int)$res['sid'];
						
						// make $this->approved
						$this->approved[$sid] = array();
						$this->master[$sid] = array();
						// $sid >= 10000: My store
						if (!$isPackage && $sid < 10000) {
							foreach($this->stores[$sid]['items'] as $arr) {
								if (is_array($arr) && !empty($arr['approved'])) {
									$this->master[$sid][$arr['target_key']] = true;
								}
							}
						}
						foreach ($items as $key => $check) {
							$_sid = $isPackage? intval(substr($check['dirname'], 1)) : $sid;
							// $sid >= 10000: My store (all approve)
							if ($sid >= 10000 || (isset($this->master[$_sid]) && isset($this->master[$_sid][$check['target_key']]))) {
								$this->approved[$sid][$check['target_key']] = true;
							} else {
								unset($items[$key]);
							}
						}
						$this->_setmSiteModuleObjects($sid, $caller);
						
						$rObjs = array();
						foreach($items as $key => $item){
							if ($isPackage) {
								$_sid = intval(substr($item['dirname'], 1));
								if (!isset($rObjs[$_sid])) {
									$criteria = new CriteriaCompo();
									$criteria->add(new Criteria( 'sid', $_sid ) );
									$_objs = $this->modHand[$caller]->getObjects($criteria, null, null, true);
									foreach($_objs as $id => $mobj) {
										if ($mobj->get('target_type') != 'TrustModule' || $mobj->get('trust_dirname') === $mobj->get('dirname')) {
											$rObjs[$_sid][$mobj->get('target_key')] = $mobj;
										}
									}
									unset($criteria, $_objs);
								}
								$item = $this->_getItemArrFromObj($rObjs[$_sid][$item['target_key']], true);
							} else {
								foreach(array('description', 'tag') as $_key) {
									if (! @ json_encode($item[$_key])) {
										// if not UTF-8
										$item[$_key] = '';
									}
									if (! empty($item[$_key]) && (empty($items_lang[$key]) || empty($items_lang[$key][$_key]))) {
										if (strtoupper(_CHARSET) !== 'UTF-8') {
											$this->encode_numericentity($item[$_key], _CHARSET, 'UTF-8');
											$item[$_key] = mb_convert_encoding($item[$_key], _CHARSET, 'UTF-8');
										}
									}
								}
								if ($caller !== 'module') {
									// get modinfo for non module
									$criteria = new CriteriaCompo();
									$criteria->add(new Criteria( 'sid', $_sid ) );
									$criteria->add(new Criteria( 'target_key',  $item['target_key']) );
									if ($_objs = $this->modHand[$caller]->getObjects($criteria, 1, null, false)) {
										$_obj = array_shift($_objs);
										if ($_obj->modinfo) {
											$item['modinfo'] = $_obj->modinfo;
										}
									}
									unset($criteria, $_objs);
								}
							}
							if (! empty($items_lang[$key]) && isset($this->lang_mapping[$org_lang])) {
								mb_convert_variables(_CHARSET, 'UTF-8', $items_lang[$key]);
							}
							if (! empty($items_lang[$key])) {
								$item = array_merge($item, $items_lang[$key]);
							}
							$item['sid'] = $sid ;
							$item['contents'] = $res['caller'];
							switch($item['target_type']){
								case 'TrustModule':
									$this->_setDataTrustModule($item['sid'] , $item, $caller);
									break;
								case 'X2Module':
								case 'Theme':
								case 'Preload':
								default:
									$this->_setDataSingleModule($item['sid'] , $item, $caller);
							}
						}
					}
				}
			}
			file_put_contents($cacheCheckFile, $checkonly? 'bg_ok' : 'ok');
		} else {
			// Has error
			touch($cacheCheckFile, 0);
			file_put_contents($cacheCheckFile, 'ng');
		}
	}
	
	private function _getItemArrFromObj($obj, $readini = false) {
		$item = array();
		$options = $obj->unserialize_options($readini);
		$item['dirname'] = ($obj->get('target_type') === 'TrustModule')? $obj->get('trust_dirname') : $obj->get('dirname');
		$item['target_key'] = $obj->get('target_key');
		$item['target_type'] = $obj->get('target_type');
		$item['version'] = $obj->get('version')/100;
		$item['detailed_version'] = $options['detailed_version'];
		$item['replicatable'] = $obj->get('replicatable');
		$item['addon_url'] = $obj->get('addon_url');
		$item['detail_url'] = $obj->get('detail_url');
		$item['license'] = $obj->get('license');
		$item['required'] = $obj->get('required');
		$item['description'] = $obj->get('description');
		$item['screen_shot'] = $options['screen_shot'];
		$item['install_only'] = $options['install_only'];
		$item['writable_dir'] = $options['writable_dir'];
		$item['writable_file'] = $options['writable_file'];
		$item['delete_dir'] = $options['delete_dir'];
		$item['delete_file'] = $options['delete_file'];
		if (isset($options['modinfo'])) {
			$item['modinfo'] = $options['modinfo'];
		}
		return $item;
	}

	private function _setmStoreObjects( $caller )
	{
		ksort($this->stores);
		
		//この該当サイト登録済みデータを全部確認する
		$storeObjects =& $this->storeHand->getObjects(null,null,null,true);
		//echo('<pre>');var_dump($storeObjects);exit;
		foreach($storeObjects as $sid => $store){
			if (isset($this->stores[$sid])){
				$oldsobj = clone $store;
				$sObj = $this->stores[$sid];
				unset($sObj['items']);
				$storeObjects[$sid]->assignVars($sObj);
				$this->_StoreUpdate ($storeObjects[$sid] , $oldsobj);
				$this->mSiteObjects[$sid] = $storeObjects[$sid];
			}else{
				//TODO delete ok?
				// delete items
				$criteria = new CriteriaCompo();
				$criteria->add(new Criteria( 'sid', $sid ) );
				$siteModuleStoreObjects =& $this->modHand[$caller]->getObjects($criteria, null, null, true);
				foreach($siteModuleStoreObjects as $id => $mobj){
					$this->modHand[$caller]->delete($mobj ,true);
				}
				// delete store
				if ( !empty($storeObjects[$sid]) ) {
					$this->storeHand->delete($storeObjects[$sid] ,true);
				}
			}
		}
		//echo('<pre>');var_dump($this->mSiteObjects);exit;
		foreach($this->stores as $sid => $store){
			if (!isset($this->mSiteObjects[$sid])){
				$sobj = new $this->storeHand->mClass();
				$sObj = $this->stores[$sid];
				unset($sObj['items']);
				$sobj->assignVars($sObj);
				$sobj->assignVar('reg_unixtime',time());

				$setting_type = $store['setting_type'];
				switch ($setting_type){
					case 'json':
						$sobj->assignVar('setting_type',1);
						break;
					case 'ini':
					default:
					$sobj->assignVar('setting_type',0);
				}
				$sobj->setNew();
				//adump($this->stores[$sname],$sobj);
				$this->storeHand->insert($sobj ,true);
				$this->mSiteObjects[$sid] = $sobj;
			}
		}

	}
/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _StoreUpdate ($obj , $oldobj)
	{
		$newdata['name'] = $obj->getVar('name');
		$newdata['addon_url'] = $obj->getVar('addon_url');
		$newdata['setting_type'] = $obj->getShow('setting_type');
		$newdata['contents'] = $obj->getVar('contents');

		$olddata['name'] = $oldobj->getVar('name');
		$olddata['addon_url'] = $oldobj->getVar('addon_url');
		$olddata['setting_type'] = $oldobj->getVar('setting_type');
		$olddata['contents'] = $oldobj->getVar('contents');

		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->assignVar('reg_unixtime',time());
			$obj->unsetNew();
			$this->storeHand->insert($obj ,true);

			// delete this stores items
			if ($newdata['addon_url'] !== $olddata['addon_url'] || $newdata['contents'] !== $olddata['contents']) {
				$sid = (int)$obj->getVar('sid');
				$criteria = new CriteriaCompo();
				$criteria->add(new Criteria( 'sid', $sid ) );
				$siteModuleStoreObjects =& $this->modHand[$olddata['contents']]->getObjects($criteria, null, null, true);
				foreach($siteModuleStoreObjects as $mobj) {
					$this->modHand[$olddata['contents']]->delete($mobj,true);
				}
			}
		}
	}


//----------------------------------------------------------------------
	private function _setmSiteModuleObjects($sid, $caller)
	{
		//この該当サイト登録済みデータを全部確認する
		$sid = (int)$sid;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'sid', $sid ) );

		$siteModuleStoreObjects =& $this->modHand[$caller]->getObjects($criteria, null, null, true);

		$approved = $this->approved[$sid];
		foreach($siteModuleStoreObjects as $mobj){
			$is_sitedata = false;
			// 承認されたデータがなければ削除
			if (empty($approved[$mobj->getVar('target_key')])) {
				$this->modHand[$caller]->delete($mobj,true);
				continue;
			}

			if (isset($this->mSiteItemArray[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')])){
				//データ重複分は削除
				$this->modHand[$caller]->delete($mobj,true);
			}else{
				$mobj->loadTag();
				$this->mSiteItemArray[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')] = $this->getItemArray($mobj);
			}
		}

	}

	private function _setDataSingleModule($sid , $item, $caller)
	{
		//trustモジュールでない(複製可能なものはどうしよう)
		$item['version']= isset($item['version']) ? round(floatval($item['version'])*100): 0 ;
		$item['replicatable']= isset($item['replicatable']) ? intval($item['replicatable']): 0 ;
		$item['target_key']= isset($item['target_key']) ? $item['target_key']: $item['dirname'] ;
		$item['trust_dirname']= '' ;
		$item['description']= isset($item['description']) ? $item['description']: '' ;
		//$item['unzipdirlevel']= isset($item['unzipdirlevel']) ? intval($item['unzipdirlevel']): 0 ;
		$item['unzipdirlevel'] = 0; // not use "unzipdirlevel"
		$item['addon_url']= isset($item['addon_url']) ? $item['addon_url']: '' ;

		$item = $this->_createItemOptions($item, $caller);

		$mobj = new $this->modHand[$caller]->mClass();
		$mobj->assignVars($item);
		$mobj->assignVar('sid', $sid);

		$mobj->setmModule();

		if (isset($this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']])){
			$mobj->assignVar('id',$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']]['id'] );
			$this->_ModuleStoreUpdate($mobj , $this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']], $caller);
		}else{
			$mobj->setNew();
			$this->modHand[$caller]->insert($mobj ,true);
			$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']] = $this->getItemArray($mobj);
		}
		unset($mobj);

	  }
	  private function _setDataTrustModule($sid ,$item, $caller)
	  {
		  //$sid = (int)$sid;
		  $item['version']= isset($item['version']) ? round(floatval($item['version'])*100): 0 ;
		  $item['replicatable']= isset($item['replicatable']) ? intval($item['replicatable']): 0 ;
		  $item['target_key']= isset($item['target_key']) ? $item['target_key']: $item['dirname'] ;
		  $item['trust_dirname']= isset($item['trust_dirname']) ? $item['trust_dirname']: $item['dirname'] ;
		  $item['description']= isset($item['description']) ? $item['description']: '' ;
		  //$item['unzipdirlevel']= isset($item['unzipdirlevel']) ? intval($item['unzipdirlevel']): 0 ;
		  $item['unzipdirlevel'] = 0; // not use "unzipdirlevel"
		  $item['addon_url']= isset($item['addon_url']) ? $item['addon_url']: '' ;

		  $item = $this->_createItemOptions($item, $caller);

		//インストール済みの同じtrustモージュールのリストを取得
		$list = Legacy_Utils::getDirnameListByTrustDirname($item['trust_dirname']);

		if (empty($list)){
			//インストール済みの同じtrustモージュール無し、注意 is_activeはリストされない
			$mobj = new $this->modHand[$caller]->mClass();
			$mobj->assignVars($item);
			$mobj->assignVar('sid',$sid);

			$mobj->setmModule();

			if (isset($this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']])){
				$mobj->assignVar('id',$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']]['id'] );
				$this->_ModuleStoreUpdate($mobj , $this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']], $caller);
			}else{
				$mobj->setNew();
				$this->modHand[$caller]->insert($mobj ,true);
				$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']] = $this->getItemArray($mobj);
			}
			unset($mobj);

		}else{

			$_isrootdirmodule = false;
			foreach($list as $dirname){
				$mobj = new $this->modHand[$caller]->mClass();
				$mobj->assignVars($item);
				$mobj->assignVar('sid',$sid);
				//same trust_path module
				$mobj->assignVar('dirname',$dirname);

				$mobj->setmModule();

				if ( $dirname == $item['dirname'] ){
					$_isrootdirmodule = true;
				}
				if (isset($this->mSiteItemArray[$sid][$item['target_key']][$dirname])){
					$mobj->assignVar('id',$this->mSiteItemArray[$sid][$item['target_key']][$dirname]['id'] );
					$this->_ModuleStoreUpdate($mobj , $this->mSiteItemArray[$sid][$item['target_key']][$dirname], $caller);
				}else{
					$mobj->setNew();
					$this->modHand[$caller]->insert($mobj ,true);
					$this->mSiteItemArray[$sid][$item['target_key']][$dirname] = $this->getItemArray($mobj);
				}
				unset($mobj);
			}
			//そのままインストールしていない場合、そのまま追加可能なので
			if ( $_isrootdirmodule == false ){
				$mobj = new $this->modHand[$caller]->mClass();
				$mobj->assignVars($item);
				$mobj->assignVar('sid',$sid);

				$mobj->setmModule();

				if (isset($this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']])){
					$mobj->assignVar('id',$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']]['id'] );
					$this->_ModuleStoreUpdate($mobj , $this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']], $caller);
				}else{
					$mobj->setNew();
					$this->modHand[$caller]->insert($mobj ,true);
					$this->mSiteItemArray[$sid][$item['target_key']][$item['dirname']] = $this->getItemArray($mobj);
				}
				unset($mobj);
			}
		}

	}

	private function _createItemOptions( $item, $caller )
	{
		static $mVars;
		if (is_null($mVars)) {
			$mobj = new $this->modHand[$caller]->mClass();
			$mVars = $mobj->mVars;
			unset($mobj);
		}
		
		if(isset($item['writable_file'])
		|| isset($item['writable_dir'])
		|| isset($item['install_only'])
		|| isset($item['delete_file'])
		|| isset($item['delete_dir'])){
			$item_arr=array();
			if(isset($item['writable_file'])){
				$item_arr['writable_file'] = array_filter($item['writable_file'], 'strlen');
			}
			if(isset($item['writable_dir'])){
				$item_arr['writable_dir'] = array_filter($item['writable_dir'], 'strlen');
			}
			if(isset($item['install_only'])){
				$item_arr['install_only'] = array_filter($item['install_only'], 'strlen');
			}
			if(isset($item['delete_file'])){
				$item_arr['delete_file'] = array_filter($item['delete_file'], 'strlen');
			}
			if(isset($item['delete_dir'])){
				$item_arr['delete_dir'] = array_filter($item['delete_dir'], 'strlen');
			}
		}
		if(isset($item['detailed_version'])){
			$item_arr['detailed_version'] = $item['detailed_version'] ;
		} else {
			$item_arr['detailed_version'] = '';
		}
		if(isset($item['screen_shot'])){
			$item_arr['screen_shot'] = $item['screen_shot'] ;
		} else {
			$item_arr['screen_shot'] = '' ;
		}
		if(isset($item['changes_url'])){
			$item_arr['changes_url'] = $item['changes_url'] ;
		} else {
			$item_arr['changes_url'] = '' ;
		}
		if(isset($item['modinfo'])){
			$item_arr['modinfo'] = $item['modinfo'] ;
		}
		
		// check tag is UTF-8 with json_encode
		if ($this->mTagModule && isset($item['tag'])) {
			$tag = trim($item['tag']);
			$tag = preg_replace('/\s+/', ' ', $tag);
		} else {
			$tag = '' ;
		}
		
		$item['options']= serialize($item_arr) ;
		
		// clean up
		foreach(array_keys($item) as $key) {
			if (! isset($mVars[$key])) {
				unset($item[$key]);
			}
		}
		
		$item['tag'] = $tag;
		
		return $item;
	}
/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _ModuleStoreUpdate ($obj, $olddata, $caller)
	{
		$newdata = $this->getItemArray($obj);
		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->modHand[$caller]->insert($obj ,true);
		}
	}
	
	private function getItemArray($obj) {
		$data = array();
		foreach($this->itemArrayKeys as $key) {
			$data[$key] = $obj->getVar($key);
		}
		if ($obj->mTag) {
			$data['tag'] = join(' ', $obj->mTag);
		} else {
			$data['tag'] = '';
		}
		return $data;
	}

	function encode_numericentity(& $arg, $toencode, $fromencode, $keys = array()) {
		$fromencode = strtoupper($fromencode);
		$toencode = strtoupper($toencode);
		if ($fromencode === $toencode || $toencode === 'UTF-8') return;
		if ($toencode === 'EUC-JP') $toencode = 'eucJP-win';
		if (is_array($arg)) {
			foreach (array_keys($arg) as $key) {
				if (!$keys || in_array($key, $keys)) {
					$this->encode_numericentity($arg[$key], $toencode, $fromencode, $keys);
				}
			}
		} else {
			if ($arg === mb_convert_encoding(mb_convert_encoding($arg, $toencode, $fromencode), $fromencode, $toencode)) {
				return;
			}
			if (extension_loaded('mbstring')) {
				$_sub = mb_substitute_character();
				mb_substitute_character('long');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/', "\x08$1", $arg);
				if ($fromencode !== 'UTF-8') $arg = mb_convert_encoding($arg, 'UTF-8', $fromencode);
				$arg = mb_convert_encoding($arg, $toencode, 'UTF-8');
				$arg = preg_replace('/U\+([0-9A-F]{2,5})/e', '"&#".base_convert("$1",16,10).";"', $arg);
				$arg = preg_replace('/\x08([0-9A-F]{2,5})/', 'U+$1', $arg);
				mb_substitute_character($_sub);
				$arg = mb_convert_encoding($arg, $fromencode, $toencode);
			} else {
				$str = '';
				$max = mb_strlen($arg, $fromencode);
				$convmap = array(0x0080, 0x10FFFF, 0, 0xFFFFFF);
				for ($i = 0; $i < $max; $i++) {
					$org = mb_substr($arg, $i, 1, $fromencode);
					if ($org === mb_convert_encoding(mb_convert_encoding($org, $toencode, $fromencode), $fromencode, $toencode)) {
						$str .= $org;
					} else {
						$str .= mb_encode_numericentity($org, $convmap, $fromencode);
					}
				}
				$arg = $str;
			}
		}
		return;
	}
} // end class

?>
