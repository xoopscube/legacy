<?php
class xpwiki_plugin_version extends xpwiki_plugin {
	function plugin_version_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: version.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Show PukiWiki version
	
	function plugin_version_convert()
	{
		if ($this->cont['PKWK_SAFE_MODE']) return ''; // Show nothing
	
		return '<p>' . $this->cont['S_VERSION'] . '</p>';
	}
	
	function plugin_version_inline()
	{
		if ($this->cont['PKWK_SAFE_MODE']) return ''; // Show nothing
	
		return $this->cont['S_VERSION'];
	}
}
?>