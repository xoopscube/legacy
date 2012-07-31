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
	private $allCallers = array('module', 'theme', 'package');
	
	protected $mSiteObjects = array();
	protected $mSiteModuleObjects = array();

	public function __construct() {

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		//$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance

	}

	public function execute( $callers )
	{
//データの自動作成と削除

		$root =& XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
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
		$this->Func->_downloadFile( 'stores_master', $json_url, $json_fname, $downloadedFilePath, 600 );
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
		
		$cacheTTL = 600; //10min
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
		if ($this->Func->_multiDownloadFile($multiData, $cacheTTL)) {
			foreach($multiData as $i => $res) {
				if (isset($res['isLang'])) {
					continue;
				}
				if (file_exists($res['downloadedFilePath'])){
					$downloadedFilePath = $res['downloadedFilePath'];
					if ($items = @ parse_ini_file($downloadedFilePath, true)) {
						$isPackage = ($res['caller'] === 'package');
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
						$this->_setmSiteModuleObjects($sid);
						
						$rObjs = array();
						foreach($items as $key => $item){
							if ($isPackage) {
								$_sid = intval(substr($item['dirname'], 1));
								if (!isset($rObjs[$_sid])) {
									$criteria = new CriteriaCompo();
									$criteria->add(new Criteria( 'sid', $_sid ) );
									$_objs =& $this->modHand->getObjects($criteria, null, null, true);
									foreach($_objs as $id => $mobj){
										$rObjs[$_sid][$mobj->get('target_key')] = $mobj;
									}
									unset($criteria, $_objs);
								}
								$item = $this->_getItemArrFromObj($rObjs[$_sid][$item['target_key']]);
							}
							$item['sid'] = $sid ;
							$item['contents'] = $res['caller'];
							$item['description'] = (isset($items_lang[$key]) && isset($items_lang[$key]['description'])) ? $items_lang[$key]['description']
							                     : (isset($item['description'])? $item['description'] : '') ;
							if ($item['description'] && $use_mb_convert && 'UTF-8' != _CHARSET) {
								$item['description'] = mb_convert_encoding($item['description'] , _CHARSET , 'UTF-8');
							}
							switch($item['target_type']){
								case 'TrustModule':
									$this->_setDataTrustModule($item['sid'] , $item);
									break;
								case 'X2Module':
								case 'Theme':
								default:
									$this->_setDataSingleModule($item['sid'] , $item);
							}
						}
					}
				}
			}
		}
	}
	
	private function _getItemArrFromObj($obj) {
		$item = array();
		$options = $obj->unserialize_options();
		$item['dirname'] = $obj->get('dirname');
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
				$siteModuleStoreObjects =& $this->modHand->getObjects($criteria, null, null, true);
				foreach($siteModuleStoreObjects as $id => $mobj){
					$this->modHand->delete($mobj ,true);
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
				$siteModuleStoreObjects =& $this->modHand->getObjects($criteria, null, null, true);
				foreach($siteModuleStoreObjects as $mobj) {
					$this->modHand->delete($mobj,true);
				}
			}
		}
	}


