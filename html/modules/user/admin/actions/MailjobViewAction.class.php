<?php
/**
 * @package user
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/user/class/AbstractViewAction.class.php";

class User_MailjobViewAction extends User_AbstractViewAction
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
    
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_SUCCESS;
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("mailjob_view.html");
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect("./index.php?action=MailjobList", 1, _AD_USER_ERROR_CONTENT_IS_NOT_FOUND);
    }
}
