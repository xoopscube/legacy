<?php
/*
 * Created on 2007/06/02 by nao-pon http://hypweb.net/
 * $Id: render.inc.php,v 1.4 2007/09/21 06:14:07 nao-pon Exp $
 */

function xpwiki_onPageWriteAfter_render (&$xpwiki_func, &$page, &$postdata, &$notimestamp, &$mode) {

	if ( $mode !== 'update' ) {
		//// Clear cache render_*
		//$GLOBALS['xpwiki_cache_deletes'][$xpwiki_func->cont['RENDER_CACHE_DIR']][] = ($xpwiki_func->cont['RENDER_CACHE_DIR'] !== $xpwiki_func->cont['CACHE_DIR'])? '*' : 'render_*';

		// Touch time file.
		$xpwiki_func->pkwk_touch_file($xpwiki_func->cont['CACHE_DIR'] . 'pagemove.time');
	}
}

?>
