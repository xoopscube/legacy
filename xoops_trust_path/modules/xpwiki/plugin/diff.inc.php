<?php
class xpwiki_plugin_diff extends xpwiki_plugin {
	function plugin_diff_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: diff.inc.php,v 1.6 2011/11/26 12:03:10 nao-pon Exp $
	// Copyright (C)
	//   2002-2005 PukiWiki Developers Team
	//   2002      Originally written by yu-ji
	// License: GPL v2 or (at your option) any later version
	//
	// Showing colored-diff plugin
	
	function plugin_diff_action()
	{
	//	global $vars;
	
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$this->func->check_readable($page, true, true);
	
		$action = isset($this->root->vars['action']) ? $this->root->vars['action'] : '';
		switch ($action) {
			case 'delete': $retval = $this->plugin_diff_delete($page);	break;
			default:       $retval = $this->plugin_diff_view($page);	break;
		}
		return $retval;
	}
	
	function plugin_diff_view($page)
	{
	//	global $script, $hr;
	//	global $_msg_notfound, $_msg_goto, $_msg_deleted, $_msg_addline, $_msg_delline, $_title_diff;
	//	global $_title_diff_delete;
	
		$r_page = rawurlencode($page);
		$s_page = htmlspecialchars($page);
		$lasteditor = $this->func->get_lasteditor($this->func->get_pginfo($page));
		$menu = array(
			'<li>' . $this->root->_LANG['skin']['recent'] . ': ' . $lasteditor . '</li>',
			'<li>' . $this->root->_msg_addline . '</li>',
			'<li>' . $this->root->_msg_delline . '</li>'
			);
	
		$is_page = $this->func->is_page($page);
		if ($is_page) {
			$menu[] = ' <li>' . str_replace('$1', '<a href="' . $this->root->script . '?' . $r_page . '">' .
			$s_page . '</a>', $this->root->_msg_goto) . '</li>';
		} else {
			$menu[] = ' <li>' . str_replace('$1', $s_page, $this->root->_msg_deleted) . '</li>';
		}
	
		$filename = $this->cont['DIFF_DIR'] . $this->func->encode($page) . '.txt';
		if (is_file($filename)) {
			if (! $this->cont['PKWK_READONLY']) {
				$menu[] = '<li><a href="' . $this->root->script . '?cmd=diff&amp;action=delete&amp;page=' .
				$r_page . '">' . str_replace('$1', $s_page, $this->root->_title_diff_delete) . '</a></li>';
			}
			$msg = '<pre>' . $this->func->diff_style_to_css(htmlspecialchars(file_get_contents($filename))) . '</pre>' . "\n";
		} else if ($is_page) {
			$diffdata = trim(htmlspecialchars($this->func->get_source($page, TRUE, TRUE)));
			$msg = '<pre><span class="diff_added">' . $diffdata . '</span></pre>' . "\n";
		} else {
			return array('msg'=>$this->root->_title_diff, 'body'=>$this->root->_msg_notfound);
		}
	
		$menu = join("\n", $menu);
		$body = <<<EOD
<ul>
$menu
</ul>
{$this->root->hr}
EOD;
	
		return array('msg'=>$this->root->_title_diff, 'body'=>$body . $msg);
	}
	
	function plugin_diff_delete($page)
	{
	//	global $script, $vars;
	//	global $_title_diff_delete, $_msg_diff_deleted;
	//	global $_msg_diff_adminpass, $_btn_delete, $_msg_invalidpass;
	
		$filename = $this->cont['DIFF_DIR'] . $this->func->encode($page) . '.txt';
		$body = '';
		if (! $this->func->is_pagename($page))     $body = 'Invalid page name';
		if (! is_file($filename)) $body = $this->func->make_pagelink($page) . '\'s diff seems not found';
		if ($body) return array('msg'=>$this->root->_title_diff_delete, 'body'=>$body);
	
		if (isset($this->root->vars['pass'])) {
			if ($this->func->pkwk_login($this->root->vars['pass'])) {
				unlink($filename);
				return array(
					'msg'  => $this->root->_title_diff_delete,
				'body' => str_replace('$1', $this->func->make_pagelink($page), $this->root->_msg_diff_deleted)
				);
			} else {
				$body .= '<p><strong>' . $this->root->_msg_invalidpass . '</strong></p>' . "\n";
			}
		}
	
		$s_page = htmlspecialchars($page);
		$script = $this->func->get_script_uri();
		$body .= <<<EOD
<p>{$this->root->_msg_diff_adminpass}</p>
<form action="{$script}" method="post">
 <div>
  <input type="hidden"   name="cmd"    value="diff" />
  <input type="hidden"   name="page"   value="$s_page" />
  <input type="hidden"   name="action" value="delete" />
  <input type="password" name="pass"   size="12" />
  <input type="submit"   name="ok"     value="{$this->root->_btn_delete}" />
 </div>
</form>
EOD;
	
		return array('msg'=>$this->root->_title_diff_delete, 'body'=>$body);
	}
}
?>