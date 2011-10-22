<?php
/**
 * @package legacyRender
 * @version $Id: BannerclientDeleteAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/BannerclientAdminDeleteForm.class.php";

class LegacyRender_BannerclientDeleteAction extends LegacyRender_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('cid');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('bannerclient');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new LegacyRender_BannerclientAdminDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("bannerclient_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$this->mObject->loadBanner();
		$this->mObject->loadBannerfinish();
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BannerclientList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=BannerclientList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BannerclientList");
	}
}

?>
