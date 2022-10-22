<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.1
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

include_once '../mainfile.php';
include_once './class/dbmanager.php';

$dbm = new db_manager();

if ( ! $dbm->createDB() ) {
    // Could not create database. Contact the server administrator for details
	$wizard->setContent( '<div class="confirmError">' . _INSTALL_L31 . '</div>' );
	$wizard->setNext( [ 'checkDB', _INSTALL_L104 ] );
	$wizard->setBack( [ 'start', _INSTALL_L103 ] );
} else {
    // Database %s created
	$wizard->setContent( '<div class="confirmOk">' . sprintf( _INSTALL_L43, XOOPS_DB_NAME ) . '</div>' );
}

$wizard->render();
