<?php
/**
 * @package legacyRender
 * @version $Id: BannerclientListAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/BannerclientFilterForm.class.php";

class LegacyRender_BannerclientListAction extends LegacyRender_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('bannerclient');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new LegacyRender_BannerclientFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=BannerclientList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("bannerclient_list.html");
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadBannerCount();
			$this->mObjects[$key]->loadFinishBannerCount();
		}
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
	}
}

?>
