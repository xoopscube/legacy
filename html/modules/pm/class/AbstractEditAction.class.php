<?php
/**
 * @package pm
 * @version $Id: AbstractEditAction.class.php,v 1.1 2007/05/15 02:35:26 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Pm_AbstractEditAction extends Pm_AbstractAction
{
	var $mObject = null;
	var $mObjectHandler = null;
	var $mActionForm = null;

	function _getId()
	{
	}

	function &_getHandler()
	{
	}

	function _setupActionForm()
	{
	}

	function _setupObject()
	{
		$id = $this->_getId();
		
		$this->mObjectHandler =& $this->_getHandler();
		
		$this->mObject =& $this->mObjectHandler->get($id);
	
		if ($this->mObject == null && $this->isEnableCreate()) {
			$this->mObject =& $this->mObjectHandler->create();
		}
	}

	function isEnableCreate()
	{
		return true;
	}

	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->_setupObject();
		$this->_setupActionForm();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return PM_FRAME_VIEW_ERROR;
		}
	
		$this->mActionForm->load($this->mObject);
	
		return PM_FRAME_VIEW_INPUT;
	}

	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return PM_FRAME_VIEW_ERROR;
		}
		
		if (isset($_REQUEST['_form_control_cancel'])) {
			return PM_FRAME_VIEW_CANCEL;
		}
	
		$this->mActionForm->load($this->mObject);
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
	
		if($this->mActionForm->hasError()) {
			return PM_FRAME_VIEW_INPUT;
		}
	
		$this->mActionForm->update($this->mObject);
		
		return $this->_doExecute($this->mObject) ? PM_FRAME_VIEW_SUCCESS
		                                         : PM_FRAME_VIEW_ERROR;
	}

	function _doExecute()
	{
		return $this->mObjectHandler->insert($this->mObject);
	}
}

?>
