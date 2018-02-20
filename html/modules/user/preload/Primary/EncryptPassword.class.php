<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_EncryptPassword extends XCube_ActionFilter
{
    public function preFilter()
    {
        $this->mController->mRoot->mDelegateManager->add("User.EncryptPassword", array(&$this, "encryptPassword"));
        $this->mController->mRoot->mDelegateManager->add("User.GetEncryptPasswordLength", array(&$this, "getEncryptPasswordLength"));

    }

    public function encryptPassword(&$password)
    {
        $password = md5($password);
    }

    public function getEncryptPasswordLength()
    {
        // md5 length
        return 32;
    }
}
