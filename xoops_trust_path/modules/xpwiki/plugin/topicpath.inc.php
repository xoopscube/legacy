<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: topicpath.inc.php,v 1.9 2009/04/04 07:12:54 nao-pon Exp $
//
// 'topicpath' plugin for PukiWiki, available under GPL

class xpwiki_plugin_topicpath extends xpwiki_plugin {
	function plugin_topicpath_init () {

		// Show a link to $defaultpage or not
		$this->cont['PLUGIN_TOPICPATH_TOP_DISPLAY'] =  1;
		
		// Label for $defaultpage
		$this->cont['PLUGIN_TOPICPATH_TOP_LABEL'] = htmlspecialchars($this->root->module['title']);
		
		// Separetor / of / topic / path
		$this->cont['PLUGIN_TOPICPATH_TOP_SEPARATOR'] =  ' / ';
		
		// Show the page itself or not
		$this->cont['PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY'] =  1;
		
		// If PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY, add a link to itself (0: off, 1:on, 2:only on read)
		$this->cont['PLUGIN_TOPICPATH_THIS_PAGE_LINK'] =  2;

	}
	
	function plugin_topicpath_convert()
	{
		$args = func_get_args();
		$sep = (empty($args[0]))? '' : $args[0];
		return '<div>' . $this->plugin_topicpath_inline($sep) . '</div>';
	}
	
	function plugin_topicpath_inline($sep = '')
	{
		if ($sep) {
			$sep = htmlspecialchars($sep);
			$sep = preg_replace('/&amp;(#[0-9]+|#x[0-9a-f]+|' . $this->root->entity_pattern . ';)/','&$1',$sep);
			$this->cont['PLUGIN_TOPICPATH_TOP_SEPARATOR'] = $sep;
		}
		
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		if ($page === $this->root->defaultpage) return '';
		
		//if ($this->root->vars['cmd'] === 'read' && $page !== '') {
		if ($page !== '') {
		
			$parts = explode('/', $page);
		
			$b_link = TRUE;
			if ($this->cont['PLUGIN_TOPICPATH_THIS_PAGE_DISPLAY']) {
				$b_link = ($this->cont['PLUGIN_TOPICPATH_THIS_PAGE_LINK'] === 2)? ($this->root->vars['cmd'] !== 'read') : (bool)$this->cont['PLUGIN_TOPICPATH_THIS_PAGE_LINK'];
			} else {
				array_pop($parts); // Remove the page itself
			}
		
			$topic_path = array();
			while (! empty($parts)) {
				$_landing = join('/', $parts);
				$element = htmlspecialchars(array_pop($parts));
				if (! $b_link)  {
					// This page ($_landing == $page)
					$b_link = TRUE;
					$topic_path[] = $element;
				} else if ($this->cont['PKWK_READONLY'] && ! $this->func->is_page($_landing)) {
					// Page not exists
					$topic_path[] = $element;
				} else {
					// Page exists or not exists
					$topic_path[] = $this->func->make_pagelink($_landing, $element);
				}
			}
		} else {
			$topic_path[] = strip_tags($this->xpwiki->title);
		}
	
		$ret = '';
		if ($this->cont['PLUGIN_TOPICPATH_TOP_DISPLAY']) {
			$ret = $this->func->make_pagelink($this->root->defaultpage, (($sep === '/')? $this->root->mydirname : $this->cont['PLUGIN_TOPICPATH_TOP_LABEL']));
			$ret .= ($sep === '/')? ':' : $sep;
		}
		
		$ret .= join($this->cont['PLUGIN_TOPICPATH_TOP_SEPARATOR'], array_reverse($topic_path));
		
		if ($page !== '' && $this->root->vars['cmd'] !== 'read') {
			$title = strip_tags($this->xpwiki->title);
			if (strpos($title, $page) !== FALSE) $ret = str_replace($page, $ret, $title);
		}
		
		return $ret;
	}
}
?>