<?php

/**
 * Protector Module Class
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class Protector_Module extends Legacy_ModuleAdapter
{
    /**
     * Constructor
     */
    public function __construct(XoopsModule $module)
    {
        parent::__construct($module);
        $this->mDirname = $module->getVar('dirname');
    }
    
    /**
     * Get admin menu
     */
    public function getAdminMenu(): array
    {
        // Include the admin_menu.php file which defines the menu
        $mydirname = $this->mDirname;
        $adminmenu = [];
        include XOOPS_TRUST_PATH . '/modules/' . $this->mDirname . '/admin_menu.php';
        return $adminmenu;
    }
    
    /**
     * Module installation
     */
    public function install(): bool
    {
        $ret = parent::install();
        
        // Create module tables
        $this->createTables();
        
        // Set default configs
        $this->setDefaultConfigs();
        
        return $ret;
    }
    
    /**
     * Create database tables
     */
    private function createTables(): void
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        
        // Create log table
        $db->query("
            CREATE TABLE IF NOT EXISTS " . $db->prefix($this->mDirname . '_log') . " (
                lid int(10) unsigned NOT NULL auto_increment,
                timestamp int(10) unsigned NOT NULL default 0,
                type varchar(32) NOT NULL default '',
                ip int(10) unsigned NOT NULL default 0,
                agent varchar(256) NOT NULL default '',
                uri varchar(256) NOT NULL default '',
                data text,
                PRIMARY KEY (lid),
                KEY (timestamp),
                KEY (ip)
            ) ENGINE=InnoDB
        ");
        
        // Create access table
        $db->query("
            CREATE TABLE IF NOT EXISTS " . $db->prefix($this->mDirname . '_access') . " (
                ip varchar(39) NOT NULL default '',
                request_uri varchar(192) NOT NULL default '',
                expire int(10) unsigned NOT NULL default 0,
                KEY (ip, request_uri),
                KEY (expire)
            ) ENGINE=InnoDB
        ");
    }
    
    /**
     * Set default configurations
     */
    private function setDefaultConfigs(): void
    {
        $config_handler = xoops_getHandler('config');
        $module_handler = xoops_getHandler('module');
        $module = $module_handler->getByDirname($this->mDirname);
        
        // Default configs
        $configs = [
            'global_disabled' => 0,
            'log_level' => 255,
            'banip_time0' => 86400,
            'banip_time1' => 604800,
            'reliable_ips' => '127.0.0.1',
            'dos_expire' => 60,
            'dos_f5count' => 10,
            'dos_f5action' => 'exit',
            'dos_crcount' => 30,
            'dos_craction' => 'exit',
            'dos_crsafe' => 0,
            'bwlimit_count' => 0,
            'stop_crackers' => 1,
            'disable_features' => 1,
            'enable_bigumbrella' => 1,
            'spamcount_uri' => 10,
            'spamcount_user' => 0,
            'languages' => '',
            'enable_only_admin' => 1,
            'enable_only_admin_groups' => '1',
            'enable_only_admin_page' => 'index.php,admin.php',
            'enable_only_admin_hosts' => '127.0.0.1',
            'enable_only_admin_message' => 'Site maintenance in progress. Please come back later.'
        ];
        
        // Create config objects and save
        foreach ($configs as $name => $value) {
            $config = $config_handler->createConfig();
            $config->setVar('conf_modid', $module->getVar('mid'));
            $config->setVar('conf_catid', 0);
            $config->setVar('conf_name', $name);
            $config->setVar('conf_value', $value);
            $config_handler->insertConfig($config);
        }
    }
}