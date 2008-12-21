<?php
/**
 * @package user
 * @version $Id: UserSearchAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/admin/forms/UserSearchForm.class.php";

class User_UserSearchAction extends User_Action
{
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =& new User_UserSearchForm();
		$this->mActionForm->prepare();
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mActionForm->fetch();
		
		return USER_FRAME_VIEW_INPUT;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_search.html");
		$render->setAttribute("actionForm", $this->mActionForm);
		
		$groupHandler =& xoops_gethandler('group');
		$groups =& $groupHandler->getObjects(null, true);
		
		$groupOptions = array();
		foreach ($groups as $gid => $group) {
			$groupOptions[$gid] = $group->getVar('name');
		}

		$render->setAttribute('groupOptions', $groupOptions);
	}
}

?>