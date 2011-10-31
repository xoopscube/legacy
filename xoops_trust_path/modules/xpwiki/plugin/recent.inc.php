<?php
// $Id: recent.inc.php,v 1.19 2011/07/29 06:23:06 nao-pon Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2002      Y.MASUI http://masui.net/pukiwiki/ masui@masui.net
// License: GPL version 2
//
// Recent plugin -- Show RecentChanges list
//   * Usually used at 'MenuBar' page
//   * Also used at special-page, without no #recnet at 'MenuBar'

class xpwiki_plugin_recent extends xpwiki_plugin {
	function plugin_recent_init () {

		// Default number of 'Show latest N changes'
		$this->cont['PLUGIN_RECENT_DEFAULT_LINES'] =  10;

		// Limit number of executions
		$this->cont['PLUGIN_RECENT_EXEC_LIMIT'] =  2; // N times per one output

		// ----
		$this->cont['PLUGIN_RECENT_USAGE'] =  '#recent([Base Page,][Number to show])';

		// Place of the cache of 'RecentChanges'
		$this->cont['PLUGIN_RECENT_CACHE'] =  $this->cont['CACHE_DIR'] . $this->cont['PKWK_MAXSHOW_CACHE'];

	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function plugin_recent_convert()
	{
		static $exec_count = array();

		if (!isset($exec_count[$this->xpwiki->pid])) {$exec_count[$this->xpwiki->pid] = 1;}

		$prefix = "";
		$recent_lines = 0;
		$nochild = FALSE;
		$where = '';
		$dayaft_time = 0;
		$uid = 0;
		if(func_num_args()>0) {
			$args = func_get_args();
			$recent_lines = (int)$args[0];
			$prefix = $args[0];
			$nochild = (substr($prefix, -1) === '/');
			$prefix = rtrim($prefix, '/');
			if ($this->func->is_page($prefix) || ! $prefix)
			{
				if (isset($args[1]) && is_numeric($args[1]))
					$recent_lines = $args[1];
			}
			else if (isset($args[1]))
			{
				$prefix = $args[1];
				$prefix = preg_replace("/\/$/","",$prefix);
				if ($this->func->is_page($prefix) || ! $prefix)
				{
					if (isset($args[0]) && is_numeric($args[0]))
						$recent_lines = $args[0];
				}
				else
					$prefix = "";
			}
			else
				$prefix = "";

			if (isset($args[2]) && is_numeric($args[2]) && $args[2] != 0) {
				$dayaft = (($args[2] > 0)? '+' : '-' ) . abs($args[2]) . ' days';
				$dayaft_time = strtotime($dayaft) - $this->cont['LOCALZONE'];
				$where = 'editedtime < \'' . $dayaft_time . '\'';
			}

			if (isset($args[3]) && is_numeric($args[3]) && $args[3] != 0) {
				$uid = $args[3];
				if ($where) {
					$where = '('.$where.') & ';
				}
				$where .= '(uid = \'' . $args[3] . '\')';
			}
		}

		$_prefix = ($prefix)? $prefix . '/' : '';
		$prefix_page = ($prefix)? $this->func->make_pagelink($prefix).' ' : '';

		if ($uid) {
			$prefix_page .= '(' . $this->func->getUnameFromId($uid) .  ') ';
		}

		if ($dayaft_time) {
			$prefix_page .= $this->func->get_date($this->root->date_format, $dayaft_time) . ' &#65374; ';
		}



		if (!$recent_lines) $recent_lines = $this->cont['PLUGIN_RECENT_DEFAULT_LINES'];

		// Show only N times
		if ($exec_count[$this->xpwiki->pid] > $this->cont['PLUGIN_RECENT_EXEC_LIMIT']) {
			return '#recent(): You called me too much' . '<br />' . "\n";
		} else {
			++$exec_count[$this->xpwiki->pid];
		}

		$res = array();
		if ($this->root->render_mode === 'block' && isset($GLOBALS['Xpwiki_'.$this->root->mydirname]['page'])) {
			$res = $this->func->set_current_page($GLOBALS['Xpwiki_'.$this->root->mydirname]['page']);
			$this->root->pagecache_min = 0;
		}

		// Get latest N changes
		$nolisting = (!$_prefix || !$this->func->check_non_list($_prefix));
		$options = array('limit' =>$recent_lines, 'order' => ' ORDER BY editedtime DESC', 'nolisting' => $nolisting, 'withtime' =>TRUE, 'nochild' => $nochild);
		if ($where) {
			$options['where'] = $where;
		}
		$lines = $this->func->get_existpages(FALSE, $_prefix, $options);

		$date = $items = '';
		foreach ($lines as $line) {
			list($time, $page) = explode("\t", rtrim($line));

			$_date = $this->func->get_date($this->root->date_format, $time);
			if ($date != $_date) {
				// End of the day
				if ($date != '') $items .= '</ul>' . "\n";

				// New day
				$date = $_date;
				$items .= '<strong>' . $date . '</strong>' . "\n" .
				'<ul class="recent_list">' . "\n";
			}

			$s_page = htmlspecialchars($page);
			if($page === $this->root->vars['page']) {
				// No need to link to the page you just read, or notify where you just read
				$items .= ' <li><!--NA-->' . str_replace('/', '/' . $this->root->hierarchy_insert, $s_page) . '<!--/NA--></li>' . "\n";
			} else {
				$r_page = rawurlencode($page);
				$passage = $this->root->show_passage ? ' ' . $this->func->get_passage($time) : '';
				$compact = ($prefix)? '#compact:'.$prefix : '';
				$items .= ' <li>'.$this->func->make_pagelink($page, $compact).'</li>' . "\n";

			}
		}
		// End of the day
		if ($date != '') $items .= '</ul>' . "\n";

		if ($res) $this->func->set_current_page($res['page']);

		return sprintf($this->root->_recent_plugin_frame, $prefix_page, count($lines), $items);
	}
}
?>