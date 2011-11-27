<?php
class xpwiki_plugin_calendar_read extends xpwiki_plugin {
	function plugin_calendar_read_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: calendar_read.inc.php,v 1.2 2011/11/26 12:03:10 nao-pon Exp $
	// Copyright (C)
	//   2003,2005 PukiWiki Developers Team
	//   2001-2002 Originally written by yu-ji
	// License: GPL v2 or (at your option) any later version
	//
	// Calendar_read plugin (needs calendar plugin)
	
	function plugin_calendar_read_convert()
	{
	//	global $command;
	
		if (! is_file($this->cont['PLUGIN_DIR'] . 'calendar.inc.php')) return FALSE;
	
		require_once $this->cont['PLUGIN_DIR'].'calendar.inc.php';
		if (! function_exists('plugin_calendar_convert')) return FALSE;
	
		$this->root->command = 'read';
		$args = func_num_args() ? func_get_args() : array();
		$_plugin =& $this->func->get_plugin_instance("calendar");
		return call_user_func_array (array(& $_plugin, "plugin_calendar_convert"), $args);
	}
}
?>