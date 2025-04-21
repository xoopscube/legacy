<?php
/**
 * Content Security Policy Report Handler
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include necessary files
require_once '../../mainfile.php';

// Create log directory if it doesn't exist
$log_dir = XOOPS_CACHE_PATH . '/protector/logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Debug log for all requests to this endpoint
$debug_log = $log_dir . '/csp_report_debug.log';
$debug_entry = date('Y-m-d H:i:s') . " - CSP Report Handler accessed\n";
$debug_entry .= "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
$debug_entry .= "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set') . "\n";
$debug_entry .= "Raw input: " . file_get_contents('php://input') . "\n\n";
file_put_contents($debug_log, $debug_entry, FILE_APPEND);

// Only accept POST requests with JSON content
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit;
}

// Get the raw POST data
$json_data = file_get_contents('php://input');
$report = json_decode($json_data, true);

// Validate the report data
if (!$report || !isset($report['csp-report'])) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Extract the CSP report data
$csp_report = $report['csp-report'];

// Format the report for logging
$log_message = sprintf(
    "CSP Violation: %s\nBlocked URI: %s\nViolated Directive: %s\nReferrer: %s\nUser Agent: %s\n",
    $csp_report['document-uri'] ?? 'unknown',
    $csp_report['blocked-uri'] ?? 'unknown',
    $csp_report['violated-directive'] ?? 'unknown',
    $csp_report['referrer'] ?? 'N/A',
    $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
);

// Additional details if available
if (isset($csp_report['line-number'])) {
    $log_message .= sprintf(
        "Line: %s, Column: %s, Source File: %s\n",
        $csp_report['line-number'],
        $csp_report['column-number'] ?? 'N/A',
        $csp_report['source-file'] ?? 'N/A'
    );
}

// Format log entry with timestamp and IP
$log_entry = sprintf(
    "[%s] [IP: %s]\n%s\n---\n",
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'],
    $log_message
);

// Write to log file with proper permissions
$log_file = $log_dir . '/csp_violations.log';
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Store in database for the Protector admin interface
require_once XOOPS_TRUST_PATH.'/modules/protector/class/ProtectorDB.class.php';
if (class_exists('ProtectorDB')) {
    $db = new ProtectorDB();
    if (method_exists($db, 'insertCSPViolation')) {
        $db->insertCSPViolation($csp_report);
    } else {
        file_put_contents($debug_log, date('Y-m-d H:i:s') . " - Error: insertCSPViolation method not found\n", FILE_APPEND);
    }
} else {
    file_put_contents($debug_log, date('Y-m-d H:i:s') . " - Error: ProtectorDB class not found\n", FILE_APPEND);
}

// Return a 204 No Content response to the browser
header('HTTP/1.1 204 No Content');
exit;