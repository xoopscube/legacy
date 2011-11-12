<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: unfreeze.inc.php,v 1.9 2010/01/08 13:47:12 nao-pon Exp $
//
// Unfreeze(Unlock) plugin

// Show edit form when unfreezed

class xpwiki_plugin_unfreeze extends xpwiki_plugin {
	function plugin_unfreeze_init () {

		$this->cont['PLUGIN_UNFREEZE_EDIT'] =  TRUE;

	}

	function plugin_unfreeze_action()
	{
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';
		if (! $this->root->function_freeze || ! $this->func->is_page($page))
			return array('msg' => '', 'body' => '');

		$pass = isset($this->root->vars['pass']) ? $this->root->vars['pass'] : NULL;
		$redirect = $msg = $body = '';
		if (! $this->func->is_freeze($page)) {
			// Unfreezed already
			$msg  = & $this->root->_title_isunfreezed;
			$body = str_replace('$1', $this->func->make_pagelink($page), $this->root->_title_isunfreezed);

		} else if ($this->func->is_owner($page) || ($pass !== NULL && $this->func->pkwk_login($pass))) {
			// Unfreeze
			$postdata = $this->func->get_source($page);
			array_shift($postdata);
			$postdata = join('', $postdata);
			$this->root->rtf['no_checkauth_on_write'] = true;
			$this->func->file_write($this->cont['DATA_DIR'], $page, $postdata, TRUE);

			// pginfo DB write
			$this->func->pginfo_freeze_db_write ($page, 0);

			// Update
			$this->func->is_freeze($page, TRUE);

			if ($this->cont['PLUGIN_UNFREEZE_EDIT']) {
				$msg  = $this->root->_title_unfreezed;
				$redirect = $this->root->script . '?cmd=edit&page=' . rawurlencode($page);
			} else {
				$msg  = $this->root->_title_unfreezed;
			}

		} else {
			// Show unfreeze form
			$msg    = & $this->root->_title_unfreeze;
			$s_page = htmlspecialchars($page);
			$script = $this->func->get_script_uri();
			$body   = ($pass === NULL) ? '' : "<p><strong>{$this->root->_msg_invalidpass}</strong></p>\n";
			$body  .= <<<EOD
<p>{$this->root->_msg_unfreezing}</p>
<form action="{$script}" method="post">
 <div>
  <input type="hidden"   name="cmd"  value="unfreeze" />
  <input type="hidden"   name="page" value="$s_page" />
  <input type="password" name="pass" size="12" />
  <input type="submit"   name="ok"   value="{$this->root->_btn_unfreeze}" />
 </div>
</form>
EOD;
		}

		return array('msg'=>$msg, 'body'=>$body, 'redirect'=>$redirect);
	}
}
?>