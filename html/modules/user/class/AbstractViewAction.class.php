<?php
/**
 * @package User
 * @version $Id: AbstractViewAction.class.php,v 1.1 2007/05/15 02:34:49 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class User_AbstractViewAction extends User_Action
{
	var $mObject = null;
	var $mObjectHandler = null;

	function User_AbstractViewAction()
	{
	}

	function _getId()
	{
	}

	function &_getHandler()
	{
	}

	function _setupObject()
	{
		$id = $this->_getId();
		
		$this->mObjectHandler =& $this->_getHandler();
		
		$this->mObject =& $this->mObjectHandler->get($id);
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
		return _VIEW;
	}

	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->_setupObject();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return USER_FRAME_VIEW_ERROR;
		}
	
		return USER_FRAME_VIEW_SUCCESS;
	}

	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
}

?>
