<?php
class xpwiki_plugin_size extends xpwiki_plugin {
	function plugin_size_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: size.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Text-size changing via CSS plugin
	
		$this->cont['PLUGIN_SIZE_MAX'] =  60; // px
		$this->cont['PLUGIN_SIZE_MIN'] =   8; // px
	
	// ----
		$this->cont['PLUGIN_SIZE_USAGE'] =  '&size(px){Text you want to change};';

	}
	
	function plugin_size_inline()
	{
		if (func_num_args() != 2) return $this->cont['PLUGIN_SIZE_USAGE'];
	
		list($size, $body) = func_get_args();
	
		// strip_autolink() is not needed for size plugin
		//$body = strip_htmltag($body);
		
		if ($size == '' || $body == '' || ! preg_match('/^\d+$/', $size))
			return $this->cont['PLUGIN_SIZE_USAGE'];
	
		$size = max($this->cont['PLUGIN_SIZE_MIN'], min($this->cont['PLUGIN_SIZE_MAX'], $size));
		return '<span style="font-size:' . $size .
		'px;display:inline-block;line-height:130%;text-indent:0px">' .
		$body . '</span>';
	}
}
?>