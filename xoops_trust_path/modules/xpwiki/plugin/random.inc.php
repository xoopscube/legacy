<?php
class xpwiki_plugin_random extends xpwiki_plugin {
	function plugin_random_init () {


	/////////////////////////////////////////////////
	// PukiWiki - Yet another WikiWikiWeb clone.
	//
	// $Id: random.inc.php,v 1.2 2007/09/19 11:27:15 nao-pon Exp $
	//
	
	/*
	 *プラグイン random
	  配下のページをランダムに表示する
	
	 *Usage
	  #random(メッセージ)
	
	 *パラメータ
	 -メッセージ~
	 リンクに表示する文字列
	
		 */

	}
	
	function plugin_random_convert()
	{
	//	global $script, $vars;
	
		$title = '[Random XpWikiLink]'; // default
		if (func_num_args()) {
			$args  = func_get_args();
			$title = $args[0];
		}
	
		return "<p><a href=\"{$this->root->script}?plugin=random&amp;refer=" .
		rawurlencode($this->root->vars['page']) . '">' .
		htmlspecialchars($title) . '</a></p>';
	}
	
	function plugin_random_action()
	{
	//	global $vars;
	
		$pattern = $this->func->strip_bracket($this->root->vars['refer']) . '/';
		$pages = array();
		foreach ($this->func->get_existpages() as $_page) {
			if (strpos($_page, $pattern) === 0)
				$pages[$_page] = $this->func->strip_bracket($_page);
		}
	
		srand((double)microtime() * 1000000);
		$page = array_rand($pages);
	
		if ($page !== '') $this->root->vars['refer'] = $page;
	
		return array('body'=>'','msg'=>'');
	}
}
?>