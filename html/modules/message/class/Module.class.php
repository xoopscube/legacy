<?php
/**
 * Message module for private messages and forward to email
 * @package    Message
 * @version    2.3.3
 * @author     Other authors Nuno Luciano aka gigamaster, 2020 XCL23
 * @author     Osamu Utsugi aka Marijuana
 * @copyright  (c) 2005-2023 The XOOPSCube Project, Authors
 * @license    GPL 3.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Message_Module extends Legacy_ModuleAdapter
{
    public function __construct($xoopsModule)
    {
        parent::__construct($xoopsModule);
    }

    public function hasAdminIndex()
    {
        //return true;
        $root = XCube_Root::getSingleton();
        return $root->mController->getHelpViewUrl($this->mXoopsModule);
    }

    public function getAdminIndex()
    {
        $root = XCube_Root::getSingleton();
        return $root->mController->getPreferenceEditUrl($this->mXoopsModule);
    }

    /**
     * Message Admin Menu
     * @return void|null
     */
    public function getAdminMenu()
    {
        if ($this->_mAdminMenuLoadedFlag) {
            return $this->mAdminMenu;
        }
        $root = XCube_Root::getSingleton();
        $this->mAdminMenu[] = [
            'link' => $root->mController->getPreferenceEditUrl($this->mXoopsModule),
            'title' => _PREFERENCES,
            'show' => true,
        ];
        $this->mAdminMenu[] = [
            'link' => $root->mController->getHelpViewUrl($this->mXoopsModule),
            'title' => _HELP,
            'absolute' => true
        ];
        $this->_mAdminMenuLoadedFlag = true;
        return $this->mAdminMenu;
    }

}
