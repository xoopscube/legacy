<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: GroupEditAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/GroupAdminEditForm.class.php';

class User_GroupEditAction extends User_AbstractEditAction
{
    public function _getId()
    {
        return xoops_getrequest('groupid');
    }
    
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('groups');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_GroupAdminEditForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('group_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('index.php?action=GroupList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('index.php?action=GroupList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('index.php?action=GroupList');
    }
}
