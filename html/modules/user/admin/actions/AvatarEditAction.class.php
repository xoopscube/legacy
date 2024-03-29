<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: AvatarEditAction.class.php,v 1.1 2007/05/15 02:34:41 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/AvatarAdminEditForm.class.php';

class User_AvatarEditAction extends User_AbstractEditAction
{
    public function _getId()
    {
        return xoops_getrequest('avatar_id');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('avatar');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new User_AvatarAdminEditForm();
        $this->mActionForm->prepare();
    }
    
    public function _doExecute()
    {
        if (null != $this->mActionForm->mFormFile) {
            if (!$this->mActionForm->mFormFile->saveAs(XOOPS_UPLOAD_PATH)) {
                return false;
            }
            
            if (null != $this->mActionForm->mOldFileName && 'blank.gif' != $this->mActionForm->mOldFileName) {
                @unlink(XOOPS_UPLOAD_PATH . '/' . $this->mActionForm->mOldFileName);
                
                //
                // Change user_avatar of all users who are setting this avatar.
                //
                if (!$this->mObject->isNew()) {
                    $linkHandler =& xoops_getmodulehandler('avatar_user_link');
                    $criteria =new Criteria('avatar_id', $this->mObject->get('avatar_id'));
                    $linkArr =& $linkHandler->getObjects($criteria);

                    $userHandler =& xoops_gethandler('user');
                    foreach ($linkArr as $link) {
                        $user =& $userHandler->get($link->get('user_id'));

                        if (is_object($user)) {
                            $user->set('user_avatar', $this->mObject->get('avatar_file'));
                            $userHandler->insert($user);
                        }
                        unset($user);
                    }
                }
            }
        }
        
        return parent::_doExecute();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('avatar_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=AvatarList', 1, _MD_USER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }
}
