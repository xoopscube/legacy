<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';
/**
* XoopsSimpleObject
*/
class Xupdate_ModuleStore extends Xupdate_Root {

	public $mModule ;
	public $modinfo = array();

	public function __construct()
	{
		parent::__construct() ;

		//項目
		$this->initVar('id', XOBJ_DTYPE_INT, '0', false);//Primary key
		$this->initVar('sid', XOBJ_DTYPE_INT, '0', false);//store join

		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('type', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('version', XOBJ_DTYPE_INT, 100, false);
		$this->initVar('last_update', XOBJ_DTYPE_INT, null, false);
		$this->initVar('trust_dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('rootdirname', XOBJ_DTYPE_STRING, '', false);

	}

	/**
	 * @return string
	 */
	function getRenderedVersion()
	{
		return sprintf('%01.2f', $this->getVar('version') / 100);
	}
	/**
	 * @
	 */
	public function setmModule()
	{
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$this->mModule =& $hModule->getByDirname($this->getVar('dirname')) ;
		if (is_object($this->mModule)){
			$this->modinfo =& $this->mModule->getInfo();
		}else{
			$this->mModule = new XoopsModule();//空のobject
			$this->mModule->cleanVars();
		}
	}
	/**
	 * @return bool
	 */
	public function isTrustDirnameError()
	{
		$ret = false;
		if ( $this->getVar('type') == 'TrustModule' ){
			$trust_dirname = $this->mModule->getVar('trust_dirname');
			if ( !empty($trust_dirname) && $this->getVar('trust_dirname') != $trust_dirname
				&& (isset($this->modinfo['trust_dirname']) && $this->getVar('trust_dirname') != $this->modinfo['trust_dirname'] ) ){
				$ret = true;
			}
		}
		return $ret;
	}
	/**
	 * @return bool
	 */
	public function hasNeedUpdate()
	{
		if (empty($this->modinfo)){
			return false;
		}else{
			return ($this->getVar('version') < Legacy_Utils::convertVersionFromModinfoToInt($this->modinfo['version']));
		}
	}

	public function get_StoreUrl()
	{
		//TODO for test dirname ?
		$ret = XOOPS_URL .'/modules/xupdate/admin/index.php?action=ModuleInstall&target_key='
			.$this->getVar('dirname') .'&target_type='.$this->getVar('type');
		return $ret;
	}
	public function get_InstallUrl()
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}
	public function get_UpdateUrl()
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}

} // end class

/**
* XoopsObjectGenericHandler extends
*/
class Xupdate_ModuleStoreHandler extends XoopsObjectGenericHandler
{
//	public $mTable = '{dirname}_modulestore';//TODO not datbase ,test now ダミーです
	public $mTable = 'xupdate_modulestore';//TODO not datbase ,test now ダミーです
	public $mPrimary = 'id';
	//XoopsSimpleObject
	public $mClass = 'Xupdate_ModuleStore';

	public $items = array();
	public $store = array();
	public $mSiteModuleObjects = array();

