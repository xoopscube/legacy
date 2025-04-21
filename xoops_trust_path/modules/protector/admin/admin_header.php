<?php
/**
 * Admin Header for Protector
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

 // Include header
xoops_cp_header();

require_once dirname(__DIR__) . '/include/permission_check.php';

// Check if user has permission to access the module admin
if (!protector_check_permission('module_access')) {
    redirect_header(XOOPS_URL, 3, _NOPERM);
    exit();
}