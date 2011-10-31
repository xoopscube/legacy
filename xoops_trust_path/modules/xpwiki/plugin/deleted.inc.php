<?php
class xpwiki_plugin_deleted extends xpwiki_plugin {
	function plugin_deleted_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: deleted.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Show deleted (= Exists in BACKUP_DIR or DIFF_DIR but not in DATA_DIR)
	// page list to clean them up
	//
	// Usage:
	//   index.php?plugin=deleted[&file=on]
	//   index.php?plugin=deleted&dir=diff[&file=on]
	
	function plugin_deleted_action()
	{
	//	global $vars;
	//	global $_deleted_plugin_title, $_deleted_plugin_title_withfilename;
	
		$dir = isset($this->root->vars['dir']) ? $this->root->vars['dir'] : 'backup';
		$withfilename  = isset($this->root->vars['file']);
	
		$_DIR['diff'  ]['dir'] = $this->cont['DIFF_DIR'];
		$_DIR['diff'  ]['ext'] = '.txt';
		$_DIR['backup']['dir'] = $this->cont['BACKUP_DIR'];
		$_DIR['backup']['ext'] = $this->cont['BACKUP_EXT']; // .gz or .txt
		//$_DIR['cache' ]['dir'] = CACHE_DIR; // No way to delete them via web browser now
		//$_DIR['cache' ]['ext'] = '.ref';
		//$_DIR['cache' ]['ext'] = '.rel';
	
		if (! isset($_DIR[$dir]))
			return array('msg'=>'Deleted plugin', 'body'=>'No such setting: Choose backup or diff');
	
		$deleted_pages  = array_diff(
			$this->func->get_existpages($_DIR[$dir]['dir'], $_DIR[$dir]['ext']),
		$this->func->get_existpages());
	
		if ($withfilename) {
			$retval['msg'] = $this->root->_deleted_plugin_title_withfilename;
		} else {
			$retval['msg'] = $this->root->_deleted_plugin_title;
		}
		$retval['body'] = $this->func->page_list($deleted_pages, $dir, $withfilename);
	
		return $retval;
	}
}
?>