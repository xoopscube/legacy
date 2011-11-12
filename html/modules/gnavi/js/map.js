// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                        main script for googleMap                          //
// ------------------------------------------------------------------------- //

/*-----Gnavi const values-----*/

	//Map types dimension on strings
	var GN_MTX = new Array(
			"G_NORMAL_MAP",
			"G_SATELLITE_MAP",
			"G_HYBRID_MAP",
			"G_PHYSICAL_MAP",
			"G_MOON_ELEVATION_MAP",
			"G_MOON_VISIBLE_MAP",
			"G_MARS_ELEVATION_MAP",
			"G_MARS_VISIBLE_MAP",
			"G_MARS_INFRARED_MAP",
			"G_SKY_VISIBLE_MAP",
			"G_HYBRID_PHYSICAL_MAP");

	// Add CustomMap Types if you need
	if(window['G_PHYSICAL_MAP'] && window['G_HYBRID_MAP'] && window['G_NORMAL_MAP'])
		var G_HYBRID_PHYSICAL_MAP = new GMapType([G_PHYSICAL_MAP.getTileLayers()[0],G_HYBRID_MAP.getTileLayers()[1]],G_NORMAL_MAP.getProjection(),"Hybrid Physical");

	//handl events type    'end' operates googlemap at high speed.
	var GN_DRAG ='dragend'; // 'dragend' or 'drag' 
	var GN_MOVE ='moveend'; // 'moveend' or 'move'

/*-----Gnavi Global Params-----*/

	var gn_map=null;
	var gn_om=null
	var gn_geo=null;
	var gn_mymk=null;

	var gn_lg =null;
	var gn_geoz=null;
	var gn_mheight=null;
	var gn_url=null;
	var gn_ulop=null;
	var gn_mk=[];
	var gn_desc=[];
	var gn_l=0;
	var gn_ic='';
	var gn_mt=null;
	var gn_kmls=null;
	var gn_mykmls=null;
	var gn_ep=0;
	var gn_drkm=0;
	var gn_it='';
	var gn_ilt=0;
	var gn_ilg=0;
	var gn_iz=0;
	var gn_pe=null;
	var gn_pekey="";


/*-----Gnavi functions-----*/

function InitializeGmap(){

	if(!document.getElementById('map'))return false;

	if(!gn_mt)gn_mt=G_NORMAL_MAP;
	opts = {mapTypes : getMapTypes(gn_mt) };
	gn_map = new GMap2(document.getElementById('map'),opts);

	// If don't use , comment out these settings.  

		// option1: add small map
		gn_om = new GOverviewMapControl();
		gn_map.addControl(gn_om);

		// option2: add large map control
		gn_map.addControl(new GLargeMapControl());

		// option3: add Scale
		gn_map.addControl(new GScaleControl());

		// option4: add maptype control button
		addGMapTypeControl();

		// option5: enable DoubleClickZoom
		gn_map.enableDoubleClickZoom();

		// option6: enable smooze Zoom
		gn_map.enableContinuousZoom();

		// option7: enable move by keybode
		new GKeyboardHandler(gn_map);
		
		//option10: zoom then get address by geocording (gn_geoz=-1:no set)
		gn_geoz=18;

		// option11: set default maptype
		setGMapType();

		//option 12: include kml files
		ShowGeoXml();

		if(gn_pekey!="")InitPlaceEngine();

	return true;
}

function setGMapType(){
	if(gn_mt){
		//set maptype
		GEvent.addListener(gn_map,"load",function(){ 
			gn_map.setMapType( gn_mt );
			setTimeout("setOv()",100);
	    });
	}
}

function InitPlaceEngine(){
	include("http://www.placeengine.com/javascripts/pengine.js");
	var cr="<a href='www.placeengine.com'><img src='images/pe_logo.png'/></a>";

	l();
	function l(){
		if(window['PEngine']) m();
		else setTimeout(l,100);
	}
	function m(){
		gn_pe = new PEngine({onGetLocation:c,idstatus:"pestatus",appkey:gn_pekey});
		var s="";
		s+="<input src='images/wide_bt2.png' type='image' onclick='gn_pe.registerLocation(gn_map)' />&nbsp;";
		s+="<input src='images/wide_bt1.png' type='image' onclick='gn_pe.getLocation()' />&nbsp;";
		s+="<span id='pestatus'></span>";
		s+="&nbsp;"+cr;
		document.getElementById("peui").innerHTML=s;
	}
	function c(x, y, r, info){
	    if (gn_map != null){
			gn_map.panTo(new GLatLng(y, x));
			gn_map.openInfoWindowHtml(new GLatLng(y, x),info.addr+"<div align='center'>"+cr+"</div>");
		}
	    if (document.getElementById("pestatus")!=null)document.getElementById("pestatus").innerHTML = info.addr;
	}
}


