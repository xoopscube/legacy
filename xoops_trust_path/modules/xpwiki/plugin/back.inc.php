<?php
class xpwiki_plugin_back extends xpwiki_plugin {
	function plugin_back_init () {


	// $Id: back.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	// Copyright (C)
	//   2003-2004 PukiWiki Developers Team
	//   2002      Katsumi Saito <katsumi@jo1upk.ymt.prug.or.jp>
	//
	// back plugin
	
	// Allow specifying back link by page name and anchor, or
	// by relative or site-abusolute path
		$this->cont['PLUGIN_BACK_ALLOW_PAGELINK'] =  $this->cont['PKWK_SAFE_MODE']; // FALSE(Compat), TRUE
	
	// Allow JavaScript (Compat)
		$this->cont['PLUGIN_BACK_ALLOW_JAVASCRIPT'] =  TRUE; // TRUE(Compat), FALSE, $this->cont['PKWK_ALLOW_JAVASCRIPT']
	
	// ----
		$this->cont['PLUGIN_BACK_USAGE'] =  '#back([text],[center|left|right][,0(no hr)[,Page-or-URI-to-back]])';

	}
	function plugin_back_convert()
	{
	//	global $_msg_back_word, $script;
	
		if (func_num_args() > 4) return $this->cont['PLUGIN_BACK_USAGE'];
		list($word, $align, $hr, $href) = array_pad(func_get_args(), 4, '');
	
		$word = trim($word);
		$word = ($word == '') ? $this->root->_msg_back_word : htmlspecialchars($word);
	
		$align = strtolower(trim($align));
		switch($align){
		case ''      : $align = 'center';
		               /*FALLTHROUGH*/
		case 'center': /*FALLTHROUGH*/
		case 'left'  : /*FALLTHROUGH*/
		case 'right' : break;
		default      : return $this->cont['PLUGIN_BACK_USAGE'];
		}
	
		$hr = (trim($hr) != '0') ? '<hr class="full_hr" />' . "\n" : '';
	
		$link = TRUE;
		$href = trim($href);
		if ($href != '') {
			if ($this->cont['PLUGIN_BACK_ALLOW_PAGELINK']) {
				if ($this->func->is_url($href)) {
					$href = rawurlencode($href);
				} else {
					$array = $this->func->anchor_explode($href);
					$array[0] = rawurlencode($array[0]);
					$array[1] = ($array[1] != '') ? '#' . rawurlencode($array[1]) : '';
					$href = $this->root->script . '?' . $array[0] .  $array[1];
					$link = $this->func->is_page($array[0]);
				}
			} else {
				$href = rawurlencode($href);
			}
		} else {
			if (! $this->cont['PLUGIN_BACK_ALLOW_JAVASCRIPT'])
				return $this->cont['PLUGIN_BACK_USAGE'] . ': Set a page name or an URI';
			$href  = 'javascript:history.go(-1)';
		}
	
		if($link){
			// Normal link
			return $hr . '<div style="text-align:' . $align . '">' .
			'[ <a href="' . $href . '">' . $word . '</a> ]</div>' . "\n";
		} else {
			// Dangling link
			return $hr . '<div style="text-align:' . $align . '">' .
			'[ <span class="noexists">' . $word . '<a href="' . $href .
			'">?</a></span> ]</div>' . "\n";
		}
	}
}
?>