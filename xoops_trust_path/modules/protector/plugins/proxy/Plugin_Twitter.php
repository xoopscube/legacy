<?php
/**
 * Twitter Plugin for Protector Proxy
 *
 * This plugin handles Twitter content by removing scripts
 * and optimizing the display.
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
 * Twitter Plugin
 */
class Plugin_Twitter extends ProtectorProxyPluginBase {
    /**
     * Initialize plugin
     * 
     * @return bool Success
     */
    public function init() {
        return true;
    }
    
    /**
     * Pre-process URL - this plugin doesn't modify URLs
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // This plugin doesn't modify URLs
        return $url;
    }
    
    /**
     * Handle specific domains
     * 
     * @param string $url The URL to handle
     * @return string|false Content or false to continue normal processing
     */
    public function handleDomain($url) {
        // Check if this is a Twitter URL
        if (strpos($url, 'twitter.com') !== false || strpos($url, 'x.com') !== false) {
            // Let the proxy handle the initial fetch, we'll process it in postProcess
            return false;
        }
        return false;
    }
    
    /**
     * Process content after fetching
     * 
     * @param string $content The content to process
     * @param string $url The original URL
     * @return string Modified content
     */
    public function postProcess($content, $url) {
        // Only process Twitter URLs
        if (strpos($url, 'twitter.com') === false && strpos($url, 'x.com') === false) {
            return $content;
        }
        
        // Remove all JavaScript
        $content = $this->removeScripts($content);
        
        // Add Twitter-specific styling
        $twitter_css = '<style>
            /* Twitter styles added by Protector Proxy */
            .twitter-timeline {
                max-width: 100% !important;
                width: 100% !important;
            }
            .twitter-tweet {
                margin: 10px auto !important;
                max-width: 550px !important;
            }
        </style>';
        
        // Add CSS to head
        $content = preg_replace('/<\/head>/i', $twitter_css . '</head>', $content);
        
        // Add notice about limited functionality
        $notice = '<div style="background:#f5f8fa;padding:10px;text-align:center;border:1px solid #e1e8ed;margin:10px;">
                    <strong>Twitter/X Notice:</strong> You are viewing Twitter/X through a secure proxy.
                    Some interactive features may be limited for security reasons.
                   </div>';
        
        // Insert notice after the body tag
        $content = preg_replace('/<body([^>]*)>/i', '<body$1>' . $notice, $content);
        
        return $content;
    }
    
    /**
     * Remove scripts from content
     * 
     * @param string $content HTML content
     * @return string Content without scripts
     */
    private function removeScripts($content) {
        // Remove script tags and their content
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        
        // Remove onclick and other event handlers
        $content = preg_replace('/\s+on\w+\s*=\s*([\'"])[^\'"]*\1/i', '', $content);
        
        return $content;
    }
}