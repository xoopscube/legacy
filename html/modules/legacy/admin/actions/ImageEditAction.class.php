<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageEditAction.class.php,v 1.3 2008/09/25 15:11:53 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/admin/actions/ImageCreateAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImageAdminEditForm.class.php";

class Legacy_ImageEditAction extends Legacy_ImageCreateAction
{
	function _getId()
	{
		return isset($_REQUEST['image_id']) ? xoops_getrequest('image_id') : 0;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_ImageAdminEditForm();
		$this->mActionForm->prepare();
	}
	
	function isEnableCreate()
	{
		return false;
	}
	
	function _enableCatchImgcat()
	{
		return false;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$this->mObject->loadImagecategory();

		$render->setTemplateName("image_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		
		$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
		$t_category = $handler->get($this->mObject->get('imgcat_id'));
		
		$categoryArr =& $handler->getObjects(new Criteria('imgcat_storetype', $t_category->get('imgcat_storetype')));
		$render->setAttribute('categoryArr', $categoryArr);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward('./index.php?action=ImageList&imgcat_id=' . $this->mObject->get('imgcat_id'));
	}
}

?>
