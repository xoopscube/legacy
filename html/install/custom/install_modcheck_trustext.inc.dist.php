<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  Copyright 2005-2024 XOOPSCube Project
 * @license    GPL 2.0
 */

// checking XOOPS_ROOT_PATH and XOOPS_URL
include_once '../mainfile.php';

echo '<h2>custom/install_modcheck_trusttext.inc.dist</h2>';

$writeok = [ 'cache/', 'templates_c/', 'uploads/', 'uploads/xupdate/', 'modules/protector/configs/' ];
$error   = false;

foreach ( $writeok as $wok ) {
	if ( ! is_dir( XOOPS_TRUST_PATH . '/' . $wok ) ) {
		if ( file_exists( XOOPS_TRUST_PATH . '/' . $wok ) ) {
			@chmod( XOOPS_TRUST_PATH . '/' . $wok, 0666 );
			if ( ! is_writable( XOOPS_TRUST_PATH . '/' . $wok ) ) {
				$wizard->addArray( 'checks', _NGIMG . sprintf( _INSTALL_L83, $wok ) );
				$error = true;
			} else {
				$wizard->addArray( 'checks', _OKIMG . sprintf( _INSTALL_L84, $wok ) );
			}
		}
	} else {
		@chmod( XOOPS_TRUST_PATH . '/' . $wok, 0777 );
		if ( ! is_writable( XOOPS_TRUST_PATH . '/' . $wok ) ) {
			$wizard->addArray( 'checks', _NGIMG . sprintf( _INSTALL_L85, XOOPS_TRUST_PATH . '/' . $wok ) );
			$error = true;
		} else {
			$wizard->addArray( 'checks', _OKIMG . sprintf( _INSTALL_L86, XOOPS_TRUST_PATH . '/' . $wok ) );
		}
	}
}

if ( ! $error ) {
	$wizard->assign( 'message', '<div class="confirmOk">'. _INSTALL_L87 .'</div>' );
    $wizard->assign( 'message', '<div class="confirmOk">install modcheck trustext inc dist</div>' );
} else {
	$wizard->assign( 'message', '<div class="confirmError">'. _INSTALL_L46 .'</div>' );
	$wizard->setReload( true );
}
$wizard->render( 'install_modcheck.tpl.php' );
