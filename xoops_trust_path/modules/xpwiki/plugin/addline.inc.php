<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: addline.inc.php,v 1.5 2008/11/18 04:10:40 nao-pon Exp $
//
/* 
*プラグイン addline
 その場に、固定の文字列を追加する。

*Usage
 #addline(設定名[,above|below|up|down|number|nonumber][,btn:<ボタンテキスト>][,ltext:<左テキスト>][,rtext:<右テキスト>])
 &addline(設定名[,before|after|above|below|up|down|number|nonumber]){<ボタンテキスト>};

*パラメータ
  設定:	  「:config/plugin/addline/設定」の設定名を記載
  above|below|up|down: 上か下に追加する。
  before|after: ボタンテキストの前か後に追加する。
  ltext: ボタンの左側のテキスト
  rtext: ボタンの右側のテキスト

*設定ページの内容
 追加する文字列を記載する。複数行でもよい。
 例：
	|&attachref;|&attachref;|&attachref;|
	|あいう|えおか|きくけ|
*/


class xpwiki_plugin_addline extends xpwiki_plugin {
	
	function plugin_addline_init() {
		/////////////////////////////////////////////////
		// コメントを挿入する位置 1:欄の前 0:欄の後
		$this->config['ADDLINE_INS'] = '1';
		$this->msg = array(
			'btn_submit'	=> $this->root->_btn_insert,
			'title_collided'=> $this->root->_title_comment_collided,
			'msg_collided'	=> $this->root->_msg_comment_collided,
		);
	}
	function plugin_addline_convert() {
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}
		static $no_flag = array();
		if (!isset($no_flag[$this->xpwiki->pid])) {$no_flag[$this->xpwiki->pid] = 0;}
	
		if ($this->cont['PKWK_READONLY']) return ''; // Show nothing
	
		if (!array_key_exists($this->root->vars['page'],$numbers[$this->xpwiki->pid]))
		{
			$numbers[$this->xpwiki->pid][$this->root->vars['page']] = 0;
		}
		$addline_no = $numbers[$this->xpwiki->pid][$this->root->vars['page']]++;
		
		$above = $this->config['ADDLINE_INS'];
		$configname = 'default';
		$disabled = '';
		$btn_text = '';
		$right_text = $left_text = '';
		if ( func_num_args() ){
			foreach ( func_get_args() as $opt ){
			if ( $opt === 'above' || $opt === 'up' ){
					$above = 1;
				}
				else if (preg_match("/btn:(.+)/i",$opt,$args)){
					$btn_text = $args[1];
					if (strtolower(substr($btn_text, -4)) === "auth") {
						$btn_text = htmlspecialchars(rtrim(substr($btn_text, 0, strlen($btn_text)-4), ':'));
						$auth = 1;
						if ($this->cont['PKWK_READONLY'] || ! $this->func->check_editable($this->root->vars['page'], FALSE, FALSE)) {
							$disabled = ' disabled="disabled"';
						}
					}
				}
				else if (preg_match("/rtext:(.+)/i",$opt,$args)){
					$right_text = htmlspecialchars($args[1]);
				}
				else if (preg_match("/ltext:(.+)/i",$opt,$args)){
					$left_text = htmlspecialchars($args[1]);
				}
				else if ( $opt === 'below' || $opt === 'down' ){
					$above = 0;
				}
				else if ( $opt === 'number' ){
			$no_flag[$this->xpwiki->pid] = 1;
			}
				else if ( $opt === 'nonumber' ){
			$no_flag[$this->xpwiki->pid] = 0;
			}
				else {
					$configname = $opt;
				}
			}
			if (! $btn_text) $btn_text = $this->msg['btn_submit'];
			if (! $disabled && $no_flag[$this->xpwiki->pid]) $btn_text .= "[$addline_no]";
		}
	
		$f_page	  = htmlspecialchars($this->root->vars['page']);
		$f_config = htmlspecialchars($configname);
	
