<?php
/*
 * Created on 2008/10/09 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: build_js.inc.php,v 1.7 2012/01/03 04:54:57 nao-pon Exp $
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
		case 'attachDel':
			$page = isset($args[1])? $args[1] : '';
			$file = isset($args[2])? $args[2] : '';
			$age = isset($args[3])? $args[3] : '';
			$returi = isset($args[4])? $args[4] : '';
			if (!$page || !$file) return false;
			$param = '&amp;refer='.rawurlencode($page)
			       . ($age ? '&amp;age='.$age : '')
			       . '&amp;';
			$param .= 'file='.rawurlencode($file);
			if ($returi) $param .= '&amp;returi='.rawurlencode($returi);

			return '<a href="'.$this->root->script.'?plugin=attach&pcmd=delete'.$param.'" title="'.$this->root->_btn_delete.'" onclick="return confirm(\''.htmlspecialchars($file, ENT_QUOTES).': '.htmlspecialchars($this->root->_attach_messages['msg_delete'], ENT_QUOTES).'\')"><img src="'.$this->cont['LOADER_URL'].'?src=trash_16.gif" alt="'.$this->root->_btn_delete.'" /></a>';

			break;
		default :
			return false;
		}
	}
}
?>