if (typeof(googlemaps_maps) == 'undefined') {
	// add vml namespace for MSIE
	var agent = navigator.userAgent.toLowerCase();
	if (agent.indexOf("msie") != -1 && agent.indexOf("opera") == -1) {
		try {
		document.namespaces.add('v', 'urn:schemas-microsoft-com:vml');
		document.createStyleSheet().addRule('v\:*', 'behavior: url(#default#VML);');
		} catch(e) {}
	}

	var googlemaps_maps = new Array();
	var googlemaps_markers = new Array();
	var googlemaps_marker_mgrs = new Array();
	var googlemaps_icons = new Array();
	var googlemaps_crossctrl = new Array();
	var onloadfunc = new Array();
	var onloadfunc2 = new Array();
}

function PGMarker (point, icon, page, map, hidden, visible, title, maxtitle, maxcontent, minzoom, maxzoom) {
	var marker = null;
	if (hidden == false) {
		var opt = new Object();
		if (icon != '') {
			opt.icon = googlemaps_icons[page][icon];
		} else if (!!googlemaps_icons[page]['_default']) {
			opt.icon = googlemaps_icons[page]['_default'];
		}
		if (title != '') { opt.title = title; }
		marker = new GMarker(point, opt);
		GEvent.addListener(marker, "click", function() { this.pukiwikigooglemaps.onclick(); });
		marker.pukiwikigooglemaps = this;
	}

	this.marker = marker;
	this.icon = icon;
	this.map = map;
	this.point = point;
	this.minzoom = minzoom;
	this.maxzoom = maxzoom;

	var _visible = false;
	var _html = null;
	var _zoom = null;
	var _type = null;

	this.setHtml = function(h) {_html = h;}
	this.setZoom = function(z) {_zoom = parseInt(z);}
	this.setType = function(t) {_type = t;}
	this.getHtml = function() {return _html;}
	this.getZoom = function() {return _zoom;}
	//this.getType = function() {return _type;}

	this.onclick = function () {
		var map = googlemaps_maps[page][this.map];
        var maxContentDiv = document.createElement('div');

        maxContentDiv.innerHTML = 'Loading...';
        infowindowopts = {maxContent:maxContentDiv, maxTitle:maxtitle};
        if (maxcontent == "") {
            map.getInfoWindow().disableMaximize();
            infowindowopts = {};
        }

		if (_type !== map.getCurrentMapType()) {
			map.setMapType(_type);
		}

		if (_zoom) {
			if (map.getZoom() != _zoom) {
				map.setZoom(_zoom);
			}
		}

		map.panTo(this.point);

		if ( _html && this.marker ) {
			//map.panTo(this.point);
			// Wait while load image.
			var root = document.createElement('div');
			root.innerHTML = _html;

			var checkNodes = new Array();
			var doneOpenInfoWindow = false;
			checkNodes.push(root);

			while (checkNodes.length) {
				var node = checkNodes.shift();
				if (node.hasChildNodes()) {
					for (var i=0; i<node.childNodes.length; i++) {
						checkNodes.push(node.childNodes.item(i));
					}
				} else {
					var tag = node.tagName;
					if (tag && tag.toUpperCase() == "IMG") {
						if (node.complete == false) {
							// Wait while load image.
							var openInfoWindowFunc = function (xmlhttp) {
								marker.openInfoWindowHtml(_html, infowindowopts);
							}
							var async = false;
							if (agent.indexOf("msie") != -1 && agent.indexOf("opera") == -1) {
								async = true;
							}
							if (PGTool.downloadURL(node.src, openInfoWindowFunc, async, null, null)) {
								doneOpenInfoWindow = true;
							}
							break;
						}
					}
				}
			}
			if (doneOpenInfoWindow == false) {
				this.marker.openInfoWindowHtml(_html, infowindowopts);
                if (maxcontent) {
                    maxContentDiv.style.width = "100%";
                    maxContentDiv.style.height = "98%";
                    maxContentDiv.innerHTML = '<iframe src="' + maxcontent +
                    '" frameborder="0" height=100% width=100%>required iframe enabled browser</iframe>';
                }
			}
		} else {
			//map.panTo(this.point);
		}
	}

	this.isVisible = function () {
		return _visible;
	}
	this.show = function () {
		if (_visible) return;
		if (this.marker) this.marker.show();
		_visible = true;
	}

	this.hide = function () {
		if (!_visible) return;
		if (this.marker != null) this.marker.hide();
		_visible = false;
	}

	if (visible) {
		this.show();
	} else {
		this.hide();
	}
	return this;
}