function addGMapTypeControl(){

		if(getMapTypes(gn_mt)==G_DEFAULT_MAP_TYPES && window['G_HYBRID_PHYSICAL_MAP']){
			gn_map.addMapType(G_PHYSICAL_MAP);
			gn_map.addMapType(G_HYBRID_PHYSICAL_MAP);
			//gn_map.addMapType(G_SATELLITE_3D_MAP);

			var c = new GHierarchicalMapTypeControl();
			c.clearRelationships();
			c.addRelationship(G_SATELLITE_MAP, G_HYBRID_MAP, unescape(gn_lg['addlabel']), false);
			c.addRelationship(G_PHYSICAL_MAP, G_HYBRID_PHYSICAL_MAP, unescape(gn_lg['addlabel']), false);
			gn_map.addControl(c);
		}else{
			gn_map.addControl(new GMapTypeControl());
		}
}

function gn_feedLoader(){

	var feed = new google.feeds.Feed(gn_feedlink);
	feed.setNumEntries(gn_feednum);   
      feed.load(function(result) {
        if (!result.error) {
			var container = document.getElementById("feed");
			var s="";
			s+="<ul>";
			for (var i = 0; i < result.feed.entries.length; i++) {
				var entry = result.feed.entries[i];
				s+="<li>"+df(entry.publishedDate)+"&nbsp;<a href='"+entry.link+"' target='_blank'>"+entry.title+"</a></li>";
			}
			s+="</ul>";
			s+="<div align='right'><img src='images/rss.gif' align='absmiddle'/>&nbsp;<a href='"+gn_feedlink+"' target='_blank'>"+result.feed.title+"</a>";
			var d = document.createElement("div");
			d.innerHTML=s;
			container.appendChild(d);
        }
      });

	function df(a){
		var d,y,m,d;
		d = new Date(a);
		y = d.getYear();
		m = d.getMonth() + 1;
		d = d.getDate();
		if (y < 2000) y += 1900;
		if (m < 10) m = "0" + m;
		if (d < 10) m = "0" + d;
		return y + "/" + m + "/" + d;
	}
}

function getMapTypes(m){
	var r;
	switch(m){
		case 	G_NORMAL_MAP:
		case 	G_SATELLITE_MAP:
		case 	G_HYBRID_MAP:
		case 	G_PHYSICAL_MAP:
		case 	G_HYBRID_PHYSICAL_MAP:
			r = G_DEFAULT_MAP_TYPES;
			break;
		case 	G_MOON_ELEVATION_MAP:
		case 	G_MOON_VISIBLE_MAP:
			r = G_MOON_MAP_TYPES;
			break;
		case 	G_MARS_ELEVATION_MAP:
		case 	G_MARS_VISIBLE_MAP:
		case 	G_MARS_INFRARED_MAP:
			r = G_MARS_MAP_TYPES;
			break;
		case 	G_SKY_VISIBLE_MAP:
			r = G_SKY_MAP_TYPES;
			break;
		default:
			r = G_DEFAULT_MAP_TYPES;
	}

	return r;
}

function setOv() {
   	var m = gn_om.getOverviewMap();
	if (m) {    
		if(m.isLoaded()){
			m.clearOverlays();
			m.setMapType( gn_mt );
		}else{
			GEvent.addListener(m,"load",function(){ 
				m.clearOverlays();
				m.setMapType( gn_mt );
		    });
		}
	} else {
	   	setTimeout("setOv()",100);
	}
}

