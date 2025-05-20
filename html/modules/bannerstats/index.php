<?php
// html/modules/bannerstats/index.php

// Include XOOPS mainfile. This bootstraps the XOOPS environment.
include_once __DIR__ . '/../../mainfile.php';

// Include XOOPS header. This sets up the theme, Smarty ($xoopsTpl), etc.
require_once XOOPS_ROOT_PATH . '/header.php';

// Ensure session is started, as BannerClientSession relies on it.
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// Get the current module's directory name
$moduleDirname = basename(dirname(__FILE__)); // This will be 'bannerstats'

// Include necessary classes from your module
require_once XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientSession.class.php';
require_once XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientToken.class.php';
// require_once XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerStatsManager.class.php';

// Determine the action to perform. Default to 'Login'.
$actionName = isset($_REQUEST['action']) ? ucfirst(trim($_REQUEST['action'])) : 'Login';

// Security: Basic validation for action name (alphanumeric)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $actionName)) {
    $actionName = 'Login';
}

// If not authenticated and the action is not 'Login', 'Logout', or 'Contact', force to 'Login'.
if (!BannerClientSession::isAuthenticated() &&
    !in_array($actionName, ['Login', 'Logout', 'Contact'])) {
    $actionName = 'Login';
}

// --- ENSURE THESE INITIALIZATIONS ARE PRESENT AND CORRECTLY PLACED ---
$actionInstance = null;
$actionErrorMessage = null; // <<< THIS LINE IS CRITICAL
$templateToRender = null;
$pageTitle = "Banner Statistics"; // Default page title
// --- END INITIALIZATIONS ---


// Define known, valid actions and their corresponding class details
$knownActions = [
    'Login'   => ['file' => 'LoginAction.class.php',   'class' => 'Bannerstats_LoginAction'],
    'Stats'   => ['file' => 'StatsAction.class.php',   'class' => 'Bannerstats_StatsAction'],
    'Logout'  => ['file' => 'LogoutAction.class.php',  'class' => 'Bannerstats_LogoutAction'],
    //'Contact' => ['file' => 'ContactAction.class.php', 'class' => 'Bannerstats_ContactAction'],
    'EmailStats' => ['file' => 'EmailStatsAction.class.php', 'class' => 'Bannerstats_EmailStatsAction'],
    'ChangeUrl'  => ['file' => 'ChangeUrlAction.class.php',  'class' => 'Bannerstats_ChangeUrlAction'],
    'RequestSupport'   => ['file' => 'RequestSupportAction.class.php', 'class' => 'Bannerstats_RequestSupportAction'],
    // Add other directly callable actions like CreateBanner, ChangeUrl if they exist
    // 'ChangeUrl'  => ['file' => 'ChangeUrlAction.class.php',  'class' => 'Bannerstats_ChangeUrlAction'],

];
// --- Action Dispatching Logic (as previously discussed) ---
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
// --- End Action Dispatching Logic ---


// Handle any errors that occurred during action loading or preparation
// This is likely where your line 100 is, or the block just before it.
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

// If a template name was set (either by a successful action or error handling), display it
if ($templateToRender) {
    $templateIdentifier = 'db:' . $templateToRender;
    if (is_object($GLOBALS['xoopsTpl'])) {
        $GLOBALS['xoopsTpl']->display($templateIdentifier);
    } else {
        echo "Critical Error: Template engine not available.";
        if ($actionErrorMessage) { // Check if $actionErrorMessage is set before trying to use it
            echo "<br>Error Details: " . htmlspecialchars($actionErrorMessage);
        }
    }
}

// Include XOOPS footer
require_once XOOPS_ROOT_PATH . '/footer.php';