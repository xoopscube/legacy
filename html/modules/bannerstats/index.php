<?php
/**
 * Bannerstats - Module for XCL
 * Client-side entry point for banner client actions.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

include_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

// Check session is started for BannerClientSession
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClientSession.class.php';

$moduleDirname = 'bannerstats';
$root = XCube_Root::getSingleton();
$controller = $root->getController(); // For redirects

global $xoopsTpl;
if (!is_object($xoopsTpl)) {
    echo "Critical error: Template engine not initialized.";
    exit;
}

$actionNameInput = isset($_REQUEST['action']) ? trim(xoops_getrequest('action')) : '';
$actionName = (!empty($actionNameInput) && preg_match("/^\w+$/", $actionNameInput)) ? $actionNameInput : 'Login';

$actionClassName = 'Bannerstats_' . ucfirst($actionName) . 'Action';
$actionFilePath = XOOPS_MODULE_PATH . "/bannerstats/actions/" . ucfirst($actionName) . "Action.class.php";

$pageTitle = "Banner Statistics";

if (file_exists($actionFilePath)) {
    require_once $actionFilePath;
    if (class_exists($actionClassName)) {
        $actionInstance = new $actionClassName();
        $actionsRequiringAuth = ['Stats', 'ChangeUrl', 'EmailStats', 'RequestSupport', 'Logout'];
        $isProtectedAction = in_array($actionName, $actionsRequiringAuth, true);

        if ($isProtectedAction && !BannerClientSession::isAuthenticated()) {
            $returnUrlParam = ($actionName !== 'Logout' && $_SERVER['REQUEST_METHOD'] === 'GET') ? "&return_url=" . urlencode($_SERVER['REQUEST_URI']) : '';
            $controller->executeForward(XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Login" . $returnUrlParam);
            exit;
        }

        if (($actionName === 'Login' || $actionName === 'Authenticate') && BannerClientSession::isAuthenticated()) {
            $controller->executeForward(XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Stats");
            exit;
        }

        $templateToDisplay = null;

        if (method_exists($actionInstance, 'getPageTitle')) {
            $pageTitle = $actionInstance->getPageTitle();
        }

        $xoopsTpl->assign('page_title', htmlspecialchars($pageTitle, ENT_QUOTES));

        $xoopsTpl->assign('bannerstats_client_is_auth', BannerClientSession::isAuthenticated());
        if (BannerClientSession::isAuthenticated()) {
            $xoopsTpl->assign('bannerstats_client_login', htmlspecialchars(BannerClientSession::getClientLogin() ?? '', ENT_QUOTES));
            $xoopsTpl->assign('bannerstats_logout_link', XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Logout");
            $xoopsTpl->assign('bannerstats_stats_link', XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Stats");
            $xoopsTpl->assign('bannerstats_support_link', XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=RequestSupport");
        } else {
            $xoopsTpl->assign('bannerstats_login_link', XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=Login");
        }

        if ('POST' === xoops_getenv('REQUEST_METHOD') && method_exists($actionInstance, 'execute')) {
            $templateToDisplay = $actionInstance->execute($controller, $xoopsTpl);
        } elseif (method_exists($actionInstance, 'getDefaultView')) {
            $templateToDisplay = $actionInstance->getDefaultView($controller, $xoopsTpl);
        } else {
            $xoopsTpl->assign('bannerstats_error_message', "Action '{$actionName}' is not configured correctly (method missing).");
            $templateToDisplay = 'bannerstats_error.html';
        }

        if (is_string($templateToDisplay) && !empty($templateToDisplay)) {
            $xoopsTpl->display('db:' . $templateToDisplay);
        }

    } else {
        $xoopsTpl->assign('page_title', "Error");
        $xoopsTpl->assign('bannerstats_error_message', "The requested action '{$actionName}' could not be processed (class not found).");
        $xoopsTpl->display('db:bannerstats_error.html');
    }
} else {
    $xoopsTpl->assign('page_title', "Error");
    $xoopsTpl->assign('bannerstats_error_message', "The requested action '{$actionName}' is not available (file not found).");
    $xoopsTpl->display('db:bannerstats_error.html');
}

require_once XOOPS_ROOT_PATH . '/footer.php';
