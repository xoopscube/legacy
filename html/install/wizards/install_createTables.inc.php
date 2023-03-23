<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.3
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once '../mainfile.php';
include_once './class/dbmanager.php';

$dbm = new db_manager();

$tables = [];
// @todo @gigamaster replace deprecated
$result = $dbm->queryFromFile( './sql/' . ( ( XOOPS_DB_TYPE === 'mysqli' ) ? 'mysql' : XOOPS_DB_TYPE ) . '.structure.sql' );

$wizard->assign( 'reports', $dbm->report() );

if ( ! $result ) {
	$wizard->assign( 'message', '<div class="confirmError">'. _INSTALL_L114 .'</div>' );
	$wizard->setBack( [ 'start', _INSTALL_L103 ] );
} else {
    // Database tables created
	$wizard->assign( 'message', '<div class="confirmInfo">'. _INSTALL_L115 .'</div>' );
}

$wizard->render( 'install_createTables.tpl.php' );
