<?php
/*
 * Created on 2008/07/24 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: gate.php,v 1.1 2008/07/29 14:35:40 nao-pon Exp $
 */

$hypGateOption['nocommonAllowWays'] = array('imgconv');
$hypGateOption['nodosAllowWays'] = array('imgconv');
$hypGateOption['noumbAllowWays'] = array('imgconv');

$way = (isset($_GET['way']))? $_GET['way'] : ((isset($_POST['way']))? $_POST['way'] : '');
$way = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $way);

if ($hypGateOption['xmode']) {
	if (!in_array($way, $hypGateOption['nocommonAllowWays'])) hyp_Gate_goOut('Bad request.');
}

if ($hypGateOption['nodos']) {
	if (!in_array($way, $hypGateOption['nodosAllowWays'])) hyp_Gate_goOut('Bad request.');
}

if ($hypGateOption['noumb']) {
	if (!in_array($way, $hypGateOption['noumbAllowWays'])) hyp_Gate_goOut('Bad request.');
}

$cacheurl = $rooturl . '/class/hyp_common/cache';
$cachepath = $rootpath . '/class/hyp_common/cache';

$file_php = dirname(__FILE__) . '/' . $way . '.php';
if (file_exists($file_php)) {
	include $file_php;
} else {
	hyp_Gate_goOut('File not found.');
}

function hyp_Gate_goOut($str = '') {
	error_reporting(0);
	while( ob_get_level() ) {
		ob_end_clean() ;
	}
	header("HTTP/1.0 404 Not Found");
	exit($str);
}
