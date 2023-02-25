<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright (c) 2005-2022 The XOOPS Cube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit();
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractStoreAction.class.php';

class Xupdate_Admin_PreloadStoreAction extends Xupdate_AbstractStoreAction {
	public function __construct() {
		$this->contents    = 'preload';
		$this->action      = 'PreloadStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_PRELOAD;
		parent::__construct();
	}
} // end class
;
