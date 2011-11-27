<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: rename.inc.php,v 1.18 2011/11/26 12:03:10 nao-pon Exp $
//
// Rename plugin: Rename page-name and related data
//
// Usage: http://path/to/pukiwikiphp?plugin=rename[&refer=page_name]

class xpwiki_plugin_rename extends xpwiki_plugin {
	function plugin_rename_init() {
		$this->conf['popup'] = array(
			'top' => '0px',
			'right' => '0px',
			'left' => '',
			'bottom' => '',
			'width' => '300px',
			'height' => '98%'
		);
	}

	function plugin_rename_action()
	{
	//	global $whatsnew;

		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits this');

		// 管理画面モード指定
		// 便宜上、ログインしていなくてもパスワードで実行できるようにしておく。
		if ($this->root->userinfo['admin'] && $this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}

		$popup_pos = '';
		foreach(array('top', 'left', 'bottom', 'right', 'width', 'height') as $_prm) {
			if (isset($this->conf['popup'][$_prm])) {
				if (preg_match('/^(\d+)(%|p(?:x|c|t)|e(?:m|x)|in|(?:c|m)m)?/', $this->conf['popup'][$_prm], $_match)) {
				 	if (empty($_match[2])) $_match[2] = 'px';
				 	$popup_pos .= ',' . $_prm . ':\'' . $_match[1] . $_match[2] . '\'';
				}
			}
		}
		$this->make_pagelink_options = array('popup' => array('use' => true, 'position' => $popup_pos));


		$method = $this->plugin_rename_getvar('method');
		$regex = $this->plugin_rename_getvar('regex');

		if ($method == 'regex') {
			$src = $this->plugin_rename_getvar('src');
			if ($src == '') return $this->plugin_rename_phase1();

			if ($regex) {
				$src_pattern = '#' . $src . '#';
			} else {
				$src_pattern = '/' . preg_quote($src, '/') . '/';
			}

			$arr0 = preg_grep($src_pattern, $this->func->get_existpages());
			if (! is_array($arr0) || empty($arr0))
				return $this->plugin_rename_phase1('nomatch');

			$dst = $this->plugin_rename_getvar('dst');
			$arr1 = preg_replace($src_pattern, $dst, $arr0);
			foreach ($arr1 as $page)
				if (! $this->func->is_pagename($page))
					return $this->plugin_rename_phase1('notvalid');

			return $this->plugin_rename_regex($arr0, $arr1);

		} else {
			// $method == 'page'
			$page  = $this->plugin_rename_getvar('page');
			$refer = $this->plugin_rename_getvar('refer');

			if ($refer === '') {
				return $this->plugin_rename_phase1();

			} else if (! $this->func->is_page($refer)) {
				return $this->plugin_rename_phase1('notpage', $refer);

			} else if ($refer === $this->root->whatsnew) {
				return $this->plugin_rename_phase1('norename', $refer);

			} else if ($page === '' || $page === $refer) {
				return $this->plugin_rename_phase2();

			} else if (! $this->func->is_pagename($page)) {
				return $this->plugin_rename_phase2('notvalid');

			} else {
				return $this->plugin_rename_refer();
			}
		}
	}

	// 変数を取得する
	function plugin_rename_getvar($key)
	{
	//	global $vars;
		return isset($this->root->vars[$key]) ? $this->root->vars[$key] : '';
	}

	// エラーメッセージを作る
	function plugin_rename_err($err, $page = '')
	{
	//	global $_rename_messages;

		if ($err == '') return '';

		$body = $this->root->_rename_messages['err_' . $err];
		if (is_array($page)) {
			$tmp = '';
			foreach ($page as $_page) {
				$tmp .= "- [[$_page]]\n";
			}
			$page = $tmp;
		}
		if ($page !== '') $body = sprintf($body, $this->func->convert_html($page));

		$msg = sprintf($this->root->_rename_messages['err'], $body);
		return $msg;
	}

