<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once '../mainfile.php';
include_once './class/dbmanager.php';

$dbm   = new db_manager();
$title = _INSTALL_L104;

if ( ! $dbm->isConnectable() ) {
	$wizard->addArray( 'checks', _NGIMG . _INSTALL_L106 );
	$wizard->addArray( 'msgs', '<div class="confirmError">'. _INSTALL_L107 .'</div' );
	$wizard->setBack( [ 'start', _INSTALL_L103 ] );
	$wizard->setReload( true );
} else {
    // Check connection to Database
	$wizard->addArray( 'checks', _OKIMG . _INSTALL_L108 );

    // DB
	if ( ! $dbm->dbExists() ) {
		$wizard->addArray( 'checks', _NGIMG . sprintf(  _INSTALL_L109 , XOOPS_DB_NAME ) );
		$wizard->addArray( 'msgs', '<div class="confirmError">'. _INSTALL_L21 . '<br><code>' . XOOPS_DB_NAME . '</code></div>' );
		$wizard->addArray( 'msgs', '<div class="confirmInfo">'._INSTALL_L22. '</div>' );
	} else {

		$wizard->addArray( 'checks', _OKIMG . sprintf( _INSTALL_L110, '<code>'.XOOPS_DB_NAME.'</code>' ) );
        // Detect Table Users
		if ( ! $dbm->tableExists( 'users' ) ) {
			$wizard->addArray( 'msgs', '<div class="confirmOk">'._INSTALL_L111.'</div>' );
			$wizard->setNext( [ 'createTables', _INSTALL_L40 ] );

        }elseif ( $dbm->tableExists( 'users' ) ) {
            $wizard->addArray('msgs', '<div class="confirmError">'._INSTALL_L130.'</div>');
            $wizard->setBack( [ 'start', _INSTALL_L103 ] );
            // Force might duplicate results in DB tables eg. groups
            $wizard->setNext( [ 'createTables', _INSTALL_L40 ] );
		} elseif ( ! $dbm->tableExists( 'config' ) ) {
			$wizard->addArray( 'msgs', '<div class="confirmError">'._INSTALL_L130.'</div>' );
            // Attempt to update previous versions
			$wizard->setNext( [ 'updateTables', _INSTALL_L14 ] );
            $wizard->setReload( true );
		} else {
			$wizard->addArray( 'checks', _NGIMG . _INSTALL_L131 );
			$wizard->addArray( 'msgs', '<div class="confirmError">'. _INSTALL_L131 .'</div>' );
			$wizard->setBack( [ 'start', _INSTALL_L103 ] );
		}
	}
}

$wizard->render( 'install_checkDB.tpl.php' );
