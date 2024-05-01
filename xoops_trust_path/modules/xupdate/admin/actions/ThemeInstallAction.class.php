<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
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
class Xupdate_Admin_ThemeInstallAction extends Xupdate_AbstractInstallAction {
	public function __construct() {
		parent::__construct();
		$this->contents    = 'theme';
		$this->action      = 'ThemeStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_THEME;
		$this->my_dir_path = XOOPS_ROOT_PATH . '/themes';
	}
}
