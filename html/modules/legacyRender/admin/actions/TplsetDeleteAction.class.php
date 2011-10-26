<?php
/**
 * @package legacyRender
 * @version $Id: TplsetDeleteAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplsetDeleteForm.class.php";

class LegacyRender_TplsetDeleteAction extends LegacyRender_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('tplset_id');
	}

	function &_getHandler()
	{
		$handler = xoops_getmodulehandler('tplset');
		return $handler;
	}

	function _setupObject()
	{
		$id = $this->_getId();
		
		$this->mObjectHandler = $this->_getHandler();
		
		$this->mObject =& $this->mObjectHandler->get($id);
		if (is_object($this->mObject) && $this->mObject->get('tplset_name') == 'default') {
			$this->mObject = null;
		}
	}
	
	function _setupActionForm()
	{
		$this->mActionForm =new LegacyRender_TplsetDeleteForm();
		$this->mActionForm->prepare();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("tplset_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=TplsetList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=TplsetList");
	}
}

?>
