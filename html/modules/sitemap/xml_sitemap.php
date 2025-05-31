<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * 
 * @package    Sitemap
 * @version    2.5.0
 * @author     Gigamaster, 2020 XCL PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL V2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    $mainfilePath = dirname(__FILE__, 3) . '/mainfile.php';
    if (file_exists($mainfilePath)) {
        require_once $mainfilePath;
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        die("CRITICAL ERROR in modules/sitemap/xml_sitemap.php: mainfile.php not found. Path tried: " . htmlspecialchars($mainfilePath) . ". Current file: " . __FILE__);
    }
}

if (!defined('XOOPS_ROOT_PATH')) {
    header('Content-Type: text/plain; charset=utf-8');
    die("XOOPSCube Environment could not be loaded. ROOT PATH is not defined even after attempting to include mainfile.php in modules/sitemap/xml_sitemap.php.");
}

ob_start();

global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

if (!is_object($xoopsModule) || $xoopsModule->getVar('dirname') !== 'sitemap') {
    $moduleHandler = xoops_gethandler('module');
    $sitemapModuleObject = $moduleHandler->getByDirname('sitemap');
    if (is_object($sitemapModuleObject)) {
        if (!isset($xoopsModuleConfig) ||
            (is_object($xoopsModule) && $xoopsModule->getVar('mid') != $sitemapModuleObject->getVar('mid')) ||
            !is_object($xoopsModule)
        ) {
            $configHandler = xoops_gethandler('config');
            $xoopsModuleConfig = $configHandler->getConfigsByCat(0, $sitemapModuleObject->getVar('mid'));
        }
    } else {
        $buffered_output = ob_get_clean();
        header('Content-Type: text/plain; charset=utf-8');
        echo "Sitemap module object could not be loaded. Cannot proceed.\n";
        if (!empty($buffered_output)) echo "Buffered output (debug): " . htmlspecialchars($buffered_output);
        exit;
    }
}

$sitemap_configs = $xoopsModuleConfig;
if (empty($sitemap_configs)) {
    $buffered_output = ob_get_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Sitemap module configuration could not be loaded.\n";
    if (!empty($buffered_output)) echo "Buffered output (debug): " . htmlspecialchars($buffered_output);
    exit;
}
$sitemap_configs['alltime_guest'] = true;

if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}

ob_end_clean();
ob_start();

header('Content-Type:text/xml; charset=utf-8');

require_once XOOPS_ROOT_PATH . '/modules/sitemap/include/sitemap.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';

$sitemap_data_structure = sitemap_show();

$xoopsTpl = new XoopsTpl();
$xoopsTpl->assign('sitemap', $sitemap_data_structure);

$sitemapModuleForTpl = xoops_gethandler('module')->getByDirname('sitemap');
if (is_object($sitemapModuleForTpl)) {
    $xoopsTpl->assign('this', [
        'mods' => $sitemapModuleForTpl->getVar('dirname'),
        'name' => $sitemapModuleForTpl->getVar('name')
    ]);
} else {
     $xoopsTpl->assign('this', ['mods' => 'sitemap', 'name' => 'Sitemap']);
}

if (is_object(@$GLOBALS['xoopsLogger'])) {
    if (property_exists($GLOBALS['xoopsLogger'], 'activated')) {
        $GLOBALS['xoopsLogger']->activated = false;
    } elseif (method_exists($GLOBALS['xoopsLogger'], 'disable')) {
        $GLOBALS['xoopsLogger']->disable();
    } elseif (method_exists($GLOBALS['xoopsLogger'], 'stop')) {
        $GLOBALS['xoopsLogger']->stop();
    } else {
        @$GLOBALS['xoopsLogger']->activated = false;
    }
}

$xml_output = $xoopsTpl->fetch('db:xml_sitemap.html');

if (strpos(ltrim($xml_output), '<?xml') !== 0) {
    error_log("Sitemap XML Error: Template output does not start with <?xml. Actual start: " . substr(htmlspecialchars($xml_output), 0, 200));
    ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    echo "Error: Generated content is not valid XML. Please check server error logs. Problematic template output starts with:\n";
    echo htmlspecialchars(substr($xml_output, 0, 500));
    exit();
}

echo $xml_output;
ob_end_flush();
exit();
