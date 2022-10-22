<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPS Cube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit();
}

require_once XUPDATE_TRUST_PATH . '/class/handler/ModuleStore.class.php';

/**
 * XoopsSimpleObject
 */
class Xupdate_PreloadStore extends Xupdate_ModuleStore {

	const PRIMARY = 'id';
	const DATANAME = 'preloadstore';

	public function get_StoreUrl() {
		//TODO for test dirname ?
		$root       =& XCube_Root::getSingleton();
		$modDirname = $root->mContext->mModule->mAssetManager->mDirname;
		$ret        = XOOPS_MODULE_URL . '/' . $modDirname . '/admin/index.php?action=PreloadInstall'
		              . '&id=' . $this->getVar( 'id' );

		return $ret;
	}

	public function get_InstallUrl() {
		$ret = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreloadInstall&dirname='
		       . $this->getVar( 'dirname' );

		return $ret;
	}

	public function get_UpdateUrl() {
		$ret = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreloadUpdate&dirname='
		       . $this->getVar( 'dirname' );

		return $ret;
	}
} // end class

/**
 * XoopsObjectGenericHandler extends
 */
class Xupdate_PreloadStoreHandler extends Xupdate_ModuleStoreHandler {
	public $mClass = 'Xupdate_PreloadStore';

	/**
	 * getDataname
	 *
	 * @param void
	 *
	 * @return    string[]
	 */
	public function getDataname() {
		return 'preloadstore';
	}
} // end class
;
