<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3.1
 * @author Other authors Gigamaster (XCL 2.3)
 * @author Naoki Sawada, Naoki Okino
 * @copyright  (c) 2005-2022 Author
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
class Xupdate_Admin_PreloadInstallAction extends Xupdate_AbstractInstallAction {
	public function __construct() {
		parent::__construct();
		$this->contents    = 'preload';
		$this->action      = 'PreloadStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_PRELOAD;
		$this->my_dir_path = XOOPS_ROOT_PATH . '/preload';
	}
}
