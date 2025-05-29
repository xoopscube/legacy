<?php
/**
 * Bannerstats - Module for XCL
 * BannerstatsUpdater.class.php
 *
 * Custom updater class for the Bannerstats module.
 * Handles database schema changes and data migrations during updates.
 * Uses phased updates.
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

require_once XOOPS_ROOT_PATH . '/modules/legacy/admin/class/ModuleUpdater.class.php';

class Bannerstats_Updater extends Legacy_ModulePhasedUpgrader
{

    public function __construct()
    {
        parent::__construct();
        // Define update methods for each version milestone
        // The key is the version number * 100 (e.g., 1.1 is 110)
        $this->_mMilestone = [
            // '110' => 'update110', // Example for version 1.1
            // '120' => 'update120', // Example for version 1.2
        ];
    }

    /**
     * Example update method for version 1.1 (milestone 110).
     * Rename this method and add logic for actual updates.
     *
     * @return bool True on success, false on failure.
     */
    public function update110()
    {
        $this->mLog->addReport(sprintf("Applying updates for version 1.1 for %s...", $this->_mTargetXoopsModule->get('dirname')));

        // Get the database object (available via parent class)
        $db = $this->db;
        $modDirname = $this->_mTargetXoopsModule->get('dirname');
        $bannerTable = $db->prefix($modDirname . '_banner');

        // Example: Add a new column 'priority' if it doesn't exist
        $sql = sprintf("ALTER TABLE %s ADD COLUMN `priority` INT(3) NOT NULL DEFAULT 0 AFTER `weight`", $bannerTable);
        if (!$db->query($sql)) {
            // Check if the error is "column already exists" - if so, it's not a fatal error
            if (strpos(strtolower($db->error()), 'duplicate column name') === false && strpos(strtolower($db->error()), 'already exists') === false) {
                 $this->mLog->addError(sprintf("Error adding 'priority' column to %s: %s", $bannerTable, $db->error()));
                 return false; // Halt on other errors
            }
            $this->mLog->addReport("Note: 'priority' column might have already existed.");
        } else {
            $this->mLog->addReport("'priority' column added successfully to " . $bannerTable);
        }

        // Add other update steps for version 1.1 here (e.g., data migrations)

        // After applying updates for this milestone, call updatemain()
        // updatemain() handles updating the module version in the database
        $this->updatemain();

        return true; // Indicate successful update for this milestone
    }

    /**
     * This method is called after each milestone update.
     * It handles updating the module version in the database and other common tasks.
     * We can override this if needed, but the parent implementation is usually sufficient.
     */
    public function updatemain()
    {
        parent::updatemain();
        // We can add logic here that runs after *each* milestone update
        // Example: Re-installing templates or blocks if they changed significantly
        // Legacy_ModuleInstallUtils::clearAllOfModuleTemplatesForUpdate($this->_mTargetXoopsModule, $this->mLog);
        // Legacy_ModuleInstallUtils::installAllOfModuleTemplates($this->_mTargetXoopsModule, $this->mLog);
    }

    // Implement other update methods (update120, update130, etc.) as needed for future versions
}
