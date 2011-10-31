<?php
/*
 * Created on 2008/04/24 by nao-pon http://hypweb.net/
 * $Id: nocontents.inc.php,v 1.2 2008/04/27 12:04:34 nao-pon Exp $
 */
class xpwiki_plugin_nocontents extends xpwiki_plugin {
	function plugin_nocontents_convert() {
		$this->root->rtf['contents_converted'][$this->root->rtf['contntId']] = TRUE;
		return '';
	}
}
?>