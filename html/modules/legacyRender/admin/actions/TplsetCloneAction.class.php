<?php
/**
 * @package legacyRender
 * @version $Id: TplsetCloneAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/admin/actions/TplsetEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplsetCloneForm.class.php";

class LegacyRender_TplsetCloneAction extends LegacyRender_TplsetEditAction
{
	var $mCloneObject = null;
	
	function _setupObject()
	{
		parent::_setupObject();
		$this->mCloneObject =& $this->mObjectHandler->create();
	}
	
	function _setupActionForm()
	{
		$this->mActionForm =new LegacyRender_TplsetCloneForm();
		$this->mActionForm->prepare();
	}

	function isAllowDefault()
	{
		return true;
	}

	function execute(&$controller, &$xoopsUser)
	{
		if ($this->mObject == null) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}

		if (xoops_getrequest('_form_control_cancel') != null) {
			return LEGACYRENDER_FRAME_VIEW_CANCEL;
		}

		//
		// If image is no, the data has to continue to keep his value.
		//
		$this->mActionForm->load($this->mCloneObject);

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if($this->mActionForm->hasError()) {
			return LEGACYRENDER_FRAME_VIEW_INPUT;
		}
			
		$this->mActionForm->update($this->mCloneObject);
		
		return $this->mObjectHandler->insertClone($this->mObject, $this->mCloneObject) ? LEGACYRENDER_FRAME_VIEW_SUCCESS
		                                                     : LEGACYRENDER_FRAME_VIEW_ERROR;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("tplset_clone.html");
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
