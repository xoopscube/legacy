<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: UserDeleteAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/UserAdminDeleteForm.class.php';

class User_UserDeleteAction extends User_AbstractDeleteAction
{
    public function _getId()
    {
        return xoops_getrequest('uid');
    }

    public function &_getHandler()
    {
        $handler =& xoops_gethandler('user');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_UserAdminDeleteForm();
        $this->mActionForm->prepare();
    }
    
    public function _setupObject()
    {
        //
        // It is not possible to delete the super administrator.
        //
        parent::_setupObject();
        if (is_object($this->mObject) && 1 == $this->mObject->get('uid')) {
            $this->mObject = null;
        }
    }

    public function _doExecute()
    {
        XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete', new XCube_Ref($this->mObject));
        $handler =& xoops_gethandler('member');
        if ($handler->delete($this->mObject)) {
            XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete.Success', new XCube_Ref($this->mObject));
            return USER_FRAME_VIEW_SUCCESS;
        } else {
            XCube_DelegateUtils::call('Legacy.Admin.Event.UserDelete.Fail', new XCube_Ref($this->mObject));
            return USER_FRAME_VIEW_ERROR;
        }
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=UserList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=UserList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=UserList');
    }
}
