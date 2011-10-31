<?php
/*
 * Created on 2008/03/05 by nao-pon http://hypweb.net/
 * $Id: autolink.inc.php,v 1.1 2008/03/06 23:44:43 nao-pon Exp $
 */

class xpwiki_plugin_autolink extends xpwiki_plugin {
	function plugin_autolink_convert () {
		$options = array(
			'on'=>false,
			'off'=>false,
		);
		// Option analysis
		$args = func_get_args();
		$this->fetch_options($options, $args);
		if ($options['on']) {
			return '<!--/NA-->';
		} else if ($options['off']) {
			return '<!--NA-->';
		} else {
			return '';
		}
	}
}
?>