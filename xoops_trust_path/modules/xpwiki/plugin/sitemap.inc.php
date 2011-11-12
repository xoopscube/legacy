<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: sitemap.inc.php,v 1.3 2011/07/29 07:14:25 nao-pon Exp $
//
// IndexPages plugin: Show a list of page names
class xpwiki_plugin_sitemap extends xpwiki_plugin {
	function plugin_sitemap_init () {

	}

	function plugin_sitemap_action()
	{
		error_reporting(0);
		$this->getlist();
	}

	// Get a list
	function getlist()
	{
		$options = array(
			'nolisting' => TRUE,
			'withtime'  => TRUE,
			'asguest'   => TRUE
		);

		$pages = $this->func->get_existpages(FALSE, '', $options);

		$items = array();
		$items[] = '<?xml version="1.0" encoding="UTF-8"?>';
		$items[] = '<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">';
		foreach($pages as $page) {
			list($time, $name) = explode("\t", $page);
			$items[] = "<url>";
			$items[] = "<loc>" . htmlspecialchars($this->func->get_page_uri($name, TRUE), ENT_QUOTES) . '</loc>';
			$items[] = "<lastmod>" . gmdate('Y-m-d\TH:i:s+00:00', ($time + $this->cont['LOCALZONE'])) . '</lastmod>';
			//$items[] = "<changefreq></changefreq>";
			$items[] = "</url>";
		}
		$items[] = '</urlset>';
		$xml = join("\n", $items);

		// clear output buffer
		$this->func->clear_output_buffer();

		header('Content-type: application/xml');
		echo($xml);
		exit();
	}
}
?>