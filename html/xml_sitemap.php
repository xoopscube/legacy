<?php
/**
 * Sitemap Root Wrapper
 *
 * This script's sole purpose is to include and execute the sitemap module's
 * xml_sitemap.php script. It ensures that the request is effectively
 * handled by the module, allowing the sitemap to be accessible from the site root.
 * @package    Sitemap
 * @version    2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */


$moduleSitemapScript = __DIR__ . '/modules/sitemap/xml_sitemap.php';

if (file_exists($moduleSitemapScript)) {
    require $moduleSitemapScript;
} else {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Sitemap Generation Error: The sitemap module's core XML script could not be found.\n";
    echo "Expected at: " . htmlspecialchars($moduleSitemapScript) . "\n";
    echo "Please check your sitemap module installation.\n";
    // Optional 404 or 500 HTTP status code
    // http_response_code(404);
}

exit;
