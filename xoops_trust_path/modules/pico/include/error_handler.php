<?php
/**
 * Pico content management D3 module for XCL
 * Custom error handler for Pico module
 * 
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster, XCL/PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

/**
 * Register custom error handler for Pico
 * 
 * @param string $mydirname Module directory name
 */
function pico_register_error_handler($mydirname) {
    // Store the module dirname for later use
    global $pico_error_mydirname;
    $pico_error_mydirname = $mydirname;
    
    // Set custom error handler
    set_error_handler('pico_error_handler');
}

/**
 * Custom error handler function
 * 
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool Whether the error was handled
 */
function pico_error_handler($errno, $errstr, $errfile, $errline) {
    global $pico_error_mydirname;
    
    // Only handle errors in the Pico module
    if (!strpos($errfile, 'pico')) {
        return false; // Let PHP handle errors outside of Pico
    }
    
    // Log to XoopsLogger if available
    if (class_exists('XoopsLogger') && isset($GLOBALS['xoopsLogger']) && is_object($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addExtra('Pico Error', "$errstr in $errfile on line $errline");
    }
    
    // Log to PHP error log
    error_log("Pico module error: $errstr in $errfile on line $errline");
    
    // For serious errors, notify webmasters
    if ($errno == E_ERROR || $errno == E_PARSE || $errno == E_CORE_ERROR || 
        $errno == E_COMPILE_ERROR || $errno == E_USER_ERROR || $errno == E_USER_WARNING) {
        
        // Include notification function
        include_once dirname(__FILE__) . '/notification.inc.php';
        
        // Send notification
        $subject = 'Pico Module: Error Detected';
        $message = "An error has occurred in the Pico module:\n\n";
        $message .= "Error: $errstr\n";
        $message .= "File: $errfile\n";
        $message .= "Line: $errline\n";
        
        pico_notify_webmasters($pico_error_mydirname, $subject, $message);
    }
    
    // Return true to prevent PHP's internal error handler
    return true;
}