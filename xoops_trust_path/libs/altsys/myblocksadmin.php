<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Admin Blocks of each module
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */


require_once __DIR__ . '/class/AltsysBreadcrumbs.class.php';
require_once __DIR__ . '/include/gtickets.php';
include_once __DIR__ . '/include/altsys_functions.php';
include_once __DIR__ . '/include/mygrouppermform.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

// language file
altsys_include_language_file( 'myblocksadmin' );
altsys_get_core_type();
include_once __DIR__ . '/class/MyBlocksAdminForXCL21.class.php';
$myba =& MyBlocksAdminForXCL21::getInstance();

// permission
$myba->checkPermission();

// set parameters target_mid , target_dirname etc.
$myba->init( $xoopsModule );


// transaction
if ( ! empty( $_POST ) ) {
	$myba->processPost();
}


// RENDER View

// header
xoops_cp_header();

// mymenu
altsys_include_mymenu();

$myba->processGet();

// footer
xoops_cp_footer();
