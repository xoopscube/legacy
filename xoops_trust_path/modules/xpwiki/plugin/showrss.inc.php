<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: showrss.inc.php,v 1.16 2011/12/18 00:31:38 nao-pon Exp $
//  Id:showrss.inc.php,v 1.40 2003/03/18 11:52:58 hiro Exp
// Copyright (C):
//     2002-2006 PukiWiki Developers Team
//     2002      PANDA <panda@arino.jp>
//     (Original)hiro_do3ob@yahoo.co.jp
// License: GPL, same as PukiWiki
//
// Show RSS (of remote site) plugin
// NOTE:
//    * This plugin needs 'PHP xml extension'
//    * Cache data will be stored as CACHE_DIR/*.tmp

class xpwiki_plugin_showrss extends xpwiki_plugin {
	function plugin_showrss_init () {

		$this->conf['PLUGIN_SHOWRSS_USAGE'] =  '#showrss(URI-to-RSS[,default|menubar|recent[,Cache-lifetime[,Show-timestamp]]])';
		$this->conf['max'] = 10;
		$this->conf['allow_html_urls'] = array(
			// URL of which it is effective is html is judged from an agreement forward.
			// 'http://...',
		);
	}

	// Show related extensions are found or not
	function plugin_showrss_action()
	{
		if ($this->cont['PKWK_SAFE_MODE']) $this->func->die_message('PKWK_SAFE_MODE prohibit this');

		$body = '';
		foreach(array('xml') as $extension){
			$$extension = extension_loaded($extension) ?
				'&color(green){Found};' :
				'&color(red){Not found};';
			$body .= '| ' . $extension . ' extension | ' . $$extension . ' |' . "\n";
		}
		return array('msg' => 'showrss_info', 'body' => $this->func->convert_html($body));
	}

	function plugin_showrss_convert()
	{
		$this->func->add_tag_head('showrss.css');

		static $_xml;
		if (! isset ($_xml)) $_xml = extension_loaded('xml');
		if (! $_xml) return '#showrss: xml extension is not found<br />' . "\n";

		$num = func_num_args();
		if ($num == 0) return $this->conf['PLUGIN_SHOWRSS_USAGE'] . '<br />' . "\n";

		$argv = func_get_args();
		$timestamp = FALSE;
		$cachehour = 0;
		$show_description = $template = $uri = '';
		$max = $this->conf['max'];
		switch ($num) {
		case 6: $max       = intval(trim($argv[5]));
		case 5: $show_description = strtolower(trim($argv[4]));
		case 4: $timestamp = (trim($argv[3]) == '1');   /*FALLTHROUGH*/
		case 3: $cachehour = floatval(trim($argv[2]));    /*FALLTHROUGH*/
		case 2: $template  = strtolower(trim($argv[1]));/*FALLTHROUGH*/
		case 1: $uri       = trim($argv[0]);
		}

		$class = ($template == '' || $template == 'default') ? 'XpWikiShowRSS_html' : 'XpWikiShowRSS_html_' . $template;
		if (! is_numeric($cachehour))
			return '#showrss: Cache-lifetime seems not numeric: ' . htmlspecialchars($cachehour) . '<br />' . "\n";
		if (! XC_CLASS_EXISTS($class))
			return '#showrss: Template not found: ' . htmlspecialchars($template) . '<br />' . "\n";
		if (! $this->func->is_url($uri))
			return '#showrss: Seems not URI: ' . htmlspecialchars($uri) . '<br />' . "\n";

		$cachehour = max(0.016, $cachehour); // 最低1分はキャッシュ

		list($rss, $time) = $this->plugin_showrss_get_rss($uri, $cachehour);
		if ($rss === FALSE) return '#showrss: Failed fetching RSS from the server<br />' . "\n";

		$time_str = '';
		if ($timestamp > 0) {
			$time_str = '<p style="font-size:10px; font-weight:bold">Last-Modified:' .
			$this->func->get_date('Y/m/d H:i:s', $time) . '</p>';
		}

		$data = array($uri, $rss, $this->conf, $this->func->is_editable_only_admin($this->root->vars['page']));
		$obj = new $class($this->xpwiki, $data, $show_description, $max, $uri);
		return $obj->toString($time_str);
	}

