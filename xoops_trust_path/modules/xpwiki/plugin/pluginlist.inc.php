<?php
/*
 * Created on 2008/03/25 by nao-pon http://hypweb.net/
 * $Id: pluginlist.inc.php,v 1.9 2011/07/29 07:14:25 nao-pon Exp $
 */

class xpwiki_plugin_pluginlist extends xpwiki_plugin {
	function plugin_pluginlist_init () {
		$this->load_language();
		$this->config['disabled'] = array(
			'autolink',
			'ajaxtree',
			'aname',
			'bugtrack',
			'bugtrack_list',
			'br',
			'calendar',
			'calendar_edit',
			'calendar_read',
			'color',
			'csv2newpage',
			'build_js',
			'easylogin',
			'endregion',
			'fckxpwikiver',
			'font',
			'freeze',
			'hr',
			'includesubmenu',
			'googlemaps2_draw',
			'googlemaps2_icon',
			'googlemaps2_mark',
			'hypcommonver',
			'insert',
			'img',
			'ls',
			'menu',
			'page_aliases',
			'paint',
			'ping',
			'pluginlist',
			'ref',
			'renderattach',
			'replacer',
			'server',
			'setlinebreak',
			'size',
			'stationary',
			'sub',
			'sup',
			'topicpath',
			'version',
			'versionlist',
			'vote2',
			'webthumbnail',
			'xoopsadmin',
			'xpwikiver',
		);
	}

	function plugin_pluginlist_action () {
		$cacheKey = 'pluginsJSON_' . $this->cont['UI_LANG'];

		if (isset($this->root->vars['clearcache'])) {
			$this->func->cache_del_db($cacheKey, 'pluginlist');
		}

		$out = $this->func->cache_get_db($cacheKey, 'pluginlist');

		if (!$out) {
			list($plugins, $blocks, $inlines, $cmds) = $this->get_plugins();
			$out = '{';
			foreach ($plugins as $plugin) {
				if (! in_array($plugin, $this->config['disabled']) && (in_array($plugin, $blocks) || in_array($plugin, $inlines))) {
					$block_usage = (in_array($plugin, $blocks))? '"' . $this->json_encode($this->add_otherDir($plugin, 'convert', @ $this->msg[$plugin]['block_usage'])) . '"' : 'false';
					$inline_usage = (in_array($plugin, $inlines))? '"' . $this->json_encode($this->add_otherDir($plugin, 'inline', @ $this->msg[$plugin]['inline_usage']))  . '"' : 'false';
					$out .= '"' . $this->json_encode($plugin) . '":{"title":"' . $this->json_encode(@ $this->msg[$plugin]['title']) . '","block_usage":' . $block_usage . ',"inline_usage":' . $inline_usage . '},';
				}
			}
			$out = rtrim($out, ',');
			$out .= '}';
			$this->func->cache_save_db($out, 'pluginlist', 86400, $cacheKey);
		}

		// clear output buffer
		$this->func->clear_output_buffer();
		header('Content-Type: application/x-javascript; charset=utf-8');
		header('Content-Length: ' . strlen($out));
		echo $out;
		exit();
	}

	function json_encode ($str) {
		$str = preg_replace('/(\x22|\x2F|\x5C)/', '\\\$1', $str);
		$str = str_replace(array("\x00","\x08","\x09","\x0A","\x0C","\x0D"), array('','\b','\t','\n','\f','\r'), $str);
		if ($this->cont['SOURCE_ENCODING'] !== 'UTF-8') {
			$str = mb_convert_encoding($str, 'UTF-8', $this->cont['SOURCE_ENCODING']);
		}
		return $str;
	}

	function add_otherDir ($name, $mode, $usage) {
		if ($usage) {
			$plugin = & $this->func->get_plugin_instance($name);
			$func = 'can_call_otherdir_'. $mode;
			if ($num = $plugin->$func()) {
				if ($num > 1) {
					$reg = '/^((?:[^,]+,){'. ($num - 1) .'}\[*)(.+)$/s';
				} else {
					$reg = '/^([^\(]+\(\[*)(.+)$/s';
				}
				$usage = preg_replace($reg, '$1[<'.$this->msg['dirname'].'>:]$2', $usage);
			}
		}
		return $usage;
	}

	function plugin_pluginlist_convert () {
		return $this->build_list();
	}

	function build_list () {
		list($plugins, $blocks, $inlines, $cmds) = $this->get_plugins();
		$html = '<h4>Block plugins</h4><ul><li>#';
		$html .= join('</li><li>#', $blocks);
		$html .= '</li></ul><hr /><h4>Inline plugins</h4><ul><li>&amp;';
		$html .= join(';</li><li>&amp;', $inlines);
		$html .= ';</li></ul><hr /><h4>Command plugins</h4><ul><li>';
		$html .= join('</li><li>', $cmds);
		$html .= '</li></ul>';
		return $html;
	}

	function get_plugins ($sort = true) {
		$plugins = array();
		if ($dh = opendir($this->root->mytrustdirpath . '/plugin/')) {
			while (($file = readdir($dh)) !== false) {
				if (preg_match('/^([a-z_0-9-]+)\.inc\.php$/i', $file, $match)) {
					$plugins[] = $match[1];
				}
			}
			closedir($dh);
		}
		$cmds = $inlines = $blocks = array();
		foreach($plugins as $name) {
			$checks[] = $name;
			if ($this->func->exist_plugin_convert($name)) {
				$blocks[] = $name;
			}
			if ($this->func->exist_plugin_inline($name)) {
				$inlines[] = $name;
			}
			if ($this->func->exist_plugin_action($name)) {
				$cmds[] = $name;
			}
		}
		if ($sort) {
			sort($plugins);
			sort($blocks);
			sort($inlines);
			sort($cmds);
		}
		return array($plugins, $blocks, $inlines, $cmds);
	}
}
?>