<?php
class xpwiki_plugin_memo extends xpwiki_plugin {
	function plugin_memo_init () {


	// $Id: memo.inc.php,v 1.6 2009/05/02 04:13:27 nao-pon Exp $
	//
	// Memo box plugin
	
		$this->cont['MEMO_COLS'] =  60; // Columns of textarea
		$this->cont['MEMO_ROWS'] =   5; // Rows of textarea

	}
	
	function plugin_memo_action()
	{
	//	global $script, $vars, $cols, $rows;
	//	global $_title_collided, $_msg_collided, $_title_updated;
	
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
		if (! isset($this->root->vars['msg']) || $this->root->vars['msg'] == '') return;
	
		$memo_body = preg_replace('/' . "\r" . '/', '', $this->root->vars['msg']);
		$memo_body = str_replace("\n", '\n', $memo_body);
		$memo_body = str_replace('"', '&#x22;', $memo_body); // Escape double quotes
		$memo_body = str_replace(',', '&#x2c;', $memo_body); // Escape commas
	
		$postdata_old  = $this->func->get_source($this->root->vars['refer']);
		$postdata = '';
		$memo_no = 0;
		foreach($postdata_old as $line) {
			if (preg_match("/^#memo\(?.*\)?$/i", $line)) {
				if ($memo_no == $this->root->vars['memo_no']) {
					$postdata .= '#memo(' . $memo_body . ')' . "\n";
					$line = '';
				}
				++$memo_no;
			}
			$postdata .= $line;
		}
	
		$postdata_input = $memo_body . "\n";
	
		$body = '';
		if ($this->func->get_digests($this->func->get_source($this->root->vars['refer'], TRUE, TRUE)) !== $this->root->vars['digest']) {
			$title = $this->root->_title_collided;
			$body  = $this->root->_msg_collided . "\n";
	
			$s_refer  = htmlspecialchars($this->root->vars['refer']);
			$s_digest = htmlspecialchars($this->root->vars['digest']);
			$s_postdata_input = htmlspecialchars($postdata_input);
			$script = $this->func->get_script_uri();
			$body .= <<<EOD
<form action="{$script}?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer"  value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" class="norich" rows="{$this->root->rows}" cols="{$this->root->cols}" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>
EOD;
		} else {
			$this->func->page_write($this->root->vars['refer'], $postdata);
	
			$title = $this->root->_title_updated;
		}
		$retvars['msg']  = & $title;
		$retvars['body'] = & $body;
	
		$this->root->vars['page'] = $this->root->vars['refer'];
	
		return $retvars;
	}
	
	function plugin_memo_convert()
	{
	//	global $script, $vars, $digest;
	//	global $_btn_memo_update;
	//	static $numbers = array();
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}
	
		if (! isset($numbers[$this->xpwiki->pid][$this->root->vars['page']])) $numbers[$this->xpwiki->pid][$this->root->vars['page']] = 0;
		$memo_no = $numbers[$this->xpwiki->pid][$this->root->vars['page']]++;
	
		$data = func_get_args();
		$data = implode(',', $data);	// Care all arguments
		$data = str_replace('&#x2c;', ',', $data); // Unescape commas
		$data = str_replace('&#x22;', '"', $data); // Unescape double quotes
		$data = htmlspecialchars(str_replace('\n', "\n", $data));
	
		if ($this->cont['PKWK_READONLY']) {
			$_script = '';
			$_submit = '';	
		} else {
			$_script = $this->func->get_script_uri();
			$_submit = '<input type="submit" name="memo"    value="' . $this->root->_btn_memo_update . '" />';
		}
	
		$s_page   = htmlspecialchars($this->root->vars['page']);
		$s_digest = htmlspecialchars($this->root->digest);
		$s_cols   = $this->cont['MEMO_COLS'];
		$s_rows   = $this->cont['MEMO_ROWS'];
		$string   = <<<EOD
<form action="$_script" method="post" class="memo">
 <div>
  <input type="hidden" name="memo_no" value="$memo_no" />
  <input type="hidden" name="refer"   value="$s_page" />
  <input type="hidden" name="plugin"  value="memo" />
  <input type="hidden" name="digest"  value="$s_digest" />
  <textarea name="msg" class="norich" rows="$s_rows" cols="$s_cols">$data</textarea>
  <div>$_submit</div>
 </div>
</form>
EOD;
	
		return $string;
	}
}
?>