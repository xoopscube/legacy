<?php
/*
 * Created on 2008/12/06 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: sub.inc.php,v 1.2 2009/04/04 07:10:57 nao-pon Exp $
 */

class xpwiki_plugin_sub extends xpwiki_plugin {
	function plugin_sub_inline() {
		$args = func_get_args();
		$body = array_pop($args);
		if ($body === ''){
			if (isset($args[0])) {
				$body = htmlspecialchars($args[0]);
			} else {
				return FALSE;
			}
		}
		return '<sub>' . $body . '</sub>';
	}
}
