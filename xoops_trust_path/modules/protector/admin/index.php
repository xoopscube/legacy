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

// Get page parameter (changed from action to page for consistency)
$page = $_GET['page'] ?? 'dashboard';

// Include appropriate page file
switch ($page) {
    case 'log':
        include_once __DIR__ . '/log_viewer.php';
        break;
    case 'ban':
        include_once __DIR__ . '/ban_manager.php';
        break;
    case 'prefix_manager':
        include_once __DIR__ . '/prefix_manager.php';
        break;
    case 'advisory':
        include_once __DIR__ . '/advisory.php';
        break;
    case 'mymenu':
        include_once __DIR__ . '/mymenu.php';
        break;
    case 'safe_list':
        include_once __DIR__ . '/safe_list.php';
        break;
    default:
        include_once __DIR__ . '/dashboard.php';
        break;
}
