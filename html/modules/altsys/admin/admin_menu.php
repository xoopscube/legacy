<?php

if (! defined('XOOPS_TRUST_PATH')) {
    die('set XOOPS_TRUST_PATH in mainfile.php') ;
}

$mydirname = basename(dirname(__DIR__)) ;
$mydirpath = dirname(__DIR__) ;
// require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname
$mytrustdirname = 'altsys' ;

require XOOPS_TRUST_PATH.'/libs/'.$mytrustdirname.'/admin_menu.php' ;
