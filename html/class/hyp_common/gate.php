<?php
/*
 * Created on 2008/07/24 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: gate.php,v 1.1 2008/07/29 14:35:40 nao-pon Exp $
 */

$hypGateOption = array();
$hypGateOption['xmode'] = (isset($_GET['_x']))? intval($_GET['_x']) : ((isset($_POST['_x']))? intval($_POST['_x']) : 2);
$hypGateOption['nodos'] = (isset($_GET['_d']))? 0 : ((isset($_POST['_d']))? 0 : 1);
$hypGateOption['noumb'] = (isset($_GET['_u']))? 0 : ((isset($_POST['_u']))? 0 : 1);

// for XOOPS core
if ($hypGateOption['xmode'] === 2) {
	define('_LEGACY_PREVENT_LOAD_CORE_', TRUE);
	$xoopsOption['nocommon'] = TRUE;
} else if ($hypGateOption['xmode'] === 1) {
	define('_LEGACY_PREVENT_EXEC_COMMON_', TRUE);
	$xoopsOption['nocommon'] = TRUE;	
}

// for protector
if ($hypGateOption['nodos']) { define('PROTECTOR_SKIP_DOS_CHECK', TRUE); }
if ($hypGateOption['noumb']) { define('BIGUMBRELLA_DISABLED', TRUE); }

include ('../../mainfile.php');
if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;

$trustpath = XOOPS_TRUST_PATH;
$rootpath = XOOPS_ROOT_PATH;
$rooturl = XOOPS_URL;

include($trustpath . '/class/hyp_common/ways/gate.php');
