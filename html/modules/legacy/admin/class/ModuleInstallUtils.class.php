<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleInstallUtils.class.php,v 1.11 2008/10/26 04:07:23 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstallInformation.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleInstaller.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleUpdater.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/class/ModuleUninstaller.class.php";

require_once XOOPS_ROOT_PATH."/class/template.php";

define("MODINSTALL_LOGTYPE_REPORT", "report");
define("MODINSTALL_LOGTYPE_WARNING", "warning");
define("MODINSTALL_LOGTYPE_ERROR", "error");

/**
 * A temporary log class.
 */
class Legacy_ModuleInstallLog
{
    public $mFetalErrorFlag = false;
    public $mMessages = array();

    public function add($msg)
    {
        $this->mMessages[] = array('type' => MODINSTALL_LOGTYPE_REPORT, 'message' => $msg);
    }

    public function addReport($msg)
    {
        $this->add($msg);
    }
    
    public function addWarning($msg)
    {
        $this->mMessages[] = array('type' => MODINSTALL_LOGTYPE_WARNING, 'message' => $msg);
    }

    public function addError($msg)
    {
        $this->mMessages[] = array('type' => MODINSTALL_LOGTYPE_ERROR, 'message' => $msg);
        $this->mFetalErrorFlag = true;
    }
    
    public function hasError()
    {
        return $this->mFetalErrorFlag;
    }
}

/**
 * This class is collection of static utility functions for module installation.
 * These functions are useful for Legacy modules' system-fixed-installer and
 * modules' custom-installer. All functions for the custom-installer are added
 * notes as "FOR THE CUSTOM-ISNTALLER".
 * 
 * For more attentions, see base classes for the custom-installer.
 * 
 * @see Legacy_PhasedUpgrader
 */
class Legacy_ModuleInstallUtils
{
    /**
     * This is factory for the installer. The factory reads xoops_version
     * without modulehandler, to prevent cache in modulehandler.
     */
    public static function &createInstaller($dirname)
    {
        $installer =& Legacy_ModuleInstallUtils::_createInstaller($dirname, 'installer', 'Legacy_ModuleInstaller');
        return $installer;
    }
    
    /**
     * This is factory for the updater. The factory reads xoops_version
     * without modulehandler, to prevent cache in modulehandler.
     */
    public static function &createUpdater($dirname)
    {
        $updater =& Legacy_ModuleInstallUtils::_createInstaller($dirname, 'updater', 'Legacy_ModulePhasedUpgrader');
        return $updater;
    }
    
    /**
     * This is factory for the uninstaller. The factory reads xoops_version
     * without modulehandler, to prevent cache in modulehandler.
     */
    public static function &createUninstaller($dirname)
    {
        $uninstaller =& Legacy_ModuleInstallUtils::_createInstaller($dirname, 'uninstaller', 'Legacy_ModuleUninstaller');
        return $uninstaller;
    }
    
    /**
     * The generic factory for installers. This function is used by other
     * utility functions.
     * @param string $dirname
     * @param string $mode 'installer' 'updater' or 'uninstaller'
     * @param string $defaultClassName
     */
    public static function &_createInstaller($dirname, $mode, $defaultClassName)
    {
        $info = array();
        
        $filepath = XOOPS_MODULE_PATH . "/${dirname}/xoops_version.php";
        if (file_exists($filepath)) {
            @include $filepath;
            $info = $modversion;
        }

        if (isset($info['legacy_installer']) && is_array($info['legacy_installer']) && isset($info['legacy_installer'][$mode])) {
            $updateInfo = $info['legacy_installer'][$mode];
                
            $className = $updateInfo['class'];
            $filePath = isset($updateInfo['filepath']) ? $updateInfo['filepath'] : XOOPS_MODULE_PATH . "/${dirname}/admin/class/${className}.class.php";
            $namespace = isset($updateInfo['namespace']) ? $updateInfo['namespace'] : ucfirst($dirname);
                
            if ($namespace != null) {
                $className = "${namespace}_${className}";
            }
                
            if (!XC_CLASS_EXISTS($className) && file_exists($filePath)) {
                require_once $filePath;
            }
                
            if (XC_CLASS_EXISTS($className)) {
                $installer =new $className();
                return $installer;
            }
        }
        
        $installer =new $defaultClassName();
        return $installer;
    }
    
    
    /**
     * Executes SQL file which xoops_version of $module specifies. This
     * function is usefull for installers, but it's impossible to control
     * for detail.
     * 
     * @static
     * @param XoopsModule $module
     * @param Legacy_ModuleInstallLog $log
     * @note FOR THE CUSTOM-INSTALLER
     */
    public static function installSQLAutomatically(&$module, &$log)
    {
        $dbTypeAliases = array(
            'mysqli' => 'mysql'
        );
        $sqlfileInfo =& $module->getInfo('sqlfile');
        $dirname = $module->getVar('dirname');
        $dbType = (isset($sqlfileInfo[XOOPS_DB_TYPE]) || !isset($dbTypeAliases[XOOPS_DB_TYPE]))? XOOPS_DB_TYPE : $dbTypeAliases[XOOPS_DB_TYPE];

        if (!isset($sqlfileInfo[$dbType])) {
            return;
        }
        
        $sqlfile = $sqlfileInfo[$dbType];
        $sqlfilepath = XOOPS_MODULE_PATH . "/${dirname}/${sqlfile}";
        
        if (isset($module->modinfo['cube_style']) && $module->modinfo['cube_style'] == true) {
            require_once XOOPS_MODULE_PATH . "/legacy/admin/class/Legacy_SQLScanner.class.php";
            $scanner =new Legacy_SQLScanner();
            $scanner->setDB_PREFIX(XOOPS_DB_PREFIX);
            $scanner->setDirname($module->get('dirname'));
            
            if (!$scanner->loadFile($sqlfilepath)) {
                $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_SQL_FILE_NOT_FOUND, $sqlfile));
                return false;
            }
    
