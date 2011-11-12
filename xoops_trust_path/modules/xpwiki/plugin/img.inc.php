<?php
class xpwiki_plugin_img extends xpwiki_plugin {
	function plugin_img_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: img.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Inline-image plugin
	
		$this->cont['PLUGIN_IMG_USAGE'] =  '#img(): Usage: (URI-to-image[,right[,clear]])<br />' . "\n";
		$this->cont['PLUGIN_IMG_CLEAR'] =  '<div style="clear:both"></div>' . "\n"; // Stop word-wrapping

	}
	
	// Output inline-image tag from a URI
	function plugin_img_convert()
	{
		if ($this->cont['PKWK_DISABLE_INLINE_IMAGE_FROM_URI'])
			return '#img(): PKWK_DISABLE_INLINE_IMAGE_FROM_URI prohibits this' .
			'<br>' . "\n";
	
		$args = func_get_args();
	
		// Check the 2nd argument first, for compatibility
		$arg = isset($args[1]) ? strtoupper($args[1]) : '';
		if ($arg == '' || $arg == 'L' || $arg == 'LEFT') {
			$align = 'left';
		} else if ($arg == 'R' || $arg == 'RIGHT') {
			$align = 'right';
		} else {
			// Stop word-wrapping only (Ugly but compatible)
			// Short usage: #img(,clear)
			return $this->cont['PLUGIN_IMG_CLEAR'];
		}
	
		$url = isset($args[0]) ? $args[0] : '';
		if (! $this->func->is_url($url) || ! preg_match('/\.(jpe?g|gif|png)$/i', $url))
			return $this->cont['PLUGIN_IMG_USAGE'];
	
		$arg = isset($args[2]) ? strtoupper($args[2]) : '';
		$clear = ($arg == 'C' || $arg == 'CLEAR') ? $this->cont['PLUGIN_IMG_CLEAR'] : '';
	
		return <<<EOD
<div style="float:$align;padding:.5em 1.5em .5em 1.5em">
 <img src="$url" alt="" />
</div>$clear
EOD;
	}
}
?>