<?php
/**
 *
 * @package Legacy
 * @version $Id: MiscFriendForm.class.php,v 1.3 2008/09/25 15:12:39 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Legacy_MiscFriendForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.MiscFriendForm.TOKEN';
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['yname'] =new XCube_StringProperty('yname');
        $this->mFormProperties['ymail'] =new XCube_StringProperty('ymail');
        $this->mFormProperties['fname'] =new XCube_StringProperty('fname');
        $this->mFormProperties['fmail'] =new XCube_StringProperty('fmail');

        //
        // Set field properties
        //

        $this->mFieldProperties['yname'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['yname']->setDependsByArray(['required']);
        $this->mFieldProperties['yname']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_YNAME);

        $this->mFieldProperties['ymail'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['ymail']->setDependsByArray(['required', 'email']);
        $this->mFieldProperties['ymail']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_YMAIL);
        $this->mFieldProperties['ymail']->addMessage('required', _MD_LEGACY_ERROR_EMAIL, _MD_LEGACY_LANG_YMAIL);

        $this->mFieldProperties['fname'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['fname']->setDependsByArray(['required']);
        $this->mFieldProperties['fname']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_FNAME);

        $this->mFieldProperties['fmail'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['fmail']->setDependsByArray(['required', 'email']);
        $this->mFieldProperties['fmail']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_FMAIL);
        $this->mFieldProperties['fmail']->addMessage('email', _MD_LEGACY_ERROR_EMAIL, _MD_LEGACY_LANG_FMAIL);
    }

    public function load(&$user)
    {
        $this->set('yname', $user->get('uname'));
        $this->set('ymail', $user->get('email'));
    }

    public function update(&$mailer)
    {
        $mailer->assign('YOUR_NAME', $this->get('yname'));
        $mailer->assign('FRIEND_NAME', $this->get('fname'));
        $mailer->setToEmails($this->get('fmail'));
        $mailer->setFromEmail($this->get('ymail'));
        $mailer->setFromName($this->get('yname'));
    }
}
