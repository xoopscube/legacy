<?php
/**
 * Protector Permissions Admin
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

require_once XOOPS_TRUST_PATH . '/libs/altsys/include/gtickets.php';

// Check admin permission
if (!is_object($xoopsUser) || !$xoopsUser->isAdmin()) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// Get module info
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
$mid = $module->getVar('mid');

// Display header
xoops_cp_header();

// Get group handler
$group_handler = xoops_getHandler('group');
$groups = $group_handler->getObjects();

// Get permission handler
$gperm_handler = xoops_getHandler('groupperm');

// Process form submission
if (isset($_POST['submit']) && isset($_POST['perm_name'])) {
    $perm_name = $_POST['perm_name'];
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    
    // Delete existing permissions for this item
    $criteria = new CriteriaCompo();
    $criteria->add(new Criteria('gperm_modid', $mid));
    $criteria->add(new Criteria('gperm_name', $perm_name));
    $criteria->add(new Criteria('gperm_itemid', $item_id));
    $gperm_handler->deleteAll($criteria);
    
    // Add new permissions - ensure we have an array of groups
    if (isset($_POST['groups']) && is_array($_POST['groups'])) {
        foreach ($_POST['groups'] as $group_id) {
            // Convert to integer to ensure valid group ID
            $group_id = intval($group_id);
            if ($group_id > 0) {
                $gperm_handler->addRight($perm_name, $item_id, $group_id, $mid);
            }
        }
    }
    
    // Always give admin group access to module_admin
    if ($perm_name === 'module_admin') {
        $gperm_handler->addRight($perm_name, $item_id, XOOPS_GROUP_ADMIN, $mid);
    }
    
    // Debug information
    // echo '<div class="success">Permissions updated for ' . $perm_name . '</div>';
    
    // Redirect after successful update
    redirect_header('index.php?page=permissions', 3, 'Permissions updated successfully');
    exit();
}

// Note that 'redirect_header' must be placed before any html layout element (e.g. mymnenu.php) 
// Display admin menu
include __DIR__ . '/mymenu.php';

// Display custom permission forms
echo '<div class="ui-card" data-layout="column" data-self="size-x1">';
echo '<h2>Permissions Management</h2>';

// Module Admin permissions
echo '<div class="ui-card" data-layout="column" data-self="size-x1">';
echo '<h3>Module Administration</h3>';
echo '<form method="post">';
echo '<input type="hidden" name="perm_name" value="module_admin">';
echo '<input type="hidden" name="item_id" value="0">';
echo '<table class="outer" width="100%">';
echo '<thead><tr><th>Group</th><th>Permission</th></tr></thead>';
echo '<tbody>';

// Get existing permissions
$admin_perms = $gperm_handler->getGroupIds('module_admin', 0, $mid);

foreach ($groups as $group) {
    $group_id = $group->getVar('groupid');
    $checked = in_array($group_id, $admin_perms) ? ' checked="checked"' : '';
    // Always check admin group and disable checkbox
    $disabled = ($group_id == XOOPS_GROUP_ADMIN) ? ' disabled="disabled"' : '';
    if ($group_id == XOOPS_GROUP_ADMIN) {
        $checked = ' checked="checked"';
    }
    
    echo '<tr class="' . ($group_id % 2 ? 'even' : 'odd') . '">';
    echo '<td>' . $group->getVar('name') . '</td>';
    echo '<td align="center"><input type="checkbox" name="groups[]" value="' . $group_id . '"' . $checked . $disabled . '></td>';
    echo '</tr>';
    
    // Add hidden field for admin group to ensure it's always submitted
    if ($group_id == XOOPS_GROUP_ADMIN) {
        echo '<input type="hidden" name="groups[]" value="' . XOOPS_GROUP_ADMIN . '">';
    }
}

echo '</tbody>';
echo '<tfoot><tr><td colspan="2"><input type="submit" name="submit" value="Update Module Admin Permissions"></td></tr></tfoot>';
echo '</table></form>';
echo '</div>';

// Proxy Use permissions
echo '<div class="ui-card" data-layout="column" data-self="size-x1">';
echo '<h3>Proxy Usage</h3>';
echo '<form method="post">';
echo '<input type="hidden" name="perm_name" value="proxy_use">';
echo '<input type="hidden" name="item_id" value="0">';
echo '<table class="outer" width="100%">';
echo '<thead><tr><th>Group</th><th>Permission</th></tr></thead>';
echo '<tbody>';

// Get existing permissions
$proxy_perms = $gperm_handler->getGroupIds('proxy_use', 0, $mid);

foreach ($groups as $group) {
    $group_id = $group->getVar('groupid');
    $checked = in_array($group_id, $proxy_perms) ? ' checked="checked"' : '';
    
    echo '<tr class="' . ($group_id % 2 ? 'even' : 'odd') . '">';
    echo '<td>' . $group->getVar('name') . '</td>';
    echo '<td align="center"><input type="checkbox" name="groups[]" value="' . $group_id . '"' . $checked . '></td>';
    echo '</tr>';
}

echo '</tbody>';
echo '<tfoot><tr><td colspan="2"><input type="submit" name="submit" value="Update Proxy Use Permissions"></td></tr></tbody>';
echo '</table></form>';
echo '</div>';

echo '</div>'; // Close main ui-card

// Display footer
xoops_cp_footer();