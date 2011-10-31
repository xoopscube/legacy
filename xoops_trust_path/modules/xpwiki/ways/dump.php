<?php
/*
 * Created on 2010/05/08 by nao-pon http://hypweb.net/
 * $Id: dump.php,v 1.1 2010/05/10 02:31:23 nao-pon Exp $
 */

if (isset($_GET['list']) || (isset($_GET['act']) && $_GET['act'] === 'maketar')) {
	$_GET['cmd'] = 'dump';
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

xpWikiGate_goOut('Bad request.');
?>