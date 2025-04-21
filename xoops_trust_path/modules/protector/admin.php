<?php
/**
 * Protector module for XCL
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

$mytrustdirname = basename( __DIR__ );
$mytrustdirpath = __DIR__;

// environment
require_once XOOPS_ROOT_PATH . '/class/template.php';
// Remove reference operators
$module_handler    = xoops_gethandler( 'module' );
$xoopsModule       = $module_handler->getByDirname( $mydirname );
$config_handler    = xoops_gethandler( 'config' );
$xoopsModuleConfig = $config_handler->getConfigsByCat( 0, $xoopsModule->getVar( 'mid' ) );

// check permission of 'module_admin' of this module
$moduleperm_handler = xoops_gethandler( 'groupperm' );
// Improve null check and permission verification
if ( !isset($xoopsUser) || !is_object($xoopsUser) || !$moduleperm_handler->checkRight( 'module_admin', $xoopsModule->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
	die( 'only admin can access this area' );
}

$xoopsOption['pagetype'] = 'admin';
require XOOPS_ROOT_PATH . '/include/cp_functions.php';

// language files (admin.php)
$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'];
// Use file_exists more efficiently by checking most likely path first
if ( file_exists( "$mytrustdirpath/language/$language/admin.php" ) ) {
	// default language file
	include_once "$mytrustdirpath/language/$language/admin.php";
} elseif ( file_exists( "$mydirpath/language/$language/admin.php" ) ) {
	// user customized language file
	include_once "$mydirpath/language/$language/admin.php";
} else {
	// fallback english
	include_once "$mytrustdirpath/language/english/admin.php";
}

// language files (main.php)
// Use same pattern for main.php
if ( file_exists( "$mytrustdirpath/language/$language/main.php" ) ) {
	// default language file
	include_once "$mytrustdirpath/language/$language/main.php";
} elseif ( file_exists( "$mydirpath/language/$language/main.php" ) ) {
	// user customized language file
	include_once "$mydirpath/language/$language/main.php";
} else {
	// fallback english
	include_once "$mytrustdirpath/language/english/main.php";
}

// Improve request parameter handling
$lib = '';
$page = '';

if ( !empty( $_GET['lib'] ) ) {
	// common libs (eg. altsys)
	$lib  = preg_replace( '/[^a-zA-Z0-9_-]/', '', $_GET['lib'] );
	$page = isset($_GET['page']) ? preg_replace( '/[^a-zA-Z0-9_-]/', '', $_GET['page'] ) : '';
	
	$lib_path = XOOPS_TRUST_PATH . '/libs/' . $lib;
	$page_file = $lib_path . '/' . $page . '.php';
	$index_file = $lib_path . '/index.php';

	if ( file_exists( $page_file ) ) {
		include $page_file;
	} elseif ( file_exists( $index_file ) ) {
		include $index_file;
	} else {
		die( 'wrong request' );
	}
} else {
	// fork each pages of this module
	$page = isset($_GET['page']) ? preg_replace( '/[^a-zA-Z0-9_-]/', '', $_GET['page'] ) : '';
	
	$page_file = "$mytrustdirpath/admin/$page.php";
	$index_file = "$mytrustdirpath/admin/index.php";

	if ( file_exists( $page_file ) ) {
		include $page_file;
	} elseif ( file_exists( $index_file ) ) {
		include $index_file;
	} else {
		die( 'wrong request' );
	}
}
