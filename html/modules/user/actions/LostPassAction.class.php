<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: LostPassAction.class.php,v 1.3 2008/07/20 05:55:52 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/forms/LostPassEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/user/class/LostPassMailBuilder.class.php';

/***
 * @internal
 * @public
 * The process of lostpass. This action sends a mail even if the input mail
 * address isn't registered in the site. Because displaying error message in
 * such case shows the part of the personal information. We will discuss about
 * this spec.
 */
class User_LostPassAction extends User_Action
{
    /***
     * @var User_LostPassEditForm
     */
    public $mActionForm = null;
    
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new User_LostPassEditForm();
        $this->mActionForm->prepare();
    }
    
    public function isSecure()
    {
        return false;
    }
    
    // Allow anonymous users only.
    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        return !$controller->mRoot->mContext->mUser->mIdentity->isAuthenticated();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $root =& XCube_Root::getSingleton();
        $code = $root->mContext->mRequest->getRequest('code') ?? '';
        $email = $root->mContext->mRequest->getRequest('email') ?? '';
        
        if (0 == strlen($code) || 0 == strlen($email)) {
            return USER_FRAME_VIEW_INPUT;
        } else {
            return $this->_updatePassword($controller);
        }
    }

    public function _updatePassword(&$controller)
    {
        $extraVars = [];
        $this->mActionForm->fetch();

        $userHandler =& xoops_gethandler('user');
        $criteria =new CriteriaCompo(new Criteria('email', $this->mActionForm->get('email')));
        $criteria->add(new Criteria('pass', $this->mActionForm->get('code'), '=', '', 'LEFT(%s, 5)'));
        $lostUserArr =& $userHandler->getObjects($criteria);
        
        if (is_array($lostUserArr) && count($lostUserArr) > 0) {
            $lostUser =& $lostUserArr[0];
        } else {
            return USER_FRAME_VIEW_ERROR;
        }

        $newpass = xoops_makepass();
        $extraVars['newpass'] = $newpass;

        $lostUser->set('pass', User_Utils::encryptPassword($newpass), true);
        if (!$userHandler->insert($lostUser, true)) {
            return USER_FRAME_VIEW_ERROR;
        }

        $builder =new User_LostPass2MailBuilder();
        //$getXConfig = getXoopsConfig();  error 'lost-password'
		$getXConfig = $controller->mRoot->mContext->getXoopsConfig();
		
		
        //$director =new User_LostPassMailDirector($builder, $lostUser, $controller->mRoot->mContext->$getXConfig, $extraVars);
        $director =new User_LostPassMailDirector($builder, $lostUser, $getXConfig, $extraVars);
        
        $director->contruct();
        $xoopsMailer =& $builder->getResult();
        XCube_DelegateUtils::call('Legacy.Event.RegistUser.SendMail', new XCube_Ref($xoopsMailer), 'LostPass2');
        if (!$xoopsMailer->send()) {
            // $xoopsMailer->getErrors();
            return USER_FRAME_VIEW_ERROR;
        }

        return USER_FRAME_VIEW_SUCCESS;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
        
        if ($this->mActionForm->hasError()) {
            return USER_FRAME_VIEW_INPUT;
        }
        
        $userHandler =& xoops_gethandler('user');
        $lostUserArr =& $userHandler->getObjects(new Criteria('email', $this->mActionForm->get('email')));

        if (is_array($lostUserArr) && (count($lostUserArr) > 0) ) {
            $lostUser =& $lostUserArr[0];
        } else {
            return USER_FRAME_VIEW_ERROR;
        }

        $builder =new User_LostPass1MailBuilder();
    
		$root =& XCube_Root::getSingleton();
		$getXConfig = $root->mContext->getXoopsConfig(); // fix lost-password
		

        //$director =new User_LostPassMailDirector($builder, $lostUser, $controller->mRoot->mContext->$getXConfig);
        $director =new User_LostPassMailDirector($builder, $lostUser, $getXConfig);
        
        $director->contruct();
        $xoopsMailer =& $builder->getResult();
        XCube_DelegateUtils::call('Legacy.Event.RegistUser.SendMail', new XCube_Ref($xoopsMailer), 'LostPass1');

        if (!$xoopsMailer->send()) {
            // $xoopsMailer->getErrors();
            return USER_FRAME_VIEW_ERROR;
        }

        return USER_FRAME_VIEW_SUCCESS;
    }
    
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('user_lostpass.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect(XOOPS_URL . '/', 3, _MD_USER_MESSAGE_SEND_PASSWORD);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect(XOOPS_URL . '/', 3, _MD_USER_ERROR_SEND_MAIL);
    }
}