	// Get and save RSS
	function plugin_showrss_get_rss($target, $cachehour)
	{
		$data  = '';
		$time = NULL;
		if ($cachehour) {
			// Get the cache not expired
			$filename = $this->cont['CACHE_DIR'] . 'plugin/' . md5($target) . '.showrss';

			if (is_readable($filename) && (filemtime($filename) + $cachehour * 60 * 60) > $this->cont['UTC']) {
				$data  = unserialize(file_get_contents($filename));
				$time = filemtime($filename) - $this->cont['LOCALZONE'];
			}
		}

		if ($time === NULL) {
			// Remove expired cache
			$this->plugin_showrss_cache_expire($cachehour);

			// Newly get RSS
			$data = $this->func->http_request($target);
			if ($data['rc'] !== 200)
				return array(FALSE, 0);

			$buf = $data['data'];
			$time = $this->cont['UTIME'];

			// Parse
			$obj = new XpWikiShowRSS_XML($this->xpwiki);
			$data = $obj->parse($buf);

			// Save RSS into cache
			if ($cachehour) {
				$fp = fopen($filename, 'wb');
				fwrite($fp, serialize($data));
				fclose($fp);
			}

			// Update plainDB
			$this->func->need_update_plaindb();
		}

		return array($data, $time);
	}

	// Remove cache if expired limit exeed
	function plugin_showrss_cache_expire($cachehour)
	{
		$expire = $cachehour * 60 * 60; // Hour
		$dh = dir($this->cont['CACHE_DIR'] . 'plugin/');
		while (($file = $dh->read()) !== FALSE) {
			if (substr($file, -8) != '.showrss') continue;
			$file = $this->cont['CACHE_DIR'] . 'plugin/' . $file;
			$last = $this->cont['UTC'] - filemtime($file);
			if ($last > $expire) unlink($file);
		}
		$dh->close();
	}

	function plugin_showrss_get_timestamp($str)
	{
		$str = trim($str);
		if ($str == '') return $this->cont['UTIME'];

		$matches = array();
		if (preg_match('/(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.\d+)?(([+-])(\d{2}):(\d{2}))?/', $str, $matches)) {
			$time = strtotime($matches[1] . ' ' . $matches[2]);
			if ($time == -1) {
				$time = $this->cont['UTIME'];
			} else if (@ $matches[3]) {
				$diff = ($matches[5] * 60 + $matches[6]) * 60;
				$time += ($matches[4] == '-' ? $diff : -$diff);
			}
			return $time;
		} else {
			$time = strtotime($str);
			return ($time == -1) ? $this->cont['UTIME'] : $time - $this->cont['LOCALZONE'];
		}
	}
}

// Create HTML from RSS array()
class XpWikiShowRSS_html
{
	var $items = array();
	var $class = '';

	function XpWikiShowRSS_html(& $xpwiki, $data, $show_description = '', $max = 10)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;

		$count = 1;
		list($url, $rss, $conf, $is_editable_admin_only) = $data;

		$allow_html = FALSE;

		if ($show_description === 'html') {
			if ($is_editable_admin_only) {
				$allow_html = TRUE;
			} else {
				if ($conf['allow_html_urls']) {
					foreach($conf['allow_html_urls'] as $_url) {
						if (strpos($url, $_url) === 0) {
							$allow_html = TRUE;
							break;
						}
					}
				}
			}
		}

