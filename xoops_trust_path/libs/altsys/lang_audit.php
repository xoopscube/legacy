<?php
/**
 * Altsys Language Audit Translations
 * Integrates the XOOPSCube/XCL Language File Audit Tool into Altsys admin UI
 * 
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

// XOOPSCube XCL Language File Audit Tool
// Scans all modules in XOOPS_ROOT_PATH and XOOPS_TRUST_PATH for language files
// Compares all constants in /english/*.php to those in each other language folder
// Outputs an HTML report with emoji for success/fail
// v2.5.0 also reports extra constants
// (defined in translation but not in English, or not used anywhere on the code)


// include_once __DIR__ . '/../../mainfile.php';
require_once __DIR__ . '/class/AltsysBreadcrumbs.class.php';
require_once __DIR__ . '/include/gtickets.php';
include_once __DIR__ . '/include/altsys_functions.php';

// Permission: only groups with 'module_admin' of 'altsys' can access
// only groups have 'module_admin' of 'altsys' can do that.
$module_handler = xoops_gethandler( 'module' );
$module         = $module_handler->getByDirname( 'altsys' );
if ( ! is_object( $module ) ) {
	die( 'install altsys' );
}
$moduleperm_handler = xoops_gethandler( 'groupperm' );
if ( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin', $module->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
	die( 'only admin of altsys can access this area' );
}

$audit_page_title = _MI_ALTSYS_MENU_MYLANGAUDIT;

// Emergency language reset to handler (must be at top, before any output)
if (isset($_GET['resetlang']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    require_once dirname(__FILE__) . '/mainfile.php';
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    $table = XOOPS_DB_PREFIX . '_config';
    $sql = "UPDATE `$table` SET conf_value='english' WHERE conf_name='language' AND conf_modid=0";
    $ok = $db->queryF($sql);
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB update failed']);
    }
    exit;
}

// Extras for D3 modules prefix
function extract_defines($file, $mydirname = null) {
    $defines = [];
    if (!file_exists($file)) return $defines;
    $content = file_get_contents($file);
    // D3 module support: detect $constpref assignment
    $constpref = null;
    if (preg_match('/\$constpref\s*=\s*([\'\"])?(_MB_|_MI_|_MD_)?\.'
        . '\s*strtoupper\s*\(\s*\$mydirname\s*\)\s*([\'\"])?/i', $content)) {
        // If $mydirname is not set, try to infer from path
        if (!$mydirname) {
            // e.g. .../modules/d3forum/language/...
            if (preg_match('#modules/([^/]+)/language/#', str_replace('\\','/',$file), $m)) {
                $mydirname = $m[1];
            }
        }
        if ($mydirname) {
            // Try to infer prefix (e.g. _MB_D3FORUM_)
            if (preg_match('/\$constpref\s*=\s*([\'\"])?(_MB_|_MI_|_MD_)?\.'
                . '\s*strtoupper\s*\(\s*\$mydirname\s*\)\s*([\'\"])?/i', $content, $pm)) {
                $constpref = ($pm[2] ?? '') . strtoupper($mydirname) . '_';
            }
        }
    }
    // Match define($constpref . '_XYZ', ...)
    if ($constpref && preg_match_all('/define\s*\(\s*\$constpref\s*\.\s*[\'\"](_[A-Z0-9]+)[\'\"]\s*,/', $content, $matches)) {
        foreach ($matches[1] as $suffix) {
            $defines[] = $constpref . ltrim($suffix, '_');
        }
    }
    // Also match normal define('CONSTANT', ...)
    if (preg_match_all("/define\\s*\\(\\s*['\"]([^'\"]+)['\"]\\s*,/", $content, $matches2)) {
        foreach ($matches2[1] as $c) {
            // Avoid duplicates
            if (!in_array($c, $defines)) $defines[] = $c;
        }
    }
    return $defines;
}

// Get all used constants in codebase (for extra constant reporting)
// Replace the problematic code with this
$roots = [
    XOOPS_ROOT_PATH . '/modules',
    XOOPS_TRUST_PATH . '/modules'
];
$code_roots = [
    XOOPS_ROOT_PATH,
    XOOPS_TRUST_PATH
];

// Also modify the get_all_used_constants function to handle directory errors
function get_all_used_constants($root_dirs) {
    $used = [];
    foreach ($root_dirs as $dir) {
        // Skip if directory doesn't exist
        if (!is_dir($dir)) {
            continue;
        }
        
        try {
            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
            foreach ($rii as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                    $content = file_get_contents($file->getPathname());
                    // Match both constant('_CONST') and _CONST direct usage
                    if (preg_match_all("/(['\"])(_[_A-Z0-9]+)\\1|\b(_[_A-Z0-9]+)\b/", $content, $matches)) {
                        foreach (array_merge($matches[2], $matches[3]) as $const) {
                            if ($const) $used[$const] = true;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Log the error but continue with other directories
            error_log("Error scanning directory {$dir}: " . $e->getMessage());
        }
    }
    return $used;
}

$used_constants = get_all_used_constants($code_roots);

$all_reports = [];

foreach ($roots as $base) {
    foreach (glob($base . '/*', GLOB_ONLYDIR) as $moddir) {
        $module = basename($moddir);
        $langbase = $moddir . '/language';
        if (!is_dir($langbase)) continue;
        $languages = [];
        foreach (glob($langbase . '/*', GLOB_ONLYDIR) as $langdir) {
            $langcode = basename($langdir);
            $languages[$langcode] = true;
        }
        $englishdir = $langbase . '/english';
        if (!is_dir($englishdir)) continue;
        foreach (glob($englishdir . '/*.php') as $efile) {
            $fname = basename($efile);
            $edefs = extract_defines($efile);
            foreach ($languages as $langcode => $_) {
                if ($langcode == 'english') continue;
                $tfile = $langbase . '/' . $langcode . '/' . $fname;

// --- Add Debugging Here ---
//     if ($langcode == 'pt_utf8' && $fname == 'admin.php') { // Optional: Only print for the specific file
//      error_log("Checking for file: " . $tfile); // Log to PHP error log
       // Or use echo for direct output (might clutter the page):
//       echo "Checking for file: " . $tfile . "<br>";
//       echo "File exists check: " . (file_exists($tfile) ? 'TRUE' : 'FALSE') . "<br>";
//       echo "File is readable check: " . (is_readable($tfile) ? 'TRUE' : 'FALSE') . "<br>";
//  }
 // --- End Debugging ---

                $tdefs = extract_defines($tfile);
                $missing = array_diff($edefs, $tdefs);
                $extra   = array_diff($tdefs, $edefs);
                $extra_unused = array_filter($extra, function($const) use ($used_constants) {
                    return !isset($used_constants[$const]);
                });
                $all_reports[] = [
                    'module' => $module,
                    'lang' => $langcode,
                    'file' => $fname,
                    'missing' => $missing,
                    'extra' => $extra,
                    'extra_unused' => $extra_unused,
                    'ok' => empty($missing)
                ];
            }
        }
    }
}

// Collect unique modules and languages
$modules = [];
$languages = [];
foreach ($all_reports as $r) {
    $modules[$r['module']] = true;
    $languages[$r['lang']] = true;
}
ksort($modules); ksort($languages);

// Prepare module and language options for the template
$module_options = [];
$language_options = [];

foreach(array_keys($modules) as $mod) {
    $module_options[] = [
        'value' => htmlspecialchars($mod, ENT_QUOTES, 'UTF-8'),
        'text' => htmlspecialchars($mod, ENT_QUOTES, 'UTF-8')
    ];
}

foreach(array_keys($languages) as $lang) {
    $language_options[] = [
        'value' => htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'),
        'text' => htmlspecialchars($lang, ENT_QUOTES, 'UTF-8')
    ];
}

// mail
// TODO check if any setup available
// PHP: handle email send request using Mailer
if (isset($_GET['sendmail']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $to = filter_var($input['to'] ?? '', FILTER_VALIDATE_EMAIL);
    $mod = $input['mod'] ?? '';
    $lang = $input['lang'] ?? '';
    $data = $input['data'] ?? '';
    if (!$to || !$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email or data.']);
        exit;
    }
    require_once dirname(__FILE__) . '/mainfile.php';
    require_once XOOPS_ROOT_PATH . '/class/xoopsmailer.php';
    $subject = 'XOOPSCube XCL - Recovery Language Audit Report';
    if ($mod || $lang) $subject .= " [" . ($mod ? "Module: $mod " : "") . ($lang ? "Lang: $lang" : "") . "]";
    $body = "Attached is the audit report as a JSON file.\n\n";
    $mailer = new xoopsmailer();
    $mailer->useMail();
    $mailer->setToEmails([$to]);
    $mailer->setFromEmail($GLOBALS['xoopsConfig']['adminmail'] ?? 'webmaster@' . $_SERVER['SERVER_NAME']);
    $mailer->setFromName($GLOBALS['xoopsConfig']['sitename'] ?? 'XOOPS Site');
    $mailer->setSubject($subject);
    $mailer->setBody($body);
    // Attach the JSON report
    $filename = 'lang_audit_report.json';
    if (method_exists($mailer->multimailer, 'addStringAttachment')) {
        $mailer->multimailer->addStringAttachment($data, $filename, 'base64', 'application/json');
    }
    $ok = $mailer->send();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        $errors = method_exists($mailer, 'getErrors') ? $mailer->getErrors(false) : ['Mail send failed.'];
        echo json_encode(['error' => 'Mail send failed.', 'details' => $errors]);
    }
    exit;
}

$jsonAllreports = json_encode($all_reports, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE);
//
xoops_cp_header();

// mymenu
altsys_include_mymenu();

// Render - Assign to Smarty Template
$tpl = new D3Tpl();

// Assign the modules and languages arrays to the template
$tpl->assign('modules', $modules);
$tpl->assign('languages', $languages);
$tpl->assign('module_options', $module_options);
$tpl->assign('language_options', $language_options);
$tpl->assign('lang_audit_title', $audit_page_title);
$tpl->assign('lang_audit_reports', $all_reports);
$tpl->assign('lang_audit_jsonreports', json_encode($all_reports));

$tpl->display('db:altsys_main_lang_audit.html');

xoops_cp_footer();
exit;
