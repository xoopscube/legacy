<?php
class xpwiki_plugin_lookup extends xpwiki_plugin {
	function plugin_lookup_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: lookup.inc.php,v 1.4 2010/01/08 13:50:42 nao-pon Exp $
	// Copyright (C)
	//   2002-2005 PukiWiki Developers Team
	//   2001-2002 Originally written by yu-ji
	// License: GPL v2 or (at your option) any later version
	//
	// InterWiki lookup plugin

		$this->cont['PLUGIN_LOOKUP_USAGE'] =  '#lookup(interwikiname[,button_name[,default]])';

	}

	function plugin_lookup_convert()
	{
	//	global $vars;
	//	static $id = 0;
		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;}

		$num = func_num_args();
		if ($num == 0 || $num > 3) return $this->cont['PLUGIN_LOOKUP_USAGE'];

		$args = func_get_args();
		$interwiki = htmlspecialchars(trim($args[0]));
		$button    = isset($args[1]) ? trim($args[1]) : '';
		$button    = ($button != '') ? htmlspecialchars($button) : 'lookup';
		$default   = ($num > 2) ? htmlspecialchars(trim($args[2])) : '';
		$default = str_replace('$uname', $this->cont['USER_NAME_REPLACE'], $default);
		$s_page    = htmlspecialchars($this->root->vars['page']);
		++$id[$this->xpwiki->pid];

		$script = $this->func->get_script_uri();
		$ret = <<<EOD
<form action="$script" method="post">
 <div>
  <input type="hidden" name="plugin" value="lookup" />
  <input type="hidden" name="refer"  value="$s_page" />
  <input type="hidden" name="inter"  value="$interwiki" />
  <label for="_p_lookup_{$id[$this->xpwiki->pid]}">$interwiki:</label>
  <input type="text" name="page" id="_p_lookup_{$id[$this->xpwiki->pid]}" size="30" value="$default" />
  <input type="submit" value="$button" />
 </div>
</form>
EOD;
		return $ret;
	}

	function plugin_lookup_action()
	{
	//	global $post; // Deny GET method to avlid GET loop

		$page  = isset($this->root->post['page'])  ? $this->root->post['page']  : '';
		$inter = isset($this->root->post['inter']) ? $this->root->post['inter'] : '';
		if ($page === '') return FALSE; // Do nothing
		if ($inter === '') return array('msg'=>'Invalid access', 'body'=>'');

		$url = $this->func->get_interwiki_url($inter, $page);
		if ($url === FALSE) {
			$msg = sprintf('InterWikiName "%s" not found', $inter);
			$msg = htmlspecialchars($msg);
			return array('msg'=>'Not found', 'body'=>$msg);
		}

		$this->func->send_location('', '', $url);
	}
}
?>