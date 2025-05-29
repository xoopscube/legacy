<?php
/**
 * Bannerstats - Module for XCL
 * BannerstatsUninstaller.class.php
 *
 * Custom uninstaller class for the Bannerstats module.
 * Handles dropping database tables and other cleanup.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/legacy/admin/class/ModuleUninstaller.class.php';

class Bannerstats_Uninstaller extends Legacy_ModuleUninstaller
{

    public function __construct()
    {
        parent::__construct();
        // set specific options here if needed
        // $this->setForce(true); // Example: Force dropping tables
    }

    /**
     * Executes the uninstallation process.
     * This method is called by the core uninstaller.
     *
     * @return bool True on success, false on failure.
     */
    public function executeUninstall()
    {
        $this->mLog->addReport(sprintf("Executing custom uninstaller script for %s...", $this->_mTargetXoopsModule->get('dirname')));

        // Get the database object (available via parent class)
        $db = $this->db;

        $modDirname = $this->_mTargetXoopsModule->get('dirname');

        // Define the tables to be dropped
        $tablesToDrop = [
            $modDirname . '_banner',
            $modDirname . '_bannerclient',
            $modDirname . '_bannerfinish',
            // Add any other tables your module created
            // $modDirname . '_bannerstats_impressions_log',
            // $modDirname . '_bannerstats_clicks_log',
        ];

        foreach ($tablesToDrop as $tableNameWithoutPrefix) {
            $prefixedTableName = $db->prefix($tableNameWithoutPrefix);
            $sql = sprintf("DROP TABLE IF EXISTS %s", $prefixedTableName);

            if (!$db->query($sql)) {
                $this->mLog->addError(sprintf("Failed to drop table %s: %s", $prefixedTableName, $db->error()));
                // Decide if this is a fatal error. Returning false halts uninstallation.
                // return false;
            } else {
                $this->mLog->addReport(sprintf("Table %s dropped successfully.", $prefixedTableName));
            }
        }

        // Add other cleanup tasks here (e.g., removing module-specific files from uploads)

        $this->mLog->addReport(sprintf("Custom uninstaller script for %s completed.", $modDirname));

        return true; // Indicate successful execution
    }

    // We can override other methods from Legacy_ModuleUninstaller if needed
    // For example, executeUninstallTemplates() if the module needs custom template handling.
}
