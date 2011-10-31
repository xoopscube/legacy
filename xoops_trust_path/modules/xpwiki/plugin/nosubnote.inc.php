<?php
//
// Created on 2006/11/24 by nao-pon http://hypweb.net/
// $Id: nosubnote.inc.php,v 1.1 2009/03/13 08:18:49 nao-pon Exp $
//
class xpwiki_plugin_nosubnote extends xpwiki_plugin {
	function plugin_nosubnote_convert()
	{
		$this->root->nonflag['subnote'] = TRUE;
		return '';
	}
}
?>