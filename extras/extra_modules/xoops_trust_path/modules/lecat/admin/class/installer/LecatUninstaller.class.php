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
 * Lecat_Uninstaller
**/
class Lecat_Uninstaller
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
     * _uninstallModule
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _uninstallModule()
    {
        $moduleHandler =& Lecat_Utils::getXoopsHandler('module');
    
        if($moduleHandler->delete($this->_mXoopsModule))
        {
            $this->mLog->addReport(_MI_LECAT_INSTALL_MSG_MODULE_INFORMATION_DELETED);
        }
        else
        {
            $this->mLog->addError(_MI_LECAT_INSTALL_ERROR_MODULE_INFORMATION_DELETED);
        }
    }

    /**
     * _uninstallTables
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _uninstallTables()
    {
        $root =& XCube_Root::getSingleton();
        $db =& $root->mController->getDB();
        $dirname = $this->_mXoopsModule->get('dirname');
    
        $tables =& $this->_mXoopsModule->getInfo('tables');
        if(is_array($tables))
        {
            foreach($tables as $table)
            {
                $tableName = str_replace(
                    array('{prefix}','{dirname}'),
                    array(XOOPS_DB_PREFIX,$dirname),
                    $table
                );
                $sql = sprintf('drop table `%s`;',$tableName);
                
                if($db->query($sql))
                {
                    $this->mLog->addReport(
                        XCube_Utils::formatString(
                            _MI_LECAT_INSTALL_MSG_TABLE_DOROPPED,
                            $tableName
                        )
                    );
                }
                else
                {
                    $this->mLog->addError(
                        XCube_Utils::formatString(
                            _MI_LECAT_INSTALL_ERROR_TABLE_DOROPPED,
                            $tableName
                        )
                    );
                }
            }
        }
    }

    /**
     * _uninstallTemplates
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _uninstallTemplates()
    {
        Lecat_InstallUtils::uninstallAllOfModuleTemplates($this->_mXoopsModule,$this->mLog,false);
    }

    /**
     * _uninstallBlocks
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _uninstallBlocks()
    {
        Lecat_InstallUtils::uninstallAllOfBlocks($this->_mXoopsModule,$this->mLog);
    
        $tplHandler =& Lecat_Utils::getXoopsHandler('tplfile');
        $cri =new Criteria('tpl_module',$this->_mXoopsModule->get('dirname'));
        if(!$tplHandler->deleteAll($cri))
        {
            $this->mLog->addError(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_ERROR_BLOCK_TPL_DELETED,
                    $tplHandler->db->error()
                )
            );
        }
    }

    /**
     * _uninstallPreferences
     * 
     * @param   void
     * 
     * @return  void
    **/
    private function _uninstallPreferences()
    {
        Lecat_InstallUtils::uninstallAllOfConfigs($this->_mXoopsModule,$this->mLog);
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
                    _MI_LECAT_INSTALL_MSG_MODULE_UNINSTALLED,
                    $this->_mXoopsModule->get('name')
                )
            );
        }
        else if(is_object($this->_mXoopsModule))
        {
            $this->mLog->addError(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_ERROR_MODULE_UNINSTALLED,
                    $this->_mXoopsModule->get('name')
                )
            );
        }
        else
        {
            $this->mLog->addError(
                XCube_Utils::formatString(
                    _MI_LECAT_INSTALL_ERROR_MODULE_UNINSTALLED,
                    'something'
                )
            );
        }
    }

    /**
     * executeUninstall
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function executeUninstall()
    {
        $this->_uninstallTables();
        if(!$this->_mForceMode && $this->mLog->hasError())
        {
            $this->_processReport();
            return false;
        }
    
        if($this->_mXoopsModule->get('mid') != null)
        {
            $this->_uninstallModule();
            if(!$this->_mForceMode && $this->mLog->hasError())
            {
                $this->_processReport();
                return false;
            }
    
            $this->_uninstallTemplates();
            if(!$this->_mForceMode && $this->mLog->hasError())
            {
                $this->_processReport();
                return false;
            }
    
            $this->_uninstallBlocks();
            if(!$this->_mForceMode && $this->mLog->hasError())
            {
                $this->_processReport();
                return false;
            }
    
            $this->_uninstallPreferences();
            if(!$this->_mForceMode && $this->mLog->hasError())
            {
                $this->_processReport();
                return false;
            }
        }
    
        $this->_processReport();
        return true;
    }
}

?>
