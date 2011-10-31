<?php
//
// Created on 2006/10/15 by nao-pon http://hypweb.net/
// $Id: base_func.php,v 1.11 2007/09/20 13:08:10 nao-pon Exp $
//
class XpWikiBaseFunc {
	
	// xpWiki functions.
	// This will be overwrited by XpwikiXoopsWapper class.
		
	function get_zonetime () {
		return date("Z");	
	}
	
	function set_moduleinfo () {
		
		$this->root->module['name'] = 'xpWiki';
		$this->root->module['version'] = '0.5';
		$this->root->module['credits'] = '&copy; 2006- hypweb.net';
		$this->root->module['author'] = 'nao-pon';
		$this->root->module['platform'] = 'standalone';
		$this->root->enable_pagecomment = FALSE;
	}
	
	function set_siteinfo () {
		$this->root->siteinfo['root_url'] = '';
		$this->root->siteinfo['site_name'] = '';
	}
	
	function set_userinfo () {
		$this->root->userinfo['admin'] = FALSE;
		$this->root->userinfo['uid'] = 0;
		$this->root->userinfo['email'] = '';
		$this->root->userinfo['uname'] = '';
		$this->root->userinfo['uname_s'] = '';
		$this->root->userinfo['gids'] = array();
	}

	function get_userinfo_by_id ($uid, $defname=NULL) {
		if (is_null($defname)) {
			$defname = $this->root->anonymous;
		}
		$result = array(
			'admin' => FALSE,
			'uid' => 0,
			'uname' => $defname,
			'uname_s' => htmlspecialchars($defname),
			'email' => '',
			'gids' => array(),
			);
		return $result;
	}
	
	function get_lang ($default) {
		return $default;
	}
	
		// 追加 フェイスマーク 取得
	function get_extra_facemark() {
		return array();
	}
}
?>