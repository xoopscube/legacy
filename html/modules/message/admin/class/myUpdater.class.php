<?php
/**
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 3
 * @author Marijuana
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH.'/modules/legacy/admin/class/ModuleUpdater.class.php';

class Message_myUpdater extends Legacy_ModulePhasedUpgrader
{
    public function Message_myUpdater()
    {
        self::__construct();
    }

    public function __construct()
    {
        parent::__construct();
        $this->_mMilestone = array(
            '041' => 'update041',
            '060' => 'update060',
            '070' => 'update070'
        );
    }
  
    public function updatemain()
    {
        Legacy_ModuleInstallUtils::clearAllOfModuleTemplatesForUpdate($this->_mTargetXoopsModule, $this->mLog);
        Legacy_ModuleInstallUtils::installAllOfModuleTemplates($this->_mTargetXoopsModule, $this->mLog);
    
        $this->saveXoopsModule($this->_mTargetXoopsModule);
        $this->mLog->add('Version'.($this->_mTargetVersion / 100).' for update.');
        $this->_mCurrentVersion = $this->_mTargetVersion;
    }
  
    public function update070()
    {
        $this->mLog->addReport(_AD_LEGACY_MESSAGE_UPDATE_STARTED);
        $root = XCube_Root::getSingleton();
        $db = $root->mController->getDB();
    
        $sql = "ALTER TABLE `".$db->prefix('message_inbox')."` ";
        $sql.= "ADD `uname` varchar(100) NOT NULL default ''";
        if (!$db->query($sql)) {
            $this->mLog->addReport($db->error());
        }
    
        $this->updatemain();
        return true;
    }
  
    public function update060()
    {
        $this->mLog->addReport(_AD_LEGACY_MESSAGE_UPDATE_STARTED);
        $root = XCube_Root::getSingleton();
        $db = $root->mController->getDB();
    
        $sql = "ALTER TABLE `".$db->prefix('message_users')."` ";
        $sql.= "ADD `viewmsm` int( 1 ) UNSIGNED NOT NULL DEFAULT '0', ";
        $sql.= "ADD `pagenum` int( 2 ) UNSIGNED NOT NULL DEFAULT '0', ";
        $sql.= "ADD `blacklist` VARCHAR( 255 ) NOT NULL DEFAULT ''";
        if (!$db->query($sql)) {
            $this->mLog->addReport($db->error());
        }
    
        $this->updatemain();
        return true;
    }
  
    public function update041()
    {
        $this->mLog->addReport(_AD_LEGACY_MESSAGE_UPDATE_STARTED);
    
    //Add Table
    $sqlfileInfo = $this->_mTargetXoopsModule->getInfo('sqlfile');
        $dirname = $this->_mTargetXoopsModule->getVar('dirname');
        $sqlfile = $sqlfileInfo[XOOPS_DB_TYPE];
        $sqlfilepath = XOOPS_MODULE_PATH.'/'.$dirname.'/'.$sqlfile;
        require_once XOOPS_MODULE_PATH.'/legacy/admin/class/Legacy_SQLScanner.class.php';
        $scanner = new Legacy_SQLScanner();
        $scanner->setDB_PREFIX(XOOPS_DB_PREFIX);
        $scanner->setDirname($this->_mTargetXoopsModule->get('dirname'));
        if (!$scanner->loadFile($sqlfilepath)) {
            $this->mLog->addError(XCube_Utils::formatString(_AD_LEGACY_ERROR_SQL_FILE_NOT_FOUND, $sqlfile));
            return false;
        }
  
        $scanner->parse();
        $sqls = $scanner->getSQL();
        $root = XCube_Root::getSingleton();
        $db = $root->mController->getDB();
  
        foreach ($sqls as $sql) {
            if (strpos($sql, '_message_users') !== false) {
                if (!$db->query($sql)) {
                    $this->mLog->addError($db->error());
                    return false;
                }
            }
        }
        $this->mLog->addReport(_AD_LEGACY_MESSAGE_DATABASE_SETUP_FINISHED);
    //add table

    $this->updatemain();
        return true;
    }
}
