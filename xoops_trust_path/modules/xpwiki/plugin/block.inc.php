<?php
class xpwiki_plugin_block extends xpwiki_plugin {
	function plugin_block_init () {


	// $Id: block.inc.php,v 1.8 2009/09/01 06:37:24 nao-pon Exp $
	
	/*
	 * countdown.inc.php
	 * License: GPL
	 * Author: nao-pon http://hypweb.net
	 * XOOPS Module Block Plugin
	 *
		 */

	}
	
	function plugin_block_convert()
	{
	//	static $b_count = 1;
		static $b_count = array();
		if (!isset($b_count[$this->xpwiki->pid])) {$b_count[$this->xpwiki->pid] = 0;}
		static $b_tag = array();
		if (!isset($b_tag[$this->xpwiki->pid])) {$b_tag[$this->xpwiki->pid] = array();}
		static $b_round = array();
		$ie5_div = "";
		$_style = "";
		$tate_div = "";
		$tate_js = "";
		$tate_style = "";
		$block_class = "wiki_body_block";
		if (!isset($b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])) $b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = 0;
		
		$params = array(
			'end'=>false,
			'clear'=>false,
			'left'=>false,
			'center'=>false,
			'right'=>false,
			'around'=>false,
			'tate'=>false,
			'h'=>'',
			'width'=>"",
			'w'=>"",
			'class'=>false,
			'font-size'=>'',
			'round' => false,
			'_args'=>array(),
			'_done'=>FALSE,
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
			$ret .= str_repeat("</div>",$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])."\n";
			//$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]]--;
			$b_count[$this->xpwiki->pid]--;
			return $ret;
		}
		// clear
		if ($params['clear']) return '<div style="clear:both"></div>'."\n";
		
		$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = 1;
		//$b_count[$this->xpwiki->pid]++;
		
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
			$block_class .= ' ' . trim(htmlspecialchars($params['class']));
		}
		
		//$b_count[$this->xpwiki->pid]++;
		//$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]]++;
		
		if ($tate)
		{
			$this->load_language();
			$this->func->add_tag_head('block.css');
			$block_class .= " wiki_body_block_tate";
			//$tate_div = "<div class=\"tate\">";
			//$b_tag[$b_count]++;
			$tate_style = " style=\"writing-mode:tb-rl;\"";
			$tate_js = "\n<script type=\"text/javascript\">\n<!--\nif (!wikihelper_WinIE) document.write(\"<div style='text-align:right;'><small>{$this->msg['ie_only_tate']}</small></div>\");\n-->\n</script>\n";
			
			if (strpos($width,"%")) $width = "";
			if (strpos($height,"%")) $height = "";
		}
		
		if (preg_match("/^[\d]+%?$/",$fontsize))
		{
			$fontsize = (!strstr($fontsize,"%"))? $fontsize."px" : $fontsize;
			$_style .= "font-size:".$fontsize.";";
		}
	
		$match = array();
		if (preg_match("/^([\d]+%?)(px)?$/i",$width,$match))
		{
			$width = (!strstr($match[1],"%"))? $match[1]."px" : $match[1];
			$_style .= "width:".$width.";";
		}
		
		if (preg_match("/^([\d]+%?)(px)?$/i",$height,$match))
		{
			$height = (!strstr($match[1],"%"))? $match[1]."px" : $match[1];
			$_style .= "height:".$height.";";
		}
		
		if ($params['around'])
			$style = " style='float:{$align};display:inline;{$_style}'";
		else
		{
			if ($params['left'])
			{
				$style = " style='margin-left:0px;margin-right:auto;{$_style}'";
			}
			elseif ($params['right'])
			{
				$style = " style='margin-left:auto;margin-right:0px;{$_style}'";
			}
			else
			{
				$style = " style='margin-left:auto;margin-right:auto;{$_style}'";
			}
			//$ie5_div = "<div class=\"ie5\"{$tate_style}>";
			//$ie5_div = "<div class=\"ie5\">";
			$ie5_div = '';
			//$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]]++;
		}
		
		$round = '';
		if ($params['round']) {
			$round ='<div class="round_box"><div class="round_bi"><div class="round_bt"><div></div></div><div class="round_content">';
			$b_round[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] = TRUE;
			$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]] += 3;
			$this->func->add_tag_head('block.css');
		}

		$body = '';
		if ($params['_args']) {
			$body = array_pop($params['_args']);
		}
		
		if ($body) {
			$body = $this->func->convert_html_multiline($body);
			if (isset($b_round[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])) {
				$body .= '<div class="round_bb"><div></div></div>';
			}
			$body .= str_repeat("</div>",$b_tag[$this->xpwiki->pid][$b_count[$this->xpwiki->pid]])."\n";
			$b_count[$this->xpwiki->pid]--;
			$areadiv_closer = '';
		} else {
			$areadiv_closer = $this->func->get_areadiv_closer();
		}

		return "{$areadiv_closer}{$ie5_div}<div{$style} class=\"{$block_class}\">{$tate_div}{$tate_js}{$round}{$body}";

	}
}
?>