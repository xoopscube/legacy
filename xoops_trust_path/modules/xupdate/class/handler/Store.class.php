<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Xupdate_StoreObject
**/
class Xupdate_StoreObject extends XoopsSimpleObject
{
	/**
	 * __construct
	 *
	 * @param	void
	 *
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('sid', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('contents', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('addon_url', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('setting_type', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('reg_unixtime', XOBJ_DTYPE_INT, '0', false);

	}

}

/**
 * Xupdate_StoreHandler
**/
class Xupdate_StoreHandler extends XoopsObjectGenericHandler
{
	public /*** string ***/ $mTable = '{dirname}_store';

	public /*** string ***/ $mPrimary = 'sid';

	public /*** string ***/ $mClass = 'Xupdate_StoreObject';

	private $cacheCheckFile;
	
	/**
	 * __construct
	 *
	 * @param	XoopsDatabase  &$db
	 * @param	string	$dirname
	 *
	 * @return	void
	**/
	public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
	{
		$this->mTable = strtr($this->mTable,array('{dirname}' => $dirname));
		parent::XoopsObjectGenericHandler($db);
		$configHandler = & xoops_gethandler('config');
		$module_config = $configHandler->getConfigsByDirname($dirname);
		$this->cacheCheckFile = XOOPS_TRUST_PATH . '/' . trim($module_config['temp_path'], '/') . '/' . rawurlencode(substr(XOOPS_URL, 7)).'_cacheCheck.ini.php';
	}

	public function setNeedCacheRemake($remove = false) {
		if ($remove) {
			unlink($this->cacheCheckFile);
		} else {
			touch($this->cacheCheckFile, 0);
		}
	}
	
	public function getCacheCheckFile() {
		return $this->cacheCheckFile;
	}
}

?>
