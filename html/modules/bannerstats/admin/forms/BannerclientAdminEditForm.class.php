<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Validator.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';

class Bannerstats_BannerclientAdminEditForm extends XCube_ActionForm
{
    public function getTokenName(): string
    {
        $cid = $this->get('cid');
        return 'module.bannerstats.BannerclientAdminEditForm.TOKEN' . ($cid ? '.' . $cid : '.NEW');
    }

    public function prepare()
    {
        // Set form properties for all editable banner client fields
        $this->mFormProperties['cid'] = new XCube_IntProperty('cid');
        $this->mFormProperties['name'] = new XCube_StringProperty('name');
        $this->mFormProperties['contact'] = new XCube_StringProperty('contact');
        $this->mFormProperties['email'] = new XCube_StringProperty('email');
        $this->mFormProperties['login'] = new XCube_StringProperty('login');
        $this->mFormProperties['passwd'] = new XCube_StringProperty('passwd');
        $this->mFormProperties['passwd_confirm'] = new XCube_StringProperty('passwd_confirm');

        // Add the address and contact fields
        $this->mFormProperties['tel'] = new XCube_StringProperty('tel');
        $this->mFormProperties['address1'] = new XCube_StringProperty('address1');
        $this->mFormProperties['address2'] = new XCube_StringProperty('address2');
        $this->mFormProperties['city'] = new XCube_StringProperty('city');
        $this->mFormProperties['region'] = new XCube_StringProperty('region');
        $this->mFormProperties['postal_code'] = new XCube_StringProperty('postal_code');
        $this->mFormProperties['country_code'] = new XCube_StringProperty('country_code');
        $this->mFormProperties['extrainfo'] = new XCube_TextProperty('extrainfo');
        $this->mFormProperties['status'] = new XCube_IntProperty('status');


        // Set field properties with validation rules
        $this->mFieldProperties['name'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['name']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['name']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_NAME);
        $this->mFieldProperties['name']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_NAME, '255');
        $this->mFieldProperties['name']->addVar('maxlength', 255);

        $this->mFieldProperties['contact'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['contact']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['contact']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_CONTACT, '255');
        $this->mFieldProperties['contact']->addVar('maxlength', 255);

