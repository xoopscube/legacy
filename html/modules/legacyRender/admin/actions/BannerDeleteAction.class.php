<?php
/**
 * @package legacyRender
 * @version $Id: BannerDeleteAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/BannerAdminDeleteForm.class.php";

class LegacyRender_BannerDeleteAction extends LegacyRender_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('bid');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('banner');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new LegacyRender_BannerAdminDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("banner_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$this->mObject->loadBannerclient();
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BannerList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=BannerList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BannerList");
	}
}

?>
