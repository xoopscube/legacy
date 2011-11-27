<?php
/**
 * ajaxtree.inc.php - List pages as an Ajax tree menu
 *
 * @author	   revulo
 * @licence	   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html  GPLv2
 * @version	   1.2 beta1
 * @link	   http://www.revulo.com/PukiWiki/Plugin/AjaxTree.html
 */

/*
 * Created on 2008/02/07 by nao-pon http://hypweb.net/
 * $Id: ajaxtree.inc.php,v 1.12 2011/11/26 12:03:10 nao-pon Exp $
 */

class xpwiki_plugin_ajaxtree extends xpwiki_plugin {

	function plugin_ajaxtree_init()
	{

		// Check the mtime of wiki directory
		if (!isset($this->cont['PLUGIN_AJAXTREE_CHECK_MTIME'])) {
			$this->cont['PLUGIN_AJAXTREE_CHECK_MTIME'] =  false;
		}

		// Display the number of descendant pages
		if (!isset($this->cont['PLUGIN_AJAXTREE_COUNT_DESCENDANTS'])) {
			$this->cont['PLUGIN_AJAXTREE_COUNT_DESCENDANTS'] =	true;
		}

		// Move FrontPage to the top of the tree
		if (!isset($this->cont['PLUGIN_AJAXTREE_TOP_DEFAULTPAGE'])) {
			$this->cont['PLUGIN_AJAXTREE_TOP_DEFAULTPAGE'] =  true;
		}

		// Hide top-level leaf pages such as Help, MenuBar etc.
		if (!isset($this->cont['PLUGIN_AJAXTREE_HIDE_TOPLEVEL_LEAVES'])) {
			$this->cont['PLUGIN_AJAXTREE_HIDE_TOPLEVEL_LEAVES'] =  true;
		}

		// Ignore list
		if (!isset($this->cont['PLUGIN_AJAXTREE_NON_LIST'])) {
			$this->cont['PLUGIN_AJAXTREE_NON_LIST'] =  '';
		}

		// Include list
		if (!isset($this->cont['PLUGIN_AJAXTREE_INCLUDE_LIST'])) {
			$this->cont['PLUGIN_AJAXTREE_INCLUDE_LIST'] =  '';
		}

		// Expand list
		if (!isset($this->cont['PLUGIN_AJAXTREE_EXPAND_LIST'])) {
			$this->cont['PLUGIN_AJAXTREE_EXPAND_LIST'] =  '';
		}

		$messages['_ajaxtree_messages'] = array(
			'title'	  => 'AjaxTree',
			'toppage' => $this->root->module['title']
		);
		$this->func->set_plugin_messages($messages);
	}

	function plugin_ajaxtree_get_leaf_flags($clear = FALSE)
	{
		static $leaf = null;

		if ($clear) {
			$leaf = null;
			return;
		}

		if (is_null($leaf)) {
			$leaf = array();
			$pages = $this->plugin_ajaxtree_get_pages();
			foreach ($pages as $page) {
				if (isset($leaf[$page])) {
					continue;
				}
				$leaf[$page] = true;

				while (($pos = strrpos($page, '/')) !== false) {
					$page  = substr($page, 0, $pos);
					$isset = isset($leaf[$page]);
					$leaf[$page] = false;
					if ($isset === true) {
						break;
					}
				}

			}
		}
		return $leaf;
	}

	function plugin_ajaxtree_get_children($current = null, $clear = FALSE)
	{
		static $children = null;

		if ($clear) {
			$children = null;
			return;
		}

		if (is_null($children)) {
			$children = array();
			$pages = $this->plugin_ajaxtree_get_pages();
			foreach ($pages as $page) {
				$pos	= strrpos($page, '/');
				$parent = $pos ? substr($page, 0, $pos) : '/';
				$children[$parent][] = $page;
			}
		}

		if ($current === null) {
			return $children;
		}

		if (!isset($children[$current])) {
			$children[$current] = array();
		}
		//natcasesort($children[$current]);
		$this->func->pagesort($children[$current]);
		return $children[$current];
	}

