<?php
/*
 * Created on 2008/01/16 by nao-pon http://hypweb.net/
 * $Id: ref.php,v 1.1 2008/01/16 05:17:54 nao-pon Exp $
 */

if (isset($_GET['page']) && isset($_GET['src'])) {
	$_GET['plugin'] = 'ref';
	require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/main.php' ;
}

xpWikiGate_goOut('Bad request.');
?>