<?php
/**
 * @package Legacy
 * @version $Id: MiscOnlineAction.class.php,v 1.1 2007/05/15 02:34:31 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/forms/OnlineFilterForm.class.php";

class User_MiscOnlineAction extends User_AbstractListAction
{
	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$controller->mRoot->mLanguageManager->loadModuleMessageCatalog('user');
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('online', 'user');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new User_OnlineFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./misc.php?type=online";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_misc_online.html");
		
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadModule();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute('enableViewIP', $controller->mRoot->mContext->mUser->isInRole('Module.user.Admin'));
	}
}

?>
