<?php
//
// $Id: renderattach.inc.php,v 1.2 2008/01/03 12:31:49 nao-pon Exp $
//
class xpwiki_plugin_renderattach extends xpwiki_plugin {
	function plugin_renderattach_init () {
		$this->render_attach = $this->root->render_attach;
	}
	
	function plugin_renderattach_convert()
	{
		$args = func_get_args();
		$page = @ $args[0];
		if ($this->func->is_page($this->render_attach . '/' . $page)) {
			$this->root->render_attach = $this->render_attach . '/' . $page;
		}
		return '';
	}
}
?>