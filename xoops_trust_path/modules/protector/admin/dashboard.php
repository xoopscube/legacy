<?php
/**
 * Protector Admin Dashboard
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

// Get protector instance
$protector = protector::getInstance();

// Display dashboard content
echo '<h3>' . _MI_PROTECTOR_NAME . '</h3>';

// Display module information
echo '<div class="protector-dashboard">';
echo '<h4>' . _MI_PROTECTOR_DESC . '</h4>';
echo '<ul>';
// Use module version from xoopsModule instead of non-existent getVersion method
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
echo '<li>Version: ' . $module->getVar('version') . '</li>';
// Check if module is active instead of using non-existent isEnabled method
echo '<li>Status: ' . ($module->getVar('isactive') ? 'Enabled' : 'Disabled') . '</li>';
echo '</ul>';

// Display quick links
echo '<h4>Admin Menu</h4>';
echo '<ul>';
echo '<li><a href="index.php?page=log">' . _MI_PROTECTOR_LOGLIST . '</a></li>';
echo '<li><a href="index.php?page=ban">' . _MI_PROTECTOR_IPBAN . '</a></li>';
echo '<li><a href="index.php?page=safe_list">IP Safe List</a></li>';
echo '<li><a href="index.php?page=prefix_manager">' . _MI_PROTECTOR_PREFIXMANAGER . '</a></li>';
echo '<li><a href="index.php?page=advisory">' . _MI_PROTECTOR_ADVISORY . '</a></li>';
echo '</ul>';
echo '</div>';

// Include footer
xoops_cp_footer();