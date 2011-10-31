<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: norelated.inc.php,v 1.2 2008/02/08 02:55:52 nao-pon Exp $
//
// norelated plugin
// - Stop showing related link automatically if $related_link = 1

class xpwiki_plugin_norelated extends xpwiki_plugin {
	function plugin_norelated_convert()
	{
		$this->root->nonflag['related'] = TRUE;
		return '';
	}
}
?>