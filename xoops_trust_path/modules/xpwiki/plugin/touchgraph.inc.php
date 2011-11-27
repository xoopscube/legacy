<?php
class xpwiki_plugin_touchgraph extends xpwiki_plugin {
	function plugin_touchgraph_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: touchgraph.inc.php,v 1.3 2011/11/26 12:03:10 nao-pon Exp $
	//
	// Output an index for 'TouchGraph WikiBrowser'
	// http://www.touchgraph.com/
	//
	// Usage: (Check also TGWikiBrowser's sample)
	//    java -Dfile.encoding=EUC-JP \
	//    -cp TGWikiBrowser.jar;BrowserLauncher.jar com.touchgraph.wikibrowser.TGWikiBrowser \
	//    http://<pukiwiki site>/index.php?plugin=touchgraph \
	//    http://<pukiwiki site>/index.php? FrontPage 2 true
	//
	// Note: -Dfile.encoding=EUC-JP (or UTF-8) may not work with Windows OS
	//   http://www.simeji.com/wiki/pukiwiki.php?Java%A4%CE%CD%AB%DD%B5 (in Japanese)


	function plugin_touchgraph_action()
	{
	//	global $vars;

		$this->func->pkwk_headers_sent();
		header('Content-type: text/plain');
		if (isset($this->root->vars['reverse'])) {
			$this->plugin_touchgraph_ref();
		} else {
			$this->plugin_touchgraph_rel();
		}
		return array('exit' => 0);
	}

	// Normal
	function plugin_touchgraph_rel()
	{
		foreach ($this->func->get_existpages() as $page) {
			if ($this->func->check_non_list($page)) continue;

			$file = $this->cont['CACHE_DIR'] . $this->func->encode($page) . '.rel';
			if (is_file($file)) {
				echo $page;
				$data = file($file);
				foreach(explode("\t", trim($data[0])) as $name) {
					if ($this->func->check_non_list($name)) continue;
					echo ' ', $name;
				}
				echo "\n";
			}
		}
	}

	// Reverse
	function plugin_touchgraph_ref()
	{
		foreach ($this->func->get_existpages() as $page) {
			if ($this->func->check_non_list($page)) continue;

			$file = $this->cont['CACHE_DIR'] . $this->func->encode($page) . '.ref';
			if (is_file($file)) {
				echo $page;
				foreach (file($file) as $line) {
					list($name) = explode("\t", $line);
					if ($this->func->check_non_list($name)) continue;
					echo ' ', $name;
				}
				echo "\n";
			}
		}
	}
}
?>