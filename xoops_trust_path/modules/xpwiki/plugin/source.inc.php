<?php
class xpwiki_plugin_source extends xpwiki_plugin {
	function plugin_source_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: source.inc.php,v 1.2 2007/09/19 12:10:10 nao-pon Exp $
	//
	// Source plugin
	
	// Output source text of the page
	function plugin_source_action()
	{
	//	global $vars, $_source_messages;
	
		if ($this->cont['PKWK_SAFE_MODE']) $this->func->die_message('PKWK_SAFE_MODE prohibits this');
	
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$this->root->vars['refer'] = $page;
	
		if (! $this->func->is_page($page) || ! $this->func->check_readable($page, false, false))
			return array('msg' => $this->root->_source_messages['msg_notfound'],
			'body' => $this->root->_source_messages['err_notfound']);
	
		return array('msg' => $this->root->_source_messages['msg_title'],
		'body' => '<pre id="source">' .
		htmlspecialchars($this->func->get_source($page, TRUE, TRUE)) . '</pre>');
	}
}
?>