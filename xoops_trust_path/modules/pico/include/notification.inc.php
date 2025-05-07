<?php
/**
 * Pico content management D3 module for XCL
 * Custom notification handler for Pico 
 * 
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster, XCL/PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */
require_once XOOPS_ROOT_PATH . '/mainfile.php';
// Include required XoopsCube notification functions
require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';

function pico_notify_webmasters($mydirname, $subject, $message, $tags = array()) {
    global $xoopsConfig;
    
    // Get module ID
    $module_handler = xoops_gethandler('module');
    $module = $module_handler->getByDirname($mydirname);
    if (!is_object($module)) {
        return false;
    }
    $mid = $module->getVar('mid');
    
    // Get webmaster group IDs (typically group 1 is XOOPS webmasters)
    $webmaster_groups = array(XOOPS_GROUP_ADMIN); // Default webmaster group
    
    // Get module admin groups
    $gperm_handler = xoops_gethandler('groupperm');
    $admin_groups = $gperm_handler->getGroupIds('module_admin', $mid);
    $webmaster_groups = array_merge($webmaster_groups, $admin_groups);
    $webmaster_groups = array_unique($webmaster_groups);
    
    // Get users from these groups
    $member_handler = xoops_gethandler('member');
    
    // Process tags if any
    if (!empty($tags) && is_array($tags)) {
        foreach ($tags as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
    }
    
    // Collect admin emails and user IDs
    $admin_emails = array();
    $admin_uids = array();
    
    // Add site admin email
    if (!empty($xoopsConfig['adminmail'])) {
        $admin_emails[] = $xoopsConfig['adminmail'];
    }
    
    // Try to get webmaster emails and user IDs if member handler is working
    if (is_object($member_handler)) {
        foreach ($webmaster_groups as $group_id) {
            $users = $member_handler->getUsersByGroup($group_id);
            if (is_array($users)) {
                foreach ($users as $user) {
                    if (is_object($user)) {
                        $email = $user->getVar('email');
                        if (!empty($email)) {
                            $admin_emails[] = $email;
                        }
                        $admin_uids[] = $user->getVar('uid');
                    }
                }
            }
        }
    }
    
    // Remove duplicates
    $admin_emails = array_unique($admin_emails);
    $admin_uids = array_unique($admin_uids);
    
    // Send private messages to admins
    $pm_sent = true;
    
    // Try using the standard privmessage handler first (will be intercepted by Message module if active)
    $pm_handler = xoops_gethandler('privmessage');
    
    if (is_object($pm_handler)) {
        // Get the current time for all messages
        $current_time = time();
        
        foreach ($admin_uids as $uid) {
            // Create a new message
            $pm = $pm_handler->create();
            $pm->setVar('subject', $subject);
            $pm->setVar('from_userid', 1); // System user ID
            $pm->setVar('to_userid', $uid);
            $pm->setVar('msg_text', $message);
            $pm->setVar('msg_time', $current_time);
            $pm->setVar('read_msg', 0);
            
            // Save the message
            $result = $pm_handler->insert($pm);
            if ($result) {
                $pm_sent = true;
                // Optionally log success
                // error_log("PM sent to user ID: " . $uid);
            } else {
                // Optionally log failure
                error_log("Error sending PM to user ID: " . $uid);
                $pm_sent = false;
            }
        }
    }
    
    // If no emails found, log error but continue with PM notifications
    if (empty($admin_emails)) {
        error_log("Pico notification: No admin emails found to send notification");
        return $pm_sent; // Return true if at least PMs were sent
    }
    
    // Suppress deprecated warnings for XoopsMailer
    $errorReporting = error_reporting();
    error_reporting($errorReporting & ~E_DEPRECATED);
    
    // Use XoopsCube's mailer with correct methods
    $xoopsMailer = new XoopsMailer();
    $xoopsMailer->useMail();
    $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
    $xoopsMailer->setFromName($xoopsConfig['sitename']);
    $xoopsMailer->setSubject($subject);
    $xoopsMailer->setBody($message);
    
    // Add all recipients using the correct method
    $xoopsMailer->setToEmails($admin_emails); // Pass the entire array at once
    
    // Send the email
    $email_sent = $xoopsMailer->send();
    
    // Restore error reporting
    error_reporting($errorReporting);
    
    // Return true if either email or PM was sent successfully
    return ($email_sent || $pm_sent);
}