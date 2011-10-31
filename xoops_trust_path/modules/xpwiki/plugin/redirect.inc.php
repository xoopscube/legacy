<?php
//
// Created on 2006/12/08 by nao-pon http://hypweb.net/
// $Id: redirect.inc.php,v 1.2 2007/07/31 03:03:38 nao-pon Exp $
//
class xpwiki_plugin_redirect extends xpwiki_plugin {
	function plugin_redirect_action()
	{
		if (empty($this->root->vars['to'])) return FALSE;
		$to = preg_replace('#^(\.*/)+#' , '', trim($this->root->vars['to']));
		$this->func->send_location('', '', $this->cont['ROOT_URL'].$to);
	}
}
?>