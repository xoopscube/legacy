<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

/**
 * Xupdate_AbstractViewAction
 **/
abstract class Xupdate_AbstractViewAction extends Xupdate_AbstractAction {
	/*** XoopsSimpleObject ***/
	public $mObject = null;

	/*** XoopsObjectGenericHandler ***/
	public $mObjectHandler = null;

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
	 * @return void &XoopsObjectGenericHandler
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
		return _VIEW;
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

		return is_object( $this->mObject );
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

		return XUPDATE_FRAME_VIEW_SUCCESS;
	}

	/**
	 * execute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function execute() {
		return $this->getDefaultView();
	}
}
