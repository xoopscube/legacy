<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 **/

include_once __DIR__ . '/../../mainfile.php';

require_once XOOPS_ROOT_PATH . '/header.php';

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

$moduleDirname = basename(dirname(__FILE__));

require_once XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientSession.class.php';
require_once XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientToken.class.php';

$actionName = isset($_REQUEST['action']) ? ucfirst(trim($_REQUEST['action'])) : 'Login';

if (!preg_match('/^[a-zA-Z0-9_]+$/', $actionName)) {
    $actionName = 'Login';
}

if (!BannerClientSession::isAuthenticated() &&
    !in_array($actionName, ['Login', 'Logout', 'Contact'])) {
    $actionName = 'Login';
}

$actionInstance = null;
$actionErrorMessage = null;
$templateToRender = null;
$pageTitle = "Banner Statistics";


$knownActions = [
    'Login'   => ['file' => 'LoginAction.class.php',   'class' => 'Bannerstats_LoginAction'],
    'Stats'   => ['file' => 'StatsAction.class.php',   'class' => 'Bannerstats_StatsAction'],
    'Logout'  => ['file' => 'LogoutAction.class.php',  'class' => 'Bannerstats_LogoutAction'],
    //'FAQ' => ['file' => 'FaqAction.class.php', 'class' => 'Bannerstats_FaqAction'],
    'EmailStats' => ['file' => 'EmailStatsAction.class.php', 'class' => 'Bannerstats_EmailStatsAction'],
    'ChangeUrl'  => ['file' => 'ChangeUrlAction.class.php',  'class' => 'Bannerstats_ChangeUrlAction'],
    'RequestSupport'   => ['file' => 'RequestSupportAction.class.php', 'class' => 'Bannerstats_RequestSupportAction'],
    // Add other directly callable actions like CreateBanner, ChangeUrl if they exist
    // 'ChangeUrl'  => ['file' => 'ChangeUrlAction.class.php',  'class' => 'Bannerstats_ChangeUrlAction'],

];

if (isset($knownActions[$actionName])) {
    $actionDetails = $knownActions[$actionName];
    $actionFile = XOOPS_MODULE_PATH . '/' . $moduleDirname . '/actions/' . $actionDetails['file'];
    $className = $actionDetails['class'];

    if ($actionName === 'Stats' && !BannerClientSession::isAuthenticated()) {
        header("Location: " . XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Login");
        exit();
    }

    if (file_exists($actionFile)) {
        require_once $actionFile;
        if (class_exists($className)) {
            $actionInstance = new $className();
        } else {
            $actionErrorMessage = "System Error: The page handler ('" . htmlspecialchars($className) . "') is defined incorrectly. Please contact the administrator.";
            error_log("Bannerstats Error: Class {$className} not found in {$actionFile} for action '{$actionName}'.");
        }
    } else {
        $actionErrorMessage = "System Error: The page handler file for ('" . htmlspecialchars($actionName) . "') is missing. Please contact the administrator.";
        error_log("Bannerstats Error: Action file {$actionFile} not found for known action '{$actionName}'.");
    }
} else {
    $actionErrorMessage = "The requested page ('" . htmlspecialchars($actionName) . "') is not available.";
    error_log("Bannerstats Error: Unknown or unmapped action requested: '{$actionName}'.");
}

if ($actionInstance && !$actionErrorMessage) {
    if (method_exists($actionInstance, 'getPageTitle')) {
        $pageTitle = $actionInstance->getPageTitle();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && method_exists($actionInstance, 'execute')) {
        $templateToRender = $actionInstance->execute();
    } elseif (method_exists($actionInstance, 'getDefaultView')) {
        $templateToRender = $actionInstance->getDefaultView();
    } else {
        $actionErrorMessage = 'System Error: The action (' . htmlspecialchars($actionName) . ') is not properly configured (missing its execution method). Please contact the administrator.';
        error_log("Bannerstats Error: Action class " . get_class($actionInstance) . " is missing execute() or getDefaultView() method.");
    }
} elseif (!$actionInstance && !$actionErrorMessage) {
    $actionErrorMessage = $actionErrorMessage ?: 'System Error: Unable to initialize the requested page. Please contact the administrator.';
    error_log("Bannerstats Critical Error: Failed to obtain a valid action instance for action '{$actionName}'. Error: " . ($actionErrorMessage ?: 'Unknown reason.'));
}

// Handle any errors on action loading or preparation
if ($actionErrorMessage !== null) {
    if (is_object($GLOBALS['xoopsTpl'])) {
        $GLOBALS['xoopsTpl']->assign('bannerstats_error_message', $actionErrorMessage);
    }
    $templateToRender = 'bannerstats_error.html';
    $pageTitle = "Error - Banner Statistics";
}

// Assign final page title to Smarty
if (is_object($GLOBALS['xoopsTpl'])) {
    $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', htmlspecialchars($pageTitle, ENT_QUOTES));
}

if ($templateToRender) {
    $templateIdentifier = 'db:' . $templateToRender;
    if (is_object($GLOBALS['xoopsTpl'])) {
        $GLOBALS['xoopsTpl']->display($templateIdentifier);
    } else {
        echo "Critical Error: Template engine not available.";
        if ($actionErrorMessage) {
            echo "<br>Error Details: " . htmlspecialchars($actionErrorMessage);
        }
    }
}

// render footer
require_once XOOPS_ROOT_PATH . '/footer.php';