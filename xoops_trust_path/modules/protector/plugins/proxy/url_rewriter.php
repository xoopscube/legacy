<?php
/**
 * URL Rewriter Plugin for Protector Proxy
 *
 * This plugin rewrites URLs in proxied content to ensure they are also
 * accessed through the proxy.
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class ProtectorProxyUrlRewriterPlugin {
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
        // Get base URL
        $base_url = parse_url($url);
        $base_scheme = $base_url['scheme'] ?? 'http';
        $base_host = $base_url['host'] ?? '';
        $base_path = isset($base_url['path']) ? dirname($base_url['path']) : '';
        
        if ($base_path === '/') {
            $base_path = '';
        }
        
        // Replace absolute URLs
        $content = preg_replace_callback(
            '/(href|src|action)=["\']((https?:\/\/[^"\']+))["\']/',
            function($matches) {
                $attr = $matches[1];
                $full_url = $matches[2];
                
                // Don't rewrite URLs that are already proxied
                if (strpos($full_url, XOOPS_URL . '/modules/protector/proxy.php') === 0) {
                    return $matches[0];
                }
                
                return $attr . '="' . XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($full_url) . '"';
            },
            $content
        );
        
        // Replace relative URLs
        $content = preg_replace_callback(
            '/(href|src|action)=["\']((?!https?:\/\/|javascript:|mailto:|#)[^"\']+)["\']/',
            function($matches) use ($base_scheme, $base_host, $base_path) {
                $attr = $matches[1];
                $rel_url = $matches[2];
                
                // Handle different types of relative URLs
                if (substr($rel_url, 0, 1) === '/') {
                    // Root-relative URL
                    $full_url = $base_scheme . '://' . $base_host . $rel_url;
                } else {
                    // Directory-relative URL
                    $full_url = $base_scheme . '://' . $base_host . $base_path . '/' . $rel_url;
                }
                
                return $attr . '="' . XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($full_url) . '"';
            },
            $content
        );
        
        // Replace CSS URLs
        $content = preg_replace_callback(
            '/url\(["\']?([^)]+)["\']?\)/',
            function($matches) use ($base_scheme, $base_host, $base_path) {
                $url = trim($matches[1], '\'"');
                
                // Skip data URLs
                if (strpos($url, 'data:') === 0) {
                    return $matches[0];
                }
                
                // Handle absolute URLs
                if (preg_match('/^https?:\/\//', $url)) {
                    return 'url("' . XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($url) . '")';
                }
                
                // Handle relative URLs
                if (substr($url, 0, 1) === '/') {
                    // Root-relative URL
                    $full_url = $base_scheme . '://' . $base_host . $url;
                } else {
                    // Directory-relative URL
                    $full_url = $base_scheme . '://' . $base_host . $base_path . '/' . $url;
                }
                
                return 'url("' . XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($full_url) . '")';
            },
            $content
        );
        
        return $content;
    }
}