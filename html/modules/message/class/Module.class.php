<?php
/**
 * @license https://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
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
        return true;
    }

    public function getAdminIndex()
    {
        $root = XCube_Root::getSingleton();
        return $root->mController->getPreferenceEditUrl($this->mXoopsModule);
    }

    public function getAdminMenu()
    {
        if ($this->_mAdminMenuLoadedFlag) {
            return $this->mAdminMenu;
        }
        $root = XCube_Root::getSingleton();
        $this->mAdminMenu[] = [
            'link' => $root->mController->getPreferenceEditUrl($this->mXoopsModule),
            'title' => _PREFERENCES,
            'show' => true
        ];
        $this->_mAdminMenuLoadedFlag = true;
        return $this->mAdminMenu;
    }
}
