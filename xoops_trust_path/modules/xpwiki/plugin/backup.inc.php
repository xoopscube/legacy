<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: backup.inc.php,v 1.20 2010/01/08 13:59:06 nao-pon Exp $
// Copyright (C)
//   2002-2005 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// Backup plugin

class xpwiki_plugin_backup extends xpwiki_plugin {
	function plugin_backup_init () {
		// Prohibit rendering old wiki texts (suppresses load, transfer rate, and security risk)
		$this->cont['PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING'] =  $this->cont['PKWK_SAFE_MODE'] || $this->cont['PKWK_OPTIMISE'];

		$this->icons['edit']['url']      = $this->cont['LOADER_URL'] . '?src=page_white_edit.png';
		$this->icons['edit']['width']    = '16';
		$this->icons['edit']['height']   = '16';

		$this->icons['source']['url']    = $this->cont['LOADER_URL'] . '?src=page_white_text.png';
		$this->icons['source']['width']  = '16';
		$this->icons['source']['height'] = '16';

		$this->icons['rewind']['url']    = $this->cont['LOADER_URL'] . '?src=arrow_undo.png';
		$this->icons['rewind']['width']  = '16';
		$this->icons['rewind']['height'] = '16';
	}

	function plugin_backup_action() {

		if (! $this->root->do_backup) return;

		$page = isset($this->root->vars['page']) ? $this->root->vars['page']  : '';
		if ($page === '') return array('msg'=>$this->root->_title_backuplist, 'body'=>$this->plugin_backup_get_list_all());

		$this->func->check_readable($page, true, true);
		$s_page = htmlspecialchars($page);
		$pgid = $this->func->get_pgid_by_name($page);
		$isowner = $this->func->is_owner($page);
		$s_age = (isset($this->root->vars['age'])) ? $this->root->vars['age'] : 0;

		$action = isset($this->root->vars['action']) ? $this->root->vars['action'] : '';
		if ($action === 'delete') {
			if ($isowner) {
				return $this->plugin_backup_delete($page);
			} else {
				return $this->action_msg_owner_only();
			}
		} else if ($action === 'dorewind') {
			if ($isowner) {
				return $this->do_rewind($page, intval($s_age));
			} else {
				return $this->action_msg_owner_only();
			}
		}

		$s_action = $r_action = '';
		if ($action != '') {
			$s_action = htmlspecialchars($action);
			$r_action = rawurlencode($action);
		}

		$script = $this->func->get_script_uri();

		$view_now = ($action === 'diff' || $action === 'source');

		$edit_icon = '<a href="' . $this->cont['HOME_URL'] . '?cmd=edit&amp;pgid=' . $pgid . '&amp;backup=$1" title="' . htmlspecialchars($this->root->_msg_backupedit) . '"><img src="' . $this->icons['edit']['url'] . '" alt="' . htmlspecialchars($this->root->_msg_backupedit) . '" width="' . $this->icons['edit']['width'] . '" height="' . $this->icons['edit']['height'] . '" /></a>';
		$source_icon = '<a href="' . $this->cont['HOME_URL'] . '?cmd=backup&amp;pgid=' . $pgid . '&amp;action=source&amp;age=$1" title="' . htmlspecialchars($this->root->_msg_source) . '"><img src="' . $this->icons['source']['url'] . '" alt="' . htmlspecialchars($this->root->_msg_source) . '" width="' . $this->icons['source']['width'] . '" height="' . $this->icons['source']['height'] . '" /></a>';
		$rewind_icon = '<a href="' . $this->cont['HOME_URL'] . '?cmd=backup&amp;pgid=' . $pgid . '&amp;action=rewind&amp;age=$1" title="' . htmlspecialchars($this->root->_msg_rewind) . '"><img src="' . $this->icons['rewind']['url'] . '" alt="' . htmlspecialchars($this->root->_msg_rewind) . '" width="' . $this->icons['rewind']['width'] . '" height="' . $this->icons['rewind']['height'] . '" /></a>';

		if ($view_now && ($s_age === 'Cur' || !$s_age)) {
			$s_age = 'Cur';
			$is_now = TRUE;
			$data_age = ($action === 'diff')? 'last' : 'none';
		} else {
			$s_age = intval($s_age);
			if (!$s_age) return array( 'msg'=>$this->root->_title_pagebackuplist, 'body'=>$this->plugin_backup_get_list($page));
			$is_now = FALSE;
			$data_age = $s_age;
			if ($action === 'diff') $data_age .= ',' . ($s_age - 1);
		}

		$backups = $this->func->get_backup($page, 0, $data_age);
		$backups_count = count($backups);

		if (!$is_now && ($s_age > $backups_count || !$s_age)) {
			return array( 'msg'=>$this->root->_title_pagebackuplist, 'body'=>$this->plugin_backup_get_list($page));
		}

		if ($action === 'rewind') {
			$date = $this->func->format_date($backups[$s_age]['time']);
			$dorewind_title = str_replace('$1', $date, $this->root->_title_dorewind);
			$body = <<<EOD
{$dorewind_title}
<form method="POST" action="{$script}">
<input type="hidden" name="plugin" value="backup" />
<input type="hidden" name="action" value="dorewind" />
<input type="hidden" name="age" value="{$s_age}" />
<input type="hidden" name="page" value="{$s_page}" />
<input type="submit" value="{$this->root->_msg_rewind}" />
</form>
EOD;
		} else {
			$body  = '<ul>' . "\n";
			//if (!$is_now) $body .= ' <li><a href="' . $script . '?cmd=backup">' . $this->root->_msg_backuplist . '</a></li>' ."\n";

			$href    = $script . '?cmd=backup&amp;pgid=' . $pgid . '&amp;age=' . $s_age;
			$is_page = $this->func->is_page($page);
			$editable = $this->func->check_editable($page, FALSE, FALSE);

			if ($s_age && $is_page && $action != 'diff')
				$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
				'&amp;action=diff">' . $this->root->_msg_diff . '</a>',
				$this->root->_msg_view) . '</li>' . "\n";

			if (is_numeric($s_age) && $is_page && $action != 'nowdiff')
				$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
				'&amp;action=nowdiff">' . $this->root->_msg_nowdiff . '</a>',
				$this->root->_msg_view) . '</li>' . "\n";

