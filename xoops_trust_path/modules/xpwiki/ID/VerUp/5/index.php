<?php
//
// Created on 2006/10/18 by nao-pon http://hypweb.net/
// $Id: index.php,v 1.1 2011/11/18 14:33:49 nao-pon Exp $
//

define( 'PROTECTOR_SKIP_FILESCHECKER' , 1 );

require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
?>