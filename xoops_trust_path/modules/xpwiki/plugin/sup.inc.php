<?php
/*
 * Created on 2008/12/06 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: sup.inc.php,v 1.2 2009/04/04 07:10:57 nao-pon Exp $
 */

class xpwiki_plugin_sup extends xpwiki_plugin {
	function plugin_sup_inline() {
		$args = func_get_args();
		$body = array_pop($args);
		if ($body === ''){
			if (isset($args[0])) {
				$body = htmlspecialchars($args[0]);
			} else {
				return FALSE;
			}
		}
		return '<sup>' . $body . '</sup>';
	}
}
