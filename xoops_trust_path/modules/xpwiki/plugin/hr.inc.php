<?php
class xpwiki_plugin_hr extends xpwiki_plugin {
	function plugin_hr_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: hr.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Horizontal rule plugin
	
	function plugin_hr_convert()
	{
		return '<hr class="short_line" />';
	}
}
?>