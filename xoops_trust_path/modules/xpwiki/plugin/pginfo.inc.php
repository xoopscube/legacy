<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: pginfo.inc.php,v 1.33 2011/12/13 07:45:14 nao-pon Exp $
//

class xpwiki_plugin_pginfo extends xpwiki_plugin {

	function plugin_pginfo_action()
	{
		$pmode = (empty($this->root->post['pmode']))? '' : $this->root->post['pmode'];
		$page = (isset($this->root->vars['page']))? $this->root->vars['page'] : '';

		// 権限チェック
		if ($page === '') {
			if (! $this->root->userinfo['admin']) {
				return $this->action_msg_admin_only();
			}
		} else {
			if (! $this->func->is_owner($page)) {
				return $this->action_msg_owner_only();
			}
		}

		// 言語ファイルの読み込み
		$this->load_language();

		if ($pmode === 'setparm'){
			// 登録処理
			return $this->save_parm($page);
		} else {
			if ($page === '') {
				// 管理画面モード指定
				if ($this->root->module['platform'] == "xoops") {
					$this->root->runmode = "xoops_admin";
				}
				return $this->show_admin_form();
			} else {
				// 権限設定フォーム
				return $this->show_page_form($page);
			}
		}
	}



	// 登録処理
	function save_parm ($page = '') {
		// inherit = 0:継承指定なし, 1:規定値継承指定, 2:強制継承指定
		//           3:規定値継承した値, 4:強制継承した値

		$child_dat = array();
		$do_child = FALSE;
		$redirect = '';

		if ($page !== '') {
			// ソース読み込み
			$src = $this->func->get_source($page);

			// ページ情報読み込み
			$pginfo = $this->func->get_pginfo($page);

			$is_init = (! empty($this->root->rtf['is_init']));
			if ($is_init) {
				// post チェック for dbsync.inc.php
				foreach(array('uid', 'einherit', 'vinherit') as $_key) {
					if (! isset($this->root->post[$_key])) {
						$this->root->post[$_key] = $pginfo[$_key];
					}
				}
				foreach(array('eaid', 'egid', 'vaid', 'vgid') as $_key) {
					if (! isset($this->root->post[$_key])) {
						$this->root->post[$_key] = $pginfo[$_key . 's'];
					}
				}
			}

			// 下層ページの一覧
			$cpages = $this->func->get_existpages(NULL, $page.'/');
			sort($cpages, SORT_STRING);

			// #pginfo 再構築
			$change_uid = FALSE;
			if ($this->root->userinfo['admin']) {
				$uid = intval($this->root->post['uid']);
				if ($is_init || $pginfo['uid'] !== $uid) {
					$userinfo = $this->func->get_userinfo_by_id($uid);
					$pginfo['uid'] = $userinfo['uid'];
					$pginfo['ucd'] = '';
					$pginfo['uname'] = $userinfo['uname_s'];
					$change_uid = TRUE;
					if ($is_init) {
						$pginfo['lastuid'] = $userinfo['uid'];
						$pginfo['lastucd'] = '';
						$pginfo['lastuname'] = $userinfo['uname_s'];
					}
				}
			}
			if ($pginfo['einherit'] !== 4)
			{
				// 元々このページのみの設定値だった？
				$only_this = ($pginfo['einherit'] === 0)? TRUE : FALSE;

				$pginfo['einherit'] = (int)@$this->root->post['einherit'];
				if ($pginfo['einherit'] === 3) {
					//設定解除
					$_pginfo = $this->func->pageinfo_inherit($page);
					$pginfo['egids'] = $_pginfo['egids'];
					$pginfo['eaids'] = $_pginfo['eaids'];
					// 下層ページも設定解除
					if (!$only_this && $cpages) {
						foreach ($cpages as $_page) {
							$child_dat[$_page]['einherit'] = 3;
							$child_dat[$_page]['egids'] = $_pginfo['egids'];
							$child_dat[$_page]['eaids'] = $_pginfo['eaids'];
						}
						$do_child = TRUE;
					}
				} else {
					$egid = @$this->root->post['egid'];
					if ($egid === 'select') {
						$egid = @join('&', @$this->root->post['egids']);
						if (!$egid) {$egid = 'none';}
					}
					$pginfo['egids'] = $egid;

					$eaid = @$this->root->post['eaid'];
					if ($eaid === 'select') {
						$eaid = @str_replace(',', '&', @$this->root->post['eaids']);

						$_aids = array();
						foreach(explode('&', $eaid) as $_aid) {
							$_aid = intval($_aid);
							if ($_aid && ! $this->func->check_admin($_aid)) {
								$_aids[] = $_aid;
							}
						}
						$eaid = join('&', $_aids);

						if (!$eaid) {$eaid = 'none';}
					}
					$pginfo['eaids'] = $eaid;
				}
				// 下層ページの継承指定
				if ($cpages) {
					if ($pginfo['einherit'] === 1 || $pginfo['einherit'] === 2) {
						foreach ($cpages as $_page) {
							$child_dat[$_page]['einherit'] = $pginfo['einherit'] + 2;
							$child_dat[$_page]['egids'] = $pginfo['egids'];
							$child_dat[$_page]['eaids'] = $pginfo['eaids'];
						}
						$do_child = TRUE;
					}
				}
			}

			if ($pginfo['vinherit'] !== 4)
			{
				// 元々このページのみの設定値だった？
				$only_this = ($pginfo['vinherit'] === 0)? TRUE : FALSE;

				$pginfo['vinherit'] = (int)@$this->root->post['vinherit'];
				if ($pginfo['vinherit'] === 3) {
					//設定解除
					$_pginfo = $this->func->pageinfo_inherit($page);
					$pginfo['vgids'] = $_pginfo['vgids'];
					$pginfo['vaids'] = $_pginfo['vaids'];
					// 下層ページも設定解除
					if (!$only_this && $cpages) {
						foreach ($cpages as $_page) {
							$child_dat[$_page]['vinherit'] = 3;
							$child_dat[$_page]['vgids'] = $_pginfo['vgids'];
							$child_dat[$_page]['vaids'] = $_pginfo['vaids'];
						}
						$do_child = TRUE;
					}
				} else {
					$vgid = @$this->root->post['vgid'];
					if ($vgid === 'select') {
						$vgid = @join('&', @$this->root->post['vgids']);
						if (!$vgid) {$vgid = 'none';}
					}
					$pginfo['vgids'] = $vgid;

					$vaid = @$this->root->post['vaid'];
					if ($vaid === 'select') {
						$vaid = @str_replace(',', '&', @$this->root->post['vaids']);

						$_aids = array();
						foreach(explode('&', $vaid) as $_aid) {
							$_aid = intval($_aid);
							if ($_aid && ! $this->func->check_admin($_aid)) {
								$_aids[] = $_aid;
							}
						}
						$vaid = join('&', $_aids);

						if (!$vaid) {$vaid = 'none';}
					}
					$pginfo['vaids'] = $vaid;
				}
				if ($cpages) {
					// 下層ページの継承指定
					if ($pginfo['vinherit'] === 1 || $pginfo['vinherit'] === 2) {
						foreach ($cpages as $_page) {
							$child_dat[$_page]['vinherit'] = $pginfo['vinherit'] + 2;
							$child_dat[$_page]['vgids'] = $pginfo['vgids'];
							$child_dat[$_page]['vaids'] = $pginfo['vaids'];
						}
						$do_child = TRUE;
					}
				}
			}
			$pginfo_str = '#pginfo('.join("\t",$pginfo).')'."\n";

			// 凍結されている? #freeze は必ずファイル先頭
			$buf = array_shift($src);
			if (rtrim($buf) !== '#freeze') {
				array_unshift($src, $buf);
				$buf = '';
			}
			// #pginfo 差し替え
			$src = preg_replace("/^#pginfo\(.*\)[\r\n]*/m", '', join('', $src));
			$src = $buf . $pginfo_str . $src;

			// get_pginfo() のキャッシュを破棄
			$this->func->get_pginfo($page, FALSE, TRUE);

			// ページ保存
			if ($this->func->is_page($page)) {
				$this->root->rtf['no_checkauth_on_write'] = TRUE;
				$this->func->file_write($this->cont['DATA_DIR'], $page, $src, TRUE);
			} else {
				// ページが未作成の場合
				$redirect = $this->root->script."?cmd=edit&amp;page=".rawurlencode($page);
				$this->func->page_write($page, "\t");
				$this->root->rtf['no_checkauth_on_write'] = TRUE;
				$src .= "\n" . $this->func->auto_template($page);
				$this->func->file_write($this->cont['DATA_DIR'], $page, $src, TRUE);
			}

			if (! $is_init) {
				// pginfo DB 更新
				$this->func->pginfo_perm_db_write($page, $pginfo, $change_uid);
			}

		} else {

			// サイト規定値を保存

			$pginfo = $this->root->pginfo;

			// 既存ページの一覧
			$cpages = $this->func->get_existpages(NULL);
			sort($cpages, SORT_STRING);

			// pginfo 再構築
			$pginfo['einherit'] = (int)@$this->root->post['einherit'];
			$pginfo['einherit'] = ($pginfo['einherit'] === 2)? 2 : 1;
			$egid = @$this->root->post['egid'];
			if ($egid === 'select') {
				$egid = @join('&', @$this->root->post['egids']);
				if (!$egid) {$egid = 'none';}
			}
			$pginfo['egids'] = $egid;

			$pginfo['vinherit'] = (int)@$this->root->post['vinherit'];
			$pginfo['vinherit'] = ($pginfo['vinherit'] === 2)? 2 : 1;
			$eaid = @$this->root->post['eaid'];
			if ($eaid === 'select') {
				$eaid = @str_replace(',', '&', @$this->root->post['eaids']);
				if (!$eaid) {$eaid = 'none';}
			}
			$pginfo['eaids'] = $eaid;

			$vgid = @$this->root->post['vgid'];
			if ($vgid === 'select') {
				$vgid = @join('&', @$this->root->post['vgids']);
				if (!$vgid) {$vgid = 'none';}
			}
			$pginfo['vgids'] = $vgid;

			$vaid = @$this->root->post['vaid'];
			if ($vaid === 'select') {
				$vaid = @str_replace(',', '&', @$this->root->post['vaids']);
				if (!$vaid) {$vaid = 'none';}
			}
			$pginfo['vaids'] = $vaid;

			// 既存ページの継承指定
			foreach ($cpages as $_page) {
				$child_dat[$_page]['einherit'] = $pginfo['einherit'] + 2;
				$child_dat[$_page]['egids'] = $pginfo['egids'];
				$child_dat[$_page]['eaids'] = $pginfo['eaids'];

				$child_dat[$_page]['vinherit'] = $pginfo['vinherit'] + 2;
				$child_dat[$_page]['vgids'] = $pginfo['vgids'];
				$child_dat[$_page]['vaids'] = $pginfo['vaids'];
			}

			// pukiwiki.ini.php のデータ形式に変換
			$dat = <<<EOD
\$root->pginfo = array(
	'uid'       => 0,
	'ucd'       => '',
	'uname'     => '',
	'einherit'  => {$pginfo['einherit']},
	'eaids'     => '{$pginfo['eaids']}',
	'egids'     => '{$pginfo['egids']}',
	'vinherit'  => {$pginfo['vinherit']},
	'vaids'     => '{$pginfo['vaids']}',
	'vgids'     => '{$pginfo['vgids']}',
	'lastuid'   => 0,
	'lastucd'   => '',
	'lastuname' => '',
);
EOD;
			// Config保存
			$this->func->save_config('pukiwiki.ini.php', 'pginfo', $dat);
			$redirect = $this->root->script.'?cmd=pginfo';
		}

		if (! $is_init) {
			// 下層ページ更新
			$this->save_parm_child ($child_dat);
		}

		$msg  = $this->msg['done_ok'];
		$body = '';
		return array('msg'=>$msg, 'body'=>$body, 'redirect'=>$redirect, 'pginfo'=>$pginfo);

	}

