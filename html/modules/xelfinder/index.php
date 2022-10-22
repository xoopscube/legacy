<?php

// fix IIS PATH_INFO
if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
	$_SERVER['PATH_INFO'] = preg_replace('/^'.preg_quote($_SERVER['SCRIPT_NAME']).'/', '', $_SERVER['PATH_INFO']);
}

if ( ( isset($_GET['page']) && ($_GET['page'] === 'view' || $_GET['page'] === 'tmb' ) )
        ||
     ( isset($_SERVER['PATH_INFO']) && preg_match('#^/(?:view|tmb)/#', $_SERVER['PATH_INFO'] ) )
   ) {
	define('PROTECTOR_SKIP_DOS_CHECK', TRUE);
	define('BIGUMBRELLA_DISABLED', TRUE);
	define('HYP_COMMON_SKIP_POST_FILTER', TRUE);
	define('PROTECTOR_SKIP_FILESCHECKER' , 1 );
}

require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	die( 'set XOOPS_TRUST_PATH in mainfile.php' );
}

$mydirname = basename( __DIR__ ) ;
$mydirpath = __DIR__;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

if( @$_GET['mode'] == 'admin' ) {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin.php' ;
} else {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

