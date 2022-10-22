<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 The XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractInstallAction.class.php';

/**
 * Xupdate_Admin_StoreAction
 *
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ModuleInstallAction extends Xupdate_AbstractInstallAction {
	public function __construct() {
		parent::__construct();
		$this->contents    = 'module';
		$this->action      = 'ModuleStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_MODULE;
		$this->my_dir_path = XOOPS_MODULE_PATH;
	}
}
