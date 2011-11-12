<?php
/*
 * Created on 2008/06/25 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: hyp_rss2html.php,v 1.5 2009/05/25 00:13:03 nao-pon Exp $
 */

class HypRss2Html
{
	var $is_item = FALSE;
	var $is_base = FALSE;
	var $template = 'simple';
	var $preRemoves = array();
	var $detect_order = 'ASCII, JIS, UTF-8, eucJP-win, EUC-JP, SJIS-win, SJIS';
	
	function HypRss2Html($src) {
		if ($this->preRemoves) {
			foreach($this->preRemoves as $tag) {
				// PHP might down in the regular expression. 
				$_tmp = '';
				$_arr = explode('</' . $tag . '>', $src);
				if (isset($arr[1])) {
					foreach($_arr as $_) {
						$_tmp .= strstr($_, '<' . $tag, true);
					}
					$src = $_tmp;
				}
			}
		}
		$this->src = preg_replace('/([\x00-\x08]|[\x0b\x0c]|[\x0e-\x1f]|[\x7f])+/', '', $src);
	}
	
	function getHtml() {
		$this->_parseXML();
		return $this->toString();
	}

	function _parseXML()
	{
		$this->items   = array();
		$this->item    = array();
		$this->is_item = FALSE;
		$this->tag     = '';
		$this->level_base = 0;
		
		$buf = $this->src;
		
		// Detect encoding
		$matches = array();
		if(preg_match('/<\?xml [^>]*\bencoding="([a-z0-9-_]+)"/i', $buf, $matches)) {
			$this->encoding = $matches[1];
		} else {
			$this->encoding = mb_detect_encoding($buf, $this->detect_order);
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

		//return $this->items;
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
		$this->tag = '';
		if ($this->is_item || ($this->is_base && $this->level_base === 1)) {
			$this->tag = $name;
		}
		if ($name == 'ITEM' || $name == 'ENTRY') {
			$this->is_item = TRUE;
		}
		if (! $this->is_base && ($name == 'CHANNEL' || $name == 'FEED')) {
			$this->is_base = TRUE;
		}
		if ($this->is_item && $name === 'LINK') {
			if ((isset($attrs['REL']) && strtoupper($attrs['REL']) === 'ALTERNATE') || !isset($attrs['REL']) && isset($attrs['HREF'])) {
				$this->item['LINK'] = $attrs['HREF'];
			}
		}
		if ($this->level_base === 1 && $name === 'LINK') {
			if ((isset($attrs['REL']) && strtoupper($attrs['REL']) === 'ALTERNATE') || !isset($attrs['REL']) && isset($attrs['HREF'])) {
				$this->base['LINK'] = $attrs['HREF'];
			}
		}
		if ($this->is_base) {
			$this->level_base++;
		}
	}

	// Tag end
	function end_element($parser, $name)
	{
		if ($this->is_base) {
			$this->level_base--;
		}

		if ($this->is_base && ! $this->is_item) {
			if ($name == 'CHANNEL' || $name == 'FEED') {
				$this->base = array_map(array(& $this, 'escape'), $this->base);
				if (! isset($this->base['DESCRIPTION']) && isset($this->base['SUBTITLE'])) {
					$this->base['DESCRIPTION'] = $this->base['SUBTITLE'];
				}
				if (isset($this->base['TITLE'])) {
					if (isset($this->base['LINK'])) {
						$this->base['LINKED_TITLE'] = '<a href="'.$this->base['LINK'].'">' . $this->base['TITLE'] . '</a>';
					} else {
						$this->base['LINKED_TITLE'] = $this->base['TITLE'];
					}
				}
				$this->is_base = FALSE;
			}
		}
		
		if ($this->is_item && ($name === 'ITEM' || $name === 'ENTRY')) {
	
			$item = array_map(array(& $this, 'escape'), $this->item);
			$this->item = array();
	
			if (isset($item['DC:DATE'])) {
				$time = $this->_get_timestamp($item['DC:DATE']);
				
			} else if (isset($item['PUBDATE'])) {
				$time = $this->_get_timestamp($item['PUBDATE']);
	
	//		} else if (isset($item['UPDATED'])) {
	//			$time = $this->_get_timestamp($item['UPDATED']);
				
			} else if (isset($item['PUBLISHED'])) {
				$time = $this->_get_timestamp($item['PUBLISHED']);
	
			} else if (isset($item['DESCRIPTION']) &&
				($description = trim($item['DESCRIPTION'])) !== '' && strtotime($description) !== -1) {
					$time = strtotime($description);
	
			} else {
				$time = time();
			}
			
			if (! isset($item['DESCRIPTION']) && isset($item['SUMMARY'])) {
				$item['DESCRIPTION'] = $item['SUMMARY'];
			}
			
			$item['_TIMESTAMP'] = $time;
			$item['DATE'] = date('D M j G:i', $time);
			$item['PASSAGE'] = $this->_get_passage($time);
			
			$this->items[] = $item;
			$this->is_item        = FALSE;
		}

	}

	function character_data($parser, $data)
	{
		if ($this->tag) {
			if ($this->is_base && ! $this->is_item) {
				if (! isset($this->base[$this->tag])) $this->base[$this->tag] = '';
				$this->base[$this->tag] .= $data;				
			} else {
				if (! $this->is_item) return;
				if (! isset($this->item[$this->tag])) $this->item[$this->tag] = '';
				$this->item[$this->tag] .= $data;
			}
		}
	}

	function toString($timestamp = '')
	{
		$temp['base'] = file_get_contents( dirname( __FILE__ ) . '/templates/' . $this->template . '/base.html');
		$temp['item'] = file_get_contents( dirname( __FILE__ ) . '/templates/' . $this->template . '/item.html');

		preg_match_all('/<_([A-Z_-]+)_>/', $temp['base'], $match);
		$reps['base'] = array_unique($match[1]);
		preg_match_all('/<_([A-Z_-]+)_>/', $temp['item'], $match);
		$reps['item'] = array_unique($match[1]);

		$this->base['ITEMS'] = '';
		foreach ($this->items as $item) {
			$item_t = $temp['item'];
			foreach($reps['item'] as $key) {
				$data = (isset($item[$key]))? $item[$key] : '';
				$item_t = str_replace('<_' . $key . '_>', $data, $item_t);
			}
			$this->base['ITEMS'] .= $item_t;
		}

		$base = $temp['base'];
		foreach($reps['base'] as $key) {
			$data = (isset($this->base[$key]))? $this->base[$key] : '';
			$base = str_replace('<_' . $key . '_>', $data, $base);
		}
		
		return $base;
	}


	function _get_timestamp($str)
	{
		$str = trim($str);
		if ($str == '') return time();
	
		$matches = array();
		if (preg_match('/(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.\d+)?(([+-])(\d{2}):(\d{2}))?/', $str, $matches)) {
			$time = strtotime($matches[1] . ' ' . $matches[2]);
			if ($time == -1) {
				$time = time();
			} else if (@ $matches[3]) {
				$diff = ($matches[5] * 60 + $matches[6]) * 60;
				$time += ($matches[4] == '-' ? $diff : -$diff);
			}
			return $time;
		} else {
			$time = strtotime($str);
			return ($time == -1) ? time() : $time;
		}
	}

	function _get_passage($time, $paren = FALSE)
	{
		static $units = array('m'=>60, 'h'=>24, 'd'=>1);
		static $utime;
		
		if (!$utime) {
			$utime = time();
		}
		
		$time = ($utime - $time) / 60; // minutes
	
		foreach ($units as $unit=>$card) {
			if (abs($time) < $card) break;
			$time /= $card;
		}
		$time = floor($time) . $unit;
	
		return $paren ? '(' . $time . ')' : $time;
	}
}
?>