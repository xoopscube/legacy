<?php
/**
 *
 * @package Legacy
 * @version $Id: AbstractListAction.class.php,v 1.3 2008/09/25 15:11:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_PageNavigator.class.php";

class Legacy_AbstractListAction extends Legacy_Action
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
		
		return LEGACY_FRAME_VIEW_INDEX;
	}
}

?>
