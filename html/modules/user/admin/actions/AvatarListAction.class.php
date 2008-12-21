<?php
/**
 * @package user
 * @version $Id: AvatarListAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/AvatarFilterForm.class.php";

class User_AvatarListAction extends User_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('avatar');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =& new User_AvatarFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=AvatarList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("avatar_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
	}
}

?>
