<?php
/**
 * @package User
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: UserRecountForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class User_RecountForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.user.RecountForm.TOKEN' . $this->get('uid');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['uid'] =new XCube_IntProperty('uid');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['uid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['uid']->setDependsByArray(['required', 'objectExist']);
        $this->mFieldProperties['uid']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_UID);
        $this->mFieldProperties['uid']->addMessage('objectExist', _AD_USER_ERROR_OBJECTEXIST, _MD_USER_LANG_UID);
        $this->mFieldProperties['uid']->addVar('handler', 'users');
        $this->mFieldProperties['uid']->addVar('module', 'user');
    }

    public function load(&$obj)
    {
        $this->set('uid', $obj->get('uid'));
    }

    public function update(&$obj)
    {
        $obj->set('uid', $this->get('uid'));
    }
}
