<?php
/*
 * Created on 2008/08/23 by nao-pon http://hypweb.net/
 * $Id: temp.inc.php,v 1.1 2008/08/30 06:33:22 nao-pon Exp $
 */

class xpwiki_plugin_temp extends xpwiki_plugin {
	function plugin_temp_init() {
		$this->config['templates'] = array();
	}
	
	function plugin_temp_convert() {
		$options = array();
		$count = func_num_args();
		
		if (! $count) return '';
		$args = func_get_args();
		$name = array_shift($args);
		if ($args && strpos($args[$count - 2], "\r") !== FALSE) {
			$body = array_pop($args);
		}
		if ($body) {
			$this->config['templates'][$name] = $body;
		} else {
			if (isset($this->config['templates'][$name])) {
				$body = $this->config['templates'][$name];
				krsort($args);
				$i = count($args);
				foreach ($args as $arg) {
					$body = str_replace('$' . $i--, $arg, $body);
				}
				return $this->func->convert_html_multiline($body);
			}
		}
		return '';
	}
}
