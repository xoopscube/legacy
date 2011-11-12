<?php
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: ls2.inc.php,v 1.17 2009/11/17 09:15:45 nao-pon Exp $
//
// List plugin 2

/*
 * 配下のページや、その見出し(*,**,***)の一覧を表示する
 * Usage
 *  #ls2(pattern[,title|include|link|reverse|compact, ...],heading title)
 *
 * pattern  : 省略するときもカンマが必要
 * 'title'  : 見出しの一覧を表示する
 * 'include': インクルードしているページの見出しを再帰的に列挙する
 * 'link   ': actionプラグインを呼び出すリンクを表示
 * 'reverse': ページの並び順を反転し、降順にする
 * 'compact': 見出しレベルを調整する
 *     PLUGIN_LS2_LIST_COMPACTがTRUEの時は無効(変化しない)
 * heading title: 見出しのタイトルを指定する (linkを指定した時のみ)
 */

class xpwiki_plugin_ls2 extends xpwiki_plugin {
	function plugin_ls2_init () {

		// 見出しアンカーの書式
		$this->cont['PLUGIN_LS2_ANCHOR_PREFIX'] =  '#content_1_';

		// 見出しアンカーの開始番号
		$this->cont['PLUGIN_LS2_ANCHOR_ORIGIN'] =  0;

		// 見出しレベルを調整する(デフォルト値)
		$this->cont['PLUGIN_LS2_LIST_COMPACT'] =  FALSE;

		$this->params = array(
			'link'        => FALSE,
			'title'       => FALSE,
			'include'     => FALSE,
			'reverse'     => FALSE,
			'compact'     => $this->cont['PLUGIN_LS2_LIST_COMPACT'],
			'pagename'    => FALSE,
			'basename'    => FALSE,
			'notemplate'  => FALSE,
			'relatedcount'=> FALSE,
			'depth'       => FALSE,
			'nonew'       => FALSE,
			'nonlist'     => FALSE,
			'col'         => 1,
			'_args'       => array()
		);
	}

