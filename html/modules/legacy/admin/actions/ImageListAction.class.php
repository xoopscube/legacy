<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageListAction.class.php,v 1.4 2008/09/25 15:11:51 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImageFilterForm.class.php";

class Legacy_ImageListAction extends Legacy_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('image');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =& new Legacy_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=ImageList";
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$result = parent::getDefaultView($controller, $xoopsUser);
		if ($result == LEGACY_FRAME_VIEW_INDEX) {
			$cat_id = xoops_getrequest('imgcat_id');
			$handler =& xoops_getmodulehandler('imagecategory');
			$this->mCategory =& $handler->get($cat_id);
			
			if ($this->mCategory == null) {
				$result = LEGACY_FRAME_VIEW_ERROR;
			}
		}
		
		return $result;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("image_list.html");
		
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadImagecategory();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		
		$render->setAttribute("category", $this->mCategory);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=ImagecategoryList");
	}
}

?>
