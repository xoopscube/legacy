<?php

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';

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
    case 'threat_intelligence':
        include __DIR__ . '/threat_intelligence.php';
        break;
    case 'dashboard':
    default:
        include __DIR__ . '/dashboard.php';
        break;
}
