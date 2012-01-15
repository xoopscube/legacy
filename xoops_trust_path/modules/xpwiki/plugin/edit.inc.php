<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: edit.inc.php,v 1.73 2012/01/14 03:39:51 nao-pon Exp $
// Copyright (C) 2001-2006 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Edit plugin (cmd=edit)

class xpwiki_plugin_edit extends xpwiki_plugin {
	function plugin_edit_init () {
		// Remove #freeze written by hand
		$this->cont['PLUGIN_EDIT_FREEZE_REGEX'] = '/^(?:#freeze(?!\w)\s*)+/im';
	}

	function plugin_edit_action()
	{
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');

		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// check alias page
		if (!$this->func->is_page($page) && isset($this->root->page_aliases[$page])) {
			$page = $this->root->page_aliases[$page];
		}

		if ($page && $this->root->page_case_insensitive) {
			$this->func->get_pagename_realcase($page);
			$this->root->get['page'] = $this->root->post['page'] = $this->root->vars['page'] = $page;
		}

		$this->func->check_editable($page, true, true);

		if (isset($this->root->vars['write'])) {
			if ($this->func->check_riddle()) {
				return $this->plugin_edit_write();
			} else {
				return $this->plugin_edit_preview(TRUE);
			}
		} else if (isset($this->root->vars['preview']) || ($this->root->load_template_func && isset($this->root->vars['template']))) {
			return $this->plugin_edit_preview();
		} else if (isset($this->root->vars['cancel'])) {
			return $this->plugin_edit_cancel();
		}

		$title = $source = '';

		if (!empty($this->root->get['backup'])) {
			$backup_age = intval($this->root->get['backup']);
			$backup = $this->func->get_backup($page, $backup_age);
			$source = $backup['data'];
			$title = '$1 - ' . str_replace('$1', $backup_age, $this->root->_msg_backupedit);
		}

		if (!$source) {
			$source = $this->func->get_source($page);
		}
		if (is_array($source)) $postdata = join('', $source);
		$this->root->vars['orgkey'] = ($postdata)? $this->func->cache_save_db($postdata, 'edit') : '';
		if (! empty($this->root->vars['paraid'])) {
			$_postdata = $postdata;
			$postdata = $this->plugin_edit_parts($this->root->vars['paraid'], $source);
			if ($postdata === FALSE) {
				unset($this->root->vars['paraid']);
				$postdata = $_postdata; // なかったことに :)
			}
		}

		if ($postdata == '') $postdata = $this->func->auto_template($page);

		// Q & A 認証
		$options = $this->get_riddle();

		$body = $this->func->edit_form($page, $postdata, FALSE, TRUE, $options);

		if (isset($this->root->vars['ajax'])) {
			$this->func->convert_finisher($body);
			$body = <<<EOD
<editform><![CDATA[{$body}]]></editform>
EOD;
			$this->func->send_xml($body);
		}

		return array('msg' => ($title ? $title : $this->root->_title_edit), 'body' => $body);
	}