function ShowGeoXml(){
	if(gn_kmls){
		for( var i = 0; i < gn_kmls.length; i++ ) {
			var x = new GGeoXml(gn_kmls[i]);
			gn_map.addOverlay(x);
		}
	}
	if(gn_mykmls){
		for( var i = 0; i < gn_mykmls.length; i++ ) {
			var x = new GGeoXml(gn_mykmls[i]);
			gn_map.addOverlay(x);
		}
	}
}



function ShowItemGMap() {

	//show map on individual article.

	if(GBrowserIsCompatible()){

		if(gn_it) gn_mt = eval(gn_it) ;
		if(!InitializeGmap())return;

		//setcenter
		var c = new GLatLng(gn_ilt,gn_ilg);
		gn_map.setCenter(c,parseInt(gn_iz));
		var p = new Object();
		p.title = unescape(gn_lg['here']);

		if(gn_ic==''){
			gn_mymk = new GMarker(c);
			gn_map.addOverlay(gn_mymk);
		}else{
			var p = gn_ic.split(",");
			icon = new GIcon();
			icon.image = p[0];
			icon.iconSize = new GSize(eval(p[1]), eval(p[2]));
			if(p[3]!=''){
				icon.shadow = p[3];
				icon.shadowSize = new GSize(eval(p[4]), eval(p[5]));
			}
			icon.iconAnchor = new GLatLng(eval(p[7]),eval(p[6])); 
			gn_mymk = new GMarker(c,icon);
			gn_map.addOverlay(gn_mymk);
		}

	}else{

		document.getElementById("map").innerHTML='<strong>'+unescape(gn_lg['gmapdisable'])+'</strong>';

	}
}

function ShowGMap() {

	// display many markers.

	if(GBrowserIsCompatible()){

	//	mashmap(gn_map);

		if(document.getElementById('mt').value && window[document.getElementById('mt').value]){
			gn_mt = eval(document.getElementById('mt').value);
		}else{
			gn_mt = G_NORMAL_MAP;
		}

		if(!InitializeGmap())return;

		if(gn_drkm){
    		var k = gn_url+'/kml.php?'+gn_ulop;
			var g = new GGeoXml(k);
			gn_map.addOverlay(g);
		}

		searchSales();

		//addListener
		GEvent.addListener(gn_map, GN_MOVE, function() {
			var p = gn_map.getCenter();
			DrawLatLngTxt(p);
	    });
		GEvent.addListener(gn_map, 'zoomend',function(oldZoomLevel, newZoomLevel) {
	     		document.getElementById('z').value  =newZoomLevel;
	     		document.getElementById('sz').innerHTML  =newZoomLevel;
		});

		//addListener
		GEvent.addListener(gn_map, 'maptypechanged', function() {
			for (i in GN_MTX){
				if(eval(GN_MTX[i])==gn_map.getCurrentMapType()){
					document.getElementById('mt').value = GN_MTX[i];break;
				}
			}
	    });
		
		//setcenter
		var c = new GLatLng(document.getElementById('lat').value,document.getElementById('lng').value);
	  	gn_map.setCenter(c,parseInt(document.getElementById('z').value));

		if(gn_ep)right_click();
		
	}else{

		document.getElementById("map").innerHTML='<strong>'+unescape(gn_lg['gmapdisable'])+'</strong>';

	}
}

function right_click(){

	var r = document.createElement("div");
	r.style.visibility = "hidden";
	r.innerHTML = "<input type='button' onclick='frmlatlng.submit()' style='padding:3px;font-size:13px;cursor:pointer;' value='"+unescape(gn_lg['additem'])+"'>";

	gn_map.getContainer().appendChild(r);

	GEvent.addListener(gn_map, "singlerightclick", function(point) {
		DrawLatLngTxt(gn_map.fromContainerPixelToLatLng(point));
		var p = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(point.x, point.y));
		p.apply(r);
		r.style.visibility = "visible";
	});

	GEvent.addListener(gn_map, "click", function() {
	  	if (r.style.visibility == "visible")r.style.visibility = "hidden";
	});
	GEvent.addListener(gn_map, "mouseout", function() {
	  	if (r.style.visibility == "visible")r.style.visibility = "hidden";
	});
	GEvent.addListener(gn_map, GN_MOVE, function() {
	  	if (r.style.visibility == "visible")r.style.visibility = "hidden";
	});

}

