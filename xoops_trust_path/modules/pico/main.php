<?php
/**
 * Pico content management D3 module for XCL
 * @package    Pico
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

$mytrustdirname = basename( __DIR__ );
$mytrustdirpath = __DIR__;

// check permission of 'module_read' of this module
// (already checked by common.php)

$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';

if ( ! file_exists( $langmanpath ) ) {
	die( 'install the latest altsys' );
}

require_once( $langmanpath );

$langman = D3LanguageManager::getInstance();

$langman->read( 'main.php', $mydirname, $mytrustdirname );

// get page name (new in 2008-03-24)
$page = preg_replace( '/[^a-zA-Z0-9_-]/', '', @$_GET['page'] );

if ( empty( $page ) ) {
	preg_match( '/[?&]page\=([a-zA-Z0-9_-]+)/', @$_SERVER['REQUEST_URI'], $regs );
	$page = @$regs[1];
}

// fork each pages
if ( file_exists( "$mytrustdirpath/main/$page.php" ) ) {

	include "$mytrustdirpath/main/$page.php";

} else {

	include "$mytrustdirpath/main/index.php";

}
