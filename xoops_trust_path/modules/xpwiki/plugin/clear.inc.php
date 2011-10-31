<?php
class xpwiki_plugin_clear extends xpwiki_plugin {
	function plugin_clear_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: clear.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Clear plugin - inserts a CSS class 'clear', to set 'clear:both'
	
	function plugin_clear_convert()
	{
		return '<div class="clear"></div>';
	}
}
?>