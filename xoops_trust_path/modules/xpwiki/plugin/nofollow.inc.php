<?php
class xpwiki_plugin_nofollow extends xpwiki_plugin {
	function plugin_nofollow_init () {



	}
	// $Id: nofollow.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	// Copyright (C) 2005 PukiWiki Developers Team
	// License: The same as PukiWiki
	//
	// NoFollow plugin
	
	// Output contents with "nofollow,noindex" option
	function plugin_nofollow_convert()
	{
	//	global $vars, $nofollow;
	
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
	
		if($this->func->is_freeze($page)) $this->root->nofollow = 1;
	
		return '';
	}
}
?>