	function save_parm_child ($dat) {

		$nomore_e = $nomore_v = '';

		foreach ($dat as $page=>$_pginfo) {
			// ソース読み込み
			$src = $this->func->get_source($page);

			// ページ情報読み込み
			$pginfo = $this->func->get_pginfo($page);

			// 継承チェック & 上書き
			$do = FALSE;
			if (!$nomore_e || strpos($page, $nomore_e) === FALSE) {
				$nomore_e = '';
				if (isset($_pginfo['einherit']) && ($pginfo['einherit'] > 2 || $_pginfo['einherit'] === 4)) {
					if ($pginfo['einherit'] !== $_pginfo['einherit'] ||
							$pginfo['egids'] !== $_pginfo['egids'] ||
							$pginfo['eaids'] !== $_pginfo['eaids']) {
						$pginfo['einherit'] = $_pginfo['einherit'];
						$pginfo['egids'] = $_pginfo['egids'];
						$pginfo['eaids'] = $_pginfo['eaids'];
						$do = TRUE;
					}
				} else if ($pginfo['einherit'] === 1 || $pginfo['einherit'] === 2) {
					$nomore_e = $page  . '/';
				}
			}

			if (!$nomore_v || strpos($page, $nomore_v) === FALSE) {
				$nomore_v = '';
				if (isset($_pginfo['vinherit']) && ($pginfo['vinherit'] > 2 || $_pginfo['vinherit'] === 4)) {
					if ($pginfo['vinherit'] !== $_pginfo['vinherit'] ||
							$pginfo['vgids'] !== $_pginfo['vgids'] ||
							$pginfo['vaids'] !== $_pginfo['vaids']) {
						$pginfo['vinherit'] = $_pginfo['vinherit'];
						$pginfo['vgids'] = $_pginfo['vgids'];
						$pginfo['vaids'] = $_pginfo['vaids'];
						$do = TRUE;
					}
				} else if ($pginfo['vinherit'] === 1 || $pginfo['vinherit'] === 2) {
					$nomore_v = $page  . '/';
				}
			}

			// 保存
			if ($do) {
				$pginfo_str = '#pginfo('.join("\t",$pginfo).')'."\n";

				// 凍結されている? #freeze は必ずファイル先頭
				$buf = array_shift($src);
				if (rtrim($buf) !== '#freeze') {
					array_unshift($src, $buf);
					$buf = '';
				}
				// #pginfo 差し替え
				$src = preg_replace("/^#pginfo\(.*\)[\r\n]*/m", '', join('', $src));
				$src = $buf . $pginfo_str . $src;

				// get_pginfo() のキャッシュを破棄
				$this->func->get_pginfo($page, FALSE, TRUE);

				// ページ保存
				$this->func->file_write($this->cont['DATA_DIR'], $page, $src, TRUE);

				// pginfo DB 更新
				$this->func->pginfo_perm_db_write($page, $pginfo);

			}
		}
	}

