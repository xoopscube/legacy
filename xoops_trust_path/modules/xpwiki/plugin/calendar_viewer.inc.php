<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: calendar_viewer.inc.php,v 1.16 2010/06/04 07:08:50 nao-pon Exp $
//
// Calendar viewer plugin - List pages that calendar/calnedar2 plugin created
// (Based on calendar and recent plugin)

/*
 ** pagename
 * - A working root of calendar or calendar2 plugin
 *   pagename/2004-12-30
 *   pagename/2004-12-31
 *   ...
 *
 ** (yyyy-mm|n|this)
 * this    - Show 'this month'
 * yyyy-mm - Show pages at year:yyyy and month:mm
 * n       - Show first n pages
 * x*n     - Show first n pages from x-th page (0 = first)
 *
 ** [mode]
 * past   - Show today, and the past below. Recommended for ChangeLogs and diaries (default)
 * future - Show today, and the future below. Recommended for event planning and scheduling
 * view   - Show all, from the past to the future
 *
 ** [separater]
 * - Specify separator of yyyy/mm/dd
 * - Default: '-' (yyyy-mm-dd)
 *
 * TODO
 *  Stop showing links 'next month' and 'previous month' with past/future mode for 'this month'
 *    #calendar_viewer(pagename,this,past)
 */

