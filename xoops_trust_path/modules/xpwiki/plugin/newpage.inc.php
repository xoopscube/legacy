<?php
// $Id: newpage.inc.php,v 1.12 2011/07/29 01:38:37 nao-pon Exp $
//
// Newpage plugin

class xpwiki_plugin_newpage extends xpwiki_plugin {
	function plugin_newpage_init () {
		$this->conf['listmax'] = 200;
	}

	function plugin_newpage_convert()
	{
		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;}

		if ($this->cont['PKWK_READONLY'] === 1) return ''; // Show nothing

		$newpage = '';
		if (func_num_args()) list($newpage, $default) = array_pad(func_get_args(), 2, '');
		if (strtolower($newpage) === 'this') {
			$newpage = $this->root->vars['page'] . '/';
		}
		if ($default) {
			if ($default === '$uname') {
				$default = $this->cont['USER_NAME_REPLACE'];
			} else {
				$default = htmlspecialchars($default);
			}
		}

		if (! preg_match('/^' . $this->root->BracketName . '$/', $newpage)) $newpage = '';


		$s_page = htmlspecialchars(isset($this->root->vars['refer']) ? $this->root->vars['refer'] : $this->root->vars['page']);

		++$id[$this->xpwiki->pid];
		$base_form = $newpage? $this->get_base_form($newpage, $id[$this->xpwiki->pid]) : '<label for="_p_newpage_'.$id[$this->xpwiki->pid].'">' . $this->root->_msg_newpage . ': </label>';

		$script = $this->func->get_script_uri();
		$ret = <<<EOD
<form action="{$script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="newpage" />
  <input type="hidden" name="refer" value="$s_page" />
  $base_form
  <input type="text" value="{$default}" name="page" id="_p_newpage_{$id[$this->xpwiki->pid]}" value="" size="30" />
  <input type="submit" value="{$this->root->_btn_edit}" />
 </div>
</form>
EOD;

		return $ret;
	}

	function plugin_newpage_action()
	{

		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');

		if ($this->root->vars['page'] === '') {
			$base = (empty($this->root->vars['base']))? '' : rtrim($this->root->vars['base'], '/') . '/';
			$retvars['msg']  = $this->root->_msg_newpage;
			$retvars['body'] = $this->plugin_newpage_convert($base);
			return $retvars;
		} else {
			$base = (empty($this->root->vars['base']))? '' : rtrim($this->root->vars['base'], '/') . '/';
			$page    = $base . $this->func->pagename_normalize($this->func->strip_bracket($this->root->vars['page']));
			$r_page  = rawurlencode(isset($this->root->vars['refer']) ?
				$this->func->get_fullname($page, $this->root->vars['refer']) : $page);
			$r_refer = rawurlencode($this->root->vars['refer']);

			$this->func->send_location('', '', $this->func->get_script_uri() .
			'?cmd=read&page=' . $r_page . '&refer=' . $r_refer);
		}
	}

	function get_base_form($base, $id) {
		$base = rtrim($base, '/');
		if ($this->conf['listmax'] > 1) {
			$options = array(
				'order' => ' ORDER BY `editedtime` DESC ',
				'limit' => $this->conf['listmax'] - 1
			);
			$pages = $this->func->get_existpages(FALSE, $base . '/', $options);
			natcasesort($pages);
		} else {
			$pages = array();
		}

		$form = array();
		$base = htmlspecialchars($base) . '/';
		if (count($pages) < 1) {
			$form[] = '<input type="hidden" name="base" value="' . $base . '" />';
			$form[] = '<label for="_p_newpage_'.$id.'">'.$base.'</label>';
		} else {
			$form[] = '<select name="base" size="1" onchange="$(\'_p_newpage_'.$id.'\').focus();">';
			$form[] = '<option selected="selected">' . $base . '</option>';
			foreach($pages as $page) {
				$form[] = '<option>' . htmlspecialchars($page) . '/</option>';
			}
			$form[] = '</select>';
		}
		return join("\n", $form);
	}
}
?>