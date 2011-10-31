<?php
/*
 * Created on 2008/01/16 by nao-pon http://hypweb.net/
 * $Id: attach.php,v 1.1 2008/01/16 05:17:54 nao-pon Exp $
 */

if (isset($_GET['openfile']) || (isset($_GET['pcmd']) && $_GET['pcmd'] === 'open')) {
	$_GET['plugin'] = 'attach';
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

xpWikiGate_goOut('Bad request.');
?>