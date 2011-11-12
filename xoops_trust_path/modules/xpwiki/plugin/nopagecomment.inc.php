<?php
//
// Created on 2006/12/07 by nao-pon http://hypweb.net/
// $Id: nopagecomment.inc.php,v 1.2 2008/02/08 02:55:52 nao-pon Exp $
//
class xpwiki_plugin_nopagecomment extends xpwiki_plugin {
	function plugin_nopagecomment_convert()
	{
		$this->root->nonflag['pagecomment'] = TRUE;
		return '';
	}
}
?>