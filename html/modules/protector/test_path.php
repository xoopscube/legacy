<?php
/**
 * Test script to diagnose path issues
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */
require_once '../../mainfile.php';

// Output header
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Proxy Path Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .info-section { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
        .test-button { 
            display: inline-block; 
            padding: 10px 15px; 
            background: #4CAF50; 
            color: white; 
            text-decoration: none; 
            border-radius: 4px;
            margin: 5px;
        }
        .test-button:hover { background: #45a049; }
        footer {
            margin-top: 30px;
            padding: 15px;
            background: #000;
            color: #fff;
            text-align: center;
        }
        footer a { color: #fff; }
    </style>
</head>
<body>
    <h1>Proxy Path Test</h1>
    
    <div class="info-section">
        <h2>Path Analysis</h2>
        <?php
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $phpSelf = $_SERVER['PHP_SELF'] ?? '';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        echo "<p><strong>REQUEST_URI:</strong> " . htmlspecialchars($requestUri) . "</p>";
        echo "<p><strong>SCRIPT_NAME:</strong> " . htmlspecialchars($scriptName) . "</p>";
        echo "<p><strong>PHP_SELF:</strong> " . htmlspecialchars($phpSelf) . "</p>";
        echo "<p><strong>DOCUMENT_ROOT:</strong> " . htmlspecialchars($documentRoot) . "</p>";
        
        // Check for path issues
        if (strpos($requestUri, '/modules/modules/') !== false) {
            echo '<p class="error">Detected incorrect path: /modules/modules/</p>';
            echo '<p>This should be: /modules/protector/</p>';
            
            // Suggest fix
            echo '<p>Possible fix: Check your .htaccess file or server configuration for incorrect rewrite rules.</p>';
        } else {
            echo '<p class="success">Path structure appears correct.</p>';
        }
        
        // Get current module directory
        $currentDir = basename(dirname(__FILE__));
        echo "<p><strong>Current module directory:</strong> " . htmlspecialchars($currentDir) . "</p>";
        
        // Construct correct proxy URL
        $correctProxyUrl = XOOPS_URL . '/modules/' . $currentDir . '/proxy.php';
        echo "<p><strong>Correct proxy URL should be:</strong> " . htmlspecialchars($correctProxyUrl) . "</p>";
        
        // Check if proxy.php exists
        $proxyFile = dirname(__FILE__) . '/proxy.php';
        if (file_exists($proxyFile)) {
            echo '<p class="success">proxy.php file exists at the correct location.</p>';
        } else {
            echo '<p class="error">proxy.php file not found at: ' . htmlspecialchars($proxyFile) . '</p>';
        }
        
        // Check trust path
        $trustPathProxyFile = XOOPS_TRUST_PATH . '/modules/protector/proxy.php';
        if (file_exists($trustPathProxyFile)) {
            echo '<p class="success">Trust path proxy.php file exists.</p>';
        } else {
            echo '<p class="error">Trust path proxy.php file not found at: ' . htmlspecialchars($trustPathProxyFile) . '</p>';
        }
        ?>
    </div>
    
    <div class="info-section">
        <h2>Test Links</h2>
        <p>
            <a class="test-button" href="<?php echo htmlspecialchars($correctProxyUrl . '?url=' . urlencode('https://www.wikipedia.org')); ?>" target="_blank">
                Test Wikipedia via Proxy
            </a>
            
            <a class="test-button" href="<?php echo htmlspecialchars($correctProxyUrl . '?url=' . urlencode('https://example.com')); ?>" target="_blank">
                Test Example.com via Proxy
            </a>
            
            <a class="test-button" href="<?php echo htmlspecialchars($correctProxyUrl); ?>" target="_blank">
                Go to Proxy Homepage
            </a>
        </p>
    </div>
    
    <div class="info-section">
        <h2>Proxy Verification</h2>
        <p>When using the proxy, you should see a black footer at the bottom of the page with information including:</p>
        <ul>
            <li>The original URL being proxied</li>
            <li>Your IP address</li>
            <li>The proxy server name</li>
            <li>The current timestamp</li>
        </ul>
        <p>This footer confirms the content is being served through your proxy and not directly from the target site.</p>
        <p class="success">If you see this footer, your proxy is working correctly!</p>
    </div>
    
    <footer>
        <a href="<?php echo htmlspecialchars(XOOPS_URL . '/modules/' . $currentDir . '/proxy.php'); ?>">Return to Proxy</a>
    </footer>
</body>
</html>