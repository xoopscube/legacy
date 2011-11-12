<?php
//
// Created on 2006/12/04 by nao-pon http://hypweb.net/
// $Id: rss.inc.php,v 1.4 2008/03/06 23:36:22 nao-pon Exp $
//

function xpwiki_onPageWriteAfter_rss(&$xpwiki_func, &$page, &$postdata, &$notimestamp, &$mode, &$diffdata) {
	// CACHE_DIR/plugin/*.rss ¤òºï½ü
	$GLOBALS['xpwiki_cache_deletes'][$xpwiki_func->cont['CACHE_DIR'].'plugin/']['rss'] = '*.rss';
}
?>