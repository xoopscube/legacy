<?php
/*
 * Created on 2008/10/09 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: build_js.inc.php,v 1.6 2011/09/26 12:06:26 nao-pon Exp $
 */

class xpwiki_plugin_build_js extends xpwiki_plugin {
	function plugin_build_js_init() {

	}

	function plugin_build_js_inline() {
		$args = func_get_args();
		$action = $args[0];
		switch ($action) {
		case 'refInsert':
			if (empty($args[1])) {
				return false;
			}
			if (empty($args[2])) {
				$args[2] = '';
			} else {
				list($args[2]) = explode('/', $args[2]);
			}
			if ($this->root->vars['refer'] !== $this->root->vars['base']) {
				$args[1] = $this->root->vars['refer'] . '/' . $args[1];
			}
			if ($this->root->vars['basedir'] !== $this->root->mydirname) {
				$args[1] = $this->root->mydirname . ':' . $args[1];
			}

			$obj = (empty($_GET['winop']))? 'parent' : 'opener.window';
			if (! empty($_GET['mode']) && $_GET['mode'] === 'fck') {
				$jsfunc = $obj . '.XpWiki.FCKrefInsert';
			} else {
				$jsfunc = $obj . '.XpWiki.refInsert';
			}

			if ($this->cont['UA_PROFILE'] === 'mobile') {
				$attr = ' data-role="button" data-icon="plus"';
			} else {
				$attr = ' class="button"';
			}

			return '<span'.$attr.' onclick="' . $jsfunc . '(\''.htmlspecialchars($args[1], ENT_QUOTES).'\',\''.$args[2].'\')">'.$this->root->_attach_messages['msg_insert'].'</span>';

			break;
		default :
			return false;
		}
	}
}
?>