		if ($rss && is_array($rss)) {
			foreach ($rss as $date=>$items) {
				if ($count > $max) break;
				foreach ($items as $item) {
					if ($count > $max) break;
					$count++;
					$link = @ $item['LINK'];

					$this->func->encode_numericentity($item['TITLE'], $this->cont['SOURCE_ENCODING'], 'UTF-8');
					$linkstr = mb_convert_encoding($item['TITLE'], $this->cont['SOURCE_ENCODING'], 'UTF-8');
					$linkstr = strip_tags($this->func->unhtmlspecialchars($linkstr, ENT_QUOTES));
					$title = $this->func->format_date($item['_TIMESTAMP']);
					$passage = $this->func->get_passage($item['_TIMESTAMP']);
					if ($link) {
						$link = '<a href="' . $link . '" title="' .  $title . ' ' . $passage . '" rel="nofollow">' . $linkstr . '</a>';
					} else {
						$link = '<span title="' .  $title . ' ' . $passage . '">' . $linkstr . '</span>';
					}

					if ($show_description) {
						if (isset($item['DESCRIPTION']) || isset($item['CONTENT'])) {
							if (!isset($item['DESCRIPTION'])) {
								$item['DESCRIPTION'] = $item['CONTENT'];
							}

							$item['DESCRIPTION'] = $this->func->unhtmlspecialchars($item['DESCRIPTION'], ENT_QUOTES);

							if (! $allow_html) {
								$item['DESCRIPTION'] = strip_tags($item['DESCRIPTION']);
								$item['DESCRIPTION'] = htmlspecialchars(mb_substr($item['DESCRIPTION'], 0, 255, 'UTF-8'));
								$item['DESCRIPTION'] = preg_replace('/&amp;#(\d+);/', '&#$1;', $item['DESCRIPTION']);
							}

							$this->func->encode_numericentity($item['DESCRIPTION'], $this->cont['SOURCE_ENCODING'], 'UTF-8');
							$item['DESCRIPTION'] = mb_convert_encoding($item['DESCRIPTION'], $this->cont['SOURCE_ENCODING'], 'UTF-8');

							$link .= '<br />' . '<div class="quotation">' . $item['DESCRIPTION'] . '</div>';
						}
					}

					$this->items[$date][] = $this->format_link($link);
				}
			}
		}
	}

	function format_link($link)
	{
		return '<div style="clear:both;">' . $link . '</div>' . "\n";
	}

	function format_list($date, $str)
	{
		return $str;
	}

	function format_body($str)
	{
		return '<div>' . $str . '</div>';
	}

	function toString($timestamp)
	{
		$retval = '';
		foreach ($this->items as $date=>$items)
			$retval .= $this->format_list($date, join('', $items));
		$retval = $this->format_body($retval);
		return <<<EOD
<div class='showrss'>
 <div{$this->class}>
$retval$timestamp
 </div>
</div>
EOD;
	}
}

class XpWikiShowRSS_html_menubar extends XpWikiShowRSS_html
{
	var $class = ' class="small"';

	//function XpWikiShowRSS_html_menubar(& $xpwiki) {
	//	parent::XpWikiShowRSS_html($xpwiki);
	//}

	function format_link($link) {
		return '<li style="clear:both;">' . $link . '</li>' . "\n";
	}

	function format_body($str) {
		return '<ul class="recent_list">' . "\n" . $str . '</ul>' . "\n";
	}
}

class XpWikiShowRSS_html_recent extends XpWikiShowRSS_html
{
	var $class = ' class="small"';

	//function XpWikiShowRSS_html_recent (& $xpwiki) {
	//	parent::XpWikiShowRSS_html($xpwiki);
	//}

	function format_link($link) {
		return '<li style="clear:both;">' . $link . '</li>' . "\n";
	}

	function format_list($date, $str) {
		return '<div style="clear:both;"><strong>' . $date . '</strong>' . "\n" .
			'<ul class="recent_list">' . "\n" . $str . '</ul></div>' . "\n";
	}
}

	// Get RSS and array() them
class XpWikiShowRSS_XML
{
	var $items;
	var $item;
	var $is_item;
	var $tag;
	var $encoding;
	var $pass;

