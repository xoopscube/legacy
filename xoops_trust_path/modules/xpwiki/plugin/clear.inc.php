<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: clear.inc.php,v 1.2 2011/11/22 09:09:42 nao-pon Exp $
//
// Clear plugin - inserts a CSS class 'clear', to set 'clear:both'


class xpwiki_plugin_clear extends xpwiki_plugin {
	function plugin_clear_init () {

	}

	function plugin_clear_convert() {
		list($side) = func_get_args();
		$side = strtolower($side);
		$class = 'clear';
		if (in_array($side, array('left', 'right'))) {
			$class .= '_'.$side;
		}
		return '<div class="'.$class .'"></div>';
	}
}
