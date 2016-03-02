<?php
/**
 * @package user
 * @version $Id: MailjobDeleteAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/user/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/MailjobAdminDeleteForm.class.php";

class User_MailjobDeleteAction extends User_AbstractDeleteAction
{
    public function _getId()
    {
        return xoops_getrequest('mailjob_id');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('mailjob');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_MailjobAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("mailjob_delete.html");
        $render->setAttribute('actionForm', $this->mActionForm);
        #cubson::lazy_load('mailjob', $this->mObject);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=MailjobList");
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect("./index.php?action=MailjobList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=MailjobList");
    }
}