var PGTool = new function () {
	this.fmtNum = function (x) {
		var n = x.toString().split(".");
		n[1] = (n[1] + "000000").substr(0, 6);
		return n.join(".");
	}
	this.getLatLng = function (x, y, api) {
		switch (api) {
			case 0:
				x = x - y * 0.000046038 - x * 0.000083043 + 0.010040;
				y = y - y * 0.00010695  + x * 0.000017464 + 0.00460170;
			case 1:
				t = x;
				x = y;
				y = t;
				break;
		}
		return new GLatLng(x, y);
	}
	this.getXYPoint = function (x, y, api) {
		if (api < 2) {
			t = x;
			x = y;
			y = t;
		}
		if (api == 0) {
			nx = 1.000083049 * x + 0.00004604674815 * y - 0.01004104571;
			ny = 1.000106961 * y - 0.00001746586797 * x - 0.004602192204;
			x = nx;
			y = ny;
		}
		return {x:x, y:y};
	}
	this.createXmlHttp = function () {
		if (typeof(XMLHttpRequest) == "function") {
			return new XMLHttpRequest();
		}
		if (typeof(ActiveXObject) == "function") {
			try {
				return new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {};
			try {
				return new ActiveXObject("Microsoft.XMLHTTP");
			} catch(e) {};
		}
		return null;
	}
	this.downloadURL = function (url, func, async, postData, contentType) {
		var xmlhttp = this.createXmlHttp();
		if (!xmlhttp) {
			return null;
		}
		if (async && func) {
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4) {
					func(xmlhttp);
				}
			};
		}
		try {
			if (postData) {
				xmlhttp.open("POST", url, async);
				if (!contentType) {
					contentType = "application/x-www-form-urlencoded";
				}
				xmlhttp.setRequestHeader("Content-Type", contentType);
				xmlhttp.send(postData);
			} else {
				xmlhttp.open("GET", url, async);
				xmlhttp.send(null);
			}
		} catch(e) {
			return false;
		}
		if (!async && func) func(xmlhttp);
	}

	this.transparentGoogleLogo = function(map) {
		var container = map.getContainer();
		for (var i=0; i<container.childNodes.length; i++) {
			var node = container.childNodes.item(i);
			if (node.tagName != "A") continue;
			if (node.hasChildNodes() == false) continue;

			var img = node.firstChild;
			if (img.tagName != "IMG") continue;
			if (img.src.match(/http:.*\/poweredby\.png/) == null) continue;

			node.style.backgroundColor = "transparent";
			break;
		}
		return;
	}

	this.getMapTypeName = function(type) {
		if (type == G_HYBRID_MAP) {
			return 'hybrid';
		} else if (type == G_SATELLITE_MAP) {
			return 'satellite';
		} else if (type == G_PHYSICAL_MAP) {
			return 'physical';
		} else {
			return 'normal';
		}
	}
}

