<?php
/* Pukiwiki GoogleMaps plugin 2.3
 * http://reddog.s35.xrea.com
 * -------------------------------------------------------------------
 * Copyright (c) 2005, 2006, 2007 OHTSUKA, Yoshio
 * This program is free to use, modify, extend at will. The author(s)
 * provides no warrantees, guarantees or any responsibility for usage.
 * Redistributions in any form must retain this copyright notice.
 * ohtsuka dot yoshio at gmail dot com
 * -------------------------------------------------------------------
 * 2007-12-01 2.3.0 ¾ÜºÙ¤Ïgooglemaps2.inc.php
 */

class xpwiki_plugin_googlemaps2_icon extends xpwiki_plugin {
	function plugin_googlemaps2_icon_init () {

		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_IMAGE'] =  'http://www.google.com/mapfiles/marker.png';
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_SHADOW'] = 'http://www.google.com/mapfiles/shadow50.png';
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_IW'] =  20;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_IH'] =  34;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_SW'] =  37;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_SH'] =  34;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_IANCHORX'] =  10;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_IANCHORY'] =  34;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_SANCHORX'] =  10;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_SANCHORY'] =  0;
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_TRANSPARENT'] =  'http://www.google.com/mapfiles/markerTransparent.png';
		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_AREA'] =  '1 7 7 0 13 0 19 7 19 12 13 20 12 23 11 34 9 34 8 23 6 19 1 13 1 70';

		$this->cont['PLUGIN_GOOGLEMAPS2_ICON_REGEX'] = '#^http://[a-z]+\.google\.com#i';
	}
	
	function plugin_googlemaps2_icon_get_default () {
		return array(
			'image'       => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_IMAGE'],
			'shadow'      => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_SHADOW'],
			'iw'          => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_IW'],
			'ih'          => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_IH'],
			'sw'          => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_SW'],
			'sh'          => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_SH'],
			'ianchorx'    => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_IANCHORX'],
			'ianchory'    => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_IANCHORY'],
			'sanchorx'    => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_SANCHORX'],
			'sanchory'    => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_SANCHORY'],
			'transparent' => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_TRANSPARENT'],
			'area'        => $this->cont['PLUGIN_GOOGLEMAPS2_ICON_AREA'],
			'basepage'    => $this->root->vars['page']
		);
	}
	
	function plugin_googlemaps2_icon_convert() {
		if (func_num_args() < 1) {
			$args = array('Default', '');
		} else {
			$args = func_get_args();
		}
		return $this->plugin_googlemaps2_icon_output($args[0], array_slice($args, 1));
	}
	
	function plugin_googlemaps2_icon_inline() {
		if (isset($this->root->rtf['GET_HEADING_INIT'])) return 'Google Maps';
		if (func_num_args() < 1) {
			$args = array('Default', '');
		} else {
			$args = func_get_args();
			array_pop($args);
		}
		return $this->plugin_googlemaps2_icon_output($args[0], array_slice($args, 1));
	}
	
	function plugin_googlemaps2_icon_output($name, $params) {
		
		$p_googlemaps2 =& $this->func->get_plugin_instance('googlemaps2');
				
		if (! isset($this->root->rtf['PUSH_PAGE_CHANGES']) && $p_googlemaps2->plugin_googlemaps2_is_supported_profile() && !$p_googlemaps2->lastmap_name) {
			return "googlemaps2_icon: {$p_googlemaps2->msg['err_need_googlemap2']}";
		}

		if (!$p_googlemaps2->plugin_googlemaps2_is_supported_profile()) {
			return '';
		}
	
		$defoptions = $this->plugin_googlemaps2_icon_get_default();
		
		$inoptions = array();
		foreach ($params as $param) {
			list($index, $value) = array_pad(split('=', $param, 2), 2, '');
			$index = trim($index);
			$value = htmlspecialchars(trim($value), ENT_QUOTES);
			$inoptions[$index] = $value;
		}
		
		if (array_key_exists('define', $inoptions)) {
			$this->root->vars['googlemaps2_icon'][$inoptions['define']] = $inoptions;
			return "";
		}
		
		$coptions = array();
		if (array_key_exists('class', $inoptions)) {
			$class = $inoptions['class'];
			if (array_key_exists($class, $this->root->vars['googlemaps2_icon'])) {
				$coptions = $this->root->vars['googlemaps2_icon'][$class];
			}
		}
		$options = array_merge($defoptions, $coptions, $inoptions);
		$image       = $this->optimize_image($options['image'], $options['basepage']);
		$shadow      = $this->optimize_image($options['shadow'], $options['basepage']);
		$iw          = (integer)$options['iw'];
		$ih          = (integer)$options['ih'];
		$sw          = (integer)$options['sw'];
		$sh          = (integer)$options['sh'];
		$ianchorx    = (integer)$options['ianchorx'];
		$ianchory    = (integer)$options['ianchory'];
		$sanchorx    = (integer)$options['sanchorx'];
		$sanchory    = (integer)$options['sanchory'];
		$transparent = $this->optimize_image($options['transparent'], $options['basepage']);
		$area        = $options['area'];
	
		$coords = array();
		if (isset($area)) {
			$c = substr($area, 0, 1);
			switch ($c) {
				case "'":
				case "[";
				case "{";
					$area = substr($area, 1, strlen($area)-2);
					break;
				case "&":
					if (substr($area, 0, 6) == "&quot;") {
						$area = substr($area, 6, strlen($area)-12);
					}
					break;
			}
			foreach (explode(' ', $area) as $p) {
				if (strlen($p) <= 0) continue;
				array_push($coords, $p);
			}
		}
		$coords = join($coords, ",");
		$page = $p_googlemaps2->get_pgid($this->root->vars['page']);
	
		// Output
		if ($image && $shadow && $transparent) {
			$output = <<<EOD
<script type="text/javascript">
//<![CDATA[
onloadfunc.push( function () {
	var icon = new GIcon();
	icon.image = "$image";
	icon.shadow = "$shadow";
	icon.iconSize = new GSize($iw, $ih);
	icon.shadowSize = new GSize($sw, $sh);
	icon.iconAnchor = new GPoint($ianchorx, $ianchory);
	icon.infoWindowAnchor = new GPoint($sanchorx, $sanchory);
	icon.transparent = "$transparent";
	icon.imageMap = [$coords];
	icon.pukiwikiname = "$name";
	googlemaps_icons["$page"]["$name"] = icon;
});
//]]>
</script>

EOD;
			return $output;
		} else {
			return '';
		}
	}
	
	function optimize_image($image, $basepage) {
		if (strtolower(substr($image, 0, 4)) !== 'http') {
			$image = $this->func->unhtmlspecialchars($image, ENT_QUOTES);
			if (strpos($image, '/') !== FALSE) {
				$basepage = $this->func->page_dirname($image);
				$image = $this->func->page_basename($image);
			}
			$image = $this->cont['HOME_URL'].'gate.php?way=ref&_nodos&_noumb&page='.rawurlencode($basepage).'&src='.rawurlencode($image);
		} else {
			if ($this->cont['PLUGIN_GOOGLEMAPS2_ICON_REGEX']) {
				if (strpos($image, $this->root->siteinfo['host']) !== 0 && !preg_match($this->cont['PLUGIN_GOOGLEMAPS2_ICON_REGEX'], $image)) {
					$image = '';
				}
			}
		}
		return $image;
	}
}
?>