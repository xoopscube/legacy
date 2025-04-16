<?php
/**
 * Test Plugin for Protector Proxy
 *
 * This is a demonstration plugin showing the different stages
 * where you can modify proxy content.
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class ProtectorProxyTestPlugin {
    private $proxy;
    
    /**
     * Constructor
     * 
     * @param ProtectorProxy $proxy The proxy instance
     */
    public function __construct($proxy) {
        $this->proxy = $proxy;
    }
    
    /**
     * Process content
     * 
     * @param string $url The original URL
     * @param string $content The content to process
     * @return string The processed content
     */
    public function processContent($url, $content) {
        // Add a test banner to show the plugin is working
        $banner = '<div style="background:#e6f7ff;padding:10px;text-align:center;border:1px solid #91d5ff;margin:10px;">
                    <strong>Test Plugin Active:</strong> This content has been modified by the test plugin.
                    <br>URL: ' . htmlspecialchars($url) . '
                    <br>Content Length: ' . strlen($content) . ' bytes
                    <br>Time: ' . date('Y-m-d H:i:s') . '
                   </div>';
        
        // Insert banner after the body tag
        $content = preg_replace('/<body([^>]*)>/i', '<body$1>' . $banner, $content);
        
        // If no body tag found, prepend to the content
        if (strpos($content, '<body') === false) {
            $content = $banner . $content;
        }
        
        // Log that this plugin processed the content
        if (method_exists($this->proxy, 'logRequest')) {
            $this->proxy->logRequest($url, 'plugin', 'Test plugin processed content');
        }
        
        return $content;
    }
}