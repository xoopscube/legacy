<?php
class xpwiki_plugin_noheader extends xpwiki_plugin {
	function plugin_noheader_convert() {
		$this->func->add_tag_head('noheader.css');
		return '';
	}
}
?>