<?php
/**
 *
 * @package Legacy
 * @version $Id: ImagecategoryDeleteAction.class.php,v 1.3 2008/09/25 15:11:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImagecategoryAdminDeleteForm.class.php";

class Legacy_ImagecategoryDeleteAction extends Legacy_AbstractDeleteAction
{
	function _getId()
	{
		return isset($_REQUEST['imgcat_id']) ? xoops_getrequest('imgcat_id') : 0;
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('imagecategory');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_ImagecategoryAdminDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("imagecategory_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=ImagecategoryList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=ImagecategoryList", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=ImagecategoryList");
	}
}

?>
