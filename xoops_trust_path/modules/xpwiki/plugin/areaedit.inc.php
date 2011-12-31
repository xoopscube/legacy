<?php
class xpwiki_plugin_areaedit extends xpwiki_plugin {
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: areaedit.inc.php,v 1.14 2011/12/31 16:07:55 nao-pon Exp $
//
/*
*プラグイン areaedit
 指定した位置のみ編集可能にする

*Usage
 #areaedit([start|end|btn:<text>|nofreeze|noauth|collect[:<page>]])
 &areaedit([nofreeze|noauth|preview[:<num>]]){<text>};

*/

	function plugin_areaedit_init() {
		// 言語ファイルの読み込み
		$this->load_language();
	}

	//========================================================
	function plugin_areaedit_convert() {
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}
		static $starts = array();
		if (!isset($starts[$this->xpwiki->pid])) {$starts[$this->xpwiki->pid] = array();}

		$page = $this->root->vars['page'];
		if (!array_key_exists($page,$numbers[$this->xpwiki->pid]))	$numbers[$this->xpwiki->pid][$page] = 0;
		$areaedit_no = $numbers[$this->xpwiki->pid][$page]++;

		$end_flag = $nofreeze = $noauth =  0;
		$collect = '';
		$btn_name = $this->msg['btn_name'];
		if ( func_num_args() ){
			foreach ( func_get_args() as $opt ){
				$matches = array();
				if ( $opt == 'start' ){
					 $end_flag = 0;
				}
				else if ( $opt == 'end' ){
					$end_flag = 1;
				}
				else if ( $opt == 'nofreeze' ){
					$nofreeze = 1;
					$noauth = 1;
				}
				else if ( $opt == 'noauth' ){
					$nofreeze = 1;
					$noauth = 1;
				}
				else if ( preg_match('/^collect(?::(.+))?$/',$opt,$matches) ){
					$collect = $matches[1] ? $matches[1] : $page;
				}
				else if ( preg_match('/^btn:(.*)$/',$opt,$matches) ){
					$btn_name = $matches[1];
				}
			}
		}
		if ( $end_flag ) {
			//return "<div></div>";
			return "</div>";
		}
		if (!array_key_exists($page,$starts[$this->xpwiki->pid]))	$starts[$this->xpwiki->pid][$page] = 0;
		$areaedit_start_no = $starts[$this->xpwiki->pid][$page]++;

		if ($this->root->plugin_follow_editauth) {
			$nofreeze = 0;
			$noauth = 0;
		}

		$id = "area".substr(md5($page.$areaedit_no), mt_rand(0, 24), 7);

		$f_page	  = rawurlencode($page);

