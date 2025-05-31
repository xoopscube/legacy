<?php
/**
 * Sitemap Module - Admin Overview Page
 * @package    Sitemap
 * @version    2.5.0
 * @author     gigamaster 2020 XCL/PHP7
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname(__FILE__, 4) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

global $xoopsUser, $xoopsModule, $xoopsConfig, $xoopsTpl, $xoopsModuleConfig;

if (!is_object($xoopsUser) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

$myts = MyTextSanitizer::getInstance();
$sitemap_configs = $xoopsModuleConfig;

$sitemapHealth = [
    'xml_sitemap_url' => '',
    'last_generated_date' => 'N/A',
    'url_count' => 0,
    'xml_load_status' => false,
    'xml_load_message' => '',
    'robots_exists' => false,
    'robots_sitemap_directive_found' => false,
    'robots_sitemap_directive_correct' => false,
    'robots_sitemap_directive_url' => ''
];

// Sitemap URL check in admin page
$sitemapModulePhysicalPath = XOOPS_MODULE_PATH . '/sitemap/xml_sitemap.php';
$sitemapModuleUrl = XOOPS_URL . '/modules/sitemap/xml_sitemap.php';
$sitemapRootPhysicalPath = XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'xml_sitemap.php';
$sitemapRootUrl = XOOPS_URL . '/xml_sitemap.php';

$actualSitemapPhysicalPathForMtime = '';
$urlToFetchForValidation = '';

if (file_exists($sitemapRootPhysicalPath)) {
    $urlToFetchForValidation = $sitemapRootUrl;
    $actualSitemapPhysicalPathForMtime = $sitemapRootPhysicalPath;
} else {
    $urlToFetchForValidation = $sitemapModuleUrl;
    $actualSitemapPhysicalPathForMtime = $sitemapModulePhysicalPath;
}
$sitemapHealth['xml_sitemap_url'] = $urlToFetchForValidation;

// Last Generated Date
if (!empty($actualSitemapPhysicalPathForMtime) && file_exists($actualSitemapPhysicalPathForMtime)) {
    $sitemapHealth['last_generated_date'] = formatTimestamp(filemtime($actualSitemapPhysicalPathForMtime), 'l');
} else {
    $sitemapHealth['last_generated_date'] = defined('_AD_SITEMAP_HEALTH_SITEMAP_NOT_FOUND') ? _AD_SITEMAP_HEALTH_SITEMAP_NOT_FOUND : 'Sitemap file not found';
}

// Fetch and Parse XML Sitemap, URL Count and Validation
$xmlContent = '';
if (!empty($urlToFetchForValidation)) {
    error_log("Sitemap Admin: Attempting to fetch XML via file_get_contents from URL: " . $urlToFetchForValidation);
    
    $context_options = [
        'ssl' => [ // HTTPS requests, local/dev
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ],
        'http' => [ // HTTP context
            'method' => "GET",
            'header' => "User-Agent: XOOPS/XCL Sitemap Admin Checker/1.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)\r\n" . // Try a common bot User-Agent
                        "Accept: application/xml,text/xml,*/*;q=0.8\r\n",
            'timeout' => 20, // timeout
            'follow_location' => 1, // redirects
            'max_redirects' => 5
        ]
    ];
    $stream_context = stream_context_create($context_options);
    
    if (function_exists('error_clear_last')) {
        error_clear_last();
    }

    $fetchedContent = @file_get_contents($urlToFetchForValidation, false, $stream_context);

    if ($fetchedContent === false) {
        $last_error = error_get_last();
        $error_detail = $last_error ? " (PHP Error: " . htmlspecialchars($last_error['message']) . " in " . htmlspecialchars(basename($last_error['file'])) . " on line " . $last_error['line'] . ")" : " (Unknown fetch error)";
        $sitemapHealth['xml_load_message'] = (defined('_AD_SITEMAP_HEALTH_XML_FETCH_ERROR') ? _AD_SITEMAP_HEALTH_XML_FETCH_ERROR : 'Could not fetch XML content') . $error_detail;
        error_log("Sitemap Admin: file_get_contents FAILED for URL: " . $urlToFetchForValidation . $error_detail);
    } else {
        $xmlContent = $fetchedContent;
        error_log("Sitemap Admin: Content fetched via file_get_contents from " . $urlToFetchForValidation . " (first 1000 chars): " . substr(htmlspecialchars($xmlContent), 0, 1000));
    }
} else {
    $sitemapHealth['xml_load_message'] = _AD_SITEMAP_HEALTH_XML_INVALID;
}

// XML Parsing logic
$dom = new DOMDocument;
$dom->preserveWhiteSpace = false;

