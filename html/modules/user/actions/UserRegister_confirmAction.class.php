<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: UserRegister_confirmAction.class.php,v 1.3 2007/12/15 15:45:35 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/forms/UserConfirmForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/forms/UserRegisterEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/class/RegistMailBuilder.class.php';

/***
 * @internal
 * This action uses the special technic to realize confirming. It gets the
 * register action form which has been inputted in UserRegister, through
 * unserialize(). And, it uses a simple action form to confirm lastly.
 */
class User_UserRegister_confirmAction extends User_Action
{
    public $mActionForm = null;
    public $mRegistForm = null;
    public $mConfig = null;
    
    public $mNewUser = null;
    
    public $mRedirectMessage = null;

    /***
     * TODO this member function uses the old style delegate.
     * @param $controller
     * @param $xoopsUser
     * @param $moduleConfig
     */
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mConfig = $moduleConfig;

        $this->_getRegistForm($controller);
        $this->_processActionForm();
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null != XCube_Root::getSingleton()->mContext->mRequest->getRequest('_form_control_cancel')) {
            return USER_FRAME_VIEW_CANCEL;
        }

        $memberHandler =& xoops_gethandler('member');
        $this->mNewUser =& $memberHandler->createUser();
        $this->mRegistForm->update($this->mNewUser);
        $this->mNewUser->set('uorder', $controller->mRoot->mContext->getXoopsConfig('com_order'), true);
        $this->mNewUser->set('umode', $controller->mRoot->mContext->getXoopsConfig('com_mode'), true);
        if (1 == $this->mConfig['activation_type']) {
            $this->mNewUser->set('level', 1, true);
        }

        if (!$memberHandler->insertUser($this->mNewUser)) {
            $this->mRedirectMessage = _MD_USER_LANG_REGISTERNG;
            return USER_FRAME_VIEW_ERROR;
        }

        if (!$memberHandler->addUserToGroup(XOOPS_GROUP_USERS, $this->mNewUser->get('uid'))) {
            $this->mRedirectMessage = _MD_USER_LANG_REGISTERNG;
            return USER_FRAME_VIEW_ERROR;
        }

        $this->_clearRegistForm($controller);

        $this->_processMail($controller);
        $this->_eventNotifyMail($controller);
        
        XCube_DelegateUtils::call('Legacy.Event.RegistUser.Success', new XCube_Ref($this->mNewUser));
        
        return USER_FRAME_VIEW_SUCCESS;
    }
    
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_INPUT;
    }

    /***
     * Get regist actionform from Session and set it to the member property.
     * @access private
     * @param $controller
     */
    public function _getRegistForm(&$controller)
    {
        $this->mRegistForm = unserialize($_SESSION['user_register_actionform']);
        if (!is_object($this->mRegistForm)) {
            $controller->executeForward('./register.php?action=UserRegister');
        }
    }

    /***
     * Clear session.
     * @access private
     * @param $controller
     */
    public function _clearRegistForm(&$controller)
    {
        unset($_SESSION['user_register_actionform']);
    }
    
    public function _processMail(&$controller)
    {
        $activationType = $this->mConfig['activation_type'];
        
        if (1 == $activationType) {
            return;
        }

        // Wmm..
        $builder = (0 == $activationType) ? new User_RegistUserActivateMailBuilder()
                                          : new User_RegistUserAdminActivateMailBuilder();

        $director =new User_UserRegistMailDirector($builder, $this->mNewUser, $controller->mRoot->mContext->getXoopsConfig(), $this->mConfig);
        $director->contruct();
        $mailer =& $builder->getResult();
        XCube_DelegateUtils::call('Legacy.Event.RegistUser.SendMail', new XCube_Ref($mailer), (0 == $activationType)? 'Register' : 'AdminActivate');
        
        if (!$mailer->send()) {
            // TODO CHECKS and use '_MD_USER_ERROR_YOURREGMAILNG'
        }
    }
    
    public function _eventNotifyMail(&$controller)
    {
        if (1 == $this->mConfig['new_user_notify'] && !empty($this->mConfig['new_user_notify_group'])) {
            $builder =new User_RegistUserNotifyMailBuilder();
            $director =new User_UserRegistMailDirector($builder, $this->mNewUser, $controller->mRoot->mContext->getXoopsConfig(), $this->mConfig);
            $director->contruct();
            $mailer =& $builder->getResult();
            XCube_DelegateUtils::call('Legacy.Event.RegistUser.SendMail', new XCube_Ref($mailer), 'Notify');
            $mailer->send();
        }
    }

    public function _processActionForm()
    {
        $this->mActionForm =new User_UserConfirmForm();
        $this->mActionForm->prepare();
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect(XOOPS_URL . '/', 1, $this->mRedirectMessage);
    }

    /**
     * executeViewCancel
     *
     * @param                        $controller
     * @param                        $xoopsUser
     * @param XCube_RenderTarget    &$render
     *
     * @return    void
     */
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward(XOOPS_URL.'/register.php');
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_register_confirm.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('registForm', $this->mRegistForm);
    }
    
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $activationType = $this->mConfig['activation_type'];

        if (0 == $activationType) {
            $render->setTemplateName('user_register_finish.html');
            $render->setAttribute('complete_message', _MD_USER_MESSAGE_YOURREGISTERED);
        } elseif (1 == $activationType) {
            $controller->executeRedirect(XOOPS_URL . '/', 4, _MD_USER_MESSAGE_ACTLOGIN);
        } elseif (2 == $activationType) {
            $render->setTemplateName('user_register_finish.html');
            $render->setAttribute('complete_message', _MD_USER_MESSAGE_YOURREGISTERED2);
        } else {
            //
            // This case is never.
            //
            $render->setTemplateName('user_register_finish.html');
            $render->setAttribute('complete_message', _MD_USER_MESSAGE_YOURREGISTERED2);
        }
    }
}
