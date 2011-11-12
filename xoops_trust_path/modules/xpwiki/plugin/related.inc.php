<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: related.inc.php,v 1.8 2009/05/02 04:17:20 nao-pon Exp $
//
// Related plugin: Show Backlinks for the page

// #related([<Max count>[,nopassage][,notitle][,context][,context:<Max bytes>/<Max Parts>][,separate][,highlight]])

class xpwiki_plugin_related extends xpwiki_plugin {
	function plugin_related_init () {

		$this->config['showContextAction'] = TRUE;
		$this->config['showMaxAction'] = 100;
		$this->config['showMaxConvert'] = 100;

	}
	
	function plugin_related_convert()
	{
		if (! empty($this->root->rtf['is_init'])) {
			return FALSE;
		}
		
		$options = array(
			'max' => $this->config['showMaxConvert'],
			'backlink' => TRUE,
			'nopassage' => FALSE,
			'notitle' => FALSE,
			'context' => '',
			'separate' => FALSE,
			'highlight' => FALSE,
		);

		if (func_num_args()) {
			$args  = func_get_args();
			$this->fetch_options($options, $args, array('max'));
		}
		
		$options['backlink'] = TRUE;
		
		if ($options['separate']) {
			$options['delimiter'] = "\x08";
		}
		
		if ($options['max'] < 1) {
			$options['max'] = $this->config['showMaxConvert'];
		}
		
		$ret = $this->func->make_related($this->cont['PageForRef'], 'p', $options['max'], $options);
		
		if ($options['separate']) {
			$ret = str_replace(array(">\x08", "\x08<", "\x08"), array('>...', '...<', '...</div><div class="context">...'), $ret);
		}
		return $ret;
	}
	
	// Show Backlinks: via related caches for the page
	function plugin_related_action()
	{
	//	global $vars, $script, $defaultpage, $whatsnew;
	
		$_page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$showcontent = isset($this->root->vars['context']) ? (bool)$this->root->vars['context'] : $this->config['showContextAction'];
		//$pnum = isset($this->root->vars['pnum']) ? max(intval($this->root->vars['pnum']), 1) : 1;
		//$start = ($pnum - 1) * $this->config['showMaxAction'];
		$start = isset($this->root->vars['start']) ? max(intval($this->root->vars['start']), 0) : 0;
		
		if ($_page === '') $_page = $this->root->defaultpage;
	
		// Get related from cache
		$count = $this->func->links_count_related_db($_page);
		$max = ($count > $this->config['showMaxAction'])? $this->config['showMaxAction'] : 0;
		
		if ($max) {
			$pnavObj = $this->func->getPageNav($count, $max, $start);
			$pnavObj->url = '?plugin=related&amp;page=' . rawurlencode($_page) . '&amp;start=';
			$pnav = '<div class="pagenav">Total: ' . $count . ' pages&nbsp;&nbsp;' . $pnavObj->renderNav() . '</div>';
		} else {
			$pnav = ($count > 3)? '<div class="pagenav">Total: ' . number_format($count) . ' pages</div>' : '';
		}
		
		$data = $this->func->links_get_related_db($_page, $start, $max);
		if (! empty($data)) {
			// Hide by array keys (not values)
			foreach(array_keys($data) as $page)
				if (
					$page === $this->root->whatsnew
					|| ! $this->func->is_page($page)
					|| $this->func->check_non_list($page)
				) {
					unset($data[$page]);
				}
		}
	
		// Result
		$s_word = htmlspecialchars($_page);
		$msg = 'Backlinks for: ' . $s_word;
		$retval = $this->func->make_pagelink($_page, 'Return to ' . $s_word) . '<br />'. "\n";
		
		$retval .= $pnav;
		
		if (empty($data)) {
			$retval .= '<ul><li>No related pages found.</li></ul>' . "\n";	
		} else {
			// Show count($data)?
			ksort($data);
			$retval .= '<ul>' . "\n";
			foreach ($data as $page=>$time) {
				$context = '';
				$passage = ' <small>' . $this->func->get_passage($time) . '</small>';
				$title = $this->func->get_heading($page);
				if ($title) {
					$title = ' [ ' . $title . ' ]';
				}
				if ($showcontent) {
					$words = array_unique(array_merge(array($_page, $this->func->basename($_page)), $this->func->get_page_alias($_page, TRUE)));
					$context = $this->func->get_page_context($page, $words);
					// for highlight
					$this->root->vars['word'] = join(' ',$words);
				}
				$retval .= ' <li>' . $this->func->make_pagelink($page, $page) . $passage . $title . $context . '</li>' . "\n";
			}
			$retval .= '</ul>' . "\n";
		}
		$retval .= $pnav;
		return array('msg'=>$msg, 'body'=>$retval);
	}
}
?>