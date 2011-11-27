<?php
/*
 * Created on 2009/01/19 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: relatedview.inc.php,v 1.3 2011/11/26 12:03:10 nao-pon Exp $
 */

// #relatedview([noautolink][,nowikiname][,eachpage][,search:<PageName or Regex with "#">][,nosearch:<PageName or Regex with "#">])

// PukiWiki - Yet another WikiWikiWeb clone.
//
//	relatedview.inc.php		Copyright Cue 2005
//
//	2005/06/10	v0.01
//		とりあえず作る
//
//	2005/06/10	v0.02
//		念のため再入防止
//		マッチングパターンの見直し
//		逆リンク処理をエイリアス利用に変更
//
//	2005/06/11	v0.03
//		リスト・テーブルは空行出力を抑制
//		参照元が#include行だった場合、取り込みを抑制
//
//	2005/06/11	v0.04
//		wiki textのヘッダをオプションで挿入可能に(主にテーブル向け)
//		逆リンク時に直近のアンカーを付加
//
//	2005/06/11	v0.05
//		コメントの日付が間違えてたので修正(恥
//
//	2005/06/12	v0.06
//		デリミタに/を使ってたバグを修正
//		相対リンクに対応
//
//	2005/06/12	v0.07
//		閲覧制限のチェック追加
//
//	2005/06/12	v0.08
//		閲覧制限の変更
//		パターン見直し
//		整形済みとコメントの取り込みを抑制
//
//	2005/06/26	v0.09
//		WikiName,Autolink対応
//
//	2005/07/05	v0.10
//		引数仕様を変更(v0.04仕様を廃止)
//		ブロックプラグインの取り込みを抑制
//
//	2005/07/07	v0.11
//		検索パターンのエラー出力抑制
//		相対パス→絶対パスのバグ修正
//		正規表現でない検索指定の相対パスに対応
//		テーブルの上セル連結('~')を実データで置換するよう処理
//
//	2005/07/09	v0.12
//		ページ読み込み順を自然順ソートに変更
//		対象行中のインラインプラグインはボディ部だけ見るよう変更
//		複数行プラグインはボディ部を含めてスキップ
//
//	2005/07/14	v0.13
//		インラインプラグインのボディ部の取り扱いを変更
//
//	2005/07/31	v0.14
//		複数行プラグイン有効判定のバグ取り
//		ごく簡単なオプションチェック追加
//
//	2005/07/31	v0.15
//		autolink.dat読み込みでflockし忘れ修正

class xpwiki_plugin_relatedview extends xpwiki_plugin {
	function plugin_relatedview_init () {



	}
	