var PGDraw = new function () {
	var self = this;
	this.weight = 10;
	this.opacity = 0.5;
	this.color = "#00FF00";
	this.fillopacity = 0;
	this.fillcolor = "#FFFF00";

	this.line = function (plist) {
		return new GPolyline(plist, this.color, this.weight, this.opacity);
	}

	this.rectangle = function (p1, p2) {
		var points = new Array (
			p1,
			new GLatLng(p1.lat(), p2.lng()),
			p2,
			new GLatLng(p2.lat(), p1.lng()),
			p1
		);
		return draw_polygon (plist);
	}

	this.circle  = function (point, radius) {
		return draw_ngon(point, radius, 0, 48, 0, 360);
	}

	this.arc = function (point, outradius, inradius, st, ed) {
		while (st > ed) { ed += 360; }
		if (st == ed) {
			return this.circle(point, outradius, inradius);
		}
		return draw_ngon(point, outradius, inradius, 48, st, ed);
	}

	this.ngon = function (point, radius, n, rotate) {
		if (n < 3) return null;
		return draw_ngon(point, radius, 0, n, rotate, rotate+360);
	}

	this.polygon = function (plist) {
		return draw_polygon (plist);
	}

	function draw_ngon (point, outradius, inradius, div, st, ed) {
		if (div <= 2) return null;

		var incr = (ed - st) / div;
		var lat = point.lat();
		var lng = point.lng();
		var out_plist = new Array();
		var in_plist  = new Array();
		var rad = 0.017453292519943295; /* Math.PI/180.0 */
		var en = 0.00903576399827824;   /* 1/(6341km * rad) */
		var out_clat = outradius * en;
		var out_clng = out_clat/Math.cos(lat * rad);
		var in_clat = inradius * en;
		var in_clng = in_clat/Math.cos(lat * rad);

		for (var i = st ; i <= ed; i+=incr) {
			if (i+incr > ed) {i=ed;}
			var nx = Math.sin(i * rad);
			var ny = Math.cos(i * rad);

			var ox = lat + out_clat * nx;
			var oy = lng + out_clng * ny;
			out_plist.push(new GLatLng(ox, oy));

			if (inradius > 0) {
			var ix = lat + in_clat  * nx;
			var iy = lng + in_clng  * ny;
			in_plist.push (new GLatLng(ix, iy));
			}
		}

		var plist;
		if (ed - st == 360) {
			plist = out_plist;
			plist.push(plist[0]);
		} else {
			if (inradius > 0) {
				plist = out_plist.concat( in_plist.reverse() );
				plist.push(plist[0]);
			} else {
				out_plist.unshift(point);
				out_plist.push(point);
				plist = out_plist;
			}
		}

		return draw_polygon(plist);
	}

	function draw_polygon (plist) {
		if (self.fillopacity <= 0) {
		return new GPolyline(plist, self.color, self.weight, self.opacity);
		}
		return new GPolygon(plist, self.color, self.weight, self.opacity,
		self.fillcolor, self.fillopacity);
	}

}


//
// Center Cross control
//
function PGCross() {
	this.map = null;
	this.container = null;
};
PGCross.prototype = new GControl(false, false);

PGCross.prototype.initialize = function(map) {
	this.map = map;
	this.container = document.createElement("div");
	var crossDiv = this.createWidget(16, 2, "#000000");
	this.container.appendChild(crossDiv);
	this.container.width = crossDiv.width;
	this.container.height = crossDiv.height;

	var cross = this;
	GEvent.addDomListener(map, "resize", function(e) {
		var size = cross.getCrossCenter();
		cross.container.style.top  = size.height + 'px';
		cross.container.style.left = size.width  + 'px';
	});
	// TODO:The mouse event on Cross is spread to Map of the layer below.
	//GEvent.addDomListener(crossDiv, "dblclick", function(e) {
	//		if (map.doubleClickZoomEnabled())
	//			map.zoomIn();
	//});

	map.getContainer().appendChild(this.container);

	info = map.getInfoWindow();
	var container = this.container;
	var hidefunc = function() { map.getContainer().removeChild(container); }
	var showfunc = function() { map.getContainer().appendChild(container); }
	GEvent.addListener(map, "infowindowclose", function(){ showfunc(); });
	GEvent.addListener(info, "maximizeclick", function(){ hidefunc(); });
	GEvent.addListener(info, "restoreend", function(){ showfunc(); });

	return this.container;
}

PGCross.prototype.getCrossCenter = function() {
	var msize = this.map.getSize();
	var x = (msize.width  - this.container.width)/2.0;
	var y = (msize.height - this.container.height)/2.0;
	return new GSize(Math.ceil(x), Math.ceil(y));
}

