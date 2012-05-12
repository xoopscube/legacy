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

	public $Xupdate  ;	// Xupdate instance
	public $Func ;	// Functions instance

	public $storeHand;
	public $modHand;

	public $stores ;
	public $items ;

	protected $mSiteObjects = array();
	protected $mSiteModuleObjects = array();

	public function __construct() {

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		//$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance

	}

	public function execute( $caller )
	{
//データの自動作成と削除

		$root =& XCube_Root::getSingleton();
		$language = $root->mContext->getXoopsConfig('language');
		$downloadDirPath = $this->Xupdate->params['temp_path'];
		$realDirPath = realpath($downloadDirPath);

		// temp start
		// 取り急ぎ、ローカルファイルから作成したが、
		// 最終的には、xoopscube.netにマスタファイルをjson形式で置き、
		// それを読み出すこととなる。
		//	include dirname(__FILE__) . '/stores.inc.php';
		//	$this->stores = $stores;
		//	$json_fname = $downloadDirPath . '/stores.txt';
		//	$fp = fopen($json_fname, "w");
		//	$_json = json_encode($stores);
		//	fwrite($fp, $_json);
		//	fclose($fp);

		//このストアーごとのアイテム配列をセットしてください
		//登録済のデータをマージします
		//未登録のデータは自動で登録

		$json_fname = dirname(__FILE__) . '/settings/stores.txt';

		$fp = fopen($json_fname, "r");
		$_read_str = fread($fp, filesize($json_fname));
		fclose($fp);
		$stores = json_decode($_read_str, true);
			//adump($stores);
		$this->stores = $stores;
		$this->_setmStoreObjects( $caller );
			//adump($this->stores);
			//adump($this->mSiteObjects);

		$json_fname = dirname(__FILE__) . '/settings/contents.txt';
		$fp = fopen($json_fname, "r");
		$fread_ = fread($fp, filesize($json_fname));
		$arr_master = json_decode($fread_, true);
		fclose($fp);
			//adump($fread_, $arr_master);

		// temp end

		$downloadedFilePath = '';
		foreach($this->stores as $sname => $store){
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
				case 'module':
				default:
					$target_key = 'modules.ini';
					$tempFilename = 'modules'.(int)$store['sid'].'.ini.php';
					$contents = 'modules';
			}

			// make language directory
			$languagePath = $this->Func->makeDirectory( $downloadDirPath, 'language' );
			$languageEachPath = $this->Func->makeDirectory( $languagePath, $language );

			if ($this->Func->_downloadFile($target_key, $downloadUrl, $tempFilename, $downloadedFilePath) && file_exists(($downloadedFilePath))){
				$_pathinfo = pathinfo( $downloadUrl);
				$_dirname = $_pathinfo['dirname'];
				$_filename = $_pathinfo['filename'];
				$downloadLangUrl = $_dirname. '/'.$_filename.'/language/'.$language.'/'.$_filename.'.ini';
				$tempLangFilename = 'language/'.$language.'/'.$contents.(int)$store['sid'].'.ini.php';
					//adump($downloadLangUrl, $tempLangFilename);
				if ($this->Func->_downloadFile($target_key, $downloadLangUrl, $tempLangFilename, $downloadedLangFilePath) && file_exists(($downloadedLangFilePath))){
					//adump($downloadUrl,$downloadedFilePath);
					$this->_setmSiteModuleObjects($store['sid'], $caller);
					$items = parse_ini_file($downloadedFilePath, true);
					$items_lang = parse_ini_file($downloadedLangFilePath, true);
					//adump($items, $items_lang);
					foreach($items as $key => $item){
						//adump($store['sid'],$item['target_key']);
							if (isset($arr_master[$store['sid']][$item['target_key']])){
							$master = $arr_master[$store['sid']][$item['target_key']];
							if ( $item['target_key'] == $master['target_key']  && $master['approved'] == 'true' ) {
								//adump($store['sid'],$key);
								$item['sid'] = $store['sid'] ;
								$item['description'] = isset($items_lang[$item['target_key']]['description']) ? $items_lang[$item['target_key']]['description'] : '' ;
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

	}


	private function _setmStoreObjects( $caller )
	{
		//この該当サイト登録済みデータを全部確認する
		$storeObjects =& $this->storeHand->getObjects(null,null,null,true);

		foreach($this->stores as $sname => $store){
			$sid = (int)$store['sid'];
			if (isset($storeObjects[$sid])){
				$oldsobj = clone $storeObjects[$sid];
				$storeObjects[$sid]->assignVars($store);
				$this->_StoreUpdate ($storeObjects[$sid] , $oldsobj);
				$this->mSiteObjects[$sid]=$storeObjects[$sid];
			}else{

				//TODO delete ok?
				$criteria = new CriteriaCompo();
				if ($caller === 'theme'){
					$criteria->add(new Criteria( 'target_type', 'Theme' ) );
				} else {
					$cri_compo = new CriteriaCompo();
					$cri_compo->add(new Criteria( 'target_type', 'TrustModule' ) );
					$cri_compo->add(new Criteria( 'target_type', 'X2Module'), 'OR' ) ;
					$criteria->add( $cri_compo );
				}
				if ( !empty($sid) ){
					$criteria->add(new Criteria( 'sid', $sid ) );
				}
				$siteModuleStoreObjects =& $this->modHand->getObjects($criteria);

				foreach($siteModuleStoreObjects as $id => $mobj){
					$this->modHand->delete($mobj ,true);
				}
				$this->storeHand->delete($storeObjects[$sid] ,true);
			}
		}

		foreach($this->stores as $sname => $store){
			$sid=(int)$store['sid'];
			if (!isset($this->mSiteObjects[$sid]) && $this->stores[$sname]){
				$sobj = new $this->storeHand->mClass();
				$sobj->assignVars($this->stores[$sname]);
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
				//$this->mSiteObjects[$sid] = $sobj;
				$this->mSiteObjects[$sname] = $sobj;
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

		$olddata['name'] = $oldobj->getVar('name');
		$olddata['addon_url'] = $oldobj->getVar('addon_url');
		$olddata['setting_type'] = $oldobj->getVar('setting_type');

		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->assignVar('reg_unixtime',time());
			$obj->unsetNew();
			$this->storeHand->insert($obj ,true);
		}

	}


//----------------------------------------------------------------------
	private function _setmSiteModuleObjects($sid = null, $caller)
	{
		//この該当サイト登録済みデータを全部確認する
		$criteria = new CriteriaCompo();
		if ($caller === 'theme'){
			$criteria->add(new Criteria( 'target_type', 'Theme' ) );
		} else {
			$cri_compo = new CriteriaCompo();
			$cri_compo->add(new Criteria( 'target_type', 'TrustModule' ) );
			$cri_compo->add(new Criteria( 'target_type', 'X2Module'), 'OR' ) ;
			$criteria->add( $cri_compo );
		}
		if ( !empty($sid) ){
			$criteria->add(new Criteria( 'sid', (int)$sid ) );
		}
		$siteModuleStoreObjects =& $this->modHand->getObjects($criteria, null, null, true);

		if (empty($siteModuleStoreObjects)){
		//	return;
		}

		foreach($siteModuleStoreObjects as $sid => $mobj){
			if (isset($this->items[$sid])){
				$is_sitedata = false;
				foreach($this->items[$sid] as $key => $item){
					if ( $item['dirname'] == $mobj->getVar('dirname')
						|| $item['dirname'] == $mobj->getVar('trust_dirname') ){
						$is_sitedata = true;
						break;
					}
				}
				//このサイトデータに無い
				if ($is_sitedata == false){
					$this->modHand->delete($mobj,true);
					continue;
				}
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
		if(function_exists('mb_convert_encoding')){
			if ('UTF-8' != _CHARSET){
				$item['description'] = mb_convert_encoding($item['description'] , _CHARSET , 'UTF-8');
			}
		}
		$item['unzipdirlevel']= isset($item['unzipdirlevel']) ? intval($item['unzipdirlevel']): 0 ;
		$item['addon_url']= isset($item['addon_url']) ? $item['addon_url']: '' ;

		if(isset($item['writable_file']) || isset($item['writable_dir']) || isset($item['install_only'])){
			$item_arr=array();
			if(isset($item['writable_file'])){
				$item_arr['writable_file']= $item['writable_file'] ;
				unset ($item['writable_file']);
			}
			if(isset($item['writable_dir'])){
				$item_arr['writable_dir']= $item['writable_dir'] ;
				unset ($item['writable_dir']);
			}
			if(isset($item['install_only'])){
				$item_arr['install_only']= $item['install_only'] ;
				unset ($item['install_only']);
			}
			$item['options']= serialize($item_arr) ;
			//adump($item['options']);
		} else{
			$item['options']= '';
		}

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
		if(function_exists('mb_convert_encoding')){
			if ('UTF-8' != _CHARSET){
				$item['description'] = mb_convert_encoding($item['description'] , _CHARSET , 'UTF-8');
			}
		}
		$item['unzipdirlevel']= isset($item['unzipdirlevel']) ? intval($item['unzipdirlevel']): 0 ;
		$item['addon_url']= isset($item['addon_url']) ? $item['addon_url']: '' ;

		if(isset($item['writable_file']) || isset($item['writable_dir']) || isset($item['install_only'])){
			$item_arr=array();
			if(isset($item['writable_file'])){
				$item_arr['writable_file']= $item['writable_file'] ;
				unset ($item['writable_file']);
			}
			if(isset($item['writable_dir'])){
				$item_arr['writable_dir']= $item['writable_dir'] ;
				unset ($item['writable_dir']);
			}
			if(isset($item['install_only'])){
				$item_arr['install_only']= $item['install_only'] ;
				unset ($item['install_only']);
			}
			$item['options']= serialize($item_arr) ;
			//adump($item['options']);
		} else{
			$item['options']= '';
		}

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
				if (isset($this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']])){
					$mobj->assignVar('id',$this->mSiteModuleObjects[$sid][$item['target_key']][$dirname]->getVar('id') );
					$this->_ModuleStoreUpdate($mobj , $this->mSiteModuleObjects[$sid][$item['target_key']][$item['dirname']]);
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

		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->modHand->insert($obj ,true);
		}

	}

} // end class

?>