	//第一段階:ページ名または正規表現の入力
	function plugin_rename_phase1($err = '', $page = '')
	{
	//	global $script, $_rename_messages;

		$msg    = $this->plugin_rename_err($err, $page);
		$refer  = $this->plugin_rename_getvar('refer');
		$method = $this->plugin_rename_getvar('method');

		$radio_regex = $radio_page = '';
		if ($method == 'regex') {
			$radio_regex = ' checked="checked"';
		} else {
			$radio_page  = ' checked="checked"';
		}
		$select_refer = $this->plugin_rename_getselecttag($refer);

		$s_src = htmlspecialchars($this->plugin_rename_getvar('src'));
		$s_dst = htmlspecialchars($this->plugin_rename_getvar('dst'));
		$script = $this->func->get_script_uri();
		$ret = array();
		$ret['msg']  = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
$msg
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="radio" name="method" id="_p_rename_method_page" value="page"$radio_page />
  <label for="_p_rename_method_page">{$this->root->_rename_messages['msg_page']}:</label>$select_refer<br />
  <input type="radio" name="method" id="_p_rename_method_reg" value="regex"$radio_regex />
  <label for="_p_rename_method_reg">{$this->root->_rename_messages['msg_part_rep']}:</label>
  <input type="checkbox" value="1" name="regex" id="_p_rename_regex"><label for="_p_rename_regex"> {$this->root->_rename_messages['msg_regex']}</label>
  <br />
  <label for="_p_rename_from">From:</label>
  <br />
  <input type="text" name="src" id="_p_rename_from" size="80" value="$s_src" /><br />
  <label for="_p_rename_to">To:</label><br />
  <input type="text" name="dst" id="_p_rename_to"   size="80" value="$s_dst" /><br />
  <input type="submit" value="{$this->root->_rename_messages['btn_next']}" /><br />
 </div>
</form>
EOD;
		return $ret;
	}

	//第二段階:新しい名前の入力
	function plugin_rename_phase2($err = '', $page = '')
	{
	//	global $script, $_rename_messages;

		$msg   = $this->plugin_rename_err($err, $page);
		$page  = $this->plugin_rename_getvar('page');
		$refer = $this->plugin_rename_getvar('refer');
		if ($page === '') $page = $refer;

		$msg_related = '';
		$related = $this->plugin_rename_getrelated($refer);
		if (! empty($related))
			$msg_related = '<label for="_p_rename_related">' . $this->root->_rename_messages['msg_do_related'] . '</label>' .
		'<input type="checkbox" name="related" id="_p_rename_related" value="1" checked="checked" /><br />';

		$msg_rename = sprintf($this->root->_rename_messages['msg_rename'], $this->func->make_pagelink($refer, htmlspecialchars($refer), '', '', 'pagelink', $this->make_pagelink_options));
		$s_page  = htmlspecialchars($page);
		$s_refer = htmlspecialchars($refer);
		$script = $this->func->get_script_uri();
		$ret = array();
		$ret['msg']  = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
$msg
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="hidden" name="refer"  value="$s_refer" />
  $msg_rename<br />
  <label for="_p_rename_newname">{$this->root->_rename_messages['msg_newname']}:</label>
  <input type="text" name="page" id="_p_rename_newname" size="80" value="$s_page" /><br />
  $msg_related
  <input type="submit" value="{$this->root->_rename_messages['btn_next']}" /><br />
 </div>
</form>
EOD;
		if (! empty($related)) {
			$ret['body'] .= '<hr /><p>' . $this->root->_rename_messages['msg_related'] . '</p><ul>';
			sort($related);
			foreach ($related as $name)
				$ret['body'] .= '<li>' . $this->func->make_pagelink($name, htmlspecialchars($name), '', '', 'pagelink', $this->make_pagelink_options) . '</li>';
			$ret['body'] .= '</ul>';
		}
		return $ret;
	}

