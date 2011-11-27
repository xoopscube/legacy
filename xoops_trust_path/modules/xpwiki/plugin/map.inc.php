<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: map.inc.php,v 1.5 2011/11/26 12:03:10 nao-pon Exp $
//
// Site map plugin

/*
 * プラグイン map: サイトマップ(のようなもの)を表示
 * Usage : http://.../pukiwiki.php?plugin=map
 * パラメータ
 *   &refer=ページ名
 *     起点となるページを指定
 *   &reverse=true
 *     あるページがどこからリンクされているかを一覧。
 */

class xpwiki_plugin_map extends xpwiki_plugin {

	function plugin_map_init () {
		// Show $non_list files
		$this->show_hidden =  0; // 0, 1
		$this->show_not_related = false;
	}
	
	function plugin_map_action()
	{
	//	global $vars, $whatsnew, $defaultpage, $non_list;
	
		$reverse = isset($this->root->vars['reverse']);
		$refer   = isset($this->root->vars['refer']) ? $this->root->vars['refer'] : '';
		if ($refer === '' || ! $this->func->is_page($refer))
			$this->root->vars['refer'] = $refer = $this->root->defaultpage;
	
		$retval['msg']  = $reverse ? 'Relation map (link from)' : 'Relation map, from $1';
		$retval['body'] = '';
	
		// Get pages
		$pages = array_values(array_diff($this->func->get_existpages(), array($this->root->whatsnew)));
		if (! $this->show_hidden)
			$pages = array_diff($pages, preg_grep('/' . $this->root->non_list . '/', $pages));
		if (empty($pages)) {
			$retval['body'] = 'No pages.';
			return $retval;
		} else {
			$retval['body'] .= '<p>' . "\n" .  'Total: ' . count($pages) .
			' page(s) on this site.' . "\n" . '</p>' . "\n";
		}
	
		// Generate a tree
		$nodes = array();
		foreach ($pages as $page)
			$nodes[$page] = & new XpWikiMapNode($this->xpwiki, $page, $reverse);
	
		// Node not found: Because of filtererd by $non_list
		if (! isset($nodes[$refer])) $this->root->vars['refer'] = $refer = $this->root->defaultpage;
	
		if ($reverse) {
			$keys = array_keys($nodes);
			sort($keys);
			$alone = array();
			$retval['body'] .= '<ul>' . "\n";
			foreach ($keys as $page) {
				if (! empty($nodes[$page]->rels)) {
					$retval['body'] .= $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
				} else {
					$alone[] = $page;
				}
			}
			$retval['body'] .= '</ul>' . "\n";
			if (! empty($alone)) {
				$retval['body'] .= '<hr />' . "\n" .
				'<p>No link from anywhere in this site.</p>' . "\n";
				$retval['body'] .= '<ul>' . "\n";
				foreach ($alone as $page)
					$retval['body'] .= $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
				$retval['body'] .= '</ul>' . "\n";
			}
		} else {
			$nodes[$refer]->chain($nodes);
			$retval['body'] .= '<ul>' . "\n" . $nodes[$refer]->toString($nodes) . '</ul>' . "\n";
			
			if ($this->show_not_related) {
				$retval['body'] .= '<hr />' . "\n" .
				'<p>Not related from ' . htmlspecialchars($refer) . '</p>' . "\n";
				$keys = array_keys($nodes);
				sort($keys);
				$retval['body'] .= '<ul>' . "\n";
				foreach ($keys as $page) {
					if (! $nodes[$page]->done) {
						$nodes[$page]->chain($nodes);
						$retval['body'] .= $nodes[$page]->toString($nodes, 1, $nodes[$page]->parent_id);
					}
				}
				$retval['body'] .= '</ul>' . "\n";
			}
		}
	
		// 終了
		return $retval;
	}
}
	
class XpWikiMapNode
{
	var $page;
	var $is_page;
	var $link;
	var $id;
	var $rels;
	var $parent_id = 0;
	var $done;
	var $hide_pattern;

	function XpWikiMapNode(& $xpwiki, $page, $reverse = FALSE)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;}
		
		static $yetlists = array();
		if (isset($yetlists[$this->root->mydirname])) {
			$this->yetlists = $yetlists[$this->root->mydirname];
		} else {
			$_yetlists = array();
			if (is_file($this->cont['CACHE_DIR']."yetlist.dat")) {
				$_yetlists = unserialize(file_get_contents($this->cont['CACHE_DIR']."yetlist.dat"));
			}
			foreach($_yetlists as $notyet => $refs) {
				foreach($refs as $ref) {
					$this->yetlists[$ref][] = $notyet;
				}
			}
			$yetlists[$this->root->mydirname] = $this->yetlists;
		}

		$this->page    = $page;
		$this->is_page = $this->func->is_page($page);
		$this->cache   = $this->cont['CACHE_DIR'] . $this->func->encode($page);
		$this->done    = ! $this->is_page;
		$this->link    = $this->func->make_pagelink($page);
		$this->id      = ++$id[$this->xpwiki->pid];
		$this->hide_pattern = '/' . $this->root->non_list . '/';

		$this->rels = $reverse ? $this->ref() : $this->rel();
		$mark       = $reverse ? '' : '<sup>+</sup>';
		$this->mark = '<a id="rel_' . $this->id . '" href="' . $this->root->script .
			'?plugin=map&amp;refer=' . rawurlencode($this->page) . '">' .
			$mark . '</a>';

	}

	function hide(& $pages)
	{
		if (! $this->show_hidden)
			$pages = array_diff($pages, preg_grep($this->hide_pattern, $pages));
		return $pages;
	}

	function ref()
	{
		$refs = array_keys($this->func->links_get_related_db($this->page));
		sort($refs);
		return $refs;
	}

	function rel()
	{
		$rels = array_keys($this->func->links_get_linked_db($this->page));
		$yetlists = isset($this->yetlists[$this->page])? $this->yetlists[$this->page] : array();
		$rels = array_merge($rels, $yetlists);
		sort($rels);
		return $rels;
	}

	function chain(& $nodes)
	{
		if ($this->done) return;

		$this->done = TRUE;
		if ($this->parent_id == 0) $this->parent_id = -1;

		foreach ($this->rels as $page) {
			if (! isset($nodes[$page])) $nodes[$page] = & new XpWikiMapNode($this->xpwiki, $page);
			if ($nodes[$page]->parent_id == 0)
				$nodes[$page]->parent_id = $this->id;
		}
		foreach ($this->rels as $page)
			$nodes[$page]->chain($nodes);
	}

	function toString(& $nodes, $level = 1, $parent_id = -1)
	{
		$indent = str_repeat(' ', $level);

		if (! $this->is_page) {
			return $indent . '<li>' . $this->link . '</li>' . "\n";
		} else if ($this->parent_id != $parent_id) {
			return $indent . '<li>' . $this->link .
				'<a href="#rel_' . $this->id . '">...</a></li>' . "\n";
		}
		$retval = $indent . '<li>' . $this->mark . $this->link . "\n";
		if (! empty($this->rels)) {
			$childs = array();
			$level += 2;
			foreach ($this->rels as $page)
				if (isset($nodes[$page]) && $this->parent_id != $nodes[$page]->id)
					$childs[] = $nodes[$page]->toString($nodes, $level, $this->id);

			if (! empty($childs))
				$retval .= $indent . ' <ul>' . "\n" .
					join('', $childs) . $indent . ' </ul>' . "\n";
		}
		$retval .= $indent . '</li>' . "\n";

		return $retval;
	}
}
?>