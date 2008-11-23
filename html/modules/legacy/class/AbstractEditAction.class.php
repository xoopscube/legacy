<?php
/**
 *
 * @package Legacy
 * @version $Id: AbstractEditAction.class.php,v 1.3 2008/09/25 15:11:27 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_AbstractEditAction extends Legacy_Action
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

	function prepare(&$controller, &$xoopsUser)
	{
		$this->_setupObject();
		$this->_setupActionForm();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
	
		$this->mActionForm->load($this->mObject);
	
		return LEGACY_FRAME_VIEW_INPUT;
	}

	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
	
		if (xoops_getrequest('_form_control_cancel') != null) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->load($this->mObject);
		
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
	
		if($this->mActionForm->hasError()) {
			return LEGACY_FRAME_VIEW_INPUT;
		}
	
		$this->mActionForm->update($this->mObject);
		
		return $this->_doExecute($this->mObject) ? LEGACY_FRAME_VIEW_SUCCESS
		                                         : LEGACY_FRAME_VIEW_ERROR;
	}

	function _doExecute()
	{
		return $this->mObjectHandler->insert($this->mObject);
	}
}

?>
