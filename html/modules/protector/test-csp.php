<?php
/**
 * CSP Violation Test Page
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include mainfile.php for XOOPS environment
require_once '../../mainfile.php';

// Don't use XOOPS header/footer which might interfere with testing
// Instead, output a simple HTML page with CSP violations

// Set a strict CSP header for testing
header("Content-Security-Policy: default-src 'self';  style-src 'self' 'unsafe-inline'; script-src 'self'; report-uri /modules/protector/csp-report.php");

// Create log directory if it doesn't exist (for debugging)
$log_dir = XOOPS_CACHE_PATH . '/protector/logs';
if (!is_dir($log_dir)) {
    @mkdir($log_dir, 0755, true);
}

// Log that the test page was accessed
$log_file = $log_dir . '/csp_test.log';
$log_entry = date('Y-m-d H:i:s') . ' - Test page accessed from ' . $_SERVER['REMOTE_ADDR'] . "\n";
@file_put_contents($log_file, $log_entry, FILE_APPEND);

// Output HTML directly
?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSP Violation Test</title>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="../../common/css/x-layout.css">
    <style>
    body{background: #212325;
    ;}
    body {    
        background: #212325;
        color: #abc;
        font: normal 16px / 2 system-ui, sans-serif;
        position        : relative; /* for SCROLL-TO-TOP */
        margin          : 0 auto 2rem;
        box-sizing: border-box;
    }
    a:link, a:visited{
        color: blueviolet;
    border: 1px solid blueviolet;
    border-radius: 0.5rem;
    padding: 0.5rem 1.5rem;
    text-decoration: none;
    }
    a:hover{
        color:azure
    }
    </style>
</head>
<body>
    <div data-layout="column mx-auto" data-self="size-large">
    <h2>CSP Violation Test</h2>
    <p>This page will intentionally trigger a CSP violation report.</p>
    
    <!-- This inline script will violate the CSP policy -->
    <script>
        // This will trigger a CSP violation because inline scripts are not allowed
        console.log('This script violates CSP');
        alert('If you see this alert, CSP is not working properly!');
    </script>

    <!-- This external script will also violate the CSP policy -->
    <script src="https://example.com/external-script.js"></script>

    <p>Check your CSP violation logs to see if the reports were received.</p>
    <p>If CSP is working correctly:</p>
    <ul>
        <li>You should NOT see an alert popup</li>
        <li>The browser console should show CSP violation errors</li>
        <li>Violations should be logged in <code>XOOPS_CACHE_PATH/protector/logs/csp_violations.log</code></li>
        <li>Violations should appear in the Protector admin interface</li>
    </ul>

    <p><a href="<?php echo XOOPS_URL; ?>">Return to homepage</a></p>
    </div>
</body>
</html>
<?php
exit;
?>