	function XpWikiShowRSS_XML(& $xpwiki)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;
	}

	function parse($buf)
	{
		$this->items   = array();
		$this->item    = array();
		$this->is_item = FALSE;
		$this->tag     = '';
		$this->pass    = FALSE;

		// Detect encoding
		$matches = array();
		if(preg_match('/<\?xml [^>]*\bencoding="([a-z0-9-_]+)"/i', $buf, $matches)) {
			$this->encoding = $matches[1];
		} else {
			$this->encoding = mb_detect_encoding($buf);
		}

		// Normalize to UTF-8 / ASCII
		if (! in_array(strtolower($this->encoding), array('us-ascii', 'iso-8859-1', 'utf-8'))) {
			$buf = mb_convert_encoding($buf, 'utf-8', $this->encoding);
			$this->encoding = 'utf-8';
		}

		// Parsing
		$xml_parser = xml_parser_create($this->encoding);
		xml_set_element_handler($xml_parser, array(& $this, 'start_element'), array(& $this, 'end_element'));
		xml_set_character_data_handler($xml_parser, array(& $this, 'character_data'));
		if (! xml_parse($xml_parser, $buf, 1)) {
			return(sprintf('XML error: %s at line %d in %s',
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser), $buf));
		}
		xml_parser_free($xml_parser);

		return $this->items;
	}

	function escape($str)
	{
		// Unescape already-escaped chars (&lt;, &gt;, &amp;, ...) in RSS body before htmlspecialchars()
		$str = strtr($str, array_flip(get_html_translation_table(ENT_COMPAT)));
		// Escape
		$str = htmlspecialchars($str);
		//echo $str;
		// Unescape
		$str = str_replace('&amp;', '&', $str);
		// Encoding conversion
		//$this->func->encode_numericentity($str, $this->cont['SOURCE_ENCODING'], $this->encoding);
		$str = mb_convert_encoding($str, 'UTF-8', $this->encoding);
		return trim($str);
	}

	// Tag start
	function start_element($parser, $name, $attrs)
	{
		if ($this->is_item) {
			$this->tag     = $name;
			if ($name === 'SOURCE') {
				// for ATOM feed
				$this->pass = TRUE;
			}
		} else if ($name === 'ITEM' || $name === 'ENTRY') {
			$this->is_item = TRUE;
		}
		if (! $this->pass && $this->is_item && $name === 'LINK') {
			if ((isset($attrs['REL']) && strtoupper($attrs['REL']) === 'ALTERNATE') || !isset($attrs['REL']) && isset($attrs['HREF'])) {
				$this->item['LINK'] = $attrs['HREF'];
			}
		}
	}

	// Tag end
	function end_element($parser, $name)
	{
		if ($name === 'SOURCE') {
			// for ATOM feed
			$this->pass = FALSE;
		}

		if (! $this->is_item || ($name !== 'ITEM' && $name !== 'ENTRY')) return;

		$item = array_map(array(& $this, 'escape'), $this->item);
		$this->item = array();

		if (isset($item['DC:DATE'])) {
			$time = xpwiki_plugin_showrss::plugin_showrss_get_timestamp($item['DC:DATE']);

		} else if (isset($item['PUBDATE'])) {
			$time = xpwiki_plugin_showrss::plugin_showrss_get_timestamp($item['PUBDATE']);

		} else if (isset($item['PUBLISHED'])) {
			$time = xpwiki_plugin_showrss::plugin_showrss_get_timestamp($item['PUBLISHED']);

		} else if (isset($item['UPDATED'])) {
			$time = xpwiki_plugin_showrss::plugin_showrss_get_timestamp($item['UPDATED']);

		} else if (isset($item['DESCRIPTION']) &&
			($description = trim($item['DESCRIPTION'])) != '' &&
			($time = strtotime($description)) != -1) {
				$time -= $this->cont['LOCALZONE'];

		} else {
			$time = $this->cont['UTC'] - $this->cont['LOCALZONE'];
		}
		$item['_TIMESTAMP'] = $time;
		$date = $this->func->get_date('Y-m-d', $item['_TIMESTAMP']);

		$this->items[$date][] = $item;
		$this->is_item        = FALSE;
	}

	function character_data($parser, $data)
	{
		if (! $this->is_item) return;
		if (! isset($this->item[$this->tag])) $this->item[$this->tag] = '';
		$this->item[$this->tag] .= $data;
	}
}
?>