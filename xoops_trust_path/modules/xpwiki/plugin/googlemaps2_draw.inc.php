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
 * 2007-12-01 2.3.0 詳細はgooglemaps2.inc.php
 */

class xpwiki_plugin_googlemaps2_draw extends xpwiki_plugin {
	function plugin_googlemaps2_draw_init () {

		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_OPACITY'] = 0.5;       //透明度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_WEIGHT'] = 10;        //太さ
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_COLOR'] = "#FF0000"; //色
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT'] = 35.0;      //緯度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG'] = 135.0;     //経度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT1'] = 35.0;      //座標1緯度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG1'] = 135.0;     //座標1経度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT2'] = 36.0;      //座標2緯度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG2'] = 136.0;     //座標2経度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_RADIUS'] = 1;         //半径または外径(単位はkm)
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_INRADIUS'] = 0;         //内径(単位はkm)
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_STANGLE'] = 0;         //開始角
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_EDANGLE'] = 180;       //終了角
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_ROTATE'] = 0;         //回転度数
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_N'] = 3;         //正n角形の頂点数
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_FILLOPACITY'] = 0.5;       //塗りつぶし透明度
		$this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_FILLCOLOR'] = "#FFFF00"; //塗りつぶし色

	}
	
	function plugin_googlemaps2_draw_get_default () {

		return array(
			'opacity '    => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_OPACITY'],
			'weight'      => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_WEIGHT'],
			'color'       => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_COLOR'],
			'map'         => $this->cont['PLUGIN_GOOGLEMAPS2_DEF_MAPNAME'],
			'lat'         => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT'],
			'lng'         => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG'],
			'lat1'        => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT1'],
			'lng1'        => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG1'],
			'lat2'        => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LAT2'],
			'lng2'        => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_LNG2'],
			'radius'      => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_RADIUS'],
			'inradius'    => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_INRADIUS'],
			'stangle'     => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_STANGLE'],
			'edangle'     => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_EDANGLE'],
			'rotate'      => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_ROTATE'],
			'n'           => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_N'],
			'fillopacity' => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_FILLOPACITY'],
			'fillcolor'   => $this->cont['PLUGIN_GOOGLEMAPS2_DRAW_DEF_FILLCOLOR']
		);
	
	}
	
	function plugin_googlemaps2_draw_convert() {
		$args = func_get_args();
		if (sizeof($args)<2) {
			return "error: plugin googlemaps2_mark wrong args\n";
		}
		return $this->plugin_googlemaps2_draw_output($args[0], array_slice($args, 1));
	}
	
	function plugin_googlemaps2_draw_inline() {
		if (isset($this->root->rtf['GET_HEADING_INIT'])) return 'Google Maps';
		$args = func_get_args();
		$str = array_pop($args);
		if (sizeof($args)<2) {
			return "error: plugin googlemaps2_mark wrong args\n";
		}
		return $this->plugin_googlemaps2_draw_output($args[0], array_slice($args, 1));
	}
	
	function plugin_googlemaps2_draw_output($command, $params) {
	
		$p_googlemaps2 =& $this->func->get_plugin_instance('googlemaps2');
				
		if (! isset($this->root->rtf['PUSH_PAGE_CHANGES']) && $p_googlemaps2->plugin_googlemaps2_is_supported_profile() && !$p_googlemaps2->lastmap_name) {
			return "googlemaps2_draw: {$p_googlemaps2->msg['err_need_googlemap2']}";
		}
		
		if (!$p_googlemaps2->plugin_googlemaps2_is_supported_profile()) {
			return '';
		}
	
		if (!array_key_exists('googlemaps2_draw_history', $this->root->vars)) {
			$this->root->vars['googlemaps2_draw_history'] = $this->plugin_googlemaps2_draw_get_default ();
		}
		
		$inoptions = array();
		$narray = array();
		foreach ($params as $param) {
			$pos = strpos($param, '=');
			if ($pos == false) {
				if (is_numeric($param)) {
					array_push($narray, trim($param));
				}
				continue;
			}
			$index = trim(substr($param, 0, $pos));
			$value = htmlspecialchars(trim(substr($param, $pos+1)), ENT_QUOTES);
	
			$inoptions[$index] = $value;
		}
	
		if (array_key_exists('define', $inoptions)) {
			$this->root->vars['googlemaps2_draw'][$inoptions['define']] = $inoptions;
			return "";
		}
	
		$coptions = array();
		if (array_key_exists('class', $inoptions)) {
			$class = $inoptions['class'];
			if (array_key_exists($class, $this->root->vars['googlemaps2_draw'])) {
				$coptions = $this->root->vars['googlemaps2_draw'][$class];
			}
		}
		$options = array_merge($this->root->vars['googlemaps2_draw_history'], $coptions, $inoptions);
		$this->root->vars['googlemaps2_draw_history'] = $options;
	
		$opacity     = (float)$options['opacity'];
		$weight      = (float)$options['weight'];
		$color       = $options['color'];
		if ($options['map'] === $this->cont['PLUGIN_GOOGLEMAPS2_DEF_MAPNAME']) {
			$map     = $p_googlemaps2->lastmap_name;
		} else {
			$map     = $p_googlemaps2->plugin_googlemaps2_addprefix($this->root->vars['page'], $options['map']);
		}
		$lat         = (float)$options['lat'];
		$lng         = (float)$options['lng'];
		$lat1        = (float)$options['lat1'];
		$lng1        = (float)$options['lng1'];
		$lat2        = (float)$options['lat2'];
		$lng2        = (float)$options['lng2'];
		$radius      = (float)$options['radius'];
		$inradius    = (float)$options['inradius'];
		$stangle     = (float)$options['stangle'];
		$edangle     = (float)$options['edangle'];
		$rotate      = (float)$options['rotate'];
		$n           = (integer)$options['n'];
		$fillopacity = (float)$options['fillopacity'];
		$fillcolor   = $options['fillcolor'];
		
		$page = $p_googlemaps2->get_pgid($this->root->vars['page']);
		// Create Marker
		$output = <<<EOD
<script type="text/javascript">
//<![CDATA[
onloadfunc.push(function() {
	if (document.getElementById("$map") == null) {
		alert("googlemaps error: mapname \\"$map\\" is not defined");
		return;
	}
	PGDraw.weight = $weight;
	PGDraw.opacity = $opacity;
	PGDraw.color = "$color";
	PGDraw.fillopacity = $fillopacity;
	PGDraw.fillcolor = "$fillcolor";

EOD;
		switch ($command) {
			case "line":
			case "polygon":
				if (count($narray) < 2)
					break;
				$output .= "	var points = new Array();\n";
				for ($i=0; $i < count($narray); $i++) {
					$lat = $narray[$i];
					$lng = $narray[++$i];
					$output .= "	points.push(new GLatLng($lat, $lng));\n";
				}
				if ($command == "line") {
					$output .= "	var poly = PGDraw.line(points);\n";
				} else {
					$output .= "	var poly = PGDraw.polygon(points);\n";
				}
				break;
			case "rectangle":
				$output .= "	var p1 = new GLatLng(".$lat1.", ".$lng1.");\n";
				$output .= "	var p2 = new GLatLng(".$lat2.", ".$lng2.");\n";
				$output .= "	var poly = PGDraw.rectangle(p1, p2);\n";
				break;
			case "circle":
				$output .= "	var center = new GLatLng(".$lat.", ".$lng.");\n";
				$output .= "	var outradius = ".$radius.";\n";
				$output .= "	var inradius = ".$inradius.";\n";
				$output .= "	var poly = PGDraw.circle(center, outradius, inradius);\n";
				break;
			case "arc":
				$output .= "	var center = new GLatLng(".$lat.", ".$lng.");\n";
				$output .= "	var outradius = ".$radius.";\n";
				$output .= "	var inradius = ".$inradius.";\n";
				$output .= "	var st = ".$stangle.";\n";
				$output .= "	var ed = ".$edangle.";\n";
				$output .= "	var poly = PGDraw.arc(center, outradius, inradius, st, ed);\n";
				break;
			case "ngon":
				$output .= "	var center = new GLatLng(".$lat.", ".$lng.");\n";
				$output .= "	var radius = ".$radius.";\n";
				$output .= "	var rotate = ".$rotate.";\n";
				$output .= "	var n = ".$n.";\n";
				$output .= "	var poly = PGDraw.ngon(center, radius, n, rotate);\n";
				break;
			default:
				$output .= "	var poly = null;\n";
		}
		$output .= "	if (poly) googlemaps_maps['$page']['$map'].addOverlay(poly);\n";
		$output .= <<<EOD
});
//]]>
</script>\n
EOD;
	
		return $output;
	}
}
?>