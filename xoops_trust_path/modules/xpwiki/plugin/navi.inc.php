<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: navi.inc.php,v 1.12 2009/06/26 00:40:01 nao-pon Exp $
//
// Navi plugin: Show DocBook-like navigation bar and contents

/*
 * Usage:
 *   #navi(contents-page-name)   <for ALL child pages>
 *   #navi([contents-page-name][,reverse]) <for contents page>
 *
 * Parameter:
 *   contents-page-name - Page name of home of the navigation (default:itself)
 *   reverse            - Show contents revese
 *
 * Behaviour at contents page:
 *   Always show child-page list like 'ls' plugin
 *
 * Behaviour at child pages:
 *
 *   The first plugin call - Show a navigation bar like a DocBook header
 *
 *     Prev  <contents-page-name>  Next
 *     --------------------------------
 *
 *   The second call - Show a navigation bar like a DocBook footer
 *
 *     --------------------------------
 *     Prev          Home          Next
 *     <pagename>     Up     <pagename>
 *
 * Page-construction example:
 *   foobar    - Contents page, includes '#navi' or '#navi(foobar)'
 *   foobar/1  - One of child pages, includes one or two '#navi(foobar)'
 *   foobar/2  - One of child pages, includes one or two '#navi(foobar)'
 */

class xpwiki_plugin_navi extends xpwiki_plugin {
	function plugin_navi_init () {
	
		// Exclusive regex pattern of child pages
		$this->config['EXCLUSIVE_REGEX'] =  '';
		//$this->config['EXCLUSIVE_REGEX'] = '#/_#'; // Ignore 'foobar/_memo' etc.
	
		// Insert <link rel=... /> tags into XHTML <head></head>
		$this->config['LINK_TAGS'] =  FALSE;	// FALSE, TRUE
		
		$this->options = array(
			'home'    => '',
			'reverse' => FALSE,
			'level'   => FALSE,
			'nolevel' => FALSE,
		);
	}
	
