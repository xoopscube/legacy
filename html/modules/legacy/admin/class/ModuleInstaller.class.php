<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleInstaller.class.php,v 1.4 2008/10/26 04:00:40 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstallUtils.class.php";

/**
 * This class extends a base class for the process of install module. This is added
 * some private functions.
 * 
 * @todo It seems possibility to abstract with other installer classes.
 */
class Legacy_ModuleInstaller
{
    /**
     * @public
     * @var Legacy_ModuleInstallLog
     */
    public $mLog = null;
    
    public $_mForceMode = false;
    
    /**
     * @var XoopsModule
     * @remark [Precondition] _mXoopsModule has to be an object.
     */
    public $_mXoopsModule = null;
    
    public function Legacy_ModuleInstaller()
    {
        self::__construct();
    }

    public function __construct()
    {
        $this->mLog =new Legacy_ModuleInstallLog();
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
    
    public function _installTables()
    {
        Legacy_ModuleInstallUtils::installSQLAutomatically($this->_mXoopsModule, $this->mLog);
    }
    
    /**
     * @todo Do rewrite.
     */
    public function _installModule()
    {
        $moduleHandler =& xoops_gethandler('module');
        if (!$moduleHandler->insert($this->_mXoopsModule)) {
            $this->mLog->addError("*Could not install module information*");
            return false;
        }
        
        $gpermHandler =& xoops_gethandler('groupperm');

        //
        // Add a permission which administrators can manage.
        //
        if ($this->_mXoopsModule->getInfo('hasAdmin')) {
            $adminPerm =& $this->_createPermission(XOOPS_GROUP_ADMIN);
            $adminPerm->setVar('gperm_name', 'module_admin');

            if (!$gpermHandler->insert($adminPerm)) {
                $this->mLog->addError(_AD_LEGACY_ERROR_COULD_NOT_SET_ADMIN_PERMISSION);
            }
        }

        //
        // Add a permission which administrators can manage. (Special for Legacy System Module)
        //
        if ($this->_mXoopsModule->getVar('dirname') == 'system') {
            $root =& XCube_Root::getSingleton();
            $root->mLanguageManager->loadModuleAdminMessageCatalog('system');

            require_once XOOPS_ROOT_PATH . "/modules/system/constants.php";
            
            $fileHandler = opendir(XOOPS_ROOT_PATH . "/modules/system/admin");
            while ($file = readdir($fileHandler)) {
                $infoFile = XOOPS_ROOT_PATH . "/modules/system/admin/" . $file . "/xoops_version.php";
                if (file_exists($infoFile)) {
                    require_once $infoFile;
                    if (!empty($modversion['category'])) {
                        $sysAdminPerm  =& $this->_createPermission(XOOPS_GROUP_ADMIN);
                        $adminPerm->setVar('gperm_itemid', $modversion['category']);
                        $adminPerm->setVar('gperm_name', 'system_admin');
                        if (!$gpermHandler->insert($adminPerm)) {
                            $this->mLog->addError(_AD_LEGACY_ERROR_COULD_NOT_SET_SYSTEM_PERMISSION);
                        }
                        unset($sysAdminPerm);
                    }
                    unset($modversion);
                }
            }
        }
        
        if ($this->_mXoopsModule->getInfo('hasMain')) {
            $read_any = $this->_mXoopsModule->getInfo('read_any');
            if ($read_any) {
                $memberHandler =& xoops_gethandler('member');
                $groupObjects =& $memberHandler->getGroups();
                //
                // Add a permission all group members and guest can read.
                //
                foreach ($groupObjects as $group) {
                    $readPerm =& $this->_createPermission($group->getVar('groupid'));
                    $readPerm->setVar('gperm_name', 'module_read');

                    if (!$gpermHandler->insert($readPerm)) {
                        $this->mLog->addError(_AD_LEGACY_ERROR_COULD_NOT_SET_READ_PERMISSION);
                    }
                }
            } else {
                //
                // Add a permission which administrators can read.
                //
                $root =& XCube_Root::getSingleton();
                $groups = $root->mContext->mXoopsUser->getGroups(true);
                foreach ($groups as $mygroup) {
                    $readPerm =& $this->_createPermission($mygroup);
                    $readPerm->setVar('gperm_name', 'module_read');

                    if (!$gpermHandler->insert($readPerm)) {
                        $this->mLog->addError(_AD_LEGACY_ERROR_COULD_NOT_SET_READ_PERMISSION);
                    }
                }
            }
        }
    }

    /**
     * Create a permission object which has been initialized for admin.
     * For flexibility, creation only and not save it.
     * @access private
     * @param $group
     */
    public function &_createPermission($group)
    {
        $gpermHandler =& xoops_gethandler('groupperm');

        $perm =& $gpermHandler->create();

        $perm->setVar('gperm_groupid', $group);
        $perm->setVar('gperm_itemid', $this->_mXoopsModule->getVar('mid'));
        $perm->setVar('gperm_modid', 1);
        
        return $perm;
    }

    /**
     * @static
     */
    public function _installTemplates()
    {
        Legacy_ModuleInstallUtils::installAllOfModuleTemplates($this->_mXoopsModule, $this->mLog);
    }

    public function _installBlocks()
    {
        Legacy_ModuleInstallUtils::installAllOfBlocks($this->_mXoopsModule, $this->mLog);
    }

    public function _installPreferences()
    {
        Legacy_ModuleInstallUtils::installAllOfConfigs($this->_mXoopsModule, $this->mLog);
    }
    
    public function _processScript()
    {
        $installScript = trim($this->_mXoopsModule->getInfo('onInstall'));
        if ($installScript != false) {
            require_once XOOPS_MODULE_PATH . "/" . $this->_mXoopsModule->get('dirname') . "/" . $installScript;
            $funcName = 'xoops_module_install_' . $this->_mXoopsModule->get('dirname');
            
            if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", $funcName)) {
                $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_FAILED_TO_EXECUTE_CALLBACK, $funcName));
                return;
            }
            
            if (function_exists($funcName)) {
                // Because X2 can use reference parameter, Legacy doesn't use the following code;'
                // if (!call_user_func($funcName, $this->_mXoopsModule)) {

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
            $this->mLog->add(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_INSTALLATION_MODULE_SUCCESSFUL, $this->_mXoopsModule->get('name')));
        } else {
            $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_INSTALLATION_MODULE_FAILURE, $this->_mXoopsModule->get('name')));
        }
    }

    /**
     * @todo Check whether $this->_mXoopsObject is ready.
     */
    public function executeInstall()
    {
        $this->_installTables();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }

        $this->_installModule();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }

        $this->_installTemplates();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }

        $this->_installBlocks();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }
        
        $this->_installPreferences();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }
        
        $this->_processScript();
        if (!$this->_mForceMode && $this->mLog->hasError()) {
            $this->_processReport();
            return false;
        }
        
        $this->_processReport();
        
        return true;
    }
}
