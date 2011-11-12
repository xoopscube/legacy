<?php
// $Id: bugtrack.inc.php,v 1.6 2007/12/14 00:02:08 nao-pon Exp $
//
// PukiWiki BugTrack plugin
//
// Copyright:
// 2002-2005 PukiWiki Developers Team
// 2002 Y.MASUI GPL2  http://masui.net/pukiwiki/ masui@masui.net

class xpwiki_plugin_bugtrack extends xpwiki_plugin {
	function plugin_bugtrack_init()
	{
		// Numbering format
		$this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'] =  '%d'; // Like 'page/1'
		//$this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'] =  '%03d'; // Like 'page/001'


		static $init = array();

		if (isset($init[$this->xpwiki->pid])) return; // Already init
		if (isset($this->root->_plugin_bugtrack)) die('Global $_plugin_bugtrack had been init. Why?');
		$init[$this->xpwiki->pid] = TRUE;
	
		$this->root->_plugin_bugtrack = array(
			'priority_list'  => array('緊急', '重要', '普通', '低'),
			'state_list'     => array('提案', '着手', 'CVS待ち', '完了', '保留', '却下'),
			'state_sort'     => array('着手', 'CVS待ち', '保留', '完了', '提案', '却下'),
			'state_bgcolor'  => array('#ccccff', '#ffcc99', '#ccddcc', '#ccffcc', '#ffccff', '#cccccc', '#ff3333'),
			'header_color'   => '#44a',
			'header_bgcolor' => '#ffffcc',
			'base'     => 'ページ',
			'summary'  => 'サマリ',
			'nosummary'=> 'ここにサマリを記入して下さい',
			'priority' => '優先順位',
			'state'    => '状態',
			'name'     => '投稿者',
			'noname'   => '名無しさん',
			'date'     => '投稿日',
			'body'     => 'メッセージ',
			'category' => 'カテゴリー',
			'pagename' => 'ページ名',
			'pagename_comment' => '空欄のままだと自動的にページ名が振られます。',
			'version_comment'  => '空欄でも構いません',
			'version'  => 'バージョン',
			'submit'   => '追加'
		);
	}
	
	// #bugtrack: Show bugtrack form
	function plugin_bugtrack_convert()
	{
	
		if ($this->cont['PKWK_READONLY']) return ''; // Show nothing
	
		$base = $this->root->vars['page'];
		$category = array();
		if (func_num_args()) {
			$category = func_get_args();
			$_base    = $this->func->get_fullname($this->func->strip_bracket(array_shift($category)), $base);
			if ($this->func->is_pagename($_base)) $base = $_base;
		}
	
		return $this->plugin_bugtrack_print_form($base, $category);
	}
	
	function plugin_bugtrack_print_form($base, $category)
	{
		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;}
	
		++$id[$this->xpwiki->pid];
	
		$select_priority = "\n";
		$count = count($this->root->_plugin_bugtrack['priority_list']);
		$selected = '';
		for ($i = 0; $i < $count; ++$i) {
			if ($i == ($count - 1)) $selected = ' selected="selected"'; // The last one
			$priority_list = htmlspecialchars($this->root->_plugin_bugtrack['priority_list'][$i]);
			$select_priority .= '    <option value="' . $priority_list . '"' .
			$selected . '>' . $priority_list . '</option>' . "\n";
		}
	
		$select_state = "\n";
		for ($i = 0; $i < count($this->root->_plugin_bugtrack['state_list']); ++$i) {
			$state_list = htmlspecialchars($this->root->_plugin_bugtrack['state_list'][$i]);
			$select_state .= '    <option value="' . $state_list . '">' .
			$state_list . '</option>' . "\n";
		}
	
		if (empty($category)) {
			$encoded_category = '<input name="category" id="_p_bugtrack_category_' . $id[$this->xpwiki->pid] .
			'" type="text" />';
		} else {
			$encoded_category = '<select name="category" id="_p_bugtrack_category_' . $id[$this->xpwiki->pid] . '">';
			foreach ($category as $_category) {
				$s_category = htmlspecialchars($_category);
				$encoded_category .= '<option value="' . $s_category . '">' .
				$s_category . '</option>' . "\n";
			}
			$encoded_category .= '</select>';
		}
	
