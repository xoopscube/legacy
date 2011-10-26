<?php
/**
 * @package user
 * @version $Id: RanksDeleteAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/RanksAdminDeleteForm.class.php";

class User_RanksDeleteAction extends User_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('rank_id');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('ranks');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new User_RanksAdminDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("ranks_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=RanksList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=RanksList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=RanksList");
	}
}

?>