	function plugin_ajaxtree_get_counts($clear = FALSE)
	{
		static $counts = null;

		if ($clear) {
			$counts = null;
			return;
		}

		if (is_null($counts)) {
			$counts = array();
			$pages = $this->plugin_ajaxtree_get_children();
			foreach ($pages as $page => $children) {
				$count = count($children);
				@ $counts[$page] += $count;
				while (($pos = strrpos($page, '/')) !== false) {
					$page = substr($page, 0, $pos);
					@ $counts[$page] += $count;
				}
				if ($this->cont['PLUGIN_AJAXTREE_TOP_DEFAULTPAGE']) {
					@ $counts['/'] += $count;
				}
			}
		}
		return $counts;
	}

	function plugin_ajaxtree_get_pages($reflash = FALSE, $clear = FALSE)
	{
		static $pages = null;

		if ($clear) {
			$pages = null;
			return;
		}

		if ($reflash || is_null($pages)) {
			$temp[0] = $this->root->userinfo['admin'];
			$temp[1] = $this->root->userinfo['uid'];
			$this->root->userinfo['admin'] = FALSE;
			$this->root->userinfo['uid'] = 0;

			if ($reflash) {
				$pages = $this->func->get_existpages(FALSE, '', array('nocache' => TRUE));
			} else {
				$pages = $this->func->get_existpages();
			}

			$this->root->userinfo['admin'] = $temp[0];
			$this->root->userinfo['uid'] = $temp[1];

			$this->plugin_ajaxtree_filter_pages($pages);

			$this->func->complementary_pagesort($pages);
		}

		return $pages;
	}


	function plugin_ajaxtree_action()
	{
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}

		if ($this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}

