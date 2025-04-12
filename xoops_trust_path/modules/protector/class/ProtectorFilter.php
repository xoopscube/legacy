<?php

/**
 * Protector Filter Handler for XCL
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class ProtectorFilterHandler
{
    private array $filters = [];
    private static ?ProtectorFilterHandler $instance = null;

    /**
     * Private constructor for singleton pattern
     */
    private function __construct()
    {
        // Load all filters
        $this->loadFilters();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load all filter classes
     */
    private function loadFilters(): void
    {
        $filter_dir = dirname(__DIR__) . '/filters';
        
        if (!is_dir($filter_dir)) {
            return;
        }
        
        $handler = opendir($filter_dir);
        if (!$handler) {
            return;
        }
        
        while (($file = readdir($handler)) !== false) {
            if (str_ends_with($file, '.php')) {
                include_once $filter_dir . '/' . $file;
                $filter_name = substr($file, 0, -4);
                
                // Create filter instance if class exists
                if (class_exists($filter_name)) {
                    $this->filters[$filter_name] = new $filter_name();
                }
            }
        }
        closedir($handler);
    }

    /**
     * Execute a specific filter type
     */
    public function execute(string $type): bool
    {
        $ret = true;
        
        foreach ($this->filters as $filter) {
            if (method_exists($filter, $type)) {
                try {
                    $ret_filter = $filter->$type();
                    if ($ret_filter === false) {
                        $ret = false;
                    }
                } catch (\Exception $e) {
                    error_log('Protector filter error in ' . get_class($filter) . "::$type - " . $e->getMessage());
                }
            }
        }
        
        return $ret;
    }
}

/**
 * Abstract base class for all filters
 */
abstract class ProtectorFilterAbstract
{
    protected protector $protector;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->protector = protector::getInstance();
    }
    
    /**
     * Get config value with default fallback
     */
    protected function getConf(string $key, $default = null)
    {
        $conf = $this->protector->getConf();
        return $conf[$key] ?? $default;
    }
    
    /**
     * Log message
     */
    protected function logMessage(string $message, int $uid = 0, bool $unique = false, int $level = 1): bool
    {
        $this->protector->message .= $message . "\n";
        return $this->protector->output_log(get_class($this), $uid, $unique, $level);
    }
    
    /**
     * Register bad IP
     */
    protected function registerBadIp(int $jailed_time = 0, ?string $ip = null): bool
    {
        return $this->protector->register_bad_ips($jailed_time, $ip);
    }
    
    /**
     * Deny by htaccess
     */
    protected function denyByHtaccess(?string $ip = null): bool
    {
        return $this->protector->deny_by_htaccess($ip);
    }
    
    /**
     * Purge sessions
     */
    protected function purge(bool $redirect_to_top = false): void
    {
        $this->protector->purge($redirect_to_top);
    }
}
