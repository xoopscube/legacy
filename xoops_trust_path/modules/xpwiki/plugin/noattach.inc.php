<?php
//
// Created on 2006/11/24 by nao-pon http://hypweb.net/
// $Id: noattach.inc.php,v 1.2 2008/02/08 02:55:52 nao-pon Exp $
//
class xpwiki_plugin_noattach extends xpwiki_plugin {
	function plugin_noattach_convert()
	{
		$this->root->nonflag['attach'] = TRUE;
		return '';
	}
}
?>