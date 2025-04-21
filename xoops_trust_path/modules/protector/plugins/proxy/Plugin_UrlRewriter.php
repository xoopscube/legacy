<?php
/**
 * URL Rewriter Plugin for Protector Proxy
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
 * URL Rewriter plugin for proxy functionality
 */
class Plugin_UrlRewriter extends ProtectorProxyPluginBase {
    /**
     * URL rewriting rules
     */
    private $rules = [];
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Get configuration
        $config = $this->proxy->getConfig();
        
        // Load rewriting rules from config
        if (isset($config['url_rewrite_rules']) && is_array($config['url_rewrite_rules'])) {
            $this->rules = $config['url_rewrite_rules'];
        } else {
            // Default rules
            $this->rules = [
                // Example: '/search\?q=(.+)/' => '/search?query=$1'
            ];
        }
        
        return true;
    }
    
    /**
     * Pre-process URL - apply URL rewriting rules
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Apply rewriting rules to URL
        foreach ($this->rules as $pattern => $replacement) {
            // If pattern is a regex (starts and ends with /)
            if (substr($pattern, 0, 1) === '/' && substr($pattern, -1) === '/') {
                $url = preg_replace($pattern, $replacement, $url);
            } else {
                // Simple string replacement
                $url = str_replace($pattern, $replacement, $url);
            }
        }
        
        return $url;
    }
    
    /**
     * Post-process content - rewrite URLs in content
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Only process HTML content
        if (strpos($content, '<!DOCTYPE html') !== false || strpos($content, '<html') !== false) {
            // Parse URL to get base
            $parsedUrl = parse_url($url);
            $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':' . $parsedUrl['port'];
            }
            
            // Rewrite absolute URLs to go through proxy
            $proxyUrl = XOOPS_URL . '/modules/protector/proxy.php?url=';
            
            // Replace href and src attributes
            $content = preg_replace_callback(
                '/(href|src)=(["\'])(https?:\/\/[^"\']+)(["\'])/i',
                function($matches) use ($proxyUrl) {
                    $attr = $matches[1];
                    $quote = $matches[2];
                    $targetUrl = $matches[3];
                    $endQuote = $matches[4];
                    
                    // Don't rewrite URLs that are already going through the proxy
                    if (strpos($targetUrl, $proxyUrl) === 0) {
                        return $matches[0];
                    }
                    
                    return $attr . '=' . $quote . $proxyUrl . urlencode($targetUrl) . $endQuote;
                },
                $content
            );
        }
        
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
}