PGCross.prototype.createWidget = function(nsize, lwidth, lcolor) {
	var hsize = (nsize - lwidth) / 2;
	var nsize = hsize * 2 + lwidth;
	var border = document.createElement("div");
	border.width = nsize;
	border.height = nsize;
	var table = '\
<table width="'+nsize+'" border="0" cellspacing="0" cellpadding="0">\
  <tr>\
  <td style="width:'+ hsize+'px; height:'+hsize+'px; background-color:transparent; border:0px;"></td>\
  <td style="width:'+lwidth+'px; height:'+hsize+'px; background-color:'+lcolor+';  border:0px;"></td>\
  <td style="width:'+ hsize+'px; height:'+hsize+'px; background-color:transparent; border:0px;"></td>\
  </tr>\
  <tr>\
  <td style="width:'+ hsize+'px; height:'+lwidth+'px; background-color:'+lcolor+'; border:0px;"></td>\
  <td style="width:'+lwidth+'px; height:'+lwidth+'px; background-color:'+lcolor+'; border:0px;"></td>\
  <td style="width:'+ hsize+'px; height:'+lwidth+'px; background-color:'+lcolor+'; border:0px;"></td>\
  </tr>\
  <tr>\
  <td style="width:'+ hsize+'px; height:'+hsize+'px; background-color:transparent; border:0px;"></td>\
  <td style="width:'+lwidth+'px; height:'+hsize+'px; background-color:'+lcolor+';  border:0px;"></td>\
  <td style="width:'+ hsize+'px; height:'+hsize+'px; background-color:transparent; border:0px;"></td>\
  </tr>\
</table>';
	border.innerHTML = table;
	border.firstChild.style.MozOpacity = 0.5;
	border.firstChild.style.filter = 'alpha(opacity=50)';
	return border;
}

PGCross.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, this.getCrossCenter());
}

PGCross.prototype.changeStyle = function(color, opacity) {
	var table = this.container.firstChild.firstChild;
	var children = table.getElementsByTagName("td");
	for (var i = 0; i < children.length; i++) {
		var node = children[i];
		if (node.style.backgroundColor != "transparent") {
			node.style.backgroundColor = color;
		}
	}
	table.style.MozOpacity = opacity;
	table.style.filter = 'alpha(opacity=' + (opacity * 100) + ')';
}

//
// Marker ON/OFF
//

function p_googlemaps_marker_toggle (page, mapname, check, name) {
	var markers = googlemaps_markers[page][mapname];
	for (key in markers) {
		if (!markers.hasOwnProperty(key)) continue;
		var m = markers[key];
		if (m.icon == name) {
			if (check.checked) {
				m.show();
			} else {
				m.hide();
			}
		}
	}
}

function p_googlemaps_togglemarker_checkbox (page, mapname, undefname, defname) {
	var icons = {};
	var markers = googlemaps_markers[page][mapname];
	for (key in markers) {
		if (!markers.hasOwnProperty(key)) continue;
		var map = markers[key].map;
		var icon = markers[key].icon;
		icons[icon] = 1;
	}
	var iconlist = new Array();
	for (n in icons) {
		if (!icons.hasOwnProperty(n)) continue;
		iconlist.push(n);
	}
	iconlist.sort();

	var r = document.createElement("div");
	var map = document.getElementById(mapname);
	map.parentNode.insertBefore(r, map.nextSibling);

	for (i in iconlist) {
		if (!iconlist.hasOwnProperty(i)) continue;
		var name = iconlist[i];
		var id = "ti_" + mapname + "_" + name;
		var input = document.createElement("input");
		var label = document.createElement("label");
		input.setAttribute("type", "checkbox");
		input.id = id;
		label.htmlFor = id;
		if (name == "") {
		label.appendChild(document.createTextNode(undefname));
		} else if (name == "Default") {
		label.appendChild(document.createTextNode(defname));
		} else {
		label.appendChild(document.createTextNode(name));
		}
		eval("input.onclick = function(){p_googlemaps_marker_toggle('" + page + "','" + mapname + "', this, '" + name + "');}");

		r.appendChild(input);
		r.appendChild(label);
		input.setAttribute("checked", "checked");
	}
}

