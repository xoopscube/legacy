<?php
/**
 * Sitemap Module - obots.txt Editor Admin Page
 * @package    Sitemap
 * @version    2.5.0
 * @author     gigamaster 2020 XCL/PHP7
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname(__FILE__, 4) . '/mainfile.php'; // Corrected path
require_once XOOPS_ROOT_PATH . '/header.php';

global $xoopsUser, $xoopsModule, $xoopsConfig, $xoopsTpl, $xoopsModuleConfig;

if (!is_object($xoopsUser) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

$root =& XCube_Root::getSingleton();
if (is_object($root->mLanguageManager) && is_object($xoopsModule)) {
    $root->mLanguageManager->loadModinfoMessageCatalog($xoopsModule->getVar('dirname'));
    // specific language constants in admin.php
    // $root->mLanguageManager->loadModuleAdminMessageCatalog($xoopsModule->getVar('dirname'));
}


$myts = MyTextSanitizer::getInstance();
$robotsFilePath = XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'robots.txt'; // Use DIRECTORY_SEPARATOR for cross-platform compatibility
$messages = [];
$currentRobotsContentForDisplay = '';
$formContent = '';

// Function to generate recommended robots.txt content
function sitemap_generate_recommended_robots($sitemapUrl) {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n\n";
    $content .= "# Common XOOPS/XCL paths to disallow\n";
    $content .= "Disallow: /admin.php\n";
    $content .= "Disallow: /userinfo.php\n";
    $content .= "Disallow: /edituser.php\n";
    $content .= "Disallow: /include/\n";
    $content .= "Disallow: /class/\n";
    $content .= "Disallow: /kernel/\n";
    $content .= "Disallow: /language/\n";
    $content .= "Disallow: /libraries/\n";
    $content .= "Disallow: /Frameworks/\n";
    $content .= "Disallow: /plugins/\n";
    $content .= "Disallow: /install/\n";
    $content .= "Disallow: /sql/\n";
    $content .= "Disallow: /templates_c/\n";
    $content .= "Disallow: /cache/\n";
    $content .= "Disallow: /data/\n";
    $content .= "Disallow: /temp/\n";
    $content .= "Disallow: /uploads/\n";
    $content .= "\n";
    $content .= "# Sitemap URL (ensure this is the correct absolute URL)\n";
    $content .= "Sitemap: " . htmlspecialchars($sitemapUrl, ENT_QUOTES) . "\n";
    return $content;
}

$sitemapModuleUrl = XOOPS_URL . '/modules/sitemap/xml_sitemap.php';
$sitemapUrl = $sitemapModuleUrl;

if (file_exists(XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'xml_sitemap.php')) {
    $rootSitemapContent = @file_get_contents(XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'xml_sitemap.php');
    if ($rootSitemapContent && strpos($rootSitemapContent, 'SITEMAP_ROOT_CONTROLLER_LOADED') !== false) {
        $sitemapUrl = XOOPS_URL . '/xml_sitemap.php';
    }
}
$recommendedRobotsContent = sitemap_generate_recommended_robots($sitemapUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header(XOOPS_URL . '/modules/sitemap/admin/robots_editor.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }

    if (isset($_POST['save_robots'])) {
        $newContent = $_POST['robots_content'] ?? '';
        $newContent = str_replace("\r\n", "\n", $newContent);
        $newContent = rtrim($newContent) . "\n";

        if (@file_put_contents($robotsFilePath, $newContent) !== false) {
            $messages[] = ['type' => 'success', 'text' => _MI_SITEMAP_ROBOTS_SAVE_SUCCESS];
        } else {
            $error_permission = false;
            if (!file_exists($robotsFilePath)) {
                if (!is_writable(XOOPS_ROOT_PATH)) {
                    $error_permission = true;
                }
            } else {
                if (!is_writable($robotsFilePath)) {
                    $error_permission = true;
                }
            }
            if ($error_permission) {
                 $messages[] = ['type' => 'error', 'text' => _MI_SITEMAP_ROBOTS_SAVE_ERROR_PERMISSION];
            } else {
                 $messages[] = ['type' => 'error', 'text' => _MI_SITEMAP_ROBOTS_SAVE_ERROR_GENERAL . " (Path: " . $robotsFilePath .")"];
            }
        }
    } elseif (isset($_POST['load_recommended'])) {
        // handled by JS, but POST can be a fallback
        $formContent = $recommendedRobotsContent;
    }
}

// Read current robots.txt content
clearstatcache(true, $robotsFilePath); // Clear file status cache before checking
if (file_exists($robotsFilePath)) {
    if (is_readable($robotsFilePath)) {
        $currentRobotsContentForDisplay = @file_get_contents($robotsFilePath);
        if ($formContent === '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $formContent = $currentRobotsContentForDisplay;
        }
    } else {
        $messages[] = ['type' => 'error', 'text' => _MI_SITEMAP_ROBOTS_READ_ERROR . " (Check permissions on " . $robotsFilePath . ")"];
    }
} else {
    $messages[] = ['type' => 'info', 'text' => _MI_SITEMAP_ROBOTS_FILE_NOT_EXIST];
    if ($formContent === '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        $formContent = $recommendedRobotsContent;
    }
}

$xoopsTpl = new XoopsTpl();
// Assign to Smarty
$xoopsTpl->assign('xoops_pagetitle', _MI_SITEMAP_ROBOTS_TITLE . ' - ' . $xoopsModule->getVar('name'));
$xoopsTpl->assign('sitemap_messages', $messages);
$xoopsTpl->assign('sitemap_current_robots_content_display', $myts->makeTboxData4Show($currentRobotsContentForDisplay));
$xoopsTpl->assign('sitemap_form_content', $myts->makeTareaData4Edit($formContent));
$xoopsTpl->assign('sitemap_recommended_robots_content_js', json_encode($recommendedRobotsContent)); // json_encode handles escaping for JS
$xoopsTpl->assign('sitemap_warning_overwrite', _MI_SITEMAP_ROBOTS_WARNING_OVERWRITE);
$xoopsTpl->assign('sitemap_page_title', _MI_SITEMAP_ROBOTS_TITLE);
$xoopsTpl->assign('sitemap_page_desc', _MI_SITEMAP_ROBOTS_DESC);

// Token for the form 
$xoopsTpl->assign('xoops_token_form', $GLOBALS['xoopsSecurity']->getTokenHTML());

// This MUST set admin/templates path
$xoopsTpl->display(XOOPS_MODULE_PATH.'/sitemap/admin/templates/sitemap_admin_robots.html' );

require_once XOOPS_ROOT_PATH . '/footer.php';
