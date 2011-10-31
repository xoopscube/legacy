<?php
class xpwiki_plugin_ls extends xpwiki_plugin {
	function plugin_ls_init () {


	/*
	 * PukiWiki lsプラグイン
	 *
	 * CopyRight 2002 Y.MASUI GPL2
	 * http://masui.net/pukiwiki/ masui@masui.net
	 *
	 * $Id: ls.inc.php,v 1.6 2009/03/13 08:18:49 nao-pon Exp $
		 */

	}
	
	function plugin_ls_convert()
	{
	//	global $vars;
	
		$with_title = FALSE;
	
		if (func_num_args())
		{
			$args = func_get_args();
			$with_title = in_array('title',$args);
		}
	
		$prefix = $this->cont['PageForRef'].'/';
	
		$pages = array();
		foreach ($this->func->get_existpages(FALSE, $prefix) as $page)
		{
			//if (strpos($page,$prefix) === 0)
			//{
				$pages[] = $page;
			//}
		}
		//natcasesort($pages);
		$this->func->pagesort($pages);
	
		$ls = array();
		foreach ($pages as $page)
		{
			$comment = '';
			if ($with_title)
			{
				list($comment) = $this->func->get_source($page);
				// 見出しの固有ID部を削除
				$comment = preg_replace('/^(\*{1,5}.*)\[#[A-Za-z][\w-]+\](.*)$/','$1$2',$comment);
	
				$comment = '- ' . ereg_replace('^[-*]+','',$comment);
			}
			$ls[] = "-[[$page]] $comment";
		}
	
		return $this->func->convert_html($ls);
	}
}
?>