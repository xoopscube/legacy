<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: MailjobAdminEditForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class User_MailjobAdminEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.user.MailjobAdminEditForm.TOKEN' . $this->get('mailjob_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['mailjob_id'] =new XCube_IntProperty('mailjob_id');
        $this->mFormProperties['title'] =new XCube_StringProperty('title');
        $this->mFormProperties['body'] =new XCube_TextProperty('body');
        $this->mFormProperties['from_name'] =new XCube_StringProperty('from_name');
        $this->mFormProperties['from_email'] =new XCube_StringProperty('from_email');
        $this->mFormProperties['is_pm'] =new XCube_BoolProperty('is_pm');
        $this->mFormProperties['is_mail'] =new XCube_BoolProperty('is_mail');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['mailjob_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['mailjob_id']->setDependsByArray(['required']);
        $this->mFieldProperties['mailjob_id']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_MAILJOB_ID);
    
        $this->mFieldProperties['title'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['title']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['title']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _AD_USER_LANG_TITLE, '191');
        $this->mFieldProperties['title']->addVar('maxlength', '191');
    
        $this->mFieldProperties['body'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['body']->setDependsByArray(['required']);
        $this->mFieldProperties['body']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_BODY);
    
        $this->mFieldProperties['from_name'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['from_name']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['from_name']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, '191');
        $this->mFieldProperties['from_name']->addVar('maxlength', '191');
    
        $this->mFieldProperties['from_email'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['from_email']->setDependsByArray(['maxlength', 'email']);
        $this->mFieldProperties['from_email']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, '191');
        $this->mFieldProperties['from_email']->addVar('maxlength', '191');
        $this->mFieldProperties['from_email']->addMessage('email', _AD_USER_ERROR_EMAIL, _AD_USER_LANG_FROM_EMAIL);
    }
    
    public function validateFrom_email()
    {
        if ($this->get('is_mail') && 0 == strlen($this->get('from_email'))) {
            $this->addErrorMessage(XCube_Utils::formatString(_MD_USER_ERROR_REQUIRED, _AD_USER_LANG_FROM_EMAIL));
        }
    }
    
    public function validate()
    {
        parent::validate();
        
        if (!$this->get('is_pm') && !$this->get('is_mail')) {
            $this->addErrorMessage(_AD_USER_ERROR_MAILJOB_SEND_MEANS);
        }
    }

    public function load(&$obj)
    {
        $this->set('mailjob_id', $obj->get('mailjob_id'));
        $this->set('title', $obj->get('title'));
        $this->set('body', $obj->get('body'));
        $this->set('from_name', $obj->get('from_name'));
        $this->set('from_email', $obj->get('from_email'));
        $this->set('is_pm', $obj->get('is_pm'));
        $this->set('is_mail', $obj->get('is_mail'));
    }

    public function update(&$obj)
    {
        $obj->set('mailjob_id', $this->get('mailjob_id'));
        $obj->set('title', $this->get('title'));
        $obj->set('body', $this->get('body'));
        $obj->set('from_name', $this->get('from_name'));
        $obj->set('from_email', $this->get('from_email'));
        $obj->set('is_pm', $this->get('is_pm'));
        $obj->set('is_mail', $this->get('is_mail'));
    }
}
