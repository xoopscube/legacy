<?php
class xpwiki_plugin_interwiki extends xpwiki_plugin {
	function plugin_interwiki_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: interwiki.inc.php,v 1.3 2008/01/29 23:54:36 nao-pon Exp $
	//
	// InterWiki redirection plugin (OBSOLETE)
	
	function plugin_interwiki_action()
	{
	//	global $vars, $InterWikiName;
	
		if ($this->cont['PKWK_SAFE_MODE']) $this->func->die_message('InterWiki plugin is not allowed');
	
		$match = array();
		if (! preg_match("/^{$this->root->InterWikiName}$/", $this->root->vars['page'], $match))
			return $this->plugin_interwiki_invalid();
	
		$url = $this->func->get_interwiki_url($match[2], $match[3]);
		if ($url === FALSE) return $this->plugin_interwiki_invalid();
	
		$this->func->send_location('', '', $url);
	}
	
	function plugin_interwiki_invalid()
	{
		return array(
			'msg'  => $this->root->_title_invalidiwn,
			'body' => str_replace(array('$1', '$2'),
						array(htmlspecialchars($this->root->vars['page']),
							$this->func->make_pagelink('InterWikiName')
						),$this->root->_msg_invalidiwn)
		);
	}
}
?>