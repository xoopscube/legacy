<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_PageNavigator.class.php";

class LegacyRender_AbstractListAction extends LegacyRender_Action
{
	var $mObjects = array();
	var $mFilter = null;

	function &_getHandler()
	{
	}

	function &_getFilterForm()
	{
	}

	function _getBaseUrl()
	{
	}
	
	function &_getPageNavi()
	{
		$navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START);
		return $navi;
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mFilter =& $this->_getFilterForm();
		$this->mFilter->fetch();
		
		$handler =& $this->_getHandler();
		$this->mObjects =& $handler->getObjects($this->mFilter->getCriteria());
		
		return LEGACYRENDER_FRAME_VIEW_INDEX;
	}
}

?>
