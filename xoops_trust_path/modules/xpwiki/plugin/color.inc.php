<?php
class xpwiki_plugin_color extends xpwiki_plugin {
	function plugin_color_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: color.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Text color plugin
	
	// Allow CSS instead of <font> tag
	// NOTE: <font> tag become invalid from XHTML 1.1
		$this->cont['PLUGIN_COLOR_ALLOW_CSS'] =  TRUE; // TRUE, FALSE
	
	// ----
		$this->cont['PLUGIN_COLOR_USAGE'] =  '&color(foreground[,background]){text};';
		$this->cont['PLUGIN_COLOR_REGEX'] =  '/^(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i';

	}
	
	function plugin_color_inline()
	{
	//	global $pkwk_dtd;
	
		$args = func_get_args();
		$text = $this->func->strip_autolink(array_pop($args)); // Already htmlspecialchars(text)
	
		list($color, $bgcolor) = array_pad($args, 2, '');
		if ($color != '' && $bgcolor != '' && $text == '') {
			// Maybe the old style: '&color(foreground,text);'
			$text    = htmlspecialchars($bgcolor);
			$bgcolor = '';
		}
		if (($color == '' && $bgcolor == '') || $text == '' || func_num_args() > 3)
			return $this->cont['PLUGIN_COLOR_USAGE'];
	
		// Invalid color
		foreach(array($color, $bgcolor) as $col){
			if ($col != '' && ! preg_match($this->cont['PLUGIN_COLOR_REGEX'], $col))
				return '&color():Invalid color: ' . htmlspecialchars($col) . ';';
		}
	
		if ($this->cont['PLUGIN_COLOR_ALLOW_CSS'] === TRUE || ! isset($this->root->pkwk_dtd) || $this->root->pkwk_dtd == $this->cont['PKWK_DTD_XHTML_1_1']) {
			$delimiter = '';
			if ($color != '' && $bgcolor != '') $delimiter = '; ';
			if ($color   != '') $color   = 'color:' . $color;
			if ($bgcolor != '') $bgcolor = 'background-color:' . $bgcolor;
			return '<span style="' . $color . $delimiter . $bgcolor . '">' .
			$text . '</span>';
		} else {
			if ($bgcolor != '') return '&color(): bgcolor (with CSS) not allowed;';
			return '<font color="' . $color . '">' . $text . '</font>';
		}
	}
}
?>