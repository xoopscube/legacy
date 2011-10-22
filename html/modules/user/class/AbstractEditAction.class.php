<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class User_AbstractEditAction extends User_Action
{
	var $mObject = null;
	var $mObjectHandler = null;
	var $mActionForm = null;
	var $mConfig;

	/**
	 * @access protected
	 */
	function _getId()
	{
	}

	/**
	 * @access protected
	 */
	function &_getHandler()
	{
	}

	/**
	 * @access protected
	 */
	function _setupActionForm()
	{
	}

	/**
	 * @access protected
	 */
	function _setupObject()
	{
		$id = $this->_getId();
		
		$this->mObjectHandler = $this->_getHandler();
		
		$this->mObject =& $this->mObjectHandler->get($id);
		
		if ($this->mObject == null && $this->isEnableCreate()) {
			$this->mObject =& $this->mObjectHandler->create();
		}
	}

	/**
	 * _getPageAction
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getPageAction()
	{
		return _EDIT;
	}

	/**
	 * @access protected
	 */
	function isEnableCreate()
	{
		return true;
	}

	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;

		$this->_setupActionForm();
		$this->_setupObject();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return USER_FRAME_VIEW_ERROR;
		}
	
		$this->mActionForm->load($this->mObject);
		
		return USER_FRAME_VIEW_INPUT;
	}

	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return USER_FRAME_VIEW_ERROR;
		}
	
		if (xoops_getrequest('_form_control_cancel') != null) {
			return USER_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->load($this->mObject);
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
	
		if($this->mActionForm->hasError()) {
			return USER_FRAME_VIEW_INPUT;
		}
	
		$this->mActionForm->update($this->mObject);
		
		return $this->_doExecute($this->mObject) ? USER_FRAME_VIEW_SUCCESS
		                                         : USER_FRAME_VIEW_ERROR;
	}

	/**
	 * @access protected
	 */
	function _doExecute()
	{
		return $this->mObjectHandler->insert($this->mObject);
	}
}

?>
