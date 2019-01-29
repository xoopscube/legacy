<?php
/**
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class Message_Module extends Legacy_ModuleAdapter
{
    public function __construct(&$xoopsModule)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($xoopsModule);
        //parent::Legacy_ModuleAdapter($xoopsModule);
    }
  
    public function hasAdminIndex()
    {
        return true;
    }
  
    public function getAdminIndex()
    {
        //return XOOPS_MODULE_URL.'/'.$this->mXoopsModule->get('dirname').'/admin/index.php';
    $root = XCube_Root::getSingleton();
        return $root->mController->getPreferenceEditUrl($this->mXoopsModule);
    }
  
    public function getAdminMenu()
    {
        if ($this->_mAdminMenuLoadedFlag) {
            return $this->mAdminMenu;
        }
        $root = XCube_Root::getSingleton();
        $this->mAdminMenu[] = array(
      'link' => $root->mController->getPreferenceEditUrl($this->mXoopsModule),
      'title' => _PREFERENCES,
      'show' => true
    );
        $this->_mAdminMenuLoadedFlag = true;
        return $this->mAdminMenu;
    }
}
