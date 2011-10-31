<?php
/*
 * Created on 2007/05/22 by nao-pon http://hypweb.net/
 * $Id: api.inc.php,v 1.5 2008/03/06 23:36:22 nao-pon Exp $
 */

function xpwiki_onPageWriteAfter_api (&$xpwiki_func, &$page, &$postdata, &$notimestamp, &$mode) {

	if ( $mode !== 'update' ) {
		// Clear cache *.autolink.api
		$GLOBALS['xpwiki_cache_deletes'][$xpwiki_func->cont['CACHE_DIR']]['api'] = '*.autolink.api';
	}
}

?>
