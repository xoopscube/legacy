<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

$mytrustdirname = basename( __DIR__ );
$mytrustdirpath = __DIR__;

// language files
$language = empty( $GLOBALS['xoopsConfig']['language'] ) ? 'english' : $GLOBALS['xoopsConfig']['language'];

if ( file_exists( "$mydirpath/language/$language/blocks.php" ) ) {
	// user customized language file (already read by class/xoopsblock.php etc)
	// include_once "$mydirpath/language/$language/blocks.php" ;
} elseif ( file_exists( "$mytrustdirpath/language/$language/blocks_common.php" ) ) {
	// default language file
	include_once "$mytrustdirpath/language/$language/blocks_common.php";
	include "$mytrustdirpath/language/$language/blocks_each.php";
} else {
	// fallback english
	include_once "$mytrustdirpath/language/english/blocks_common.php";
	include "$mytrustdirpath/language/english/blocks_each.php";
}

require_once "$mytrustdirpath/blocks/block_functions.php";
