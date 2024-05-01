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
	exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Xupdate_Admin_ModuleStoreForm extends XCube_ActionForm {
	/***
	 * If the request is GET, never return token name.
	 * By this logic, a action can have three page in one action.
	 */
	public function getTokenName() {
		//
		//
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			return 'module.xupdate.Admin_ModuleStorForm.TOKEN';
		} else {
			return null;
		}
	}

	/***
	 * For displaying the confirm-page, don't show CSRF error.
	 * Always return null.
	 */
	public function getTokenErrorMessage() {
		return null;
	}


	public function prepare() {
		// set properties
		$this->mFormProperties['dirname'] = new XCube_StringArrayProperty( 'dirname' );

		// set fields
		$this->mFieldProperties['dirname'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['dirname']->setDependsByArray( [ 'required', 'maxlength' ] );
		$this->mFieldProperties['dirname']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_NAME, '191' );
		$this->mFieldProperties['dirname']->addMessage( 'maxlength', _MD_XUPDATE_ERROR_MAXLENGTH, _MD_XUPDATE_LANG_NAME, '191' );
		$this->mFieldProperties['dirname']->addVar( 'maxlength', 191 );
	}
}
