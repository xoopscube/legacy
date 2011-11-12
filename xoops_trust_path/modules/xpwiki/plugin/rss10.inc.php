<?php
class xpwiki_plugin_rss10 extends xpwiki_plugin {
	function plugin_rss10_init () {



	}
	// RSS 1.0 plugin - had been merged into rss plugin
	// $Id: rss10.inc.php,v 1.3 2011/07/29 07:14:25 nao-pon Exp $

	function plugin_rss10_action()
	{
		$this->func->clear_output_buffer();
		$this->func->pkwk_headers_sent();
		header('Status: 301 Moved Permanently');
		header('Location: ' . $this->func->get_script_uri() . '?cmd=rss&ver=1.0'); // HTTP
		exit;
	}
}
?>