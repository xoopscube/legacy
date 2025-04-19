<?php
/**
 * Base class for Protector Proxy plugins
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

/**
 * Abstract base class for proxy plugins
 */
class ProtectorProxyPluginBase {
    /**
     * Reference to the proxy
     */
    protected $proxy;
    
    /**
     * Constructor
     */
    public function __construct($proxy) {
        $this->proxy = $proxy;
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Override in child classes if needed
    }
    
    /**
     * Process content after fetching
     * 
     * @param string $content The content to process
     * @param string $baseUrl The base URL of the content
     * @return string|false The processed content or false on failure
     */
    public function postProcess($content, $baseUrl) {
        // Override in child classes
        return $content;
    }
}

// Instead of defining the namespace here, include a separate file
require_once __DIR__ . '/PluginBaseNamespace.class.php';