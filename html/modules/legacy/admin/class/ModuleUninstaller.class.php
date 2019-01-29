<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleUninstaller.class.php,v 1.6 2008/09/25 15:12:41 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstallUtils.class.php";

class Legacy_ModuleUninstaller
{
    /**
     * This instance is prepared automatically in the constructor.
     * 
     * @public
     * @var Legacy_ModuleInstallLog
     */
    public $mLog = null;
    
    public $_mForceMode = false;
    
    /**
     * @protected
     * @var XoopsModule
     * @remark [Precondition] _mXoopsModule has to be an object.
     */
    public $_mXoopsModule = null;
    
    /**
     * @brief XCube_Delegate
     * @attention
     *     This may be changed in the future.
     * @todo
     *     We may have to move this delegate to another class. Or, we may
     *     have to add the same delegates to other installer classes.
     */
    public $m_fireNotifyUninstallTemplateBegun;
    
    public function Legacy_ModuleUninstaller()
    {
        self::__construct();
    }

    public function __construct()
    {
        $this->mLog =new Legacy_ModuleInstallLog();
        $this->m_fireNotifyUninstallTemplateBegun =new XCube_Delegate();
        $this->m_fireNotifyUninstallTemplateBegun->register("Legacy_ModuleUninstaller._fireNotifyUninstallTemplateBegun");
    }
    
    /**
     * Sets the current XoopsModule.
     * 
     * @public
     * @param XoopsModule $xoopsModule
     */
    public function setCurrentXoopsModule(&$xoopsModule)
    {
        $this->_mXoopsModule =& $xoopsModule;
    }
    
    /**
     * Sets a value indicating whether the force mode is on.
     * @param bool $isForceMode
     */
    public function setForceMode($isForceMode)
    {
        $this->_mForceMode = $isForceMode;
    }
    
    /**
     * Deletes module information from XOOPS database because this class is
     * uninstaller.
     * 
     * @protected
     */
    public function _uninstallModule()
    {
        $moduleHandler =& xoops_gethandler('module');
        if (!$moduleHandler->delete($this->_mXoopsModule)) {
            $this->mLog->addError(_AD_LEGACY_ERROR_DELETE_MODULEINFO_FROM_DB);
        } else {
            $this->mLog->addReport(_AD_LEGACY_MESSAGE_DELETE_MODULEINFO_FROM_DB);
        }
    }

    /**
     * Drop table because this class is uninstaller.
     * 
     * @protected
     */
    public function _uninstallTables()
    {
        $root =& XCube_Root::getSingleton();
        $db =& $root->mController->getDB();

        $dirname = $this->_mXoopsModule->get('dirname');
        $t_search = array('{prefix}', '{dirname}', '{Dirname}', '{_dirname_}');
        $t_replace = array(XOOPS_DB_PREFIX, strtolower($dirname), ucfirst(strtolower($dirname)), $dirname);
        
        $tables = $this->_mXoopsModule->getInfo('tables');
        if ($tables != false && is_array($tables)) {
            foreach ($tables as $table) {
                //
                // TODO Do we need to check reserved core tables?
                //
                $t_tableName = $table;
                if (isset($this->_mXoopsModule->modinfo['cube_style']) && $this->_mXoopsModule->modinfo['cube_style'] == true) {
                    $t_tableName = str_replace($t_search, $t_replace, $table);
                } else {
                    $t_tableName = $db->prefix($table);
                }
                
                $sql = "DROP TABLE " . $t_tableName;
                
                if ($db->query($sql)) {
                    $this->mLog->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_DROP_TABLE, $t_tableName));
                } else {
                    $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_DROP_TABLE, $t_tableName));
                }
            }
        }
    }

    /**
     * Delete template because this class is uninstaller.
     * @protected
     */
    public function _uninstallTemplates()
    {
        $this->m_fireNotifyUninstallTemplateBegun->call(new XCube_Ref($this->_mXoopsModule));
        Legacy_ModuleInstallUtils::uninstallAllOfModuleTemplates($this->_mXoopsModule, $this->mLog);
    }

    /**
     * Delete all of module's blocks.
     * 
     * @note Templates Delete is move into Legacy_ModuleInstallUtils.
     */
    public function _uninstallBlocks()
    {
        Legacy_ModuleInstallUtils::uninstallAllOfBlocks($this->_mXoopsModule, $this->mLog);

        //
        // Additional
        //
        $tplHandler =& xoops_gethandler('tplfile');
        $criteria =new Criteria('tpl_module', $this->_mXoopsModule->get('dirname'));
        if (!$tplHandler->deleteAll($criteria)) {
            $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_DELETE_BLOCK_TEMPLATES, $tplHandler->db->error()));
        }
    }

    public function _uninstallPreferences()
    {
        Legacy_ModuleInstallUtils::uninstallAllOfConfigs($this->_mXoopsModule, $this->mLog);
        Legacy_ModuleInstallUtils::deleteAllOfNotifications($this->_mXoopsModule, $this->mLog);
        Legacy_ModuleInstallUtils::deleteAllOfComments($this->_mXoopsModule, $this->mLog);
    }

    public function _processScript()
    {
        $installScript = trim($this->_mXoopsModule->getInfo('onUninstall'));
        if ($installScript != false) {
            require_once XOOPS_MODULE_PATH . "/" . $this->_mXoopsModule->get('dirname') . "/" . $installScript;
            $funcName = 'xoops_module_uninstall_' . $this->_mXoopsModule->get('dirname');
            
            if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $funcName)) {
                $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_FAILED_TO_EXECUTE_CALLBACK, $funcName));
                return;
            }
            
            if (function_exists($funcName)) {
                // Because X2 can use reference parameter, Legacy doesn't use the following code;'
                // if (!call_user_func($funcName, $this->_mXoopsModule, new XCube_Ref($this->mLog))) {

                $result = $funcName($this->_mXoopsModule, new XCube_Ref($this->mLog));
                if (!$result) {
                    $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_FAILED_TO_EXECUTE_CALLBACK, $funcName));
                }
            }
        }
    }
    
    public function _processReport()
    {
        if (!$this->mLog->hasError()) {
            $this->mLog->add(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_UNINSTALLATION_MODULE_SUCCESSFUL, $this->_mXoopsModule->get('name')));
        } else {
            $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_UNINSTALLATION_MODULE_FAILURE, $this->_mXoopsModule->get('name')));
        }
    }

    /**
     * @todo Check whether $this->_mXoopsObject is ready.
     */
    public function executeUninstall()
    {
        $this->_uninstallTables();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }
        if ($this->_mXoopsModule->get('mid') != null) {
            $this->_uninstallModule();
            if (!$this->_mForceMode && $this->mLog->hasError()) {
                $this->_processReport();
                return false;
            }

            $this->_uninstallTemplates();
            if (!$this->_mForceMode && $this->mLog->hasError()) {
                $this->_processReport();
                return false;
            }

            $this->_uninstallBlocks();
            if (!$this->_mForceMode && $this->mLog->hasError()) {
                $this->_processReport();
                return false;
            }
            
            $this->_uninstallPreferences();
            if (!$this->_mForceMode && $this->mLog->hasError()) {
                $this->_processReport();
                return false;
            }
            
            $this->_processScript();
            if (!$this->_mForceMode && $this->mLog->hasError()) {
                $this->_processReport();
                return false;
            }
        }
        $this->_processReport();
        
        return true;
    }
}