	function plugin_relatedview_convert()
	{
		static $busy = array();
		if (!isset($busy[$this->xpwiki->pid])) {$busy[$this->xpwiki->pid] = false;}
		if(!$busy[$this->xpwiki->pid]) $busy[$this->xpwiki->pid] = true; else return '';
	
		$args = array(
			'noautolink' => false,
			'nowikiname' => false,
			'search' => false,
			'nosearch' => false,
			'eachpage' => false,
		);
		$arg = func_get_args();
		if ($arg) {
			$this->fetch_options($args, $arg, array(), '_args', ' *(?:=>|=|:) *');
			if (! empty($args['_args'])) {
				return '<p>relatedview : unknown option(s). '.htmlspecialchars(join(',', $args['_args'])).'</p>';
			}
		}
		$category = isset($this->cont['PageForRef']) ? $this->func->strip_bracket($this->cont['PageForRef']) : '';
		$q_category = preg_quote($category, '/');
		if(! empty($args['search']) && !preg_match('/^#.*#$/', $args['search']))
			$args['search'] = '#^'.preg_quote($this->func->get_fullname($args['search'], $category), '#').'#';
		if(! empty($args['nosearch']) && !preg_match('/^#.*#$/', $args['nosearch']))
			$args['nosearch'] = '#^'.preg_quote($this->func->get_fullname($args['nosearch'], $category), '#').'#';
		$follow_wikiname = !$this->root->nowikiname && !$args['nowikiname'] && preg_match('/^'.$this->root->WikiName.'$/', $category);
		$follow_autolink = $this->root->autolink && !$args['noautolink'] && is_file($this->cont['CACHE_DIR'] . 'autolink.dat');
		if($follow_autolink){
			$fp = fopen($this->cont['CACHE_DIR'] . 'autolink.dat', 'r');
			flock($fp, LOCK_SH);
			@list($auto, $auto_a, $ignores) = file($this->cont['CACHE_DIR'] . 'autolink.dat');
			flock($fp, LOCK_UN);
			fclose($fp);
			if (strpos($ignores, $category) !== false) {
				$follow_autolink = false;
			} else {
				$_follow_autolink = true;
				foreach(explode("\t", $auto) as $_auto) {
					if ($_follow_autolink && !preg_match('/^(?:'.$_auto.')$/x', $category)) {
						$follow_autolink = false;
					}
				}
			}
		}
	
		$links = array_keys($this->func->links_get_related_db($category));
		foreach($links as $key=>$page){
			if ($page == $this->root->whatsnew ||
				preg_match('/'.$this->root->non_list.'/', $page) ||
				(!empty($args['search']) && !@preg_match($args['search'], $page)) ||
				(!empty($args['nosearch']) && @preg_match($args['nosearch'], $page)) ||
				!$this->func->check_readable($page, false, false))
					unset($links[$key]);
		}
		natsort($links);
	
		if($follow_autolink || $follow_wikiname){
			$link_pattern = '/(\[\[(?:.+?>)?)?'.$q_category.'(?(1)(?:#(?:[A-Za-z][\w-]*)?)?\]\])/';
			$replace_patterns = array(
				'/\[\[(.+?)>'.$q_category.'(?:#(?:[A-Za-z][\w-]*)?)?\]\]/',
			'/(\[\[)?'.$q_category.'(?(1)(?:#(?:[A-Za-z][\w-]*)?)?\]\])/'
		);
		}else{
			$link_pattern = '/\[\[(?:.+?>)?'.$q_category.'(?:#(?:[A-Za-z][\w-]*)?)?\]\]/';
			$replace_patterns = array(
				'/\[\[(.+?)>'.$q_category.'(?:#(?:[A-Za-z][\w-]*)?)?\]\]/',
			'/\[\['.$q_category.'(?:#(?:[A-Za-z][\w-]*)?)?\]\]/'
		);
		}
		$anchor_pattern = '/(?:(^\*{1,3}.*?\[#)|(?:^#|&)aname\()([A-Z][\w-]+?)(?(1)\]|[,)])/i';
		$relative_pattern = '/\[\[((?:(?!\]\]).)+>)?(\.{0,2}\/(?:'.$this->root->BracketName.')?)((?:#(?:[A-Za-z][\w-]*)?)?)\]\]/';
	
		$body = $matches = $cell_buffers = array();
		$is_ver146 = TRUE;
		$enable_multiline = $is_ver146 && !$this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK'] ? 1 : 0;
		foreach($links as $page){
			$anchor = $page;
			$_body = array();
			foreach(preg_grep('%^(?!//| |\t)%', $this->func->get_source($page)) as $data){
				if($enable_multiline > 1){
					if(preg_match('/^\}{'.$enable_multiline.'}/', $data))
						$enable_multiline = 1;
					continue;
				}
				if(preg_match($anchor_pattern, $data, $matches))
					$anchor = $page.'#'.$matches[2];
				if(preg_match('/^#/', $data, $matches)){
					if($enable_multiline && preg_match('/(\{{2,})\s*$/', $data, $matches))
						$enable_multiline = strlen($matches[1]);
					continue;
				}
				$this->_page = $page;
				//$data = preg_replace($relative_pattern, "'[[$1'.get_fullname('$2', '$page').'$3]]'", $data);
				$data = preg_replace_callback($relative_pattern, array($this, 'get_fullname'), $data);
				if(preg_match('/^\|(.+)\|([hfc]?)$/i', $data, $matches)){
					$cells = explode('|', $matches[1]);
					foreach($cells as $key=>$val){
						if($val == '~')
							$cells[$key] = $cell_buffers[$key];
						else
							$cell_buffers[$key] = $val;
					}
					$data = '|'.implode('|', $cells).'|'.$matches[2]."\n";
				}
				$plain = $data;
				while(preg_match('/&(\w+)(?:\([^)]*\))?(?:\{((?:(?!(?R)|\}).)*)\})?;/', $plain, $matches)){
					if(isset($matches[2]) && (!$is_ver146 || $follow_wikiname || preg_match('/^size$/i', $matches[1])))
						$plain = str_replace($matches[0], '#'.$matches[2].'#', $plain);
					else
						$plain = str_replace($matches[0], '#', $plain);
				}
				if(preg_match($link_pattern, $plain)){
					$replacements = array(
						'[[$1>'.$anchor.']]',
					'[['.$category.'>'.$anchor.']]'
				);
					$_body[] = rtrim(preg_replace($replace_patterns, $replacements, $data)) . "\n";
					if(preg_match('/^[^-+:|,]/', $data))
						$_body[] = "\n";
				}
			}
			if ($_body) {
				if ($args['eachpage']) {
					$body[] = '-[[' . $page . ']] [ ' . $this->func->get_heading($page) . ' ]' . "\n";
					$body[] = '#block(class:context){{{{{{{{{{' . "\n";
				}
				foreach($_body as $__body) {
					$body[] = $__body;
				}
				if ($args['eachpage']) {
					$body[] = '}}}}}}}}}}' . "\n";
				}
			}
		}
		if ($body) {
			$this->func->cleanup_template_source($body);
			if (!$args['eachpage']) {
				array_unshift($body, '#block(class:context){{{{{{{{{{' . "\n");
				$body[] = '}}}}}}}}}}' . "\n";
			}
			$contents_auto_insertion = $this->root->contents_auto_insertion;
			$this->root->contents_auto_insertion = 0;
			$retval = $this->func->convert_html($body);
			$this->root->contents_auto_insertion = $contents_auto_insertion;
		} else {
			$retval = "No related pages found.<br />\n";
		}
		$busy[$this->xpwiki->pid] = false;
		return $retval;
	}
	
	function get_fullname($match) {
		return '[[' . $match[1] . $this->func->get_fullname($match[2], $this->_page) . $match[3] . ']]';
	}
}
?>