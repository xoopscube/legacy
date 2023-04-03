<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

/**
 * Xupdate_Admin_IndexAction
 **/
class Xupdate_Admin_InstallCheckerAction extends Xupdate_AbstractAction {
	/**
	 * getDefaultView
	 *
	 * @return void
	 */
	public function getDefaultView() {
		if ( ! $this->mod_config['_FtpLoginCheck'] || ! $this->_removeInstallDir() ) {
			$url = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $this->mRoot->mContext->mModule->mXoopsModule->get( 'mid' );
			redirect_header( $url, 10, _MD_XUPDATE_MESSAGE_INSTALL_COMPLETE_WARNING );
		} else {
			$url = XOOPS_URL . '/index.php';
			header( 'Location: ' . $url );
		}
		exit();
	}
}