	// Preview
	function plugin_edit_preview($ng_riddle = FALSE)
	{
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// Loading template
		if (! $ng_riddle && isset($this->root->vars['template_page']) && $this->func->is_page($this->root->vars['template_page'])) {

			$this->root->vars['msg'] = $this->func->get_source($this->root->vars['template_page'], TRUE, TRUE);

			// Cut fixed anchors
			$this->root->vars['msg'] = preg_replace('/^(\*{1,5}.*)\[#[A-Za-z][\w-]+\](.*)$/m', '$1$2', $this->root->vars['msg']);
		}

		$this->root->vars['msg'] = preg_replace($this->cont['PLUGIN_EDIT_FREEZE_REGEX'], '', $this->root->vars['msg']);
		$postdata = $this->root->vars['msg'];

		if (isset($this->root->vars['add']) && $this->root->vars['add']) {
			if (isset($this->root->vars['add_top']) && $this->root->vars['add_top']) {
				$postdata  = $postdata . "\n\n" . $this->func->get_source($page, TRUE, TRUE);
			} else {
				$postdata  = $this->func->get_source($page, TRUE, TRUE) . "\n\n" . $postdata;
			}
		}

		$body = $this->root->_msg_preview . '<br />' . "\n";
		if ($postdata == '') {
			$body .= '<strong>' . $this->root->_msg_preview_delete . '</strong><br />' . "\n";
		}

		$this->root->rtf['preview'] = TRUE;
		if ($postdata) {
			$postdata = $this->func->make_str_rules($postdata);
			$postdata = explode("\n", $postdata);
			$postdata = $this->func->drop_submit($this->func->convert_html($postdata));
			// Add target="_blank"
			$postdata = preg_replace_callback(
						'/(<script.*?<\/script>)|(<a[^>]+)>/isS' ,
						create_function('$arr', 'return $arr[1]? $arr[1] : ((strpos($arr[2], \'target=\') === FALSE)? "$arr[2] target=\"_blank\">" : "$arr[0]");') ,
						$postdata
					);
			if (isset($this->root->vars['ajax'])) {
				$class = 'ajax_preview';
				if (isset($this->root->rtf['useJavascriptInHead'])) {
					$postdata = '<script src="" />';
				} else {
					$postdata = str_replace(array('<![CDATA[', ']]>'), '', $postdata);
				}
			} else {
				$class = 'preview';
			}
			$body .= '<div id="xpwiki_preview_area" class="' . $class . '">' . $postdata . '</div>' . "\n";
			if (empty($this->root->vars['ajax'])) $body .= <<<EOD
<script type="text/javascript">
<!--
new Resizable('xpwiki_preview_area', {mode:'y'});
-->
</script>
EOD;
		}

		// Q & A 認証
		$options = $this->get_riddle();

		if (isset($this->root->vars['ajax'])) {
			// xml special chars
			// clear output buffer
			$this->func->clear_output_buffer();
			// cont['USER_NAME_REPLACE'] を 置換
			$body = str_replace(
					array($this->cont['USER_NAME_REPLACE'], $this->cont['USER_CODE_REPLACE']) ,
					array($this->root->userinfo['uname_s'], $this->root->userinfo['ucd']) ,
					$body);
			$body = preg_replace('/<div id="(xpwiki_body|'.preg_quote($this->root->vars['paraid'], '/').')"/', '<div ', $body);
			$body .= $this->func->edit_form($page, $this->root->vars['msg'], $this->root->vars['digest'], TRUE, $options);

			$this->func->convert_finisher($body);

			$title = (!$ng_riddle)? $this->root->_title_preview : $this->root->_title_ng_riddle;
			$title = '<h3>'.str_replace('$1', htmlspecialchars($page), $title).'</h3>';
			$body = $title.$body;

			if (preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $body)) {
				if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
					HypCommonFunc::loadClass('MobilePictogramConverter');
				}
				if (XC_CLASS_EXISTS('MobilePictogramConverter')) {
					$mpc =& MobilePictogramConverter::factory_common();
					$mpc->setImagePath($this->cont['ROOT_URL'] . 'images/emoji');
					$mpc->setString($body, FALSE);
					$body = $mpc->autoConvertModKtai();
				}
			}

