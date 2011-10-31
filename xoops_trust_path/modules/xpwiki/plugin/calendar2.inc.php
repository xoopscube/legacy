<?php
// $Id: calendar2.inc.php,v 1.15 2011/09/17 07:38:27 nao-pon Exp $
//
// Calendar2 plugin
//
// Usage:
//	#calendar2({[pagename|*],[yyyymm],[off]})
//	off: Don't view today's

class xpwiki_plugin_calendar2 extends xpwiki_plugin {
	function plugin_calendar2_init () {
		$this->conf['NaviTitle'] = 'Archives';

		$this->conf['options'] = array(
			'class' => 'button',
		);
	}

	function plugin_calendar2(&$func) {
		parent::xpwiki_plugin($func);

		$this->conf['options'] = array(
			'class' => 'button',
		);
	}

	function can_call_otherdir_inline() {
		return 1;
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_calendar2_inline() {

		$options = $this->conf['options'];

		$page = $this->root->vars['page'];
		$args = func_get_args();
		$body = array_pop($args);
		if (!$body) $body = 'New item';
		if ($args) {
			$this->fetch_options($options, $args);
			if (! empty($options['_args'])) {
				if ($this->func->is_pagename($options['_args'][0])) {
					$page = $this->func->strip_bracket($options['_args'][0]);
				}
			}
		}
		$options['class'] = htmlspecialchars($options['class']);

		$date = date('Y-m-d');
		$page .= '/' . $date;

		$base = $page;
		$i = 1;
		while($this->func->is_page($page)) {
			$page = $base . '-' . $i++;
		}
		if ($this->func->check_editable($page, FALSE, FALSE)) {
			return '<span class="'.$options['class'].'"><a href="'.$this->root->script.'?cmd=edit&amp;page='.rawurlencode($page).'">'.$body.'</a></span>';
		}
		return '';
	}

	function plugin_calendar2_convert() {
		$this->func->add_tag_head('calendar.css');

		$date_str = $this->func->get_date('Ym');
		$base     = $this->func->strip_bracket($this->root->vars['page']);

		$today_view = TRUE;
		$date_view = FALSE;
		$navi_view = FALSE;
		if (func_num_args()) {
			$args = func_get_args();
			foreach ($args as $arg) {
				if (is_numeric($arg) && (strlen($arg) == 6 || strlen($arg) == 8)) {
					$date_str = $arg;
					$date_view = TRUE;
				} else if ($arg === 'off') {
					$today_view = FALSE;
				} else if ($arg === 'navi') {
					$navi_view = TRUE;

				// for PukiWikiMod compat
				} else if(strtolower(substr($arg,0,9)) == "category:"){
					$category_view = htmlspecialchars(substr($arg,8));
				} else if(strtolower(substr($arg,0,9)) == "contents:"){
					$contents_lev = (int)substr($arg,9);

				} else {
					$base = $this->func->strip_bracket($arg);
				}
			}
		}
		if ($base === '*' || !$this->func->is_pagename($base)) {
			$base   = '';
			$prefix = '';
		} else {
			$prefix = $base . '/';
		}
		$r_base   = rawurlencode($base);
		$s_base   = htmlspecialchars($base);
		$r_prefix = rawurlencode($prefix);
		$s_prefix = htmlspecialchars($prefix);

		$yr  = substr($date_str, 0, 4);
		$mon = substr($date_str, 4, 2);
		if (strlen($date_str) === 8) {
			$now_day = substr($date_str,6,2);
			$other_month = 0;
		} else {
			if ($yr != $this->func->get_date('Y') || $mon != $this->func->get_date('m')) {
				$now_day = 1;
				$other_month = 1;
			} else {
				$now_day = $this->func->get_date('d');
				$other_month = 0;
			}
		}

		$today = getdate(mktime(0, 0, 0, $mon, $now_day, $yr) - $this->cont['LOCALZONE'] + $this->cont['ZONETIME']);

		$m_num = $today['mon'];
		$d_num = $today['mday'];
		$year  = $today['year'];

		$f_today = getdate(mktime(0, 0, 0, $m_num, 1, $year) - $this->cont['LOCALZONE'] + $this->cont['ZONETIME']);
		$wday = $f_today['wday'];
		$day  = 1;

		$y = substr($date_str, 0, 4) + 0;
		$m = substr($date_str, 4, 2) + 0;

		$this_date_str = sprintf('%04d%02d', $y, $m);
		$m_name = ($date_view)? $year . '.' . $m_num : '<a href="'.$this->root->script.'?plugin=calendar2&amp;file='.$r_base.'&amp;date='.$this_date_str.'">'. $year . '.' . $m_num . '</a>';

		$prev_date_str = ($m == 1) ?
			sprintf('%04d%02d', $y - 1, 12) : sprintf('%04d%02d', $y, $m - 1);

		$next_date_str = ($m == 12) ?
			sprintf('%04d%02d', $y + 1, 1) : sprintf('%04d%02d', $y, $m + 1);

		// Can make new page.
		$is_editable = $this->func->check_editable($base . '/1', FALSE, FALSE);

		$ret = '';

		if ($navi_view) {
			$ret .= $this->getNavi($prefix);
		}

		if ($today_view) {
			$ret = '<table border="0" summary="calendar frame">' . "\n" .
			' <tr>' . "\n" .
			'  <td valign="top">' . "\n";
		}
		$ret .= <<<EOD
<div class="calendar_calendar">
 <table class="style_calendar" cellspacing="1" summary="calendar body">
  <tr>
   <td class="style_td_caltop" colspan="7">
    <a href="{$this->root->script}?plugin=calendar2&amp;file=$r_base&amp;date=$prev_date_str">&lt;&lt;</a>
    <strong>$m_name</strong>
    <a href="{$this->root->script}?plugin=calendar2&amp;file=$r_base&amp;date=$next_date_str">&gt;&gt;</a>
EOD;

		if ($prefix) $ret .= "\n" .
		'      <br />[<a href="' . $this->func->get_page_uri($base, true) . '">' . $s_base . '</a>]';

		$ret .= "\n" .
			'     </td>' . "\n" .
			'    </tr>'  . "\n" .
			'    <tr>'   . "\n";

		foreach($this->root->weeklabels as $label)
			$ret .= '     <td class="style_td_week">' . $label . '</td>' . "\n";

		$ret .= '    </tr>' . "\n" .
		'    <tr>'  . "\n";
		// Blank
		for ($i = 0; $i < $wday; $i++)
			$ret .= '     <td class="style_td_blank">&nbsp;</td>' . "\n";

		// this month pages
		$m_pages = $this->func->get_existpages(FALSE, $prefix . sprintf('%4d-%02d-', $year, $m_num), array('select' => array('name')));

		while (checkdate($m_num, $day, $year)) {
			$dt     = sprintf('%4d-%02d-%02d', $year, $m_num, $day);
			$page   = $prefix . $dt;
			$r_page = rawurlencode($page);
			$s_page = htmlspecialchars($page);

			if ($wday == 0 && $day > 1) {
				$ret .=
					'    </tr>' . "\n" .
					'    <tr>' . "\n";
			}

			$style = 'style_td_day'; // Weekday
			if (! $other_month && ($day == $today['mday']) && ($m_num == $today['mon']) && ($year == $today['year'])) { // Today
				$style = 'style_td_today';
			} else if ($wday == 0) { // Sunday
				$style = 'style_td_sun';
			} else if ($wday == 6) { //  Saturday
				$style = 'style_td_sat';
			}

			// for PukiWikiMod compat
			$moblog_page = $page."-0";
			$normal_page = isset($m_pages[$page]);
			if ($normal_page || isset($m_pages[$moblog_page])) {
				$_page = ($normal_page)? $page : $moblog_page;
				$link = '<div class="style_td_written">'.$this->func->make_pagelink($_page, $day).'</div>';
			} else {
				if (! $is_editable) {
					$link = $day;
				} else {
					$link = $this->root->script . '?cmd=edit&amp;page=' . $r_page . '&amp;refer=' . $r_base;
					$link = '<a href="' . $link . '" title="' . $s_page . '">' . $day . '</a>';
				}
			}

			$ret .= '     <td class="' . $style . '">' . "\n" .
			'      ' . $link . "\n" .
			'     </td>' . "\n";
			++$day;
			$wday = ++$wday % 7;
		}

		if ($wday > 0)
			while ($wday++ < 7) // Blank
				$ret .= '     <td class="style_td_blank">&nbsp;</td>' . "\n";

		$ret .= '  </tr>'   . "\n" .
				' </table>' . "\n" .
				'</div>' . "\n";

		if ($today_view) {
			$tpage = $prefix . sprintf('%4d-%02d-%02d', $today['year'],	$today['mon'], $today['mday']);
			$r_tpage = rawurlencode($tpage);
			if ($this->func->is_page($tpage)) {
				$_page = $this->root->vars['page'];
				$str = $this->func->convert_html($this->func->get_source($tpage), $tpage);
				if (! $this->cont['PKWK_READONLY'] && $this->func->check_editable($tpage, FALSE, FALSE)) {
					$str .= '<hr /><a class="small" href="' . $this->root->script .
						'?cmd=edit&amp;page=' . $r_tpage . '">' .
						$this->root->_calendar2_plugin_edit . '</a>';
				}
			} else {
				$str = sprintf($this->root->_calendar2_plugin_empty,
				$this->func->make_pagelink($tpage));
			}
			$ret .= '  </td>' . "\n" .
				'  <td valign="top">' . $str . '</td>' . "\n" .
				' </tr>'   . "\n" .
				'</table>' . "\n" .
				'</div>' . "\n";
		}

		return $ret;
	}

	function plugin_calendar2_action() {

		$page = $this->func->strip_bracket($this->root->vars['page']);
		$this->root->vars['page'] = '*';
		if ($this->root->vars['file']) $this->root->vars['page'] = $this->root->vars['file'];

		$date = $this->root->vars['date'];

		if ($date == '') $date = $this->func->get_date('Ym');
		$yy = sprintf('%04d.%02d', substr($date, 0, 4),substr($date, 4, 2));

		if (strlen($date) === 8) {
			$yy = sprintf("%04d.%02d.%02d",substr($date,0,4),substr($date,4,2),substr($date,6,2));
			$args = $this->root->vars['page'] . ',' . $date;
		} else {
			$yy = sprintf("%04d.%02d",substr($date,0,4),substr($date,4,2));
			$args = $this->root->vars['page'] . ',' . $date . ',off,navi';
		}

		//$aryargs = array($this->root->vars['page'], $date, 'off');
		$s_page  = htmlspecialchars($this->root->vars['page']);

		// Set nest level
		if (!isset($this->root->rtf['convert_nest'])) {
			$this->root->rtf['convert_nest'] = 1;
		} else {
			++$this->root->rtf['convert_nest'];
		}

		$ret['msg']  = 'calendar ' . $s_page . '/' . $yy;
		$ret['body'] = $this->func->do_plugin_convert('calendar2', $args);

		$args = $this->root->vars['page'] . ',' . str_replace('.', '-', $yy) . ',future,-';
		$plugin = & $this->func->get_plugin_instance('calendar_viewer');
		$ret['body'] .= $this->func->do_plugin_convert('calendar_viewer', $args);

		--$this->root->rtf['convert_nest'];

		$this->root->vars['page'] = $page;

		return $ret;
	}

	function getNavi($base) {
		$base = trim($base, '/');
		$ret = $res = array();
		foreach($this->func->get_existpages(FALSE, $base . '/') as $page) {
			$arr = explode('-', $this->func->basename($page));
			if (isset($arr[1])) {
				$y = intval($arr[0]);
				$m = intval($arr[1]);
				if ($y && $m && $y > 1900 && $m < 13) {
					if (isset($res[$y][$m])) {
						++$res[$y][$m];
					} else {
						$res[$y][$m] = 1;
					}
				}
			}
		}
		if ($res) {
			$link = $this->root->script . '?plugin=calendar2&amp;file=' . rawurlencode($base) . '&amp;date=';
			ksort($res);
			foreach ($res as $y => $mArr) {
				$ret[] = '<dl><dt>' . $y . '<dt><dd>';
				$mRet = array();
				for ($m = 1; $m < 13; $m++) {
					if (! empty($mArr[$m])) {
						$_m = '<a href="' . $link . sprintf('%04d%02d', $y, $m) . '" title="' . sprintf('%d-%d (%d)', $y, $m, $mArr[$m]) . '">' . $m . '</a>';
						$mRet[] = '<span class="calendar_navi_have"> ' . $_m . ' </span>';;
					} else {
						$mRet[] = '<span class="calendar_navi_empty"> ' . $m . ' </span>';
					}
				}
				$ret[] = join('', $mRet);
				$ret[] = '</dd></dl>';
			}
		}
		return '<div class="calendar_navi"><div class="calendar_navi_title">' . $this->conf['NaviTitle'] . '</div>' . join('', $ret) . '</div>';
	}
}
?>