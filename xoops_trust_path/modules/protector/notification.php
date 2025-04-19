<?php
/**
 * Protector notification functions
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

// This function is called by the notification system to get information about an item
function protector_notify_iteminfo($category, $item_id)
{
    global $xoopsDB;
    
    if ($category == 'global') {
        $item['name'] = '';
        $item['url'] = XOOPS_URL . '/modules/protector/admin/index.php';
        return $item;
    }
    
    return null;
}

// Function to trigger notification events
function protector_trigger_event($category, $item_id, $event, $extra_tags = [], $user_list = null, $omit_user_id = null)
{
    global $xoopsUser;
    
    $module_handler = xoops_gethandler('module');
    $module = $module_handler->getByDirname('protector');
    
    if (!is_object($module)) {
        return false;
    }
    
    $notification_handler = xoops_gethandler('notification');
    
    // Default tags
    $tags = [
        'MODULE_NAME' => $module->getVar('name'),
        'MODULE_URL' => XOOPS_URL . '/modules/protector/',
        'MODULE_ADMIN_URL' => XOOPS_URL . '/modules/protector/admin/index.php'
    ];
    
    // Add extra tags
    if (is_array($extra_tags) && count($extra_tags) > 0) {
        $tags = array_merge($tags, $extra_tags);
    }
    
    // Trigger the event
    $notification_handler->triggerEvent($category, $item_id, $event, $tags, $user_list, $omit_user_id);
    
    return true;
}