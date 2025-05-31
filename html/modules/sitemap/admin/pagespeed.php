<?php
/**
 * Sitemap Module - Admin Pagespeed
 * @package    Sitemap
 * @version    2.5.0
 * @author     gigamaster 2020 XCL/PHP7
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname(__FILE__, 4) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

global $xoopsUser, $xoopsModule, $xoopsConfig, $xoopsTpl, $xoopsModuleConfig;

// Security: Ensure user is admin
if (!is_object($xoopsUser) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

$root = XCube_Root::getSingleton();
if (is_object($root->mLanguageManager) && is_object($xoopsModule)) {
    $root->mLanguageManager->loadModuleMessageCatalog($xoopsModule->getVar('dirname')); // _MI_
    $root->mLanguageManager->loadModuleAdminMessageCatalog($xoopsModule->getVar('dirname')); // _AD_
}

$myts = MyTextSanitizer::getInstance();
$sitemap_configs = $xoopsModuleConfig;

$pageSpeedData = [
    'apiKeyUsed' => false,
    'warning' => '',
    'scores' => null,
    'error' => null,
    'url_tested' => '',
    'strategy' => 'mobile', // Default strategy
    'show_results' => false // Flag to shown results
];

// Get URL to test from GET or POST, default to homepage
$url_to_test_input = $_REQUEST['psi_url'] ?? XOOPS_URL; // Use $_REQUEST to catch both GET and POST
$pageSpeedData['url_tested'] = filter_var(trim($url_to_test_input), FILTER_SANITIZE_URL);

// Get strategy from GET or POST, default to mobile
$strategy_input = strtolower($_REQUEST['psi_strategy'] ?? 'mobile');
if (in_array($strategy_input, ['mobile', 'desktop'])) {
    $pageSpeedData['strategy'] = $strategy_input;
}

// Check if form submit or check trigger
if (isset($_REQUEST['psi_check'])) {
    $pageSpeedData['show_results'] = true;

    if (empty($pageSpeedData['url_tested']) || !filter_var($pageSpeedData['url_tested'], FILTER_VALIDATE_URL)) {
        $pageSpeedData['error'] = _AD_SITEMAP_PAGESPEED_INVALID_URL;
        $pageSpeedData['scores'] = null;
    } else {
        $pageSpeedApiKey = $sitemap_configs['pagespeed_api_key'] ?? '';

        if (empty($pageSpeedApiKey)) {
            $pageSpeedData['warning'] = _AD_SITEMAP_PAGESPEED_NO_KEY_WARNING;
        } else {
            $pageSpeedData['apiKeyUsed'] = true;
        }

        $psiApiEndpoint = "https://www.googleapis.com/pagespeedonline/v5/runPagespeed";
        $psiRequestUrl = $psiApiEndpoint . "?url=" . urlencode($pageSpeedData['url_tested']) . "&strategy=" . $pageSpeedData['strategy'];
        if ($pageSpeedData['apiKeyUsed']) {
            $psiRequestUrl .= "&key=" . urlencode($pageSpeedApiKey);
        }

        error_log("Sitemap Admin (PageSpeed Page): Attempting to fetch PageSpeed data from: " . $psiRequestUrl);

        $psi_context_options = [
            'http' => [
                'method' => "GET",
                'ignore_errors' => true,
                'timeout' => 60, // PageSpeed API can be very slow :(
                'header' => "User-Agent: XOOPS/XCL Sitemap PageSpeed Checker/1.1\r\n" .
                            "Accept: application/json\r\n"
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $psi_stream_context = stream_context_create($psi_context_options);
        $psiResponseJson = @file_get_contents($psiRequestUrl, false, $psi_stream_context);

        if ($psiResponseJson === false) {
            $last_psi_error = error_get_last();
            $pageSpeedData['error'] =  _AD_SITEMAP_PAGESPEED_FETCH_ERROR . ($last_psi_error ? ' PHP Error: ' . htmlspecialchars($last_psi_error['message']) : '');
            error_log("Sitemap Admin (PageSpeed Page): PageSpeed file_get_contents FAILED. " . ($last_psi_error ? ' PHP Error: ' . $last_psi_error['message'] : ''));
        } else {
            $psiResponseData = json_decode($psiResponseJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $pageSpeedData['error'] = _AD_SITEMAP_PAGESPEED_JSON_ERROR;
                error_log("Sitemap Admin (PageSpeed Page): PageSpeed JSON decode error. Response: " . substr($psiResponseJson, 0, 500));
            } elseif (isset($psiResponseData['error'])) {
                $pageSpeedData['error'] = _AD_SITEMAP_PAGESPEED_API_ERROR . ($psiResponseData['error']['message'] ?? 'Unknown API error.');
                error_log("Sitemap Admin (PageSpeed Page): PageSpeed API returned an error: " . ($psiResponseData['error']['message'] ?? 'Unknown API error.'));
            } elseif (isset($psiResponseData['lighthouseResult']['categories'])) {
                $pageSpeedData['scores'] = [];
                foreach ($psiResponseData['lighthouseResult']['categories'] as $categoryName => $categoryData) {
                    if (isset($categoryData['score']) && is_numeric($categoryData['score'])) {
                        $title = $categoryData['title'] ?? ucfirst($categoryName);
                        $pageSpeedData['scores'][$title] = [
                            'score' => round($categoryData['score'] * 100),
                            'id' => $categoryData['id'] ?? $categoryName // Store ID for report details
                        ];
                    }
                }
                if(empty($pageSpeedData['scores'])){
                    $pageSpeedData['error'] = _AD_SITEMAP_PAGESPEED_NO_SCORES;
                    error_log("Sitemap Admin (PageSpeed Page): PageSpeed response parsed, but no scores found in categories. Full Response: " . print_r($psiResponseData, true));
                }
                // link to the full report
                if (isset($psiResponseData['lighthouseResult']['finalUrl'])) {
                     $pageSpeedData['report_url'] = 'https://pagespeed.web.dev/report?url=' . urlencode($psiResponseData['lighthouseResult']['finalUrl']);
                } elseif (isset($psiResponseData['id'])) {
                     $pageSpeedData['report_url'] = 'https://pagespeed.web.dev/report?url=' . urlencode($psiResponseData['id']);
                }


            } else {
                $pageSpeedData['error'] = _AD_SITEMAP_PAGESPEED_UNEXPECTED_RESPONSE;
                error_log("Sitemap Admin (PageSpeed Page): PageSpeed unexpected response structure. Full Response: " . print_r($psiResponseData, true));
            }
        }
    }
}

// Assign data to Smarty
$xoopsTpl->assign('sitemap_pagespeed_data', $pageSpeedData);
$xoopsTpl->assign('xoops_pagetitle', _AD_SITEMAP_PAGESPEED_PAGE_TITLE . ' - ' . $xoopsModule->getVar('name'));

// WE MUST SET MODULE 'admin/templates' path
$xoopsTpl->display(XOOPS_MODULE_PATH.'/sitemap/admin/templates/sitemap_admin_pagespeed.html' );

require_once XOOPS_ROOT_PATH . '/footer.php';