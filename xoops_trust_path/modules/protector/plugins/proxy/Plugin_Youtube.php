<?php
/**
 * YouTube Plugin for Protector Proxy
 *
 * This plugin handles YouTube content by providing a simplified
 * and more secure viewing experience.
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
 * YouTube Plugin
 */
class Plugin_Youtube extends ProtectorProxyPluginBase {
    /**
     * Initialize plugin
     * 
     * @return bool Success
     */
    public function init() {
        return true;
    }
    
    /**
     * Pre-process URL - modify headers for YouTube requests
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Only process YouTube URLs
        if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
            // Set custom user agent for YouTube requests
            if (function_exists('curl_setopt') && isset($this->proxy->ch)) {
                curl_setopt($this->proxy->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
            }
            
            // Redirect homepage to trending to avoid issues
            if (preg_match('#^https?://(www\.)?youtube\.com/?$#i', $url)) {
                return 'https://www.youtube.com/feed/trending';
            }
        }
        
        return $url;
    }
    
    /**
     * Post-process content - modify YouTube pages
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Only process YouTube URLs
        if (strpos($url, 'youtube.com') === false && strpos($url, 'youtu.be') === false) {
            return $content;
        }
        
        // Extract video ID if this is a watch page
        $videoId = $this->extractVideoId($url);
        
        if ($videoId) {
            // Create a simplified video page
            return $this->createVideoPage($videoId, $url);
        }
        
        // For other YouTube pages, remove scripts and ads
        $content = $this->removeScripts($content);
        
        // Add notice about limited functionality
        $notice = '<div style="background:#f8f8f8;padding:10px;text-align:center;border:1px solid #e1e1e1;margin:10px;">
                    <strong>YouTube Notice:</strong> You are viewing YouTube through a secure proxy.
                    Some interactive features may be limited for security reasons.
                   </div>';
        
        // Insert notice after the body tag
        $content = preg_replace('/<body([^>]*)>/i', '<body$1>' . $notice, $content);
        
        return $content;
    }
    
    /**
     * Handle domain - create custom video player for YouTube videos
     * 
     * @param string $url The URL to handle
     * @return string|false The content or false if not handled
     */
    public function handleDomain($url) {
        // Extract video ID
        $videoId = $this->extractVideoId($url);
        
        if ($videoId) {
            // Create a simplified video page
            return $this->createVideoPage($videoId, $url);
        }
        
        return false;
    }
    
    /**
     * Extract YouTube video ID from URL
     * 
     * @param string $url YouTube URL
     * @return string|false Video ID or false if not found
     */
    private function extractVideoId($url) {
        // Match video ID from various YouTube URL formats
        if (preg_match('/[?&]v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return false;
    }
    
    /**
     * Create a video player page for YouTube
     * 
     * @param string $videoId YouTube video ID
     * @param string $originalUrl Original YouTube URL
     * @return string HTML content
     */
    private function createVideoPage($videoId, $originalUrl) {
        $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $videoId;
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>YouTube Video</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; background: #f5f5f5; }
                .video-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .video-player { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
                .video-player iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
                .video-info { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class="video-container">
                <h1>YouTube Video</h1>
                <div class="video-player">
                    <iframe src="' . htmlspecialchars($embedUrl) . '" 
                            frameborder="0" width="100%" height="100%" 
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen></iframe>
                </div>
                <div class="video-info">
                    <p>Video ID: ' . htmlspecialchars($videoId) . '</p>
                    <p>Original URL: ' . htmlspecialchars($originalUrl) . '</p>
                    <p><a href="' . XOOPS_URL . '/modules/protector/proxy.php">Return to Proxy</a></p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
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
