<?php
class xpwiki_plugin_lastmod extends xpwiki_plugin {
	function plugin_lastmod_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: lastmod.inc.php,v 1.2 2007/09/19 11:27:15 nao-pon Exp $
	//
	// Lastmod plugin - Show lastmodifled date of the page
	// Originally written by Reimy, 2003
	
	function plugin_lastmod_inline()
	{
	//	global $vars, $WikiName, $BracketName;
	
		$args = func_get_args();
		$page = $args[0];
	
		if ($page === ''){
			$page = $this->root->vars['page']; // Default: page itself
		} else {
			if (preg_match("/^({$this->root->WikiName}|{$this->root->BracketName})$/", $this->func->strip_bracket($page))) {
				$page = $this->func->get_fullname($this->func->strip_bracket($page), $this->root->vars['page']);
			} else {
				return FALSE;
			}
		}
		if (! $this->func->is_page($page)) return FALSE;
	
		return $this->func->format_date($this->func->get_filetime($page));
	}
}
?>