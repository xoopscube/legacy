<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

if (!defined('XOOPS_ROOT_PATH')) exit;

require_once XOOPS_ROOT_PATH. '/core/XCube_PageNavigator.class.php';

class FileManager_AbstractListAction extends FileManager_Action
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
		$navi =& new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START);
		return $navi;
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mFilter =& $this->_getFilterForm();
		$this->mFilter->fetch();

		$handler =& $this->_getHandler();
		$this->mObjects =& $handler->getObjects($this->mFilter->getCriteria());

		return CONTENTS_FRAME_VIEW_INDEX;
		
	}

	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
}

?>
