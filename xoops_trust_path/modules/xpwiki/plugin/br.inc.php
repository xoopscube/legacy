<?php
class xpwiki_plugin_br extends xpwiki_plugin {
	function plugin_br_init () {


	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: br.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Forcing a line break plugin
	
	// Escape using <br> in <blockquote> (BugTrack/583)
		$this->cont['PLUGIN_BR_ESCAPE_BLOCKQUOTE'] =  1;
	
	// ----
	
		$this->cont['PLUGIN_BR_TAG'] =  '<br class="spacer" />';

	}
	
	function plugin_br_convert()
	{
		if ($this->cont['PLUGIN_BR_ESCAPE_BLOCKQUOTE']) {
			return '<div class="spacer">&nbsp;</div>';
		} else {
			return $this->cont['PLUGIN_BR_TAG'];
		}
	}
	
	function plugin_br_inline()
	{
		return $this->cont['PLUGIN_BR_TAG'];
	}
}
?>