function p_googlemaps_regist_marker (page, mapname, center, key, option) {
	if (document.getElementById(mapname) == null) {
		mapname = mapname.replace(/^pukiwikigooglemaps2_/, "");
		page = mapname.match(/(^.*?)_/)[1];
		mapname = mapname.replace(/^.*?_/, "");
		alert("googlemaps2: '" + option.title + "' It failed in the marker's registration." +
		"PageName: " + page + ", Not found map name '" + mapname + "'.");
		return;
	}
	option.title = option.title.replace(/&lt;/g, '<');
	option.title = option.title.replace(/&gt;/g, '>');
	option.title = option.title.replace(/&quot;/g, '"');
	option.title = option.title.replace(/&#039;/g, '\'');
	option.title = option.title.replace(/&amp;/g, '&');
	var m = new PGMarker(center, option.icon, page, mapname, option.noicon, true, option.title, option.maxtitle, option.maxcontent, option.minzoom, option.maxzoom);
	m.setHtml(option.infohtml);
	if (!option.zoom) {
		option.zoom = googlemaps_maps[page][mapname].getZoom();
	}
	m.setZoom(option.zoom);
	if (!option.type) {
		option.type = googlemaps_maps[page][mapname].getCurrentMapType();
	}
	m.setType(option.type);
	googlemaps_markers[page][mapname][key] = m;
}

function p_googlemaps_regist_to_markermanager (page, mapname, use_marker_mgr) {
	var markers = googlemaps_markers[page][mapname];

	if (use_marker_mgr == false) {
		for ( var key in markers) {
			if (!markers.hasOwnProperty(key)) continue;
			var m = markers[key];

			if (m.marker) {
				googlemaps_maps[page][mapname].addOverlay(m.marker);
			}
		}
		return;
	}

	var mgr = googlemaps_marker_mgrs[page][mapname];
	var levels = new Object();

	for (key in markers) {
		if (!markers.hasOwnProperty(key)) continue;
		var m = markers[key];
		var minzoom = m.minzoom<0 ? 0:m.minzoom;
		var maxzoom = m.maxzoom>17? 17:m.maxzoom;
		if (minzoom > maxzoom) {
			maxzoom = minzoom;
		}

		if (m.marker) {
			if (levels[minzoom] == undefined) {
				levels[minzoom] = new Object();
			}
			if (levels[minzoom][maxzoom] == undefined) {
				levels[minzoom][maxzoom] = new Array();
			}
			levels[minzoom][maxzoom].push(m.marker);
		}
	}

	for (minzoom in levels) {
		if (!levels.hasOwnProperty(minzoom)) continue;
		for (maxzoom in levels[minzoom]) {
			if (!levels[minzoom].hasOwnProperty(maxzoom)) continue;
			if (levels[minzoom][maxzoom])
			mgr.addMarkers(levels[minzoom][maxzoom], parseInt(minzoom), parseInt(maxzoom));
		}
	}
	mgr.refresh();
}

function p_googlemaps_auto_zoom (page, mapname) {
		var gb;
		var count = 0;
		var map = googlemaps_maps[page][mapname];
		var markers = googlemaps_markers[page][mapname];
		for( var key in markers ){
			if (!markers.hasOwnProperty(key)) continue;
			var marker = markers[key].marker;
			if( count == 0 ){
				gb = new GLatLngBounds( marker.getPoint(), marker.getPoint() );
			}else{
				var point = marker.getPoint();
				gb.extend( point );
			}
			count++;
		}
		if (count > 1) {
			map.setCenter( gb.getCenter(), map.getBoundsZoomLevel( gb ) );
		}
}

XpWiki.domInitFunctions.push(function() {
	if (GBrowserIsCompatible()) {
		while (onloadfunc.length > 0) {
			onloadfunc.shift()();
		}
		while (onloadfunc2.length > 0) {
			onloadfunc2.shift()();
		}
	}
});

window.onunload = function () {
	GUnload();
}
