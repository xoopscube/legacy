<?php

/**
 * Bandwidth Limiter Filter
 *
 * This filter limits bandwidth usage to prevent DoS attacks
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class precommon_bwlimit extends ProtectorFilterAbstract
{
    /**
     * Execute bandwidth limit check
     */
    public function execute(): bool
    {
        // Get bandwidth limit file path
        $bwlimit_file = XOOPS_TRUST_PATH . '/modules/protector/configs/bwlimit.php';
        
        // Check if file exists and is readable
        if (!is_readable($bwlimit_file)) {
            return true;
        }
        
        // Get file contents
        $file_data = file_get_contents($bwlimit_file);
        
        // Check if file is valid
        if (empty($file_data) || strlen($file_data) < 6) {
            return true;
        }
        
        // Extract expiration time
        $expire_time = (int)substr($file_data, 0, 10);
        
        // Check if limit is still active
        if ($expire_time < time()) {
            return true;
        }
        
        // Get client IP
        $ip = $this->protector->remote_ip;
        
        // Check if IP is in group1
        $group1_ips = $this->protector->get_group1_ips();
        if (in_array($ip, $group1_ips, true)) {
            return true;
        }
        
        // Bandwidth limit is active - block access
        header('HTTP/1.0 503 Service unavailable');
        header('Retry-After: 600');
        
        echo '<html><head><title>503 Service Temporarily Unavailable</title></head><body><h1>Service Temporarily Unavailable</h1><p>The server is temporarily unable to service your request due to bandwidth limitations. Please try again later.</p></body></html>';
        
        exit;
    }
}