	//ページ名と関連するページを列挙し、phase3へ
	function plugin_rename_refer()
	{
		$page  = $this->plugin_rename_getvar('page');
		$refer = $this->plugin_rename_getvar('refer');

		//$pages[$this->func->encode($refer)] = $this->func->encode($page);
		$pages[$this->func->encode($refer)] = $page;

		if ($this->plugin_rename_getvar('related') != '') {
			$from = $this->func->strip_bracket($refer);
			$to   = $this->func->strip_bracket($page);
			foreach ($this->plugin_rename_getrelated($refer) as $_page) {
				//$pages[$this->func->encode($_page)] = $this->func->encode(str_replace($from, $to, $_page));
				$pages[$this->func->encode($_page)] = str_replace($from, $to, $_page);
			}
		}
		$exists = array();
		foreach($pages as $_from => $_to) {
			if ($this->func->is_page($_to) || in_array($_to, array_map('strval', array_keys($this->root->page_aliases)))) {
				$exists[] = $page;
			} else {
				$pages[$_from] = $this->func->encode($_to);
			}
		}

		if ($exists) {
			return $this->plugin_rename_phase2('already', $exists);
		}
		return $this->plugin_rename_phase3($pages);
	}

	//正規表現でページを置換
	function plugin_rename_regex($arr_from, $arr_to)
	{
		$exists = array();
		foreach ($arr_to as $page)
			if ($this->func->is_page($page) || in_array($page, array_map('strval', array_keys($this->root->page_aliases))))
				$exists[] = $page;

		if (! empty($exists)) {
			return $this->plugin_rename_phase1('already', $exists);
		} else {
			$pages = array();
			foreach ($arr_from as $refer)
				$pages[$this->func->encode($refer)] = $this->func->encode(array_shift($arr_to));
			return $this->plugin_rename_phase3($pages);
		}
	}

	function plugin_rename_phase3($pages)
	{
	//	global $script, $_rename_messages;

		$msg = $input = '';
		$files = $this->plugin_rename_get_files($pages);

		$exists = array();
		foreach ($files as $_page=>$arr)
			foreach ($arr as $old=>$new)
				if (is_file($new))
					$exists[$_page][$old] = $new;

		$pass = $this->plugin_rename_getvar('pass');
		$pmode = $this->plugin_rename_getvar('pmode');
		if ($pmode === 'proceed' && $this->func->pkwk_login($pass)) {
			return $this->plugin_rename_proceed($pages, $files, $exists);
		} else if ($pass != '') {
			$msg = $this->plugin_rename_err('adminpass');
		}

		$method = $this->plugin_rename_getvar('method');
		if ($method == 'regex') {
			$s_src = htmlspecialchars($this->plugin_rename_getvar('src'));
			$s_dst = htmlspecialchars($this->plugin_rename_getvar('dst'));
			$msg   .= $this->root->_rename_messages['msg_part_rep'] . '<br />';
			$input .= '<input type="hidden" name="method" value="regex" />';
			$input .= '<input type="hidden" name="src"    value="' . $s_src . '" />';
			$input .= '<input type="hidden" name="dst"    value="' . $s_dst . '" />';
		} else {
			$s_refer   = htmlspecialchars($this->plugin_rename_getvar('refer'));
			$s_page    = htmlspecialchars($this->plugin_rename_getvar('page'));
			$s_related = htmlspecialchars($this->plugin_rename_getvar('related'));
			$msg   .= $this->root->_rename_messages['msg_page'] . '<br />';
			$input .= '<input type="hidden" name="method"  value="page" />';
			$input .= '<input type="hidden" name="refer"   value="' . $s_refer   . '" />';
			$input .= '<input type="hidden" name="page"    value="' . $s_page    . '" />';
			$input .= '<input type="hidden" name="related" value="' . $s_related . '" />';
		}

		if (! empty($exists)) {
			$msg .= $this->root->_rename_messages['err_already_below'] . '<ul>';
			foreach ($exists as $page=>$arr) {
				$pname = $this->func->decode($page);
				$msg .= '<li>' . $this->func->make_pagelink($pname, htmlspecialchars($pname), '', '', 'pagelink', $this->make_pagelink_options);
				$msg .= $this->root->_rename_messages['msg_arrow'];
				$msg .= htmlspecialchars($pname);
				if (! empty($arr)) {
					$msg .= '<ul>' . "\n";
					foreach ($arr as $ofile=>$nfile)
						$msg .= '<li>' . $ofile .
					$this->root->_rename_messages['msg_arrow'] . $nfile . '</li>' . "\n";
					$msg .= '</ul>';
				}
				$msg .= '</li>' . "\n";
			}
			$msg .= '</ul><hr />' . "\n";

			$input .= '<input type="radio" name="exist" value="0" checked="checked" />' .
			$this->root->_rename_messages['msg_exist_none'] . '<br />';
			$input .= '<input type="radio" name="exist" value="1" />' .
			$this->root->_rename_messages['msg_exist_overwrite'] . '<br />';
		}

		$passform = ($this->root->userinfo['admin'])? '' :
			'<label for="_p_rename_adminpass">'.$this->root->_rename_messages['msg_adminpass'].'</label>
  <input type="password" name="pass" id="_p_rename_adminpass" value="" />';

		$script = $this->func->get_script_uri();
		$ret = array();
		$ret['msg'] = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
<p>$msg</p>
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="hidden" name="pmode" value="proceed" />
  $input
  $passform
  <input type="submit" value="{$this->root->_rename_messages['btn_submit']}" />
 </div>
</form>
<p>{$this->root->_rename_messages['msg_confirm']}</p>
EOD;

		ksort($pages);
		$ret['body'] .= '<ul>' . "\n";
		foreach ($pages as $old=>$new)
			$oldname = $this->func->decode($old);
			$ret['body'] .= '<li>' .  $this->func->make_pagelink($oldname, htmlspecialchars($oldname), '', '', 'pagelink',$this->make_pagelink_options) .
			$this->root->_rename_messages['msg_arrow'] .
			htmlspecialchars($this->func->decode($new)) .  '</li>' . "\n";
		$ret['body'] .= '</ul>' . "\n";
		return $ret;
	}

