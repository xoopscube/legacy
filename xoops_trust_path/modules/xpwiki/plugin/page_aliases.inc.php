<?php
/*
 * Created on 2007/08/30 by nao-pon http://hypweb.net/
 * $Id: page_aliases.inc.php,v 1.4 2011/09/17 07:32:44 nao-pon Exp $
 */

class xpwiki_plugin_page_aliases extends xpwiki_plugin {

	function plugin_page_aliases_action() {
		return array('msg'=>'Page aliases list', 'body'=>$this->get());
	}

	function can_call_otherdir_convert() {
		return 1;
	}

	function can_call_otherdir_inline() {
		return 1;
	}

	function plugin_page_aliases_convert() {
		return $this->get();
	}

	function plugin_page_aliases_inline() {

		$arg = func_get_args();

		// get body "{...}"
		$body = array_pop($arg);

		$page = '';
		if (isset($arg[0])) {
			$page = $arg[0];
		}
		if (!$page || $this->func->is_page($page)) {
			$page = $this->root->vars['page'];
		}

		$pagealiases = $this->func->get_page_alias($page, true, false, 'relative');

		return $pagealiases? join(', ', $pagealiases) : $this->root->_LANG['skin']['none'];

	}

	function get() {
		$result = array_intersect($this->root->page_aliases, $this->func->get_existpages());
		//$result = $this->root->page_aliases;
		$ret = "- Page aliases list\n";
		foreach($result as $_alias => $_page) {
			$ret .= "-- [[{$_alias}]] &#187; [[{$_page}]]\n";
		}
//		$ret = "|Alias|Page|h\n";
//		foreach($this->root->page_aliases as $_alias => $_page) {
//			$ret .= "|[[{$_alias}]]|[[{$_page}]]|\n";
//		}
		return $this->func->convert_html($ret);
	}
}
?>