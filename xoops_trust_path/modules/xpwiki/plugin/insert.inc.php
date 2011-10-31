<?php
class xpwiki_plugin_insert extends xpwiki_plugin {
	function plugin_insert_init () {


	// $Id: insert.inc.php,v 1.7 2008/11/26 23:42:04 nao-pon Exp $
	//
	// Text inserting box plugin
	
		$this->cont['INSERT_COLS'] =  70; // Columns of textarea
		$this->cont['INSERT_ROWS'] =   5; // Rows of textarea
		$this->cont['INSERT_INS'] =    1; // Order of insertion (1:before the textarea, 0:after)

	}
	
	function plugin_insert_action()
	{
	//	global $script, $vars, $cols, $rows;
	//	global $_title_collided, $_msg_collided, $_title_updated;
	
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
		if (! isset($this->root->vars['msg']) || $this->root->vars['msg'] == '') return;
	
		$this->root->vars['msg'] = preg_replace('/' . "\r" . '/', '', $this->root->vars['msg']);
		$insert = ($this->root->vars['msg'] != '') ? "\n" . $this->root->vars['msg'] . "\n" : '';
	
		$postdata = '';
		$postdata_old  = $this->func->get_source($this->root->vars['refer']);
		$insert_no = 0;
	
		foreach($postdata_old as $line) {
			if (! $this->cont['INSERT_INS']) $postdata .= $line;
			if (preg_match('/^#insert(?:\([^)]*\))?$/i', $line)) {
				if ($insert_no == $this->root->vars['insert_no'])
					$postdata .= $insert;
				$insert_no++;
			}
			if ($this->cont['INSERT_INS']) $postdata .= $line;
		}
		$postdata_input = $insert . "\n";
	
		$body = '';
		if ($this->func->get_digests($this->func->get_source($this->root->vars['refer'], TRUE, TRUE)) !== $this->root->vars['digest']) {
			$title = $this->root->_title_collided;
			$body = $this->root->_msg_collided . "\n";
	
			$s_refer  = htmlspecialchars($this->root->vars['refer']);
			$s_digest = htmlspecialchars($this->root->vars['digest']);
			$s_postdata_input = htmlspecialchars($postdata_input);
			$script = $this->func->get_script_uri();
			$body .= <<<EOD
<form action="{$script}?cmd=preview" method="post">
 <div>
  <input type="hidden" name="refer"  value="$s_refer" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="{$this->root->rows}" cols="{$this->root->cols}" id="textarea">$s_postdata_input</textarea><br />
 </div>
</form>
EOD;
		} else {
			$this->func->page_write($this->root->vars['refer'], $postdata);
	
			$title = $this->root->_title_updated;
		}
		$retvars['msg']  = $title;
		$retvars['body'] = $body;
	
		$this->root->vars['page'] = $this->root->vars['refer'];
	
		return $retvars;
	}
	
	function plugin_insert_convert()
	{
	//	global $script, $vars, $digest;
	//	global $_btn_insert;
	//	static $numbers = array();
		static $numbers = array();
		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}
	
		if ($this->cont['PKWK_READONLY']) return ''; // Show nothing
	
		if (! isset($numbers[$this->xpwiki->pid][$this->root->vars['page']])) $numbers[$this->xpwiki->pid][$this->root->vars['page']] = 0;
	
		$insert_no = $numbers[$this->xpwiki->pid][$this->root->vars['page']]++;
	
		$s_page   = htmlspecialchars($this->root->vars['page']);
		$s_digest = htmlspecialchars($this->root->digest);
		$s_cols = $this->cont['INSERT_COLS'];
		$s_rows = $this->cont['INSERT_ROWS'];
		$script = $this->func->get_script_uri();
		$string = <<<EOD
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="insert_no" value="$insert_no" />
  <input type="hidden" name="refer"  value="$s_page" />
  <input type="hidden" name="plugin" value="insert" />
  <input type="hidden" name="digest" value="$s_digest" />
  <textarea name="msg" rows="$s_rows" cols="$s_cols"></textarea>
  <div><input type="submit" name="insert" value="{$this->root->_btn_insert}" /></div>
 </div>
</form>
EOD;
	
		return $string;
	}
}
?>