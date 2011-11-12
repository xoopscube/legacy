<?php
// $Id: template.inc.php,v 1.4 2008/11/17 02:34:23 nao-pon Exp $
//
// Load template plugin

class xpwiki_plugin_template extends xpwiki_plugin {
	function plugin_template_init () {

		$this->cont['MAX_LEN'] =  60;

	}
	
	function plugin_template_action()
	{
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');
		if (! isset($this->root->vars['refer']) || ! $this->func->is_page($this->root->vars['refer']) || ! $this->func->check_readable($this->root->vars['refer'], FALSE, FALSE))
			return FALSE;
	
		$lines = $this->func->get_source($this->root->vars['refer']);
		$this->func->cleanup_template_source($lines);
		while(isset($lines[0]) && trim($lines[0], "\r\n") === '') {
			array_shift($lines);
		}
	
		$begin = (isset($this->root->vars['begin']) && is_numeric($this->root->vars['begin'])) ? $this->root->vars['begin'] : 0;
		$end   = (isset($this->root->vars['end'])   && is_numeric($this->root->vars['end']))   ? $this->root->vars['end'] : count($lines) - 1;
		if ($begin > $end) {
			$temp  = $begin;
			$begin = $end;
			$end   = $temp;
		}
		$page    = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		$is_page = $this->func->is_page($page);
	
		// edit
		if ($is_pagename = $this->func->is_pagename($page) && (! $is_page || ! empty($this->root->vars['force']))) {
			
			$this->func->check_editable($page, true, true);
			
			// Q & A Ç§¾Ú
			$edit =& $this->func->get_plugin_instance('edit');
			$options = $edit->get_riddle();
			
			$postdata       = join('', array_splice($lines, $begin, $end - $begin + 1));
			$retvar['msg']  = $this->root->_title_edit;
			$retvar['body'] = $this->func->edit_form($this->root->vars['page'], $postdata, FALSE, FALSE, $options);
			$this->root->vars['refer']  = $this->root->vars['page'];
			return $retvar;
		}
		$begin_select = $end_select = '';
		for ($i = 0; $i < count($lines); $i++) {
			$line = htmlspecialchars(mb_strimwidth($lines[$i], 0, $this->cont['MAX_LEN'], '...'));
	
			$tag = ($i == $begin) ? ' selected="selected"' : '';
			$begin_select .= "<option value=\"$i\"$tag>$line</option>\n";
	
			$tag = ($i == $end) ? ' selected="selected"' : '';
			$end_select .= "<option value=\"$i\"$tag>$line</option>\n";
		}
	
		$_page = htmlspecialchars($page);
		$msg = $tag = '';
		if ($is_page) {
			$msg = $this->root->_err_template_already;
			$tag = '<input type="checkbox" name="force" value="1" />'.$this->root->_msg_template_force;
		} else if ($page !== '' && ! $is_pagename) {
			$msg = str_replace('$1', $_page, $this->root->_err_template_invalid);
		}
	
		$s_refer = htmlspecialchars($this->root->vars['refer']);
		$s_page  = ($page === '') ? str_replace('$1', $s_refer, $this->root->_msg_template_page) : $_page;
		$script = $this->func->get_script_uri();
		$ret     = <<<EOD
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="template" />
  <input type="hidden" name="refer"  value="$s_refer" />
  {$this->root->_msg_template_start} <select name="begin" size="10">$begin_select</select><br /><br />
  {$this->root->_msg_template_end}   <select name="end"   size="10">$end_select</select><br /><br />
  <label for="_p_template_refer">{$this->root->_msg_template_refer}</label>
  <input type="text" name="page" id="_p_template_refer" value="$s_page" />
  <input type="submit" name="submit" value="{$this->root->_btn_template_create}" /> $tag
 </div>
</form>
EOD;
	
		$retvar['msg']  = ($msg == '') ? $this->root->_title_template : $msg;
		$retvar['body'] = $ret;
	
		return $retvar;
	}
}
?>