<?php

/**
 * Protector Module Class
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

class Protector_Module extends Legacy_ModuleAdapter
{
    /**
     * Module directory name
     * @var string
     */
    protected $mDirname;
    
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
        $adminmenu = [];
        include XOOPS_TRUST_PATH . '/modules/' . $this->mDirname . '/admin_menu.php';
        return $adminmenu;
    }
    
    /**
     * Module installation
     * 
     * @param XoopsModule|null $module The module to install
     * @param bool $force Force installation
     * @return bool Success or failure
     */
    public function installModule(?XoopsModule $module = null, bool $force = false): bool
    {
        // Get the module object from parent if not provided
        if ($module === null) {
            // Use the module object from the mXoopsModule property
            $module = $this->mXoopsModule;
        }
        
        // Create module tables
        $this->createTables();
        
        // Set default configs
        $this->setDefaultConfigs();
        
        return true;
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
        
        // Check if module was found
        if (!$module) {
            // Log error or throw exception
            trigger_error("Could not find module with dirname: {$this->mDirname}");
            return;
        }
        
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
            'enable_only_admin_message' => 'Site maintenance in progress. Please come back later.',
            // CSP related configs
            'enable_csp' => 0,
            'csp_report_only' => 1,
            'csp_default_src' => "'self'",
            'csp_script_src' => "'self' 'unsafe-inline' 'unsafe-eval'",
            'csp_style_src' => "'self' 'unsafe-inline'",
            'csp_img_src' => "'self' data:",
            'csp_connect_src' => "'self'",
            'csp_font_src' => "'self'",
            'csp_media_src' => "'self'",
            'csp_frame_src' => "'self'",
            'csp_report_uri' => 'modules/protector/csp-report.php',
            'csp_log_max_entries' => 1000
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