<?php
/**
 * @package legacyRender
 * @version $Id: BannerEditAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/BannerAdminEditForm.class.php";

class LegacyRender_BannerEditAction extends LegacyRender_AbstractEditAction
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

	function _setupObject()
	{
		parent::_setupObject();
		if (is_object($this->mObject) && $this->mObject->isNew()) {
			$this->mObject->set('cid', xoops_getrequest('cid'));
		}
	}
	
	function _setupActionForm()
	{
		$this->mActionForm =new LegacyRender_BannerAdminEditForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("banner_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$this->mObject->loadBannerclient();
		$render->setAttribute('object', $this->mObject);
		
		$bannerclientHandler =& xoops_getmodulehandler('bannerclient');
		$bannerclientArr =& $bannerclientHandler->getObjects();
		foreach (array_keys($bannerclientArr) as $key) {
			$bannerclientArr[$key]->loadBanner();
		}
		$render->setAttribute('bannerclientArr', $bannerclientArr);
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
