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

class xpwiki_plugin_googlemaps2_insertmarker extends xpwiki_plugin {
	function plugin_googlemaps2_insertmarker_init () {

		// 言語ファイルの読み込み
		$this->load_language();

		$this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_DIRECTION'] =  'down'; //追加していく方向(up, down)
		$this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_TITLE_MAXLEN'] =  40; //タイトルの最長の長さ
		$this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_CAPTION_MAXLEN'] =  400; //キャプションの最長の長さ
		$this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_URL_MAXLEN'] =  1024; //URLの最長の長さ

	}

	function plugin_googlemaps2_insertmarker_action() {

		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits editing');

		if(is_numeric($this->root->vars['lat'])) $lat = $this->root->vars['lat']; else return;
		if(is_numeric($this->root->vars['lng'])) $lng = $this->root->vars['lng']; else return;
		if(is_numeric($this->root->vars['zoom'])) $zoom = $this->root->vars['zoom']; else return;
		if(is_numeric($this->root->vars['mtype'])) $mtype = $this->root->vars['mtype']; else return;

		$maptypes = array(
			1 => 'satellite',
			2 => 'hybrid',
			3 => 'physical',
		);
		$mtypename = isset($maptypes[$mtype])? $maptypes[$mtype] : 'normal';

		$map    = htmlspecialchars(trim($this->root->vars['map']));
		$icon   = htmlspecialchars($this->root->vars['icon']);
		$title   = substr($this->root->vars['title'], 0, $this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_TITLE_MAXLEN']);
		$caption = substr(trim($this->root->vars['caption']), 0, $this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_CAPTION_MAXLEN']);
		$image   = substr($this->root->vars['image'], 0, $this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_URL_MAXLEN']);
		$maxurl  = substr($this->root->vars['maxurl'], 0, $this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_URL_MAXLEN']);

		$minzoom = $this->root->vars['minzoom'] == '' ? '' : (int)$this->root->vars['minzoom'];
		$maxzoom = $this->root->vars['maxzoom'] == '' ? '' : (int)$this->root->vars['maxzoom'];

		$caption .= (isset($this->root->vars['save_addr']))? ($caption? '&br;' : '') . $this->msg['cap_addr'] . ': ' . $this->root->vars['addr'] : '';

		$title   = htmlspecialchars(str_replace("\n", '', $title));
		$caption = str_replace("\n", '&br;', $caption);
		$image   = htmlspecialchars($image);
		$maxurl  = htmlspecialchars($maxurl);

		$marker = '-&googlemaps2_mark('.$lat.', '.$lng;
		if ($title)         $marker .= ', title='.$title;
		if ($map)           $marker .= ', map='.$map;
		//if ($caption != '') $marker .= ', caption='.$caption;
		if ($icon != '')    $marker .= ', icon='.$icon;
		if ($image != '')   $marker .= ', image='.$image;
		if ($maxurl != '')  $marker .= ', maxurl='.$maxurl;
		if ($minzoom != '')  $marker .= ', minzoom='.$minzoom;
		if ($maxzoom != '')  $marker .= ', maxzoom='.$maxzoom;
		if (!empty($this->root->vars['save_zoom'])) $marker .= ', zoom='.$zoom;
		if (!empty($this->root->vars['save_mtype'])) $marker .= ', type='.$mtypename;
		$marker .= '){'.$caption.'};';

		$no       = 0;
		$postdata = '';
		$above    = ($this->root->vars['direction'] == 'up');

		$postdata_old = $this->func->get_source($this->root->vars['refer']);
		$this->func->escape_multiline_pre($postdata_old, TRUE);
		foreach ($postdata_old as $line) {
			if (! $above) $postdata .= $line;
			if (preg_match('/^#googlemaps2_insertmarker/i', $line) && $no++ == $this->root->vars['no']) {
				if ($above) {
					$postdata = rtrim($postdata) . "\n" . $marker . "\n";
				} else {
					$postdata = rtrim($postdata) . "\n" . $marker . "\n";
				}
			}
			if ($above) $postdata .= $line;
		}

		$title = $this->root->_title_updated;
		$body = '';
		if ($this->func->get_digests($this->func->get_source($this->root->vars['refer'], TRUE, TRUE)) != $this->root->vars['digest']) {
			$title = $this->root->_title_comment_collided;
			$body  = $this->root->_msg_comment_collided . $this->func->make_pagelink($this->root->vars['refer']);
		}

		$this->func->escape_multiline_pre($postdata, FALSE);
		$this->func->page_write($this->root->vars['refer'], $postdata);

		$retvars['msg']  = $title;
		$retvars['body'] = $body;
		$this->root->vars['page'] = $this->root->vars['refer'];

		//表示していたポジションを返すcookieを追加
		$cookieval = 'lat|'.$lat.'|lng|'.$lng.'|zoom|'.$zoom.'|mtype|'.$mtype;
		if ($minzoom) $cookieval .= '|minzoom|'.$minzoom;
		if ($maxzoom) $cookieval .= '|maxzoom|'.$maxzoom;
		setcookie('pukiwkigooglemaps2insertmarker'.$this->root->vars['no'], $cookieval);
		return $retvars;
	}

	function plugin_googlemaps2_insertmarker_get_default() {
	//	global $vars;
		return array(
			'map'       => $this->cont['PLUGIN_GOOGLEMAPS2_DEF_MAPNAME'],
			'direction' => $this->cont['PLUGIN_GOOGLEMAPS2_INSERTMARKER_DIRECTION']
		);
	}
	//inline型はテキストのパースがめんどくさそうなのでとりあえず放置。
	//function plugin_googlemaps2_insertmarker_inline() {
	//	return $this->msg['err_noinline'] . "\n";
	//}
	function plugin_googlemaps2_insertmarker_convert() {
		static $numbers = array();

		if (!isset($numbers[$this->xpwiki->pid])) {$numbers[$this->xpwiki->pid] = array();}

		$p_googlemaps2 =& $this->func->get_plugin_instance('googlemaps2');

		if ($p_googlemaps2->plugin_googlemaps2_is_supported_profile() && !$p_googlemaps2->lastmap_name) {
			return "googlemaps2_insertmarker: {$p_googlemaps2->msg['err_need_googlemap2']}";
		}

		if (!$p_googlemaps2->plugin_googlemaps2_is_supported_profile()) {
			return '';
		}

		if ($this->cont['PKWK_READONLY']) {
			return "read only<br>";
		}

		$this->msg['default_icon_caption'] = $p_googlemaps2->msg['default_icon_caption'];

		//オプション

		$defoptions = $this->plugin_googlemaps2_insertmarker_get_default();
		$inoptions = array();
		foreach (func_get_args() as $param) {
			$pos = strpos($param, '=');
			if ($pos == false) continue;
			$index = trim(substr($param, 0, $pos));
			$value = htmlspecialchars(trim(substr($param, $pos+1)), ENT_QUOTES);
			$inoptions[$index] = $value;
		}

		if (array_key_exists('define', $inoptions)) {
			$this->root->vars['googlemaps2_insertmarker'][$inoptions['define']] = $inoptions;
			return '';
		}

		$this->func->add_tag_head('googlemaps2.css');

		$coptions = array();
		if (array_key_exists('class', $inoptions)) {
			$class = $inoptions['class'];
			if (array_key_exists($class, $this->root->vars['googlemaps2_insertmarker'])) {
				$coptions = $this->root->vars['googlemaps2_icon'][$class];
			}
		}
		$options = array_merge($defoptions, $coptions, $inoptions);
		if ($options['map'] === $this->cont['PLUGIN_GOOGLEMAPS2_DEF_MAPNAME']) {
			$map      = $p_googlemaps2->lastmap_name;
			$mapname  = '';
		} else {
			$map      = $p_googlemaps2->plugin_googlemaps2_addprefix($this->root->vars['page'], $options['map']);
			$mapname  = $options['map'];//ユーザーに表示させるだけのマップ名（prefix除いた名前）
		}
		$direction = $options['direction'];
		$this->root->script    = $this->func->get_script_uri();
		$s_page    = htmlspecialchars($this->root->vars['page']);
		$page = $p_googlemaps2->get_pgid($this->root->vars['page']);

		if (! isset($numbers[$this->xpwiki->pid][$page]))
			$numbers[$this->xpwiki->pid][$page] = 0;
		$no = $numbers[$this->xpwiki->pid][$page]++;

		$imprefix = "_p_googlemaps2_insertmarker_".$page."_".$no;
		$script = $this->func->get_script_uri();
		$output = <<<EOD
<form action="{$script}" id="${imprefix}_form" method="post">
<div style="padding:2px;">
  <input type="hidden" name="plugin"    value="googlemaps2_insertmarker" />
  <input type="hidden" name="refer"     value="$s_page" />
  <input type="hidden" name="direction" value="$direction" />
  <input type="hidden" name="no"        value="$no" />
  <input type="hidden" name="digest"    value="{$this->root->digest}" />
  <input type="hidden" name="map"       value="$mapname" />
  <input type="hidden" name="zoom"      value="10" id="${imprefix}_zoom"/>
  <input type="hidden" name="mtype"     value="0"  id="${imprefix}_mtype"/>

  {$this->msg['cap_lat']}: <input type="text" name="lat" id="${imprefix}_lat" size="10" />
  {$this->msg['cap_lng']}: <input type="text" name="lng" id="${imprefix}_lng" size="10" />
  {$this->msg['cap_title']}:
  <input type="text" name="title"    id="${imprefix}_title" size="20" />
  {$this->msg['cap_icon']}:
  <select name="icon" id ="${imprefix}_icon">
  <option value="Default">{$this->msg['default_icon_caption']}</option>
  </select>
  <div class="googlemaps2_optional">
  {$this->msg['cap_image']}:
  <input type="text" name="image"    id="${imprefix}_image" size="20" />
  {$this->msg['cap_state']}:[
  <input type="checkbox" name="save_zoom" value="1" checked="checked" /> {$this->msg['cap_zoom']}
  |
  <input type="checkbox" name="save_mtype" value="1" checked="checked" /> {$this->msg['cap_type']}
  ]
  <br />
  {$this->msg['cap_marker']}:[ {$this->msg['cap_zoommin']}:
  <select name="minzoom" id ="${imprefix}_minzoom">
  <option value="">--</option>
  <option value="0"> 0</option> <option value="1"> 1</option>
  <option value="2"> 2</option> <option value="3"> 3</option>
  <option value="4"> 4</option> <option value="5"> 5</option>
  <option value="6"> 6</option> <option value="7"> 7</option>
  <option value="8"> 8</option> <option value="9"> 9</option>
  <option value="10">10</option> <option value="11">11</option>
  <option value="12">12</option> <option value="13">13</option>
  <option value="14">14</option> <option value="15">15</option>
  <option value="16">16</option> <option value="17">17</option>
  </select>
  |
  {$this->msg['cap_zoommax']}:
  <select name="maxzoom" id ="${imprefix}_maxzoom">
  <option value="">--</option>
  <option value="0"> 0</option> <option value="1"> 1</option>
  <option value="2"> 2</option> <option value="3"> 3</option>
  <option value="4"> 4</option> <option value="5"> 5</option>
  <option value="6"> 6</option> <option value="7"> 7</option>
  <option value="8"> 8</option> <option value="9"> 9</option>
  <option value="10">10</option> <option value="11">11</option>
  <option value="12">12</option> <option value="13">13</option>
  <option value="14">14</option> <option value="15">15</option>
  <option value="16">16</option> <option value="17">17</option>
  </select>
  ]
  <br />
  <input type="checkbox" name="save_addr" value="1" checked="checked" />{$this->msg['cap_addr']}: <input type="text" name="addr" id="${imprefix}_addr" size="50" />
  </div>
  {$this->msg['cap_note']}:
  <textarea id="{$imprefix}_textarea" name="caption" id="${imprefix}_caption" class="norich" rows="2" cols="55"></textarea>
  <input type="submit" name="Mark" value="{$this->msg['btn_mark']}" />
</div>
</form>

<script type="text/javascript">
//<![CDATA[
onloadfunc.push(function() {
	var map = googlemaps_maps['$page']['$map'];
	var geocoder = new GClientGeocoder();
	if (!map) {
		var form = document.getElementById("${imprefix}_form");
		form.innerHTML = '<div>' + '{$this->msg['err_map_notfind']}'.replace('\$mapname', '{$map}') + '</div>';
	} else {
		var lat   = document.getElementById("${imprefix}_lat");
		var lng   = document.getElementById("${imprefix}_lng");
		var zoom  = document.getElementById("${imprefix}_zoom");
		var mtype = document.getElementById("${imprefix}_mtype");
		var form  = document.getElementById("${imprefix}_form");
		var icon  = document.getElementById("${imprefix}_icon");
		var addr  = document.getElementById("${imprefix}_addr");

		var update_func = function() {
			lat.value = PGTool.fmtNum(map.getCenter().lat());
			lng.value = PGTool.fmtNum(map.getCenter().lng());
			zoom.value = parseInt(map.getZoom());
			mtype.value = -1;
			var curmaptype = map.getCurrentMapType();
			var maptypes  = map.getMapTypes();
			var cname = curmaptype.getName(false);
			for (i in maptypes) {
				if (!maptypes.hasOwnProperty(i)) continue;
				if (maptypes[i].getName(false) == cname) {
					mtype.value = i;
					break;
				}
			}
			//geocoder.getLocations(new GLatLng(lat.value, lng.value), set_addr);
		};

		var update_addr = function() {
			geocoder.getLocations(new GLatLng(map.getCenter().lat(), map.getCenter().lng()), set_addr);
		}

		var set_addr = function(response) {
			if (!response || response.Status.code != 200) {
				addr.value = '';
			} else {
				var place = response.Placemark[0];
				var i = 1;
				while(place.AddressDetails.Accuracy < 7 && place.AddressDetails.Accuracy > 4) {
					place = response.Placemark[i++];
				}
				if (place.AddressDetails.Country.CountryNameCode == 'JP') {
					addr.value = place.address.replace(new RegExp('^' + place.AddressDetails.Country.CountryName + '(, *)?'), '');
				} else {
					addr.value = place.address;
				}
			}
		}

		//Whenever the map is dragged, the parameter is dynamically substituted.
		GEvent.addListener(map, 'moveend', update_func);
		GEvent.addListener(map, 'maptypechanged', update_func);
		GEvent.addListener(map, 'zoom', update_func);

		GEvent.addListener(map, 'moveend', update_addr);

		update_func();
		update_addr();

		//The position of the map is initialized if there is a cookie. Contents of the cookie are cleared when finishing using it.
		(function () {
			var cookies = document.cookie.split(";");
			for (i in cookies) {
				if (!cookies.hasOwnProperty(i)) continue;
				var kv = cookies[i].split("=");
				for (j in kv) {
					if (!kv.hasOwnProperty(j)) continue;
					kv[j] = kv[j].replace(/^\s+|\s+$/g, "");
				}
				if (kv[0] == "pukiwkigooglemaps2insertmarker$no") {
					if (kv.length == 2 && kv[1].length > 0) {
						var mparam = {lat:0, lng:0, zoom:10, mtype:0};
						var oparam = {maxzoom:"", minzoom:""};
						var params = decodeURIComponent(kv[1]).split("|");
						for (var j = 0; j < params.length; j++) {
							//dump(params[j] + "=" + params[j+1] + "\\n");
							switch (params[j]) {
								case "lat": mparam.lat = parseFloat(params[++j]); break;
								case "lng": mparam.lng = parseFloat(params[++j]); break;
								case "zoom": mparam.zoom = parseInt(params[++j]); break;
								case "mtype": mparam.mtype = parseInt(params[++j]); break;
								case "maxzoom": oparam.maxzoom = parseInt(params[++j]); break;
								case "minzoom": oparam.minzoom = parseInt(params[++j]); break;
								default: j++; break;
							}
						}
						map.setCenter(new GLatLng(mparam.lat, mparam.lng),
								mparam.zoom, map.getMapTypes()[mparam.mtype]);

						var smz;
						var options;
						smz = document.getElementById("${imprefix}_minzoom")
						options = smz.childNodes;
						for (var j=0; j<options.length; j++) {
							var option = options.item(j);
							if (option.value == oparam.minzoom) {
								option.selected = true;
								break;
							}
						}

						smz = document.getElementById("${imprefix}_maxzoom")
						options = smz.childNodes;
						for (var j=0; j<options.length; j++) {
							var option = options.item(j);
							if (option.value == oparam.maxzoom) {
								option.selected = true;
								break;
							}
						}
					}
					break;
				}
			}
			document.cookie = "pukiwkigooglemaps2insertmarker$no=;";
		})();

		//Input check
		form.onsubmit = function () {
			if (isNaN(parseFloat(lat.value)) || isNaN(lat.value) ||
				isNaN(parseFloat(lng.value)) || isNaN(lng.value)) {
				alert("{$this->msg['err_irreg_dat']} LAT : " + lat.value + "  LNG : " + lng.value);
				return false;
			}
			return true;
		};
	}
	//The selection is updated reading all the icon definitions that exist on this page.
	onloadfunc.push(function() {
		for(iconname in googlemaps_icons['$page']) {
			if (!googlemaps_icons['$page'].hasOwnProperty(iconname) || iconname == 'Default') continue;
			var opt = document.createElement("option");
			opt.value = iconname;
			opt.appendChild(document.createTextNode(iconname));
			icon.appendChild(opt);
		}
	});
});
//]]>
</script>
EOD;

		return $output;
	}
}
?>