        $this->mFieldProperties['email'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['email']->setDependsByArray(['required', 'email', 'maxlength']);
        $this->mFieldProperties['email']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_EMAIL);
        $this->mFieldProperties['email']->addMessage('email', _AD_BANNERSTATS_ERROR_EMAIL, _AD_BANNERSTATS_EMAIL);
        $this->mFieldProperties['email']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_EMAIL, '255');
        $this->mFieldProperties['email']->addVar('maxlength', 255);

        $this->mFieldProperties['login'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['login']->setDependsByArray(['required', 'maxlength']);
        $this->mFieldProperties['login']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_LOGIN);
        $this->mFieldProperties['login']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_LOGIN, '50'); // definitions.php has 50
        $this->mFieldProperties['login']->addVar('maxlength', 50);

        $this->mFieldProperties['passwd'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['passwd']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['passwd']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_PASSWD, '255');
        $this->mFieldProperties['passwd']->addVar('maxlength', 255);

        $this->mFieldProperties['tel'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['tel']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['tel']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_TEL, '50');
        $this->mFieldProperties['tel']->addVar('maxlength', 50);

        $this->mFieldProperties['address1'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['address1']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['address1']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_ADDRESS1, '255');
        $this->mFieldProperties['address1']->addVar('maxlength', 255);

        $this->mFieldProperties['address2'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['address2']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['address2']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_ADDRESS2, '255');
        $this->mFieldProperties['address2']->addVar('maxlength', 255);

        $this->mFieldProperties['city'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['city']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['city']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_CITY, '100');
        $this->mFieldProperties['city']->addVar('maxlength', 100);

        $this->mFieldProperties['region'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['region']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['region']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_REGION, '100');
        $this->mFieldProperties['region']->addVar('maxlength', 100);

        $this->mFieldProperties['postal_code'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['postal_code']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['postal_code']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_POSTAL_CODE, '20');
        $this->mFieldProperties['postal_code']->addVar('maxlength', 20);

        $this->mFieldProperties['country_code'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['country_code']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['country_code']->addMessage('maxlength', _AD_BANNERSTATS_ERROR_MAXLENGTH, _AD_BANNERSTATS_COUNTRY_CODE, '2');
        $this->mFieldProperties['country_code']->addVar('maxlength', 2);
        
        $this->mFieldProperties['extrainfo'] = new XCube_FieldProperty($this);

        $this->mFieldProperties['status'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['status']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['status']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_STATUS);
        $this->mFieldProperties['status']->addMessage('intRange', _AD_BANNERSTATS_ERROR_STATUS, _AD_BANNERSTATS_STATUS, '0', '1');
        $this->mFieldProperties['status']->addVar('min', 0);
        $this->mFieldProperties['status']->addVar('max', 1);
    }

    public function validatePasswordConfirmation()
    {
        $passwd = $this->get('passwd');
        $passwd_confirm = $this->get('passwd_confirm');
        $cid = $this->get('cid');

        if (empty($cid) || !empty($passwd)) {
            if (empty($passwd)) {
                 $this->addErrorMessage(sprintf(_AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_PASSWD));
            } elseif ($passwd !== $passwd_confirm) {
                $this->addErrorMessage(sprintf(_AD_BANNERSTATS_ERROR_PASSWD_CONFIRM_MATCH, _AD_BANNERSTATS_PASSWD, _AD_BANNERSTATS_PASSWD_CONFIRM));
            }
        }
    }

    public function validate()
    {
        parent::validate();

        $this->validatePasswordConfirmation();
    }

    public function load(&$obj)
    {
        $this->set('cid', $obj->get('cid'));
        $this->set('name', $obj->get('name') ?? '');
        $this->set('contact', $obj->get('contact') ?? '');
        $this->set('email', $obj->get('email') ?? '');
        $this->set('login', $obj->get('login') ?? '');
        $this->set('passwd', '');
        $this->set('passwd_confirm', '');

        // Load the address and contact fields
        $this->set('tel', $obj->get('tel') ?? '');
        $this->set('address1', $obj->get('address1') ?? '');
        $this->set('address2', $obj->get('address2') ?? '');
        $this->set('city', $obj->get('city') ?? '');
        $this->set('region', $obj->get('region') ?? '');
        $this->set('postal_code', $obj->get('postal_code') ?? '');
        $this->set('country_code', $obj->get('country_code') ?? '');
        $this->set('extrainfo', $obj->get('extrainfo') ?? '');
        $this->set('status', $obj->get('status'));
    }

    public function update(&$obj)
    {
        $obj->set('name', $this->get('name'));
        $obj->set('contact', $this->get('contact'));
        $obj->set('email', $this->get('email'));
        $obj->set('login', $this->get('login'));

        $new_passwd = $this->get('passwd');
        if (!empty($new_passwd)) {
            $obj->set('passwd', password_hash($new_passwd, PASSWORD_DEFAULT));
        }

        // Update the address and contact fields
        $obj->set('tel', $this->get('tel'));
        $obj->set('address1', $this->get('address1'));
        $obj->set('address2', $this->get('address2'));
        $obj->set('city', $this->get('city'));
        $obj->set('region', $this->get('region'));
        $obj->set('postal_code', $this->get('postal_code'));
        $obj->set('country_code', $this->get('country_code'));
        $obj->set('extrainfo', $this->get('extrainfo'));
        $obj->set('status', $this->get('status'));
        
        // Update timestamp
        $obj->set('last_updated', time());
        if ($obj->isNew()) {
            $obj->set('date_created', time());
        }
    }
}
