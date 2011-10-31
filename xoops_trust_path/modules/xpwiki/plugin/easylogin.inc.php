<?php
/*
 * Created on 2008/06/20 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: easylogin.inc.php,v 1.4 2009/03/20 06:17:38 nao-pon Exp $
 */

class xpwiki_plugin_easylogin extends xpwiki_plugin
{
	function plugin_easylogin_inline () {
		return $this->get_a_tag ();
	}

	function plugin_easylogin_convert () {
		$a = $this->get_a_tag ();
		
		if ($a = $this->get_a_tag()) {
			return '[ ' . $a . ' ]';
		} else {
			return '';
		}
	}
	
	function get_a_tag () {
		if (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER && HypCommonFunc::get_version() >= '20080620') {
			HypCommonFunc::loadClass('HypKTaiRender');
			$r = new HypKTaiRender();
			$r->set_myRoot(XOOPS_URL);
			
			$msg['easylogin'] = ($this->root->k_tai_conf['msg']['easylogin'])? $this->root->k_tai_conf['msg']['easylogin'] : 'Easy Login';
			$msg['logout'] = ($this->root->k_tai_conf['msg']['logout'])? $this->root->k_tai_conf['msg']['logout'] : 'Logout';
			
			if (! empty($r->vars['ua']['isKTai'])) {
				if (! $this->root->userinfo['uid']) {
					$add = '_EASYLOGIN';
					if ($r->vars['ua']['carrier'] === 'docomo') {
						$add .= '&guid=ON';
					}
					$url = $r->myRoot . $r->removeSID($_SERVER['REQUEST_URI']);
					$url .= ((strpos($url, '?') === FALSE)? '?' : '&') . $add;
					$url = str_replace('&', '&amp;', $url);
					return '<a href="' . $url . '">' . $msg['easylogin'] . '</a>';
				} else {
					$guid = ($r->vars['ua']['carrier'] === 'docomo')? '&amp;guid=ON' : '';
					return '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $this->root->userinfo['uid'] . $guid . '">' . $this->root->userinfo['uname_s'] . '</a> <a href="' . XOOPS_URL . '/user.php?op=logout">' . $msg['logout'] . '</a>';
				}
			}
		}
		return '';
	}
}