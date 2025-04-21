<?php
/**
 * Notification functions for Protector module
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include required notification functions
require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

/**
 * Send security threat notification to subscribed users
 * 
 * @param string $threat_type Type of security threat (SQL Injection, DoS, etc.)
 * @param string $threat_desc Detailed description of the threat
 * @param string $ip IP address of the potential attacker
 * @param string $agent User agent of the potential attacker
 * @return bool Success status
 */
function protector_send_security_notification($threat_type, $threat_desc, $ip = '', $agent = '') {
    // Get module info
    $module_handler = xoops_gethandler('module');
    $protector_module = $module_handler->getByDirname('protector');
    
    if (!is_object($protector_module)) {
        return false;
    }
    
    $module_id = $protector_module->getVar('mid');
    
    // Use current IP if not provided
    if (empty($ip)) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Use current user agent if not provided
    if (empty($agent)) {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    // Prepare notification tags
    $tags = [
        'MODULE_NAME' => $protector_module->getVar('name'),
        'SITE_NAME' => $GLOBALS['xoopsConfig']['sitename'],
        'SITE_URL' => XOOPS_URL,
        'ADMIN_URL' => XOOPS_URL . '/modules/protector/admin/index.php',
        'THREAT_TYPE' => $threat_type,
        'THREAT_IP' => $ip,
        'THREAT_DATE' => date('Y-m-d H:i:s'),
        'THREAT_DESC' => $threat_desc,
        'THREAT_AGENT' => $agent
    ];
    
    // Get database connection
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    // Get notification handler
    $notification_handler = xoops_gethandler('notification');
    
    try {
        // Get all users subscribed to this event
        $sql = "SELECT not_uid FROM " . $db->prefix('xoopsnotifications') . " 
                WHERE not_modid = " . $module_id . " 
                AND not_category = 'global' 
                AND not_event = 'security_threat'";
        $result = $db->query($sql);
        
        if (!$result) {
            return false;
        }
        
        $subscribers = [];
        while ($row = $db->fetchArray($result)) {
            $subscribers[] = $row['not_uid'];
        }
        
        // If no subscribers, don't try to send notifications
        if (empty($subscribers)) {
            return false;
        }
        
        // Get current timestamp
        $now = time();
        
        // Create a notification message
        $subject = "[{$GLOBALS['xoopsConfig']['sitename']}] Security Alert: {$threat_type}";
        $message = "A security threat has been detected by the Protector module.\n\n";
        $message .= "Threat Type: {$threat_type}\n";
        $message .= "IP Address: {$ip}\n";
        $message .= "Date/Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "User Agent: {$agent}\n";
        $message .= "Description: {$threat_desc}\n\n";
        $message .= "You can manage your notifications at: " . XOOPS_URL . "/notifications.php";
        
        // FIX: Skip the standard notification trigger which is causing the error
        // $notification_handler->triggerEvent('global', 0, 'security_threat', $tags);

        // Send direct message
        foreach ($subscribers as $uid) {
            // Check if the message_users table has an entry for this user
            $check_sql = "SELECT * FROM " . $db->prefix('message_users') . " WHERE uid = " . $uid;
            $check_result = $db->query($check_sql);
            
            if (!$check_result || $db->getRowsNum($check_result) == 0) {
                // Create a user entry if it doesn't exist - using correct columns from schema
                $create_user_sql = "INSERT INTO " . $db->prefix('message_users') . " 
                                  (uid, usepm, tomail, viewmsm, pagenum, blacklist) 
                                  VALUES 
                                  (" . $uid . ", 1, 0, 1, 10, '')";
                $db->queryF($create_user_sql);
            }
            
            // Insert into inbox with the correct column name
            $sql = "INSERT INTO " . $db->prefix('message_inbox') . " 
                   (uid, from_uid, title, message, utime, is_read, uname) 
                   VALUES 
                   (" . $uid . ", 1, '" . addslashes($subject) . "', '" . addslashes($message) . "', " . $now . ", 0, 'System')";
            
            $db->queryF($sql);
        }
        
        return true;
    } catch (Exception $e) {
        // Log the error for debugging
        error_log('Protector notification error: ' . $e->getMessage());
        return false;
    }
}