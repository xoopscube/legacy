<?php

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
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

if( @$_GET['mode'] == 'admin' ) {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin.php' ;
} else {
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

