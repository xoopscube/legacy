<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: popular.inc.php,v 1.12 2009/11/17 09:18:42 nao-pon Exp $
//

/*
 * PukiWiki popular プラグイン
 * (C) 2002, Kazunori Mizushima <kazunori@uc.netyou.jp>
 *
 * 人気のある(アクセス数の多い)ページの一覧を recent プラグインのように表示します。
 * 通算および今日に別けて一覧を作ることができます。
 * counter プラグインのアクセスカウント情報を使っています。
 *
 * [使用例]
 * #popular
 * #popular(20)
 * #popular(20,FrontPage|MenuBar)
 * #popular(20,FrontPage|MenuBar,1)
 * #popular(20,FrontPage|MenuBar,1,XOOPS)
 * #popular(20,FrontPage|MenuBar,-1,,1)
 *
 * [引数]
 * 1 - 表示する件数                                    default 10
 * 2 - 表示させないページ(半角スペースまたは | 区切り) default なし
 * 3 - 今日(today|1)か昨日(yesterday|-1)か通算(total|0)の一覧かのフラグ         default false
 * 4 - 集計対象の仮想階層ページ名                      default なし
 * 5 - 多階層ページの場合、最下層のみを表示 ( 0 or 1 ) default 0
 */

class xpwiki_plugin_popular extends xpwiki_plugin {
	
	function plugin_popular_init()
	{
		$this->cont['PLUGIN_POPULAR_DEFAULT'] =  10;
	}
	
	function can_call_otherdir_convert() {
		return 4;
	}

	function plugin_popular_convert()
	{
		
		$max = $this->cont['PLUGIN_POPULAR_DEFAULT'];
		$except = '';
	
		$array = func_get_args();
		$yesterday = $today = FALSE;
		$prefix = '';
		$compact = 0;
	
		switch (func_num_args()) {
		case 5:
			if ($array[4]) $compact = 1;
		case 4:
			$prefix = $array[3];
			$prefix = rtrim($prefix, '/');
		case 3:
			if ($array[2]) {
				$array[2] = strtolower($array[2]);
				if ($array[2] !== 'false' && $array[2] !== 'total') {
					$today = $this->func->get_date('Y/m/d');
					if ($array[2] === 'yesterday' || $array[2] === '-1') {
						$yesterday = $this->func->get_date('Y/m/d', $this->cont['UTIME'] - 86400);
					}
				}
			}
		case 2:
			$except = $array[1];
			$except = str_replace(array("&#124;","&#x7c;",'#'), '|', $except);
		case 1:
			$max = (int)$array[0]; 
			$max = (!$max)? $this->cont['PLUGIN_POPULAR_DEFAULT'] : $max;
		}
	
		$nopage = ' AND p.editedtime != 0';
		if ($except)
		{
			$excepts = explode('|', $except);
			foreach($excepts as $_except)
			{
				if (substr($_except,-1) == '/')
				{
					$_except .= '%';
				}
				$nopage .= ' AND (p.name NOT LIKE \'' . $_except . '\')';
			}
		}
		$counters = array();
		
		$where = $this->func->get_readable_where('p.');

		if ($prefix) {
			$prefix = $this->func->strip_bracket($prefix);
			if ($where)
				$where = ' (p.name LIKE \'' . $prefix . '/%\') AND (' . $where . ')';
			else
				$where = ' p.name LIKE \'' . $prefix . '/%\'';
		}
	
		if ($where) $where = ' AND (' . $where . ')';
		if ($today) {
			$_where = $where;
			$where = ' WHERE (c.pgid = p.pgid) AND (p.name NOT LIKE \':%\') AND (today = \'' . $today . '\')' . ($yesterday ? 'AND (c.`yesterday_count` != 0)' : '') . $nopage . $_where;
			if ($yesterday) {
				$where .= ' UNION SELECT p.`name`, c.`today_count` AS `count`';
				$where .= ' FROM ' . $this->xpwiki->db->prefix($this->root->mydirname . '_count') . ' as c INNER JOIN ' . $this->xpwiki->db->prefix($this->root->mydirname . '_pginfo') . ' as p ON c.pgid = p.pgid';
				$where .= ' WHERE (p.name NOT LIKE \':%\') AND (today = \'' . $yesterday . '\')' . $nopage . $_where;
				$select = 'p.`name`, c.`yesterday_count` AS `count`';
			} else {
				$select = 'p.`name`, c.`today_count` AS `count`';
			}
		} else {
			$where = ' WHERE (p.name NOT LIKE \':%\')' . $nopage . $where;
			$select = 'p.`name`, c.`count` AS `count`';
		}
		$query = 'SELECT ' . $select . ' FROM ' . $this->xpwiki->db->prefix($this->root->mydirname . '_count') . ' as c INNER JOIN ' . $this->xpwiki->db->prefix($this->root->mydirname . '_pginfo') . ' as p ON c.pgid = p.pgid ' . $where . ' ORDER BY `count` DESC LIMIT ' . $max;
		$res = $this->xpwiki->db->query($query);
		if ($res) {
			while($data = $this->xpwiki->db->fetchRow($res)) {
				$counters[$data[0]] = $data[1];
			}
		}
	
		$items = '';
		if ($prefix) {
			$bypege = ' [ ' . $this->func->make_pagelink($prefix, $prefix) . ' ] ';
		} else {
			$bypege = '';
		}
		
		if (count($counters))
		{
			$_style = $this->root->_ul_left_margin + $this->root->_ul_margin;
			$_style = ' style="margin-left:' . $_style . 'px;padding-left:' . $_style . 'px;';
			$items = '<ul class="popular_list"' . $_style . '">';
			$new_mark = '';
			
			foreach ($counters as $page=>$count) {
				//Newマーク付加
				if ($this->func->exist_plugin_inline('new'))
					$new_mark = $this->func->do_plugin_inline('new', $page . ',nolink');
				
				if ($compact)
					$page = $this->func->make_pagelink($page,$this->func->basename($page));
				else
				{
					if ($prefix)
						$page = $this->func->make_pagelink($page, '#compact:' . $prefix);
					else
						$page = $this->func->make_pagelink($page);
				}
				
				$items .= ' <li>' . $page . '<span class="counter">(' . $count . ')</span>' . $new_mark . '</li>' . "\n";
				}
			$items .= '</ul>';
		}
		//return sprintf($today ? $this->root->_popular_plugin_today_frame : $this->root->_popular_plugin_frame,count($counters),$bypege,$items);
		return sprintf($today ? ($yesterday ? $this->root->_popular_plugin_yesterday_frame : $this->root->_popular_plugin_today_frame) : $this->root->_popular_plugin_frame, count($counters), $items, $bypege);

	}
}
?>