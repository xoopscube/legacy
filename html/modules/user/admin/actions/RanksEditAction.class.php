<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/RanksAdminEditForm.class.php';

class User_RanksEditAction extends User_AbstractEditAction
{
    public function _getId()
    {
        return xoops_getrequest('rank_id');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('ranks');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_RanksAdminEditForm();
        $this->mActionForm->prepare();
    }
    
    public function _doExecute()
    {
        if (null != $this->mActionForm->mFormFile) {
            @unlink(XOOPS_UPLOAD_PATH . '/' . $this->mActionForm->mOldFileName);
            if (!$this->mActionForm->mFormFile->SaveAs(XOOPS_UPLOAD_PATH)) {
                return USER_FRAME_VIEW_ERROR;
            }
        }
        
        return parent::_doExecute();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('ranks_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=RanksList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=RanksList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=RanksList');
    }
}
