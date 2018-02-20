<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class EncryptSha1Password extends XCube_ActionFilter
{
	public function preFilter()
	{
	    //登録されてる暗号化デリゲート先をリセットする。
		$this->mController->mRoot->mDelegateManager->reset("User.EncryptPassword");
        $this->mController->mRoot->mDelegateManager->reset("User.getEncryptPasswordLength");

		$this->mController->mRoot->mDelegateManager->add("User.EncryptPassword", array(&$this, "encryptPassword"));
        $this->mController->mRoot->mDelegateManager->add("User.GetEncryptPasswordLength", array(&$this, "getEncryptPasswordLength"));
	}

	public function encryptPassword(&$password)
	{
		$password = sha1($password);
	}

    public function getEncryptPasswordLength()
    {
        // sha1 length
        return 40;
    }

}
