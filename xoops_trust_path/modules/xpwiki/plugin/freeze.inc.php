<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: freeze.inc.php,v 1.9 2010/01/08 13:47:11 nao-pon Exp $
//
// Freeze(Lock) plugin

// Reserve 'Do nothing'. '^#freeze' is for internal use only.

class xpwiki_plugin_freeze extends xpwiki_plugin {
	function plugin_freeze_init () {

	}
	function plugin_freeze_convert() { return ''; }

	function plugin_freeze_action()
	{
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		if (! $this->root->function_freeze || ! $this->func->is_page($page))
			return array('msg' => '', 'body' => '');

		$pass = isset($this->root->vars['pass']) ? $this->root->vars['pass'] : NULL;
		$msg = $body = '';
		if ($this->func->is_freeze($page)) {
			// Freezed already
			$msg  = & $this->root->_title_isfreezed;
			$body = str_replace('$1', $this->func->make_pagelink($page), $this->root->_title_isfreezed);

		} else if ($this->func->is_owner($page) || ($pass !== NULL && $this->func->pkwk_login($pass))) {
			// Freeze
			$postdata = $this->func->get_source($page);
			array_unshift($postdata, "#freeze\n");
			$this->root->rtf['no_checkauth_on_write'] = true;
			$this->func->file_write($this->cont['DATA_DIR'], $page, join('', $postdata), TRUE);

			// pginfo DB write
			$this->func->pginfo_freeze_db_write($page, 1);

			// Update
			$this->func->is_freeze($page, TRUE);

			$msg = $this->root->_title_freezed;

		} else {
			// Show a freeze form
			$msg    = & $this->root->_title_freeze;
			$s_page = htmlspecialchars($page);
			$script = $this->func->get_script_uri();
			$body   = ($pass === NULL) ? '' : "<p><strong>{$this->root->_msg_invalidpass}</strong></p>\n";
			$body  .= <<<EOD
<p>{$this->root->_msg_freezing}</p>
<form action="{$script}" method="post">
 <div>
  <input type="hidden"   name="cmd"  value="freeze" />
  <input type="hidden"   name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="{$this->root->_btn_freeze}" />
 </div>
</form>
EOD;
		}
		return array('msg'=>$msg, 'body'=>$body);
	}
}
?>