			$body = <<<EOD
<xpwiki>
<content><![CDATA[{$body}]]></content>
<mode>preview</mode>
</xpwiki>
EOD;
			$this->func->send_xml($body);
		} else {
			$body .= $this->func->edit_form($page, $this->root->vars['msg'], (isset($this->root->vars['digest'])? $this->root->vars['digest'] : FALSE), TRUE, $options);
		}

		return array('msg'=>(!$ng_riddle)? $this->root->_title_preview : $this->root->_title_ng_riddle, 'body'=>$body);
	}

	// Inline: Show edit (or unfreeze text) link
	function plugin_edit_inline()
	{
		static $usage = array();
		if (!isset($usage[$this->xpwiki->pid])) {
			$usage[$this->xpwiki->pid] = '&edit(pagename#anchor[[,noicon],nolabel])[{label}];';
		}

		if ($this->cont['PKWK_READONLY']) return ''; // Show nothing

		// Arguments
		$args = func_get_args();

		// {label}. Strip anchor tags only
		$s_label = $this->func->strip_htmltag(array_pop($args), FALSE);

		$page    = array_shift($args);
		if ($page === NULL) $page = '';
		$_noicon = $_nolabel = $_paraedit = FALSE;
		foreach($args as $arg){
			switch(strtolower($arg)){
			case ''        :                    break;
			case 'paraedit': $_paraedit = TRUE; break;
			case 'nolabel' : $_nolabel  = TRUE; break;
			case 'noicon'  : $_noicon   = TRUE; break;
			default        : return $usage[$this->xpwiki->pid];
			}
		}
		if ($_paraedit) $_nolabel = TRUE;

		// Separate a page-name and a fixed anchor
		list($s_page, $id, $editable) = $this->func->anchor_explode($page, TRUE);

		// Default: This one
		if ($s_page === '') $s_page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// 編集権限チェック
		$is_editable = $this->func->check_editable($s_page,FALSE,FALSE);

		// $s_page fixed
		$isfreeze = $this->func->is_freeze($s_page);
		$ispage   = $this->func->is_page($s_page);
		//if ($_paraedit && ($isfreeze || !$is_editable)) return ''; // Show nothing
		if ($isfreeze || !$is_editable) return ''; // Show nothing

		// Paragraph edit enabled or not
		$short = htmlspecialchars('Edit');
		$js = $ajax = '';
		$ajaxurl = htmlspecialchars(rawurlencode($s_page), ENT_QUOTES);
		if ($this->root->fixed_heading_anchor_edit && $editable && $ispage && ! $isfreeze) {
			// Paragraph editing
			$js = ' onmouseover="wikihelper_area_highlite(\'' . htmlspecialchars($id) . '\',1);"' .
					' onmouseout="wikihelper_area_highlite(\'' . htmlspecialchars($id) . '\',0);"';
			$ajax = ($this->root->use_ajax_edit && $this->root->render_mode === 'main')? ' onclick="return xpwiki_ajax_edit(\'' . $ajaxurl . '\',\'' . htmlspecialchars($id) . '\');"' : '';
			$id    = rawurlencode($id);
			$title = htmlspecialchars(str_replace('$1', $s_page.$page, $this->root->_title_edit));
			$icon = '<img src="' . $this->cont['IMAGE_DIR'] . 'paraedit.png' .
			'" width="9" height="9" alt="' .
			$short . '" title="' . $title . '" /> ';
			$class = ' class="anchor_super"';
		} else {
			// Normal editing / unfreeze
			$id    = '';
			if ($isfreeze) {
				$title = 'Unfreeze %s';
				$icon  = 'unfreeze.png';
			} else {
				$title = 'Edit %s';
				$icon  = 'edit.png';
				$ajax = ($this->root->use_ajax_edit && $this->root->render_mode === 'main')? ' onclick="return xpwiki_ajax_edit(\'' . $ajaxurl . '\');"' : '';
			}
			$title = htmlspecialchars(sprintf($title, $s_page));
			$icon = '<img src="' . $this->cont['IMAGE_DIR'] . $icon .
			'" width="20" height="20" alt="' .
			$short . '" title="' . $title . '" />';
			$class = '';
		}
		if ($_noicon) $icon = ''; // No more icon
		if ($_nolabel) {
			if (!$_noicon) {
				$s_label = '';     // No label with an icon
			} else {
				$s_label = $short; // Short label without an icon
			}
		} else {
			if ($s_label == '') $s_label = $title; // Rich label with an icon
		}

		// URL
		if ($isfreeze) {
			$url   = $this->root->script . '?cmd=unfreeze&amp;page=' . rawurlencode($s_page);
		} else {
			$s_id = ($id == '') ? '' : '&amp;paraid=' . $id;
			$url  = $this->root->script . '?cmd=edit&amp;page=' . rawurlencode($s_page) . $s_id;
		}
		$atag  = '<a' . $class . ' href="' . $url . '" title="' . $title . '"' . $js . $ajax . '>';
		static $atags = array();
		if (!isset($atags[$this->xpwiki->pid])) {$atags[$this->xpwiki->pid] = '</a>';}

		if (!empty($this->root->rtf['preview'])) {
			// Preview mode
			return $icon . $s_label;
		} else if ($ispage) {
			// Normal edit link
			return $atag . $icon . $s_label . $atags[$this->xpwiki->pid];
		} else {
			// Dangling edit link
			return '<span class="noexists">' . $atag . $icon . $atags[$this->xpwiki->pid] .
			$s_label . $atag . '?' . $atags[$this->xpwiki->pid] . '</span>';
		}
	}

	// Write, add, or insert new comment
	function plugin_edit_write()
	{
		$_uname = (empty($this->root->vars['uname']) || !empty($this->root->vars['anonymous']))? $this->root->siteinfo['anonymous'] : $this->root->vars['uname'];
		if ($_uname) {
			if (! empty($this->root->vars['anonymous'])) {
				$this->root->cookie['name'] = $_uname;
			} else {
				// save name to cookie
				$this->func->save_name2cookie($_uname);
			}
		}

		$page   = isset($this->root->vars['page'])   ? $this->root->vars['page']   : '';
		$add    = isset($this->root->vars['add'])    ? $this->root->vars['add']    : '';
		$digest = isset($this->root->vars['digest']) ? $this->root->vars['digest'] : '';
		$paraid = isset($this->root->vars['paraid']) ? $this->root->vars['paraid'] : '';
		$original = '';

		$this->root->vars['msg'] = preg_replace($this->cont['PLUGIN_EDIT_FREEZE_REGEX'], '', $this->root->vars['msg']);
		$this->root->vars['msg'] = $this->func->remove_pginfo($this->root->vars['msg']);
		$msg = & $this->root->vars['msg']; // Reference

		// Get original data from cache DB.
		if (! empty($this->root->vars['orgkey'])) {
			$original = (string)$this->func->cache_get_db($this->root->vars['orgkey'], 'edit', true);
			$original = $this->func->remove_pginfo($original);
		}

		// ParaEdit
		$hash = '';
		if ($paraid) {
			if (! $original) {
				$original = $this->func->remove_pginfo($this->func->get_source($page, TRUE, TRUE));
			}
			$source = preg_split('/([^\n]*\n)/', $original, -1,
				PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			if ($this->plugin_edit_parts($paraid, $source, $msg) !== FALSE) {
				$fullmsg = join('', $source);
			} else {
				// $this->root->vars['msg']だけがページに書き込まれてしまうのを防ぐ。
				$fullmsg = rtrim($original) . "\n\n" . $msg;
			}
			$msg = $fullmsg;
			$hash = '#' . $paraid;
		}

		// 文末処理
		$msg = rtrim($msg)."\n";

		// 改行・TAB・スペースのみだったら削除とみなす
		$msg = preg_replace('/^\s+$/', '', $msg);

		// Page title
		if ($msg && ! empty($this->root->post['pgtitle'])) {
			$msg = $this->root->title_setting_string . trim($this->root->post['pgtitle']) . "\n" . $msg;
		}

		$retvars = array();

		// Collision Detection
		$oldpagesrc = $this->func->get_source($page, TRUE, TRUE);
		$oldpagemd5 = $this->func->get_digests($oldpagesrc);
		if ($digest != $oldpagemd5) {
			$this->root->vars['digest'] = $oldpagemd5; // Reset
			unset($this->root->vars['paraid']); // 更新が衝突したら全文編集に切り替え

			$oldpagesrc = $this->func->remove_pginfo($oldpagesrc);
			list($postdata_input, $auto) = $this->func->do_update_diff($oldpagesrc, $msg, $original);

			$retvars['msg' ] = $this->root->_title_collided;
			$retvars['body'] = ($auto ? $this->root->_msg_collided_auto : $this->root->_msg_collided) . "\n";
			$retvars['body'] .= $this->root->do_update_diff_table;
			$retvars['body'] .= $this->func->edit_form($page, $postdata_input, $oldpagemd5, FALSE);

			if (isset($this->root->vars['ajax'])) {
				$this->func->convert_finisher($retvars['body']);
				$body = <<<EOD
<xpwiki>
<content><![CDATA[{$retvars['body']}]]></content>
<mode>preview</mode>
</xpwiki>
EOD;
				$this->func->send_xml($body);
			}

			return $retvars;
		}

		// Action?
		if ($add) {
			// Add
			if (isset($this->root->vars['add_top']) && $this->root->vars['add_top']) {
				$postdata  = $msg . "\n\n" . $this->func->get_source($page, TRUE, TRUE);
			} else {
				$postdata  = $this->func->get_source($page, TRUE, TRUE) . "\n\n" . $msg;
			}
		} else {
			// Edit or Remove
			$postdata = & $msg; // Reference
		}

		// NULL POSTING, OR removing existing page
		if (! $postdata) {
			$this->func->page_write($page, '');

			if ($this->root->trackback) $this->func->tb_delete($page);

			if ($this->root->maxshow_deleted && $this->func->is_page($this->root->whatsdeleted)) {
				$url = $this->func->get_page_uri($this->root->whatsdeleted , true);
			} else {
				$url = $this->cont['HOME_URL'];
			}
			$title = str_replace('$1', htmlspecialchars($page), $this->root->_title_deleted);

			if (isset($this->root->vars['ajax'])) {
				$url = htmlspecialchars($url, ENT_QUOTES);
				$body = <<<EOD
<xpwiki>
<content><![CDATA[{$title}]]></content>
<mode>delete</mode>
<url>{$url}</url>
</xpwiki>
EOD;
				$this->func->send_xml($body);
			}

			$this->func->redirect_header($url, 1, $title);
		}

		// $notimeupdate: Checkbox 'Do not change timestamp'
		$notimestamp = isset($this->root->vars['notimestamp']) && $this->root->vars['notimestamp'] != '';
		if ($this->root->notimeupdate > 1 && ! $this->root->userinfo['admin']) {
			$notimestamp = false;
		}

		$this->func->page_write($page, $postdata, $this->root->notimeupdate != 0 && $notimestamp);

		if (isset($this->root->vars['ajax'])) {
			if (!empty($this->root->vars['nonconvert'])) {
				$body = '';
			} else {
				$obj = new XpWiki($this->root->mydirname);
				$obj->init($page);
				$obj->root->userinfo['uname_s'] = htmlspecialchars($this->root->cookie['name']);
				$obj->execute();
				if (isset($obj->root->rtf['useJavascriptInHead'])) {
					$body = '<script src="" />';
				} else {
					$body = $obj->body;
					// set target
					if (isset($this->root->vars['popup'])) {
						$body = preg_replace('/(<a[^>]+)(href=(?:"|\')[^#])/isS', '$1target="' . ((intval($this->root->vars['popup']) === 1)? '_parent' : htmlspecialchars(substr($this->root->vars['popup'],0,30))) . '" $2', $body);
					}
					$body = str_replace(array('<![CDATA[', ']]>'), '', $body);
				}

				if (preg_match('/\(\([eisv]:[0-9a-f]{4}\)\)|\[emj:\d{1,4}(?::(?:im|ez|sb))?\]/S', $body)) {
					if (! XC_CLASS_EXISTS('MobilePictogramConverter')) {
						HypCommonFunc::loadClass('MobilePictogramConverter');
					}
					if (XC_CLASS_EXISTS('MobilePictogramConverter')) {
						$mpc =& MobilePictogramConverter::factory_common();
						$mpc->setImagePath($this->cont['ROOT_URL'] . 'images/emoji');
						$mpc->setString($body, FALSE);
						$body = $mpc->autoConvertModKtai();
					}
				}

			}
			$body = <<<EOD
<xpwiki>
<content><![CDATA[{$body}]]></content>
<mode>write</mode>
</xpwiki>
EOD;
			$this->func->send_xml($body);
		}

		$this->func->send_location($page, $hash);
	}

	// Cancel (Back to the page / Escape edit page)
	function plugin_edit_cancel()
	{
		if ($this->func->is_page($this->root->vars['page'])) {
			$ret = $this->root->vars['page'];
		} else {
			if (empty($this->root->vars['refer'])) {
				$ret = $this->root->defaultpage;
			} else {
				$ret = $this->root->vars['refer'];
			}
		}
		// ParaEdit
		$paraid = isset($this->root->vars['paraid']) ? $this->root->vars['paraid'] : '';
		$hash = '';
		if ($paraid) {
			$hash = '#' . $paraid;
		}
		$this->func->send_location($ret, $hash);
	}

	// ソースの一部を抽出/置換する
	function plugin_edit_parts($id, & $source, $postdata = '')
	{
		$id = preg_quote($id, '/');
		$postdata = rtrim($postdata)."\n\n";

		// 改行・TAB・スペースのみだったら削除とみなす
		$postdata = preg_replace('/^[ \s]+$/', '', $postdata);

		if ($this->root->paraedit_partarea === 'level') {
			$start = -1;
			$final = count($source);
			$multiline = 0;
			$matches = array();
			foreach ($source as $i => $line) {
				// multiline plugin. refer lib/convert_html
				if(empty($this->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK'])) {
					if ($multiline < 2) {
						if (preg_match('/^#([^\(\{]+)(?:\(([^\r]*)\))?(\{*)/', $line, $matches)) {
							$multiline  = strlen($matches[3]);
						}
					} else {
						if (preg_match('/^\}{' . $multiline . '}/', $line, $matches)) {
							$multiline = 0;
						}
						continue;
					}
				}

				if ($start === -1) {
					if (preg_match('/^(\*{1,5})(.*?)\[#(' . $id . ')\](.*?)$/m', $line, $matches)) {
						$start = $i;
						$hlen = strlen($matches[1]);
					}
				} else {
					if (preg_match('/^(\*{1,5})/m', $line, $matches) && strlen($matches[1]) <= $hlen) {
						$final = $i;
						break;
					}
				}
			}
			if ($start !== -1) {
				return join('', array_splice($source, $start, $final - $start, $postdata));
			}
		} else {

			$heads = preg_grep('/^\*{1,5}.+\[#[A-Za-z][\w-]+\].*$/', $source);
			$heads[count($source)] = ''; // Sentinel

			while (list($start, $line) = each($heads)) {
				if (preg_match('/\[#' . $id . '\]/', $line)) {
					list($end, $line) = each($heads);
					return join('', array_splice($source, $start, $end - $start, $postdata));
				}
			}

		}
		return FALSE;
	}

	// Q & A 認証用 $option 取得
	function get_riddle () {
		if ($this->root->userinfo['admin'] ||
			$this->root->riddle_auth === 0 ||
			($this->root->riddle_auth === 1 && $this->root->userinfo['uid'] !== 0)
		) {
			$options = array();
		} else {
			$options['riddle'] = array_rand($this->root->riddles);
		}
		return $options;
	}
}
?>