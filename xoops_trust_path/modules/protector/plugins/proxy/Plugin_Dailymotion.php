<?php
/**
 * Dailymotion plugin for Protector Proxy
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
 * Plugin for handling Dailymotion content
 */
class Plugin_Dailymotion extends ProtectorProxyPluginBase {
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
     * Handle domain - extract video ID and create embed page if needed
     * 
     * @param string $url The URL to handle
     * @return string|false Content or false if not handled
     */
    public function handleDomain($url) {
        // Check if this is a Dailymotion URL
        if (strpos($url, 'dailymotion.com') !== false) {
            // Extract video ID from URL
            $videoId = $this->extractVideoId($url);
            
            if ($videoId) {
                // Create a simple page with the embedded video
                return $this->createEmbedPage($videoId);
            }
        }
        
        // Let the proxy handle the request normally
        return false;
    }
    
    /**
     * Process Dailymotion content
     * 
     * @param string $content The content to process
     * @param string $baseUrl The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $baseUrl) {
        // Only process Dailymotion URLs
        if (strpos($baseUrl, 'dailymotion.com') === false) {
            return $content;
        }
        
        // Replace Dailymotion embed codes with proxied versions
        $content = preg_replace_callback(
            '/<iframe[^>]*src=(["\'])(https?:\/\/(?:www\.)?dailymotion\.com\/embed\/video\/[^"\']+)\\1[^>]*><\/iframe>/i',
            function($matches) {
                $originalUrl = $matches[2];
                $videoId = $this->extractVideoId($originalUrl);
                
                if ($videoId) {
                    // Create proxied embed URL
                    $proxyUrl = XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($originalUrl);
                    
                    // Return iframe with proxied URL
                    return '<iframe src="' . $proxyUrl . '" frameborder="0" width="480" height="270" allowfullscreen></iframe>';
                }
                
                return $matches[0];
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Extract video ID from Dailymotion URL
     * 
     * @param string $url Dailymotion URL
     * @return string|false Video ID or false if not found
     */
    private function extractVideoId($url) {
        // Match video ID from various Dailymotion URL formats
        if (preg_match('/dailymotion\.com\/(?:embed\/)?video\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/dai\.ly\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return $matches[1];
        }
        
        return false;
    }
    
    /**
     * Create an embed page for a Dailymotion video
     * 
     * @param string $videoId Dailymotion video ID
     * @return string HTML content
     */
    private function createEmbedPage($videoId) {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Dailymotion Video</title>
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
                <h1>Dailymotion Video</h1>
                <div class="video-player">
                    <iframe src="https://www.dailymotion.com/embed/video/' . htmlspecialchars($videoId) . '" 
                            frameborder="0" width="100%" height="100%" 
                            allow="autoplay; encrypted-media" allowfullscreen></iframe>
                </div>
                <div class="video-info">
                    <p>Video ID: ' . htmlspecialchars($videoId) . '</p>
                    <p><a href="' . XOOPS_URL . '/modules/protector/proxy.php">Return to Proxy</a></p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}