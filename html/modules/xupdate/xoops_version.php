<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

$mydirname = $myDirName = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/xoops_version.php' ;

?>