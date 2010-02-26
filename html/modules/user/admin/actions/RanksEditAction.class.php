<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/RanksAdminEditForm.class.php";

class User_RanksEditAction extends User_AbstractEditAction
{
	function _getId()
	{
		return xoops_getrequest('rank_id');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('ranks');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new User_RanksAdminEditForm();
		$this->mActionForm->prepare();
	}
	
	function _doExecute()
	{
		if ($this->mActionForm->mFormFile != null) {
			@unlink(XOOPS_UPLOAD_PATH . "/" . $this->mActionForm->mOldFileName);
			if (!$this->mActionForm->mFormFile->SaveAs(XOOPS_UPLOAD_PATH)) {
				return USER_FRAME_VIEW_ERROR;
			}
		}
		
		return parent::_doExecute();
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("ranks_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=RanksList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=RanksList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=RanksList");
	}
}

?>
