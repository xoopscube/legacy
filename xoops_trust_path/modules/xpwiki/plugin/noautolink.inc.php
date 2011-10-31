<?php
/*
 * Created on 2008/04/25 by nao-pon http://hypweb.net/
 * $Id: noautolink.inc.php,v 1.1 2008/04/25 12:13:18 nao-pon Exp $
 */

class xpwiki_plugin_noautolink extends xpwiki_plugin {
	function plugin_noautolink_convert() {
		$this->root->autolink = 0;
		$this->root->ext_autolinks = array();
		return '';
	}
}
?>