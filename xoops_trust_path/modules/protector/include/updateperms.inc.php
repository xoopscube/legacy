<?php
/**
 * Update Permissions for Protector
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

/**
 * Update module permissions
 * 
 * @param string $dirname Module directory name
 * @return bool Success
 */
function protector_update_permissions($dirname = 'protector') {
    // Get module ID
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname($dirname);
    
    if (!is_object($module)) {
        return false;
    }
    
    $mid = $module->getVar('mid');
    
    // Delete existing permissions
    $gperm_handler = xoops_getHandler('groupperm');
    $gperm_handler->deleteByModule($mid);
    
    // Add module admin permission for admin group
    $gperm_handler->addRight('module_admin', 0, XOOPS_GROUP_ADMIN, $mid);
    
    // Add proxy use permission for admin group by default
    $gperm_handler->addRight('proxy_use', 0, XOOPS_GROUP_ADMIN, $mid);
    
    // Also add proxy use permission for registered users (group 2) if it exists
    // we get all groups and find the one named "Webmasters"
    $group_handler = xoops_getHandler('group');
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('name', 'Webmasters'));
    $groups = $group_handler->getObjects($criteria);
    
    if (!empty($groups)) {
        foreach ($groups as $group) {
            $gperm_handler->addRight('proxy_use', 0, $group->getVar('groupid'), $mid);
        }
    }
    
    // Add proxy use permission for registered users (group 2)
    $gperm_handler->addRight('proxy_use', 0, XOOPS_GROUP_USERS, $mid);
    
    return true;
}