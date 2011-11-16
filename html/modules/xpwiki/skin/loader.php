<?php
//
// Created on 2006/10/25 by nao-pon http://hypweb.net/
// $Id: loader.php,v 1.3 2011/11/16 12:57:36 nao-pon Exp $
//

define('_LEGACY_PREVENT_LOAD_CORE_', TRUE); // for XOOPS Cube Legacy
$xoopsOption['nocommon'] = TRUE;

define('PROTECTOR_SKIP_DOS_CHECK', TRUE);
define('BIGUMBRELLA_DISABLED', TRUE);

require '../../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' ) ;

require '../mytrustdirname.php' ; // set $mytrustdirname

$skin_dirname = dirname(__FILE__);

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/loader.php' ;
?>