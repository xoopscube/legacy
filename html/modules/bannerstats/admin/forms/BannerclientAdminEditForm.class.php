<?php
/**
 * @package bannerstats
 * @version $Id: BannerclientAdminEditForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class Bannerstats_BannerclientAdminEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.bannerstats.BannerclientAdminEditForm.TOKEN' . $this->get('cid');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['cid'] =new XCube_IntProperty('cid');
        $this->mFormProperties['name'] =new XCube_StringProperty('name');
        $this->mFormProperties['contact'] =new XCube_StringProperty('contact');
        $this->mFormProperties['email'] =new XCube_StringProperty('email');
        $this->mFormProperties['login'] =new XCube_StringProperty('login');
        $this->mFormProperties['passwd'] =new XCube_StringProperty('passwd');
        $this->mFormProperties['extrainfo'] =new XCube_TextProperty('extrainfo');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['cid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['cid']->setDependsByArray(['required']);
        $this->mFieldProperties['cid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_CID);
    
        $this->mFieldProperties['name'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['name']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['name']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_NAME, '60');
        $this->mFieldProperties['name']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_NAME, '60');
        $this->mFieldProperties['name']->addVar('maxlength', '60');
    
        $this->mFieldProperties['contact'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['contact']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['contact']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_CONTACT, '60');
        $this->mFieldProperties['contact']->addVar('maxlength', '60');
    
        $this->mFieldProperties['email'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['email']->setDependsByArray(['maxlength', 'email']);
        $this->mFieldProperties['email']->addMessage('email', _AD_BANNERSTATS_ERROR_EMAIL, _AD_BANNERSTATS_LANG_EMAIL, '60');
        $this->mFieldProperties['email']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_EMAIL, '60');
        $this->mFieldProperties['email']->addVar('maxlength', '60');
    
        $this->mFieldProperties['login'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['login']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['login']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_LOGIN, '10');
        $this->mFieldProperties['login']->addVar('maxlength', '10');
    
        $this->mFieldProperties['passwd'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['passwd']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['passwd']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LANG_PASSWD, '10');
        $this->mFieldProperties['passwd']->addVar('maxlength', '10');
    }

    public function validateLogin()
    {
        if (strlen($this->get('login')) > 0) {
            $handler =& xoops_getmodulehandler('bannerclient', 'bannerstats');
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('login', $this->get('login')));
            $criteria->add(new Criteria('cid', $this->get('cid'), '<>'));

            if ($handler->getCount($criteria) > 0) {
                $this->addErrorMessage(_AD_BANNERSTATS_ERROR_LOGIN_REPETITION);
            }
        }
    }
    
    public function validatePasswd()
    {
        if (strlen($this->get('login')) > 0 && 0 == strlen($this->get('passwd'))) {
            $this->addErrorMessage(XCube_Utils::formatString(_AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LANG_PASSWD));
        }
    }

    public function load(&$obj)
    {
        $this->set('cid', $obj->get('cid'));
        $this->set('name', $obj->get('name'));
        $this->set('contact', $obj->get('contact'));
        $this->set('email', $obj->get('email'));
        $this->set('login', $obj->get('login'));
        $this->set('passwd', $obj->get('passwd'));
        $this->set('extrainfo', $obj->get('extrainfo'));
    }

    public function update(&$obj)
    {
        $obj->set('cid', $this->get('cid'));
        $obj->set('name', $this->get('name'));
        $obj->set('contact', $this->get('contact'));
        $obj->set('email', $this->get('email'));
        $obj->set('login', $this->get('login'));
        $obj->set('passwd', $this->get('passwd'));
        $obj->set('extrainfo', $this->get('extrainfo'));
    }
}
