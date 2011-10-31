<?php
/*
 * Created on 2009/11/09 by nao-pon http://xoops.hypweb.net/
 * $Id: twitter.inc.php,v 1.1 2009/11/17 09:23:47 nao-pon Exp $
 */

class xpwiki_plugin_twitter extends xpwiki_plugin {
	function plugin_twitter_init() {
		$this->usage = 'Usage: &#38;twitter(UserName){Show text};';
	}

	function plugin_twitter_inline() {
		$args = func_get_args();
		$alias = array_pop($args);
		if ($args) {
			$name = array_pop($args);
			if (! $alias) $alias = htmlspecialchars($name);
			return '<a href="http://twitter.com/' . urlencode($name) . '">' . $alias . '</a>';
		} else {
			return $this->usage;
		}
	}
}
