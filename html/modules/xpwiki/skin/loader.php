<?php
//
// Created on 2006/10/25 by nao-pon http://hypweb.net/
// $Id: loader.php,v 1.2 2006/11/24 13:42:43 nao-pon Exp $
//

define('_LEGACY_PREVENT_LOAD_CORE_', TRUE); // for XOOPS Cube Legacy
$xoopsOption['nocommon'] = TRUE;
require '../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

require '../mytrustdirname.php' ; // set $mytrustdirname

$skin_dirname = dirname(__FILE__);

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/loader.php' ;
?>