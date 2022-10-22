<?php

if (! defined('XOOPS_ROOT_PATH')) {
	include dirname( __FILE__, 3 ) . '/mainfile.php';
}

if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	die( 'set XOOPS_TRUST_PATH in mainfile.php' );
}

$mydirname = basename( __DIR__ ) ;
$mydirpath = __DIR__;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/manager.php' ;
