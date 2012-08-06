<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH .'/class/handler/ModuleStore.class.php';
/**
* XoopsSimpleObject
*/
class Xupdate_ThemeStore extends Xupdate_ModuleStore {

	public $mModule ;
	public $modinfo = array();

	public function __construct()
	{
		parent::__construct() ;
	}

	/**
	 * @return string
	 */
	//function getRenderedVersion()
	//{
	//	return sprintf('%01.2f', $this->getVar('version') / 100);
	//}
	/**
	 * @
	 */
	public function setmModule($readini = true)
	{
		return parent::setmModule($readini);
	}
	/**
	 * @return bool
	 */
	public function isDirnameError()
	{
		return parent::isDirnameError();
	}
	/**
	 * @return bool
	 */
	public function hasNeedUpdate()
	{
		return parent::hasNeedUpdate();
	}

	public function get_StoreUrl()
	{
		//TODO for test dirname ?
		$root =& XCube_Root::getSingleton();
		$modDirname = $root->mContext->mModule->mAssetManager->mDirname;
		$ret = XOOPS_MODULE_URL .'/'.$modDirname.'/admin/index.php?action=ThemeInstall'
			.'&id='.$this->getVar('id') .'&dirname='.$this->getVar('dirname');
		return $ret;
	}
	public function get_InstallUrl()
	{
		$ret = XOOPS_MODULE_URL .'/legacy/admin/index.php?action=ThemeInstall&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}
	public function get_UpdateUrl()
	{
		$ret = XOOPS_MODULE_URL .'/legacy/admin/index.php?action=ThemeInstall&dirname='
			.$this->getVar('dirname') ;
		return $ret;
	}

} // end class

/**
* XoopsObjectGenericHandler extends
*/
class Xupdate_ThemeStoreHandler extends Xupdate_ModuleStoreHandler
{
	public $mTable = '{dirname}_modulestore';

	public $mPrimary = 'id';
	//XoopsSimpleObject
	public $mClass = 'Xupdate_ThemeStore';


	public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
	{
		$this->mTable = strtr($this->mTable,array('{dirname}' => $dirname));
		parent::__construct($db, $dirname);

	}


	public function &getObjects($criteria = null, $limit = null, $start = null,  $id_as_key = false)
	{
		return parent::getObjects($criteria, $limit, $start,  $id_as_key);
	}


} // end class

?>