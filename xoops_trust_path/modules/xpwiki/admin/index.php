<?php

include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";
$xw =& XpWiki::getInitedSingleton($mydirname);

if (!$xw->func->get_pgid_by_name($xw->root->defaultpage)) {
	$xw->func->send_location('', '', $xw->cont['HOME_URL'] . '?cmd=dbsync');
}

xoops_cp_header() ;

include dirname(__FILE__).'/mymenu.php' ;

xoops_cp_footer() ;

?>