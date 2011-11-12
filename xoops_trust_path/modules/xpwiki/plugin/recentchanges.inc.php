<?php
//
// Created on 2006/11/19 by nao-pon http://hypweb.net/
// $Id: recentchanges.inc.php,v 1.15 2009/09/01 03:04:41 nao-pon Exp $
//
class xpwiki_plugin_recentchanges extends xpwiki_plugin {
	
	var $show_recent;
	
	function plugin_recentchanges_init () {
		// 直近追加された部分を表示する
		$this->show_recent = TRUE;
		// そのフォーマット
		$this->show_recent_format = '<div class="recent_add">$1</div>';
		
		// Add CSS
		$this->func->add_tag_head('recentchanges.css');
	}
	
	function plugin_recentchanges_action()
	{
		$res = $this->func->get_existpages(FALSE, '', array('limit' => $this->root->maxshow, 'order' => ' ORDER BY editedtime DESC', 'withtime' => TRUE, 'nolisting' => TRUE));
		
		if ($res)
		{
			$date = $items = "";
			$cnt = 0;
			$items = '<ol class="list1">';
			//while(list($page, $editedtime) = $this->xpwiki->db->fetchRow($res))
			foreach($res as $_res)
			{
				list($editedtime, $page) = explode("\t", $_res);
				$lastmod = $this->func->format_date($editedtime);
				//$tb_tag = ($this->root->trackback)? "<a href=\"$script?plugin=tb&amp;__mode=view&amp;tb_id=".tb_get_id($data[1])."\" title=\"TrackBack\">TB(".$this->func->tb_count($data[1]).")</a> - " : "";
				$tb_tag = '';
				$lasteditor = $this->func->get_lasteditor($this->func->get_pginfo($page));
				if ($lasteditor) $lasteditor = ' <small>by '.$lasteditor.'</small>';
				$items .= '<li style="clear:both;">'.$this->func->make_pagelink($page).' '.$this->func->get_pg_passage($page).$tb_tag;
				$items .= '<ul class="list2"><li>'.$lastmod.$lasteditor.' <a href="'.$this->root->script.'?cmd=backup&amp;page='.rawurlencode($page).'&amp;action=diff" title="'.htmlspecialchars($this->root->_msg_diff).'"><img src="'.$this->cont['LOADER_URL'].'?src=compare_diff.png" alt="'.htmlspecialchars($this->root->_msg_diff).'" width="16" height="16"></a>';
				$added = $this->func->get_page_changes($page);
				if ($this->show_recent && $added) {
					list($added) = explode('&#182;<!--ADD_TEXT_SEP-->',$added);
					$added = $this->func->drop_submit($added);
					$added = preg_replace('/<a[^>]+>(.+?)<\/a>/', '$1', $added);
					$items .= str_replace('$1', $added, $this->show_recent_format);
				}
				$items .="</li></ul></li>\n";
			}
			$items .= '</ol>';
	
		}

		$ret['msg'] = $this->root->whatsnew." Last {$this->root->maxshow}";
		$ret['body'] = $items;
		return $ret;
	}
}
?>