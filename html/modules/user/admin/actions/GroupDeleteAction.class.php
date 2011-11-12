<?php
/**
 * @package user
 * @version $Id: GroupDeleteAction.class.php,v 1.2 2007/08/24 14:17:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/GroupAdminDeleteForm.class.php";

class User_GroupDeleteAction extends User_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('groupid');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('groups');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new User_GroupAdminDeleteForm();
		$this->mActionForm->prepare();
	}
	
	function _doExecute()
	{
		$handler =& xoops_gethandler('group');
		$group =& $handler->get($this->mObject->get('groupid'));
		
		$handler =& xoops_gethandler('member');
		
		if (!$handler->delete($group)) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		$handler =& xoops_gethandler('groupperm');
		if (!$handler->deleteByGroup($this->mObject->get('groupid'))) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		return USER_FRAME_VIEW_SUCCESS;
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("group_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=GroupList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=GroupList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=GroupList");
	}
}

?>
