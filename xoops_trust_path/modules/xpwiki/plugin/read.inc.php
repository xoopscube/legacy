<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: read.inc.php,v 1.13 2011/12/18 00:35:06 nao-pon Exp $
//
// Read plugin: Show a page and InterWiki

class xpwiki_plugin_read extends xpwiki_plugin {
	function plugin_read_init () {

	}

	function plugin_read_action()
	{
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// check alias page
		if (!$this->func->is_page($page) && $real = $this->func->is_alias($page)) {
			if (! headers_sent()) {
				header('HTTP/1.1 301 Moved Permanently');
				header('Status: 301 Moved Permanently');
			}
			$this->func->send_location('', '', $this->func->get_page_uri($real, TRUE, 'default'));
		}

		if ($this->func->is_page($page)) {
			// ページを表示
			if ($this->func->check_readable($page, true, true)) {
				$this->func->header_lastmod($page);
				return array('msg'=>'', 'body'=>'');
			} else {
				return array('msg'=>'Not readable.', 'body'=>"\n");
			}

		}

		if (! $this->cont['PKWK_SAFE_MODE'] && $this->func->is_interwiki($page)) {
			return $this->func->do_plugin_action('interwiki'); // InterWikiNameを処理

		} else if ($this->func->is_pagename($page, false)) {
			// Case insensitive ?
			if (@ $this->root->page_case_insensitive) {
				if ($this->func->is_page($this->func->get_pagename_realcase($page))) {
					$this->root->get['page'] = $this->root->post['page'] = $this->root->vars['page'] = $page;
					// ページを表示
					$this->func->check_readable($page, true, true);
					$this->func->header_lastmod($page);
					return array('msg'=>'', 'body'=>'');
				}
			}
			if ($this->root->render_mode === 'block') {
				// ブロック表示モードは編集リンク
				return array('msg' => $this->root->_title_edit, 'body' => $this->func->make_pagelink($page));
			} else {
				if (! headers_sent()) header('HTTP/1.0 404 Not Found'); // for Serach engines
				// 存在しないので、編集フォームを表示
				$this->root->vars['cmd'] = 'edit';
				return $this->func->do_plugin_action('edit');
			}
		} else {
			// 無効なページ名
			return array(
				'msg'=>$this->root->_title_invalidwn,
				'body'=>str_replace('$1', htmlspecialchars($page),
				str_replace('$2', 'WikiName', $this->root->_msg_invalidiwn))
			);
		}
	}
}
?>