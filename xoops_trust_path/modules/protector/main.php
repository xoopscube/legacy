<?php
/**
 * Protector main dispatcher
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Direct access is prohibited
if (!defined('XOOPS_ROOT_PATH') || !defined('XOOPS_TRUST_PATH')) {
    exit();
}

$mytrustdirname = basename( __DIR__ );

$mytrustdirpath = __DIR__;

$page = isset($_GET['page']) ? trim($_GET['page']) : '';

// Dispatch
switch ($page) {
    case 'notification_update':
        require_once XOOPS_ROOT_PATH.'/include/notification_update.php';
        break;
    default:
        // Default action - redirect to admin
        header('Location: '.XOOPS_URL.'/modules/protector/admin/index.php');
        exit();
}
