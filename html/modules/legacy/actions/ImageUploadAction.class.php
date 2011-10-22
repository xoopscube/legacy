<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageUploadAction.class.php,v 1.4 2008/09/25 15:36:30 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/admin/actions/ImageEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/ImageUploadForm.class.php";

/***
 * @internal
 */
class Legacy_ImageUploadAction extends Legacy_ImageEditAction
{
	var $mCategory = null;

	function prepare(&$controller, &$xoopsUser)
	{
		parent::prepare($controller, $xoopsUser);
		$controller->setDialogMode(true);
		
		$root =& $controller->mRoot;
		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('image', 'legacy');
		return $handler;
	}

	function _setupObject()
	{
		$this->mObjectHandler =& $this->_getHandler();
		$this->mObject =& $this->mObjectHandler->create();
		$this->mObject->set('imgcat_id', xoops_getrequest('imgcat_id'));
	}

	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_ImageUploadForm();
		$this->mActionForm->prepare();
	}
	
	function hasPermission(&$controller, &$xoopsUser)
	{
		$groups = array();
		if (is_object($xoopsUser)) {
			$groups = $xoopsUser->getGroups();
		}
		else {
			$groups = array(XOOPS_GROUP_ANONYMOUS);
		}
			
		$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
		$this->mCategory =& $handler->get(xoops_getrequest('imgcat_id'));
		if (!is_object($this->mCategory ) || (is_object($this->mCategory) && !$this->mCategory->hasUploadPerm($groups))) {
			return false;
		}

		return true;
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_image_upload.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		
		$render->setAttribute('category', $this->mCategory);
		$render->setAttribute('target', xoops_getrequest('target'));
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL . "/imagemanager.php?imgcat_id=" . $this->mActionForm->get('imgcat_id') . "&target=" . xoops_getrequest('target'));
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		redirect_header(XOOPS_URL . "/imagemanager.php", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}
}

?>
