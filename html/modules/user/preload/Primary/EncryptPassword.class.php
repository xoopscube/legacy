<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// load password_compat - https://github.com/ircmaxell/password_compat
if (version_compare(PHP_VERSION, '5.5.0', '<') && (version_compare(PHP_VERSION, '5.3.7', '>=') || defined('PHP53_BCRYPT_Y2_FIXED'))) {
    include_once dirname(dirname(dirname(__FILE__))) . '/compat/password.php';
}

class User_EncryptPassword extends XCube_ActionFilter
{
    private $useNativeHashing = false;

    public function User_EncryptPassword(&$controller)
    {
        self::__construct($controller);
    }

    public function __construct(&$controller)
    {
        parent::__construct($controller);
        $this->useNativeHashing = (function_exists('password_hash') && function_exists('password_needs_rehash'));
    }

    public function preFilter()
    {
        $this->mController->mRoot->mDelegateManager->add("User.EncryptPassword", array($this, 'encryptPassword'));
        $this->mController->mRoot->mDelegateManager->add("User.PasswordVerify", array($this, 'passwordVerify'));
        $this->mController->mRoot->mDelegateManager->add("User.PasswordNeedsRehash", array($this, 'needsRehash'));
    }

    public function encryptPassword(&$password)
    {
        $input = $password;
        if ($this->useNativeHashing) {
            $password = password_hash($input, PASSWORD_DEFAULT);
        } else {
            $password = md5($input);
        }
    }

    public function passwordVerify(&$result, $password, $hash)
    {
        $result = false;
        if (strlen($hash) === 32) {
            $result = md5($password) === $hash;
        } else if ($this->useNativeHashing) {
            $result = password_verify($password, $hash);
        }
    }

    public function needsRehash(&$needs, $val)
    {
        if ($this->useNativeHashing) {
            $needs = password_needs_rehash($val, PASSWORD_DEFAULT);
        } else {
            // md5 length = 32
            $needs = strlen($val) !== 32;
        }
    }
}
