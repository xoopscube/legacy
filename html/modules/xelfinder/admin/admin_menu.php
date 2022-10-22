<?php

if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	die( 'set XOOPS_TRUST_PATH into mainfile.php' );
}

$mydirname = basename( dirname( __FILE__, 2 ) ) ;
$mydirpath = dirname( __FILE__, 2 );

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin_menu.php' ;
