<?php
/**
 * @package user
 * @version $Id: UserSearchListAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/UserSearchFilterForm.class.php";

class User_UserSearchListAction extends User_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('users_search');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =& new User_UserSearchFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}
	
	function _getBaseUrl()
	{
		return "./index.php?action=UserSearchList";
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$controller->mRoot->mDelegateManager->add('Legacy.Event.Explaceholder.Get.UserPagenaviOtherUrl',
'User_UserSearchListAction::renderOtherUrlControl');

		$render->setTemplateName("user_search_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
	}
	
	function renderOtherUrlControl(&$buf, $params)
	{
		if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
			$navi =& $params['pagenavi'];
			$url = $params['url'];
			if(count($navi->mExtra) > 0) {
				$t_arr = array();
			
				foreach($navi->mExtra as $key => $value) {
					$t_arr[] = $key . "=" . urlencode($value);
				}
			
				if (count($t_arr) == 0) {
					$buf = $url;
					return;
				}
			
				if (strpos($url,"?")!==false) {
					$buf = $url . "&amp;" . implode("&amp;", $t_arr);
				}
				else {
					$buf = $url . "?" . implode("&amp;", $t_arr);
				}
			}
		}
	}
}

?>
