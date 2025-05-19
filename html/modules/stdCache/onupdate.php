<?php
/**
 * stdCache Module onUpdate Script
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

/**
 * Performs tasks upon module update
 *
 * @param XoopsModule $module Reference to the module object
 * @param int $previous_version The version number of the module
 * @return bool True on success, false on failure
 */
function xoops_module_update_stdCache(XoopsModule $module, $previous_version) {
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $mid = $module->getVar('mid');

    // Remove Obsolete Configuration Items
    $obsolete_configs = [
        'notification_limit',
        'cache_limit_notification_mail',
        'admin_notification_group',
    ];

    $config_handler = xoops_gethandler('config');
    $success = true;

    if (is_object($config_handler)) {
        foreach ($obsolete_configs as $config_name) {
            $criteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
            $criteria->add(new Criteria('conf_name', $config_name));
            
            // Use getConfigs() instead of getObjects()
            $configObjects = $config_handler->getConfigs($criteria); 

            if (is_array($configObjects) && count($configObjects) > 0) { // getConfigs returns an array
                foreach ($configObjects as $configObj) {
                    if (is_object($configObj) && !$config_handler->deleteConfig($configObj)) {
                        $module->setErrors(sprintf("Failed to delete obsolete config item: %s", $config_name));
                        $success = false; // Mark failure but continue trying to remove others
                    } else if (!is_object($configObj)) {
                        $module->setErrors(sprintf("Invalid object found for obsolete config item: %s", $config_name));
                        $success = false;
                    }
                }
            }
        }
    } else {
        $module->setErrors("Failed to get config_handler during module update.");
        $success = false;
    }


    // Template Cache Clearing
    if (class_exists('XoopsTpl')) {
        $xoopsTpl = new XoopsTpl();
        if (method_exists($xoopsTpl, 'clear_cache') && method_exists($xoopsTpl, 'clear_compiled_tpl')) {
            $xoopsTpl->clear_cache(null, 'mod_' . $module->getVar('dirname'));
            $xoopsTpl->clear_compiled_tpl(null, 'mod_' . $module->getVar('dirname'));
        } else {
            $module->setErrors("XoopsTpl methods for cache clearing not available.");
            // Not necessarily a fatal error for the update itself
        }
    } else {
        $module->setErrors("XoopsTpl class not available for cache clearing.");
    }

    return $success;
}
