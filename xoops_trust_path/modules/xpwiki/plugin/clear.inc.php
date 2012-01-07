<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: clear.inc.php,v 1.3 2012/01/07 07:48:20 nao-pon Exp $
//
// Clear plugin - inserts a CSS class 'clear', to set 'clear:both'


class xpwiki_plugin_clear extends xpwiki_plugin {
	function plugin_clear_init () {

	}

	function plugin_clear_convert() {
		list($side) = array_pad(func_get_args(), 1, '');
		$side = strtolower($side);
		$class = 'clear';
		if (in_array($side, array('left', 'right'))) {
			$class .= '_'.$side;
		}
		return '<div class="'.$class .'"></div>';
	}
}
