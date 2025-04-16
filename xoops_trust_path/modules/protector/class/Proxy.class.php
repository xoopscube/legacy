<?php
/**
 * Protector Web Proxy Class
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class ProtectorProxy {
    private $protector;
    private $config;
    private $stats;
    private $plugins = [];
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->protector = protector::getInstance();
        $this->loadConfig();
        $this->loadStats();
        $this->loadPlugins();
    }
    
    /**
     * Load proxy configuration
     */
    private function loadConfig() {
        $module_handler = xoops_getHandler('module');
        $config_handler = xoops_getHandler('config');
        $module = $module_handler->getByDirname('protector');
        $configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        
        $this->config = [
            'enabled' => $configs['proxy_enabled'] ?? 0,
            'allowed_domains' => $configs['proxy_allowed_domains'] ?? '',
            'blocked_domains' => $configs['proxy_blocked_domains'] ?? '',
            'cache_enabled' => $configs['proxy_cache_enabled'] ?? 1,
            'cache_time' => $configs['proxy_cache_time'] ?? 3600,
            'log_requests' => $configs['proxy_log_requests'] ?? 1,
            'strip_js' => $configs['proxy_strip_js'] ?? 0,
            'strip_cookies' => $configs['proxy_strip_cookies'] ?? 0,
            'user_agent' => $configs['proxy_user_agent'] ?? '',
            'plugins_enabled' => $configs['proxy_plugins_enabled'] ?? []
        ];
    }
    
    /**
     * Load proxy statistics
     */
    private function loadStats() {
        $this->stats = [
            'total_requests' => 0,
            'blocked_requests' => 0,
            'cached_resources' => 0
        ];
        
        $stats_file = XOOPS_TRUST_PATH . '/modules/protector/cache/proxy_stats.php';
        if (file_exists($stats_file)) {
            include $stats_file;
            if (isset($proxy_stats) && is_array($proxy_stats)) {
                $this->stats = $proxy_stats;
            }
        }
    }
    
    /**
     * Save proxy statistics
     */
    private function saveStats() {
        $stats_file = XOOPS_TRUST_PATH . '/modules/protector/cache/proxy_stats.php';
        $content = "<?php\n";
        $content .= "// Proxy statistics - Generated on " . date('Y-m-d H:i:s') . "\n";
        $content .= "\$proxy_stats = " . var_export($this->stats, true) . ";\n";
        
        file_put_contents($stats_file, $content);
    }
    
    /**
     * Load enabled plugins
     */
    private function loadPlugins() {
        if (empty($this->config['plugins_enabled'])) {
            return;
        }
        
        $plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
        if (!is_dir($plugins_dir)) {
            mkdir($plugins_dir, 0755, true);
        }
        
        foreach ($this->config['plugins_enabled'] as $plugin_name) {
            $plugin_file = $plugins_dir . '/' . $plugin_name . '.php';
            if (file_exists($plugin_file)) {
                require_once $plugin_file;
                $class_name = 'ProtectorProxy' . ucfirst($plugin_name) . 'Plugin';
                if (class_exists($class_name)) {
                    $this->plugins[] = new $class_name($this);
                }
            }
        }
    }
    
    /**
     * Process a proxy request
     * 
     * @param string $url The URL to proxy
     * @return bool|string The proxied content or false on error
     */
    public function processRequest($url) {
        // Increment total requests
        $this->stats['total_requests']++;
        
        // Check if URL is allowed
        if (!$this->isUrlAllowed($url)) {
            $this->stats['blocked_requests']++;
            $this->saveStats();
            $this->logRequest($url, 'blocked', 'URL not allowed');
            return false;
        }
        
        // Check cache
        if ($this->config['cache_enabled']) {
            $cached_content = $this->getFromCache($url);
            if ($cached_content !== false) {
                $this->stats['cached_resources']++;
                $this->saveStats();
                $this->logRequest($url, 'cache', 'Served from cache');
                return $cached_content;
            }
        }
        
        // Fetch content
        $content = $this->fetchUrl($url);
        if ($content === false) {
            $this->logRequest($url, 'error', 'Failed to fetch URL');
            return false;
        }
        
        // Process content through plugins
        $content = $this->processContentWithPlugins($url, $content);
        
        // Cache content
        if ($this->config['cache_enabled']) {
            $this->saveToCache($url, $content);
        }
        
        // Log request
        $this->logRequest($url, 'success', 'Proxied successfully');
        
        // Save stats
        $this->saveStats();
        
        return $content;
    }
    
    /**
     * Check if URL is allowed
     * 
     * @param string $url The URL to check
     * @return bool True if allowed, false otherwise
     */
    private function isUrlAllowed($url) {
        $domain = parse_url($url, PHP_URL_HOST);
        
        // Check blocked domains
        $blocked_domains = explode("\n", $this->config['blocked_domains']);
        foreach ($blocked_domains as $blocked) {
            $blocked = trim($blocked);
            if (empty($blocked)) {
                continue;
            }
            
            if ($domain === $blocked || (substr($blocked, 0, 1) === '.' && substr($domain, -strlen($blocked)) === $blocked)) {
                return false;
            }
        }
        
        // If allowed domains is empty, allow all non-blocked domains
        if (empty(trim($this->config['allowed_domains']))) {
            return true;
        }
        
        // Check allowed domains
        $allowed_domains = explode("\n", $this->config['allowed_domains']);
        foreach ($allowed_domains as $allowed) {
            $allowed = trim($allowed);
            if (empty($allowed)) {
                continue;
            }
            
            if ($domain === $allowed || (substr($allowed, 0, 1) === '.' && substr($domain, -strlen($allowed)) === $allowed)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Fetch URL content
     * 
     * @param string $url The URL to fetch
     * @return bool|string The content or false on error
     */
    private function fetchUrl($url) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        // Set custom user agent if configured
        if (!empty($this->config['user_agent'])) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->config['user_agent']);
        }
        
        // Handle cookies if not stripped
        if (!$this->config['strip_cookies']) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, '');
        }
        
        $content = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
        
        if ($content === false) {
            $this->logRequest($url, 'error', 'cURL error: ' . $error);
            return false;
        }
        
        if ($info['http_code'] >= 400) {
            $this->logRequest($url, 'error', 'HTTP error: ' . $info['http_code']);
            return false;
        }
        
        return $content;
    }
    
    /**
     * Process content with plugins
     * 
     * @param string $url The original URL
     * @param string $content The content to process
     * @return string The processed content
     */
    private function processContentWithPlugins($url, $content) {
        foreach ($this->plugins as $plugin) {
            if (method_exists($plugin, 'processContent')) {
                $content = $plugin->processContent($url, $content);
            }
        }
        
        // Strip JavaScript if configured
        if ($this->config['strip_js']) {
            $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        }
        
        return $content;
    }
    
    /**
     * Get content from cache
     * 
     * @param string $url The URL to get from cache
     * @return bool|string The cached content or false if not found
     */
    private function getFromCache($url) {
        $cache_file = $this->getCacheFilename($url);
        
        if (!file_exists($cache_file)) {
            return false;
        }
        
        $cache_time = filemtime($cache_file);
        if ($cache_time < time() - $this->config['cache_time']) {
            // Cache expired
            return false;
        }
        
        return file_get_contents($cache_file);
    }
    
    /**
     * Save content to cache
     * 
     * @param string $url The URL to cache
     * @param string $content The content to cache
     */
    private function saveToCache($url, $content) {
        $cache_dir = XOOPS_TRUST_PATH . '/modules/protector/cache/proxy';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        $cache_file = $this->getCacheFilename($url);
        file_put_contents($cache_file, $content);
    }
    
    /**
     * Get cache filename for URL
     * 
     * @param string $url The URL
     * @return string The cache filename
     */
    private function getCacheFilename($url) {
        $cache_dir = XOOPS_TRUST_PATH . '/modules/protector/cache/proxy';
        return $cache_dir . '/' . md5($url) . '.cache';
    }
    
    /**
     * Log proxy request
     * 
     * @param string $url The requested URL
     * @param string $status The request status
     * @param string $message Additional message
     */
    private function logRequest($url, $status, $message = '') {
        if (!$this->config['log_requests']) {
            return;
        }
        
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        
        $log_data = [
            'timestamp' => time(),
            'ip' => ip2long($_SERVER['REMOTE_ADDR']),
            'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'type' => 'PROXY',
            'description' => sprintf('URL: %s, Status: %s, Message: %s', $url, $status, $message)
        ];
        
        $db->queryF(
            'INSERT INTO ' . $db->prefix($this->protector->mydirname . '_log') . 
            ' (timestamp, ip, agent, type, description) VALUES (' . 
            $log_data['timestamp'] . ', ' . 
            $log_data['ip'] . ', ' . 
            $db->quoteString($log_data['agent']) . ', ' . 
            $db->quoteString($log_data['type']) . ', ' . 
            $db->quoteString($log_data['description']) . ')'
        );
    }
    
    /**
     * Get proxy statistics
     * 
     * @return array The proxy statistics
     */
    public function getStats() {
        return $this->stats;
    }
    
    /**
     * Get proxy configuration
     * 
     * @return array The proxy configuration
     */
    public function getConfig() {
        return $this->config;
    }
}