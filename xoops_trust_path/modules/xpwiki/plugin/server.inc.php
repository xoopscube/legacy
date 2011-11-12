<?php
// $Id: server.inc.php,v 1.2 2007/11/30 05:02:35 nao-pon Exp $
//
// Server information plugin
// by Reimy http://pukiwiki.reimy.com/

class xpwiki_plugin_server extends xpwiki_plugin {
	function plugin_server_init () {

		foreach (array('SERVER_ADMIN', 'SERVER_NAME',
			'SERVER_PORT', 'SERVER_SOFTWARE') as $key) {
			$this->$key = isset($_SERVER[$key]) ? $_SERVER[$key] : '';
		}
	}
	
	function plugin_server_convert()
	{
	
		if ($this->cont['PKWK_SAFE_MODE']) return ''; // Show nothing
	
		return '<dl>' . "\n" .
		'<dt>Server Name</dt>'     . '<dd>' . $this->SERVER_NAME . '</dd>' . "\n" .
		'<dt>Server Software</dt>' . '<dd>' . $this->SERVER_SOFTWARE . '</dd>' . "\n" .
		'<dt>Server Admin</dt>'    . '<dd>' .
			'<a href="mailto:' . $this->SERVER_ADMIN . '">' .
			$this->SERVER_ADMIN . '</a></dd>' . "\n" .
		'</dl>' . "\n";
	}
}
?>