	function plugin_navi_convert()
	{
		// インクルードされている場合は無効にする
		if ($this->root->rtf['convert_nest'] > 1) return '';
	
		static $navi = array();
		if (!isset($navi[$this->xpwiki->pid])) {$navi[$this->xpwiki->pid] = array();}
	
		$options = $this->options;
		$args = func_get_args();
		
		if ($args) {
			$this->fetch_options($options, $args, array('home'));
		} else {
			$options['level'] = TRUE;
		}
		if ($options['nolevel']) $options['level'] = FALSE;
		
		$current = $this->root->vars['page'];

		$set_home = FALSE;
		$home_default = FALSE;
		if ($options['home']) {
			$home = $this->func->get_fullname($this->func->strip_bracket($options['home']), $current);
			$is_home = ($home === $current);
			if (! $is_home &&
			    ! preg_match('/^' . preg_quote($home, '/') . '/', $current)) {
				return '#navi(' . htmlspecialchars($home) .
				'): Not a child page like: ' .
				htmlspecialchars($home . '/' . $this->func->basename($current)) .
				'<br />';
			}
			$set_home = TRUE;
		} else {
			$is_home = FALSE;
			if (strpos($current, '/') === FALSE) {
				$home = $this->root->defaultpage;
				$home_default = TRUE;
			} else {
				$home = $this->func->page_dirname($current);
			}
		}
		
		if (! $this->func->is_page($home)) {
			return '#navi(contents-page-name): No such page: ' .
			htmlspecialchars($home) . '<br />';
		}		
		
		$key = $home_default? '/' : $home;
		$pages  = array();
		$footer = isset($navi[$this->xpwiki->pid][$key]); // The first time: FALSE, the second: TRUE
		if (! $footer) {
			$navi[$this->xpwiki->pid][$key] = array(
				'up'   =>'',
				'down' =>'',
				'down1'=>'',
				'prev' =>'',
				'prev1'=>'',
				'next' =>'',
				'next1'=>'',
				'home' =>'',
				'home1'=>'',
			);
	
			$pages_op = array();
			$down = '';
			if ($options['level']) {
				$pages_op['nochild'] = TRUE;
				$childlen = array_values($this->func->get_existpages(FALSE, $current . '/'));
				if ($childlen) {
					if ($this->config['EXCLUSIVE_REGEX'] != '') {
						// If old PHP could use preg_grep(,,PREG_GREP_INVERT)...
						$childlen = array_diff($childlen,
						preg_grep($this->config['EXCLUSIVE_REGEX'], $childlen));
					}
					if ($childlen) {
						$this->func->pagesort($childlen);
						$down = $childlen[0];
					}
				}
			}
			
			//$pages = preg_grep('/^' . preg_quote($home, '/') .
			//'($|\/)/', $this->func->get_existpages());
			$pages = $this->func->get_existpages(FALSE, ($home_default? '' : $home . '/'), $pages_op);
			if ($this->config['EXCLUSIVE_REGEX'] != '') {
				// If old PHP could use preg_grep(,,PREG_GREP_INVERT)...
				$pages = array_diff($pages,
				preg_grep($this->config['EXCLUSIVE_REGEX'], $pages));
			}
			$pages[] = $current; // Sentinel :)
			$pages   = array_unique($pages);
			//natcasesort($pages);
			$this->func->pagesort($pages);
			if ($options['reverse']) $pages = array_reverse($pages);
	
			$prev = $home;
			foreach ($pages as $page) {
				if ($page === $current) break;
				$prev = $page;
			}
			$next = current($pages);
	
			$pos = strrpos($current, '/');
			$up = '';
			if ($pos > 0) {
				$up = substr($current, 0, $pos);
				if (!$set_home || $home !== $up) {
					$navi[$this->xpwiki->pid][$key]['up']    =  ($set_home? '<br />' : '') . $this->func->make_pagelink($up, $this->root->_navi_up);
				}
			}
			if ($down) {
				$navi[$this->xpwiki->pid][$key]['down'] = '<br />' . $this->func->make_pagelink($down, '#compact:' . $current);
				$navi[$this->xpwiki->pid][$key]['down1']  = '<br />' . $this->func->make_pagelink($down, 'Down');
			}
			if (! $is_home) {
				$navi[$this->xpwiki->pid][$key]['prev']  = $this->func->make_pagelink($prev, '#compact:' . $home);
				$navi[$this->xpwiki->pid][$key]['prev1'] = $this->func->make_pagelink($prev, $this->root->_navi_prev);
				$this->func->add_tag_head('<link rel="prev" title="' . htmlspecialchars($prev) . '" href="' . $this->func->get_page_uri($prev, TRUE) . '" />');
			}
			if ($next != '') {
				$navi[$this->xpwiki->pid][$key]['next']  = $this->func->make_pagelink($next, '#compact:' . $home);
				$navi[$this->xpwiki->pid][$key]['next1'] = $this->func->make_pagelink($next, $this->root->_navi_next);
				$this->func->add_tag_head('<link rel="next" title="' . htmlspecialchars($next) . '" href="' . $this->func->get_page_uri($next, TRUE) . '" />');
			}
			$navi[$this->xpwiki->pid][$key]['home']  = $this->func->make_pagelink($home);
			if ($set_home) {
				$navi[$this->xpwiki->pid][$key]['home1'] = $this->func->make_pagelink($home, $this->root->_navi_home);
				$this->func->add_tag_head('<link rel="start" title="' . htmlspecialchars($home) . '" href="' . $this->func->get_page_uri($home, TRUE) . '" />');
			}
			
			// Generate <link> tag: start next prev(previous) parent(up)
			// Not implemented: contents(toc) search first(begin) last(end)
			if ($this->config['LINK_TAGS']) {
				foreach (array('start'=>$home, 'next'=>$next,
			    'prev'=>$prev, 'up'=>$up) as $rel=>$_page) {
					if ($_page !== '') {
						$s_page = htmlspecialchars($_page);
						$r_page = rawurlencode($_page);
						$this->root->head_tags[] = ' <link rel="' .
						$rel . '" href="' . $this->root->script .
						'?' . $r_page . '" title="' .
						$s_page . '" />';
					}
				}
			}
		}
	
		$ret = '';
	
		if ($is_home) {
			// Show contents
			$count = count($pages);
			if ($count == 0) {
				return '#navi(contents-page-name): You already view the result<br />';
			} else if ($count == 1) {
				// Sentinel only: Show usage and warning
				$home = htmlspecialchars($home);
				$ret .= '#navi(' . $home . '): No child page like: ' .
				$home . '/Foo';
			} else {
				$ret .= '<ul>';
				foreach ($pages as $page)
					if ($page !== $home)
						$ret .= ' <li>' . $this->func->make_pagelink($page) . '</li>';
				$ret .= '</ul>';
			}
	
		} else if ($this->cont['UA_PROFILE'] === 'keitai') {
			$ret = <<<EOD
<p>
((e:f75b)){$navi[$this->xpwiki->pid][$key]['home']}<br />
&nbsp;((e:f76f)){$navi[$this->xpwiki->pid][$key]['prev']}<br />
&nbsp;((e:f76e)){$navi[$this->xpwiki->pid][$key]['next']}
</p>
<hr class="full_hr" />
EOD;
		} else if (! $footer) {
			// Header
			$ret = <<<EOD
<ul class="navi">
 <li class="navi_left">{$navi[$this->xpwiki->pid][$key]['prev1']}</li>
 <li class="navi_right">{$navi[$this->xpwiki->pid][$key]['next1']}</li>
 <li class="navi_none">{$navi[$this->xpwiki->pid][$key]['home']}{$navi[$this->xpwiki->pid][$key]['down']}</li>
</ul>
<hr class="full_hr" />
EOD;
		} else {
			// Footer
			$ret = <<<EOD
<hr class="full_hr" />
<ul class="navi">
 <li class="navi_left">{$navi[$this->xpwiki->pid][$key]['prev1']}<br />{$navi[$this->xpwiki->pid][$key]['prev']}</li>
 <li class="navi_right">{$navi[$this->xpwiki->pid][$key]['next1']}<br />{$navi[$this->xpwiki->pid][$key]['next']}</li>
 <li class="navi_none">{$navi[$this->xpwiki->pid][$key]['home1']}{$navi[$this->xpwiki->pid][$key]['up']}{$navi[$this->xpwiki->pid][$key]['down1']}</li>
</ul>
<div style="clear:both;height:0px;"> </div>
EOD;
		}
		return $ret;
	}
}
?>