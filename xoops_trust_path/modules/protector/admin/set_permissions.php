<?php
/**
 * Set Permissions for Protector
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// require_once '../../../mainfile.php';
// require_once XOOPS_ROOT_PATH . '/include/cp_header.php';

// Include header
xoops_cp_header();

// Check admin permission
if (!is_object($xoopsUser) || !$xoopsUser->isAdmin()) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// Get module info
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
$mid = $module->getVar('mid');

// Get permission handler
$gperm_handler = xoops_getHandler('groupperm');

// Set permissions for admin group
$gperm_handler->addRight('module_admin', 0, XOOPS_GROUP_ADMIN, $mid);
$gperm_handler->addRight('proxy_use', 0, XOOPS_GROUP_ADMIN, $mid);

// Set permissions for registered users group
$gperm_handler->addRight('proxy_use', 0, XOOPS_GROUP_USERS, $mid);

// Set permissions for webmasters group if it exists
$group_handler = xoops_getHandler('group');
$webmaster_group = $group_handler->getByName('Webmasters');
if (is_object($webmaster_group)) {
    $gperm_handler->addRight('module_admin', 0, $webmaster_group->getVar('groupid'), $mid);
    $gperm_handler->addRight('proxy_use', 0, $webmaster_group->getVar('groupid'), $mid);
}

// Display success message
xoops_cp_header();
echo '<h2>Permissions Set Successfully</h2>';
echo '<p>The following permissions have been set:</p>';
echo '<ul>';
echo '<li>Admin group: module_admin, proxy_use</li>';
echo '<li>Registered users group: proxy_use</li>';
if (is_object($webmaster_group)) {
    echo '<li>Webmasters group: module_admin, proxy_use</li>';
}
echo '</ul>';
echo '<p><a href="index.php?page=permissions">Return to Permissions Management</a></p>';
xoops_cp_footer();