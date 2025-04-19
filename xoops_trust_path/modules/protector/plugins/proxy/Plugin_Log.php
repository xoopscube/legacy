<?php
/**
 * Logging Plugin for Protector Proxy
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_TRUST_PATH . '/modules/protector/class/PluginBase.class.php';

/**
 * Proxy Log Plugin
 */
class Plugin_Log extends ProtectorProxyPluginBase {
    /**
     * Log directory
     */
    private $logDir;
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Get log directory
        $this->logDir = XOOPS_TRUST_PATH . '/modules/protector/logs';
        
        // Create directory if it doesn't exist
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
        
        return is_writable($this->logDir);
    }
    
    /**
     * Pre-process URL - log the request and pass through
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Log the request
        $this->logRequest($url, [
            'status' => 'requested',
            'time' => time()
        ]);
        
        // Pass through the URL unchanged
        return $url;
    }
    
    /**
     * Post-process content - log the response and pass through
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Determine content type
        $contentType = 'unknown';
        if (strpos($content, '<!DOCTYPE html') !== false || strpos($content, '<html') !== false) {
            $contentType = 'text/html';
        } elseif (substr($content, 0, 5) === '<?xml') {
            $contentType = 'application/xml';
        } elseif (substr($content, 0, 1) === '{' || substr($content, 0, 1) === '[') {
            $contentType = 'application/json';
        }
        
        // Log the response
        $this->logRequest($url, [
            'status' => 'completed',
            'content_type' => $contentType,
            'content_length' => strlen($content),
            'time' => time()
        ]);
        
        // Pass through the content unchanged
        return $content;
    }
    
    /**
     * Handle domain - this plugin doesn't handle any domains directly
     * 
     * @param string $url The URL to handle
     * @return string|false The content or false if not handled
     */
    public function handleDomain($url) {
        // This plugin doesn't handle domains directly
        return false;
    }
    
    /**
     * Log request details
     * 
     * @param string $url The requested URL
     * @param array $response Response details
     * @return void
     */
    private function logRequest($url, $response) {
        if (!is_writable($this->logDir)) {
            return;
        }
        
        // Create log file name based on date
        $logFile = $this->logDir . '/' . date("Y-m-d") . '.log';
        
        // Prepare log data
        $data = array(
            'ip' => $_SERVER['REMOTE_ADDR'],
            'time' => date('Y-m-d H:i:s', $response['time']),
            'url' => $url,
            'status' => $response['status'] ?? 'unknown',
            'type' => $response['content_type'] ?? 'unknown',
            'size' => $response['content_length'] ?? 'unknown'
        );
        
        // Format message
        $message = implode("\t", $data) . "\r\n";
        
        // Write to log file
        @file_put_contents($logFile, $message, FILE_APPEND);
    }
}