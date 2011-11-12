<?php
/*
 * Created on 2007/06/29 by nao-pon http://hypweb.net/
 * $Id: gate.php,v 1.1 2008/11/18 00:30:53 nao-pon Exp $
 */

$xwGateOption = array();
$xwGateOption['xmode'] = (isset($_GET['_xmode']))? intval($_GET['_xmode']) : ((isset($_POST['_xmode']))? intval($_POST['_xmode']) : 0);
$xwGateOption['nodos'] = (isset($_GET['_nodos']))? 1 : ((isset($_POST['_nodos']))? 1 : 0);
$xwGateOption['noumb'] = (isset($_GET['_noumb']))? 1 : ((isset($_POST['_noumb']))? 1 : 0);
$xwGateOption['hypmode'] = (isset($_GET['_hypmode']))? intval($_GET['_hypmode']) : ((isset($_POST['_hypmode']))? intval($_POST['_hypmode']) : 0);

// for XOOPS core
if ($xwGateOption['xmode'] === 2) {
	define('_LEGACY_PREVENT_LOAD_CORE_', TRUE);
	$xoopsOption['nocommon'] = TRUE;
} else if ($xwGateOption['xmode'] === 1) {
	define('_LEGACY_PREVENT_EXEC_COMMON_', TRUE);
	$xoopsOption['nocommon'] = TRUE;	
}

// for protector
if ($xwGateOption['nodos']) { define('PROTECTOR_SKIP_DOS_CHECK', TRUE); }
if ($xwGateOption['noumb']) { define('BIGUMBRELLA_DISABLED', TRUE); }

if ($xwGateOption['hypmode'] === 1) { define('HYP_COMMON_SKIP_POST_FILTER', TRUE); }

require '../../mainfile.php' ;
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$mydirname = basename( dirname( __FILE__ ) ) ;
$mydirpath = dirname( __FILE__ ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname
require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/gate.php' ;
?>