		$this->plugin_ajaxtree_reset_cache();
		return array(
			'msg'  => 'AjaxTree',
			'body' => 'Cache is updated.'
		);
	}

	function plugin_ajaxtree_convert()
	{
		$args = func_get_args();
		if (!empty($args[0])) {
			$this->root->_ajaxtree_messages['title'] = $args[0];
		}

		$res = array();
		if ($this->root->render_mode === 'block' && isset($GLOBALS['Xpwiki_'.$this->root->mydirname]['page'])) {
			$res = $this->func->set_current_page($GLOBALS['Xpwiki_'.$this->root->mydirname]['page']);
		}

		$html = $this->plugin_ajaxtree_get_html();
		$this->plugin_ajaxtree_modify_html($html, $this->root->vars['page']);

		if ($res) $this->func->set_current_page($res['page']);

		return $html;
	}

	function plugin_ajaxtree_get_html()
	{
		$file		= $this->plugin_ajaxtree_get_cachename('/');
		$this->func->add_tag_head('ajaxtree.css');
		$this->func->add_tag_head('ajaxtree.js');

		$html = '<h5>' . htmlspecialchars($this->root->_ajaxtree_messages['title']) . '</h5>' . "\n"
		  . '<div id="' . $this->root->mydirname . '_ajaxtree" class="xpwiki_ajaxtree">' . "\n"
		  . $this->plugin_ajaxtree_read_file($file) . "\n"
		  . '</div>' . "\n";

		return $html;
	}

	function plugin_ajaxtree_modify_html(&$html, $current)
	{
		if ($this->cont['PLUGIN_AJAXTREE_TOP_DEFAULTPAGE'] && $this->root->defaultpage === $current) {
			$ancestors = array('/' , $this->root->defaultpage);
			$label	   = $this->root->_ajaxtree_messages['toppage'];
		} else {
			$tokens = explode('/', $current);
			$depth	= count($tokens);
			$ancestors[0] = $tokens[0];
			for ($i = 1; $i < $depth; $i++) {
				$ancestors[$i] = $ancestors[$i - 1] . '/' . $tokens[$i];
			}
			$label = end($tokens);
		}

		$pos = 0;
		foreach ($ancestors as $ancestor) {
			$search = '<a title="' . htmlspecialchars($ancestor) . '"';
			$pos	= strpos($html, $search, $pos);
			if ($pos === false) {
				continue;
			}

			if ($ancestor === $this->root->vars['page'] || $ancestor === '/') {
				$search	 = '>';
				$pos2	 = strpos($html, $search, $pos) + 1;
				$search	 = '</a>';
				$pos3	 = strpos($html, $search, $pos2);
				$str	 = substr($html, $pos2, $pos3 - $pos2);
				$replace = '<span class="current"><!--NA-->' . $str . '<!--/NA--></span>';
				$length	 = $pos3 - $pos + strlen($search);
				$html	 = substr_replace($html, $replace, $pos, $length);
			}

			$search = 'collapsed';
			$length = strlen($search);

			if (substr($html, $pos - 11, $length) == $search) {
				$replace = 'expanded';
				$start	 = $pos - 2 - $length;
				$html	 = substr_replace($html, $replace, $start, $length);

				$file	 = $this->plugin_ajaxtree_get_cachename($ancestor);
				$search	 = '</li>';
				$replace = $this->plugin_ajaxtree_read_file($file);
				$pos	 = strpos($html, $search, $pos);
				$html	 = substr_replace($html, $replace, $pos, 0);
			}
		}
	}

	function plugin_ajaxtree_get_script_uri()
	{
		return $this->root->script;
	}

	function plugin_ajaxtree_get_cachename($page)
	{
		if ($this->cont['SOURCE_ENCODING'] != 'UTF-8' && $this->cont['SOURCE_ENCODING'] != 'ASCII') {
			$page = mb_convert_encoding($page, 'UTF-8', $this->cont['SOURCE_ENCODING']);
		}
		return $this->cont['CACHE_DIR'] . 'plugin/ajaxtree_' . rawurlencode($page) . '.pcache.html';
	}

	function plugin_ajaxtree_write_after($page)
	{
		$this->cache_clear();

		if (@ $this->root->vars['plugin'] === 'rename' || @ $this->root->vars['cmd'] === 'rename') {
			$this->plugin_ajaxtree_reset_cache();
			return;
		}

		if ($this->cont['PLUGIN_AJAXTREE_CHECK_MTIME']) {
			$file = $this->func->get_filename($page);
			if (filemtime($file) > filemtime($this->cont['DATA_DIR'])) {
				return;
			}
		}


		while ($page !== '/') {
			$pos  = strrpos($page, '/');
			$page = $pos ? substr($page, 0, $pos) : '/';
			if ($page === '/' || $this->func->check_readable_page($page, FALSE, FALSE, 0)) {
				$this->plugin_ajaxtree_update_cache($page);
			}
			//if ($page === '/') {
			//	break;
			//}
		}
	}

	function plugin_ajaxtree_reset_cache()
	{
		$this->cache_clear();

		$pages = $this->plugin_ajaxtree_get_pages(TRUE);
		$leaf  = $this->plugin_ajaxtree_get_leaf_flags();

		foreach ($pages as $page) {
			if ($leaf[$page] === false) {
				$this->plugin_ajaxtree_update_cache($page);
			}
		}
		$this->plugin_ajaxtree_update_cache('/');
	}

	function plugin_ajaxtree_update_cache($page)
	{
		$file = $this->plugin_ajaxtree_get_cachename($page);
		if ($page == '/') {
			$html = $this->plugin_ajaxtree_get_root_html();
		} else {
			$html = $this->plugin_ajaxtree_get_subtree($page);
		}
		$this->plugin_ajaxtree_write_file($file, $html);
	}

	function plugin_ajaxtree_get_root_html()
	{
		if ($this->cont['PLUGIN_AJAXTREE_COUNT_DESCENDANTS']) {
			$counts = $this->plugin_ajaxtree_get_counts();
		} else {
			$counts = array();
		}

		$html = '';
		if ($this->cont['PLUGIN_AJAXTREE_TOP_DEFAULTPAGE']) {
			if ($this->func->exist_plugin('rewritemap')) {
				$url = $this->func->plugin_rewritemap_url($this->root->defaultpage);
			} else {
				$url = $this->plugin_ajaxtree_get_script_uri();
			}
			$title	 = htmlspecialchars('/');
			$s_label = htmlspecialchars($this->root->_ajaxtree_messages['toppage']);
			$count	 = isset($counts['/']) ? ' <span class="count">(' . $counts['/'] . ')</span>' : '';
			$html	 = '<a title="' . $title . '" href="' . $url . '" class="block">' . $s_label . $count . '</a>' . "\n";
		}

		$html .= $this->plugin_ajaxtree_get_subtree('/');

		if ($this->cont['PLUGIN_AJAXTREE_EXPAND_LIST']) {
			$leaf  = $this->plugin_ajaxtree_get_leaf_flags();
			$pages = $this->plugin_ajaxtree_get_pages();
			$pages =  preg_grep('/' . $this->cont['PLUGIN_AJAXTREE_EXPAND_LIST'] . '/', $pages);
			//natcasesort($pages);
			$this->func->pagesort($pages);

			foreach ($pages as $page) {
				if ($leaf[$page] === false) {
					$this->plugin_ajaxtree_modify_html($html, $page);
				}
			}
		}

		return $html;
	}

	function plugin_ajaxtree_get_subtree($current)
	{
		$pages	= $this->plugin_ajaxtree_get_children($current);
		$leaf	= $this->plugin_ajaxtree_get_leaf_flags();
		$script =  $this->plugin_ajaxtree_get_script_uri();

		if (! $pages) {
			return '';
		}

		if ($this->cont['PLUGIN_AJAXTREE_COUNT_DESCENDANTS']) {
			$counts = $this->plugin_ajaxtree_get_counts();
		} else {
			$counts = array();
		}

		$depth = substr_count($pages[0], '/');
		if ($depth === 0) {
			$offset = 0;
		} else {
			$offset = strrpos($pages[0], '/') + 1;
		}

		$html = '<ul class="depth_'.$depth.'">';
		foreach ($pages as $page) {
			$indents = str_repeat(' ', $depth);
			$title	 = htmlspecialchars($page);
			$url = $this->func->get_page_uri($page, true);
			$label = substr($page, $offset);
			$s_label = htmlspecialchars($label);
			if ($this->root->pagename_num2str && $this->func->is_page($page)) {
				$s_label = preg_replace('/^(?:[0-9\-]+|[B0-9][A-Z0-9]{9})$/', $this->func->get_heading($page), $s_label);
			}

			if (!$this->func->is_page($page)) {
				$s_label = '<span class="noexists">' . $s_label . $this->root->_symbol_noexists . '</span>';
			}

			$count	 = isset($counts[$page]) ? ' <span class="count">(' . $counts[$page] . ')</span>' : '';

			$html .= "\n" . $indents;
			$html .= ($leaf[$page] === true) ? '<li>' : '<li class="collapsed">';
			$html .= '<a title="' . $title . '" href="' . $url . '" class="block">' . $s_label . $count . '</a></li>';
		}
		$html .= '</ul>';

		return $this->func->strip_MyHostUrl($html);
	}

	function plugin_ajaxtree_filter_pages(&$pages)
	{
		if ($this->cont['PLUGIN_AJAXTREE_INCLUDE_LIST'] !== '') {
			$includes = preg_grep('/' . $this->cont['PLUGIN_AJAXTREE_INCLUDE_LIST'] . '/', $pages);
		} else {
			$includes = array();
		}

		if ($this->cont['PLUGIN_AJAXTREE_HIDE_TOPLEVEL_LEAVES']) {
			$leaf = $this->plugin_ajaxtree_get_leaf_flags();
			foreach ($pages as $key => $page) {
				if (strpos($page, '/') === false && $leaf[$page] === true) {
					unset($pages[$key]);
				}
			}
		}

		if ($this->cont['PLUGIN_AJAXTREE_NON_LIST'] !== '') {
			$pattern = '/(' . $this->root->non_list . ')|(' . $this->cont['PLUGIN_AJAXTREE_NON_LIST'] . ')/';
		} else {
			$pattern = '/' . $this->root->non_list . '/';
		}
		if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
			$pages = preg_grep($pattern, $pages, PREG_GREP_INVERT);
		} else {
			$pages = array_diff($pages, preg_grep($pattern, $pages));
		}

		if ($includes) {
			$pages += $includes;
		}
	}

	function plugin_ajaxtree_read_file($filename)
	{
		return is_file($filename)? file_get_contents($filename) : '';
	}

	function plugin_ajaxtree_write_file($filename, $data)
	{
		$fp = fopen($filename, is_file($filename) ? 'r+b' : 'wb');
		if ($fp === false) {
			return false;
		}
		flock($fp, LOCK_EX);
		$last = ignore_user_abort(1);
		rewind($fp);
		fwrite($fp, $data);
		fflush($fp);
		ftruncate($fp, ftell($fp));
		ignore_user_abort($last);
		flock($fp, LOCK_UN);
		fclose($fp);
		return true;
	}

	function cache_clear() {
		$this->plugin_ajaxtree_get_pages(FALSE, TRUE);
		$this->plugin_ajaxtree_get_children(null, TRUE);
		$this->plugin_ajaxtree_get_leaf_flags(TRUE);
		$this->plugin_ajaxtree_get_counts(TRUE);
	}
}
?>