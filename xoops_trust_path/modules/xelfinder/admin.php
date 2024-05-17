<?php
/**
 * X-elFinder module for XCL
 * @package    XelFinder
 * @version    XCL 2.4.0
 * @author     Naoki Sawada (aka Nao-pon) <https://github.com/nao-pon>
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

ini_set( 'default_charset', _CHARSET );
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	ini_set( 'mbstring.internal_encoding', _CHARSET );
} else {
	@ini_set( 'mbstring.internal_encoding', '' );
}
if ( ! defined( 'XOOPS_MODULE_PATH' ) ) {
	define( 'XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules' );
}
if ( ! defined( 'XOOPS_MODULE_URL' ) ) {
	define( 'XOOPS_MODULE_URL', XOOPS_URL . '/modules' );
}

$mytrustdirname = basename( __DIR__ );
$mytrustdirpath = __DIR__;

// environment
require_once XOOPS_ROOT_PATH . '/class/template.php';
$module_handler    = xoops_getHandler( 'module' );
$xoopsModule       = $module_handler->getByDirname( $mydirname );
$config_handler    = xoops_getHandler( 'config' );
$xoopsModuleConfig = $config_handler->getConfigsByCat( 0, $xoopsModule->getVar( 'mid' ) );

// check permission of 'module_admin' of this module
$moduleperm_handler = xoops_getHandler( 'groupperm' );
if ( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin', $xoopsModule->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
	die( 'only admin can access this area' );
}

$xoopsOption['pagetype'] = 'admin';
require XOOPS_ROOT_PATH . '/include/cp_functions.php';

// initialize language manager
$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';
if ( ! file_exists( $langmanpath ) ) {
	die( 'install the latest altsys' );
}
require_once( $langmanpath );
$langman = D3LanguageManager::getInstance();


if ( ! empty( $_GET['lib'] ) ) {
	// common libs (eg. altsys)
	$lib  = preg_replace( '/[^a-zA-Z0-9_-]/', '', $_GET['lib'] );
	$page = preg_replace( '/[^a-zA-Z0-9_-]/', '', @$_GET['page'] );

	// check the page can be accessed (make controllers.php just under the lib)
	$controllers = [];
	if ( file_exists( XOOPS_TRUST_PATH . '/libs/' . $lib . '/controllers.php' ) ) {
		require XOOPS_TRUST_PATH . '/libs/' . $lib . '/controllers.php';
		if ( ! in_array( $page, $controllers ) ) {
			$page = $controllers[0];
		}
	}

	if ( file_exists( XOOPS_TRUST_PATH . '/libs/' . $lib . '/' . $page . '.php' ) ) {
		include XOOPS_TRUST_PATH . '/libs/' . $lib . '/' . $page . '.php';
	} else if ( file_exists( XOOPS_TRUST_PATH . '/libs/' . $lib . '/index.php' ) ) {
		include XOOPS_TRUST_PATH . '/libs/' . $lib . '/index.php';
	} else {
		die( 'wrong request' );
	}
} else {
	// load language files (main.php & admin.php)
	$langman->read( 'modinfo.php', $mydirname, $mytrustdirname );
	$langman->read( 'main.php', $mydirname, $mytrustdirname );

	// fork each pages of this module
	$page = preg_replace( '/[^a-zA-Z0-9_-]/', '', @$_GET['page'] );

	if ( file_exists( "$mytrustdirpath/admin/$page.php" ) ) {
		include "$mytrustdirpath/admin/$page.php";
	} else if ( file_exists( "$mytrustdirpath/admin/index.php" ) ) {
		include "$mytrustdirpath/admin/index.php";
	} else {
		die( 'wrong request' );
	}

}

function xelfinderAdminLang( $name ) {
	$pref = '_MI_' . strtoupper( $GLOBALS['mydirname'] ) . '_';

	return defined( $pref . $name ) ? constant( $pref . $name ) : $pref . $name;
}

