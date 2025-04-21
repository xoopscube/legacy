<?php
/**
 * CORS Plugin for Protector Proxy
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_TRUST_PATH . '/modules/protector/class/PluginBase.class.php';

/**
 * CORS plugin for proxy functionality
 * Allows cross-origin requests to pass through the proxy with appropriate headers
 */
class Plugin_CORS extends ProtectorProxyPluginBase {
    /**
     * Configuration options
     */
    private $config = [
        'allow_origin' => '*',                    // Which origins to allow (default: all)
        'allow_methods' => 'GET,POST,PUT,DELETE,OPTIONS', // Allowed HTTP methods
        'allow_headers' => '*',                   // Allowed headers
        'expose_headers' => '',                   // Headers to expose to the client
        'max_age' => '86400',                     // Cache preflight requests (24 hours)
        'allow_credentials' => 'true',            // Allow cookies and authentication
        'debug_mode' => false                     // Enable debug logging
    ];
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load configuration from module preferences if available
        $config = $this->loadConfig();
        if ($config) {
            $this->config = array_merge($this->config, $config);
        }
        
        return true;
    }
    
    /**
     * Load configuration from module preferences
     */
    private function loadConfig() {
        $config = [];
        
        // Get module config
        $module_handler = xoops_getHandler('module');
        $module = $module_handler->getByDirname('protector');
        
        if (is_object($module)) {
            $config_handler = xoops_getHandler('config');
            $moduleConfig = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
            
            // Map module config to plugin config
            if (isset($moduleConfig['proxy_cors_origin'])) {
                $config['allow_origin'] = $moduleConfig['proxy_cors_origin'];
            }
            
            if (isset($moduleConfig['proxy_cors_methods'])) {
                $config['allow_methods'] = $moduleConfig['proxy_cors_methods'];
            }
            
            if (isset($moduleConfig['proxy_cors_headers'])) {
                $config['allow_headers'] = $moduleConfig['proxy_cors_headers'];
            }
            
            if (isset($moduleConfig['proxy_cors_debug'])) {
                $config['debug_mode'] = (bool)$moduleConfig['proxy_cors_debug'];
            }
        }
        
        return $config;
    }
    
    /**
     * Pre-process URL - add CORS headers for preflight requests
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Handle preflight OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $this->addCorsHeaders();
            
            // For OPTIONS requests, we can return an empty response
            header('Content-Length: 0');
            header('Content-Type: text/plain');
            exit(0);
        }
        
        // Debug logging
        if ($this->config['debug_mode']) {
            error_log('CORS Plugin: Processing request to ' . $url);
        }
        
        // Continue with the request
        return $url;
    }
    
    /**
     * Post-process content - add CORS headers to response
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Add CORS headers to the response
        $this->addCorsHeaders();
        
        // Debug logging
        if ($this->config['debug_mode']) {
            error_log('CORS Plugin: Added headers to response from ' . $url);
        }
        
        // Return the content unchanged
        return $content;
    }
    
    /**
     * Add CORS headers to the response
     */
    private function addCorsHeaders() {
        // Add CORS headers
        header('Access-Control-Allow-Origin: ' . $this->config['allow_origin']);
        
        // Only add other headers if we're allowing the origin
        if ($this->config['allow_origin'] !== 'none') {
            header('Access-Control-Allow-Methods: ' . $this->config['allow_methods']);
            
            // Only add these headers if they're not empty
            if (!empty($this->config['allow_headers'])) {
                header('Access-Control-Allow-Headers: ' . $this->config['allow_headers']);
            }
            
            if (!empty($this->config['expose_headers'])) {
                header('Access-Control-Expose-Headers: ' . $this->config['expose_headers']);
            }
            
            header('Access-Control-Max-Age: ' . $this->config['max_age']);
            header('Access-Control-Allow-Credentials: ' . $this->config['allow_credentials']);
        }
    }
    
    /**
     * Handle specific domains if needed
     * 
     * @param string $url The URL to check
     * @return string|false Content to return or false to continue processing
     */
    public function handleDomain($url) {
        // This plugin doesn't handle specific domains directly
        return false;
    }
}