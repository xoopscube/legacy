<?php
/**
 * @package user
 * @version $Id: GroupEditAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/GroupAdminEditForm.class.php";

class User_GroupEditAction extends User_AbstractEditAction
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
		$this->mActionForm =new User_GroupAdminEditForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("group_edit.html");
		$render->setAttribute("actionForm", $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("index.php?action=GroupList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("index.php?action=GroupList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("index.php?action=GroupList");
	}
}

?>
