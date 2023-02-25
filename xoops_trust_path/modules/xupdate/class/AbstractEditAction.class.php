<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2023 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

/**
 * Xupdate_AbstractEditAction
 **/
abstract class Xupdate_AbstractEditAction extends Xupdate_AbstractAction {
	/*** XoopsSimpleObject ***/
	public $mObject = null;

	/*** XoopsObjectGenericHandler ***/
	public $mObjectHandler = null;

	/*** XCube_ActionForm ***/
	public $mActionForm = null;

	/**
	 * _getId
	 *
	 * @return void
	 */
	protected function _getId() {
	}

	/**
	 * &_getHandler
	 *
	 * @return void
	 */
	protected function &_getHandler() {
	}

	/**
	 * _getActionName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getActionName() {
		return _EDIT;
	}

	/**
	 * _setupActionForm
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	protected function _setupActionForm() {
	}

	/**
	 * _setupObject
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	protected function _setupObject() {
		$id = $this->_getId();

		$this->mObjectHandler =& $this->_getHandler();

		$this->mObject =& $this->mObjectHandler->get( $id );

		if ( null == $this->mObject && $this->_isEnableCreate() ) {
			$this->mObject =& $this->mObjectHandler->create();
		}
	}

	/**
	 * _isEnableCreate
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	protected function _isEnableCreate() {
		return true;
	}

	/**
	 * prepare
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	public function prepare() {
		$this->_setupObject();
		$this->_setupActionForm();

		return true;
	}

	/**
	 * getDefaultView
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function getDefaultView() {
		if ( null == $this->mObject ) {
			return XUPDATE_FRAME_VIEW_ERROR;
		}

		$this->mActionForm->load( $this->mObject );

		return XUPDATE_FRAME_VIEW_INPUT;
	}

	/**
	 * execute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function execute() {
		if ( null == $this->mObject ) {
			return XUPDATE_FRAME_VIEW_ERROR;
		}

		if ( null != $this->mRoot->mContext->mRequest->getRequest( '_form_control_cancel' ) ) {
			return XUPDATE_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->load( $this->mObject );

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ( $this->mActionForm->hasError() ) {
			return XUPDATE_FRAME_VIEW_INPUT;
		}

		$this->mActionForm->update( $this->mObject );

		return $this->_doExecute();
	}

	/**
	 * _doExecute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	protected function _doExecute() {
		if ( $this->mObjectHandler->insert( $this->mObject ) ) {
			return XUPDATE_FRAME_VIEW_SUCCESS;
		}

		return XUPDATE_FRAME_VIEW_ERROR;
	}
}
