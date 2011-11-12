<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/BannerfinishFilterForm.class.php";

class LegacyRender_BannerfinishListAction extends LegacyRender_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('bannerfinish');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new LegacyRender_BannerfinishFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=BannerfinishList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("bannerfinish_list.html");
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadBannerclient();
		}
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		
		//
		// If cid is specified, load client object and assign it.
		//
		$cid = xoops_getrequest('cid');
		if ($cid > 0) {
			$handler =& xoops_getmodulehandler('bannerclient');
			$client =& $handler->get($cid);
			if (is_object($client)) {
				$render->setAttribute("currentClient", $client);
			}
		}
	}
}

?>
