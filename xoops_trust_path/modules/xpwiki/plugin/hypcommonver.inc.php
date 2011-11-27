<?php
//
// Created on 2006/11/28 by nao-pon http://hypweb.net/
// $Id: hypcommonver.inc.php,v 1.3 2011/11/26 12:03:10 nao-pon Exp $
//
class xpwiki_plugin_hypcommonver extends xpwiki_plugin {

	function plugin_hypcommonver_init() {
		return $this->time_limit = 1800; // 30 min
	}

	function plugin_hypcommonver_convert() {
		return '<p>' . $this->get_ver() . '</p>';
	}


	function plugin_hypcommonver_inline() {
		return $this->get_ver();
	}
	
	function get_ver() {
		
		$c_file = $this->cont['CACHE_DIR'] . 'plugin/hypcommonver.dat';
		
		if (is_file($c_file) && filemtime($c_file) + $this->time_limit > $this->cont['UTC']) {
			return file_get_contents($c_file);
		}
		
		$url = 'http://sourceforge.jp/cvs/view/hypweb/XOOPS_TRUST/class/hyp_common/version.php?view=co&content-type=text%2Fplain';
		
		$dat = $this->func->http_request($url);
		
		$ver = '[Error]';

		if ($dat['rc'] === 200) {
			$data = $dat['data'];

			if (preg_match('/\$version\s*=\s*\'([0-9.]+)/', $data, $match)) {
				$ver = $match[1];
			}
	
		}
		
		if ($fp = fopen($c_file,"wb")) {
			fputs($fp, $ver);
			fclose($fp);
		}
		
		return $ver;		
	}
}
?>