			if ($s_age && $action != 'source')
				$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
				'&amp;action=source">' . $this->root->_msg_source . '</a>',
				$this->root->_msg_view) . '</li>' . "\n";

			if (is_numeric($s_age) && (! $this->cont['PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING'] || $isowner) && $action)
				$body .= ' <li>' . str_replace('$1', '<a href="' . $href .
				'">' . $this->root->_msg_backup . ' No.' . $s_age . '</a>',
				$this->root->_msg_view) . '</li>' . "\n";

			if (is_numeric($s_age) && ($action === 'source' || !$action) && $isowner)
				$body .= ' <li><a href="' . $href .
				'&amp;action=rewind">' . str_replace('$1', $s_age, $this->root->_msg_dorewind) . '</a></li>' . "\n";

			if (is_numeric($s_age) && ($action === 'source' || !$action) && $editable)
				$body .= ' <li><a href="' . $script . '?cmd=edit&amp;pgid=' . $pgid . '&amp;backup=' . $s_age .
				'">' . str_replace('$1', $s_age, $this->root->_msg_backupedit) . '</a></li>' . "\n";

			if ($is_page) {
				$body .= ' <li>' . str_replace('$1',
				'<a href="' . $this->func->get_page_uri($page, true) . '">' . $s_page . '</a>',
				$this->root->_msg_goto) . "</li>\n";
			} else {
				$body .= ' <li>' . str_replace('$1', $s_page, $this->root->_msg_deleted) . "</li>\n";
			}
			$body .= '</ul>' . "\n";
		}

		$header[0] = '';
		$list2 = $list = '';
		$navi = '';

		$showlist = ($action !== 'rewind' && ($backups_count || $is_now));
		if ($showlist) {
			// list
			$_name = '_title_backup' . $action;
			$title = $this->root->$_name;
			$list .= '<li>'.htmlspecialchars(str_replace(array('$1', '$2'), array($page, ' All'), $title)) . "\n";
			foreach($backups as $age => $val) {
				$s_title = htmlspecialchars(str_replace(array('$1', '$2'), array($page, $age), $title));
				$date = $this->func->format_date($val['time']);
				$pginfo = $this->func->get_pginfo('',$val['data']);
				$lasteditor = $this->func->get_lasteditor($pginfo);
				$esummary = $this->make_esummary($pginfo['esummary']);
				$list2 .= ($age == $s_age) ?
					'   <li><em>' . $age . ': ' . $date . ' ' . $lasteditor . '</em>' . $esummary . '</li>' . "\n" :
					'   <li><a href="' . $script . '?cmd=backup&amp;action=' .
					$r_action . '&amp;pgid=' . $pgid . '&amp;age=' . $age .
					'" title="' . $s_title . '">' . $age . ': ' . $date . '</a> ' . $lasteditor . $esummary . '</li>' . "\n";
				if ($age == $s_age) {
					$header[1] = $this->make_age_label($age, $date, $lasteditor);
					$header[1] .= ' ' . str_replace('$1', $age, $source_icon);
					if ($isowner) $header[1] .= ' ' . str_replace('$1', $age, $rewind_icon);
					if ($editable) $header[1] .= ' ' . str_replace('$1', $age, $edit_icon);
					$header[1] .= $this->make_esummary($pginfo['esummary'], 'div');
				}
			}
			if ($view_now) {
				if ($action === 'diff') {
					$title = $this->root->_title_diff;
				} else if ($action === 'source') {
					$title = $this->root->_source_messages['msg_title'];
				} else {
					$title = '';
				}
				$s_title = htmlspecialchars(str_replace('$1', $page, $title));
				$date = $this->func->format_date($this->func->get_filetime($page));
				$pginfo = $this->func->get_pginfo($page);
				$esummary = $this->make_esummary($pginfo['esummary']);
				$lasteditor = $this->func->get_lasteditor($pginfo);
				$list2 .= ($is_now) ?
					'   <li><em>' . $this->root->_msg_current . ': ' . $date . ' ' . $lasteditor . '</em>' . $esummary . '</li>' . "\n" :
					'   <li><a href="' . $script . '?cmd=backup&amp;action=' .
					$r_action . '&amp;pgid=' . $pgid . '&amp;age=Cur'.
					'" title="' . $s_title . '">' . $this->root->_msg_current . ': ' . $date . '</a> ' . $lasteditor . $esummary . '</li>' . "\n";
				if ($is_now) {
					$header[1] = $this->make_age_label($this->root->_msg_current, $date, $lasteditor);
					$header[1] .= ' ' . str_replace('$1', 'Cur', $source_icon);
					if ($editable) $header[1] .= ' ' . str_replace('$1', '0', str_replace($this->root->_msg_backupedit, $this->root->_btn_edit, $edit_icon));
					$header[1] .= $this->make_esummary($pginfo['esummary'], 'div');
				}
			}
			if ($list2) {
				$list .= '<ul>' . $list2 . '</ul></li>';
			} else {
				$list .= '</li>';
			}

			// navi
			$navi_link = array('', '');
			$nav_href = $script . '?cmd=backup&amp;pgid=' . $pgid . '&amp;action=' . $action . '&amp;age=';
			if ($s_age > 1 || ($is_now && $backups_count)) {
				$age = $is_now? $backups_count : $s_age - 1;
				$date = $this->func->format_date($backups[$age]['time']);
				$pginfo = $this->func->get_pginfo('',$backups[$age]['data']);
				$lasteditor = $this->func->get_lasteditor($pginfo);
				$title = htmlspecialchars(strip_tags($this->make_age_label($age, $date, $lasteditor)));

				$navi_link[0] = '<a href="'.$nav_href . $age .'" title="' . $title . '">&#171; ' . $this->root->_navi_prev . '</a>';
			}
			if (!$is_now && ($s_age < $backups_count || $view_now)) {
				if ($s_age < $backups_count) {
					$age = $s_age + 1;
					$date = $this->func->format_date($backups[$age]['time']);
					$pginfo = $this->func->get_pginfo('',$backups[$age]['data']);
				} else {
					$age = 'Cur';
					$date = $this->func->format_date($this->func->get_filetime($page));
					$pginfo = $this->func->get_pginfo($page);
				}
				$lasteditor = $this->func->get_lasteditor($pginfo);
				$title = htmlspecialchars(strip_tags($this->make_age_label($age, $date, $lasteditor)));
				$navi_link[1] = '<a href="'.$nav_href . $age .'" title="' . $title . '">' . $this->root->_navi_next . ' &#187;</a>';
			}
			$navi = '<div>' . $navi_link[0] . '&nbsp;&nbsp;' . $navi_link[1] .'</div>';
		}

		$body .= $navi;

		if ($action === 'diff') {
			if ($s_age > 1 || ($is_now && $backups_count)) {
				$val = $is_now ? $backups[$backups_count] : $backups[$s_age - 1];
				$old = $val['data'];
				$date = $this->func->format_date($val['time']);
				$pginfo = $this->func->get_pginfo('',$val['data']);
				$lasteditor = $this->func->get_lasteditor($pginfo);
				$age = $is_now? $backups_count : ($s_age - 1);
				$header[0] = $this->make_age_label($age, $date, $lasteditor);
				$header[0] .= ' ' . str_replace('$1', $age, $source_icon);
				if ($isowner) $header[0] .= ' ' . str_replace('$1', $age, $rewind_icon);
				if ($editable) $header[0] .= ' ' . str_replace('$1', $age, $edit_icon);
				$header[0] .= $this->make_esummary($pginfo['esummary'], 'div');
			} else {
				$header[0] = '';
				$old = array();
			}
			if ($is_now) {
				$title = $this->root->_title_diff;
				$cur = $this->func->get_source($page);
			} else {
				$title = $this->root->_title_backupdiff;
				$cur = $backups[$s_age]['data'];
			}
			$old = $this->func->remove_pginfo($old);
			$cur = $this->func->remove_pginfo($cur);
			$body .= $this->func->compare_diff($old, $cur, $header);
		} else if ($action === 'nowdiff') {
			$title = $this->root->_title_backupnowdiff;
			$old = $backups[$s_age]['data'];
			$cur = $this->func->get_source($page);
			$pginfo = $this->func->get_pginfo($page);
			$header[0] = $header[1];
			$header[1] = $this->make_age_label($this->root->_msg_current, $this->func->format_date($this->func->get_filetime($page)), $this->func->get_lasteditor($pginfo));
			$header[1] .= ' ' . str_replace('$1', 'Cur', $source_icon);
			if ($editable) $header[1] .= ' ' . str_replace('$1', '0', str_replace($this->root->_msg_backupedit, $this->root->_btn_edit, $edit_icon));
			$header[1] .= $this->make_esummary($pginfo['esummary'], 'div');
			$old = $this->func->remove_pginfo($old);
			$cur = $this->func->remove_pginfo($cur);
			$body .= $this->func->compare_diff($old, $cur, $header);
		} else if ($action === 'source') {
			if ($is_now) {
				$title = $this->root->_source_messages['msg_title'];
				$data = $this->func->get_source($page, TRUE, TRUE);
			} else {
				$title = $this->root->_title_backupsource;
				$data = join('', $backups[$s_age]['data']);
			}
			$sorce = htmlspecialchars($this->func->remove_pginfo($data));
			if ($this->root->viewmode === 'print' || $this->cont['UA_PROFILE'] === 'keitai') {
				$body .=<<<EOD
<pre class="code">
{$sorce}
</pre>
EOD;
			} else {
				$body .=<<<EOD
<div class="edit_form">
 <form>
  <textarea id="xpwiki_backup_textarea" readonly="readonly" rows="{$this->root->rows}" cols="{$this->root->cols}">{$sorce}</textarea>
 </form>
</div>
EOD;
			}
		} else {
			if (! $isowner && $this->cont['PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING']) {
				$this->func->die_message('This feature is prohibited');
			} else {
				if ($action === 'rewind') {
					$title = $this->root->_title_backuprewind;
				} else {
					$title = $this->root->_title_backup;
				}
				$body .= $this->root->hr . "\n";

				$this->root->rtf['preview'] = TRUE;
				$src = join('', $backups[$s_age]['data']);
				$src = $this->func->make_str_rules($src);
				$src = explode("\n", $src);

				$body .= $this->func->drop_submit($this->func->convert_html($src));
				$this->func->convert_finisher($body);
			}
		}

		$body .= $navi;

		if ($list) {
			$href = $script . '?cmd=backup&amp;pgid=' . $pgid;
			$body .= '<hr style="clear:both;" />'. "\n";
			if ($backups_count) {
				$body .= '<ul><li><a href="'.$href.'">'. str_replace('$1', $s_page, $this->root->_title_pagebackuplist) . "</a></li>\n" . $list . '</ul>';
			} else {
				$body .= '<ul>' . $list . '</ul>';
			}
		}

		return array('msg'=>str_replace('$2', $s_age, $title), 'body'=>$body);
	}

	// Delete backup
	function plugin_backup_delete($page) {

		if (! $this->func->_backup_file_exists($page))
			return array('msg'=>$this->root->_title_pagebackuplist, 'body'=>$this->plugin_backup_get_list($page)); // Say "is not found"

		$body = '';
		if (isset($this->root->post['action'])) {
			$this->func->_backup_delete($page);
			return array(
				'msg'  => str_replace('$1', $page, $this->root->_msg_backup_deleted),
				'body' => '',
				'redirect' => $this->root->script . '?cmd=backup'
			);
		}

		$script = $this->func->get_script_uri();
		$s_page = htmlspecialchars($page);
		$s_title = str_replace('$1', $s_page, $this->root->_title_backup_delete);
		$body .= <<<EOD
<p>$s_title</p>
<form action="$script" method="post">
 <div>
  <input type="hidden"   name="cmd"    value="backup" />
  <input type="hidden"   name="page"   value="$s_page" />
  <input type="hidden"   name="action" value="delete" />
  <input type="submit"   name="ok"     value="{$this->root->_btn_delete}" />
 </div>
</form>
EOD;
		return	array('msg'=>$this->root->_title_backup_delete, 'body'=>$body);
	}

	function plugin_backup_diff($str) {
		$ul = <<<EOD
{$this->root->hr}
<ul>
 <li>{$this->root->_msg_addline}</li>
 <li>{$this->root->_msg_delline}</li>
</ul>
EOD;

		return $ul . '<pre>' . $this->func->diff_style_to_css(htmlspecialchars($str)) . '</pre>' . "\n";
	}

	function plugin_backup_get_list($page) {
		$script = $this->func->get_script_uri();
		$s_page = htmlspecialchars($page);
		$pgid = $this->func->get_pgid_by_name($page);
		$retval = array();
		$page_link = $this->func->make_pagelink($page);
		$retval[0] = <<<EOD
<ul>
 <li><a href="$script?cmd=backup">{$this->root->_msg_backuplist}</a></li>
 <li>$page_link
  <ul>
EOD;
		$retval[1] = "\n";
		$retval[2] = <<<EOD
  </ul>
 </li>
</ul>
EOD;

		$backups = $this->func->_backup_file_exists($page) ? $this->func->get_backup($page, 0, 'none') : array();
		if (empty($backups)) {
			$msg = str_replace('$1', $this->func->make_pagelink($page), $this->root->_msg_nobackup);
			$retval[1] .= '   <li>' . $msg . '</li>' . "\n";
			return join('', $retval);
		}

		if ($this->func->is_owner($page)) {
 			$retval[1] .= '   <li><a href="' . $script . '?cmd=backup&amp;action=delete&amp;pgid=' . $pgid . '">';
			$retval[1] .= str_replace('$1', $s_page, $this->root->_title_backup_delete);
			$retval[1] .= '</a></li>' . "\n";
		}

		$href = $script . '?cmd=backup&amp;pgid=' . $pgid . '&amp;age=';
		$_anchor_from = $_anchor_to   = '';
		foreach ($backups as $age=>$data) {
			if (! $this->cont['PLUGIN_BACKUP_DISABLE_BACKUP_RENDERING']) {
				$_anchor_from = '<a href="' . $href . $age . '">';
				$_anchor_to   = '</a>';
			}
			$date = $this->func->format_date($data['time'], TRUE);
			$pginfo = $this->func->get_pginfo('',$data['data']);
			$lasteditor = $this->func->get_lasteditor($pginfo);
			$esummary = $this->make_esummary($pginfo['esummary'], 'list');
			$retval[1] .= <<<EOD
   <li>$_anchor_from$age $date$_anchor_to
     [ <a href="$href$age&amp;action=diff">{$this->root->_msg_diff}</a>
     | <a href="$href$age&amp;action=nowdiff">{$this->root->_msg_nowdiff}</a>
     | <a href="$href$age&amp;action=source">{$this->root->_msg_source}</a>
     ]
     $lasteditor
     $esummary
   </li>
EOD;
		}
		$date = $this->func->format_date($this->func->get_filetime($page), TRUE);
		$page_link = $this->func->make_pagelink($page, $this->root->_msg_current . ' ' . $date);
		$pginfo = $this->func->get_pginfo($page);
		$lasteditor = $this->func->get_lasteditor($pginfo);
		$esummary = $this->make_esummary($pginfo['esummary'], 'list');
		$retval[1] .= <<<EOD
   <li>$page_link
     [ <a href="{$href}Cur&amp;action=diff">{$this->root->_msg_diff}</a>
     | <a href="{$href}Cur&amp;action=source">{$this->root->_msg_source}</a>
     ]
     $lasteditor
     $esummary
   </li>
EOD;
		return join('', $retval);
	}

	// List for all pages
	function plugin_backup_get_list_all($withfilename = FALSE) {
		// 閲覧権限のないページを省く
		$pages = array_intersect($this->func->get_existpages($this->cont['BACKUP_DIR'], $this->cont['BACKUP_EXT']), $this->func->get_existpages(FALSE, "", array('nodelete' => FALSE)));

		$pages = array_diff($pages, $this->root->cantedit);

		if (empty($pages)) {
			return '';
		} else {
			return $this->func->page_list($pages, 'backup', $withfilename);
		}
	}

	function make_age_label($age, $date, $lasteditor) {
		return $age . ': ' . $date . ' <small>' . $lasteditor . '</small>';
	}

	function do_rewind($page, $age) {
		$this->root->vars['refer'] = $page;
		if ($backup = $this->func->get_backup($page, $age, $age)) {
			$count = count($this->func->get_backup($page));
			$time = $backup['time'] + $this->cont['ZONETIME'];
			$data = join('', $backup['data']);
			$this->root->rtf['esummary'] = 'Rewound to ' . ($count - $age + 2) . ' ages ago.';
			$this->func->page_write($page, $data, TRUE);
			$this->func->touch_page($page, $time);
			//$this->root->rtf['page_touch'][$page][] = 'Rewound to ' . ($count - $age + 2) . ' ages ago.';

			$s_page = htmlspecialchars($page);
			return array(
				'msg'  => str_replace('$1', $age, $this->root->_msg_rewinded),
				'body' => ''
			);
		} else {
			return array(
				'msg'  => str_replace('$1', $age, $this->root->_msg_nobackupnum),
				'body' => ''
			);
		}
	}

	function make_esummary($esummary, $mode='ul') {
		if (! $esummary) return '';
		switch($mode) {
			case 'div': $ret = '<div class="edit_summary">' . $esummary . '</div>';
				break;
			default: $ret = '<ul><li class="edit_summary">' . $esummary . '</li></ul>';
		}
		return $ret;
	}
}
?>