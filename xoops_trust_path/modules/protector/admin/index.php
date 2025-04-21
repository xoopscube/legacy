<?php
/**
 * Protector Admin Dashboard
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// require_once '../../../mainfile.php';
// require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';

// Include header
xoops_cp_header();

// Check admin authentication
if ($xoopsUser) {
    // Fix: Use module handler to get module object instead of static call
    $module_handler = xoops_getHandler('module');
    $xoopsModule = $module_handler->getByDirname('protector');
    
    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL . '/', 3, _NOPERM);
        exit();
    }
} else {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
    exit();
}

// Get the page parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// TODO Include the appropriate page 
switch ($page) {
    case 'log':
        include __DIR__ . '/log.php';
        break;
    case 'ban':
        include __DIR__ . '/ban.php';
        break;
    case 'prefix_manager':
        include __DIR__ . '/prefix_manager.php';
        break;
    case 'advisory':
        include __DIR__ . '/advisory.php';
        break;
    case 'safe_list':
        include __DIR__ . '/safe_list.php';
        break;
// Proxy
    case 'proxy_settings':
        include __DIR__ . '/proxy_settings.php';
        break;
    case 'proxy_plugins':
        include __DIR__ . '/proxy_plugins.php';
        break;
    case 'proxy_logs':
        include __DIR__ . '/proxy_logs.php';
        break;
// HTTP:BL
    case 'threat_intelligence':
        include __DIR__ . '/threat_intelligence.php';
        break;
    case 'permissions':
        include __DIR__ . '/permissions.php';
        break;
    case 'csp_violations':
        include __DIR__ . '/csp_violations.php';
        break;
    case 'dashboard':
    default:
        include __DIR__ . '/dashboard.php';
        break;
}
