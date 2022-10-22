<?php

// for compatibility
require '../../mainfile.php' ;

if( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	die( 'set XOOPS_TRUST_PATH in mainfile.php' );
}

$mydirname = basename(__DIR__) ;
$mydirpath = __DIR__;

require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

$_GET['page'] = 'viewtopics' ;
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;


