<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.1
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

include './language/' . $language . '/welcome.php'; //This will set message to $content;

$error = false;

if ( ! $error ) {
	$wizard->assign( 'welcome', $content );
} else {
	$wizard->assign( 'message', '<div class="confirmError">'. _INSTALL_L168 .'</div>' );
	$wizard->setReload( true );
}

$wizard->render( 'install_start.tpl.php' );
