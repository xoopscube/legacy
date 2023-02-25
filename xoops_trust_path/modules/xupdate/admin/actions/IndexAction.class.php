<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3.1
 * @author Other authors Gigamaster (XCL 2.3)
 * @author Naoki Sawada, Naoki Okino
 * @copyright  (c) 2005-2023 Authors
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

/**
 * Xupdate_Admin_IndexAction
 **/
class Xupdate_Admin_IndexAction extends Xupdate_AbstractAction {
	/**
	 * getDefaultView
	 *
	 * @param void
	 *
	 * @return    Enum
	 **/
	public function getDefaultView() {
		return XUPDATE_FRAME_VIEW_SUCCESS;
	}

	/**
	 * executeViewSuccess
	 *
	 * @param XCube_RenderTarget    &$render
	 *
	 * @return    void
	 **/
	public function executeViewSuccess( &$render ) {
		$render->setTemplateName( 'admin.html' );
		$render->setAttribute( 'adminMenu', $this->mModule->getAdminMenu() );
	}
}
