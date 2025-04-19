<?php
/**
 * Socket Fetcher Plugin for Protector Proxy
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
 * Socket Fetcher plugin for proxy functionality
 */
class Plugin_SocketFetcher extends ProtectorProxyPluginBase {
    /**
     * Initialize plugin
     */
    public function init() {
        return true;
    }
    
    /**
     * Pre-process URL - this method is required by the interface
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Just return the URL unchanged
        return $url;
    }
    
    /**
     * Post-process content - this method is required by the interface
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Just return the content unchanged
        return $content;
    }
    
    /**
     * Handle domain - use socket connection for all domains
     * 
     * @param string $url The URL to handle
     * @return string|false The content or false if not handled
     */
    public function handleDomain($url) {
        // Parse URL
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) {
            return false;
        }
        
        $scheme = isset($parsedUrl['scheme']) ? strtolower($parsedUrl['scheme']) : 'http';
        $host = $parsedUrl['host'] ?? '';
        $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : ($scheme === 'https' ? 443 : 80);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        
        // Build request path
        $requestPath = $path . $query;
        
        // Get user agent from config
        $config = $this->proxy->getConfig();
        $userAgent = $config['user_agent'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
        
        // Create socket connection
        $socketUrl = ($scheme === 'https' ? 'ssl://' : '') . $host;
        $fp = @fsockopen($socketUrl, $port, $errno, $errstr, 30);
        
        if (!$fp) {
            return false;
        }
        
        // Build HTTP request
        $request = "GET $requestPath HTTP/1.1\r\n";
        $request .= "Host: $host\r\n";
        $request .= "User-Agent: $userAgent\r\n";
        $request .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n";
        $request .= "Accept-Language: en-US,en;q=0.5\r\n";
        $request .= "Accept-Encoding: identity\r\n"; // Don't request gzip to simplify processing
        $request .= "Connection: close\r\n\r\n";
        
        // Send request
        fwrite($fp, $request);
        
        // Read response
        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 4096);
        }
        fclose($fp);
        
        // Split headers and body
        $parts = explode("\r\n\r\n", $response, 2);
        if (count($parts) < 2) {
            return false;
        }
        
        $headers = $parts[0];
        $body = $parts[1];
        
        // Check for redirects
        if (preg_match('/^HTTP\/[\d.]+\s+3\d\d\s+/i', $headers)) {
            if (preg_match('/Location:\s*(.+?)[\r\n]/i', $headers, $matches)) {
                $redirectUrl = trim($matches[1]);
                
                // Handle relative URLs
                if (strpos($redirectUrl, 'http') !== 0) {
                    if (strpos($redirectUrl, '/') === 0) {
                        $redirectUrl = "$scheme://$host" . $redirectUrl;
                    } else {
                        $redirectUrl = "$scheme://$host" . dirname($path) . '/' . $redirectUrl;
                    }
                }
                
                // Follow redirect (limit to 5 redirects to prevent infinite loops)
                static $redirectCount = 0;
                if ($redirectCount < 5) {
                    $redirectCount++;
                    return $this->handleDomain($redirectUrl);
                }
            }
        }
        
        // Reset redirect counter for future requests
        static $redirectCount = 0;
        
        return $body;
    }
}