		if ( $noauth == 0 and  ! $this->func->edit_auth($page,FALSE,FALSE) ){
			return <<<EOD
<div style="margin:0px 0px 0px auto;text-align:right;" title="$areaedit_start_no">
{$this->msg['msg_cannotedit']}
</div>
<div id="{$id}">
EOD;
		}
		if ( $nofreeze == 0 and $this->func->is_freeze($page) ){
			 return	 <<<EOD
<div style="margin:0px 0px 0px auto;text-align:right;">
[<a href="{$this->root->script}?cmd=unfreeze&amp;page=$f_page" title="$areaedit_start_no">{$this->root->_msg_unfreeze}</a>]
</div>
<div id="{$id}">
EOD;
		}
		if ( $collect ) {
			if ( $btn_name == $this->msg['btn_name'] ) {
				$btn_name  = $this->msg['btn_name_collect'];
			}
			$s_page	  = htmlspecialchars($page);
			$s_refer  = htmlspecialchars($collect);
			$s_digest = htmlspecialchars($this->root->digest);
			$script = $this->func->get_script_uri();
			return <<<EOD
<div style="margin:0px auto 0px 0px;text-align:left;" title="collect">
<form action="{$script}" method="post">
 <div class="edit_form">
  <input type="hidden" name="plugin" value="areaedit" />
  <input type="hidden" name="page"	 value="$s_page" />
  <input type="hidden" name="refer"	 value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <input type="hidden" name="inline_plugin" value="0" />
  <input type="hidden" name="areaedit_no"	value="$areaedit_no" />
  <input type="submit" name="collect"		value="$btn_name" />
  <a href="{$this->root->script}?$s_refer">$collect</a>
</div>
</form>
</div>
<div id="{$id}">
EOD;
		} else {
			$js_tag = ' onmouseover="wikihelper_area_highlite(\''.$id.'\',1);" onmouseout="wikihelper_area_highlite(\''.$id.'\',0);"';

			return <<<EOD
<div style="width:auto;margin:0px 0px 0px auto;float:right;"{$js_tag}>
<a href="{$this->root->script}?plugin=areaedit&amp;areaedit_no=$areaedit_no&amp;inline_plugin=0&amp;page=$f_page&amp;digest={$this->root->digest}" title="$areaedit_start_no">$btn_name</a>
</div>
<div id="{$id}">
EOD;
		}
	}
	//========================================================
	function plugin_areaedit_inline() {
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}

		$page = $this->root->vars['page'];

		if (!array_key_exists($page,$numbers[$this->xpwiki->pid])){$numbers[$this->xpwiki->pid][$page] = 0;}

		if (empty($this->root->pwm_plugin_flg['system']['contents_convert'])){$areaedit_no = $numbers[$this->xpwiki->pid][$page]++;}

		$args = func_get_args();
		$str = array_pop($args);
		$string = "";
		$id = "area".substr(md5($page.$areaedit_no), mt_rand(0, 24), 7);
		$js_tag = "";
		if ( $str != '' ){
			$string = '<span id="'.$id.'">'.$str.'</span>';
			$js_tag = ' onmouseover="wikihelper_area_highlite(\''.$id.'\',1);" onmouseout="wikihelper_area_highlite(\''.$id.'\',0);"';
		}
		$ndigest = $this->root->digest;
		$nofreeze = $noauth = $inline_preview = 0;
		foreach ( $args as $opt ){
			$opt = trim($opt);
			$match = array();
			if ( $opt == 'nofreeze' ){
				$nofreeze = 1;
				$noauth = 1;
			}
			else if ( $opt == 'noauth' ){
				$nofreeze = 1;
				$noauth = 1;
			}
			else if ( preg_match('/^preview(?::(\d+))?$/',$opt,$match) ){
				$num = $match[1];
				if ( $num == '' ) $num = 99;
				$inline_preview = $num;
			}
			else if ( preg_match('/^uid:(\d+)?$/',$opt,$match) ){
				if ($this->root->userinfo['uid'] && $this->root->userinfo['uid'] == $match[1])
				{
					$nofreeze = 1;
					$noauth = 1;
				}
			}
			else if ( preg_match('/^ucd:([^,]+)$/',$opt,$match) ){
				if (!empty($this->root->pwm_config['deny_ucds']) && in_array($match[1], $this->root->pwm_config['deny_ucds'])) return "";
				$nofreeze = 1;
				$noauth = 1;
			}
		}
		$f_page	  = rawurlencode($page);

		if ($this->root->plugin_follow_editauth) {
			$nofreeze = 0;
			$noauth = 0;
		}

		if ( $noauth == 0 and  ! $this->func->edit_auth($page,FALSE,FALSE) ){
			$ret = $this->msg['msg_cannotedit_inline'];
		} else if ( $nofreeze == 0 and $this->func->is_freeze($page) ){
			 $ret = <<<EOD
<a href="{$this->root->script}?cmd=unfreeze&amp;page=$f_page">{$this->msg['msg_unfreeze_inline']}</a>
EOD;
		} else {
			$f_digest = rawurlencode($ndigest);
			$btn_name = $this->msg['btn_name_inline'];

			$ret = <<<EOD
<a href="{$this->root->script}?plugin=areaedit&amp;areaedit_no=$areaedit_no&amp;page=$f_page&amp;inline_plugin=1&amp;inline_preview=$inline_preview&amp;digest=$f_digest" title="{$this->msg['btn_name']}:$areaedit_no"{$js_tag}>$btn_name</a>
EOD;
		}
		return $string . $this->func->wrap_description_ignore($ret);
	}
	//========================================================
	function plugin_areaedit_action() {

		if ( ! $this->func->is_page($this->root->vars['page']) ){
			$error = str_replace('$1', $this->root->vars['page'], $this->msg['no_page_error']);
			return array(
				'msg'  => $this->msg['title_error'],
			'body' => $error,
		);
		}

		if (!empty($this->root->vars['areaedit_msg'])) $this->root->vars['areaedit_msg'] = preg_replace("/\x0D\x0A|\x0D|\x0A/","\n",$this->root->vars['areaedit_msg']);

		if ($_SERVER['REQUEST_METHOD'] == "POST" && !array_key_exists('preview',$this->root->vars))
			$this->root->vars['write'] = TRUE;

		if ( array_key_exists('inline_plugin', $this->root->vars) ) {
			if ( $this->root->vars['inline_plugin'] == 1 ) {
				return $this->plugin_areaedit_action_inline();
			}
			else {
				return $this->plugin_areaedit_action_block();
			}
		}
		return array(
			'msg'  => $this->msg['title_error'],
			'body' => $this->msg['body_error'],
		);
	}
	//========================================================
	function plugin_areaedit_action_inline() {
		$notimestamp = 0;
		$str_areaedit = 'areaedit';
		$len_areaedit = strlen($str_areaedit) + 1;
		$title = $body = $headdata = $targetdata = $taildata =	'';

		$areaedit_no = 0;
		if ( array_key_exists('areaedit_no', $this->root->vars) ) $_areaedit_no = $areaedit_no = $this->root->vars['areaedit_no'];


		$postdata_old =	array();
		$page = $this->root->vars['page'];
		$inline_preview = array_key_exists('inline_preview',$this->root->vars) ? $this->root->vars['inline_preview'] : 0;

		if (  ! array_key_exists('preview',$this->root->vars) ) {
			if ( array_key_exists('write',$this->root->vars)) {
				$postdata_old = preg_replace('/$/',"\n",
					explode("\n", $this->root->vars['headdata'] . $this->root->vars['taildata']));
			}
			else {
				$postdata_old = $this->func->get_source($page);
			}

		$ic = new XpWikiInlineConverter($this->xpwiki, array('plugin'));
		$areaedit_ct = $skipflag = 0;
		$found_nofreeze = $found_noauth =  0;
		$skipflag = 0;
		$update_flag = FALSE;
		//pcommentスタートマーカー
		$areastart = (empty($this->root->vars['start']))? "" : $this->root->vars['start'];
		foreach($postdata_old as $line)
		{
			if ( $skipflag ) {
				$taildata .= $line;
				continue;
			}
			//pcommentスタートマーカー
			if ($areastart == md5(rtrim($line))) $areastart = "";
			if ( substr($line,0,1) == ' ' || substr($line,0,2) == '//')
			{
				$headdata .= $line;
				continue;
			}
			$match = array();
			if ( ! preg_match("/&$str_areaedit/", $line, $match) ){
				$headdata .= $line;
				continue;
			}
			$pos = 0;
			$arr = $ic->get_objects($line,$page);
			while ( count($arr) ){
				$obj = array_shift($arr);
				if ( $obj->name != $str_areaedit ) continue;
				$pos = strpos($line, '&' . $str_areaedit, $pos);
				$pos += $len_areaedit;
				if ( $areastart )
				{
					$_areaedit_no++;
					continue;
				}
				if ( $areaedit_ct++ < $areaedit_no ) continue;
				$r_line = substr($line,$pos+strlen($obj->text)-$len_areaedit-1); // };.....

				$add_flag = 0;
				switch ( substr($line,$pos,1) ) {
					case '(':
						$pos += strlen($obj->param) + 2;
						break;
					case ';':
					case '{':
						$add_flag = 1;
						break;
				}
				$l_line = substr($line,0,$pos);							 // ....&areaedit
				if ( $add_flag ) $l_line .= '()';
				$options = explode(',', $obj->param);
				foreach ( $options as $opt ){
					$opt = trim($opt);
					if ( $opt == 'nofreeze' ){
						$found_nofreeze = 1;
						$found_noauth = 1;
					}
					else if ( $opt == 'noauth' ){
						$found_nofreeze = 1;
						$found_noauth = 1;
					}
					else if ( preg_match('/^preview(?::(\d+))?$/',$opt,$match) ){
						$num = $match[1];
						if ( $num == '' ) $num = 99;
						$inline_preview = $num;
					}
					else if ( preg_match('/^uid:(\d+)?$/',$opt,$match) ){
						if ($this->userinfo['uid'] && $this->userinfo['uid'] == $match[1])
						{
							$found_nofreeze = 1;
							$found_noauth = 1;
						}
					}
					else if ( preg_match('/^ucd:([^,]+)$/',$opt,$match) ){
						if ($this->root->userinfo['ucd'] == $match[1])
						{
							$found_nofreeze = 1;
							$found_noauth = 1;
						}
					}
				}

	//echo '/param=',$obj->param;

				$headdata .= $l_line;
				$targetdata = $obj->body;
				$taildata = $r_line;
				$skipflag = 1;
				break;
			}
			if ( ! $skipflag ) $headdata .= $line;
		}
	/*
	echo '/page=',$page;
	echo '/nofreeze=',$found_nofreeze;
	echo '/noauth=',$found_noauth;
	echo '/postdata_old=',join('/',$postdata_old);
	*/
		$this->root->vars['areaedit_no'] = $this->root->vars['areaedit_start_no'] = $_areaedit_no;
		if ( $found_noauth == 0 ){
			$this->func->edit_auth($page,true,true);
		}
		if ( $found_nofreeze == 0 and $this->func->is_freeze($page) ){
			$f_page = rawurlencode($page);
			$title = $this->msg['msg_cannotedit'];
			return array(
				'msg'=> $title,
			//'body' => "<h1>$title</h1>[<a href=\"{$this->root->script}?cmd=unfreeze&amp;page=$f_page\">{$this->root->_msg_unfreeze}</a>]",
			'body' => make_link($targetdata)."<hr />".$title,
		);
		}
	}
		else if ( array_key_exists('headdata', $this->root->vars) and array_key_exists('taildata', $this->root->vars) ){
			$headdata = $this->root->vars['headdata'];
			$taildata = $this->root->vars['taildata'];
		}

		if ( array_key_exists('areaedit_msg', $this->root->vars) ){
			// 改行有効
			$targetdata = $this->root->vars['areaedit_msg'];
			if (!empty($this->root->vars['enter_enable'])) $targetdata = str_replace("\n", '&br;', $targetdata);
			$targetdata = str_replace(array("\r","\n"),'',$targetdata);
		}
		if (array_key_exists('write',$this->root->vars)) {
			$nowdata = $headdata . '{' . $targetdata . '}' . $taildata;
			return $this->plugin_areaedit_write($page, $targetdata, $nowdata);
		}
		//echo "ok";
		//exit;
		$retval = $this->plugin_areaedit_preview($page, $targetdata, $headdata, $taildata, $inline_preview);

		if (array_key_exists('preview',$this->root->vars) ) return $retval;

		$title = str_replace('$1',$this->func->strip_bracket($page),$this->msg['title_edit']);
		$title = str_replace('$2',$this->root->vars['areaedit_start_no'],$title);
		return array(
			'msg'=> $title,
		'body'=> $retval['body'],
	);
	}
	//========================================================
	function plugin_areaedit_action_block() {

		$page	 = $this->root->vars['page'];
		$collect = array_key_exists('refer', $this->root->vars) ? $this->root->vars['refer'] : '';

		// 改行有効
		//if (!empty($this->root->vars['enter_enable'])) $this->root->vars['areaedit_msg'] = $this->func->auto_br($this->root->vars['areaedit_msg']);

		$headdata = $targetdata = $taildata = $para = $tailpara = '';
		if (  ! array_key_exists('preview',$this->root->vars) ) {
			if ( array_key_exists('write',$this->root->vars)) {
				$postdata_old = preg_replace('/$/',"\n",
					explode("\n", $this->root->vars['headdata'] . "\n" . $this->root->vars['taildata']));
			}
			else {
				$postdata_old = $this->func->get_source($page);
				$postdata_old = str_replace("\r",'', $postdata_old);
			}
			$options = array();
			$areaedit_ct = $areaedit_start_no = 0;
			$areaedit_no = 0;
			if ( array_key_exists('areaedit_no', $this->root->vars) ) $areaedit_no = $this->root->vars['areaedit_no'];

			$flag = $para_flag = $found_end = $found_nofreeze = $found_noauth = 0;
			foreach ( $postdata_old as $line ){
				if ( $flag == 0 ) {
					$headdata .= $line;
				}
				$matches = array();
				if ( ( $flag == 0 or $flag == 1 ) and
					preg_match('/^#areaedit(?:\(([^)]+)\))?\s*$/', $line, $matches) ){
					$options = preg_split('/\s*,\s*/', $matches[1]);
					if ( $areaedit_ct ++ == $areaedit_no ) {
						$flag = $para_flag = 1;
						foreach ( $options as $opt ){
							$opt = trim($opt);
							$mat = array();
							if ( $opt == 'nofreeze' ){
								$found_nofreeze = 1;
								$found_noauth = 1;
							}
							else if ( $opt == 'noauth' ){
								$found_nofreeze = 1;
								$found_noauth = 1;
							}
							else if ( preg_match('/^collect(?::(.+))?$/',$opt,$mat) ){
								$collect = $mat[1] ? $mat[1] : $page;
							}
						}
					}
					else {
						if ( in_array('end', $options) ){
							if ( $flag == 1 ){
								$found_end = 1;
							}
						}
						else if ( $flag == 0 ) {
							$areaedit_start_no ++;
						}
						if ( $flag == 1 ){
							$flag = 2;
							$taildata .= $line;
						}
						if ( $para_flag >= 1 ){
							$para_flag = 2;
							$tailpara .= $line;
						}
					}
					continue;
				}
				else {
					switch ( $flag ) {
						case 0:	break;
						case 1: $targetdata .= $line; break;
						case 2: $taildata	.= $line; break;
					}
					if ( $para_flag == 1 and  preg_match('/^\n?$/',$line, $matches)) {
						$para_flag = 2;
					}
					switch ( $para_flag ) {
						case 0:	break;
						case 1: $para	  .= $line; break;
						case 2: $tailpara .= $line; break;
					}
				}
			}
			if ( $found_end == 0 ){
				$this->root->vars['block'] = 1;
				$targetdata = $para;
				$taildata	= $tailpara;
			}
			$this->root->vars['areaedit_start_no'] = $areaedit_start_no;

			if ( $found_noauth == 0 ){
				$this->func->edit_auth($page,true,true);
			}
			if ( $found_nofreeze == 0 and $this->func->is_freeze($page) ){
				$f_page = rawurlencode($page);
				$title = str_replace('$1', $this->func->strip_bracket($page), $this->root->_title_isfreezed);
				return array(
					'msg'=> $title,
				'body' => "<h1>$title</h1>[<a href=\"{$this->root->script}?cmd=unfreeze&amp;page=$f_page\">{$this->root->_msg_unfreeze}</a>]",
			);
			}
		}
		else if ( array_key_exists('headdata', $this->root->vars) and array_key_exists('taildata', $this->root->vars) ){
			$headdata = $this->root->vars['headdata'];
			$taildata = $this->root->vars['taildata'];
		}
		else {
		}

		$update_flag = FALSE;
		if ( array_key_exists('areaedit_msg', $this->root->vars) ){
			$lines = split("\n", str_replace("\r",'',$this->root->vars['areaedit_msg']));
			$update_flag = TRUE;
		}
		else if ( $collect ){
			if ( $collect == $page ) {
				$lines = $this->plugin_areaedit_collect($page,$postdata_old);
				$update_flag = TRUE;
			}
			else if ( $this->func->is_page($collect) ) {
				$lines = $this->plugin_areaedit_collect($collect,$this->func->get_source($collect));
				$update_flag = TRUE;
			}
		}
		if ( $update_flag ){
			$targetdata = '';
			foreach ( $lines as $line ) {
				if ( $this->root->vars['block'] == 1 ) $line = preg_replace('/^$/','//', $line);
				$targetdata .= preg_replace('/^(?=#areaedit)/', '//', $line) . "\n";
			}
			if ( $this->root->vars['block'] == 1 ) {
				$targetdata = preg_replace('/\n?\/\/\s*$/',"\n",$targetdata);
			}
			else {
				$targetdata = preg_replace('/\s+$/',"\n",$targetdata);
			}
		}
		if (array_key_exists('write',$this->root->vars)) {
			$nowdata = $headdata . $targetdata . $taildata;
			return $this->plugin_areaedit_write($page, $targetdata, $nowdata);
		}
		$retval = $this->plugin_areaedit_preview($page, $targetdata, $headdata, $taildata, 0);
		if (array_key_exists('preview',$this->root->vars) ) return $retval;

		$title = str_replace('$1',$this->func->strip_bracket($page),$this->msg['title_edit']);
		$title = str_replace('$2',$this->root->vars['areaedit_start_no'],$title);
		return array(
			'msg'=> $title,
		'body'=> $retval['body'],
	);
	}
	//========================================================
	function plugin_areaedit_collect($page,$postdata_old){
		$str_areaedit = 'areaedit';
		$len_areaedit = strlen($str_areaedit) + 1;

		$ic = new XpWikiInlineConverter(array('plugin'));
		$outputs = array();
		$areaedit_ct = 0;
		foreach($postdata_old as $line)
		{
			if ( substr($line,0,1) == ' ' || substr($line,0,2) == '//' ) continue;
			$match = array();
			if ( ! preg_match("/&$str_areaedit/", $line, $match) )	continue;
			$pos = 0;
			$arr = $ic->get_objects($line,$page);
			while ( count($arr) ){
				$obj = array_shift($arr);
				if ( $obj->name != $str_areaedit ) continue;
				$pos = strpos($line, '&' . $str_areaedit, $pos);
				$pos += $len_areaedit;
				$outputs[] = "+" .	$obj->body;
				$areaedit_ct ++;
			}
		}
		return $outputs;
	}
	//========================================================
	// プレビュー
	function plugin_areaedit_preview($refer,$targetdata,$headdata,$taildata,$inline_flag)
	{
		$msg = $postdata_input = $targetdata;
		$msg = $this->func->remove_pginfo($msg);
		$postdata_input = $msg;

		$preview_above = '';
		if ( $inline_flag ){
			$append = "";
			$match = array();
			if ( preg_match('/^(.+)?\n/', str_replace("->\n","->___td_br___",$taildata), $match) ) $append = $match[1];
			$head = str_replace("\r",'',$headdata);
			$head = str_replace("->\n","->___td_br___",$head);
			$ary = explode("\n", $head);
			if ( $inline_flag < count($ary) ) {
				$ary = array_splice($ary, -$inline_flag, $inline_flag);
			}
			$head = join("\n", $ary);
			list ($_head,$head) = array_pad(preg_split("/(\n\*|\n\n)/s",$head,2),2,'');
			if (!$head) $head = $_head;
			$preview_above = $postdata_input;
			if ( $preview_above == '' )	 $preview_above = "&nbsp;&nbsp;";
			$preview_above = "{&font(u,o,,#FFFF99){" . $preview_above . "};}";
			$preview_above = $head . $preview_above . $append . "\n\n----\n\n";
			$preview_above = str_replace("->___td_br___","->\n",$preview_above);
			$preview_above = $this->func->make_str_rules($preview_above);
			$preview_above = explode("\n",$preview_above);
			$preview_above = $this->func->drop_submit($this->func->convert_html($preview_above));
			$preview_above = $this->plugin_areaedit_strip_link($preview_above);
		}

		$body = "{$this->root->_msg_preview}<br />\n";
		$body .= "<br />\n" . $preview_above;

		if ($postdata_input != '')
		{
			$postdata_input = $this->func->make_str_rules($postdata_input);
			$postdata_input = explode("\n",$postdata_input);
			$postdata_input = $this->func->drop_submit($this->func->convert_html($postdata_input));

			$body .= <<<EOD
<div class="preview">
  $postdata_input
</div>
EOD;
		}
		$body .= $this->areaedit_form($refer,$msg,$headdata,$taildata,$this->root->vars['digest']);

		$title = str_replace('$1',$refer,$this->msg['title_preview']);
		$title = str_replace('$2',$this->root->vars['areaedit_start_no'],$title);
		return array(
			'msg'=> $title,
			'body'=>$body,
		);
	}
	//========================================================
	function plugin_areaedit_strip_link($str){
		$str = preg_replace('/<\/?a[^>]*>/i','',$str);
		return $str;
	}
	//========================================================
	// 書き込み
	function plugin_areaedit_write($refer, $postdata_input, $postdata)
	{

		$retvars = array();

		$oldpagesrc = $this->func->get_source($refer, TRUE, TRUE);
		$oldpagemd5 = $this->func->get_digests($oldpagesrc);
		$oldpagesrc = $this->func->remove_pginfo($oldpagesrc);

		if ($oldpagemd5 != $this->root->vars['digest']) {
			$retvars['msg'] = str_replace('$1',htmlspecialchars($this->func->strip_bracket($this->root->vars['page'])),$this->root->_title_collided);
			$retvars['body'] = str_replace('$1',$this->func->make_pagelink($this->root->vars['page']),$this->msg['msg__collided']);
			$retvars['body'] .= "<p>|--> ".$this->func->make_pagelink($this->root->vars['page'])."</p>";

		} else {
			$notimestamp = !empty($this->root->vars['notimestamp']);
			if ( TRUE ){

				$this->func->page_write($refer,$postdata,$notimestamp);

				$retvars["msg"] =  $this->root->_title_updated;
				$retvars["body"] = "";
				$this->root->vars["refer"] = $this->root->vars["page"];

				return $retvars;
			}
			else if ( $postdata != '' ) {
				return array('msg'=>'test view', 'body'=>$this->func->convert_html($postdata));
			}

			$retvars['msg'] = $this->root->_title_deleted;
			$retvars['body'] = str_replace('$1',htmlspecialchars($refer),$this->root->_title_deleted);
			$this->func->tb_delete($refer);
		}

		return $retvars;
	}
	//========================================================
	// 編集フォームの表示
	function areaedit_form($page, $postdata_input, $headdata, $taildata, $digest = 0)
	{

		if ($digest == 0) {
			$digest = $this->func->get_digests($this->func->get_source($page, TRUE, TRUE));
		}
		$checked_time = array_key_exists('notimestamp',$this->root->vars) ? ' checked="checked"' : '';

		$r_page	  = rawurlencode($page);
		$s_page	  = htmlspecialchars($page);
		$r_digest = rawurlencode($digest);
		$s_digest = htmlspecialchars($digest);
		$s_postdata_input = htmlspecialchars(str_replace("&br;","\n",$postdata_input));
		$s_headdata = htmlspecialchars( $headdata );
		$s_taildata = htmlspecialchars( $taildata );
		$s_original = array_key_exists('original',$this->root->vars) ? htmlspecialchars($this->root->vars['original']) : $s_headdata . $s_postdata_input . $s_taildata;
		$b_preview = array_key_exists('preview',$this->root->vars); // プレビュー中TRUE
		$btn_preview = $b_preview ? $this->root->_btn_repreview : $this->root->_btn_preview;
		$timestamp_tag = ($this->root->userinfo['admin'] || (($this->root->userinfo['uid'] == $this->func->get_pg_auther($this->root->vars['page'])) && $this->root->userinfo['uid']))?
			'<input type="checkbox" id="notimestamp" name="notimestamp" value="true"'.$checked_time.' /><label for="notimestamp"><span style="small">'.$this->root->_btn_notchangetimestamp.'</span></label>'
		:'';
		if ($b_preview)
			$enter_enable = (!empty($this->root->vars['enter_enable']))? " checked=\"true\"" : "";
		else
			$enter_enable = " checked=\"true\"";

		$block = (empty($this->root->vars['block']))? '' : '1';
		$inline_plugin = (empty($this->root->vars['inline_plugin']))? '' : '1';
		$inline_preview = (isset($this->root->vars['inline_preview']))? intval($this->root->vars['inline_preview']) : '';
		$areaedit_no = (isset($this->root->vars['areaedit_no']))? intval($this->root->vars['areaedit_no']) : '';
		$areaedit_start_no = (isset($this->root->vars['areaedit_start_no']))? intval($this->root->vars['areaedit_start_no']) : '';
		$script = $this->func->get_script_uri();
		$body = <<<EOD
<form action="{$script}" method="post">
 <div class="edit_form">
  <input type="hidden" name="plugin" value="areaedit" />
  <input type="hidden" name="page"	 value="$s_page" />
  <input type="hidden" name="digest" value="$s_digest" />
  <input type="hidden" name="block"	 value="{$block}" />
  <input type="hidden" name="inline_plugin" value="{$inline_plugin}" />
  <input type="hidden" name="inline_preview" value="{$inline_preview}" />
  <input type="hidden" name="areaedit_no"	value="{$areaedit_no}" />
  <input type="hidden" name="areaedit_start_no" value="{$areaedit_start_no}" />
  <br />
  <textarea name="areaedit_msg" rows="{$this->root->rows}" cols="{$this->root->cols}">$s_postdata_input</textarea>
  <br />
  <input type="submit" name="preview" value="$btn_preview" accesskey="p" />
  <input type="submit"				  value="{$this->root->_btn_update}" accesskey="s" />
  {$timestamp_tag}
  <textarea name="original" rows="1" cols="1" style="display:none">$s_original</textarea>
  <textarea name="headdata" rows="1" cols="1" style="display:none">$s_headdata</textarea>
  <textarea name="taildata" rows="1" cols="1" style="display:none">$s_taildata</textarea>
 </div>
</form>
EOD;

		if (array_key_exists('help',$this->root->vars)) {
			$body .= $this->root->hr.$this->func->catrule();
		}
		else {
			if ( $this->root->vars['inline_plugin'] == 0 ){
				$body .= <<<EOD
<ul>
 <li>
 <a href="{$this->root->script}?plugin=areaedit&amp;help=true&amp;areaedit_no={$this->root->vars['areaedit_no']}&amp;inline_plugin=0&amp;page=$r_page&amp;digest=$r_digest">{$this->root->_msg_help}</a>
 </li>
</ul>
EOD;
			}
			else {
				$body .= <<<EOD
<ul>
 <li>
 <a href="{$this->root->script}?plugin=areaedit&amp;help=true&amp;areaedit_no={$this->root->vars['areaedit_no']}&amp;page=$r_page&amp;inline_plugin=1&amp;inline_preview={$this->root->vars['inline_preview']}&amp;digest=$r_digest">{$this->root->_msg_help}</a>
 </li>
</ul>
EOD;
			}
		}
		return $body;
	}
}
?>