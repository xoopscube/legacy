<?php
/**
 * Permission Check Helper
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

/**
 * Check if the current user has permission to access a specific feature
 *
 * @param string $permission_name Name of the permission to check ('module_admin' or 'proxy_use')
 * @param int $item_id Item ID (usually 0 for module-wide permissions)
 * @param string $dirname Module directory name
 * @return bool True if user has permission, false otherwise
 */
function protector_check_permission($permission_name, $item_id = 0, $dirname = 'protector') {
    global $xoopsUser;
    
    // Get module information
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname($dirname);
    
    if (!is_object($module)) {
        return false;
    }
    
    // Admin users always have permission
    if (is_object($xoopsUser) && $xoopsUser->isAdmin($module->getVar('mid'))) {
        return true;
    }
    
    // Get user groups - handle anonymous users
    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];
    
    // Check permission using standard XOOPS groupperm
    $gperm_handler = xoops_getHandler('groupperm');
    return $gperm_handler->checkRight($permission_name, $item_id, $groups, $module->getVar('mid'));
}