		$script     = $this->func->get_script_uri();
		$s_base     = htmlspecialchars($base);
		$s_name     = htmlspecialchars($this->root->_plugin_bugtrack['name']);
		$s_category = htmlspecialchars($this->root->_plugin_bugtrack['category']);
		$s_priority = htmlspecialchars($this->root->_plugin_bugtrack['priority']);
		$s_state    = htmlspecialchars($this->root->_plugin_bugtrack['state']);
		$s_pname    = htmlspecialchars($this->root->_plugin_bugtrack['pagename']);
		$s_pnamec   = htmlspecialchars($this->root->_plugin_bugtrack['pagename_comment']);
		$s_version  = htmlspecialchars($this->root->_plugin_bugtrack['version']);
		$s_versionc = htmlspecialchars($this->root->_plugin_bugtrack['version_comment']);
		$s_summary  = htmlspecialchars($this->root->_plugin_bugtrack['summary']);
		$s_body     = htmlspecialchars($this->root->_plugin_bugtrack['body']);
		$s_submit   = htmlspecialchars($this->root->_plugin_bugtrack['submit']);
		$body = <<<EOD
<form action="$script" method="post">
 <table border="0">
  <tr>
   <th><label for="_p_bugtrack_name_{$id[$this->xpwiki->pid]}">$s_name</label></th>
   <td><input id="_p_bugtrack_name_{$id[$this->xpwiki->pid]}" name="name" size="20" type="text" value="{$this->cont['USER_NAME_REPLACE']}"/></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_category_{$id[$this->xpwiki->pid]}">$s_category</label></th>
   <td>$encoded_category</td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_priority_{$id[$this->xpwiki->pid]}">$s_priority</label></th>
   <td><select id="_p_bugtrack_priority_{$id[$this->xpwiki->pid]}" name="priority">$select_priority   </select></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_state_{$id[$this->xpwiki->pid]}">$s_state</label></th>
   <td><select id="_p_bugtrack_state_{$id[$this->xpwiki->pid]}" name="state">$select_state   </select></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_pagename_{$id[$this->xpwiki->pid]}">$s_pname</label></th>
   <td><input  id="_p_bugtrack_pagename_{$id[$this->xpwiki->pid]}" name="pagename" size="20" type="text" />
    <small>$s_pnamec</small></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_version_{$id[$this->xpwiki->pid]}">$s_version</label></th>
   <td><input  id="_p_bugtrack_version_{$id[$this->xpwiki->pid]}" name="version" size="10" type="text" />
    <small>$s_versionc</small></td>
  </tr>
  <tr>
   <th><label for="_p_bugtrack_summary_{$id[$this->xpwiki->pid]}">$s_summary</label></th>
   <td><input  id="_p_bugtrack_summary_{$id[$this->xpwiki->pid]}" name="summary" size="60" type="text" /></td>
  </tr>
  <tr>
   <th><label   for="_p_bugtrack_body_{$id[$this->xpwiki->pid]}">$s_body</label></th>
   <td><textarea id="_p_bugtrack_body_{$id[$this->xpwiki->pid]}" name="body" cols="60" rows="6"></textarea></td>
  </tr>
  <tr>
   <td colspan="2" align="center">
    <input type="submit" value="$s_submit" />
    <input type="hidden" name="plugin" value="bugtrack" />
    <input type="hidden" name="mode"   value="submit" />
    <input type="hidden" name="base"   value="$s_base" />
   </td>
  </tr>
 </table>
</form>
EOD;
	
