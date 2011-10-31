<?php
//
// Created on 2006/11/13 by nao-pon http://hypweb.net/
// $Id: xoops_uname.php,v 1.2 2007/06/29 08:41:21 nao-pon Exp $
//

define('_LEGACY_PREVENT_LOAD_CORE_', TRUE); // for XOOPS Cube Legacy 
$xoopsOption['nocommon'] = TRUE; // for XOOPS 2
define('PROTECTOR_SKIP_DOS_CHECK', TRUE); // for Protector

require '../../mainfile.php';

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/xoops_uname.php' ;
?>