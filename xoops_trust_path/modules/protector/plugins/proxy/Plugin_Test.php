<?php
/**
 * Test Plugin for Protector Proxy
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
 * Test plugin for proxy functionality
 */
class Plugin_Test extends ProtectorProxyPluginBase {
    /**
     * Test results storage
     */
    private $testResults = [];
    
    /**
     * Initialize plugin
     */
    public function init() {
        $this->testResults = [
            'cors' => [],
            'headers' => [],
            'agent' => [],
            'sockets' => []
        ];
        
        return true;
    }
    
    /**
     * Handle domain - this plugin handles test domains only
     */
    public function handleDomain($url) {
        // Debug: Log that handleDomain was called
        error_log("Plugin_Test::handleDomain called for URL: " . $url);
        
        // Only handle test URLs
        if (strpos($url, 'test.proxy') !== false) {
            error_log("Plugin_Test: Generating test response for " . $url);
            return $this->generateTestResponse($url);
        }
        
        // For other URLs, run tests but don't handle the request
        error_log("Plugin_Test: Running tests for " . $url);
        $this->runTests($url);
        
        return false;
    }
    
    /**
     * Pre-process URL
     */
    public function preProcess($url) {
        // Debug: Log that preProcess was called
        error_log("Plugin_Test::preProcess called for URL: " . $url);
        
        // Check for URL path issues
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($requestUri, '/modules/modules/') !== false) {
            error_log("Plugin_Test: Detected incorrect path '/modules/modules/' in: " . $requestUri);
            
            // Log server variables for debugging
            error_log("Plugin_Test: SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'not set'));
            error_log("Plugin_Test: SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'not set'));
            error_log("Plugin_Test: PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'not set'));
        }
        
        return $url;
    }
    
    /**
     * Post-process content
     */
    public function postProcess($content, $url) {
        // Debug: Log that postProcess was called
        error_log("Plugin_Test::postProcess called for URL: " . $url);
        
        // If this is an HTML response, add a debug banner
        if (strpos($content, '<!DOCTYPE html') !== false || strpos($content, '<html') !== false) {
            $debugInfo = '<div style="position:fixed; bottom:0; left:0; right:0; background:#333; padding:10px; border-top:2px solid #ff9900; z-index:9999;">';
            $debugInfo .= '<strong>Proxy Debug:</strong> ';
            $debugInfo .= 'URL: ' . htmlspecialchars($url) . '<br>';
            $debugInfo .= 'Request URI: ' . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'not set') . '<br>';
            $debugInfo .= 'Script Name: ' . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'not set') . '<br>';
            $debugInfo .= 'Correct path should be: /modules/protector/proxy.php<br>';
            $debugInfo .= '<button onclick="this.parentNode.style.display=\'none\';">Close</button>';
            $debugInfo .= '</div>';
            
            // Insert before closing body tag
            $content = str_replace('</body>', $debugInfo . '</body>', $content);
        }
        
        return $content;
    }
    
    /**
     * Run tests on URL
     */
    private function runTests($url) {
        // Test 1: CORS Headers
        $this->testCORS($url);
        
        // Test 2: Request Headers
        $this->testHeaders($url);
        
        // Test 3: User Agent
        $this->testUserAgent($url);
        
        // Test 4: Socket Connection
        $this->testSockets($url);
        
        // Log test results
        $this->logTestResults($url);
    }
    
    /**
     * Test CORS functionality
     */
    private function testCORS($url) {
        $this->testResults['cors'] = [
            'test_name' => 'CORS Headers Test',
            'description' => 'Testing if CORS headers are properly set',
            'headers_set' => [
                'Access-Control-Allow-Origin: *',
                'Access-Control-Allow-Methods: GET, POST, OPTIONS',
                'Access-Control-Allow-Headers: Content-Type, Authorization'
            ],
            'status' => 'Headers will be set after content is fetched'
        ];
    }
    
    /**
     * Test request headers
     */
    private function testHeaders($url) {
        // Parse URL
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';
        
        // Get headers that will be sent
        $headers = [
            'User-Agent: ' . ($this->proxy->getConfig()['user_agent'] ?? 'Unknown'),
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Host: ' . $host
        ];
        
        $this->testResults['headers'] = [
            'test_name' => 'Request Headers Test',
            'description' => 'Testing request headers that will be sent',
            'headers' => $headers,
            'status' => 'Headers prepared for request'
        ];
    }
    
    /**
     * Test user agent
     */
    private function testUserAgent($url) {
        $config = $this->proxy->getConfig();
        $userAgent = $config['user_agent'] ?? 'Unknown';
        
        $this->testResults['agent'] = [
            'test_name' => 'User Agent Test',
            'description' => 'Testing if user agent is properly configured',
            'configured_agent' => $userAgent,
            'status' => !empty($userAgent) ? 'User agent configured' : 'User agent not configured'
        ];
    }
    
