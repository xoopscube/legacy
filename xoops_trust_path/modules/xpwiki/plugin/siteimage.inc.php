<?php
/*
 * Created on 2007/11/16 by nao-pon http://hypweb.net/
 * $Id: siteimage.inc.php,v 1.7 2011/11/26 12:03:10 nao-pon Exp $
 */

class xpwiki_plugin_siteimage extends xpwiki_plugin {

	function plugin_siteimage_init() {

		// 再取得までの日数
		// Days until acquiring again.
		$this->cache_day = 10;
		
		// 初回の再取得時間(分)
		// Time of re-acquisition first time.(min.)
		$this->cache1st_min = 10;
		
		// 再取得時一度に処理できる件数
		// The number of cases treatable when acquiring it again at a time.
		$this->get_max_once = 3;
		
		// サイトイメージの取得元
		// Acquisition origin of site image
		//$this->fetch_url = 'http://screenshot.livedoor.com/large/';
		//$this->fetch_url = 'http://mozshot.nemui.org/shot/160x120?';
		$this->fetch_url = 'http://capture.heartrails.com/medium?';
		
		// サムネイルサイズの定義 (l, m, s)
		// Definition of thumbnail size.(l, m, s)
		$this->thumb_size['l'] = array(
			'width'  => 160,
			'height' => 120
		);
		$this->thumb_size['m'] = array(
			'width'  => 120,
			'height' => 90
		);
		$this->thumb_size['s'] = array(
			'width'  => 90,
			'height' => 60
		);
		
		// サムネイルサイズの規定値
		// Regulated value of thumbnail size.
		$this->default_size = 's';
	}
	
	function plugin_siteimage_inline() {

		$args = func_get_args();
		$url = array_shift($args);
		$this->func->url_regularization($url);
		$prms = array(
			'nolink' => false,
			'target' => $this->root->link_target,
			'size'   => $this->default_size
		);
		$this->fetch_options($prms, $args);
		return $this->make_thumbnail($url, $prms);
	}
	
	function plugin_siteimage_convert() {

		$args = func_get_args();
		$url = array_shift($args);
		$this->func->url_regularization($url);
		$prms = array(
			'nolink' => false,
			'target' => $this->root->link_target,
			'size'   => $this->default_size,
			'around' => FALSE,
			'right'  => FALSE,
			'center' => FALSE,
			'left'   => TRUE
		);
		$this->fetch_options($prms, $args);

		$prm_size = strtolower($prms['size']);
		if (!preg_match('/[sml]/', $prm_size)) {
			$prm_size = 's';
		}
		$thumb_size = $this->thumb_size[$prm_size];
		
		$style = "width:{$thumb_size['width']}px;height:{$thumb_size['height']}px;margin:10px;";
		if ($prms['around']) {
			if ($prms['right']) {
				$style .= "float:right;margin-right:5px;";
			} else {
				$style .= "float:left;margin-left:5px;";
			}
		} else 	{
			if ($prms['right']) {
				$style .= "margin-right:10px;margin-left:auto;";
			} else if ($prms['center']) {
				$style .= "margin-right:auto;margin-left:auto;";
			} else {
				$style .= "margin-right:auto;margin-left:10px;";
			}
		}
		$img = $this->make_thumbnail($url, $prms);
		return "<div style=\"$style\">$img</div>\n";
	}
	
	function make_thumbnail($url, $prms) {

		static $count = 0;
		
		if (!preg_match('#^https?://#', $url)) {
			$url = 'http://' . $url;
		}
		
		$target = htmlspecialchars($prms['target']);
		$nolink = $prms['nolink'];
		$thumburl = $this->fetch_url . $url;
		
		$sha1 = sha1($thumburl);
		
		$prm_size = strtolower($prms['size']);
		if (!preg_match('/[sml]/', $prm_size)) {
			$prm_size = 's';
		}
		$thumb_size = $this->thumb_size[$prm_size];
		$size = $thumb_size['width'] . 'x' . $thumb_size['height'];
		$thumb_file = $this->cont['CACHE_DIR'] . 'ASIN_SITEIMAGE_' . $sha1 . '_' . $prm_size . '.jpg';
		
		$cache = $this->cont['CACHE_DIR'] . 'plugin/' . $sha1 . '.siteimage';
		$is_new = (!is_file($cache));
		if ($is_new || ($count < $this->get_max_once && filemtime($cache) + $this->cache_day * 86400 < $this->cont['UTC'])) {
			
			$count ++;
			
			$ht = new Hyp_HTTP_Request();
			$ht->init();
			$ht->ua = 'Mozilla/5.0';
			$ht->url = $thumburl;
			$ht->get();
			
			$image = '';
			if ($ht->rc === 200) {
				$image = $ht->data;
			}
			$ht = NULL;
			
			if ($image && $fp = fopen($cache, 'wb')) {
				fwrite($fp, $image);
				fclose($fp);
				foreach(array('s', 'm', 'l') as $_size) {
					@ unlink($this->cont['CACHE_DIR'] . 'ASIN_SITEIMAGE_' . $sha1 . '_' . $_size . '.jpg');
				}
				if ($is_new) {
					$this->func->pkwk_touch_file($cache, $this->cont['UTC'] - $this->cache_day * 86400 + $this->cache1st_min * 60 );
				}
			}
		}
		
		if (!is_file($thumb_file)) {
			copy($cache, $thumb_file);
			HypCommonFunc::ImageResize($thumb_file, $size);
			HypCommonFunc::ImageMagickRoundCorner($thumb_file, '', 5, 2);
		}
		
		$cache_url = str_replace($this->cont['DATA_HOME'], $this->cont['HOME_URL'], $thumb_file);
		$url = htmlspecialchars($url);
		$title = preg_replace('#^https?://#i', '', $url);
		$ret = "<img src=\"".$cache_url."\" width=\"{$thumb_size['width']}\" height=\"{$thumb_size['height']}\" alt=\"{$title}\">";
		if (!$nolink)
			$ret = "<a class=\"siteimage\" href=\"{$url}\" target=\"{$target}\" title=\"{$title}\">".$ret."</a>";
		
		return $ret;
	}
}
?>