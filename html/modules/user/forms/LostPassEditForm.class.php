<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: LostPassEditForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class User_LostPassEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.user.LostPassEditForm.TOKEN';
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['email'] =new XCube_StringProperty('email');
        $this->mFormProperties['code'] =new XCube_StringProperty('code');

        //
        // Set field properties
        //
        $this->mFieldProperties['email'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['email']->setDependsByArray(['required', 'email']);
        $this->mFieldProperties['email']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_EMAIL);
        $this->mFieldProperties['email']->addMessage('email', _MD_USER_ERROR_EMAIL, _MD_USER_LANG_EMAIL);
    }
}
