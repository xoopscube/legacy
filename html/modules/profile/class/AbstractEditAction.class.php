<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_AbstractEditAction extends Profile_AbstractAction
{
	var $mObject = null;
	var $mObjectHandler = null;
	var $mActionForm = null;

	/**
	 * @protected
	 */
	function _getId()
	{
	}

	/**
	 * @protected
	 */
	function &_getHandler()
	{
	}

	/**
	 * @protected
	 */
	function _setupActionForm()
	{
	}

	/**
	 * @protected
	 */
	function _setupObject()
	{
		$id = $this->_getId();
	
		$this->mObjectHandler =& $this->_getHandler();
	
		$this->mObject =& $this->mObjectHandler->get($id);
	
		if ($this->mObject == null && $this->_isEnableCreate()) {
			$this->mObject =& $this->mObjectHandler->create();
		}
	}

	/**
	 * @protected
	 */
	function _isEnableCreate()
	{
		return true;
	}

	/**
	 * @public
	 */
	function prepare()
	{
		$this->_setupObject();
		$this->_setupActionForm();
	}

	/**
	 * @public
	 */
	function getDefaultView()
	{
		if ($this->mObject == null) {
			return PROFILE_FRAME_VIEW_ERROR;
		}
	
		$this->mActionForm->load($this->mObject);
	
		return PROFILE_FRAME_VIEW_INPUT;
	}

	/**
	 * @public
	 */
	function execute()
	{
		if ($this->mObject == null) {
			return PROFILE_FRAME_VIEW_ERROR;
		}
	
		if (xoops_getrequest('_form_control_cancel') != null) {
		return PROFILE_FRAME_VIEW_CANCEL;
		}
	
		$this->mActionForm->load($this->mObject);
	
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
	
		if ($this->mActionForm->hasError()) {
			return PROFILE_FRAME_VIEW_INPUT;
		}
	
		$this->mActionForm->update($this->mObject);
	
		return $this->_doExecute($this->mObject);
	}

	/**
	 * @protected
	 */
	function _doExecute()
	{
		if ($this->mObjectHandler->insert($this->mObject)) {
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
	
		return PROFILE_FRAME_VIEW_ERROR;
	}
}

?>
