<?php
class xpwiki_plugin_contents extends xpwiki_plugin {
	function plugin_contents_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: contents.inc.php,v 1.3 2008/04/27 12:04:34 nao-pon Exp $
	//
	
	function plugin_contents_convert()
	{
		$this->root->rtf['contents_converted'][$this->root->rtf['contntId']] = TRUE;
		// This character string is substituted later.
		return '<#_contents_>';
	}
}
?>