<?php
//
// Created on 2006/11/28 by nao-pon http://hypweb.net/
// $Id: xpwikiver.inc.php,v 1.7 2011/11/26 12:03:10 nao-pon Exp $
//
class xpwiki_plugin_xpwikiver extends xpwiki_plugin {

	function plugin_xpwikiver_init() {
		return $this->time_limit = 1800; // 30 min
	}

	function plugin_xpwikiver_convert() {
		return '<p>' . $this->get_ver() . '</p>';
	}


	function plugin_xpwikiver_inline() {
		return $this->get_ver();
	}
	
	function get_ver() {
		
		$c_file = $this->cont['CACHE_DIR'] . 'plugin/xpwikiver.dat';
		
		if (is_file($c_file) && filemtime($c_file) + $this->time_limit > $this->cont['UTC']) {
			return file_get_contents($c_file);
		}
		
		$url = 'http://sourceforge.jp/cvs/view/hypweb/XOOPS_TRUST/modules/xpwiki/version.php?view=co&content-type=text%2Fplain';
		
		$dat = $this->func->http_request($url);
		
		$ver = '[Error]';

		if ($dat['rc'] === 200) {
			$data = $dat['data'];

			if (preg_match('/\$xpwiki_version\s*=\s*\'([0-9.]+)/', $data, $match)) {
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