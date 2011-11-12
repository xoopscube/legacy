<?php
class xpwiki_plugin_ruby extends xpwiki_plugin {
	function plugin_ruby_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: ruby.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Ruby annotation plugin: Add a pronounciation into kanji-word or acronym(s)
	// See also about ruby: http://www.w3.org/TR/ruby/
	//
	// NOTE:
	//  Ruby tag works with MSIE only now,
	//  but readable for other browsers like: 'words(pronunciation)'
	
		$this->cont['PLUGIN_RUBY_USAGE'] =  '&ruby(pronunciation){words};';

	}
	
	function plugin_ruby_inline()
	{
		if (func_num_args() != 2) return $this->cont['PLUGIN_RUBY_USAGE'];
	
		list($ruby, $body) = func_get_args();
	
		// strip_htmltag() is just for avoiding AutoLink insertion
		$body = $this->func->strip_htmltag($body);
	
		if ($ruby == '' || $body == '') return $this->cont['PLUGIN_RUBY_USAGE'];
	
		return '<ruby><rb>' . $body . '</rb>' . '<rp>(</rp>' .
		'<rt>' .  htmlspecialchars($ruby) . '</rt>' . '<rp>)</rp>' .
		'</ruby>';
	}
}
?>