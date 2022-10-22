<?php
/**
 * @file
 * @brief The page controller in the directory
 * @package xupdate
 * @version $Id$
 **/

// to call User_Utils::checkUsersPassColumnLength()
// and insert `define('XCUBE_CORE_USER_PASS_LEN_FIXED', true);` to mainfile.php
if ( ! defined( 'XCUBE_CORE_USER_PASS_LEN_FIXED' ) && is_callable( 'User_Utils::checkUsersPassColumnLength' ) && 'InstallChecker' !== xoops_getrequest( 'action' ) && 'UserPassColumnLenFix' !== xoops_getrequest( 'action' ) ) {
	header( 'Location: ' . XOOPS_URL . '/modules/xupdate/admin/index.php?action=UserPassColumnLenFix&xoops_redirect=' . rawurlencode( $_SERVER['REQUEST_URI'] ) );
	exit();
}

$root =& XCube_Root::getSingleton();
$root->mContext->mModule->setAdminMode( true );


$root->mController->executeHeader();
$root->mController->execute();


$xoopsLogger =& $root->mController->getLogger();
$xoopsLogger->stopTime();
$root->mController->executeView();
