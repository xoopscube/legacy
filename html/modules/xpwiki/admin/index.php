<?php
//
// Created on 2006/10/18 by nao-pon http://hypweb.net/
// $Id: index.php,v 1.1 2006/10/18 05:07:15 nao-pon Exp $
//

require '../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$mydirpath = dirname( dirname( __FILE__ ) );
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin.php' ;
?>