	function get_form ($page = '') {

		$this->func->add_tag_head('suggest.css');
		$this->func->add_tag_head('log.js');
		$this->func->add_tag_head('suggest.js');
		$this->func->add_tag_head('pginfo.js');

		$disabled = '';
		if ($page !== '') {
			$pginfo = $this->func->get_pginfo($page);
		} else {
			$pginfo = $this->root->pginfo;
			$disabled = ' disabled="disabled" style="display:none;"';
			$this->msg['inherit_onlythis'] = $this->msg['permission_none'] = '';
			if ($pginfo['einherit'] === 3) $pginfo['einherit'] = 1;
			if ($pginfo['vinherit'] === 3) $pginfo['vinherit'] = 1;
		}
		$spage = htmlspecialchars($page);

		$s_['einhelit'] = array_pad(array(), 4, '');
		$s_['einhelit'][$pginfo['einherit']] = ' checked="checked"';
		$s_['vinhelit'] = array_pad(array(), 4, '');
		$s_['vinhelit'][$pginfo['vinherit']] = ' checked="checked"';

		$efor_remove = $vfor_remove = $this->msg['for_remove'];
		$s_['edisable'] = $s_['edisable2'] = $s_['vdisable'] = $s_['vdisable2'] = $s_['ecannot'] = $s_['vcannot'] = '';
		if ($pginfo['einherit'] === 4) {
			$s_['edisable'] = ' disabled="disabled "';
			$s_['edisable2'] = ' disabled="disabled "';
			$s_['ecannot'] = $this->msg['can_not_set'].'<br />';
			$efor_remove = '';
		} else if ($pginfo['einherit'] === 3) {
			$s_['edisable2'] = ' disabled="disabled "';
		}
		if ($pginfo['vinherit'] === 4) {
			$s_['vdisable'] = ' disabled="disabled "';
			$s_['vdisable2'] = ' disabled="disabled "';
			$s_['vcannot'] = $this->msg['can_not_set'].'<br />';
			$vfor_remove = '';
		} else if ($pginfo['vinherit'] === 3) {
			$s_['vdisable2'] = ' disabled="disabled "';
		}

		foreach(array('eaids','egids','vaids','vgids') as $key) {
			$s_[$key]['all'] = $s_[$key]['none'] = $s_[$key]['select'] = '';
			if ($pginfo[$key] === 'none' || $pginfo[$key] === 'all') {
				$$key = $pginfo[$key];
				$s_[$key][$pginfo[$key]] = ' checked="checked"';
			} else {
				$$key = explode("&", $pginfo[$key]);
				$s_[$key]["select"] = ' checked="checked"';
			}
		}
		$edit_group_list = $this->func->make_grouplist_form('egids', $egids, $s_['edisable'], ' onchange="xpwiki_pginfo_setradio(\'eg3\')"');
		$edit_user_list = '';
		if ($eaids && is_array($eaids)) {
			foreach($eaids as $eaid) {
				$eaid = intval($eaid);
				if ($eaid && ! $this->func->check_admin($eaid)) {
					if ($pginfo['einherit'] === 4) {
						$edit_user_list .= htmlspecialchars($this->func->getUnameFromId($eaid)).'['.$eaid.'] ';
					} else {
						$edit_user_list .= '<span class="exist">'.htmlspecialchars($this->func->getUnameFromId($eaid)).'['.$eaid.'] </span>';
					}
				}
			}
			if ($edit_user_list === '') {
				$s_['eaids']['select'] = '';
				$s_['eaids']['none'] = ' checked="checked"';
			}
		}


		$view_group_list = $this->func->make_grouplist_form('vgids', $vgids, $s_['vdisable'], ' onchange="xpwiki_pginfo_setradio(\'vg3\')"');
		$view_user_list = '';
		if ($vaids && is_array($vaids)) {
			foreach($vaids as $vaid) {
				$vaid = intval($vaid);
				if ($vaid && ! $this->func->check_admin($vaid)) {
					if ($pginfo['vinherit'] === 4) {
						$view_user_list .= htmlspecialchars($this->func->getUnameFromId($vaid)).'['.$vaid.'] ';
					} else {
						$view_user_list .= '<span class="exist">'.htmlspecialchars($this->func->getUnameFromId($vaid)).'['.$vaid.'] </span>';
					}
				}
			}
			if ($view_user_list === '') {
				$s_['vaids']['select'] = '';
				$s_['vaids']['none'] = ' checked="checked"';
			}
		}

		$e_default = ($pginfo['einherit'] === 3)? '<p>'.$this->msg['default_inherit'].'</p>' : '';
		$v_default = ($pginfo['vinherit'] === 3)? '<p>'.$this->msg['default_inherit'].'</p>' : '';

		$enc = $this->cont['CONTENT_CHARSET'];

		$title_permission_default = ($page && $this->root->userinfo['admin'])? '<hr /><p><a href="'.$this->root->script.'?cmd=pginfo">'.$this->msg['title_permission_default'].'</a></p>' : '';

		$uid_form = ($page && $this->root->userinfo['admin'])? '&nbsp;&nbsp;' . $this->root->_LANG['skin']['pageowner'] . ' User ID: <input type="text" name="uid" size="5" value="' . htmlspecialchars($pginfo['uid']) . '" />' : '';
		$script = $this->func->get_script_uri();
		$form = <<<EOD
<script language="javascript">
<!--
var XpWikiSuggest1 = null;
var XpWikiSuggest2 = null;
document.observe("dom:loaded", function(){
	XpWikiSuggest1 = new XpWikiUnameSuggest('{$this->cont['HOME_URL']}','xpwiki_tag_input1','xpwiki_suggest_list1','xpwiki_tag_hidden1','xpwiki_tag_list1','{$enc}');
	XpWikiSuggest2 = new XpWikiUnameSuggest('{$this->cont['HOME_URL']}','xpwiki_tag_input2','xpwiki_suggest_list2','xpwiki_tag_hidden2','xpwiki_tag_list2','{$enc}');
});
//-->
</script>
<form action="{$script}" method="post">
<p>
 <ul>
  <li><a href="#xpwiki_edit_parmission">{$this->msg['edit_permission']}</a></li>
  <li><a href="#xpwiki_view_parmission">{$this->msg['view_parmission']}</a></li>
 </ul>
</p>
<h2 id="xpwiki_edit_parmission">{$this->msg['edit_permission']}</h2>
<p>
 {$s_['ecannot']}
 <input name="einherit" id="_edit_permission_none" type="radio" value="3" onclick="xpwiki_parm_desc('xpwiki_edit_parm_desc',0);"{$s_['einhelit'][3]}{$s_['edisable']}{$disabled} /><label for="_edit_permission_none"> {$this->msg['permission_none']}</label><br />
</p>
{$e_default}
<h4>{$this->msg['lower_page_inherit']}</h4>
<p>
 <input name="einherit" id="_edit_inherit_default" type="radio" value="1" onclick="xpwiki_parm_desc('xpwiki_edit_parm_desc',1);"{$s_['einhelit'][1]}{$s_['edisable']} /><label for="_edit_inherit_default"> {$this->msg['inherit_default']}</label><br />
 <input name="einherit" id="_edit_inherit_forced" type="radio" value="2" onclick="xpwiki_parm_desc('xpwiki_edit_parm_desc',1);"{$s_['einhelit'][2]}{$s_['edisable']} /><label for="_edit_inherit_forced"> {$this->msg['inherit_forced']}</label><br />
 <input name="einherit" id="_edit_inherit_onlythis" type="radio" value="0" onclick="xpwiki_parm_desc('xpwiki_edit_parm_desc',1);"{$s_['einhelit'][0]}{$s_['edisable']}{$disabled} /><label for="_edit_inherit_onlythis"> {$this->msg['inherit_onlythis']}</label><br />
</p>
<h4>{$this->msg['parmission_setting']}</h4>
<table style="margin-left:2em;" id="xpwiki_edit_parm_desc"><tr>
 <td>
  <input name="egid" id="_egid1" type="radio" value="all"{$s_['egids']['all']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eu1');" /><label for="_egid1" onclick="xpwiki_pginfo_setradio('eu1');"> {$this->msg['admit_all_group']}</label><br />
  <input name="egid" id="_egid2" type="radio" value="none"{$s_['egids']['none']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eu2');" /><label for="_egid2" onclick="xpwiki_pginfo_setradio('eu2');"> {$this->msg['not_admit_all_group']}</label><br />
  <input name="egid" id="_egid3" type="radio" value="select"{$s_['egids']['select']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eu2');" /><label for="_egid3" onclick="xpwiki_pginfo_setradio('eu2');"> {$this->msg['admit_select_group']}</label><br />
  <div style="margin-left:2em;">{$edit_group_list}</div>
 </td>
 <td>
  <input name="eaid" id="_eaid1" type="radio" value="all"{$s_['eaids']['all']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eg1');" /><label for="_eaid1" onclick="xpwiki_pginfo_setradio('eg1');"> {$this->msg['admit_all_user']}</label><br />
  <input name="eaid" id="_eaid2" type="radio" value="none"{$s_['eaids']['none']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eg2');" /><label for="_eaid2" onclick="xpwiki_pginfo_setradio('eg2');"> {$this->msg['not_admit_all_user']}</label><br />
  <input name="eaid" id="_eaid3" type="radio" value="select"{$s_['eaids']['select']}{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eg2');" /><label for="_eaid3" onclick="xpwiki_pginfo_setradio('eg2');"> {$this->msg['admit_select_user']}</label><br />
  <div style="margin-left:2em;">
    <div id="xpwiki_tag_list1" class="xpwiki_tag_list">{$edit_user_list}</div>
    <input type="hidden" name="eaids" id="xpwiki_tag_hidden1" value="" />
    {$this->msg['search_user']}: <input type="text" size="25" id="xpwiki_tag_input1" name="xpwiki_tag_input1" autocomplete='off' class="form_text"{$s_['edisable2']} onclick="xpwiki_pginfo_setradio('eu3');" /><br />
    {$efor_remove}
    <div id='xpwiki_suggest_list1' class="auto_complete"></div>
  </div>
 </td>
</tr></table>

<hr />

<h2 id="xpwiki_view_parmission">{$this->msg['view_parmission']}</h2>
<p>
 {$s_['vcannot']}
 <input name="vinherit" id="_view_permission_none" type="radio" value="3" onclick="xpwiki_parm_desc('xpwiki_view_parm_desc',0);"{$s_['vinhelit'][3]}{$s_['vdisable']}{$disabled} /><label for="_view_permission_none"> {$this->msg['permission_none']}</label><br />
</p>
{$v_default}
<h4>{$this->msg['lower_page_inherit']}</h4>
<p>
 <input name="vinherit" id="_view_inherit_default" type="radio" value="1" onclick="xpwiki_parm_desc('xpwiki_view_parm_desc',1);"{$s_['vinhelit'][1]}{$s_['vdisable']} /><label for="_view_inherit_default"> {$this->msg['inherit_default']}</label><br />
 <input name="vinherit" id="_view_inherit_forced" type="radio" value="2" onclick="xpwiki_parm_desc('xpwiki_view_parm_desc',1);"{$s_['vinhelit'][2]}{$s_['vdisable']} /><label for="_view_inherit_forced"> {$this->msg['inherit_forced']}</label><br />
 <input name="vinherit" id="_view_inherit_onlythis" type="radio" value="0" onclick="xpwiki_parm_desc('xpwiki_view_parm_desc',1);"{$s_['vinhelit'][0]}{$s_['vdisable']}{$disabled} /><label for="_view_inherit_onlythis"> {$this->msg['inherit_onlythis']}</label><br />
</p>
<h4>{$this->msg['parmission_setting']}</h4>
<table style="margin-left:2em;" id="xpwiki_view_parm_desc"><tr>
 <td>
  <input name="vgid" id="_vgid1" type="radio" value="all"{$s_['vgids']['all']}{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vu1');" /><label for="_vgid1" onclick="xpwiki_pginfo_setradio('vu1');"> {$this->msg['admit_all_group']}</label><br />
  <input name="vgid" id="_vgid2" type="radio" value="none"{$s_['vgids']['none']}{$s_['vdisable2']}} onclick="xpwiki_pginfo_setradio('vu2');" /><label for="_vgid2" onclick="xpwiki_pginfo_setradio('vu2');"> {$this->msg['not_admit_all_group']}</label><br />
  <input name="vgid" id="_vgid3" type="radio" value="select"{$s_['vgids']['select']}{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vu2');" /><label for="_vgid3" onclick="xpwiki_pginfo_setradio('vu2');"> {$this->msg['admit_select_group']}</label><br />
  <div style="margin-left:2em;">{$view_group_list}</div>
 </td>
 <td>
  <input name="vaid" id="_vaid1" type="radio" value="all"{$s_['vaids']['all']}{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vg1');" /><label for="_vaid1" onclick="xpwiki_pginfo_setradio('vg1');"> {$this->msg['admit_all_user']}</label><br />
  <input name="vaid" id="_vaid2" type="radio" value="none"{$s_['vaids']['none']}{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vg2');" /><label for="_vaid2" onclick="xpwiki_pginfo_setradio('vg2');"> {$this->msg['not_admit_all_user']}</label><br />
  <input name="vaid" id="_vaid3" type="radio" value="select"{$s_['vaids']['select']}{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vg2');" /><label for="_vaid3" onclick="xpwiki_pginfo_setradio('vg2');"> {$this->msg['admit_select_user']}</label><br />
  <div style="margin-left:2em;">
    <div id="xpwiki_tag_list2" class="xpwiki_tag_list">{$view_user_list}</div>
    <input type="hidden" name="vaids" id="xpwiki_tag_hidden2" value="" />
    {$this->msg['search_user']}: <input type="text" size="25" id="xpwiki_tag_input2" name="xpwiki_tag_input2" autocomplete='off' class="form_text"{$s_['vdisable2']} onclick="xpwiki_pginfo_setradio('vu3');" /><br />
    {$vfor_remove}
    <div id='xpwiki_suggest_list2' class="auto_complete"></div>
  </div>
 </td>
</tr></table>

<hr />
<input type="hidden" name="cmd" value="pginfo" />
<input type="hidden" name="page" value="{$spage}" />
<input type="hidden" name="pmode" value="setparm" />
<input id="xpwiki_parmission_submit" type="submit" value="{$this->msg['submit']}" />
{$uid_form}
</form>
{$title_permission_default}
EOD;
		return $form;
	}

	// ページ毎の権限設定フォーム
	function show_page_form ($page) {
		$src = $this->func->get_source($page, TRUE, TRUE);
		// 管理領域設定 (#xoopsadmin)
		if (preg_match('/^#xoopsadmin\b.*$/sm', $src)) {
			$this->func->redirect_header($this->func->get_page_uri($page, true), 1, 'Found "#xoopsadmin"');
		} else {
			$ret['msg'] = $this->msg['title_permission'];
			$ret['body'] = $this->get_form($page);
		}
		return $ret;
	}

	// サイト規定値の設定フォーム
	function show_admin_form () {
		$ret['msg'] = $this->msg['title_permission_default'];
		$ret['body'] = $this->get_form();
		return $ret;
	}

}
?>