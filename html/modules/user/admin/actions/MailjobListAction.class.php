<?php
/**
 * @package user
 * @version $Id: MailjobListAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/MailjobFilterForm.class.php";

class User_MailjobListAction extends User_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('mailjob');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new User_MailjobFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=MailjobList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("mailjob_list.html");
		
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadUserCount();
		}

		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
	}
}

?>
