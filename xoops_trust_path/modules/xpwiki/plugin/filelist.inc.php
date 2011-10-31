<?php
class xpwiki_plugin_filelist extends xpwiki_plugin {
	function plugin_filelist_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: filelist.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Filelist plugin: redirect to list plugin
	// cmd=filelist
	
	function plugin_filelist_action()
	{
		return $this->func->do_plugin_action('list');
	}
}
?>