	function plugin_rename_get_files($pages)
	{
		$files = array();
		$dirs  = array($this->cont['BACKUP_DIR'], $this->cont['DIFF_DIR'], $this->cont['DATA_DIR'], $this->cont['TRACKBACK_DIR']);
		if ($this->func->exist_plugin_convert('attach'))  $dirs[] = $this->cont['UPLOAD_DIR'];
		//if ($this->func->exist_plugin_convert('counter')) $dirs[] = $this->cont['COUNTER_DIR'];
		// and more ...

		$matches = array();
		foreach ($dirs as $path) {
			$dir = opendir($path);
			if (! $dir) continue;

			while ($file = readdir($dir)) {
				if ($file == '.' || $file == '..') continue;

				foreach ($pages as $from=>$to) {
					$pattern = '/^' . str_replace('/', '\/', $from) . '([._].+)$/';
					if (! preg_match($pattern, $file, $matches))
						continue;

					$newfile = $to . $matches[1];
					$files[$from][$path . $file] = $path . $newfile;
				}
			}
		}
		return $files;
	}

	function plugin_rename_proceed($pages, $files, $exists)
	{

		if ($this->plugin_rename_getvar('exist') == '')
			foreach ($exists as $key=>$arr)
				unset($files[$key]);

		foreach ($files as $page=>$arr) {
			foreach ($arr as $old=>$new) {
				@ set_time_limit(30);
				if (isset($exists[$page][$old]) && $exists[$page][$old])
					unlink($new);
				rename($old, $new);
			}
		}

		clearstatcache();

		$postdata = $this->func->get_source($this->cont['PLUGIN_RENAME_LOGPAGE']);
		$postdata[] = '*' . $this->root->now . "\n";
		if ($this->plugin_rename_getvar('method') == 'regex') {
			$postdata[] = '-' . $this->root->_rename_messages['msg_part_rep'] . "\n";
			$postdata[] = '--From:[[' . $this->plugin_rename_getvar('src') . ']]' . "\n";
			$postdata[] = '--To:[['   . $this->plugin_rename_getvar('dst') . ']]' . "\n";
		} else {
			$postdata[] = '-' . $this->root->_rename_messages['msg_page'] . "\n";
			$postdata[] = '--From:[[' . $this->plugin_rename_getvar('refer') . ']]' . "\n";
			$postdata[] = '--To:[['   . $this->plugin_rename_getvar('page')  . ']]' . "\n";
		}

		if (! empty($exists)) {
			$postdata[] = "\n" . $this->root->_rename_messages['msg_result'] . "\n";
			foreach ($exists as $page=>$arr) {
				$postdata[] = '-' . $this->func->decode($page) .
				$this->root->_rename_messages['msg_arrow'] . $this->func->decode($pages[$page]) . "\n";
				foreach ($arr as $ofile=>$nfile)
					$postdata[] = '--' . $ofile .
					$this->root->_rename_messages['msg_arrow'] . $nfile . "\n";
			}
			$postdata[] = '----' . "\n";
		}

		$alias_up = false;
		foreach ($pages as $old=>$new) {
			@ set_time_limit(30);

			$old = $this->func->decode($old);
			$new = $this->func->decode($new);
			$postdata[] = '-' . $old .
			$this->root->_rename_messages['msg_arrow'] . $new . "\n";

			// pginfo DB 更新
			$this->func->pginfo_rename_db_write($old, $new);

			// Page alias
			foreach($this->root->page_aliases as $alias => $page) {
				if ($page === $old) {
					$this->root->page_aliases[$alias] = $new;
					$alias_up = true;
				}
			}

			$source = $this->func->get_source($new, TRUE, TRUE);
			// PageWriteBefore
			$this->func->do_onPageWriteBefore($old, '', 1, 'delete', FALSE);
			$this->func->do_onPageWriteBefore($new, $source, 1, 'insert', FALSE);
			// onPageWriteAfter
			$this->func->do_onPageWriteAfter($old, '', 1, 'delete', '', FALSE);
			$this->func->do_onPageWriteAfter($new, $source, 1, 'insert', '', FALSE);
		}
		// 各種キャッシュ更新など
		$this->func->delete_caches();
		if ($alias_up) {
			$this->func->save_page_alias();
		}

		// 更新の衝突はチェックしない。

		// ファイルの書き込み
		$this->func->page_write($this->cont['PLUGIN_RENAME_LOGPAGE'], join('', $postdata));

		// Update Autolink
		$this->func->autolink_dat_update();

		//リダイレクト
		$page = $this->plugin_rename_getvar('page');
		if ($page === '') $page = $this->cont['PLUGIN_RENAME_LOGPAGE'];

		$this->func->send_location($page);
	}

	function plugin_rename_getrelated($page)
	{
		$related = array();
		$pages = $this->func->get_existpages();
		$pattern = '/(?:^|\/)' . preg_quote($this->func->strip_bracket($page), '/') . '(?:\/|$)/';
		foreach ($pages as $name) {
			if ($name == $page) continue;
			if (preg_match($pattern, $name)) $related[] = $name;
		}
		return $related;
	}

	function plugin_rename_getselecttag($page)
	{
	//	global $whatsnew;

		$pages = array();
		foreach ($this->func->get_existpages() as $_page) {
			if ($_page === $this->root->whatsnew) continue;

			$selected = ($_page === $page) ? ' selected' : '';
			$s_page = htmlspecialchars($_page);
			$pages[$_page] = '<option value="' . $s_page . '"' . $selected . '>' .
			$s_page . '</option>';
		}
		ksort($pages);
		$list = join("\n" . ' ', $pages);

		return <<<EOD
<select name="refer">
 <option value=""></option>
 $list
</select>
EOD;

	}
}
?>