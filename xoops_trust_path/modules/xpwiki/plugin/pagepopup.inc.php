<?php
/*
 * Created on 2007/10/05 by nao-pon http://hypweb.net/
 * $Id: pagepopup.inc.php,v 1.5 2009/03/13 08:14:45 nao-pon Exp $
 */

class xpwiki_plugin_pagepopup extends xpwiki_plugin {
	function plugin_pagepopup_init () {
		$this->positions = array(
			'top'    => '',
			'bottom' => '',
			'left'   => '',
			'right'  => '',
			'width'  => '',
			'height' => ''
		);
	}
	
	function can_call_otherdir_inline() {
		return 1;
	}

	function plugin_pagepopup_inline()
	{
		$op = func_get_args();
		
		$alias = array_pop($op);
		
		$page = (isset($op[0]))? $op[0] : $this->root->vars['page'];
		list($page, $anchor) = array_pad(explode('#', $page), 2, '');
		if ($anchor) $anchor = '#' . $anchor;
		$nocheck = (empty($op[1]))? FALSE : TRUE;
		
		if (strpos($page, '$page') !== FALSE) {
			$page = str_replace('$page', $this->root->vars['page'], $page);
		}
		
		if ($nocheck || ($this->func->is_page($page) || isset($this->root->page_aliases[$page]))) {
			if ($nocheck) $options['nocheck'] = TRUE;
			$options['popup']['use'] = 1;
			$options['popup']['position'] = '';
			foreach(array('top', 'left', 'bottom', 'right', 'width', 'height') as $_prm) {
				if (isset($this->positions[$_prm])) {
					if (preg_match('/^(\d+)(%|px)?/', $this->positions[$_prm], $_match)) {
					 	if (empty($_match[2])) $_match[2] = 'px';
					 	$options['popup']['position'] .= ',' . $_prm . ':\'' . $_match[1] . $_match[2] . '\'';
					}
				}
			}
		} else {
			$options = array();
		}
		
		return $this->func->make_pagelink($page, $alias, $anchor, '', 'pagelink', $options);
	}
}
?>