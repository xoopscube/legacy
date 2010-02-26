<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once LECAT_TRUST_PATH . '/admin/class/installer/LecatInstallUtils.class.php';

/**
 * Lecat_Installer
**/
class Lecat_Installer
{
    /**
     * @brief   Legacy_ModuleInstallLog
    **/
    public $mLog = null;

    /**
     * @brief   bool
    **/
    private $_mForceMode = false;

    /**
     * @brief   XoopsModule
    **/
    private $_mXoopsModule = null;

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->mLog =new Legacy_ModuleInstallLog();
    }

    /**
     * setCurrentXoopsModule
     * 
     * @param   XoopsModule  &$xoopsModule
     * 
     * @return  void
    **/
    public function setCurrentXoopsModule(/*** XoopsModule ***/ &$xoopsModule)
    {
        $this->_mXoopsModule =& $xoopsModule;
    }

    /**
     * setForceMode
     * 
     * @param   bool  $isForceMode
     * 
     * @return  void
    **/
    public function setForceMode(/*** bool ***/ $isForceMode)
    {
        $this->_mForceMode = $isForceMode;
    }

    /**
     * _installTables
     * 
     * @param   void
     * 
     * @return  bool
    **/
    private function _installTables()
    {
        return Lecat_InstallUtils::installSQLAutomatically(
            $this->_mXoopsModule,
            $this->mLog
        );
    }

    /**
     * _installModule
     * 
     * @param   void
     * 
     * @return  bool
    **/
    private function _installModule()
    {
        $moduleHandler =& Lecat_Utils::getXoopsHandler('module');
        if(!$moduleHandler->insert($this->_mXoopsModule))
        {
            $this->mLog->addError(_MI_LECAT_INSTALL_ERROR_MODULE_INSTALLED);
            return false;
        }
    
        $gpermHandler =& Lecat_Utils::getXoopsHandler('groupperm');
    
        if($this->_mXoopsModule->getInfo('hasAdmin'))
        {
            $adminPerm =& $this->_createPermission(XOOPS_GROUP_ADMIN);
            $adminPerm->setVar('gperm_name','module_admin');
            if(!$gpermHandler->insert($adminPerm))
            {
                $this->mLog->addError(_MI_LECAT_INSTALL_ERROR_PERM_ADMIN_SET);
            }
        }
    
        if($this->_mXoopsModule->getInfo('hasMain'))
        {
            if($this->_mXoopsModule->getInfo('read_any'))
            {
                    $memberHandler =& Lecat_Utils::getXoopsHandler('member');
                    $groupObjects =& $memberHandler->getGroups();
                    foreach($groupObjects as $group)
                    {
                        $readPerm =& $this->_createPermission($group->getVar('groupid'));
                        $readPerm->setVar('gperm_name','module_read');
                        if(!$gpermHandler->insert($readPerm))
                        {
                            $this->mLog->addError(_MI_LECAT_INSTALL_ERROR_PERM_READ_SET);
                        }
                    }
            }
            else
            {
                    $root =& XCube_Root::getSingleton();
                    $groups = $root->mContext->mXoopsUser->getGroups();
                    foreach($groups as $group)
                    {
                        $readPerm =& $this->_createPermission($group);
                        $readPerm->setVar('gperm_name','module_read');
                        if(!$gpermHandler->insert($readPerm))
                        {
                            $this->mLog->addError(_MI_LECAT_INSTALL_ERROR_PERM_READ_SET);
                        }
                    }
            }
        }
    
        return true;
    }

    /**
     * &_createPermission
     * 
     * @param   int  $group
     * 
     * @return  XoopsGroupPerm
    **/
    private function &_createPermission(/*** int ***/ $group)
    {
        $gpermHandler =& Lecat_Utils::getXoopsHandler('groupperm');
        $perm =& $gpermHandler->create();
        $perm->setVar('gperm_groupid',$group);
        $perm->setVar('gperm_itemid',$this->_mXoopsModule->getVar('mid'));
        $perm->setVar('gperm_modid',1);
    
        return $perm;
    }

    /**
     * _installTemplates
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _installTemplates()
    {
        Lecat_InstallUtils::installAllOfModuleTemplates(
            $this->_mXoopsModule,
            $this->mLog
        );
    }

    /**
     * _installBlocks
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _installBlocks()
    {
        Lecat_InstallUtils::installAllOfBlocks(
            $this->_mXoopsModule,
            $this->mLog
        );
    }

    /**
     * _installPreferences
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _installPreferences()
    {
        Lecat_InstallUtils::installAllOfConfigs(
            $this->_mXoopsModule,
            $this->mLog
        );
    }

    /**
     * _processReport
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _processReport()
    {
        if(!$this->mLog->hasError())
        {
            $this->mLog->add(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_MSG_MODULE_INSTALLED,
                    $this->_mXoopsModule->getInfo('name')
                )
            );
        }
        else if(is_object($this->_mXoopsModule))
        {
            $this->mLog->addError(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_ERROR_MODULE_INSTALLED,
                    $this->_mXoopsModule->getInfo('name')
                )
            );
        }
        else
        {
            $this->mLog->addError(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_ERROR_MODULE_INSTALLED,
                    'something'
                )
            );
        }
    }

    /**
     * executeInstall
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function executeInstall()
    {
        $this->_installTables();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_installModule();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_installTemplates();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_installBlocks();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_installPreferences();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        $this->_processReport();
        return true;
    }
}

?>