//----------------------------------------------------------------------
	private function _setmSiteModuleObjects($sid)
	{
		//この該当サイト登録済みデータを全部確認する
		$sid = (int)$sid;
		$criteria = new CriteriaCompo();
		//if ($caller === 'theme'){
		//	$criteria->add(new Criteria( 'target_type', 'Theme' ) );
		//} else {
		//	$cri_compo = new CriteriaCompo();
		//	$cri_compo->add(new Criteria( 'target_type', 'TrustModule' ) );
		//	$cri_compo->add(new Criteria( 'target_type', 'X2Module'), 'OR' ) ;
		//	$criteria->add( $cri_compo );
		//}
		$criteria->add(new Criteria( 'sid', $sid ) );

		$siteModuleStoreObjects =& $this->modHand->getObjects($criteria, null, null, true);

		$approved = $this->approved[$sid];
		foreach($siteModuleStoreObjects as $mobj){
			$is_sitedata = false;
			// 承認されたデータがなければ削除
			if (empty($approved[$mobj->getVar('target_key')])) {
				$this->modHand->delete($mobj,true);
				continue;
			}

			if (isset($this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')])){
				//データ重複分は削除
				$this->modHand->delete($mobj,true);
			}else{
				$this->mSiteModuleObjects[$mobj->getVar('sid')][$mobj->getVar('target_key')][$mobj->getVar('dirname')]=$mobj;
			}
		}

	}

	private function _setDataSingleModule($sid , $item)
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

		$item = $this->_createItemOptions($item);

		$mobj = new $this->modHand->mClass();
		$mobj->assignVars($item);
		$mobj->assignVar('sid', $sid);

		$mobj->setmModule();

		if (isset($this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']])){
			$mobj->assignVar('id',$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]->getVar('id') );
			$this->_ModuleStoreUpdate($mobj , $this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]);
		}else{
			$mobj->setNew();
			$this->modHand->insert($mobj ,true);
			$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']] = $mobj;
		}
		unset($mobj);

	  }
	  private function _setDataTrustModule($sid ,$item)
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

		  $item = $this->_createItemOptions($item);

		//インストール済みの同じtrustモージュールのリストを取得
		$list = Legacy_Utils::getDirnameListByTrustDirname($item['trust_dirname']);

		if (empty($list)){
			//インストール済みの同じtrustモージュール無し、注意 is_activeはリストされない
			$mobj = new $this->modHand->mClass();
			$mobj->assignVars($item);
			$mobj->assignVar('sid',$sid);

			$mobj->setmModule();

			if (isset($this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']])){
				$mobj->assignVar('id',$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]->getVar('id') );
				$this->_ModuleStoreUpdate($mobj , $this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]);
			}else{
				$mobj->setNew();
				$this->modHand->insert($mobj ,true);
				$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']] = $mobj;
			}
			unset($mobj);

		}else{

			$_isrootdirmodule = false;
			foreach($list as $dirname){
				$mobj = new $this->modHand->mClass();
				$mobj->assignVars($item);
				$mobj->assignVar('sid',$sid);
				//same trust_path module
				$mobj->assignVar('dirname',$dirname);

				$mobj->setmModule();

				if ( $dirname == $item['dirname'] ){
					$_isrootdirmodule = true;
				}
				if (isset($this->mSiteModuleObjects[$sid][$item['target_key']][$dirname])){
					$mobj->assignVar('id',$this->mSiteModuleObjects[$sid][$item['target_key']][$dirname]->getVar('id') );
					$this->_ModuleStoreUpdate($mobj , $this->mSiteModuleObjects[$sid][$item['target_key']][$dirname]);
				}else{
					$mobj->setNew();
					$this->modHand->insert($mobj ,true);
					$this->mSiteModuleObjects[$sid][$item['target_key']][$dirname] = $mobj;
				}
				unset($mobj);
			}
			//そのままインストールしていない場合、そのまま追加可能なので
			if ( $_isrootdirmodule == false ){
				$mobj = new $this->modHand->mClass();
				$mobj->assignVars($item);
				$mobj->assignVar('sid',$sid);

				$mobj->setmModule();

				if (isset($this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']])){
					$mobj->assignVar('id',$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]->getVar('id') );
					$this->_ModuleStoreUpdate($mobj , $this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]);
				}else{
					$mobj->setNew();
					$this->modHand->insert($mobj ,true);
					$this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']] = $mobj;
				}
				unset($mobj);
			}
		}

	}

	private function _createItemOptions( $item )
	{
		static $mVars;
		if (is_null($mVars)) {
			$mobj = new $this->modHand->mClass();
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
		$item['options']= serialize($item_arr) ;
		
		// clean up
		foreach(array_keys($item) as $key) {
			if (! isset($mVars[$key])) {
				unset($item[$key]);
			}
		}
		
		return $item;
	}
/*
 * このサイトのデータをデータベースに再セットする
 */
	private function _ModuleStoreUpdate ($obj , $oldobj)
	{
		$newdata['dirname'] = $obj->getVar('dirname');
		$newdata['trust_dirname'] = $obj->getVar('trust_dirname');
		$newdata['target_key'] = $obj->getVar('target_key');
		$newdata['target_type'] = $obj->getVar('target_type');
		$newdata['last_update'] = $obj->getVar('last_update');
		$newdata['version'] = $obj->getVar('version');
		$newdata['description'] = $obj->getVar('description');
		$newdata['unzipdirlevel'] = $obj->getVar('unzipdirlevel');
		$newdata['addon_url'] = $obj->getVar('addon_url');
		$newdata['options'] = $obj->getVar('options');
		$newdata['isactive'] = $obj->getVar('isactive');
		$newdata['hasupdate'] = $obj->getVar('hasupdate');
		$newdata['contents'] = $obj->getVar('contents');

		$olddata['dirname'] = $oldobj->getVar('dirname');
		$olddata['trust_dirname'] = $oldobj->getVar('trust_dirname');
		$olddata['target_key'] = $oldobj->getVar('target_key');
		$olddata['target_type'] = $oldobj->getVar('target_type');
		$olddata['last_update'] = $oldobj->getVar('last_update');
		$olddata['version'] = $oldobj->getVar('version');
		$olddata['description'] = $oldobj->getVar('description');
		$olddata['unzipdirlevel'] = $oldobj->getVar('unzipdirlevel');
		$olddata['addon_url'] = $oldobj->getVar('addon_url');
		$olddata['options'] = $oldobj->getVar('options');
		$olddata['isactive'] = $oldobj->getVar('isactive');
		$olddata['hasupdate'] = $oldobj->getVar('hasupdate');
		$olddata['contents'] = $oldobj->getVar('contents');

		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->modHand->insert($obj ,true);
		}

	}

} // end class

?>