            $scanner->parse();
            $sqls = $scanner->getSQL();
            
            $root =& XCube_Root::getSingleton();
            $db =& $root->mController->getDB();
            
            //
            // TODO The following variable exists for rollback, but it is not implemented.
            //
            foreach ($sqls as $sql) {
                if (!$db->query($sql)) {
                    $log->addError($db->error());
                    return;
                }
            }
            
            $log->addReport(_AD_LEGACY_MESSAGE_DATABASE_SETUP_FINISHED);
        } else {
            require_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
            
            $reservedTables = array('avatar', 'avatar_users_link', 'block_module_link', 'xoopscomments', 'config', 'configcategory', 'configoption', 'image', 'imagebody', 'imagecategory', 'imgset', 'imgset_tplset_link', 'imgsetimg', 'groups','groups_users_link','group_permission', 'online', 'bannerclient', 'banner', 'bannerfinish', 'priv_msgs', 'ranks', 'session', 'smiles', 'users', 'newblocks', 'modules', 'tplfile', 'tplset', 'tplsource', 'xoopsnotifications');

            $root =& XCube_Root::getSingleton();
            $db =& $root->mController->mDB;
            
            $sql_query = fread(fopen($sqlfilepath, 'r'), filesize($sqlfilepath));
            $sql_query = trim($sql_query);
            SqlUtility::splitMySqlFile($pieces, $sql_query);
            $created_tables = array();
            foreach ($pieces as $piece) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery($piece, $db->prefix());
                if (!$prefixed_query) {
                    $log->addError("${piece} is not a valid SQL!");
                    return;
                }
                
                // check if the table name is reserved
                if (!in_array($prefixed_query[4], $reservedTables)) {
                    // not reserved, so try to create one
                    if (!$db->query($prefixed_query[0])) {
                        $log->addError($db->error());
                        return;
                    } else {
                        if (!in_array($prefixed_query[4], $created_tables)) {
                            $log->addReport('  Table ' . $db->prefix($prefixed_query[4]) . ' created.');
                            $created_tables[] = $prefixed_query[4];
                        } else {
                            $log->addReport('  Data inserted to table ' . $db->prefix($prefixed_query[4]));
                        }
                    }
                } else {
                    // the table name is reserved, so halt the installation
                    $log->addError($prefixed_query[4] . " is a reserved table!");
                    return;
                }
            }
        }
    }
    
    /**
     * Installs all of module templates $module specify. This function is
     * usefull for installer and updater. In the case of updater, you should
     * uninstall all of module templates before this function.
     * 
     * This function gets informations about templates from xoops_version.
     * 
     * @warning
     * 
     * This function depends the specific spec of Legacy_RenderSystem, but this
     * static function is needed by the 2nd installer of Legacy System.
     * 
     * @static
     * @param XoopsModule $module
     * @param Legacy_ModuleInstallLog $log
     * @note FOR THE CUSTOM-INSTALLER
     * @see Legacy_ModuleInstallUtils::uninstallAllOfModuleTemplates()
     */
    public static function installAllOfModuleTemplates(&$module, &$log)
    {
        $templates = $module->getInfo('templates');
        if ($templates != false) {
            foreach ($templates as $template) {
                Legacy_ModuleInstallUtils::installModuleTemplate($module, $template, $log);
            }
        }
    }
    
    /**
     * Inserts the specified template to DB.
     * 
     * @warning
     * 
     * This function depends the specific spec of Legacy_RenderSystem, but this
     * static function is needed by the 2nd installer of Legacy System.
     * 
     * @static
     * @param XoopsModule $module
     * @param string[][] $template
     * @param Legacy_ModuleInstallLog $log
     * @return bool
     * 
     * @note This is not usefull a litte for custom-installers.
     * @todo We'll need the way to specify the template by identity or others.
     */
    public static function installModuleTemplate($module, $template, &$log)
    {
        $tplHandler =& xoops_gethandler('tplfile');

        $fileName = trim($template['file']);

        $tpldata = Legacy_ModuleInstallUtils::readTemplateFile($module->get('dirname'), $fileName);
        if ($tpldata == false) {
            return false;
        }

        //
        // Create template file object, then store it.
        //
        $tplfile =& $tplHandler->create();
        $tplfile->setVar('tpl_refid', $module->getVar('mid'));
        $tplfile->setVar('tpl_lastimported', 0);
        $tplfile->setVar('tpl_lastmodified', time());

        if (preg_match("/\.css$/i", $fileName)) {
            $tplfile->setVar('tpl_type', 'css');
        } else {
            $tplfile->setVar('tpl_type', 'module');
        }

        $tplfile->setVar('tpl_source', $tpldata, true);
        $tplfile->setVar('tpl_module', $module->getVar('dirname'));
        $tplfile->setVar('tpl_tplset', 'default');
        $tplfile->setVar('tpl_file', $fileName, true);

        $description = isset($template['description']) ? $template['description'] : '';
        $tplfile->setVar('tpl_desc', $description, true);
        
        if ($tplHandler->insert($tplfile)) {
            $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_TEMPLATE_INSTALLED, $fileName));
        } else {
            $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_INSTALL_TEMPLATE, $fileName));
            return false;
        }
    }

    /**
     * Uninstalls all of module templates $module specify. This function is
     * usefull for uninstaller and updater. In the case of update, you should
     * call this function before installAllOfModuleTemplates(). In the case of
     * uninstall, you must set 'false' to $defaultOnly.
     * 
     * This function gets informations about templates from the database.
     * 
     * @warning
     * 
     * This function depends the specific spec of Legacy_RenderSystem, but this
     * static function is needed by the 2nd installer of Legacy System.
     * 
     * @static
     * @param XoopsModule $module
     * @param Legacy_ModuleInstallLog $log
     * @param bool $defaultOnly Indicates whether this function deletes templates from all of tplsets.
     * @note FOR THE CUSTOM-INSTALLER
     * @see Legacy_ModuleInstallUtils::installAllOfModuleTemplates()
     */
    public static function _uninstallAllOfModuleTemplates(&$module, $tplset, &$log)
    {
        //
        // The following processing depends on the structure of Legacy_RenderSystem.
        //
        $tplHandler =& xoops_gethandler('tplfile');
        $delTemplates = null;
        
        $delTemplates =& $tplHandler->find($tplset, 'module', $module->get('mid'));
        
        if (is_array($delTemplates) && count($delTemplates) > 0) {
            //
            // clear cache
            //
            $xoopsTpl =new XoopsTpl();
            $xoopsTpl->clear_cache(null, "mod_" . $module->get('dirname'));
            
            foreach ($delTemplates as $tpl) {
                if (!$tplHandler->delete($tpl)) {
                    $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_TEMPLATE_UNINSTALLED, $tpl->get('tpl_file')));
                }
            }
        }
    }
    
    public static function uninstallAllOfModuleTemplates(&$module, &$log)
    {
        Legacy_ModuleInstallUtils::_uninstallAllOfModuleTemplates($module, null, $log);
    }

    public static function clearAllOfModuleTemplatesForUpdate(&$module, &$log)
    {
        Legacy_ModuleInstallUtils::_uninstallAllOfModuleTemplates($module, 'default', $log);
    }
    
    /**
     * Installs all of blocks $module specify.
     * 
     * This function gets informations about blocks from xoops_version.
     * 
     * @static
     * @param XoopsModule $module
     * @param Legacy_ModuleInstallLog $log
     * @note FOR THE CUSTOM-INSTALLER
     * @see Legacy_ModuleInstallUtils::uninstallAllOfBlocks()
     */
    public static function installAllOfBlocks(&$module, &$log)
    {
        $definedBlocks = $module->getInfo('blocks');
        if ($definedBlocks == false) {
            return true;
        }
        
        $func_num = 0;
        foreach ($definedBlocks as $block) {
            $successFlag = true;
            $updateblocks = array();
            
            // Try (1) --- func_num
            foreach ($definedBlocks as $idx => $block) {
                if (isset($block['func_num'])) {
                    $updateblocks[$idx] = $block;
                } else {
                    $successFlag = false;
                    break;
                }
            }
            
            // Try (2) --- index pattern
            if ($successFlag == false) {
                $successFlag = true;
                $updateblocks = array();
                foreach ($definedBlocks as $idx => $block) {
                    if (is_int($idx)) {
                        $block['func_num'] = $idx;
                        $updateblocks[$idx] = $block;
                    } else {
                        $successFlag = false;
                        break;
                    }
                }
            }
            
            // Try (3) --- automatic
            if ($successFlag == false) {
                $successFlag = true;
                $updateblocks = array();

                $func_num = 0;
                foreach ($definedBlocks as $block) {
                    $block['func_num'] = $func_num;
                    $updateblocks[] = $block;
                }
            }
        }
        
        foreach ($updateblocks as $block) {
            $newBlock =& Legacy_ModuleInstallUtils::createBlockByInfo($module, $block, $block['func_num']);
            Legacy_ModuleInstallUtils::installBlock($module, $newBlock, $block, $log);
        }
    }

    /**
     * Uninstalls all of blocks which $module specifies, and its permissions.
     * 
     * This function gets informations about templates from the database.
     * 
     * @static
     * @param XoopsModule $module
     * @param Legacy_ModuleInstallLog $log
     * @return bool
     * 
     * @note FOR THE CUSTOM-INSTALLER
     * @see Legacy_ModuleInstallUtils::installAllOfBlocks()
     * @see Legacy_ModuleInstallUtils::uninstallBlock()
     */
    public static function uninstallAllOfBlocks(&$module, &$log)
    {
        $handler =& xoops_gethandler('block');
        $criteria = new Criteria('mid', $module->get('mid'));

        $blockArr =& $handler->getObjectsDirectly($criteria);
        
        $successFlag = true;
        
        foreach (array_keys($blockArr) as $idx) {
            $successFlag &= Legacy_ModuleInstallUtils::uninstallBlock($blockArr[$idx], $log);
        }
        
        return $successFlag;
    }
    
    /**
     * Create XoopsBlock object by array that is defined in xoops_version, return it.
     * @param $module XoopsModule
     * @param $block array
     * @return XoopsBlock
     */
    public static function &createBlockByInfo(&$module, $block, $func_num)
    {
        $options = isset($block['options']) ? $block['options'] : null;
        $edit_func = isset($block['edit_func']) ? $block['edit_func'] : null;
        $template = isset($block['template']) ? $block['template'] : null;
        $visible = isset($block['visible']) ? $block['visible'] : (isset($block['visible_any']) ? $block['visible_any']: 0);
        $blockHandler =& xoops_gethandler('block');
        $blockObj =& $blockHandler->create();

        $blockObj->set('mid', $module->getVar('mid'));
        $blockObj->set('options', $options);
        $blockObj->set('name', $block['name']);
        $blockObj->set('title', $block['name']);
        $blockObj->set('block_type', 'M');
        $blockObj->set('c_type', 1);
        $blockObj->set('isactive', 1);
        $blockObj->set('dirname', $module->getVar('dirname'));
        $blockObj->set('func_file', $block['file']);
        
        //
        // IMPORTANT CONVENTION
        //
        $show_func = "";
        if (isset($block['class'])) {
            $show_func = "cl::" . $block['class'];
        } else {
            $show_func = $block['show_func'];
        }
        
        $blockObj->set('show_func', $show_func);
        $blockObj->set('edit_func', $edit_func);
        $blockObj->set('template', $template);
        $blockObj->set('last_modified', time());
        $blockObj->set('visible', $visible);
        
        $func_num = isset($block['func_num']) ? intval($block['func_num']) : $func_num;
        $blockObj->set('func_num', $func_num);

        return $blockObj;
    }
    
    /**
     * This function can receive both new and update.
     * @param $module XoopsModule
     * @param $blockObj XoopsBlock
     * @param $block array
     * @return bool
     */
    public static function installBlock(&$module, &$blockObj, &$block, &$log)
    {
        $isNew = $blockObj->isNew();
        $blockHandler =& xoops_gethandler('block');

        if (!empty($block['show_all_module'])) {
            $autolink = false;
        } else {
            $autolink = true;
        }
        if (!$blockHandler->insert($blockObj, $autolink)) {
            $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_INSTALL_BLOCK, $blockObj->getVar('name')));

            return false;
        } else {
            $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_BLOCK_INSTALLED, $blockObj->getVar('name')));

            $tplHandler =& xoops_gethandler('tplfile');

            Legacy_ModuleInstallUtils::installBlockTemplate($blockObj, $module, $log);
            
            //
            // Process of a permission.
            //
            if ($isNew) {
                if (!empty($block['show_all_module'])) {
                    $link_sql = "INSERT INTO " . $blockHandler->db->prefix('block_module_link') . " (block_id, module_id) VALUES (".$blockObj->getVar('bid').", 0)";
                    if (!$blockHandler->db->query($link_sql)) {
                        $log->addWarning(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SET_LINK, $blockObj->getVar('name')));
                    }
                }
                $gpermHandler =& xoops_gethandler('groupperm');
                $bperm =& $gpermHandler->create();
                $bperm->setVar('gperm_itemid', $blockObj->getVar('bid'));
                $bperm->setVar('gperm_name', 'block_read');
                $bperm->setVar('gperm_modid', 1);
                
                if (!empty($block['visible_any'])) {
                    $memberHandler =& xoops_gethandler('member');
                    $groupObjects =& $memberHandler->getGroups();
                    foreach ($groupObjects as $group) {
                        $bperm->setVar('gperm_groupid', $group->getVar('groupid'));
                        $bperm->setNew();
                        if (!$gpermHandler->insert($bperm)) {
                            $log->addWarning(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SET_BLOCK_PERMISSION, $blockObj->getVar('name')));
                        }
                    }
                } else {
                    $root =& XCube_Root::getSingleton();
                    $groups = $root->mContext->mXoopsUser->getGroups(true);
                    foreach ($groups as $mygroup) {
                        $bperm->setVar('gperm_groupid', $mygroup);
                        $bperm->setNew();
                        if (!$gpermHandler->insert($bperm)) {
                            $log->addWarning(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_SET_BLOCK_PERMISSION, $blockObj->getVar('name')));
                        }
                    }
                }
            }

            return true;
        }
    }
    
    /**
     * Uninstalls a block which $block specifies. In the same time, deletes
     * permissions for the block.
     * 
     * @param XoopsBlock $block
     * @param Legacy_ModuleInstallLog $log
     * @note FOR THE CUSTOM-INSTALLER
     * 
     * @todo error handling & delete the block's template.
     */
    public static function uninstallBlock(&$block, &$log)
    {
        $blockHandler =& xoops_gethandler('block');
        $blockHandler->delete($block);
        $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_UNINSTALLATION_BLOCK_SUCCESSFUL, $block->get('name')));
        
        //
        // Deletes permissions
        //
        $gpermHandler =& xoops_gethandler('groupperm');
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('gperm_name', 'block_read'));
        $criteria->add(new Criteria('gperm_itemid', $block->get('bid')));
        $criteria->add(new Criteria('gperm_modid', 1));
        $gpermHandler->deleteAll($criteria);
    }
    
    /**
     * Save the information of block's template specified and the source code of it
     * to database.
     * @return bool
     */
    public static function installBlockTemplate(&$block, &$module, &$log)
    {
        if ($block->get('template') == null) {
            return true;
        }
        
        $tplHandler =& xoops_gethandler('tplfile');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('tpl_type', 'block'));
        $criteria->add(new Criteria('tpl_tplset', 'default'));
        $criteria->add(new Criteria('tpl_module', $module->get('dirname')));
        $criteria->add(new Criteria('tpl_file', $block->get('template')));
        $tplfiles =& $tplHandler->getObjects($criteria);

        if (count($tplfiles) > 0) {
            $tplfile =& $tplfiles[0];
        } else {
            $tplfile =& $tplHandler->create();
            $tplfile->set('tpl_refid', $block->get('bid'));
            $tplfile->set('tpl_tplset', 'default');
            $tplfile->set('tpl_file', $block->get('template'));
            $tplfile->set('tpl_module', $module->get('dirname'));
            $tplfile->set('tpl_type', 'block');
            // $tplfile->setVar('tpl_desc', $tpl_desc);
            $tplfile->set('tpl_lastimported', 0);
        }
        
        $tplSource = Legacy_ModuleInstallUtils::readTemplateFile($module->get('dirname'), $block->get('template'), true);
        $tplfile->set('tpl_source', $tplSource);
        $tplfile->set('tpl_lastmodified', time());

        if ($tplHandler->insert($tplfile)) {
            $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_BLOCK_TEMPLATE_INSTALLED, $block->get('template')));
            return true;
        } else {
            $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_BLOCK_TEMPLATE_INSTALL, $block->get('name')));
            return false;
        }
    }
    
    /**
     * Read template file, return it.
     * 
     * @note This is must, but it depends on ...
     */
    public static function readTemplateFile($dirname, $fileName, $isblock = false)
    {
        //
        // Load template data
        //
        if ($isblock) {
            $filePath = XOOPS_MODULE_PATH . "/" . $dirname . "/templates/blocks/" . $fileName;
        } else {
            $filePath = XOOPS_MODULE_PATH . "/" . $dirname . "/templates/" . $fileName;
        }

        if (!file_exists($filePath)) {
            return false;
        }

        $lines = file($filePath);
        if ($lines == false) {
            return false;
        }

        $tpldata = "";
        foreach ($lines as $line) {
            //
            // Unify linefeed to "\r\n" 
            //
            $tpldata .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $line));
        }
        
        return $tpldata;
    }

    public static function installAllOfConfigs(&$module, &$log)
    {
        $dirname = $module->get('dirname');
        
        $fileReader =new Legacy_ModinfoX2FileReader($dirname);
        $preferences =& $fileReader->loadPreferenceInformations();
        
        //
        // Preferences
        //
        foreach (array_keys($preferences->mPreferences) as $idx) {
            Legacy_ModuleInstallUtils::installPreferenceByInfo($preferences->mPreferences[$idx], $module, $log);
        }
        
        //
        // Comments
        //
        foreach (array_keys($preferences->mComments) as $idx) {
            Legacy_ModuleInstallUtils::installPreferenceByInfo($preferences->mComments[$idx], $module, $log);
        }
        
        //
        // Notifications
        //
        foreach (array_keys($preferences->mNotifications) as $idx) {
            Legacy_ModuleInstallUtils::installPreferenceByInfo($preferences->mNotifications[$idx], $module, $log);
        }
    }
    
    public static function installPreferenceByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_gethandler('config');
        $config =& $handler->createConfig();
        $config->set('conf_modid', $module->get('mid'));
        $config->set('conf_catid', 0);
        $config->set('conf_name', $info->mName);
        $config->set('conf_title', $info->mTitle);
        $config->set('conf_desc', $info->mDescription);
        $config->set('conf_formtype', $info->mFormType);
        $config->set('conf_valuetype', $info->mValueType);
        $config->setConfValueForInput($info->mDefault);
        $config->set('conf_order', $info->mOrder);
        
        if (count($info->mOption->mOptions) > 0) {
            foreach (array_keys($info->mOption->mOptions) as $idx) {
                $option =& $handler->createConfigOption();
                $option->set('confop_name', $info->mOption->mOptions[$idx]->mName);
                $option->set('confop_value', $info->mOption->mOptions[$idx]->mValue);
                $config->setConfOptions($option);
                unset($option);
            }
        }
        
        if ($handler->insertConfig($config)) {
            $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_INSERT_CONFIG, $config->get('conf_name')));
        } else {
            $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_INSERT_CONFIG, $config->get('conf_name')));
        }
    }
    
    /**
     * Get & build config items from Manifesto by specific module object.
     */
    public static function &getConfigInfosFromManifesto(&$module)
    {
        $configInfos = $module->getInfo('config');
        
        //
        // Insert comment config by old style.
        //
        if ($module->getVar('hascomments') !=0) {
            require_once XOOPS_ROOT_PATH . "/include/comment_constants.php";

            $configInfos[] = array('name' => 'com_rule',
                                     'title' => '_CM_COMRULES',
                                     'description' => '',
                                     'formtype' => 'select',
                                     'valuetype' => 'int',
                                     'default' => 1,
                                     'options' => array('_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE, '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL, '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER, '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN)
                               );

            $configInfos[] = array('name' => 'com_anonpost',
                                     'title' => '_CM_COMANONPOST',
                                     'description' => '',
                                     'formtype' => 'yesno',
                                     'valuetype' => 'int',
                                     'default' => 0
                               );
        }

        //
        // Insert comment config by old style.
        //
        if ($module->get('hasnotification') != 0) {
            require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
            require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
            
            $t_options = array();
            $t_options['_NOT_CONFIG_DISABLE'] = XOOPS_NOTIFICATION_DISABLE;
            $t_options['_NOT_CONFIG_ENABLEBLOCK'] = XOOPS_NOTIFICATION_ENABLEBLOCK;
            $t_options['_NOT_CONFIG_ENABLEINLINE'] = XOOPS_NOTIFICATION_ENABLEINLINE;
            $t_options['_NOT_CONFIG_ENABLEBOTH'] = XOOPS_NOTIFICATION_ENABLEBOTH;
            
            $configInfos[] = array(
                'name' => 'notification_enabled',
                'title' => '_NOT_CONFIG_ENABLE',
                'description' => '_NOT_CONFIG_ENABLEDSC',
                'formtype' => 'select',
                'valuetype' => 'int',
                'default' => XOOPS_NOTIFICATION_ENABLEBOTH,
                'options' => $t_options
            );
            
            //
            // FIXME: doesn't work when update module... can't read back the
            //        array of options properly...  " changing to &quot;
            //

            unset($t_options);
            
            $t_options = array();
            $t_categoryArr =& notificationCategoryInfo('', $module->get('mid'));
            foreach ($t_categoryArr as $t_category) {
                $t_eventArr =& notificationEvents($t_category['name'], false, $module->get('mid'));
                foreach ($t_eventArr as $t_event) {
                    if (!empty($t_event['invisible'])) {
                        continue;
                    }
                    $t_optionName = $t_category['title'] . ' : ' . $t_event['title'];
                    $t_options[$t_optionName] = $t_category['name'] . '-' . $t_event['name'];
                }
            }
                
            $configInfos[] = array(
                'name' => 'notification_events',
                'title' => '_NOT_CONFIG_EVENTS',
                'description' => '_NOT_CONFIG_EVENTSDSC',
                'formtype' => 'select_multi',
                'valuetype' => 'array',
                'default' => array_values($t_options),
                'options' => $t_options
            );
        }
        
        return $configInfos;
    }
    
    /**
     * Delete all configs of $module.
     *
     * @param $module XoopsModule
     */
    public static function uninstallAllOfConfigs(&$module, &$log)
    {
        if ($module->get('hasconfig') == 0) {
            return;
        }

        $configHandler =& xoops_gethandler('config');
        $configs =& $configHandler->getConfigs(new Criteria('conf_modid', $module->get('mid')));

        if (count($configs) == 0) {
            return;
        }

        foreach ($configs as $config) {
            $configHandler->deleteConfig($config);
        }
    }
    
    public static function smartUpdateAllOfBlocks(&$module, &$log)
    {
        $dirname = $module->get('dirname');
        
        $fileReader =new Legacy_ModinfoX2FileReader($dirname);
        $latestBlocks =& $fileReader->loadBlockInformations();
        
        $dbReader =new Legacy_ModinfoX2DBReader($dirname);
        $currentBlocks =& $dbReader->loadBlockInformations();
        
        $currentBlocks->update($latestBlocks);

        foreach (array_keys($currentBlocks->mBlocks) as $idx) {
            switch ($currentBlocks->mBlocks[$idx]->mStatus) {
                case LEGACY_INSTALLINFO_STATUS_LOADED:
                    Legacy_ModuleInstallUtils::updateBlockTemplateByInfo($currentBlocks->mBlocks[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_UPDATED:
                    Legacy_ModuleInstallUtils::updateBlockByInfo($currentBlocks->mBlocks[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_NEW:
                    Legacy_ModuleInstallUtils::installBlockByInfo($currentBlocks->mBlocks[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_DELETED:
                    Legacy_ModuleInstallUtils::uninstallBlockByFuncNum($currentBlocks->mBlocks[$idx]->mFuncNum, $module, $log);
                    break;
            }
        }
    }
    
    public static function smartUpdateAllOfPreferences(&$module, &$log)
    {
        $dirname = $module->get('dirname');
        
        $fileReader =new Legacy_ModinfoX2FileReader($dirname);
        $latestPreferences =& $fileReader->loadPreferenceInformations();
        
        $dbReader =new Legacy_ModinfoX2DBReader($dirname);
        $currentPreferences =& $dbReader->loadPreferenceInformations();
        
        $currentPreferences->update($latestPreferences);

        //
        // Preferences
        //
        foreach (array_keys($currentPreferences->mPreferences) as $idx) {
            switch ($currentPreferences->mPreferences[$idx]->mStatus) {
                case LEGACY_INSTALLINFO_STATUS_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceByInfo($currentPreferences->mPreferences[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceOrderByInfo($currentPreferences->mPreferences[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_NEW:
                    Legacy_ModuleInstallUtils::installPreferenceByInfo($currentPreferences->mPreferences[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_DELETED:
                    Legacy_ModuleInstallUtils::uninstallPreferenceByOrder($currentPreferences->mPreferences[$idx]->mOrder, $module, $log);
                    break;
            }
        }
        
        //
        // Comments
        //
        foreach (array_keys($currentPreferences->mComments) as $idx) {
            switch ($currentPreferences->mComments[$idx]->mStatus) {
                case LEGACY_INSTALLINFO_STATUS_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceByInfo($currentPreferences->mComments[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceOrderByInfo($currentPreferences->mComments[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_NEW:
                    Legacy_ModuleInstallUtils::installPreferenceByInfo($currentPreferences->mComments[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_DELETED:
                    Legacy_ModuleInstallUtils::uninstallPreferenceByOrder($currentPreferences->mComments[$idx]->mOrder, $module, $log);
                    break;
            }
        }
        
        //
        // Notifications
        //
        foreach (array_keys($currentPreferences->mNotifications) as $idx) {
            switch ($currentPreferences->mNotifications[$idx]->mStatus) {
                case LEGACY_INSTALLINFO_STATUS_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceByInfo($currentPreferences->mNotifications[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_ORDER_UPDATED:
                    Legacy_ModuleInstallUtils::updatePreferenceOrderByInfo($currentPreferences->mNotifications[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_NEW:
                    Legacy_ModuleInstallUtils::installPreferenceByInfo($currentPreferences->mNotifications[$idx], $module, $log);
                    break;
                    
                case LEGACY_INSTALLINFO_STATUS_DELETED:
                    Legacy_ModuleInstallUtils::uninstallPreferenceByOrder($currentPreferences->mNotifications[$idx]->mOrder, $module, $log);
                    break;
            }
        }
    }
    
    public static function updateBlockTemplateByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_getmodulehandler('newblocks', 'legacy');
        
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $module->get('dirname')));
        $criteria->add(new Criteria('func_num', $info->mFuncNum));
        
        $blockArr =& $handler->getObjects($criteria);
        foreach (array_keys($blockArr) as $idx) {
            Legacy_ModuleInstallUtils::clearBlockTemplateForUpdate($blockArr[$idx], $module, $log);
            Legacy_ModuleInstallUtils::installBlockTemplate($blockArr[$idx], $module, $log);
        }
    }
    
    public static function updateBlockByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_getmodulehandler('newblocks', 'legacy');
        
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $module->get('dirname')));
        $criteria->add(new Criteria('func_num', $info->mFuncNum));
        
        $blockArr =& $handler->getObjects($criteria);
        foreach (array_keys($blockArr) as $idx) {
            $blockArr[$idx]->set('options', $info->mOptions);
            $blockArr[$idx]->set('name', $info->mName);
            $blockArr[$idx]->set('func_file', $info->mFuncFile);
            $blockArr[$idx]->set('show_func', $info->mShowFunc);
            $blockArr[$idx]->set('edit_func', $info->mEditFunc);
            $blockArr[$idx]->set('template', $info->mTemplate);
            
            if ($handler->insert($blockArr[$idx])) {
                $log->addReport(XCube_Utils::formatString('Update {0} block successfully.', $blockArr[$idx]->get('name')));
            } else {
                $log->addError(XCube_Utils::formatString('Could not update {0} block.', $blockArr[$idx]->get('name')));
            }
            
            Legacy_ModuleInstallUtils::clearBlockTemplateForUpdate($blockArr[$idx], $module, $log);
            Legacy_ModuleInstallUtils::installBlockTemplate($blockArr[$idx], $module, $log);
        }
    }
    
    public static function updatePreferenceByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_modid', $module->get('mid')));
        $criteria->add(new Criteria('conf_catid', 0));
        $criteria->add(new Criteria('conf_name', $info->mName));
        
        $configArr =& $handler->getConfigs($criteria);
        
        if (!(count($configArr) > 0 && is_object($configArr[0]))) {
            $log->addError('Execption Error: Could not find config.');
            return;
        }
        
        $config =& $configArr[0];
        
        $config->set('conf_title', $info->mTitle);
        $config->set('conf_desc', $info->mDescription);
        
        //
        // Decide whether it changes values.
        //
        $oldValueType = $config->get('conf_valuetype');
        if ($config->get('conf_formtype') != $info->mFormType && $oldValueType != $info->mValueType) {
            $config->set('conf_formtype', $info->mFormType);
            $config->set('conf_valuetype', $info->mValueType);
            $config->setConfValueForInput($info->mDefault);
        } else {
            $updateValue = null;
            if ($oldValueType != $info->mValueType) {
                if ($oldValueType === 'array' || $info->mValueType === 'array') {
                    $updateValue = $info->mDefault;
                } else {
                    $updateValue = $config->getConfValueForOutput();
                }
            }
            $config->set('conf_formtype', $info->mFormType);
            $config->set('conf_valuetype', $info->mValueType);
            if (!is_null($updateValue)) {
                $config->setConfValueForInput($updateValue);
            }
        }
        
        $config->set('conf_order', $info->mOrder);
        
        $optionArr =& $handler->getConfigOptions(new Criteria('conf_id', $config->get('conf_id')));
        if (is_array($optionArr)) {
            foreach (array_keys($optionArr) as $idx) {
                $handler->_oHandler->delete($optionArr[$idx]);
            }
        }
        
        if (count($info->mOption->mOptions) > 0) {
            foreach (array_keys($info->mOption->mOptions) as $idx) {
                $option =& $handler->createConfigOption();
                $option->set('confop_name', $info->mOption->mOptions[$idx]->mName);
                $option->set('confop_value', $info->mOption->mOptions[$idx]->mValue);
                $option->set('conf_id', $option->get('conf_id'));
                $config->setConfOptions($option);
                unset($option);
            }
        }

        if ($handler->insertConfig($config)) {
            $log->addReport(XCube_Utils::formatString("Preference '{0}' is updateded.", $config->get('conf_name')));
        } else {
            $log->addError(XCube_Utils::formatString("Could not update preference '{0}'.", $config->get('conf_name')));
        }
    }

    public static function updatePreferenceOrderByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_modid', $module->get('mid')));
        $criteria->add(new Criteria('conf_catid', 0));
        $criteria->add(new Criteria('conf_name', $info->mName));
        
        $configArr =& $handler->getConfigs($criteria);
        
        if (!(count($configArr) > 0 && is_object($configArr[0]))) {
            $log->addError('Execption Error: Could not find config.');
            return;
        }
        
        $config =& $configArr[0];
        
        $config->set('conf_order', $info->mOrder);

        if (!$handler->insertConfig($config)) {
            $log->addError(XCube_Utils::formatString("Could not update the order of preference '{0}'.", $config->get('conf_name')));
        }
    }
    
    public static function installBlockByInfo(&$info, &$module, &$log)
    {
        $handler =& xoops_gethandler('block');
        $block =& $handler->create();

        $block->set('mid', $module->get('mid'));
        $block->set('func_num', $info->mFuncNum);
        $block->set('options', $info->mOptions);
        $block->set('name', $info->mName);
        $block->set('title', $info->mName);
        $block->set('dirname', $module->get('dirname'));
        $block->set('func_file', $info->mFuncFile);
        $block->set('show_func', $info->mShowFunc);
        $block->set('edit_func', $info->mEditFunc);
        $block->set('template', $info->mTemplate);
        $block->set('block_type', 'M');
        $block->set('c_type', 1);

        if (!$handler->insert($block)) {
            $log->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_COULD_NOT_INSTALL_BLOCK, $block->get('name')));
            return false;
        } else {
            $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_BLOCK_INSTALLED, $block->get('name')));

            Legacy_ModuleInstallUtils::installBlockTemplate($block, $module, $log);

            return true;
        }
    }
    
    /**
     * @todo Need a message in the fail case.
     */
    public static function uninstallBlockByFuncNum($func_num, &$module, &$log)
    {
        $handler =& xoops_getmodulehandler('newblocks', 'legacy');
        
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('dirname', $module->get('dirname')));
        $criteria->add(new Criteria('func_num', $func_num));
        
        $blockArr =& $handler->getObjects($criteria);
        foreach (array_keys($blockArr) as $idx) {
            if ($handler->delete($blockArr[$idx])) {
                $log->addReport(XCube_Utils::formatString(_AD_LEGACY_MESSAGE_UNINSTALLATION_BLOCK_SUCCESSFUL, $blockArr[$idx]->get('name')));
            } else {
                // Uninstall fail
            }
            
            Legacy_ModuleInstallUtils::uninstallBlockTemplate($blockArr[$idx], $module, $log);
        }
    }
    
    /**
     * @private
     * Uninstalls the block template data specified by $block of $module.
     * @param XoopsBlock  $block
     * @param XoopsModule $module This object is must the module which has $block.
     * @param string      $tplset A name of the template set. If this is null, uninstalls
     *                            all templates of any template-sets. 
     * @param $log
     * @remarks
     *     This method users template handlers of the kernel. But, if they are hooked,
     *     they may not do something. So, abstraction mechanism is possible enough.
     */
    public static function _uninstallBlockTemplate(&$block, &$module, $tplset, &$log)
    {
        $handler =& xoops_gethandler('tplfile');
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('tpl_refid', $block->get('bid')));
        $criteria->add(new Criteria('tpl_file', $block->get('template')));
        $criteria->add(new Criteria('tpl_module', $module->get('dirname')));
        $criteria->add(new Criteria('tpl_type', 'block'));
        
        if ($tplset != null) {
            // See 'FIXME'
            $criteria->add(new Criteria('tpl_tplset', $tplset));
        }
        
        $handler->deleteAll($criteria);
    }
    
    public static function uninstallBlockTemplate(&$block, &$module, &$log)
    {
        Legacy_ModuleInstallUtils::_uninstallBlockTemplate($block, $module, null, $log);
    }
    
    /**
     * @public
     * Removes a template data from only default group of some render-system.
     */
    public static function clearBlockTemplateForUpdate(&$block, &$module, &$log)
    {
        Legacy_ModuleInstallUtils::_uninstallBlockTemplate($block, $module, 'default', $log);
    }

    public static function uninstallPreferenceByOrder($order, &$module, &$log)
    {
        $handler =& xoops_gethandler('config');

        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('conf_modid', $module->get('mid')));
        $criteria->add(new Criteria('conf_catid', 0));
        $criteria->add(new Criteria('conf_order', $order));
        
        $configArr =& $handler->getConfigs($criteria);
        
        foreach (array_keys($configArr) as $idx) {
            if ($handler->deleteConfig($configArr[$idx])) {
                $log->addReport(XCube_Utils::formatString("Delete preference '{0}'.", $configArr[$idx]->get('conf_name')));
            } else {
                $log->addError(XCube_Utils::formatString("Could not delete preference '{0}'.", $configArr[$idx]->get('conf_name')));
            }
        }
    }
    
    /**
     * Executes SQL query as cube style.
     */
    public static function DBquery($query, &$module, $log)
    {
        require_once XOOPS_MODULE_PATH . "/legacy/admin/class/Legacy_SQLScanner.class.php";
        
        $successFlag = true;
        
        $scanner =new Legacy_SQLScanner();
        $scanner->setDB_PREFIX(XOOPS_DB_PREFIX);
        $scanner->setDirname($module->get('dirname'));
        $scanner->setBuffer($query);
        $scanner->parse();
        $sqlArr = $scanner->getSQL();

        $root =& XCube_Root::getSingleton();
        
        foreach ($sqlArr as $sql) {
            if ($root->mController->mDB->query($sql)) {
                $log->addReport("Success: ${sql}");
                $successFlag &= true;
            } else {
                $log->addError("Failure: ${sql}");
                $successFlag = false;
            }
        }
        
        return $successFlag;
    }
    
    public static function deleteAllOfNotifications(&$module, &$log)
    {
        $handler =& xoops_gethandler('notification');
        $criteria =new Criteria('not_modid', $module->get('mid'));
        $handler->deleteAll($criteria);
    }

    public static function deleteAllOfComments(&$module, &$log)
    {
        $handler =& xoops_gethandler('comment');
        $criteria =new Criteria('com_modid', $module->get('mid'));
        $handler->deleteAll($criteria);
    }
}
