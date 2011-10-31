<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: aname.inc.php,v 1.4 2007/11/08 08:30:05 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// aname plugin - Set various anchor tags
//   * With just an anchor id: <a id="key"></a>
//   * With a hyperlink to the anchor id: <a href="#key">string</a>
//   * With an anchor id and a link to the id itself: <a id="key" href="#key">string</a>
//
// NOTE: Use 'id="key"' instead of 'name="key"' at XHTML 1.1

class xpwiki_plugin_aname extends xpwiki_plugin {
	function plugin_aname_init () {
		// Check ID is unique or not (compatible: no-check)
		$this->cont['PLUGIN_ANAME_ID_MUST_UNIQUE'] =  0;
	
		// Max length of ID
		$this->cont['PLUGIN_ANAME_ID_MAX'] =    40;
	
		// Pattern of ID
		$this->cont['PLUGIN_ANAME_ID_REGEX'] =  '/^[A-Za-z][\w\-]*$/';

		// Maxmum image size(px)
		$this->max_image_size = 32;
	}
	
	// Show usage
	function plugin_aname_usage($convert = TRUE, $message = '')
	{
		if ($convert) {
			if ($message == '') {
				return '#aname(anchorID[[,super][,full][,noid],XpWikiLink title])' . '<br />';
			} else {
				return '#aname: ' . $message . '<br />';
			}
		} else {
			if ($message == '') {
				return '&amp;aname(anchorID[,super][,full][,noid]){[XpWikiLink title]};';
			} else {
				return '&amp;aname: ' . $message . ';';
			}
		}
	}
	
	// #aname
	function plugin_aname_convert()
	{
		$convert = TRUE;
	
		if (func_num_args() < 1)
			return $this->plugin_aname_usage($convert);
	
		return $this->plugin_aname_tag(func_get_args(), $convert);
	}
	
	// &aname;
	function plugin_aname_inline()
	{
		$convert = FALSE;
	
		if (func_num_args() < 2)
			return $this->plugin_aname_usage($convert);
	
		$args = func_get_args(); // ONE or more
		$body = $this->func->strip_htmltag(array_pop($args), FALSE); // Strip anchor tags only
		array_push($args, $body);
	
		return $this->plugin_aname_tag($args, $convert);
	}
	
	// Aname plugin itself
	function plugin_aname_tag($args = array(), $convert = TRUE)
	{
	//	global $vars;
	//	static $_id = array();
		static $_id = array();
		if (!isset($_id[$this->xpwiki->pid])) {$_id[$this->xpwiki->pid] = array();}
	
		if (empty($args) || $args[0] == '') return $this->plugin_aname_usage($convert);
	
		$id = array_shift($args);
		$body = '';
		if (! empty($args)) $body = array_pop($args);
		$f_noid  = in_array('noid',  $args); // Option: Without id attribute
		$f_super = in_array('super', $args); // Option: CSS class
		$f_full  = in_array('full',  $args); // Option: With full(absolute) URI
	
		if ($body == '') {
			if ($f_noid)  return $this->plugin_aname_usage($convert, 'Meaningless(No link-title with \'noid\')');
			if ($f_super) return $this->plugin_aname_usage($convert, 'Meaningless(No link-title with \'super\')');
			if ($f_full)  return $this->plugin_aname_usage($convert, 'Meaningless(No link-title with \'full\')');
		}
	
		if ($this->cont['PLUGIN_ANAME_ID_MUST_UNIQUE'] && isset($_id[$this->xpwiki->pid][$id]) && ! $f_noid) {
			return $this->plugin_aname_usage($convert, 'ID already used: '. $id);
		} else {
			if (strlen($id) > $this->cont['PLUGIN_ANAME_ID_MAX'])
				return $this->plugin_aname_usage($convert, 'ID too long');
			if (! preg_match($this->cont['PLUGIN_ANAME_ID_REGEX'], $id))
				return $this->plugin_aname_usage($convert, 'Invalid ID string: ' .
				htmlspecialchars($id));
			$_id[$this->xpwiki->pid][$id] = TRUE; // Set
		}
	
		if (strpos($body, 'src:') === 0) {
			$options = array(
				'src'    => '',
				'width'  => '',
				'height' => '',
				'alt'    => ''
			);
			$args = explode(',', $body);
			$this->fetch_options($options, $args);
			if (preg_match('/^[a-z0-9_-]+\.(?:png|gif)$/i', $options['src'])) {
				$height = min($this->max_image_size, intval($options['height']));
				$height = ($height)? ' height="' . $height . '"' : '';
				$width = min($this->max_image_size, intval($options['width']));
				$width = ($width)? ' width="' . $width . '"' : '';
				$alt = ' alt="' . htmlspecialchars($options['alt']? $options['alt'] : $options['src']) . '"';
				$body = '<img src="' . $this->cont['LOADER_URL'] . '?src=' . $options['src'] . '"' . $alt . $height . $width . ' />';
				$convert = FALSE;
			}
		}
		if ($convert) {
			$body = htmlspecialchars($body);
		}
		
		$id = htmlspecialchars($id); // Insurance
		$class   = $f_super ? 'anchor_super' : 'anchor';
		$attr_id = $f_noid  ? '' : ' id="' . $id . '" name="' . $id . '"';
		$url     = $f_full  ? $this->func->get_page_uri($this->root->vars['page'], true) : '';
		if ($body != '') {
			$href  = ' href="' . $url . '#' . $id . '"';
			$title = ' title="' . $id . '"';
		} else {
			$href = $title = '';
		}
	
		return '<a class="' . $class . '"' . $attr_id . $href . $title . '>' .
		$body . '</a>';
	}
}
?>