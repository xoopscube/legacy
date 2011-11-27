<?php
// $Id: block.inc.php,v 1.10 2011/11/25 15:55:35 nao-pon Exp $

/*
 * countdown.inc.php
 * License: GPL
 * Author: nao-pon http://hypweb.net
 * XOOPS Module Block Plugin
 *
 */

class xpwiki_plugin_block extends xpwiki_plugin {
	function plugin_block_init () {


	}

	function plugin_block_convert()
	{
		static $b_count = array();
		if (!isset($b_count[$this->xpwiki->pid])) {$b_count[$this->xpwiki->pid] = 0;}
		static $b_tag = array();
		if (!isset($b_tag[$this->xpwiki->pid])) {$b_tag[$this->xpwiki->pid] = array();}
		static $b_round = array();
		$_style = '';
		$tate_div = '';
		$tate_js = '';
		$tate_style = '';
		$block_class = 'wiki_body_block';
		$need_css = false;
		if (!isset($b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])) $b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = 0;

		$params = array(
			'end'       => false,
			'clear'     => false,
			'left'      => false,
			'center'    => false,
			'right'     => false,
			'around'    => false,
			'tate'      => false,
			'h'         => '',
			'width'     => '',
			'w'         => '',
			'class'     => false,
			'font-size' => '',
			'round'     => false,
			'_args'     => array(),
			'_done'     => FALSE
		);
		// オプション解析
		$args = func_get_args();
		$this->fetch_options($params, $args);

		// end
		if ($params['end'])
		{
			$ret = $this->func->get_areadiv_closer();
			if (isset($b_round[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])) {
				$ret .= '<div class="round_bb"><div></div></div>';
			}
			$ret .= str_repeat('</div>', $b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]]) . "\n";
			$b_count[$this->xpwiki->pid]--;
			return $ret;
		}

		// body
		$body = '';
		if ($params['_args']) {
			$body = array_pop($params['_args']);
		}

		// clear
		if ($params['clear']) {
			$clear = 'both';
			if (is_string($params['clear'])) {
				$clear = strtolower($params['clear']);
				if (! in_array($clear, array('left', 'right'))) {
					$clear = 'both';
				}
			}
			if (! $body) {
				return '<div style="clear:'.$clear.'"></div>' . "\n";
			} else {
				$_style .= 'clear:'.$clear.';';
			}
		}

		$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = 1;

		if ($params['left']) $align = 'left';
		if ($params['center']) $align = 'center';
		if ($params['right']) $align = 'right';

		$around = $params['around'];
		$width = $params['w'];
		if (!$width) $width = $params['width'];
		$fontsize = $params['font-size'];

		$tate = $params['tate'];
		$height = $params['h'];

		if ($params['class']) {
			$class = trim(preg_replace('[^a-zA-Z0-9_ @-]', '', $params['class']));
			if ($class) {
				$class = preg_replace('/\s*@/', ' block_', $class);
				$block_class .= ' ' . $class;
				$need_css = true;
			}
		}

		if ($tate)
		{
			$this->load_language();
			$need_css = true;
			$block_class .= ' wiki_body_block_tate';
			$tate_style = ' style="writing-mode:tb-rl;"';
			$tate_js = "\n<script type=\"text/javascript\">\n<!--\nif (!wikihelper_WinIE) document.write(\"<div style='text-align:right;'><small>{$this->msg['ie_only_tate']}</small></div>\");\n-->\n</script>\n";

			if (strpos($width,'%')) $width = '';
			if (strpos($height,'%')) $height = '';
		}

		$unit = '%|em|px|mm|cm|in|pt|pc|ex';
		if (preg_match('/^([\d.]+)('.$unit.')?/',$fontsize,$match))
		{
			if (empty($match[2])) $match[2] = 'px';
			$fontsize = $match[1] . $match[2];
			$_style .= 'font-size:'.$fontsize.';';
		}

		$set_width = false;
		if (preg_match('/^([\d.]+)('.$unit.')?$/',$width,$match))
		{
			if (empty($match[2])) $match[2] = 'px';
			$width = $match[1] . $match[2];
			$_style .= 'width:'.$width.';';
			$set_width = true;
		}

		if (preg_match('/^([\d.]+)('.$unit.')?$/',$height,$match))
		{
			if (empty($match[2])) $match[2] = 'px';
			$height = $match[1] . $match[2];
			$_style .= 'height:'.$height.';';
		}

		if ($params['around']) {
			if (! $set_width) {
				$_style .= 'width:auto;';
			}
			$style = ' style="float:'.$align.';'.$_style.'"';
		} else {
			if ($params['left']) {
				$style = ' style="margin-left:0px;margin-right:auto;'.$_style.'"';
			} elseif ($params['right']) {
				$style = ' style="margin-left:auto;margin-right:0px;'.$_style.'"';
			} else {
				$style = ' style="margin-left:auto;margin-right:auto;'.$_style.'"';
			}
		}

		$round = '';
		if ($params['round']) {
			$round ='<div class="round_box"><div class="round_bi"><div class="round_bt"><div></div></div><div class="round_content">';
			$b_round[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = TRUE;
			$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] += 3;
			$need_css = true;
		}

		if ($need_css) {
			$this->func->add_tag_head('block.css');
		}

		if ($body) {
			$body = $this->func->convert_html_multiline($body);
			if (isset($b_round[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])) {
				$body .= '<div class="round_bb"><div></div></div>';
			}
			$body .= str_repeat('</div>', $b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])."\n";
			$b_count[$this->xpwiki->pid]--;
			$areadiv_closer = '';
		} else {
			$areadiv_closer = $this->func->get_areadiv_closer();
		}

		return $areadiv_closer.'<div'.$style.' class="'.$block_class.'">'.$tate_div.$tate_js.$round.$body;

	}
}
