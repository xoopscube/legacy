<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';
/**
* XoopsSimpleObject
*/
class Xupdate_ModuleStore extends Xupdate_Root {

	public function __construct()
	{
		parent::__construct() ;
/*
		static $_mAllowType = array(
		XOBJ_DTYPE_BOOL=>XOBJ_DTYPE_BOOL,
		XOBJ_DTYPE_INT=>XOBJ_DTYPE_INT,
		XOBJ_DTYPE_FLOAT=>XOBJ_DTYPE_FLOAT,
		XOBJ_DTYPE_STRING=>XOBJ_DTYPE_STRING,
		XOBJ_DTYPE_TEXT=>XOBJ_DTYPE_TEXT);
*/
		//TODO モジュールオブジェクトの項目をコピーしただけ
		$this->initVar('mid', XOBJ_DTYPE_FLOAT, null, false);
		$this->initVar('name', XOBJ_DTYPE_STRING, null, false, 150);
		$this->initVar('version', XOBJ_DTYPE_FLOAT, 100, false);
		$this->initVar('last_update', XOBJ_DTYPE_FLOAT, null, false);
		$this->initVar('weight', XOBJ_DTYPE_FLOAT, 0, false);
		$this->initVar('isactive', XOBJ_DTYPE_BOOL, 1, false);
		$this->initVar('dirname', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('trust_dirname', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('role', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('hasmain', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('hasadmin', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('hassearch', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('hasconfig', XOBJ_DTYPE_BOOL, 0, false);
		$this->initVar('hascomments', XOBJ_DTYPE_BOOL, 0, false);
		// RMV-NOTIFY
		$this->initVar('hasnotification', XOBJ_DTYPE_FLOAT, 0, false);

		//custom カスタム項目
		$this->initVar('id', XOBJ_DTYPE_FLOAT, 0, false);
		$this->initVar('type', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('rootdirname', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('storeurl', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('installurl', XOBJ_DTYPE_STRING, null, false);
		$this->initVar('updateurl', XOBJ_DTYPE_STRING, null, false);

	}

	/**
	 * @return string
	 */
	function getRenderedVersion()
	{
		return sprintf('%01.2f', $this->getVar('version') / 100);
	}
	/**
	 * @return bool
	 */
	function hasNeedUpdate()
	{
//		$info =& $this->getInfo();
//		return ($this->getVar('version') < Legacy_Utils::convertVersionFromModinfoToInt($info['version']));
		return false;

	}


} // end class

/**
* XoopsObjectHandler extends
*/
class Xupdate_ModuleStoreHandler extends XoopsObjectHandler
{
	public $mTable = '{dirname}_module_data';//TODO not datbase ,test now ダミーです
	public $mPrimary = 'id';
	//XoopsSimpleObject
	public $mClass = 'Xupdate_ModuleStore';

	public $items = array();

	public function Xupdate_ModuleStoreHandler(&$db) {

		parent::XoopsObjectHandler($db);

		//for test start ---------------------------
		$this->_storelist();
		//for test end ---------------------------
	}

	public function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();

		//for test start ---------------------------
		if ( !is_array($this->items)) {
			return $ret;
		}
		if ( ! empty($this->items) ){
			usort($this->items, array('Xupdate_ModuleStoreHandler','dirname_asc_sort') );
		}

		$hModule =& xoops_gethandler('module');
		foreach($this->items as $key => $myrow){
			$ccmodule = new $this->mClass();
			$ccmodule->assignVars($myrow);

			$id = $ccmodule->getVar('id');
			$ccmodule->assignVar('name',$ccmodule->getVar('dirname'));

			$MyhModule =& $hModule->getByDirname($ccmodule->getVar('dirname')) ;
			if ( is_object($MyhModule) ) {
				$ccmodule->assignVar('mid',$MyhModule->getVar('mid'));
				$ccmodule->assignVar('dirname',$MyhModule->getVar('dirname'));
				$ccmodule->assignVar('trust_dirname',$MyhModule->getVar('trust_dirname'));
				$ccmodule->assignVar('version',$MyhModule->getVar('version'));
				$ccmodule->assignVar('last_update',$MyhModule->getVar('last_update'));
				$ccmodule->assignVar('isactive',$MyhModule->getVar('isactive'));
				$ccmodule->assignVar('updateurl',$this->get_UpdateUrl($ccmodule));
			}

			$ccmodule->assignVar('rootdirname',$ccmodule->getVar('dirname'));
			$ccmodule->assignVar('storeurl',$this->get_StoreUrl($ccmodule));
			$ccmodule->assignVar('installurl',$this->get_InstallUrl($ccmodule));

			if ($id_as_key) {
				$ret[$id] =& $ccmodule;
			}else{
				$ret[$key] =& $ccmodule;
			}
			unset($ccmodule);
		}
		//for test end ---------------------------

		return $ret;
	}

	function getCount($criteria = null){
		$ret=0;
		if (is_array($this->items)) {
			 $ret = count(array_keys($this->items));
		}
		return $ret;
	}

	private function _storelist ()
	{
		include dirname(dirname(dirname(__FILE__))) .'/admin/actions/modules.ini';

		$this->items = $items;

	}

	private function get_StoreUrl($obj)
	{
		//TODO for test dirname ?
		$ret = XOOPS_URL .'/modules/xupdate/admin/index.php?action=ModuleInstall&target_key='
			.$obj->getVar('dirname') .'&target_type='.$obj->getVar('type');
		return $ret;
	}

	private function get_InstallUrl($obj)
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='
			.$obj->getVar('dirname') ;
		return $ret;
	}
	private function get_UpdateUrl($obj)
	{
		$ret = XOOPS_URL .'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='
			.$obj->getVar('dirname') ;
		return $ret;
	}

	static function name_asc_sort($a, $b)
	{
		return strcmp($a['name'], $b['name']);
	}
	static function dirname_asc_sort($a, $b)
	{
		return strcmp($a['dirname'], $b['dirname']);
	}

//for test end ---------------------------


} // end class

?>