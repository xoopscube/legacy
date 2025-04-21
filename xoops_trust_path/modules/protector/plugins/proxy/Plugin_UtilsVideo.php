<?php
/**
 * Video Utilities Plugin for Protector Proxy
 *
 * This plugin provides video player functionality for proxied video content.
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
 * Video Utilities Plugin
 */
class Plugin_UtilsVideo extends ProtectorProxyPluginBase {
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
     * Post-process content - this plugin doesn't modify content
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // This plugin doesn't modify content in post-processing
        return $content;
    }
    
    /**
     * Handle specific domains or file types
     * 
     * @param string $url The URL to handle
     * @return string|false Content or false to continue normal processing
     */
    public function handleDomain($url) {
        // Check if this is a video file
        $path = parse_url($url, PHP_URL_PATH);
        
        if ($path) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            
            // Handle video files
            if (in_array(strtolower($extension), ['mp4', 'webm', 'ogg', 'mov', 'avi'])) {
                // Create a simple page with the video player
                $html = $this->createVideoPage($url);
                return $html;
            }
        }
        
        return false;
    }
    
    /**
     * Create a video player page
     * 
     * @param string $url The video URL
     * @param string $width Width of the player
     * @param string $height Height of the player
     * @return string HTML content
     */
    public function createVideoPage($url, $width = '100%', $height = '100%') {
        $videoPlayer = $this->createVideoPlayer($url, $width, $height);
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Video Player</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body { margin: 0; padding: 20px; font-family: Arial, sans-serif; background: #f5f5f5; }
                .video-container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .video-player { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
                .video-player video, .video-player object { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
                .video-info { margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; }
            </style>
        </head>
        <body>
            <div class="video-container">
                <h1>Secure Video Player</h1>
                <div class="video-player">
                    ' . $videoPlayer . '
                </div>
                <div class="video-info">
                    <p>Source: ' . htmlspecialchars($url) . '</p>
                    <p><a href="' . XOOPS_URL . '/modules/protector/proxy.php">Return to Proxy</a></p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Create a video player
     * 
     * @param string $url The video URL
     * @param string $width Width of the player
     * @param string $height Height of the player
     * @param string $extension Force specific extension
     * @return string HTML for the video player
     */
    public function createVideoPlayer($url, $width = '100%', $height = '100%', $extension = false) {
        $path = parse_url($url, PHP_URL_PATH);
        $html5 = false;
        
        if ($path) {
            $extension = $extension ? $extension : pathinfo($path, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($extension), ['mp4', 'webm', 'ogg'])) {
                $html5 = true;
            }
        }
        
        // Create proxied URL
        $video_url = XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode($url);
        
        if ($html5) {
            // HTML5 video player
            $html = '<video width="' . $width . '" height="' . $height . '" controls autoplay>
                <source src="' . $video_url . '" type="video/' . $extension . '">
                Your browser does not support the video tag.
            </video>';
        } else {
            // Fallback to a more modern player for non-HTML5 videos
            $encoded_url = rawurlencode($video_url);
            
            $html = '<div class="fallback-player">
                <p>This video format may not be supported by your browser.</p>
                <a href="' . $video_url . '" target="_blank" class="download-link">
                    Download Video
                </a>
            </div>';
        }
        
        return $html;
    }
}