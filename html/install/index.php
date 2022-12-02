<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 * @remark     This file was entirely rewritten by the XOOPSCube Legacy Project
 */


/* The error_reporting() function specifies which errors are reported.
* PHP has many levels of errors, and using this function sets that level for the current script.
* https://www.php.net/manual/en/errorfunc.constants.php
* PHP 7 makes E_STRICT irrelevant, reclassifying most of the errors as proper warnings, notices or E_DEPRECATED
 * The ^ is the xor (bit flipping) operator, so its "off" : ^ E_ALL
*/

if ( PHP_VERSION_ID >= 70000 ) {
	//error_reporting( error_reporting() ^ E_ALL & ~E_NOTICE );
    ini_set('error_reporting', E_ERROR);
}

/*
 * To trigger an E_USER_NOTICE error, you may use the trigger_error function:
 * trigger_error("You probably should not be using the feature this way, but I will allow you to continue if you know what you are doing.", E_USER_NOTICE);
 */

/* if ( PHP_VERSION_ID >= 70000 ) {

	$exceptions = [
        E_ERROR => "E_ERROR",
        E_WARNING => "E_WARNING",
        E_PARSE => "E_PARSE",
        E_NOTICE => "E_NOTICE",
        E_CORE_ERROR => "E_CORE_ERROR",
        E_CORE_WARNING => "E_CORE_WARNING",
        E_COMPILE_ERROR => "E_COMPILE_ERROR",
        E_COMPILE_WARNING => "E_COMPILE_WARNING",
        E_USER_ERROR => "E_USER_ERROR",
        E_USER_WARNING => "E_USER_WARNING",
        E_USER_NOTICE => "E_USER_NOTICE",
        E_STRICT => "E_STRICT",
        E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
        E_DEPRECATED => "E_DEPRECATED",
        E_USER_DEPRECATED => "E_USER_DEPRECATED",
        E_ALL => "E_ALL"
];

	echo $exceptions["1"];
	$code = 256; // error constant
	echo $exceptions[$code];
} */

/*
 * error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
*/

include_once './passwd.php';

if ( INSTALL_USER !== '' || INSTALL_PASSWD !== '' ) {
	if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
		header( 'WWW-Authenticate: Basic realm="XOOPSCube Installer"' );
		header( 'HTTP/1.0 401 Unauthorized' );
		echo 'You can not access this XOOPSCube installer.';
		exit;
	} else {
		if ( INSTALL_USER !== '' && INSTALL_USER != $_SERVER['PHP_AUTH_USER'] ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			echo 'You can not access this XOOPSCube installer.';
			exit;
		}
		if ( INSTALL_PASSWD !== $_SERVER['PHP_AUTH_PW'] ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			echo 'You can not access this XOOPSCube installer.';
			exit;
		}
	}
}


include_once './class/textsanitizer.php';

$myts = TextSanitizer::getInstance();

if ( isset( $_POST ) ) {
	foreach ( $_POST as $k => $v ) {
		$$k = $myts->stripSlashesGPC( $v );
	}
}


include_once './include/functions.php';

$language = getLanguage();

include_once './language/' . $language . '/install.php';
include_once '../language/' . $language . '/timezone.php';

const _OKIMG = '<img src="img/yes.svg" alt="OK">';
const _NGIMG = '<img src="img/no.svg" alt="NG">';


include_once './class/simplewizard.php';

$wizard = new SimpleWizard();
$wizard->setBaseTemplate( './install_tpl.php' );
$wizard->setTemplatePath( './templates' );

$wizardSeq = new SimpleWizardSequence();

$wizardSeq->add( 'langselect', _INSTALL_L0, 'start', _INSTALL_L80 );
$wizardSeq->add( 'start', _INSTALL_L0, 'modcheck', _INSTALL_L81 );
$wizardSeq->add( 'modcheck', _INSTALL_L82, 'dbform', _INSTALL_L89);
$wizardSeq->add( 'dbform', _INSTALL_L90, 'dbconfirm', _INSTALL_L91 );
$wizardSeq->add( 'dbconfirm', _INSTALL_L53, 'dbsave', _INSTALL_L92, '', _INSTALL_L93 );
$wizardSeq->add( 'dbsave', _INSTALL_L92, 'modcheck_trust', _INSTALL_L166 );
$wizardSeq->add( 'modcheck_trust', _INSTALL_L167, 'mainfile', _INSTALL_L94 );
$wizardSeq->add( 'mainfile', _INSTALL_L94, 'initial', _INSTALL_L102, 'start', _INSTALL_L103 );
$wizardSeq->add( 'initial', _INSTALL_L102, 'checkDB', _INSTALL_L104, 'start', _INSTALL_L103 );
$wizardSeq->add( 'checkDB', _INSTALL_L104, 'createDB', _INSTALL_L105, 'start', _INSTALL_L103, true );
$wizardSeq->add( 'createDB', _INSTALL_L105, 'checkDB', _INSTALL_L104 );
$wizardSeq->add( 'createTables', _INSTALL_L40, 'siteInit', _INSTALL_L112 );
$wizardSeq->add( 'siteInit', _INSTALL_L112, 'insertData', _INSTALL_L116 );
$wizardSeq->add( 'insertData', _INSTALL_L116, 'finish', _INSTALL_L117 );
$wizardSeq->add( 'finish', _INSTALL_L32, 'nextStep', _INSTALL_L210 );

if ( file_exists( './custom/custom.inc.php' ) ) {
	include './custom/custom.inc.php';
}

// options for mainfile.php
$xoopsOption['nocommon'] = true;
const XOOPS_INSTALL = 1;

if ( ! empty( $_POST['op'] ) ) {
	$op = $_POST['op'];
} elseif ( ! empty( $_GET['op'] ) ) {
	$op = $_GET['op'];
} else {
	$op = 'langselect';
}
$wizard->setOp( $op );

$op           = basename( $op );
$fname        = './wizards/install_' . $op . '.inc.php';
$custom_fname = './custom/install_' . $op . '.inc.php';

if ( file_exists( $fname ) ) {
	include $fname;
} elseif ( file_exists( $custom_fname ) ) {
	include $custom_fname;
} else {
	$wizard->render();
}
