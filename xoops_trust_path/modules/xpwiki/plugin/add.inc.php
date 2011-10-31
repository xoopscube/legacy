<?php
class xpwiki_plugin_add extends xpwiki_plugin {
	function plugin_add_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: add.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Add plugin - Append new text below/above existing page
	// Usage: cmd=add&page=pagename
	
	function plugin_add_action()
	{
	//	global $get, $post, $vars, $_title_add, $_msg_add;
	
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
	
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$this->func->check_editable($page);
	
		$this->root->get['add'] = $this->root->post['add'] = $this->root->vars['add'] = TRUE;
		return array(
			'msg'  => $this->root->_title_add,
		'body' =>
				'<ul>' . "\n" .
			' <li>' . $this->root->_msg_add . '</li>' . "\n" .
			'</ul>' . "\n" .
			$this->func->edit_form($page, '')
			);
	}
}
?>