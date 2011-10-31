<?php
/*
 * Created on 2008/03/18 by nao-pon http://hypweb.net/
 * $Id: capture.inc.php,v 1.2 2008/03/25 02:20:23 nao-pon Exp $
 */

class xpwiki_plugin_capture extends xpwiki_plugin {
	function plugin_capture_convert() {
		
		$options = array();
		$args = func_get_args();
		$this->fetch_options($options, $args, array('name', 'body'));
		
		$body = '';
		if ($options['body']) {
			$body = $this->func->convert_html_multiline($options['body']);
			$this->root->rtf['capture'][$options['name']] = $body;
			return '';
		} else {
			if (isset($this->root->rtf['capture'][$options['name']])) {
				$ret = $this->root->rtf['capture'][$options['name']];
				//unset($this->root->rtf['capture'][$options['name']]);
				return $ret;
			}
		}
	}
}
?>