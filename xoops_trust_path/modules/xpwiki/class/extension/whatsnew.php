<?php
//
// Created on 2006/10/29 by nao-pon http://hypweb.net/
// $Id: whatsnew.php,v 1.18 2012/01/14 03:40:01 nao-pon Exp $
//

class XpWikiExtension_whatsnew extends XpWikiExtension {

// $this->xpwiki : Parent XpWiki object.
// $this->root   : Global variable.
// $this->cont   : Constant.
// $this->func   : XpWiki functions.

	function get ($limit, $offset) {

		$i    = 0;
		$ret  = array();
		$desc = '';

		$recent_dat  = $this->cont['PKWK_MAXSHOW_CACHE'];
		//$recent_line = @file($this->cont['CACHE_DIR'] . $recent_dat);
		//$recent_arr  = array_slice($recent_line, 0, $limit);
		$recent_arr = $this->func->get_existpages(FALSE, '', array('limit' => $limit, 'order' => ' ORDER BY editedtime DESC', 'nolisting' => TRUE, 'withtime' =>TRUE, 'where' => 'editedtime < ' . $this->cont['UTIME']));

		foreach($recent_arr as $line) {
			list($time, $base) = explode("\t", trim($line));
			$localtime = $time + date('Z');
			// 追加情報取得
			$added = $this->func->get_page_changes($base);

			$uppage = dirname($base);
			while(strpos($uppage, '/') && ! $this->func->is_page($uppage)) {
				$uppage = dirname($uppage);
			}
			if ($uppage && $uppage !== '.' && !$this->func->is_page($uppage)) {
				$uppage = $this->root->defaultpage;
			}
			if ($uppage === $base || $uppage === '.' || !$uppage) {
				$ret[$i]['cat_link'] = '';
				$ret[$i]['cat_name'] = '';
			} else {
				$ret[$i]['cat_link'] = $this->func->get_page_uri($uppage, true);
				$ret[$i]['cat_name'] = $uppage;
			}

			$ret[$i]['link']  = $this->func->get_page_uri($base, true, 'keitai');
			$ret[$i]['title'] = preg_replace('/^[0-9-]+$/', $this->func->get_heading($base), $this->func->basename($base));
			$ret[$i]['time']  = $localtime;

			// 指定ページの本文などを取得
			$pginfo = $this->func->get_pginfo($base);
			$description = $this->func->get_description_cache($base, $this->root->description_max_length_rss);

			$ret[$i]['description'] = strip_tags(($added ? $added . '&#182;' : '') . $description);
			$ret[$i]['hits']        = $this->func->get_page_views($base);
			$ret[$i]['replies']     = $this->func->count_page_comments($base);
			$ret[$i]['uid']         = $pginfo['lastuid'];
			$ret[$i]['guest_name']  = $pginfo['lastuname'];

			$i++;
		}
		return $ret;
	}
}
?>