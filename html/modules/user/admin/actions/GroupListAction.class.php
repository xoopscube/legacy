<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/GroupFilterForm.class.php";

class User_GroupListAction extends User_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('groups');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new User_GroupFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=GroupList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("group_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
	}
}

?>