		$string = '';
		$script = $this->func->get_script_uri();
		if (! $disabled || $left_text || $right_text) $string = <<<EOD
 <form action="{$script}" method="post">
  <div style="margin:0px auto 0px auto;text-align:center;">
   <input type="hidden" name="addline_no" value="$addline_no" />
   <input type="hidden" name="refer" value="$f_page" />
   <input type="hidden" name="plugin" value="addline" />
   <input type="hidden" name="above" value="$above" />
   <input type="hidden" name="digest" value="{$this->root->digest}" />
   <input type="hidden" name="configname"  value="$f_config" />
   $left_text
   <input type="submit" name="addline" value="$btn_text"{$disabled}/>
   $right_text
  </div>
 </form>
EOD;
		return $string;
	}
	function plugin_addline_inline()
	{
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}
		static $no_flag = array();
		if (!isset($no_flag[$this->xpwiki->pid])) {$no_flag[$this->xpwiki->pid] = 0;}
		
		if (!array_key_exists($this->root->vars['page'],$numbers[$this->xpwiki->pid]))
		{
			$numbers[$this->xpwiki->pid][$this->root->vars['page']] = 0;
		}
		$addline_no = $numbers[$this->xpwiki->pid][$this->root->vars['page']]++;
		
		$above = $this->config['ADDLINE_INS'];
		$configname = 'default';
		$btn_text = $this->msg['btn_submit'];
		if ( func_num_args() ){
			$args =func_get_args();
			$opt = array_pop($args);
			$btn_text = $opt ? $opt : $btn_text;
			foreach ( $args as $opt ){
				if ( $opt === 'before' ){
					$above = 3;
				}
				else if ( $opt === 'after' ){
					$above = 2;
				}
				else if ( $opt === 'above' || $opt === 'up' ){
					$above = 1;
				}
				else if ( $opt === 'below' || $opt === 'down' ){
					$above = 0;
				}
				else if ( $opt === 'number' ){
					$no_flag[$this->xpwiki->pid] = 1;
				}
				else if ( $opt === 'nonumber' ){
					$no_flag[$this->xpwiki->pid] = 0;
				}
				else {
					$configname = $opt;
				}
			}
			if ( $no_flag[$this->xpwiki->pid] == 1 ) $btn_text .= "[$addline_no]";
		}
	
		$f_page	  = rawurlencode($this->root->vars['page']);
		$f_config = rawurlencode($configname);
	
		if ($this->cont['PKWK_READONLY']) {
			$string = $btn_text;
		} else {
			$string = <<<EOD
<a href="{$this->root->script}?plugin=addline&amp;addline_inno=$addline_no&amp;above=$above&amp;refer=$f_page&amp;configname=$f_config&amp;digest={$this->root->digest}">$btn_text</a>
EOD;
		}
		return $string;
	}
	function plugin_addline_action()
	{
		if ($this->cont['PKWK_READONLY']) {
			return array(
				'msg' => $this->root->_msg_not_editable,
				'body' => ''
			);
		}
		
		$refer		   = $this->root->vars['refer'];
		$postdata_old  = $this->func->get_source($refer);
		$configname = $this->root->vars['configname'];
		$above		= $this->root->vars['above'];
	
		$block_plugin = 1;
		if ( array_key_exists('addline_inno', $this->root->vars) ) {
			$addline_no = $this->root->vars['addline_inno'];
			$block_plugin = 0;
		}
		else if ( array_key_exists('addline_no', $this->root->vars) ) {
			$addline_no = $this->root->vars['addline_no'];
		}
		
		
		$config = new XpWikiConfig($this->xpwiki, 'plugin/addline/'.$configname);
		if (!$config->read())
		{
			return array( 'msg' => 'addline error', 'body' => "<p>config file '".htmlspecialchars($configname)."' is not exist.");
		}
		$config->config_name = $configname;
		$addline = join('', $this->addline_get_source($config->page));
		$addline = rtrim($addline);
		if ( $block_plugin ){
			$postdata = $this->addline_block($addline,$postdata_old,$addline_no,$above);
			if ($postdata === FALSE) {
				return array(
					'msg' => $this->root->_msg_not_editable,
					'body' => ''
				);
			}
		}
		else {
			$postdata = $this->addline_inline($addline,$postdata_old,$addline_no,$above);
		}
	
		$title = $this->root->_title_updated;
		$body = '';
		if ($this->func->get_digests(@join('',$postdata_old)) != $this->root->vars['digest'])
		{
			$title = $this->msg['title_collided'];
			$body  = $this->msg['msg_collided'] . $this->func->make_pagelink($refer);
		}
		
		
	//	$body = $postdata; // debug
	//	foreach ( $vars as $k=>$v ){$body .= "[$k:$v]&br;";}
		$this->func->page_write($refer,$postdata);
		
		$retvars['msg'] = $title;
		$retvars['body'] = $body;
		return $retvars;
	}
	function addline_block($addline,$postdata_old,$addline_no,$above)
	{
		$postdata = '';
		$addline_ct = 0;
		foreach ($postdata_old as $line)
		{
			if (!$above)	$postdata .= $line;
			if (preg_match('/(?:^|\|(?:(?:LEFT|RIGHT|CENTER):)?)#addline(?:\(([^\)]+)\))?/', $line, $match) && $addline_ct++ == $addline_no)
			{
				if (isset($match[1])) {
					foreach(explode(',', $match[1]) as $option) {
						$option = trim($option, ' "');
						if ($option && preg_match('/(?:btn\:(?:.+\:)?auth|auth)/i', $option) && 
							(! $this->func->check_editable($this->root->vars['refer'], FALSE, FALSE))
						) {
							return false;
						}
					}
					
				}
				$postdata = rtrim($postdata)."\n$addline\n";
			}
			if ($above) $postdata .= $line;
		}
		return $postdata;
	}
	function addline_inline($addline,$postdata_old,$addline_no,$above)
	{
		$postdata = '';
		$addline_ct = 0;
		$skipflag = 0;
		foreach ($postdata_old as $line)
		{
			if ( $skipflag || substr($line,0,1) == ' ' || substr($line,0,2) == '//' ){
				$postdata .= $line;
				continue;
			}
			$ct = preg_match_all('/&addline\([^();]*\)({[^{};]*})?;/',$line, $out);
			if ( $ct ){
				for($i=0; $i < $ct; $i++){
					if ($addline_ct++ == $addline_no ){
						if ( $above == 3 ){ // before
							$line = preg_replace('/(&addline\([^();]*\)({[^{};]*})?;)/', $addline.'$1',$line,1);
						}
						else if ( $above == 2 ){ //after
							$line = preg_replace('/(&addline\([^();]*\)({[^{};]*})?;)/','$1'.$addline,$line,1);
						}
						else if ( $above == 1 ){ // above
							$line = $addline . "\n" . $line;
						}
						else if ( $above == 0 ){ //below
							$line .= $addline . "\n";
						}
						$skipflag = 1;
						break;
					}
					else if ( $above == 2 || $above == 3 ){
						$line = preg_replace('/&addline(\([^();]*\)({[^{};]*})?);/','&___addline$1___;',$line,1);
					}
				}
				if ( $above == 2 || $above == 3 ){
					$line = preg_replace('/&___addline(\([^();]*\)({[^{};]*})?)___;/','&addline$1;',$line);
				}
			}
			$postdata .= $line;
		}
		return $postdata;
	}
	function addline_get_source($page) // tracker.inc.phpのtracker_listから
	{
		$source = $this->func->get_source($page);
		
		// テンプレート用にソースをクリーンアップ
		$this->func->cleanup_template_source($source);
		
		return $source;
	}
}
?>