<?php
/*
 * Created on 2008/02/07 by nao-pon http://hypweb.net/
 * $Id: ajaxtree.inc.php,v 1.3 2010/01/08 14:02:14 nao-pon Exp $
 */

function xpwiki_onPageWriteAfter_ajaxtree (&$xpwiki_func, &$page, &$postdata, &$notimestamp, &$mode, &$diffdata) {

	// This block always execute.

	// Get plugin instance
	$plugin = & $xpwiki_func->get_plugin_instance('ajaxtree');

	$GLOBALS['xpwiki_cache_reflash_functions']['ajaxtree'] = array(
		'name' => array($plugin, 'plugin_ajaxtree_write_after'),
		'arg'  => $page
	);
}
?>