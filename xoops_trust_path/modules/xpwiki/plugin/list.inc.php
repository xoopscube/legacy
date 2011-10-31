<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: list.inc.php,v 1.2 2007/02/11 00:11:58 nao-pon Exp $
//
// IndexPages plugin: Show a list of page names
class xpwiki_plugin_list extends xpwiki_plugin {
	function plugin_list_init () {

	}
	
	function plugin_list_action()
	{
		// Redirected from filelist plugin?
		$filelist = (isset($this->root->vars['cmd']) && $this->root->vars['cmd'] == 'filelist');
		
		if ($filelist && !empty($this->root->filelist_only_admin) && !$this->root->userinfo['admin']) {
			return array(
				'msg'  => $this->root->_msg_not_readable,
				'body' => '');			
		} else {
			return array(
				'msg'  => $filelist ? $this->root->_title_filelist : $this->root->_title_list,
				'body' => $this->plugin_list_getlist($filelist));
		}
	}
	
	// Get a list
	function plugin_list_getlist($withfilename = FALSE)
	{
		$pages = array_diff($this->func->get_existpages(), array($this->root->whatsnew));
		if (! $withfilename)
			$pages = array_diff($pages, preg_grep('/' . $this->root->non_list . '/S', $pages));
		if (empty($pages)) return '';
	
		return $this->func->page_list($pages, 'read', $withfilename);
	}
}
?>