<?php
//
// Created on 2006/10/20 by nao-pon http://hypweb.net/
// $Id: skin_changer.inc.php,v 1.7 2011/11/26 12:03:10 nao-pon Exp $
//
class xpwiki_plugin_skin_changer extends xpwiki_plugin {
	function plugin_skin_changer_init () {

	}

	function plugin_skin_changer_convert() {
		$skins = $t_skins = array();
		// SKIN Dirctory
		$base = $this->cont['DATA_HOME'] .'skin/';
		if ($dir = opendir($base)) {
			$nomatch = array('.', '..', 'js');
			$nomatch[] = preg_replace("#.*/([^/]+)/$#", "$1", $this->cont['TDIARY_DIR']);
			while (false !== ($file = readdir($dir))) {
				if (is_dir($base.'/'.$file)
				 && !in_array($file, $nomatch)
				 && is_file("{$base}/{$file}/pukiwiki.skin.php")) {
					$skins[$file] = $file;
				}
			}
		}

		// tDiary Dirctory
		$base = $this->cont['DATA_HOME'] . $this->cont['TDIARY_DIR'];
		if ($dir = opendir($base)) {
			$nomatch = array('.', '..');
			while (false !== ($file = readdir($dir))) {
				if (is_dir($base.'/'.$file)
				 && !in_array($file, $nomatch)
				 && is_file("{$base}/{$file}/{$file}.css")) {
					$t_skins[$file] = 'tD-'.$file;
				}
			}
		}
		
		ksort($skins);
		ksort($t_skins);
		
		$ret = '<p><ul class="list1" style="padding-left:'.$this->root->_ul_margin.'px;margin-left:'.$this->root->_ul_margin.'px;">'."\n";
		
		$now_query = @ $_SERVER['QUERY_STRING'];
		// 特定のキーを除外
		$now_query = preg_replace('/&?(word|'.preg_quote(session_name(), '/').')=[^&]+/', '', $now_query);
		$now_query = preg_replace("/&+$/", "", $now_query);
		
		$link = (empty($now_query))? "setskin=" : "{$now_query}&setskin="; 
		foreach ($skins as $name=>$skin) {
			if ($skin == $this->root->cookie['skin'] && ($this->root->pagecache_min === 0 || $this->root->userinfo['uid'] !== 0)) {
				$ret .= '<li style="font-weight:bold;">'.htmlspecialchars($name).'</li>'."\n";
			} else {
				$ret .= '<li><a href="?'.htmlspecialchars($link.$skin).'" title="Change skin">'.htmlspecialchars($name).'</a></li>'."\n";
			}
		}
		$ret .= '<li>t-Diary Skins'."\n";
		$ret .= '<ul class="list2" style="padding-left:'.$this->root->_ul_margin.'px;margin-left:'.$this->root->_ul_margin.'px;">'."\n";
		foreach ($t_skins as $name=>$skin) {
			if ($skin == $this->root->cookie['skin'] && ($this->root->pagecache_min === 0 || $this->root->userinfo['uid'] !== 0)) {
				$ret .= '<li style="font-weight:bold;">'.htmlspecialchars($name).'</li>'."\n";
			} else {
				$ret .= '<li><a href="?'.htmlspecialchars($link.$skin).'" title="Change skin">'.htmlspecialchars($name).'</a></li>'."\n";
			}
		}
		$ret .= '</ul></li>'."\n";
		$ret .="</ul></p>\n"."\n";
		return $ret;
	}
	
	function plugin_skin_changer_inline() {
		// 引数の数をチェック
		$argv = func_get_args();
		$text = array_pop($argv);
		$name = @$argv[0];
		
		if (!$text) {
			$text = htmlspecialchars($name);
		}
		
		if (!$name && !$text) { return false; }
	
		if($name && !preg_match('/^[\w-]+$/', $name)) {
			return false;
		}
		
		/*
		$now_query = @ $_SERVER['QUERY_STRING'];
		$now_query = preg_replace("/&+$/", "", $now_query);
		
		$querys = explode('&', $now_query);
		$allow_keys = array('cmd', 'page');
		$now_query = '&';
		if ($querys) {
			foreach($querys as $query) {
				list($key, $val) = array_pad(explode('=',$query), 2, '');
				if ($val === '') {
					$now_query .= $key.'&'; 
				} else {
					if (in_array($key, $allow_keys)) {
						$now_query .= $key.'='.$val.'&'; 
					}
				}
			}		
		}
		$now_query = preg_replace("/(^&+|&+$)/", "", $now_query);
		*/
		
		$now_query = rawurlencode($this->root->vars['page']);
		
		$link = (empty($now_query))? "setskin={$name}" : "{$now_query}&setskin={$name}"; 
		
		if ($name == $this->root->cookie['skin'] && ($this->root->pagecache_min === 0 || $this->root->userinfo['uid'] !== 0)) {
			return '<span style="font-weight:bold;">'.$text.'</span>';
		} else {
			return '<a href="'.$this->root->script.'?'.str_replace("&","&amp;",$link).'" title="Change skin">'.$text.'</a>';
		}
	}

}
?>