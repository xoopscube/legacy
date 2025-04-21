<?php

/**
 * Protector Threat Intelligence Class
 *
 * Handles threat intelligence feeds and IP checking against known malicious sources
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

class ProtectorThreatIntelligence
{
    private $protector;
    private $config;
    private $cache;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->protector = protector::getInstance();
        $this->loadConfig();
        $this->initializeCache();
    }
    
    /**
     * Load configuration from module settings
     */
    private function loadConfig()
    {
        $module_handler = xoops_getHandler('module');
        $config_handler = xoops_getHandler('config');
        
        $module = $module_handler->getByDirname('protector');
        $configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        
        $this->config = [
            'httpbl_enabled' => $configs['httpbl_enabled'] ?? false,
            'httpbl_key' => $configs['httpbl_key'] ?? '',
            'httpbl_threat_threshold' => $configs['httpbl_threat_threshold'] ?? 25,
            'feed_urls' => $configs['ti_feed_urls'] ?? '',
            'check_login' => $configs['ti_check_login'] ?? true,
            'check_register' => $configs['ti_check_register'] ?? true,
            'check_forms' => $configs['ti_check_forms'] ?? false,
            'check_admin' => $configs['ti_check_admin'] ?? false,
            'cache_duration' => $configs['ti_cache_duration'] ?? 3600,
        ];
    }
    
    /**
     * Initialize the cache system
     */
    private function initializeCache()
    {
        $this->cache = [];
        // Load cached data if available
        $cache_file = XOOPS_TRUST_PATH . '/cache/protector_ti_cache.php';
        if (file_exists($cache_file)) {
            include $cache_file;
            if (isset($ti_cache) && is_array($ti_cache)) {
                $this->cache = $ti_cache;
            }
        }
    }
    
    /**
     * Check if an IP is malicious using HTTP:BL
     * 
     * @param string $ip IP address to check
     * @return array Result with threat information or false if not malicious
     */
    public function checkIpHttpBl($ip)
    {
        // Skip if not enabled or no API key
        if (!$this->config['httpbl_enabled'] || empty($this->config['httpbl_key'])) {
            return false;
        }
        
        // Check cache first
        $cache_key = 'httpbl_' . $ip;
        if (isset($this->cache[$cache_key]) && $this->cache[$cache_key]['expires'] > time()) {
            return $this->cache[$cache_key]['data'];
        }
        
        // Perform HTTP:BL lookup
        $result = $this->performHttpBlLookup($ip);
        
        // Cache the result
        $this->cache[$cache_key] = [
            'data' => $result,
            'expires' => time() + $this->config['cache_duration']
        ];
        $this->saveCache();
        
        return $result;
    }
    
    /**
     * Perform actual HTTP:BL DNS lookup
     */
    private function performHttpBlLookup($ip)
    {
        // Skip for private IPs
        if ($this->isPrivateIp($ip)) {
            return false;
        }
        
        $key = $this->config['httpbl_key'];
        $reverse_ip = implode('.', array_reverse(explode('.', $ip)));
        $lookup = $key . '.' . $reverse_ip . '.dnsbl.httpbl.org';
        
        $result = gethostbyname($lookup);
        
        // If no result or same as lookup, not found
        if ($result === $lookup) {
            return false;
        }
        
        // Parse the response
        $octets = explode('.', $result);
        if (count($octets) !== 4 || $octets[0] !== '127') {
            return false;
        }
        
        // Format: 127.days.threat.type
        return [
            'days_since_last_activity' => (int)$octets[1],
            'threat_score' => (int)$octets[2],
            'visitor_type' => $this->getVisitorType((int)$octets[3]),
            'is_malicious' => ((int)$octets[2] >= $this->config['httpbl_threat_threshold'])
        ];
    }
    
    /**
     * Get visitor type from HTTP:BL response code
     */
    private function getVisitorType($code)
    {
        $types = [];
        
        if ($code & 1) {
            $types[] = 'search_engine';
        }
        if ($code & 2) {
            $types[] = 'suspicious';
        }
        if ($code & 4) {
            $types[] = 'harvester';
        }
        if ($code & 8) {
            $types[] = 'comment_spammer';
        }
        
        return empty($types) ? 'unknown' : $types;
    }
    
    /**
     * Check if IP is private/reserved
     */
    private function isPrivateIp($ip)
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    
    /**
     * Save cache to file
     */
    private function saveCache()
    {
        $cache_file = XOOPS_TRUST_PATH . '/cache/protector_ti_cache.php';
        $data = "<?php\n";
        $data .= "// Protector Threat Intelligence Cache\n";
        $data .= "// Generated: " . date('Y-m-d H:i:s') . "\n";
        $data .= "\$ti_cache = " . var_export($this->cache, true) . ";\n";
        
        file_put_contents($cache_file, $data);
    }
    
    /**
     * Check if current request should be checked based on configuration
     */
    public function shouldCheckCurrentRequest()
    {
        // Get current script
        $script = basename($_SERVER['SCRIPT_FILENAME']);
        
        // Check login pages
        if ($this->config['check_login'] && ($script === 'user.php' && isset($_POST['op']) && $_POST['op'] === 'login')) {
            return true;
        }
        
        // Check registration
        if ($this->config['check_register'] && ($script === 'register.php' || ($script === 'user.php' && isset($_POST['op']) && $_POST['op'] === 'register'))) {
            return true;
        }
        
        // Check admin area
        if ($this->config['check_admin'] && strpos($_SERVER['PHP_SELF'], '/admin/') !== false) {
            return true;
        }
        
        // Check form submissions
        if ($this->config['check_forms'] && !empty($_POST)) {
            return true;
        }
        
        return false;
    }
}