<?php

define('PROTECTOR_SKIP_DOS_CHECK', TRUE);
define('BIGUMBRELLA_DISABLED', TRUE);
define('HYP_COMMON_SKIP_POST_FILTER', TRUE);
define('PROTECTOR_SKIP_FILESCHECKER' , 1 );

if (isset($_POST['sessionId'])) {
	session_id($_POST['sessionId']);
}

include '../../mainfile.php';

if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	die( 'set XOOPS_TRUST_PATH in mainfile.php' );
}

//error_reporting(0);

while(ob_get_level()) {
	if (! ob_end_clean()) {
		break;
	}
}

$mydirname = basename( __DIR__ ) ;
$mydirpath = __DIR__;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/connector.php' ;

