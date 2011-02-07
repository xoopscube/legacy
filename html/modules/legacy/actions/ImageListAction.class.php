<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageListAction.class.php,v 1.6 2008/09/25 14:31:45 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/ImageFilterForm.class.php";

/***
 * @internal
 */
class Legacy_ImageListAction extends Legacy_AbstractListAction
{
	var $mImgcatId = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$controller->setDialogMode(true);
		
		$root =& $controller->mRoot;
		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('image', 'legacy');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new Legacy_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return XOOPS_URL . "/imagemanager.php?op=list";
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$result = parent::getDefaultView($controller, $xoopsUser);
		if ($result == LEGACY_FRAME_VIEW_INDEX) {
			$this->mImgcatId = xoops_getrequest('imgcat_id');
			$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
			$this->mCategory =& $handler->get($this->mImgcatId );
		}
		
		return $result;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_image_list.html");
		
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadImagecategory();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		
		$render->setAttribute('imgcatId', $this->mImgcatId);
		
		$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
		
		if (is_object($xoopsUser)) {
			$groups = $xoopsUser->getGroups();
		}
		else {
			$groups = array(XOOPS_GROUP_ANONYMOUS);
		}
		$categoryArr =& $handler->getObjectsWithReadPerm($groups, 1);
		
		$render->setAttribute('categoryArr', $categoryArr);
		
		//
		// If current category object exists, check the permission of uploading.
		//
		$hasUploadPerm = null;
		if ($this->mCategory != null) {
			$hasUploadPerm = $this->mCategory->hasUploadPerm($groups);
		}
		$render->setAttribute('hasUploadPerm', $hasUploadPerm);
		$render->setAttribute("category", $this->mCategory);
		//echo xoops_getrequest('target');die();
        $render->setAttribute('target', htmlspecialchars(xoops_getrequest('target'), ENT_QUOTES));
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL . "/imagemanager.php?op=list");
	}
}

?>
