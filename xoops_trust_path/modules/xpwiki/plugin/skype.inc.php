<?php
/*
 * Created on 2009/06/30 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: skype.inc.php,v 1.1 2009/06/30 23:45:57 nao-pon Exp $
 */

class xpwiki_plugin_skype extends xpwiki_plugin {
	function xpwiki_plugin_skype($func) {
		parent::xpwiki_plugin($func);
		$func->add_js_head('http://download.skype.com/share/skypebuttons/js/skypeCheck.js');
	}
	
	function plugin_skype_init() {
		$this->conf['options'] = array(
			'id'       => '',
			'call'     => TRUE,
			'add'      => FALSE,
			'chat'     => FALSE,
			'userinfo' => FALSE,
			'sendfile' => FALSE,
			'status'   => ''
		);
		
		$this->conf['modes'] = array('add', 'chat', 'userinfo', 'sendfile', 'call'); // last item is default
		$this->conf['statuses'] = array('balloon', 'bigclassic', 'smallclassic', 'mediumicon', 'smallicon'); // last item is default
		
		$this->conf['format'] = '$image<a href="$link" onclick="return skypeCheck();">$alias</a>';
	}
	
	function plugin_skype_inline() {
		$options = $this->conf['options'];
		$args = func_get_args();
		$alias = array_pop($args);
		$this->fetch_options($options, $args, array('id'));
		
		if (!$options['id']) {
			return FALSE;
		} else {
			$id = htmlspecialchars($options['id']);
		}
		
		if (! $alias) {
			$alias = $id;
		}
		
		foreach($this->conf['modes'] as $mode) {
			if (!empty($options[$mode])) {
				break;
			}
		}
		
		if ($options['status']) {
			foreach($this->conf['statuses'] as $status) {
				if ($options['status'] === $status) {
					break;
				}
			}
		} else {
			$status = '';
		}
		
		$image = '';
		if ($status) {
			$image = '<img src="http://mystatus.skype.com/' . $status . '/' . $id . '" />';
		}
		
		$link = 'skype:' . $id . '?' . $mode;
		
		return str_replace(array('$image', '$link', '$alias'), array($image, $link, $alias), $this->conf['format']);
	}
}
?>