	function plugin_ls2_action() {

		$params = $this->params;

		foreach (array_keys($params) as $key) {
			if ($key === 'col') {
				$params[$key] = (empty($this->root->vars[$key]))? 1 : max(1, intval($this->root->vars[$key]));
			} else if ($key === 'depth') {
				$params[$key] = (empty($this->root->vars[$key]))? FALSE : intval($this->root->vars[$key]);
			} else if ($key === '_args') {
				continue;
			} else {
				$params[$key] = isset($this->root->vars[$key]);
			}
		}

		$tmp = array();
		$tmp[] = 'plugin=ls2&amp;prefix=$prefix';
		foreach (array_keys($params) as $key) {
			if ($key === 'col') {
				if ($params[$key] < 2) $tmp[] = $key.'='.intval($params[$key]);
			} else if ($key === 'depth') {
				if ($params[$key]) $tmp[] = $key.'='.intval($params[$key]);
			} else if ($key{0} === '_') {
				continue;
			} else {
				if ($params[$key]) $tmp[] = $key.'=1';
			}
		}
		$params['_link_query'] = '?' . join('&amp;', $tmp);

		$prefix = isset($this->root->vars['prefix']) ? $this->root->vars['prefix'] : '';

		$params['_base_lev'] = substr_count($prefix, '/');

		$body = $this->plugin_ls2_show_lists($prefix, $params);

		return array('body'=>$body,
		'msg'=>str_replace('$1', htmlspecialchars($prefix), $this->root->_ls2_msg_title));
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_ls2_convert() {
		$params = $this->params;

		$args = array();
		if (func_num_args()) {
			$args   = func_get_args();
		}

		$this->fetch_options($params, $args, array('prefix'));

		// compat
		if ($params['basename']) $params['pagename'] = TRUE;

		$prefix = ($params['prefix'])? $prefix = $params['prefix'] : '';

		if ($prefix === '') $prefix = $this->func->strip_bracket($this->cont['PageForRef']) . '/';
		if ($prefix === '/') $prefix = '';
		$params['_base_lev'] = substr_count($prefix, '/');

		$title = (! empty($params['_args'])) ? htmlspecialchars(join(',', $params['_args'])) :   // Manual
			str_replace('$1', htmlspecialchars($prefix), $this->root->_ls2_msg_title); // Auto

		$tmp = array();
		$tmp[] = 'plugin=ls2&amp;prefix=$prefix';
		foreach (array_keys($params) as $key) {
			if ($key === 'col') {
				if ($params[$key] > 1) $tmp[] = $key.'='.intval($params[$key]);
			} else if ($key === 'depth') {
				if ($params[$key]) $tmp[] = $key.'='.intval($params[$key]);
			} else if ($key{0} === '_' || $key === 'prefix') {
				continue;
			} else {
				if ($params[$key]) $tmp[] = $key.'=1';
			}
		}
		$params['_link_query'] = '?' . join('&amp;', $tmp);

		if (! $params['link'])
			return $this->plugin_ls2_show_lists($prefix, $params);

		$tmp = array();
		$tmp[] = 'plugin=ls2&amp;prefix=' . rawurlencode($prefix);
		if ($params['title'])   $tmp[] = 'title=1';
		if ($params['include']) $tmp[] = 'include=1';

		return '<p><a href="' . $this->root->script . str_replace('$prefix', rawurlencode($prefix), $params['_link_query']) . '">' .
		$title . '</a></p>' . "\n";
	}

	function plugin_ls2_show_lists($prefix, & $params) {
		static $_auto_template_name;

		$pages = array();
		$options = array();
		if ($params['nonlist']) {
			$options['nolisting'] = TRUE;
		}
		$pages = $this->func->get_existpages(FALSE, $prefix, $options);

		if ($params['depth'] !== FALSE || $params['pagename']) {
			//$this->func->complementary_pagesort($pages, 'natcasesort');
			$this->func->complementary_pagesort($pages, array(& $this->func, 'pagesort'));
		} else {
			//natcasesort($pages);
			$this->func->pagesort($pages);
		}

		$base_pages = array();
		if ($prefix) {
			$_base = '';
			foreach(explode('/', rtrim($prefix, '/')) as $_page) {
				$_base .= $_page;
				$base_pages[$_base] = true;
				$_base .= '/';
			}
		}

		$params['_parent_depth'] = substr_count(rtrim($prefix,'/'), '/');
		$params['_child_counts'] = array();

		// テンプレートページの正規表現
		if (!isset($_auto_template_name[$this->root->mydirname])) {
			$_temps = array();
			foreach($this->root->auto_template_rules as $_temp) {
				if (!is_array($_temp)) {
					$_temp = array($_temp);
				}
				foreach($_temp as $__temp) {
					$__temp = preg_replace('/\\\\[\d]+/', '', $__temp);
					$_temps[$__temp] = preg_quote($__temp, '/');
				}
			}
			$_auto_template_name[$this->root->mydirname] = ($_temps)? '/(?:'.join('|', $_temps).')/' : '/(?!)/';
		}

		foreach ($pages as $key => $page) {
			// complementary_pagesort() で保管された $prefix ページ($base_pages)を削除
			if ($base_pages) {
				if (isset($base_pages[$page])) {
					unset($pages[$key]);
					continue;
				}
			}

			// テンプレートページは表示しない場合
			if ($params['notemplate'] && preg_match($_auto_template_name[$this->root->mydirname], $page)) {
				unset($pages[$key]);
				continue;
			}

			// 階層深さ指定チェック
			if ($params['depth'] !== FALSE) {
				if (substr_count($page, '/') - $params['_parent_depth'] > intval($params['depth'])) {
					unset($pages[$key]);
					continue;
				}
				$params['_child_counts'][$page] = $this->func->get_child_counts($page);
			}

			$params["page_{$page}"] = 0;
		}

		if ($params['reverse']) $pages = array_reverse($pages);

		if (empty($pages)) {
			return str_replace('$1', htmlspecialchars($prefix), $this->root->_ls2_err_nopages);
		} else {
			$rows = ceil(count($pages) / $params['col']);
			$pages_g = array_chunk($pages, $rows);
			$ret = '';
			$width = ($params['col'] > 1)? floor(100 / $params['col']).'%' : 'auto';
			foreach ($pages_g as $pages) {
				$params['result'] = $params['saved'] = array();
				$last_page = $prefix;
				foreach ($pages as $page) {
					$this->plugin_ls2_get_headings($page, $params, 1);
					$last_page = $page;
				}
				$ret .= '<div style="float:left;width:'.$width.'">'."\n";
				$ret .= join("\n", $params['result']) . join("\n", $params['saved'])."\n";
				$ret .= '</div>'."\n";
			}
			return $ret.'<div style="clear:left;"></div>';
		}
	}

	function plugin_ls2_get_headings($page, & $params, $level, $include = FALSE) {
		static $_ls2_anchor = array();
		if (!isset($_ls2_anchor[$this->xpwiki->pid])) {$_ls2_anchor[$this->xpwiki->pid] = 0;}

		// ページが未表示のとき
		$is_done = (isset($params["page_$page"]) && $params["page_$page"] > 0);
		if (! $is_done) $params["page_$page"] = ++$_ls2_anchor[$this->xpwiki->pid];

		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		$title  = $s_page . ' ' . $this->func->get_pg_passage($page, FALSE);
		if ($this->root->pagename_num2str && $this->func->is_page($page)) $s_page =  preg_replace('/\/(?:[0-9\-]+|[B0-9][A-Z0-9]{9})$/','/'.$this->func->get_heading($page),$s_page);
		$margin_left = 0;
		if ($params['pagename']) {
			$s_page = $this->func->basename($s_page);
			$margin_left = (substr_count($page, '/') - $params['_base_lev']) * $this->root->_ul_margin;
		}

		$href = $this->func->get_page_uri($page, true);

		// New!
		$new_mark = '';
		if ($this->func->is_page($page) && !$params['nonew'] && $this->func->exist_plugin_inline("new"))
			$new_mark = $this->func->do_plugin_inline("new","{$page},nolink");

		// Child count
		$child_count = (!empty($params['_child_counts'][$page]) && ($params['depth'] !== FALSE) && (substr_count($page, '/') - $params['_parent_depth']) === intval($params['depth']))? ' [<a href="' . $this->root->script .  str_replace('$prefix', rawurlencode($page.'/'), $params['_link_query']) . '">+' . $params['_child_counts'][$page] . '</a>]' : '';

		// Related count
		$rel_count = ($params['relatedcount'])? ' ('.$this->func->links_get_related_count($page).')' : '';

		$this->plugin_ls2_list_push($params, $level);
		$ret = $include ? '<li style="margin-left:'.$margin_left.'px;">include ' : '<li style="margin-left:'.$margin_left.'px;">';

		if ($params['title'] && $is_done) {
			$ret .= '<a href="' . $href . '" title="' . $title . '">' . $s_page . '</a> ';
			$ret .= '<a href="#list_' . $params["page_$page"] . '"><sup>&uarr;</sup></a>';
			array_push($params['result'], $ret);
			return;
		}

		if ($this->func->is_page($page)) {
			$ret .= '<a id="list_' . $params["page_$page"] . '" href="' . $href .
				'" title="' . $title . '">' . $s_page . '</a>' . $rel_count . $child_count . $new_mark;
		} else {
			$_dirname = $this->func->page_dirname($page);
			$ret .= '<span id="list_' . $params["page_$page"] . '">' . $this->func->make_pagelink($page, (($_dirname && $params['pagename'])? '#compact:'.$_dirname : '')) . '</span>' . $rel_count . $child_count . $new_mark;
		}

		array_push($params['result'], $ret);

		$anchor = $this->cont['PLUGIN_LS2_ANCHOR_ORIGIN'];
		$matches = array();
		foreach ($this->func->get_source($page) as $line) {
			if ($params['title'] && preg_match('/^(\*{1,5})/', $line, $matches)) {
				$id    = $this->func->make_heading($line);
				$level = strlen($matches[1]);
				$id    = $this->cont['PLUGIN_LS2_ANCHOR_PREFIX'] . $anchor++;
				$this->plugin_ls2_list_push($params, $level + strlen($level));
				array_push($params['result'],
				'<li><a href="' . $href . $id . '">' . $line . '</a>');
			} else if ($params['include'] &&
				preg_match('/^#include\((.+)\)/', $line, $matches) &&
				$this->func->is_page($matches[1]))
			{
				$this->plugin_ls2_get_headings($matches[1], $params, $level + 1, TRUE);
			}
		}
	}

	//リスト構造を構築する
	function plugin_ls2_list_push(& $params, $level) {

		$result = & $params['result'];
		$saved  = & $params['saved'];
		$cont   = TRUE;
		$open   = '<ul%s>';
		$close  = '</li></ul>';

		while (count($saved) > $level || (! empty($saved) && $saved[0] != $close))
			array_push($result, array_shift($saved));

		$margin = $level - count($saved);

		// count($saved)を増やす
		while (count($saved) < ($level - 1)) array_unshift($saved, '');

		if (count($saved) < $level) {
			$cont = FALSE;
			array_unshift($saved, $close);

			$left = ($level == $margin) ? $this->root->_ul_left_margin : 0;
			if ($params['compact']) {
				$left  += $this->root->_ul_margin;   // マージンを固定
				$level -= ($margin - 1); // レベルを修正
			} else {
				$left += $margin * $this->root->_ul_margin;
			}
			$str = sprintf($this->root->_list_pad_str, $level, $left, $left);
			array_push($result, sprintf($open, $str));
		}

		if ($cont) array_push($result, '</li>');
	}

}
?>