	public function Xupdate_ModuleStoreHandler(&$db) {

		parent::__construct($db);

		//for test start ---------------------------
		$this->_storelist();
		//for test end ---------------------------

		//テストのためこの該当サイト登録済みデータを全部確認する
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria( 'sid', $this->store['sid'] ) );
		asynop('XoopsObjectHandler');
		$siteModuleStoreObjects = parent::getObjects($criteria);
		foreach($siteModuleStoreObjects as $key => $mobj){
			$_ismodulestore = false;
			if ($mobj->getVar('type') == 'TrustModule' ){
				foreach($this->items as $key => $myrow){
					if ($mobj->getVar('trust_dirname') == $myrow['dirname']){
						$this->mSiteModuleObjects[$mobj->getVar('dirname')]=$mobj;
						$_ismodulestore = true;
						break;
					}
				}
			}else{
				foreach($this->items as $key => $myrow){
					if ($mobj->getVar('trust_dirname') == '' && $mobj->getVar('dirname') == $myrow['dirname'] ){
						$this->mSiteModuleObjects[$mobj->getVar('dirname')]=$mobj;
						$_ismodulestore = true;
						break;
					}
				}
			}
			//もう登録されていない
			if ($_ismodulestore == false){
				$this->delete($mobj);
			}
		}

	}

	public function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();

		//for test start ---------------------------
		if ( !is_array($this->items)) {
			return $ret;
		}
		//for test end ---------------------------

		//未登録のデータは自動で登録
		foreach($this->items as $key => $myrow){
			if ($myrow['type'] == 'TrustModule' ){

				//インストール済みの同じtrustモージュールのリストを取得
				$list = Legacy_Utils::getDirnameListByTrustDirname($myrow['dirname']);

				if (empty($list)){
					//インストール済みの同じtrustモージュール無し
					$mModuleStore = new $this->mClass();
					$mModuleStore->assignVars($myrow);
					$mModuleStore->set('sid',$this->store['sid']);

					$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
					$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

					$mModuleStore->setmModule();
					$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );
					if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
						$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
						$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
					}else{
						$mModuleStore->setNew();
						$this->insert($mModuleStore ,true);
					}
					unset($mModuleStore);

				}else{

					$_isrootdirmodule = false;
					foreach($list as $dirname){
						$mModuleStore = new $this->mClass();
						$mModuleStore->assignVars($myrow);
						$mModuleStore->assignVar('sid',$this->store['sid']);

						$mModuleStore->assignVar('dirname',$dirname);
						$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
						$mModuleStore->assignVar('rootdirname',$myrow['dirname']);
						$mModuleStore->setmModule();
						$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

						if ( $dirname == $myrow['dirname'] ){
							$_isrootdirmodule = true;
						}
						if (isset($this->mSiteModuleObjects[$dirname])){
							$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$dirname]->getVar('id') );
							$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
						}else{
							$mModuleStore->setNew();
							$this->insert($mModuleStore ,true);
						}
						unset($mModuleStore);
					}
					//そのままインストールしていない場合、そのまま追加可能なので
					if ( $_isrootdirmodule == false ){
						$mModuleStore = new $this->mClass();
						$mModuleStore->assignVars($myrow);
						$mModuleStore->assignVar('sid',$this->store['sid']);

						$mModuleStore->assignVar('trust_dirname',$myrow['dirname']);
						$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

						$mModuleStore->setmModule();
						$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );
						if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
							$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
							$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
						}else{
							$mModuleStore->setNew();
							$this->insert($mModuleStore ,true);
						}
						unset($mModuleStore);
					}
				}
			}else{
				//trusモジュールでない(複製可能なものはどうしよう)
				$mModuleStore = new $this->mClass();
				$mModuleStore->assignVars($myrow);
				$mModuleStore->assignVar('sid',$this->store['sid']);

				$mModuleStore->assignVar('rootdirname',$myrow['dirname']);

				$mModuleStore->setmModule();
				$mModuleStore->assignVar('last_update',$mModuleStore->mModule->getVar('last_update') );

				if (isset($this->mSiteModuleObjects[$myrow['dirname']])){
					$mModuleStore->assignVar('id',$this->mSiteModuleObjects[$myrow['dirname']]->getVar('id') );
					$this->_storeupdate($mModuleStore , $this->mSiteModuleObjects[$myrow['dirname']]);
				}else{
					$mModuleStore->setNew();
					$this->insert($mModuleStore ,true);
				}
				unset($mModuleStore);
			}
		}

		//追加後の表示データ criteria付き
		$mObjects = parent::getObjects($criteria ,null,null, $id_as_key);
		foreach($mObjects as $key => $mobj){
			$mobj->setmModule();//判定用のインストール済みのモジュール情報の保持を追加
			if ($id_as_key) {
				$id = $mobj->getVar('id');
				$ret[$id] =& $mobj;
			}else{
				$ret[] =& $mobj;
			}
			unset($mobj);
		}
		return $ret;
	}

	private function _storeupdate ($obj , $oldobj)
	{
//		$newdata['last_update'] = $obj->getVar('last_update');
		$newdata['version'] = $obj->getVar('version');
//		$olddata['last_update'] = $oldobj->getVar('last_update');
		$olddata['version'] = $oldobj->getVar('version');
		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
			$obj->unsetNew();
			$this->insert($obj ,true);
		}

	}

	private function _storelist ()
	{
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/modules.ini';
		$this->items = $items;
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/store.ini';
		$this->store = $store;

	}

//for test end ---------------------------


} // end class

?>