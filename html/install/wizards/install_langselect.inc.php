<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if ( ! defined( '_INSTALL_L128' ) ) {
	define( '_INSTALL_L128', 'Select a language for the installation process' );
}

$langarr = getDirList( './language/' );

$php7   = ( version_compare(PHP_VERSION, '7.0.0') >= 0 );

foreach ( $langarr as $lang ) {
	if ( $php7 && 'english' !== $lang && '_utf8' !== substr( $lang, - 5 ) ) {
		continue;
	}
	$wizard->addArray( 'languages', $lang );
	if ( strtolower( $lang ) === $language ) {
		$wizard->addArray( 'selected', 'selected="selected"' );
	} else {
		$wizard->addArray( 'selected', '' );
	}
}

$wizard->render( 'install_langselect.tpl.php' );
