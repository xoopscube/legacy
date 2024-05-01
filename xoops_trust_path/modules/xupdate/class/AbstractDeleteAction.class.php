<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2024 The XOOPSCube Project
 * @license GPL v2.0
 */


if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractEditAction.class.php';

/**
 * Xupdate_AbstractDeleteAction
 **/
abstract class Xupdate_AbstractDeleteAction extends Xupdate_AbstractEditAction {
	/**
	 * _isEnableCreate
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	protected function _isEnableCreate() {
		return false;
	}

	/**
	 * _getActionName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getActionName() {
		return _DELETE;
	}

	/**
	 * prepare
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	public function prepare() {
		return parent::prepare() && is_object( $this->mObject );
	}

	/**
	 * _doExecute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	protected function _doExecute() {
		if ( $this->mObjectHandler->delete( $this->mObject ) ) {
			return XUPDATE_FRAME_VIEW_SUCCESS;
		}

		return XUPDATE_FRAME_VIEW_ERROR;
	}
}