function searchSales(){
	
	//get markers by kml

    var k = gn_url+'/kml.php?mime=xml&'+gn_ulop;

    var opt = {
        method: 'GET',
        asynchronous: true,
        onComplete: func2
    };
    var conn = new Ajax.Request( k, opt );

}

function func2(req){

	//show markers.

	if(!gn_drkm){
		//create icons
	  	var nl = req.responseXML.getElementsByTagName( 'IconStyle' );
		var icon = [];
	  	for( var i = 0; i < nl.length; i++ ) {
		    var nli = nl[ i ];
		    var icd = eval(nli.getElementsByTagName( 'icd' )[0].firstChild.nodeValue);
		    var iimg = nli.getElementsByTagName( 'href' )[0].firstChild.nodeValue;
		    var shadow = nli.getElementsByTagName( 'shadow' )[0].firstChild.nodeValue;
		    var param = nli.getElementsByTagName( 'param' )[0].firstChild.nodeValue;
			var p = param.split(",");

		    icon[icd] = new GIcon();
		    icon[icd].image = iimg;
		    icon[icd].iconSize = new GSize(eval(p[0]), eval(p[1]));
			if(shadow!='x'){
			    icon[icd].shadow = shadow;
			    icon[icd].shadowSize = new GSize(eval(p[2]), eval(p[3]));
			}
		    icon[icd].iconAnchor = new GLatLng(eval(p[5]),eval(p[4])); 
		    icon[icd].infoWindowAnchor = new GLatLng(eval(p[7]),eval(p[6])); 
	  	}
	}

	var lst='';
  	var nl = req.responseXML.getElementsByTagName( 'Placemark' );
  	for( var i = 0; i < nl.length; i++ ) {
	    var nli = nl[ i ];
	    var lid = eval(nli.getElementsByTagName( 'lid' )[0].firstChild.nodeValue);
	    var icd = eval(nli.getElementsByTagName( 'icd' )[0].firstChild.nodeValue);
	    var name = nli.getElementsByTagName( 'name' )[0].firstChild.nodeValue;
	    var coordinates = nli.getElementsByTagName( 'coordinates' )[0].firstChild.nodeValue;
	    var description = nli.getElementsByTagName( 'description' )[0].firstChild.nodeValue;

		lst += "<li><a href='javascript:void(0)' onclick='go("+lid+")'>"+name+"</a></li>";

		var p = coordinates.split(",");
		var ll=new GLatLng(eval(p[1]), eval(p[0]));
		if(icd==0)
			gn_mk[lid] = new GMarker(ll);
		else
			gn_mk[lid] = new GMarker(ll,icon[icd]);


		if(!gn_drkm){

			gn_map.addOverlay(gn_mk[lid]);
			var u='';
			if(gn_ulop) u = "&" + gn_ulop ; 
			gn_desc[lid]="<div style='width:250px;'><a href='"+gn_url+"/index.php?lid="+lid+u+"'>"+name+"</a><br />"+description+"</div>";

			GEvent.addListener( gn_mk[lid], "click", 
			    GEvent.callbackArgs( gn_mk[lid], function( lid ){ 
			        this.openInfoWindowHtml(gn_desc[lid]); 
			    },lid));
		}
  	}

	if(lst)
		lst = "<ul>" + lst + "</ul>";
	else
		lst = "<div>"+unescape(gn_lg['nodata'])+"</div>";

	document.getElementById("gn_mklist").innerHTML=lst;
	if(gn_l>0){gn_mk[gn_l].openInfoWindowHtml(gn_desc[gn_l]);}
}

function go(lid){
	gn_map.panTo(gn_mk[lid].getPoint());
	if(!gn_drkm)gn_mk[lid].openInfoWindowHtml(gn_desc[lid]);
}

