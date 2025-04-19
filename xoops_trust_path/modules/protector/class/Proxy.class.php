<?php
/**
 * Protector Web Proxy Class
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

class ProtectorProxy
{
    private $config = [];
    private $lastResponseHeaders = [];
    private $plugins = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loadConfig();
        $this->loadPlugins();
    }
    
    /**
     * Load configuration from module settings
     */
    private function loadConfig()
    {
        // Get module config
        $module_handler = xoops_getHandler('module');
        $config_handler = xoops_getHandler('config');
        $module = $module_handler->getByDirname('protector');
        
        if (is_object($module)) {
            $configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
            
            // Set default config values
            $this->config = [
                'enabled' => false,
                'cache_enabled' => true,
                'cache_time' => 3600,
                'log_requests' => true,
                'strip_js' => false,
                'strip_cookies' => true,
                'plugins_enabled' => []
            ];
            
            // Override with module config values
            foreach ($configs as $name => $value) {
                if (strpos($name, 'proxy_') === 0) {
                    $key = substr($name, 6); // Remove 'proxy_' prefix
                    
                    // Handle serialized arrays
                    if ($name === 'proxy_plugins_enabled' && is_string($value) && !empty($value)) {
                        try {
                            $value = unserialize($value);
                            if (!is_array($value)) {
                                $value = [];
                            }
                        } catch (Exception $e) {
                            error_log("Error unserializing plugins in Proxy class: " . $e->getMessage());
                            $value = [];
                        }
                    }
                    
                    $this->config[$key] = $value;
                }
            }
        }
    }
    
    /**
     * Load enabled plugins
     */
    private function loadPlugins()
    {
        if (!empty($this->config['plugins_enabled']) && is_array($this->config['plugins_enabled'])) {
            $plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
            
            foreach ($this->config['plugins_enabled'] as $plugin) {
                $plugin_file = $plugins_dir . '/' . $plugin . '.php';
                
                if (file_exists($plugin_file)) {
                    include_once $plugin_file;
                    $class_name = 'ProtectorProxy' . ucfirst($plugin) . 'Plugin';
                    
                    if (class_exists($class_name)) {
                        $this->plugins[] = new $class_name();
                    }
                }
            }
        }
    }
    
    /**
     * Get proxy configuration
     * 
     * @return array Configuration array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Get last response headers
     * 
     * @return array Headers from the last request
     */
    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }
    
    /**
     * Process a proxy request
     * 
     * @param string $url URL to proxy
     * @return string|false Content or false on failure
     */
    public function processRequest($url)
    {
        // Initialize cURL
        $ch = curl_init();
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        
        // Capture response headers
        $this->lastResponseHeaders = [];
        $headerCallback = function($ch, $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) == 2) {
                $this->lastResponseHeaders[trim($parts[0])] = trim($parts[1]);
            }
            return strlen($header);
        };
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, $headerCallback);
        
        // Execute request
        $content = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        // Close cURL handle
        curl_close($ch);
        
        // Check for errors
        if ($content === false) {
            error_log('Proxy error: ' . $error);
            return false;
        }
        
        // Process content through plugins
        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, 'processContent')) {
                $content = $plugin->processContent($content, $url, $info);
            }
        }
        
        // Strip JavaScript if configured
        if ($this->config['strip_js']) {
            $content = $this->stripJavaScript($content);
        }
        
        return $content;
    }
    
    /**
     * Strip JavaScript from HTML content
     * 
     * @param string $content HTML content
     * @return string Content without JavaScript
     */
    private function stripJavaScript($content)
    {
        // Remove script tags
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        
        // Remove onclick, onload, etc. attributes
        $content = preg_replace('/\s+on\w+\s*=\s*(["\']).*?\1/i', '', $content);
        
        // Remove javascript: URLs
        $content = preg_replace('/href\s*=\s*(["\'])javascript:.*?\1/i', 'href="javascript:void(0)"', $content);
        
        return $content;
    }
    
    /**
     * Rewrite URLs in HTML content to go through the proxy
     * 
     * @param string $content HTML content
     * @param string $base_url Base URL of the original content
     * @return string Content with rewritten URLs
     */
    public function rewriteUrls($content, $base_url)
    {
        // Parse base URL
        $parsed_url = parse_url($base_url);
        $base_domain = $parsed_url['scheme'] . '://' . $parsed_url['host'] . (isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '');
        $base_path = isset($parsed_url['path']) ? dirname($parsed_url['path']) : '/';
        
        // Replace relative URLs with absolute ones
        $patterns = [
            // href attributes
            '/href\s*=\s*(["\'])\s*(?!http|https|ftp|mailto|javascript|data|#)([^"\']+)(["\'])/i',
            // src attributes
            '/src\s*=\s*(["\'])\s*(?!http|https|ftp|data)([^"\']+)(["\'])/i',
            // url() in CSS
            '/url\s*\(\s*(["\']?)(?!http|https|ftp|data)([^"\')\s]+)(["\']?)\s*\)/i'
        ];
        
        $replacements = [
            'href=$1' . $base_domain . ($base_path == '/' ? '/' : $base_path . '/') . '$2$3',
            'src=$1' . $base_domain . ($base_path == '/' ? '/' : $base_path . '/') . '$2$3',
            'url($1' . $base_domain . ($base_path == '/' ? '/' : $base_path . '/') . '$2$3)'
        ];
        
        $content = preg_replace($patterns, $replacements, $content);
        
        // Replace absolute URLs with proxied ones
        $proxy_url = XOOPS_MODULE_URL . '/protector/proxy.php?url=';
        
        // Don't rewrite URLs for resources like images, CSS, JS
        $content = preg_replace_callback(
            '/href\s*=\s*(["\'])(https?:\/\/[^"\']+)(["\'])/i',
            function($matches) use ($proxy_url) {
                $url = $matches[2];
                // Don't rewrite resource URLs
                if (preg_match('/\.(jpg|jpeg|png|gif|css|js|ico|svg|woff|woff2|ttf|eot)(\?.*)?$/i', $url)) {
                    return 'href=' . $matches[1] . $url . $matches[3];
                }
                return 'href=' . $matches[1] . $proxy_url . urlencode($url) . $matches[3];
            },
            $content
        );
        
        return $content;
    }
    
    /**
     * Update proxy statistics
     *
     * @param string $stat_type Type of stat to update (total_requests, blocked_requests, cached_resources)
     * @param int $increment Amount to increment (default 1)
     * @return bool Success
     */
    public function updateStats($stat_type, $increment = 1)
    {
        // Use the proper cache path as defined in settings
        $stats_file = XOOPS_CACHE_PATH . '/protector/proxy_stats.php';
        
        // Create directory if it doesn't exist
        if (!is_dir(dirname($stats_file))) {
            mkdir(dirname($stats_file), 0777, true);
        }
        
        // Initialize stats array
        $proxy_stats = [
            'total_requests' => 0,
            'blocked_requests' => 0,
            'cached_resources' => 0
        ];
        
        // Load existing stats if available
        if (file_exists($stats_file)) {
            include $stats_file;
        }
        
        // Update the specified stat
        if (isset($proxy_stats[$stat_type])) {
            $proxy_stats[$stat_type] += $increment;
        }
        
        // Save the updated stats
        $stats_content = "<?php\n";
        $stats_content .= "// Proxy statistics - auto-generated\n";
        $stats_content .= "\$proxy_stats['total_requests'] = " . $proxy_stats['total_requests'] . ";\n";
        $stats_content .= "\$proxy_stats['blocked_requests'] = " . $proxy_stats['blocked_requests'] . ";\n";
        $stats_content .= "\$proxy_stats['cached_resources'] = " . $proxy_stats['cached_resources'] . ";\n";
        
        return file_put_contents($stats_file, $stats_content) !== false;
    }
}