<?php
//
// $Id: chat.inc.php,v 1.4 2009/03/20 06:17:38 nao-pon Exp $
//
	
class xpwiki_plugin_chat extends xpwiki_plugin {
	function plugin_chat_init () {
		// [ja]
		// ajaxchat.htm があるディレクトリのURL
		// 末尾に / が必要。
		// 必ず環境に合わせて指定してください。
		// [/ja]
		
		// [en]
		// URL of directory with ajaxchat.htm
		// '/' is necessary for the end. 
		// Please specify it according to the environment. 
		// [/en]
		$this->config['url'] = '/ajaxchat/';
		
		
		// Default height
		$this->config['height'] = '350';
		
		// Message
		$this->msg['err_pgid'] = 'Error: "pgid" cannot be acquired.';
	}
	function plugin_chat_convert()
	{
		$this->root->replaces_finish['_uI_LANg_'] = $this->get_accept_language();
		$lang = '&amp;lang=_uI_LANg_';
		
		if ($this->root->userinfo['uname_s'] !== $this->root->siteinfo['anonymous']) {
			$uname_enc = rawurlencode(mb_convert_encoding($this->root->userinfo['uname_s'], 'UTF-8', $this->cont['SOURCE_ENCODING']));
		} else {
			$uname_enc = '';
		}
		$uname = '&amp;uname=_uNAMEuTF8eNCODE_';
		$this->root->replaces_finish['_uNAMEuTF8eNCODE_'] = $uname_enc;

		$pgid = $this->func->get_pgid_by_name($this->root->vars['page']);
		$chatid = $this->root->module['mid'] * 100000 + $pgid;
		
		$stay = '';
		$height = $this->config['height'];
		foreach(func_get_args() as $cmd)
		{
			$cmd = trim($cmd);
			if (substr(strtolower($cmd),0,9) === 'staypos:r'){$stay = '&amp;staypos=r';}
			if (preg_match('/height:([\d]+)(px)?/i', $cmd, $arg)) {
				$height = max(min($arg[1],500),150);
			}
			if (preg_match('/id:([\d]+)/i', $cmd, $arg)) {
				$chatid = $arg[1];
			}
		}
		
		return ($pgid)? '<iframe src="' . $this->config['url'] . 'ajaxchat.htm?id=' . $chatid . $stay . $lang . $uname . '" width="100%" height="' . $height . '" style="border:none;" frameborder="0" border="0" allowtransparency="true" scrolling="no"></iframe>' : $this->msg['err_pgid'];
	}

	// Get HTTP_ACCEPT_LANGUAGE
	function get_accept_language () {
		$accept = @ $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		// cookie に指定があればそれを優先
		if (!empty($this->root->cookie['lang'])) {
			$accept = $this->root->cookie['lang'] . "," . $accept;
		}
		if (!empty($accept))
		{
			if (preg_match_all("/([\w\-]+)/i",$accept,$match,PREG_PATTERN_ORDER)) {
				foreach($match[1] as $lang) {
					$lang = strtolower($lang);
					return $lang;
				}
			}
		}
		return $this->cont['LANG']; // 規定値
	}
}