		return $body;
	}
	
	// Add new issue
	function plugin_bugtrack_action()
	{
	
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
		if ($this->root->post['mode'] != 'submit') return FALSE;
	
		$page = $this->plugin_bugtrack_write($this->root->post['base'], $this->root->post['pagename'], $this->root->post['summary'],
		$this->root->post['name'], $this->root->post['priority'], $this->root->post['state'], $this->root->post['category'],
		$this->root->post['version'], $this->root->post['body']);
	
		$this->func->send_location($page);
	}
	
	function plugin_bugtrack_write($base, $pagename, $summary, $name, $priority, $state, $category, $version, $body)
	{
	
		$base     = $this->func->strip_bracket($base);
		$pagename = $this->func->strip_bracket($pagename);
	
		$postdata = $this->plugin_bugtrack_template($base, $summary, $name, $priority,
		$state, $category, $version, $body);
	
		$id = $jump = 1;
		$page = $base . '/' . sprintf($this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'], $id);
		while ($this->func->is_page($page)) {
			$id   = $jump;
			$jump += 50;
			$page = $base . '/' . sprintf($this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'], $jump);
		}
		$page = $base . '/' . sprintf($this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'], $id);
		while ($this->func->is_page($page))
			$page = $base . '/' . sprintf($this->cont['PLUGIN_BUGTRACK_NUMBER_FORMAT'], ++$id);
	
		if ($pagename == '') {
			$this->func->page_write($page, $postdata);
		} else {
			$pagename = $this->func->get_fullname($pagename, $base);
			if ($this->func->is_page($pagename) || ! $this->func->is_pagename($pagename)) {
				$pagename = $page; // Set default
			} else {
				$this->func->page_write($page, 'move to [[' . $pagename . ']]');
			}
			$this->func->page_write($pagename, $postdata);
		}
	
		return $page;
	}
	
	// Generate new page contents
	function plugin_bugtrack_template($base, $summary, $name, $priority, $state, $category, $version, $body)
	{
	
		if (! preg_match("/^{$this->root->WikiName}$$/",$base)) $base = '[[' . $base . ']]';

		// save name to cookie
		if ($name) { $this->func->save_name2cookie($name); }

		if ($name != '' && ! preg_match("/^{$this->root->WikiName}$$/",$name)) $name = '[[' . $name . ']]';
	
		if ($name    == '') $name    = $this->root->_plugin_bugtrack['noname'];
		if ($summary == '') $summary = $this->root->_plugin_bugtrack['nosummary'];
	
		 return <<<EOD
* $summary

- {$this->root->_plugin_bugtrack['base'    ]}: $base
- {$this->root->_plugin_bugtrack['name'    ]}: $name
- {$this->root->_plugin_bugtrack['priority']}: $priority
- {$this->root->_plugin_bugtrack['state'   ]}: $state
- {$this->root->_plugin_bugtrack['category']}: $category
- {$this->root->_plugin_bugtrack['date'    ]}: now?
- {$this->root->_plugin_bugtrack['version' ]}: $version

** {$this->root->_plugin_bugtrack['body']}
$body
--------

#comment
EOD;
	}
	
	// ----------------------------------------
	// BugTrack-List plugin
	
	// #bugtrack_list plugin itself
	function plugin_bugtrack_list_convert()
	{
	//	global $script, $vars, $_plugin_bugtrack;
	
		$page = $this->root->vars['page'];
		if (func_num_args()) {
			list($_page) = func_get_args();
			$_page = $this->func->get_fullname($this->func->strip_bracket($_page), $page);
			if ($this->func->is_pagename($_page)) $page = $_page;
		}
	
		$data = array();
		$pattern = $page . '/';
		$pattern_len = strlen($pattern);
		foreach ($this->func->get_existpages(FALSE, $pattern) as $page)
			if (is_numeric(substr($page, $pattern_len)))
				array_push($data, $this->plugin_bugtrack_list_pageinfo($page));
	
		$count_list = count($this->root->_plugin_bugtrack['state_list']);
	
		$table = array();
		for ($i = 0; $i <= $count_list + 1; ++$i) $table[$i] = array();
	
		foreach ($data as $line) {
			list($page, $no, $summary, $name, $priority, $state, $category) = $line;
			foreach (array('summary', 'name', 'priority', 'state', 'category') as $item)
				$$item = htmlspecialchars($$item);
			$page_link = $this->func->make_pagelink($page, $page);
	
			$state_no = array_search($state, $this->root->_plugin_bugtrack['state_sort']);
			if ($state_no === NULL || $state_no === FALSE) $state_no = $count_list;
			$bgcolor = htmlspecialchars($this->root->_plugin_bugtrack['state_bgcolor'][$state_no]);
	
			$row = <<<EOD
 <tr>
  <td style="background-color:$bgcolor">$page_link</td>
  <td style="background-color:$bgcolor">$state</td>
  <td style="background-color:$bgcolor">$priority</td>
  <td style="background-color:$bgcolor">$category</td>
  <td style="background-color:$bgcolor">$name</td>
  <td style="background-color:$bgcolor">$summary</td>
 </tr>
EOD;
			$table[$state_no][$no] = $row;
		}
	
		$table_html = ' <tr>' . "\n";
		$color = htmlspecialchars($this->root->_plugin_bugtrack['header_color']);
		$bgcolor = htmlspecialchars($this->root->_plugin_bugtrack['header_bgcolor']);
		foreach (array('pagename', 'state', 'priority', 'category', 'name', 'summary') as $item)
			$table_html .= '  <th style="color:' . $color . ';background-color:' . $bgcolor . '">' .
			htmlspecialchars($this->root->_plugin_bugtrack[$item]) . '</th>' . "\n";
		$table_html .= ' </tr>' . "\n";
	
		for ($i = 0; $i <= $count_list; ++$i) {
			ksort($table[$i], SORT_NUMERIC);
			$table_html .= join("\n", $table[$i]);
		}
	
		return '<table border="1" style="width:100%;">' . "\n" .
		$table_html . "\n" .
		'</table>';
	}
	
	// Get one set of data from a page (or a page moved to $page)
	function plugin_bugtrack_list_pageinfo($page, $no = NULL, $recurse = TRUE)
	{
	
		if ($no === NULL)
			$no = preg_match('/\/([0-9]+)$/', $page, $matches) ? $matches[1] : 0;
	
		$source = $this->func->get_source($page);
	
		// Check 'moved' page _just once_
		$regex  = "/move\s*to\s*({$this->root->WikiName}|{$this->root->InterWikiName}|\[\[{$this->root->BracketName}\]\])/";
		$match  = array();
		if ($recurse && preg_match($regex, $source[0], $match))
			return $this->plugin_bugtrack_list_pageinfo($this->func->strip_bracket($match[1]), $no, FALSE);
	
		$body = join("\n", $source);
		foreach(array('summary', 'name', 'priority', 'state', 'category') as $item) {
			$regex = '/-\s*' . preg_quote($this->root->_plugin_bugtrack[$item], '/') . '\s*:(.*)/';
			if (preg_match($regex, $body, $matches)) {
				if ($item == 'name') {
					$$item = $this->func->strip_bracket(trim($matches[1]));
				} else {
					$$item = trim($matches[1]);
				}
			} else {
					$$item = ''; // Data not found
			}
		}
	
		if (preg_match("/\*([^\n]*)/", $body, $matches)) {
			$summary = $matches[1];
			$this->func->make_heading($summary);
		}
	
		return array($page, $no, $summary, $name, $priority, $state, $category);
	}
}
?>