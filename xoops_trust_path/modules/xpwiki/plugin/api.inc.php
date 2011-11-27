<?php
/*
 * Created on 2007/04/11 by nao-pon http://hypweb.net/
 * $Id: api.inc.php,v 1.11 2011/11/26 12:03:10 nao-pon Exp $
 */

class xpwiki_plugin_api extends xpwiki_plugin {
	function plugin_api_init () {
	}

	function plugin_api_action () {

		$cmd = (isset($this->root->vars['pcmd']))? (string)$this->root->vars['pcmd'] : '';

		switch ($cmd) {
			case 'autolink':
				$this->autolink();
		}

		return array('exit' => 0);

	}

	function autolink ($need_ret = false, $base = null, $options = array()) {
		if (is_null($base)) {
			$base = '';
			$base = (isset($this->root->vars['base']))? (string)$this->root->vars['base'] : '';
		}
		$base = trim($base, '/');

		$cache_key = $base;
		if ($options) {
			$cache_key .= serialize($options);
		}
		$cache = $this->cont['CACHE_DIR'].sha1($cache_key).'.autolink.api';

		if (is_file($cache)) {
			$out = file_get_contents($cache);
		} else {
			$pages = array();
			if (!$base || $this->func->is_page($base)) {
				// Get WHOLE page list (always as guest)
				$options['asguest'] = TRUE;
				$options['nolisting'] = TRUE;
				$pages = $this->func->get_existpages(FALSE, ($base ? ($base . '/') : ''), $options);

				// Get all aliases
				if (empty($options['noaliases'])) {
					$all_aliases = array_keys(array_intersect($this->root->page_aliases,  $this->func->get_existpages()));
				} else {
					$all_aliases = array();
				}
				if ($base) {
					// Extract from all aliases
					foreach($all_aliases as $_aliase) {
						if (strpos($_aliase, $base . '/') === 0) {
							$pages[] = $_aliase;
						}
					}
					// Strip $base
					$pages = array_map(create_function('$page','return substr($page,'.(strlen($base)+1).');'), $pages);
				} else {
					// Merge with all aliases
					$pages = array_merge($pages, $all_aliases);
				}
			}

			if ($pages) {
				//sort($pages, SORT_STRING);
				$out = $this->func->get_matcher_regex_safe($pages);
			} else {
				$out = '(?!)';
			}

			$fp = fopen($cache, 'w');
			fwrite($fp, $out);
			fclose($fp);
		}
		if ($need_ret) {
			return $out;
		} else {
			$this->output($out);
		}
	}

	function output ($str) {
		$this->func->clear_output_buffer();
		header ("Content-Type: text/plain; charset=".$this->cont['CONTENT_CHARSET']);
		header ("Content-Length: ".strlen($str));
		echo $str;
		exit();
	}
}

?>