if (!empty($xmlContent)) {
    libxml_use_internal_errors(true); // XML parsing errors
    if ($dom->loadXML($xmlContent)) {
        if ($dom->documentElement && $dom->documentElement->tagName === 'urlset') {
            $sitemapHealth['xml_load_status'] = true;
            $sitemapHealth['xml_load_message'] = defined('_AD_SITEMAP_HEALTH_XML_VALID') ? _AD_SITEMAP_HEALTH_XML_VALID : 'XML appears well-formed.';
            $sitemapHealth['url_count'] = $dom->getElementsByTagName('url')->length;
        } else {
            $sitemapHealth['xml_load_status'] = false;
            $sitemapHealth['xml_load_message'] = _AD_SITEMAP_HEALTH_XML_FETCH_ERROR . " Found: " . ($dom->documentElement ? htmlspecialchars($dom->documentElement->tagName) : 'None');
            error_log("Sitemap Admin: XML valid but wrong root element. Content (first 500): " . substr(htmlspecialchars($xmlContent), 0, 500));
        }
    } else {
        $sitemapHealth['xml_load_status'] = false;
        $sitemapHealth['xml_load_message'] = _AD_SITEMAP_HEALTH_XML_INVALID;
        $xmlErrors = libxml_get_errors();
        foreach ($xmlErrors as $error) {
            $sitemapHealth['xml_load_message'] .= "<br>- Error [{$error->code}] (Line: {$error->line}, Col: {$error->column}): " . htmlspecialchars(trim($error->message), ENT_QUOTES);
        }
        libxml_clear_errors();
        error_log("Sitemap Admin: XML parsing FAILED. Content received (first 1000 chars): " . substr(htmlspecialchars($xmlContent), 0, 1000));
    }
    libxml_use_internal_errors(false);
} elseif (empty($sitemapHealth['xml_load_message'])) { // fetch fail
    $sitemapHealth['xml_load_message'] = _AD_SITEMAP_HEALTH_XML_EMPTY_CONTENT;
}


// Robots.txt Status
$robotsFilePath = XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'robots.txt';
if (file_exists($robotsFilePath)) {
    $sitemapHealth['robots_exists'] = true;
    $robotsContent = @file_get_contents($robotsFilePath);
    if ($robotsContent) {

        $normalizedExpectedSitemapUrlInRobots = rtrim($urlToFetchForValidation, '/');

        if (preg_match('/^Sitemap:\s*(.*)$/im', $robotsContent, $matches)) {
            $sitemapHealth['robots_sitemap_directive_found'] = true;
            $sitemapHealth['robots_sitemap_directive_url'] = trim($matches[1]);
            $normalizedFoundSitemapUrlInRobots = rtrim($sitemapHealth['robots_sitemap_directive_url'], '/');
            if (strtolower($normalizedFoundSitemapUrlInRobots) === strtolower($normalizedExpectedSitemapUrlInRobots)) {
                $sitemapHealth['robots_sitemap_directive_correct'] = true;
            }
        }
    }
}

// Preview Data
$name = $xoopsModule->getVar('name');
$mid  = $xoopsModule->getVar('mid');

$dash = XOOPS_URL . '/admin.php';
$pref = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id='.$mid;
$help = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&amp;dirname=sitemap';
$robotsEditorUrl = XOOPS_MODULE_URL . '/sitemap/admin/robots_editor.php';

$show_sitename = '';
if(!empty($sitemap_configs['show_sitename'])) {
	$show_sitename = $myts->makeTboxData4Show($xoopsConfig['sitename']);
}
$show_slogan = '';
if(!empty($sitemap_configs['show_siteslogan'])) {
	$show_slogan   = $myts->makeTboxData4Show($xoopsConfig['slogan']);
}
$show_map = '';
if(!empty($sitemap_configs['show_site_map'])) {
	$show_map   = $myts->displayTarea( $sitemap_configs['show_map'] , 1 ) ;
}
$show_address = '';
if(!empty($sitemap_configs['show_site_address'])) {
	$show_address   = $myts->displayTarea( $sitemap_configs['show_address'] , 1 ) ;
}

$human_xml_preview = '';
$machine_xml_preview = '';
// Display preview based on $xmlContent
if (!empty($xmlContent)) {
    if ($sitemapHealth['xml_load_status']) {
        $formatted_xml_for_preview = $dom->saveXML();
        $human_xml_preview = '<pre style="max-height:40vh"><code class="language-xml">'. htmlspecialchars($formatted_xml_for_preview, ENT_QUOTES).'</code></pre>';
        $machine_xml_preview = '<pre style="max-height:40vh"><code class="language-xml">'. htmlspecialchars($xmlContent, ENT_QUOTES).'</code></pre>';
    } else {
        $human_xml_preview = '<pre style="max-height:40vh"><code class="language-html">'. htmlspecialchars($xmlContent, ENT_QUOTES).'</code></pre>';
        $machine_xml_preview = $human_xml_preview;
    }
} else {
    $human_xml_preview = '<p>' . _AD_SITEMAP_HEALTH_XML_PREVIEW_UNAVAILABLE . '</p>';
    $machine_xml_preview = $human_xml_preview;
}

// RENDER New Template
$xoopsTpl = new XoopsTpl();

// Assign to Smarty
$xoopsTpl->assign('xoops_pagetitle', _MI_SITEMAP_ADMENU_OVERVIEW);
$xoopsTpl->assign('sitemap_module_name', $name);
$xoopsTpl->assign('sitemap_health', $sitemapHealth);
$xoopsTpl->assign('sitemap_dash_url', $dash);
$xoopsTpl->assign('sitemap_pref_url', $pref);
$xoopsTpl->assign('sitemap_help_url', $help);
$xoopsTpl->assign('sitemap_robots_editor_url', $robotsEditorUrl);
$xoopsTpl->assign('sitemap_show_sitename', $show_sitename);
$xoopsTpl->assign('sitemap_show_slogan', $show_slogan);
$xoopsTpl->assign('sitemap_show_map', $show_map);
$xoopsTpl->assign('sitemap_show_address', $show_address);
$xoopsTpl->assign('sitemap_human_xml_preview', $human_xml_preview);
$xoopsTpl->assign('sitemap_machine_xml_preview', $machine_xml_preview);


// This MUST set admin/templates path
$xoopsTpl->display(XOOPS_MODULE_PATH.'/sitemap/admin/templates/sitemap_admin_overview.html' );

require_once XOOPS_ROOT_PATH . '/footer.php';