class xpwiki_plugin_calendar_viewer extends xpwiki_plugin {
	function plugin_calendar_viewer_init () {
		// Page title's date format
		//  * See PHP date() manual for detail
		//  * '$\w' = weeklabel defined in $_msg_week
		$this->cont['PLUGIN_CALENDAR_VIEWER_DATE_FORMAT'] =
			//	FALSE         // 'pagename/2004-02-09' -- As is
			//	'D, d M, Y'   // 'Mon, 09 Feb, 2004'
			//	'F d, Y'      // 'February 09, 2004'
			//	'[Y-m-d]'     // '[2004-02-09]'
			'Y/n/j ($\w)' // '2004/2/9 (Mon)'
		;

		// ----

		$this->cont['PLUGIN_CALENDAR_VIEWER_USAGE'] =
			'#calendar_viewer(pagename,this|yyyy-mm|n|x*y[,mode[,separater]])';

		$this->conf['Use_boxdate'] = 1;
		$this->conf['MinimumHeaderLevel'] = 2;
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_calendar_viewer_convert()
	{
		$this->func->add_tag_head('calendar.css');

		static $viewed = array();
		if (!isset($viewed[$this->xpwiki->pid])) {$viewed[$this->xpwiki->pid] = array();}

		if (func_num_args() < 2)
			return $this->cont['PLUGIN_CALENDAR_VIEWER_USAGE'] . '<br />' . "\n";

		$func_args = func_get_args();

		$min_header = $this->conf['MinimumHeaderLevel'];

		// for PukiWikiMod compat
		$_options = array();
		foreach($func_args as $option) {
			$option = trim($option);
			if (strtolower($option) == 'notoday') {
				//$notoday = true;
			} else if(strtolower(substr($option, 0, 9)) === 'contents:') {
				//$contents_lev = (int)substr($option, 9);
			} else if(strtolower(substr($option, 0, 7)) === 'header:') {
				$min_header = (int)substr($option, 7);
			} else {
				$_options[] = $option;
			}
		}
		$func_args = $_options;

		$min_header = max(1, min(5, $min_header));

		// Default values
		$pagename    = $func_args[0];	// 基準となるページ名
		if (strtolower($pagename) === "this") {
			$pagename = $this->root->vars['page'];
		}
		$page_YM     = '';	// 一覧表示する年月
		$limit_base  = 0;	// 先頭から数えて何ページ目から表示するか (先頭)
		$limit_pitch = 0;	// 何件づつ表示するか
		$limit_page  = 0;	// サーチするページ数
		$mode        = 'past';	// 動作モード
		$date_sep    = '-';	// 日付のセパレータ calendar2なら '-', calendarなら ''

		// Check $func_args[1]
		$matches = array();
		if (preg_match('/[0-9]{4}' . $date_sep . '[0-9]{2}/', $func_args[1])) {
			// 指定年月の一覧表示
			$page_YM     = $func_args[1];
			$limit_page  = 31;
		} else if (preg_match('/this/si', $func_args[1])) {
			// 今月の一覧表示
			$page_YM     = $this->func->get_date('Y' . $date_sep . 'm');
			$limit_page  = 31;
		} else if (preg_match('/^[0-9]+$/', $func_args[1])) {
			// n日分表示
			$limit_pitch = $func_args[1];
			$limit_page  = $func_args[1];
		} else if (preg_match('/(-?[0-9]+)\*([0-9]+)/', $func_args[1], $matches)) {
			// 先頭より数えて x ページ目から、y件づつ表示
			$limit_base  = $matches[1];
			$limit_pitch = $matches[2];
			$limit_page  = $matches[1] + $matches[2]; // 読み飛ばす + 表示する
		} else {
			return '#calendar_viewer(): ' . $this->root->_err_calendar_viewer_param2 . '<br />' . "\n";
		}

		// $func_args[2]: Mode setting
		if (isset($func_args[2]) && preg_match('/^(past|view|future)$/si', $func_args[2]))
			$mode = $func_args[2];

		// $func_args[3]: Change default delimiter
		if (isset($func_args[3])) $date_sep = $func_args[3];

		// Avoid Loop etc.
		if (isset($viewed[$this->xpwiki->pid][$pagename])) {
			$s_page = htmlspecialchars($pagename);
			return "#calendar_viewer(): You already view: $s_page<br />";
		} else {
			$viewed[$this->xpwiki->pid][$pagename] = TRUE; // Valid
		}

		// 一覧表示するページ名とファイル名のパターン　ファイル名には年月を含む
		if ($pagename == '') {
			// pagename無しのyyyy-mm-ddに対応するための処理
			$pagepattern     = '';
			$pagepattern_len = 0;
			$filepattern     = $this->func->encode($page_YM);
			$filepattern_len = strlen($filepattern);
		} else {
			$pagepattern     = $this->func->strip_bracket($pagename) . '/';
			$pagepattern_len = strlen($pagepattern);
			$pagepattern    .= $page_YM;
		}

		// ページリストの取得
		$pagelist = array();
		$_date = $this->func->get_date('Y' . $date_sep . 'm' . $date_sep . 'd');
		$page_date  = '';
		foreach($this->func->get_existpages(FALSE,$pagepattern) as $page) {

			$page_date = substr($page, $pagepattern_len);
			// Verify the $page_date pattern (Default: yyyy-mm-dd).
			// Past-mode hates the future, and
			// Future-mode hates the past.
			if (($this->plugin_calendar_viewer_isValidDate($page_date, $date_sep) == FALSE) ||
				(!$page_YM && (($page_date > $_date && ($mode == 'past')) ||
				($page_date < $_date && ($mode == 'future')))))
					continue;

			$pagelist[] = $page;
		}

		if ($mode == 'past') {
			rsort($pagelist);	// New => Old
		} else {
			sort($pagelist);	// Old => New
		}

		// Include start
		$tmppage     = $this->root->vars['page'];
		$return_body = '';

		// $limit_page の件数までインクルード
		$tmp = max($limit_base, 0); // Skip minus

		while ($tmp < $limit_page) {
			if (! isset($pagelist[$tmp])) break;

			$page = $pagelist[$tmp];

			$src = $this->func->get_source($page);

			$src = preg_replace_callback('/^\*{1,5}/m', create_function('$match',
					'return substr($match[0] . str_repeat("*", ('.$min_header.' - 1)), 0, 5);'
					), $src);

			if ($this->conf['Use_boxdate']) {
				$src = preg_replace('/^#boxdate(\(.*?\))?\n/m', '', $src, 1);
			}

			$body = $this->func->convert_html($src, $page);

			if ($this->conf['Use_boxdate']) {
				$s_page = $this->func->do_plugin_convert('boxdate', 'link,page:' . $page);
			} else {
				if ($this->cont['PLUGIN_CALENDAR_VIEWER_DATE_FORMAT'] !== FALSE) {
					$time = strtotime($this->func->basename($page)); // $date_sep must be assumed '-' or ''!
					if ($time === -1 || $time === FALSE) {
						$s_page = htmlspecialchars($page); // Failed. Why?
					} else {
						$week   = $this->root->weeklabels[date('w', $time)];
						$D      = date('D', $time);
						$s_page = str_replace(
								array('$w', '$D'),
								array($week, $D),
								date($this->cont['PLUGIN_CALENDAR_VIEWER_DATE_FORMAT'], $time)
								);
					}

				} else {
					$s_page = htmlspecialchars($page);
				}
				$s_page = $this->func->make_pagelink($page, $s_page);
			}

			$edit = '';
			if (!$this->cont['PKWK_READONLY'] && $this->func->check_editable($page, FALSE, FALSE)) {
				$edit = $this->root->script . '?cmd=edit&amp;page=' . rawurlencode($page);
				$edit = '<div style="float:right;padding-right:10px;font-size:90%;"> (<a href="' . $edit . '">' . $this->root->_LANG['skin']['edit'] . '</a>)</div>';
			}

			$head   = $edit . $s_page . "\n";
			$return_body .= '<div class="calendar_entry_base">' . $head . '<div class="calendar_entry">' . $body . '</div></div><div class="calendar_hr"><hr /></div>';

			++$tmp;
		}

		// ここで、前後のリンクを表示
		// ?plugin=calendar_viewer&file=ページ名&date=yyyy-mm
		$enc_pagename = rawurlencode(substr($pagepattern, 0, $pagepattern_len - 1));

		if ($page_YM != '') {
			// 年月表示時
			$date_sep_len = strlen($date_sep);
			$this_year    = substr($page_YM, 0, 4);
			$this_month   = substr($page_YM, 4 + $date_sep_len, 2);

			// 次月
			$next_year  = $this_year;
			$next_month = $this_month + 1;
			if ($next_month > 12) {
				++$next_year;
				$next_month = 1;
			}
			$next_YM = sprintf('%04d%s%02d', $next_year, $date_sep, $next_month);

			// 前月
			$prev_year  = $this_year;
			$prev_month = $this_month - 1;
			if ($prev_month < 1) {
				--$prev_year;
				$prev_month = 12;
			}
			$prev_YM = sprintf('%04d%s%02d', $prev_year, $date_sep, $prev_month);
			if ($mode == 'past') {
				$left_YM    = str_replace($date_sep, '', $prev_YM);
				$left_text  = '&lt;&lt;' . $prev_YM; // <<
				$right_YM   = str_replace($date_sep, '', $next_YM);
				$right_text = $next_YM . '&gt;&gt;'; // >>
			} else {
				$left_YM    = str_replace($date_sep, '', $prev_YM);
				$left_text  = '&lt;&lt;' . $prev_YM; // <<
				$right_YM   = str_replace($date_sep, '', $next_YM);
				$right_text = $next_YM . '&gt;&gt;'; // >>
			}
		} else {
			// n件表示時
			if ($limit_base <= 0) {
				$left_YM = ''; // 表示しない (それより前の項目はない)
			} else {
				$left_YM   = $limit_base - $limit_pitch . '*' . $limit_pitch;
				$left_text = sprintf($this->root->_msg_calendar_viewer_left, $limit_pitch);

			}
			if ($limit_base + $limit_pitch >= count($pagelist)) {
				$right_YM = ''; // 表示しない (それより後の項目はない)
			} else {
				$right_YM   = $limit_base + $limit_pitch . '*' . $limit_pitch;
				$right_text = sprintf($this->root->_msg_calendar_viewer_right, $limit_pitch);
			}
		}

		// ナビゲート用のリンクを末尾に追加
		if ($left_YM != '' || $right_YM != '') {
			$s_date_sep = htmlspecialchars($date_sep);
			$left_link = $right_link = '';
			if ($page_YM != '') {
				$link = $this->root->script . '?plugin=calendar2&amp;file=' . $enc_pagename . '&amp;';
			} else {
				$link = $this->root->script . '?plugin=calendar_viewer&amp;mode=' . $mode .
				'&amp;file=' . $enc_pagename . '&amp;date_sep=' . $s_date_sep . '&amp;';
			}

			if ($left_YM != '')
				$left_link = '<a href="' . $link .
				'date=' . $left_YM . '">' . $left_text . '</a>';
			if ($right_YM != '')
				$right_link = '<a href="' . $link .
				'date=' . $right_YM . '">' . $right_text . '</a>';
			// past modeは<<新 旧>> 他は<<旧 新>>
			$return_body .=
				'<div class="calendar_viewer">' .
			'<span class="calendar_viewer_left">'  . $left_link  . '</span>' .
			'<span class="calendar_viewer_right">' . $right_link . '</span>' .
			'</div>';
		}

		$this->root->get['page'] = $this->root->post['page'] = $this->root->vars['page'] = $tmppage;

		return $return_body;
	}

	function plugin_calendar_viewer_action()
	{
		$date_sep = '-';

		$return_vars_array = array();

		$page = $this->func->strip_bracket($this->root->vars['page']);
		$this->root->vars['page'] = '*';
		if (isset($this->root->vars['file'])) $this->root->vars['page'] = $this->root->vars['file'];

		$date_sep = $this->root->vars['date_sep'];

		$page_YM = $this->root->vars['date'];
		if ($page_YM == '') $page_YM = $this->func->get_date('Y' . $date_sep . 'm');
		$mode = $this->root->vars['mode'];

		// Set nest level
		if (!isset($this->root->rtf['convert_nest'])) {
			$this->root->rtf['convert_nest'] = 1;
		} else {
			++$this->root->rtf['convert_nest'];
		}

		$args_array = array($this->root->vars['page'], $page_YM, $mode, $date_sep);
		$return_vars_array['body'] = call_user_func_array (array(& $this, "plugin_calendar_viewer_convert"), $args_array);

		--$this->root->rtf['convert_nest'];

		//$return_vars_array['msg'] = 'calendar_viewer ' . $vars['page'] . '/' . $page_YM;
		$return_vars_array['msg'] = 'Calendar view ' . $this->func->make_pagelink($this->root->vars['page'], htmlspecialchars($this->root->vars['page']));
		if ($this->root->vars['page'] != '') $return_vars_array['msg'] .= ' : ';
		if (preg_match('/\*/', $page_YM)) {
			// うーん、n件表示の時はなんてページ名にしたらいい？
		} else {
			$return_vars_array['msg'] .= htmlspecialchars($page_YM);
		}

		$this->root->vars['page'] = $page;

		return $return_vars_array;
	}

	function plugin_calendar_viewer_isValidDate(&$aStr, $aSepList = '-/ .')
	{
		$matches = array();
		if ($aSepList == '') {
			// yyymmddとしてチェック（手抜き(^^;）
			return checkdate(substr($aStr, 4, 2), substr($aStr, 6, 2), substr($aStr, 0, 4));
		} else if (preg_match("#^(([0-9]{2,4})[$aSepList]([0-9]{1,2})[$aSepList]([0-9]{1,2}))([$aSepList][0-9]+)?$#", $aStr, $matches) ) {
			$aStr = $matches[1];
			return checkdate($matches[3], $matches[4], $matches[2]);
		} else {
			return FALSE;
		}
	}
}
?>