<?php
class xpwiki_plugin_yetlist extends xpwiki_plugin {
	function plugin_yetlist_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: yetlist.inc.php,v 1.4 2011/11/26 12:03:10 nao-pon Exp $
	// Copyright (C) 2001-2006 PukiWiki Developers Team
	// License: GPL v2 or (at your option) any later version
	//
	// Yet list plugin - Show a list of dangling links (not yet created)
	
	function plugin_yetlist_action()
	{
	//	global $_title_yetlist, $_err_notexist, $_symbol_noexists, $non_list;
	//	global $whatsdeleted;
	
		$retval = array('msg' => $this->root->_title_yetlist, 'body' => '');

		$yetlists = array();
		if (is_file($this->cont['CACHE_DIR']."yetlist.dat"))
		{
			$yetlists = unserialize(file_get_contents($this->cont['CACHE_DIR']."yetlist.dat"));
		}

		if (empty($yetlists)) {
			$retval['body'] = $this->root->_err_notexist;
			return $retval;
		}
	
		$empty = TRUE;
	
		// Load .ref files and Output
		$script      = $this->func->get_script_uri();
		$refer_regex = '/' . $this->root->non_list . '|^' . preg_quote($this->root->whatsdeleted, '/') . '$/S';
		ksort($yetlists, SORT_STRING);
		foreach ($yetlists as $page=>$refer) {

			if (! empty($refer)) {
				$empty = FALSE;
				$refer = array_unique($refer);
				sort($refer, SORT_STRING);
	
				$r_refer = '';
				$link_refs = array();
				foreach ($refer as $_refer) {
					$r_refer = rawurlencode($_refer);
					$link_refs[] = '<a href="' . $script . '?' . $r_refer . '">' .
					htmlspecialchars($_refer) . '</a>';
				}
				$link_ref = join(' ', $link_refs);
				unset($link_refs);
	
				$s_page = htmlspecialchars($page);
				if ($this->cont['PKWK_READONLY']) {
					$href = $s_page;
				} else {
					// Dangling link
					$href = '<span class="noexists">' . $s_page . '<a href="' .
					$script . '?cmd=edit&amp;page=' . rawurlencode($page) .
					'&amp;refer=' . $r_refer . '">' . $this->root->_symbol_noexists .
					'</a></span>';
				}
				$retval['body'] .= '<li>' . $href . ' <em>(' . $link_ref . ')</em></li>' . "\n";
			}
		}
	
		if ($empty) {
			$retval['body'] = $this->root->_err_notexist;
			return $retval;
		}
	
		if ($retval['body'] != '')
			$retval['body'] = '<ul>' . "\n" . $retval['body'] . '</ul>' . "\n";
	
		return $retval;
	}
}
?>