    /**
     * Test socket connection
     */
    private function testSockets($url) {
        // Parse URL
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) {
            $this->testResults['sockets'] = [
                'test_name' => 'Socket Connection Test',
                'description' => 'Testing direct socket connection to host',
                'status' => 'Failed - Invalid URL format',
                'error' => 'Could not parse URL'
            ];
            return;
        }
        
        $scheme = isset($parsedUrl['scheme']) ? strtolower($parsedUrl['scheme']) : 'http';
        $port = isset($parsedUrl['port']) ? $parsedUrl['port'] : ($scheme === 'https' ? 443 : 80);
        $host = $parsedUrl['host'] ?? '';
        
        if (empty($host)) {
            $this->testResults['sockets'] = [
                'test_name' => 'Socket Connection Test',
                'description' => 'Testing direct socket connection to host',
                'status' => 'Failed - No host in URL',
                'error' => 'No host specified in URL'
            ];
            return;
        }
        
        // Try socket connection
        $socketUrl = ($scheme === 'https' ? 'ssl://' : '') . $host;
        $fp = @fsockopen($socketUrl, $port, $errno, $errstr, 5);
        
        if (!$fp) {
            $this->testResults['sockets'] = [
                'test_name' => 'Socket Connection Test',
                'description' => 'Testing direct socket connection to host',
                'status' => 'Failed - Could not connect',
                'error' => "$errstr ($errno)",
                'host' => $host,
                'port' => $port,
                'scheme' => $scheme
            ];
        } else {
            fclose($fp);
            $this->testResults['sockets'] = [
                'test_name' => 'Socket Connection Test',
                'description' => 'Testing direct socket connection to host',
                'status' => 'Success - Connection established',
                'host' => $host,
                'port' => $port,
                'scheme' => $scheme
            ];
        }
    }
    
    /**
     * Log test results
     */
    private function logTestResults($url) {
        // Convert test results to string for logging
        $resultsStr = "Test results for URL: $url\n";
        
        foreach ($this->testResults as $testType => $results) {
            $resultsStr .= "\n=== $testType Test ===\n";
            foreach ($results as $key => $value) {
                if (is_array($value)) {
                    $resultsStr .= "$key:\n";
                    foreach ($value as $subValue) {
                        $resultsStr .= "  - $subValue\n";
                    }
                } else {
                    $resultsStr .= "$key: $value\n";
                }
            }
        }
        
        // Log to file for debugging
        $logFile = XOOPS_TRUST_PATH . '/modules/protector/cache/proxy_test_' . time() . '.log';
        file_put_contents($logFile, $resultsStr);
    }
    
    /**
     * Generate test response HTML
     */
    private function generateTestResponse($url) {
        // Run tests first
        $this->runTests($url);
        
        // Generate HTML response
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Protector Proxy Test Results</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .test-section { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .test-section h2 { margin-top: 0; color: #444; }
        .success { color: green; }
        .failure { color: red; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Protector Proxy Test Results</h1>
    <p>URL tested: ' . htmlspecialchars($url) . '</p>';
        
        foreach ($this->testResults as $testType => $results) {
            $html .= '<div class="test-section">
        <h2>' . ucfirst($testType) . ' Test</h2>
        <p><strong>Test Name:</strong> ' . htmlspecialchars($results['test_name']) . '</p>
        <p><strong>Description:</strong> ' . htmlspecialchars($results['description']) . '</p>
        <p><strong>Status:</strong> <span class="' . (strpos($results['status'], 'Success') !== false ? 'success' : 'failure') . '">' . 
            htmlspecialchars($results['status']) . '</span></p>';
            
            // Add specific details for each test type
            if ($testType === 'headers' && isset($results['headers'])) {
                $html .= '<h3>Headers:</h3><ul>';
                foreach ($results['headers'] as $header) {
                    $html .= '<li>' . htmlspecialchars($header) . '</li>';
                }
                $html .= '</ul>';
            } elseif ($testType === 'agent' && isset($results['configured_agent'])) {
                $html .= '<p><strong>Configured User Agent:</strong> ' . htmlspecialchars($results['configured_agent']) . '</p>';
            } elseif ($testType === 'sockets') {
                if (isset($results['error'])) {
                    $html .= '<p><strong>Error:</strong> ' . htmlspecialchars($results['error']) . '</p>';
                }
                if (isset($results['host'])) {
                    $html .= '<p><strong>Host:</strong> ' . htmlspecialchars($results['host']) . '</p>';
                    $html .= '<p><strong>Port:</strong> ' . htmlspecialchars($results['port']) . '</p>';
                    $html .= '<p><strong>Scheme:</strong> ' . htmlspecialchars($results['scheme']) . '</p>';
                }
            }
            
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        
        return $html;
    }
}