function InputGMap() {
	if(GBrowserIsCompatible()){

		gn_mheight=document.getElementById("map").style.height;

		if(document.getElementById('mt').value && window[document.getElementById('mt').value]){
			gn_mt = eval(document.getElementById('mt').value);
		}else{
			gn_mt = G_NORMAL_MAP;
		}

		//initialize
		if(!InitializeGmap())return;
		gn_geo = new GClientGeocoder();

		//setcenter
		var c = new GLatLng(document.getElementById('lat').value,document.getElementById('lng').value);
	  	gn_map.setCenter(c,parseInt(document.getElementById('z').value));

		//setmarker
		var p = new Object();
		p.draggable = true;
		p.title = unescape(gn_lg['setpoint']);
		gn_mymk = new GMarker(c, p);
		gn_map.addOverlay(gn_mymk);

		//addListener
		GEvent.addListener(gn_map, 'click', function(overlay, point) {
			if (point) {
				gn_mymk.setPoint(point);
				DrawLatLngTxt(point);
		   	}
	    });
		GEvent.addListener(gn_map, 'zoomend',function(oldZoomLevel, newZoomLevel) {
	    	document.getElementById('z').value  =newZoomLevel;
	    	document.getElementById('sz').innerHTML  =newZoomLevel;
		});
	    GEvent.addListener(gn_mymk, GN_DRAG,function() {
	       	var p = gn_mymk.getPoint();
			DrawLatLngTxt(p);
	    });

		//addListener
		GEvent.addListener(gn_map, 'maptypechanged', function() {
			for (i in GN_MTX){
				if(eval(GN_MTX[i])==gn_map.getCurrentMapType()){
					document.getElementById('mt').value = GN_MTX[i];break;
				}
			}
	    });

		ChangeMapArea(document.getElementById('set_latlng'));

	}else{

		document.getElementById("geo").style.visibility = "hidden"; 
		if(document.getElementById("geo"))document.getElementById("map").innerHTML='<strong>'+unescape(gn_lg['gmapdisable'])+'</strong>';

	}
}

function showAddress(address) {

	//get latlng by address strings.

	if(!GBrowserIsCompatible()||address=='')return;

	if (gn_geo) {
       	gn_geo.getLatLng(address,function(point) {
			if (!point) {
				alert(address + unescape(gn_lg['notfound']));
			} else {
				if(gn_geoz<0){
					gn_map.setCenter(point);
				}else{
					gn_map.setCenter(point,gn_geoz);
				}
               	gn_mymk.setPoint(point);
               	gn_mymk.openInfoWindowHtml(address);
				DrawLatLngTxt(point);
           	}
       	});
	}

}

function showAddress2(address) {

	//get latlng by address strings.

	if(!GBrowserIsCompatible()||address=='')return;
	gn_geo = new GClientGeocoder();

	if (gn_geo) {
       	gn_geo.getLatLng(address,function(point) {
			if (!point) {
				//alert(address + unescape(gn_lg['notfound']));
			} else {
				if(gn_geoz<0){
					gn_map.setCenter(point);
				}else{
					gn_map.setCenter(point,gn_geoz);
				}
				gn_map.openInfoWindow(point,document.createTextNode(address));
				DrawLatLngTxt(point);
           	}
       	});
	}

}

function DrawLatLngTxt(point){

	document.getElementById('lat').value  = mround(point.y);
	document.getElementById('slat').innerHTML  = mround(point.y);
	document.getElementById('lng').value  = mround(point.x);
	document.getElementById('slng').innerHTML  = mround(point.x);

}

function ChangeMapArea(obj){

	if(obj.checked){
		document.getElementById("maparea").style.visibility = "hidden"; 
		document.getElementById("map").style.height = "0px"; 
		if(document.getElementById("geo"))document.getElementById("geo").style.visibility = "hidden"; 
	}else{
		document.getElementById("maparea").style.visibility = "visible"; 
		document.getElementById("map").style.height = gn_mheight; 
		if(document.getElementById("geo"))document.getElementById("geo").style.visibility = "visible"; 
	}

}

/*-----Ken's common func-----*/

function mround(value){
	return Math.round(parseFloat(value)*1000000)/1000000 ;
}

function include(u) {
	var h = document.getElementsByTagName( 'head' )[0];
	var s  = document.createElement( 'script' );
	s.charset = 'utf-8';
	s.type = 'text/javascript';
	s.src  = u;
	h.appendChild(s); 
}

function var_dumpj(mt,cnt,pre){
	var r="";
	for (i in mt){
		 r +=(pre+i+" = "+mt[i])+"<hr>";
		if(cnt>0 && typeof(mt[i])=="object"){
			r +=var_dumpj(mt[i],pre+"+----",cnt-1)
		}
	}
	return r;	
}
