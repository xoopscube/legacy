/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// fusen.js
// ohguma@rnc-com.co.jp

/////////////////////////////////////////////////
// xpWiki - XOOPS's PukiWiki module.
//
// fusen.js for xpWiki by nao-pon
// http://hypweb.net
// $Id: fusen.js,v 1.18 2009/03/20 06:37:00 nao-pon Exp $
// 
var fusenVar = new Array();
var fusenMsgs = new Array();

fusenVar['offsetX'] = 0;
fusenVar['offsetY'] = 0;

// browser check
fusenVar['GK'] = document.getElementById;         // Gecko or Opera or IE
fusenVar['IE'] = (document.all && !window.opera); // IE

// mouse position
fusenVar['mouseX'] = '';
fusenVar['mouseY'] = '';

var fusenObj;
var fusenMovingObj = null;
var fusenMovingFlg = false;
var fusenResizeFlg = false;
var fusenDustboxFlg = false;
var fusenFullFlg = new Array();
var fusenShowFlg = new Array();
var fusenNowMovingOff = false;
var fusenDblClick = false;
var fusenBodyStyle = 'fusen_body_trans';
var fusenLastModified = '';
var fusenTimerID;		//Interval Timer ID
var fusenRetTimerID;	//Retry Timer ID
var fusenFullTimerID = new Array();;
var fusenClickX = 0;
var fusenClickY = 0;
var fusenClickW = 0;
var fusenClickH = 0;
var fusenBusyFlg = false;
var fusenMinWidth = 8;
var fusenMinHeight = 8;
var fusenGetRetry = 0;
var fusenLoaded = false;
var fusenLines = new Array();

// Open window for object information.
function fusen_debugobj(objref) {
	var obj = null;
	var str = '';
	if (typeof(objref) == 'string') {
		obj = $(objref);
	} else {
		obj = objref;
	}
	if (obj) {
		for(i in obj) {
			if (!obj.hasOwnProperty(i)) continue;
			str += i + "=" + obj[i] + "\n";
		}
	}
	else str = objref;
	debugWin = window.open('', '');
	window.debugWin.document.write('<html>\n<body>\n<pre>\n' + str + '\n</pre>\n</body>\n</html>');
}

function fusen_setInterval(msec)
{
	fusenVar['Interval'] = msec;
	fusen_set_timer();
}

function fusen_set_timer()
{
	if (fusenTimerID) clearTimeout(fusenTimerID);
	if (fusenVar['Interval'] > 5000) {
		fusenTimerID = setInterval("fusen_init(0)", fusenVar['Interval']);
	}
}

function fusen_busy(busy)
{
	if (busy) {
		fusenBusyFlg = true;
	} else {
		fusenBusyFlg = false;
	}
	
	var set_cursor;
	var r_cursor;
	var w_cursor;
	var obj;
	
	r_cursor = (busy)? 'wait' : 'nw-resize';
	w_cursor = (busy)? 'wait' : 'w-resize';
	
	for(var id in fusenObj)
	{
		if (!isNaN(id)) {
			obj = $('fusen_id' + id);
			set_cursor = (fusenObj[id].lk)? 'auto' : 'move';
			set_cursor = (busy)? 'wait' : set_cursor;
			obj.style.cursor = set_cursor;
			if (busy) {
				obj.onmousedown = null;
			} else {
				fusen_set_onmousedown(obj,id);
			}

			$('fusen_id' + id + 'resize').style.cursor = r_cursor;
			$('fusen_id' + id + 'wresize').style.cursor = w_cursor;
		}
	}
}

// Create HTTP request object.
function fusen_httprequest(){
	try {
		return new XMLHttpRequest();
	} catch(e) {
		var MSXML_XMLHTTP_PROGIDS = new Array(
			'MSXML2.XMLHTTP.5.0',
			'MSXML2.XMLHTTP.4.0',
			'MSXML2.XMLHTTP.3.0',
			'MSXML2.XMLHTTP',
			'Microsoft.XMLHTTP'
		);
		for (var i in MSXML_XMLHTTP_PROGIDS) {
			try {
				return new ActiveXObject(MSXML_XMLHTTP_PROGIDS[i]);
			} catch (e) {}
		}
	}
	throw 'Unable to create HTTP request object.';
}

// Post fusen data.
function fusen_postdata(mode) {
	var frm = $('edit_frm');
	var re = /input|textarea|select/i;
	var tag = '';
	var postdata = '';
	
	if (fusenTimerID) clearTimeout(fusenTimerID);
	
	var w_starus = (fusenVar['Interval'])? (fusenMsgs['fusen_func'] + ": " + fusenMsgs['com_comp'] + " [" + fusenMsgs['refreshing'] + "(" + (fusenVar['Interval']/1000) + "s)" + fusenMsgs['waiting'] + "]") : (fusenMsgs['fusen_func'] + ": " + fusenMsgs['com_comp'] + " [" + fusenMsgs['refreshing'] + " " + fusenMsgs['stopping'] + "]");
	//window.status = fusenMsgs['connecting'];
	fusen_busy(1);
	var s_mode = '';
	
	for (var i = 0; i < frm.length; i++ ) {
		var child = frm[i];
		tag = String(child.tagName);
		if (tag.match(re)) {
			if (postdata != '') {
				postdata += '&';
			}
			postdata += encodeURIComponent(child.name) + '=' + encodeURIComponent(child.value);
			if (child.name == 'mode') {
				s_mode = child.value;
			}
		}
	}
	if (postdata) postdata += '&charset=UTF-8';
	if (s_mode == 'set') {
		fusenVar['PostUrl'];
	}
	
	try {
		var xmlhttp = fusen_httprequest();
		if (mode) {
			xmlhttp.onreadystatechange = readyStateChangeHandler;
		}
		if (s_mode == 'set') {
			xmlhttp.open('POST', fusenVar['PostUrl'] + 'gate.php?way=fusen&_nodos', mode);
		} else {
			xmlhttp.open('POST', fusenVar['PostUrl'], mode);
		}
		xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded;');
		xmlhttp.send(postdata);
	} catch(e) {
		fusen_busy(0);
		xmlhttp = null;
		alert(e);
		throw 'Unable to post fusen data.';
	}
	if (!mode) {
		//window.status = w_starus;
		fusen_busy(0);
		if(xmlhttp.status == 200 || xmlhttp.status == 0) {
			var ret = xmlhttp.responseText;
			xmlhttp = null;
			fusen_set_timer();
			return ret;
		} else {
			xmlhttp = null;
			fusenLastModified = '';
			alert (fusenMsgs['err_posting']);
		}
	}
	function readyStateChangeHandler()
	{
		//window.status = fusenMsgs['communicating'];
		if (xmlhttp.readyState == 4) {
			fusen_busy(0);
			//window.status = w_starus;
			try {
				if (xmlhttp.status == 200) {
					fusen_set_timer();
				} else {
					fusenLastModified = '';
					alert(fusenMsgs['err_posting']);
				}
			} catch(e) {
				fusenLastModified = '';
				alert(fusenMsgs['err_posting']);
			}
			xmlhttp = null;
			return;
		}
	}
}

// Get fusen date.
function fusen_getdata(mod)
{
	fusen_busy(1);
	if (fusenTimerID) clearTimeout(fusenTimerID);
	
	var w_starus = (fusenVar['Interval'])? (fusenMsgs['fusen_func'] + ": " + fusenMsgs['com_comp'] + " [" + fusenMsgs['refreshing'] + "(" + (fusenVar['Interval']/1000) + "s)" + fusenMsgs['waiting'] + "]") : (fusenMsgs['fusen_func'] + ": " + fusenMsgs['com_comp'] + " [" + fusenMsgs['refreshing'] + " " + fusenMsgs['stopping'] + "]");
	//window.status = fusenMsgs['connecting'];

	try {
		var xmlhttp = fusen_httprequest();
		var url = fusenVar['JsonUrl'];
		if (Prototype.Browser.Opera) {
			url += "&t=" + (new Date).getTime();
		}
		xmlhttp.onreadystatechange = readyStateChangeHandler;
		xmlhttp.open(mod, url, true);
		xmlhttp.send(null);
	} catch(e) {
		fusen_busy(0);
		if (confirm(fusenMsgs['err_notconnect'])) {
			if (fusenRetTimerID)  clearTimeout(fusenRetTimerID);
			fusenRetTimerID = setInterval("fusen_init(0)", 2000);
		} else {
			fusenVar['Interval'] = 0;
		}
		xmlhttp = null;
		return;
	}
	

	function readyStateChangeHandler()
	{
		//window.status = fusenMsgs['communicating'];
		var er = "";
		if (xmlhttp.readyState == 4) {
			//window.status = w_starus;
			try {
				if (xmlhttp.status == 200 || xmlhttp.status == 304 || xmlhttp.status == 404) {
					fusen_busy(0);
					var lm = xmlhttp.getResponseHeader("Last-Modified");
					if (mod == 'HEAD') {
						if (xmlhttp.status == 304 || (lm && fusenLastModified == lm) || xmlhttp.status == 404) {
							fusen_set_timer();
						} else {
							setTimeout("fusen_getdata('GET')", 100);
						}
						xmlhttp = null;
						return;
					}
					fusenLastModified = lm;
					if (xmlhttp.status == 404 || (xmlhttp.status == 200 && !xmlhttp.responseXML.getElementsByTagName('fusen').length)) {
						var txt = '{}';
					} else if (xmlhttp.status == 304) {
						fusen_set_timer();
						xmlhttp = null;
						return;
					} else {
						var txt = xmlhttp.responseXML.getElementsByTagName('fusen')[0].firstChild.nodeValue;
					}
					try
					{
						var obj = $('fusen_area');
						if (fusenVar['base']) {
							var pobj = $(fusenVar['base']);
						} else if (fusenVar['FromSkin']) {
							var pobj = $('fusen_anchor');
						} else {
							var pobj = $('fusen_area');
						}
						var o_left = 0;
						var o_top = 0;
						
						fusenMovingObj = null;
						fusenMovingFlg = false;
						
						fusenVar['BasePos'] = [0,0];
						var tgtElement = pobj;
						while (pobj != null) { 
							if (Element.getStyle(pobj, 'position') != 'static') {
								fusenVar['BasePos'] = [parseInt(pobj.offsetLeft), parseInt(pobj.offsetTop)];
								break;
							}
							o_left += parseInt(pobj.offsetLeft); 
							if (fusenVar['IE'] &&
								pobj == tgtElement && 
								pobj.tagName.toUpperCase() == 'DIV') {
								o_left -= parseInt(pobj.offsetLeft);
							}
							o_top += parseInt(pobj.offsetTop); 
							pobj = pobj.offsetParent;
						}
						$('edit_bx').value = o_left;
						$('edit_by').value = o_top;
						
						var frm = $('edit_frm');
						var re = /input|textarea|select/i;
						var tag = '';
						for (var i = 0; i < frm.length; i++ ) {
							var child = frm[i];
							tag = String(child.tagName);
						}
						
						var edit_item = new Array();
						var change_status = false;
						
						if (fusenObj) {
							eval( 'var fusenObj_N = ' + txt );
							
							// New or Edited Tags
							for (var id in fusenObj_N) {
								if (!isNaN(id)) {
									if (!fusenObj[id] || fusenObj[id].tt < fusenObj_N[id].tt) {
										change_status = true;
										edit_item[id] = true;
										if (fusenObj[id]) {
											$('fusen_id' + id).id = 'fusen_id_remove' + id;
										}
										fusenObj[id] = fusenObj_N[id];
									}
								}
							}
							// Deleted Tags
							for (var id in fusenObj) {
								if (!isNaN(id)) {
									if (!fusenObj_N[id]) {
										change_status = true;
										fusen_removelines(id);
										obj.removeChild($('fusen_id' + id));
										delete fusenObj[id];
									}
								}
							}
						} else {
							eval( 'fusenObj = ' + txt );
							edit_item = fusenObj;
							while (obj.childNodes.length > 0) obj.removeChild(obj.firstChild);
						}
						
						if (edit_item) {
							// Remove line connect info
							fusenLines = new Array();
							
							for(var id in edit_item) {
								if (!isNaN(id)) {
									// Edit auth
									fusenObj[id].auth = false;
									if (fusenVar['admin']) fusenObj[id].auth = true;
									else if (fusenVar['uid'] && fusenVar['uid'] == fusenObj[id].uid) fusenObj[id].auth = true;
									else if (!fusenObj[id].uid && fusenVar['ucd'] && fusenVar['ucd'] == fusenObj[id].ucd) fusenObj[id].auth = true;
									
									var cobj = fusen_create(id, fusenObj[id]);
									document.getElementById('fusen_area').appendChild(cobj);
									fusen_set_onmousedown(cobj,id)
									cobj.onmouseover = fusen_onmouseover;
									cobj.onmouseout = fusen_onmouseout;
									fusenFullFlg[id] = false;
									
									// Resize
									if (!fusenObj[id].fix) {
										fusen_size_init(cobj);
									}
									
									if (change_status && $('fusen_id_remove' + id)) {
										obj.removeChild($('fusen_id_remove' + id));
									}
									
									if (change_status) {fusen_setlines(id);}
								}
							}
							
							if (fusenDustboxFlg) {
								fusenDustboxFlg = false;
								fusen_dustbox();
							} else {
								if (!change_status) {fusen_setlines();}
							}
							
							if (!fusenLoaded) {
								var jump_id = (!location.hash)? '' : location.hash.replace(/^#fusen([\d]+)$/,"$1");
								if (!(!jump_id)) {
									fusen_select(jump_id,true);
									setInterval("fusen_select_clear()", 5000);
								}
								document.onmouseup = fusen_onmouseup;
								document.onmousemove = fusen_onmousemove;
								
								$("fusen_top_menu").style.visibility = 'visible';
								fusen_transparent();
							}
							
							change_status = true;
							fusenLoaded = true;
						}
						if (change_status) fusen_list_make();
						fusen_set_timer();
					} catch(e) {
						er = fusenMsgs['err_baddata'];
						fusenLastModified = '';
					}
				} else {
					er = fusenMsgs['err_notcommunicating'];
				}
			} catch(e) {
				er = fusenMsgs['err_notcommunicating'];
			}
			
			if (er) {
				if (fusenGetRetry++ >= 60/(fusenVar['Interval']/1000)) {
					fusenGetRetry = 0;
					fusen_busy(0);
					if (confirm(er + ' ' + fusenMsgs['msg_retryto'] + ' ' + url.replace(/^https?:\/\/([^\/]+).*$/,"$1"))) {
						if (fusenRetTimerID)  clearTimeout(fusenRetTimerID);
						fusenRetTimerID = setInterval("fusen_init(0)", 1000);
					} else {
						fusenVar['Interval'] = 0;
						//window.status = fusenMsgs['fusen_func']+": "+fusenMsgs['com_comp']+" ["+fusenMsgs['refreshing']+" "+fusenMsgs['stopping']+"]";
						$('fusen_menu_interval').selectedIndex = 0;
					}
				} else {
					fusen_set_timer();
				}
			} else {
				fusenGetRetry = 0;
			}
			if (typeof(initLightbox) == 'function' && change_status) {
				initLightbox();
			}
			er = '';
			xmlhttp = null;
			return;
		}
	}
}

// Get text in fusen.
function fusen_getchildtext(objref) {
	var obj;
	var output = '';
	if (typeof objref == 'string') obj = $(objref);
	else obj = objref;
	if (!obj) return '';
	var group = obj.childNodes;
	for (var i = 0; i < group.length; i++) {
		if (group[i].nodeType == 3) output += group[i].nodeValue.replace(/[\r\n]/,'');
		if (group[i].childNodes.length > 0) output += fusen_getchildtext(group[i]);
	}
	return output; 
}

function fusen_grep(pat) {
	fusenMovingObj = null;
	var re = new RegExp(pat, 'im');
	for(var id in fusenObj) {
		if (!isNaN(id)) {
			if (!fusenDustboxFlg && (fusenObj[id].del)) continue;
			if (fusenDustboxFlg && !(fusenObj[id].del)) continue;
			if (fusenObj[id].disp.match(re) || fusenObj[id].name.match(re)) {
				$('fusen_id' + id).style.visibility = "visible";
			} else {
				$('fusen_id' + id).style.visibility = "hidden";
			}
		}
	}
}

// editbox control
function fusen_new(dblclick)
{
	if (!fusenLoaded) return;
	
	if (fusenDustboxFlg) {
		fusen_dustbox();
		if (dblclick) return;
	}
	
	if (fusenShowFlg['fusen_editbox']) {fusen_show('fusen_editbox');return;}
	
	fusenMovingObj = null;
	
	if (fusenTimerID) clearTimeout(fusenTimerID);
	
	$('edit_id').value = '';
	$('edit_ln').value = '';
	$('tc000000').selected = true;
	$('bgffffff').selected = true;
	$(fusenVar['textarea']).value = '';
	$('edit_name').style.visibility = "visible";
	$('edit_l').value = fusenVar['mouseX'] - fusenVar['BasePos'][0];
	$('edit_t').value = fusenVar['mouseY'] - fusenVar['BasePos'][1];
	$('edit_w').value = 0;
	$('edit_h').value = 0;
	$('edit_fix').value = 0;
	$('edit_mode').value = 'edit';
	fusen_show('fusen_editbox');
}

function fusen_editbox_hide() {
	fusenMovingObj = null;
	fusen_hide('fusen_editbox');
	fusen_FCK2normal();
	fusen_set_timer();
}

function fusen_save()
{
	//fusen_FCK2normal();
	if (typeof FCKeditorAPI == "object") {
		var oEditor = FCKeditorAPI.GetInstance(fusenVar['textarea']);
		oEditor.UpdateLinkedField();
	}
	if ($('edit_mode').value == 'edit' && !$(fusenVar['textarea']).value) {
		alert(fusenMsgs['err_nottext']);
		return;
	}
	fusenDustboxFlg = false;
	fusen_postdata(false);
	fusen_init(1);
	fusen_hide('fusen_editbox');
	fusen_FCK2normal();
}

function fusen_FCK2normal() {
	if (typeof FCKeditorAPI == "object") {
		var tArea = $(fusenVar['textarea']);
		if (tArea.style.display == 'none') {
			XpWiki.toggleFCK(fusenVar['textarea']);
		} 
	}
}

function fusen_setpos(id,auto)
{
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	fusenMovingObj = null;
	
	var obj = $('fusen_id' + id);
	
	$('edit_id').value = id;
	$('edit_l').value = parseInt(obj.style.left.replace("px",""));
	$('edit_t').value = parseInt(obj.style.top.replace("px",""));
	if (auto) {
		$('edit_fix').value = 0;
		fusenObj[id].fix = 0;
		fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'');
		obj.style.overflow = 'visible';
		obj.style.whiteSpace = 'nowrap';
		obj.style.width = 'auto';
		obj.style.height = 'auto';
		fusen_size_init(obj);
		fusen_setlines(id);
	} else {
		$('edit_fix').value = fusenObj[id].fix;
	}
	if (fusenObj[id].fix) {
		$('edit_w').value = fusenObj[id].w = parseInt(obj.style.width.replace("px",""));
		$('edit_h').value = fusenObj[id].h = parseInt(obj.style.height.replace("px",""));
	} else {
		$('edit_w').value = fusenObj[id].w;
		$('edit_h').value = fusenObj[id].h;
	}
	
//	$('edit_z').value = $(id).style.zIndex;
	$('edit_mode').value = 'set';
	
	fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'');
	
	fusen_postdata(true);
}

function fusen_edit(id)
{
	if (fusenShowFlg['fusen_editbox']) {fusen_show('fusen_editbox');return;}

	if (fusenObj[id].lk) return;
	
	if (!fusenObj[id].auth) return;
	
	fusenMovingObj = null;

	if (fusenTimerID) clearTimeout(fusenTimerID);

	var obj = $('fusen_id' + id);
	var text_body = fusenObj[id].txt;
	
	text_body = text_body.replace(/&amp;/g,"&");
	text_body = text_body.replace(/&lt;/g,"<");
	text_body = text_body.replace(/&gt;/g,">");
	text_body = text_body.replace(/&quot;/g,"\"");
	
	$('edit_id').value = id;
	$('edit_l').value = parseInt(obj.style.left.replace("px",""));
	$('edit_t').value = parseInt(obj.style.top.replace("px",""));
	$('edit_ln').value = (fusenObj[id].ln) ? 'id' + fusenObj[id].ln : '';
	$('edit_name').value = fusenObj[id].name;
	$(fusenVar['textarea']).value = text_body;
	$('edit_mode').value = 'edit';
	$('edit_w').value = fusenObj[id].w;
	$('edit_h').value = fusenObj[id].h;
	$('edit_fix').value = fusenObj[id].fix;

	var tcid = fusenObj[id].tc;
	if (!tcid) tcid = 'tc000000';
	else tcid = 'tc' + tcid.substr(1);
	var tcobj = $(tcid);
	if (!tcobj) $('tc000000').selected = true;
	else $(tcid).selected = true;

	var bgid = fusenObj[id].bg;
	if (!bgid) bgid = 'bgffffff';
	else bgid = 'bg' + bgid.substr(1);
	var bgobj = $(bgid);
	if (!bgobj) $('bg000000').selected = true;
	else $(bgid).selected = true;

	fusen_show('fusen_editbox');
}

function fusen_link(id) {
	fusenMovingObj = null;
	$('edit_l').value = parseInt($('fusen_id'+id).style.left.replace("px",""));
	$('edit_t').value = parseInt($('fusen_id'+id).style.top.replace("px","")) + $('fusen_id'+id).offsetHeight + 10;
	$('edit_w').value = 0;
	$('edit_h').value = 0;
	$('edit_fix').value = 0;
	$('edit_id').value = '';
	$('edit_ln').value = 'id' + id;
	$('edit_name').style.visibility = "visible";
	$(fusenVar['textarea']).value = '';
	$('edit_mode').value = 'edit';
	fusen_show('fusen_editbox');
}

function fusen_del(id)
{
	fusenMovingObj = null;
	var ok;
	var mode;
	
	if (fusenDustboxFlg) {
		ok = confirm(fusenMsgs['msg_burn']);
		mode = false;
	} else {
		ok = confirm(fusenMsgs['msg_dustbox']);
		mode = true;
	}
	
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	if (ok) {
		$('edit_id').value = id;
		$('edit_mode').value = 'del';
		//$('edit_ln').value = (fusenObj[id].ln) ? 'id' + fusenObj[id].ln : '';
		
		// Reload
		$('fusen_id' + id).style.visibility = "hidden";
		fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'del');
		$('fusen_id' + id).style.border = fusenVar['BorderObj']['del'];
		fusenObj[id].del = true;
		
		if (!fusenDustboxFlg) {
			//fusen_dustbox();
			fusen_list_make();
			fusen_removelines(id);
			fusen_setlines(id);
		}
		
		// Server side update
		fusen_postdata(true);
		if (!mode) {
			$('fusen_area').removeChild($('fusen_id' + id));
			delete fusenObj[id];
			fusen_list_make();
		}
	}
}

function fusen_del_multi()
{
	fusenMovingObj = null;
	var ok;
	
	ok = confirm(fusenMsgs['msg_dustall']);
	
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	if (ok) {
		var ids = new Array();
		var elm;
		for(var id in fusenObj) {
			if (!isNaN(id)) {
				elm = $('list_delbox_'+id);
				if(elm && elm.checked == true) {
					ids[id] = id;
				}
			}
		}
		$('edit_id').value = ids.join(",");
		$('edit_mode').value = 'del_m';
		for(var id in ids) {
			if (!isNaN(id)) {
				// Reload
				$('fusen_id' + id).style.visibility = "hidden";
				fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'del');
				$('fusen_id' + id).style.border = fusenVar['BorderObj']['del'];
				fusenObj[id].del = true;
				
				if (!fusenDustboxFlg) {
					fusen_removelines(id);
					fusen_setlines(id);
				}
			}
		}
		
		// Server side update
		fusen_postdata(true);
		fusen_list_make();
	}
}

function fusen_recover(id)
{
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	fusenMovingObj = null;
	$('edit_id').value = id;
	$('edit_mode').value = 'recover';
	//$('edit_ln').value = (fusenObj[id].ln) ? 'id' + fusenObj[id].ln : '';
	
	// Reload
	fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'');
	$('fusen_id' + id).style.border = fusenVar['BorderObj']['normal'];
	$('fusen_id' + id).style.visibility = "visible";
	fusenObj[id].del = false;
	fusen_dustbox();
	fusen_list_make();
	
	// Server side update
	fusen_postdata(true);
}

function fusen_burn()
{
	fusenMovingObj = null;
	var ok;
	//var mode = "burn";
	
	if (!fusenDustboxFlg) fusen_dustbox();
	
	ok = confirm(fusenMsgs['msg_emptydustbox']);
	if (!ok) return;
	
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	$('edit_id').value = "0";
	$('edit_mode').value = 'burn';

	// Server side update
	fusen_postdata(false);
	fusen_init(1);
	alert (fusenMsgs['emptydustbox']);
	fusen_dustbox();

}

function fusen_lock(id)
{
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	fusenMovingObj = null;
	$('edit_id').value = id;
	$('edit_mode').value = 'lock';
	fusenObj[id].lk = true;
	fusen_set_onmousedown($('fusen_id' + id),id);
	
	// Reload
	$('fusen_id' + id).onmousedown = null;
	fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'lock');
	$('fusen_id' + id).style.border = fusenVar['BorderObj']['lock'];
	$('fusen_id' + id).style.cursor = 'auto';
	$('fusen_id' + id + 'resize').style.visibility = 'hidden';
	$('fusen_id' + id + 'wresize').style.visibility = 'hidden';
	fusen_show_full(id,'close');

	// Server side update
	fusen_postdata(true);
}

function fusen_unlock(id)
{
	if (fusenBusyFlg) {
		alert(fusenMsgs['now_communicating']);
		return;
	}
	
	fusenMovingObj = null;
	$('edit_id').value = id;
	$('edit_mode').value = 'unlock';
	fusenObj[id].lk = false;
	fusen_set_onmousedown($('fusen_id' + id),id);
	
	// Reload
	fusen_set_menu_html($('fusen_id' + id + 'menu'),id,'');
	$('fusen_id' + id).style.border = fusenVar['BorderObj']['normal'];
	$('fusen_id' + id).style.cursor = 'move';
	$('fusen_id' + id + 'resize').style.visibility = 'visible';
	$('fusen_id' + id + 'wresize').style.visibility = 'visible';
	fusen_show_full(id,'close');

	// Server side update
	fusen_postdata(true);
}

function fusen_show(id)
{
	if (fusenVar['ReadOnly'] && id == 'fusen_editbox') {
		return;
	}
	
	fusenShowFlg[id] = true;
	if (fusenTimerID) clearTimeout(fusenTimerID);
	
	if (id == 'fusen_editbox') {
		var top = $('edit_t').value;
		var left = $('edit_l').value;
	} else {
		var top = fusenVar['mouseY'] - fusenVar['BasePos'][1];
		var left = fusenVar['mouseX'] - fusenVar['BasePos'][0];
	}

	$(id).style.left = left + "px";
	$(id).style.top = top + "px";
	
	$(id).style.zIndex = (id == 'fusen_help')? 120 : 100;
	$(id).style.visibility = "visible";
	$(id).onmousedown = fusen_onmousedown;
	
	if (id == 'fusen_editbox')
		setTimeout(function(){$(fusenVar['textarea']).focus();window.scrollTo(left,Math.max(0,top - 100));},10);
	else
		setTimeout(function(){window.scrollTo(left,Math.max(0,top - 100));},10);

}

function fusen_hide(id)
{
	if (id == 'fusen_list') fusen_select_clear();
	fusenShowFlg[id] = false;
	$(id).style.visibility = "hidden";
	$(id).style.left = "-1000px";
	$(id).style.top = "-1000px";
	document.onmouseup = fusen_onmouseup;
	document.onmousemove = fusen_onmousemove;
	fusenDblClick = false;
	fusenMovingObj = null;
	$(fusenVar['textarea']).blur();
	$("edit_ln").blur();
	//$('edit_name').style.visibility = "hidden";
	fusen_set_timer();
}

function fusen_dustbox()
{
	/*
	if (fusenBusyFlg)
	{
		alert(fusenMsgs['now_communicating']);
		return;
	}
	*/
	
	fusenMovingObj = null;
	fusenDustboxFlg = !fusenDustboxFlg;
	for(var id in fusenObj) {
		if (!isNaN(id)) {
			var obj = $('fusen_id' + id);
			if (fusenObj[id].del) {
				if (fusenDustboxFlg) obj.style.visibility = 'visible';
				else obj.style.visibility = 'hidden';
			} else {
				if (fusenDustboxFlg) obj.style.visibility = 'hidden';
				else obj.style.visibility = 'visible';
			}
		}
	}
	if (fusenDustboxFlg) {
		fusen_removelines();
		if (fusenTimerID) clearTimeout(fusenTimerID);
	} else {
		fusen_setlines();
		fusen_set_timer();
	}
}

function fusen_transparent()
{
	if ($('fusen_area')) {
		if (fusenBodyStyle != 'fusen_body') {
			fusenBodyStyle = 'fusen_body';
		} else {
			fusenBodyStyle = 'fusen_body_trans';
		}
		for (var i = 0; i < $('fusen_area').childNodes.length; i++ ) {
			if ($('fusen_area').childNodes[i].id.indexOf('fusen_id') == 0) {
				$('fusen_area').childNodes[i].className = fusenBodyStyle;
			}
		}
	}
}

function fusen_set_menu_html(tobj,id,mode)
{
	//tobj.innerHTML = '<a name="fusenid' + id + '"></a>id.' + id + ': ';
	tobj.innerHTML = 'id.' + id + ': ';
	if (!fusenVar['ReadOnly']) {
		if (mode == 'del') {
			if (fusenObj[id].auth) {
				tobj.innerHTML +=
					' <a href="javascript:fusen_recover(' + id + ')" title="' + fusenMsgs['recover'] + '">recover</a>' +
					' <a href="javascript:fusen_del(' + id + ')" title="' + fusenMsgs['burn'] + '">del</a>';
			}
		} else if (mode == 'lock') {
			if (fusenObj[id].auth) {
				tobj.innerHTML +=
					' <a href="javascript:fusen_unlock(' + id + ')" title="' + fusenMsgs['unlock'] + '">unlock</a>';
			}
			tobj.innerHTML +=
				' <a href="javascript:fusen_link(' + id + ')" title="' + fusenMsgs['new_with_line'] + '">line</a>';
		} else {
			if (fusenObj[id].auth) {
				tobj.innerHTML +=
					' <a href="javascript:fusen_edit(' + id + ')" title="' + fusenMsgs['edit'] + '">edit</a>';
				tobj.innerHTML +=
					' <a href="javascript:fusen_lock(' + id + ')" title="' + fusenMsgs['lock'] + '">lock</a>';
			}
			tobj.innerHTML +=
				' <a href="javascript:fusen_link(' + id + ')" title="' + fusenMsgs['new_with_line'] + '">line</a>';
			if (fusenObj[id].auth) {	
				tobj.innerHTML +=
					' <a href="javascript:fusen_del(' + id + ')" title="' + fusenMsgs['to_dustbox'] + '">del</a>';
			}
			if (fusenObj[id].fix) {
				tobj.innerHTML +=
					' <a href="javascript:fusen_setpos(' + id + ',1)" title="' + fusenMsgs['auto_resize'] + '">auto</a>';
			}
		}
	}
	return;
}

function fusen_create_menuobj(id, mode, obj) {
	var cobj = document.createElement("DIV");
	cobj.className = 'fusen_menu';
	cobj.id = 'fusen_id' + id + 'menu';
	fusen_set_menu_html(cobj,id,mode);
	return cobj;
}

function fusen_create_infoobj(id, obj) {
	var cobj = document.createElement("DIV");
	var d = (obj.et != "")? " : " + obj.et.substring(0,2) + "/" + obj.et.substring(2,4) + "/" + obj.et.substring(4,6) + " " + obj.et.substring(6,8) + ":" + obj.et.substring(8,10) : "";
	var md = (obj.mt != "")? obj.mt.substring(0,2) + "/" + obj.mt.substring(2,4) + "/" + obj.mt.substring(4,6) + " " + obj.mt.substring(6,8) + ":" + obj.mt.substring(8,10) : "";
	

	
	cobj.className = 'fusen_info';
	cobj.id = 'fusen_id' + id + 'info';
	cobj.innerHTML = '<span class="fusen_name" title="' + fusenMsgs['owner']+ ' at '+md+'">' + obj.name + '</span> : ' + '<span class="fusen_date" title="' + fusenMsgs['lastedit_time'] + d + '">' + md + '</span>';
	cobj.onmouseout = fusen_moving_on;
	cobj.onmouseover = fusen_moving_off;
	return cobj;
}

function fusen_create_contentsobj(id, obj) {
	var cobj = document.createElement("DIV");
	cobj.className = 'fusen_contents';
	cobj.id = 'fusen_id' + id + 'contents';
	cobj.innerHTML = obj.disp;
	cobj.onmouseout = fusen_moving_on;
	cobj.onmouseover = fusen_moving_off;
	cobj.title = '';
	return cobj;
}

function fusen_create_resizeobj(id,obj) {
	var cobj = document.createElement("IMG");
	cobj.className = 'fusen_resize';
	cobj.id = 'fusen_id' + id + 'resize';
	cobj.src = wikihelper_root_url + '/skin/loader.php?src=resize.gif';
	cobj.title = cobj.alt = 'Resize';
	cobj.onmousedown = function(){fusenResizeFlg=1;return true;};
	if (obj.lk) {
		cobj.style.visibility = 'hidden';
	}
	return cobj;
}

function fusen_create_wresizeobj(id,obj) {
	var cobj = document.createElement("IMG");
	cobj.className = 'fusen_wresize';
	cobj.id = 'fusen_id' + id + 'wresize';
	cobj.src = wikihelper_root_url + '/skin/loader.php?src=w_resize.gif';
	cobj.title = cobj.alt = 'Set Width';
	cobj.onmousedown = function(){fusenResizeFlg=2;return true;};
	if (obj.lk)
	{
		cobj.style.visibility = 'hidden';
	}
	return cobj;
	/*
	var iobj = document.createElement("A");
	iobj.name = "fusenid"+id;
	iobj.appendChild(cobj);
	return iobj;
	*/
}

function fusen_create(id, obj) {
	var fusenobj = document.createElement("DIV");
	var menuobj;
	var border;
	var visible = 'visible';
	var ox = obj.x;
	var oy = obj.y;
	
	if (obj.del) {
		menuobj =  fusen_create_menuobj(id, 'del', obj);
		border = fusenVar['BorderObj']['del'];
		visible = 'hidden';
	} else  if (obj.lk) {
		menuobj =  fusen_create_menuobj(id, 'lock', obj);
		border = fusenVar['BorderObj']['lock'];
		fusenobj.style.cursor = 'auto';
	} else {
		menuobj =  fusen_create_menuobj(id, 'normal', obj);
		border = fusenVar['BorderObj']['normal'];
		if (fusenObj[id].auth) {
			fusenobj.title = fusenMsgs['dbc2edit'];
		} else {
			fusenobj.title = "";
		}
	}
	
	// Locked?
	if (obj.lk) {
		fusenobj.style.cursor = 'auto';
	} else {
		fusenobj.style.cursor = 'move';
	}
	
	// Fixed¡©
	if (obj.fix) {
		fusenobj.style.overflow = 'hidden';
		fusenobj.style.whiteSpace = 'normal';
		fusenobj.style.width = obj.w + 'px';
		fusenobj.style.height = (obj.fix == 1)? obj.h + 'px' : 'auto';
		if (obj.fix == 1) fusenobj.title = fusenMsgs['dbc2showall'];
	}
	
	if (obj.bx) ox += parseInt($('edit_bx').value) - obj.bx;
	if (obj.by) oy += parseInt($('edit_by').value) - obj.by;
	
	ox = Math.max(0,ox);
	oy = Math.max(0,oy);
	
	fusenobj.id = 'fusen_id' + id;
	fusenobj.className = fusenBodyStyle;
	fusenobj.style.left = ox + 'px';
	fusenobj.style.top =  oy + 'px';
	fusenobj.style.color = obj.tc;
	fusenobj.style.backgroundColor = obj.bg;
	fusenobj.style.zIndex = obj.z;
	fusenobj.style.border = border;
	fusenobj.style.visibility = visible;
	fusenobj.appendChild(menuobj);
	fusenobj.appendChild(fusen_create_infoobj(id, obj));
	fusenobj.appendChild(fusen_create_contentsobj(id, obj));
	fusenobj.appendChild(fusen_create_wresizeobj(id, obj));
	fusenobj.appendChild(fusen_create_resizeobj(id, obj));
	fusenobj.ondblclick = fusen_ondblclick;
	
	return fusenobj;
}


// Line draw

function fusen_removelines(t_id) {
	var id, lineid, obj;
	for(id in fusenObj) {
		if (!isNaN(id)) {
			if (fusenObj[id].ln) {
				if (!t_id || t_id == id || t_id == fusenObj[id].ln) {
					lineid = 'line' + id + '_' + fusenObj[id].ln;
					obj = $(lineid);
					if (obj) obj.parentNode.removeChild(obj);
				}
			}
		}
	}
}

function fusen_setlines(t_id)
{
	if (fusenLines[t_id] instanceof Array) {
		if (fusenLines[t_id]) {
			for (var id in fusenLines[t_id]) {
				if (!isNaN(id)) {
					fusen_setline2(id, fusenObj[id].ln);
				}
			}
		}
	} else {
		if (!!t_id) fusenLines[t_id] = new Array();
		for(var id in fusenObj) {
			if (!isNaN(id)) {
				if (fusenObj[id].ln && !fusenObj[id].del && fusenObj[fusenObj[id].ln] && !fusenObj[fusenObj[id].ln].del) {
					if (!t_id || t_id == id || t_id == fusenObj[id].ln) {
						fusen_setline2(id, fusenObj[id].ln);
						if (!!t_id) {fusenLines[t_id][id] = true;}
					}
				}
			}
		}
	}
}

function fusen_setline2(fromid, toid)
{
	try
	{
	function getCenter(obj){
		x = parseInt(obj.style.left.replace("px",""));
		x = x + obj.offsetWidth / 2;
		return x;
	}
	function getVCenter(obj){
		y = parseInt(obj.style.top.replace("px",""));
		y = y + obj.offsetHeight / 2;
		return y;
	}

	var lineid = 'line' + fromid + '_' + toid;
	var obj = $(lineid);
	if (obj) obj.parentNode.removeChild(obj);
	var fobj = $('fusen_id' + fromid);
	var tobj = $('fusen_id' + toid);
	if(!fobj) return;
	if(!tobj) return;
	
	var fx = getCenter(fobj);
	var fy = getVCenter(fobj);
	var fw = fobj.offsetWidth / 2;
	var fh = fobj.offsetHeight / 2;
	
	var tx = getCenter(tobj);
	var ty = getVCenter(tobj);
	var tw = tobj.offsetWidth / 2;
	var th = tobj.offsetHeight / 2;

	var ft = parseInt(fobj.style.top.replace("px","")) - 1;
	var fb = ft + fobj.offsetHeight;
	var fl = parseInt(fobj.style.left.replace("px","")) - 1;
	var fr = fl + fobj.offsetWidth;
	
	var tt = parseInt(tobj.style.top.replace("px","")) - 1;
	var tb = tt + tobj.offsetHeight;
	var tl = parseInt(tobj.style.left.replace("px","")) - 1;
	var tr = tl + tobj.offsetWidth;

	if (!fusenVar['IE']) {
//		fb += 2;
//		fr += 2;
//		tb += 2;
//		tr += 2;
	}
	
	var lx;
	var ly;
	var lh;
	var lw;
	
	if (fx < tr && fb  < ty ) {
		// Left Top
		lx = fx;
		ly = fb + 1;
		lw = tl - lx;
		lh = ty - ly;
		if (!fusenVar['IE']) {
			lw += 2;
			lh += 2;
		}
		border = 4;
		if (tl < fx) {
			lh = tt - ly;
			if (!fusenVar['IE']) lh ++;
			lw = 0;
		} else if (fb > ty) {
			lx = fr + 2;
			lh = 0;
			lw = lw - fw - 2;
		}
	} else if (fx >= tr && fb < ty) {
		// Right Top
		lx = tr + 1;
		ly = fb + 1;
		lw = fx - lx - 1;
		lh = ty - ly;
		if (!fusenVar['IE']) {
			lw += 2;
			lh += 2;
		}
		border = 3;
		if (fx <= tr) {
			lx = fx;
			lh = tt - ly;
			lw = 0;
		} else if (fb > ty) {
			lw = lw - fw - 1;
			if (!fusenVar['IE']) lw ++;
			lh = 0;
		}
	} else if (fx >= tr && fb >= ty) {
		// Right Bottom
		lx = tr + 1;
		ly = ty;
		lw = fx - lx - 1;
		lh = ft - ly;
		if (!fusenVar['IE']) {
			ly ++;
			lw += 2;
			lh += 1;
		}
		border = 2;
		if (fx <= tr) {
			lx = fx + 1;
			ly = tb;
			lw = 0;
		} else if (ft < ly) {
			lw = fl - tr - 1;
			if (!fusenVar['IE']) lw ++;
			lh = 0;
		}
	} else {
		// Left Bottom
		lx = fx;
		ly = ty;
		lw = tl - lx;
		lh = ft - ly;
		if (!fusenVar['IE']) {
			ly ++;
			lw += 2;
			lh += 1;
		}
		border = 1;
		if (tl < fx) {
			lx = fx + 1;
			ly = tb + 1;
			lw = 0;
			lh = ft - tb - 1;
			if (!fusenVar['IE']) lh += 2;
		} else if (ft < ly) {
			lx = fr + 1;
			lw = tl - fr - 1;
			if (!fusenVar['IE']) lw ++;
			lh = 0;
		}
	}
	
	var obj = fusen_drawLine2(lx, ly, lw, lh, '#000000', lineid, border);
	if (!fusenVar['IE']) {
		obj.observe('mouseover', function(){
			obj.style.visibility = 'hidden';
			setTimeout(function(){obj.style.visibility = 'visible'}, 5000);
		});
		fobj.observe('mouseover', function(){obj.style.visibility = 'visible'});
		tobj.observe('mouseover', function(){obj.style.visibility = 'visible'});
	}
	document.getElementById('fusen_area').appendChild(obj);
	}
	catch(e) {
		//alert(e);
	}
}

function fusen_drawLine2(x, y, w, h, color, nid, border){
	function _drawLine(x,y,w,h,color,b)
	{
		x = Math.max(0,parseInt(x));
		y = Math.max(0,parseInt(y));
		w = Math.max(1,parseInt(w));
		h = Math.max(1,parseInt(h));
		//window.status = x+','+y+','+w+','+h+','+color+','+b;
		var objLine = document.createElement("DIV");
		var strColor = color;
		objLine.style.backgroundColor = 'transparent';
		objLine.style.position  = "absolute";
		objLine.style.overflow  = "hidden";
		objLine.style.width     = w + "px";
		objLine.style.height    = h + "px";
		objLine.style.top  = y + "px";
		objLine.style.left = x + "px";
		objLine.style.borderColor = color;
		objLine.style.borderColor = "blue";
		objLine.style.borderWidth = "0px";
		objLine.style.borderStyle = "solid";
		objLine.style.zIndex = 0;
		if (border == 1) {objLine.style.borderTopWidth = "1px"; objLine.style.borderLeftWidth = "1px";}
		if (border == 2) {objLine.style.borderTopWidth = "1px"; objLine.style.borderRightWidth = "1px";}
		if (border == 3) {objLine.style.borderBottomWidth = "1px"; objLine.style.borderRightWidth = "1px";}
		if (border == 4) {objLine.style.borderBottomWidth = "1px"; objLine.style.borderLeftWidth = "1px";}
		return objLine;
	}
	
	function _Img1(x,y,w,h,color,b)
	{
		var obj = document.createElement("img");
		obj.src = wikihelper_root_url + "/skin/loader.php?src=connect.gif";
		obj.style.zIndex = 0;
		obj.style.position  = "absolute";
		if (fusenVar['IE']) {
			var ox = 3;
			var oy = 3;
		} else {
			var ox = 4;
			var oy = 4;
		}
		if (border == 1){obj.style.top = (y + h - oy) + "px";obj.style.left = (x - ox) + "px";}
		if (border == 2){obj.style.top = (y + h - oy) + "px";obj.style.left = (x + w - ox) + "px";}
		if (border == 3){obj.style.top = (y - oy) + "px";obj.style.left = (x + w - ox) + "px";}
		if (border == 4){obj.style.top = (y - oy) + "px";obj.style.left = (x - ox) + "px";}
		return obj;
	}
	function _Img2(x,y,w,h,color,b)
	{
		var obj = document.createElement("img");
		obj.src = wikihelper_root_url + "/skin/loader.php?src=connect.gif";
		obj.style.zIndex = 0;
		obj.style.position  = "absolute";
		if (fusenVar['IE']) {
			var ox = 3;
			var oy = 3;
		} else {
			var ox = 4;
			var oy = 4;
		}
		if (border == 1){obj.style.top = (y - oy) + "px";obj.style.left = (x + w - ox) + "px";}
		if (border == 2){obj.style.top = (y - oy) + "px";obj.style.left = (x - ox) + "px";}
		if (border == 3){obj.style.top = (y + h - oy) + "px";obj.style.left = (x - ox) + "px";}
		if (border == 4){obj.style.top = (y + h - oy) + "px";obj.style.left = (x + w - ox) + "px";}
		return obj;
	}

	var objLines = document.createElement("div")
	objLines.id = nid;
	objLines.appendChild(_drawLine(x, y, w, h, color, border));
	objLines.appendChild(_Img1(x, y, w, h, color, border));
	objLines.appendChild(_Img2(x, y, w, h, color, border));
	return objLines;
}


// Event

function fusen_onmousedown(e) {

	if (fusenVar['IE']) {
		if (event.button != 1) return;
		var tag = String(event.srcElement.tagName);
	} else {
		if (e.which != 1) return;
		var tag = String(e.target.tagName);
	}
	
	if (!tag.match(/div|img|form|ul|li|dl|dd|dt/i)) {
		this.cancelBubble = true;
		return;
	}
	
	if (fusenNowMovingOff) return;
	
	//fusen_select_clear();
	if (fusenTimerID) clearTimeout(fusenTimerID);
	
	fusenMovingObj = this;
	fusenClickW = fusenMovingObj.offsetWidth - 2;
	fusenClickH = fusenMovingObj.offsetHeight - 2;
	if (fusenVar['IE']) {
		fusenVar['offsetX'] = event.clientX - fusenMovingObj.style.posLeft;
		fusenVar['offsetY'] = event.clientY - fusenMovingObj.style.posTop;
		fusenClickX = event.clientX;
		fusenClickY = event.clientY;
	} else {
		fusenVar['offsetX'] = e.pageX - parseInt(fusenMovingObj.style.left.replace("px",""));
		fusenVar['offsetY'] = e.pageY - parseInt(fusenMovingObj.style.top.replace("px",""));
		fusenClickX = e.pageX;
		fusenClickY = e.pageY;

	}
	for(var id in fusenObj) {
		if (!isNaN(id)) {
			$('fusen_id' + id).style.zIndex = 1;
		}
	}
	fusenMovingObj.style.zIndex = 90;
	fusenMovingFlg = false;
	return false;
}

function fusen_onmousemove(e)
{
	if (!fusenMovingObj) return;

	var nowpos;
	if(fusenVar['IE']) {
		nowpos = event.clientX + "," + event.clientY;
	} else {
		nowpos = e.pageX + "," + e.pageY;
	}
	
	if (fusenMovingObj && nowpos != (fusenClickX + "," + fusenClickY)) {
		this.cancelBubble = true;
		fusenMovingFlg = true;

		var id = fusenMovingObj.id.replace('fusen_id','');
		if (fusenResizeFlg) {
			if (fusenVar['IE']) {
				var x = Math.max(fusenMinWidth,fusenClickW + (event.clientX - fusenClickX));
				var y = Math.max(fusenMinHeight,fusenClickH + (event.clientY - fusenClickY));
				fusenMovingObj.style.width = x + "px";
				if (fusenResizeFlg == 1) fusenMovingObj.style.height = y + "px";
			} else {
				var x = Math.max(fusenMinWidth,fusenClickW + (e.pageX - fusenClickX));
				var y = Math.max(fusenMinHeight,fusenClickH + (e.pageY - fusenClickY));
				fusenMovingObj.style.width = x + "px";
				if (fusenResizeFlg == 1) fusenMovingObj.style.height = y + "px";
			}
			if (fusenResizeFlg == 1) {
				$('fusen_id' + id).style.overflow = "hidden";
				$('fusen_id' + id).style.whiteSpace = 'normal';
			} else {
				$('fusen_id' + id).style.overflow = "hidden";
				$('fusen_id' + id).style.whiteSpace = 'normal';
				$('fusen_id' + id).style.height = 'auto';
			}
			fusenObj[id].fix = fusenResizeFlg;
			//window.status = fusenMsgs['fusen']+" "+id+" "+fusenMsgs['resizing']+"[ W:"+x+", H:"+y+" ]";
		} else {
			if (fusenVar['IE']) {
				var x = event.clientX + document.body.scrollLeft - fusenVar['offsetX'];
				var y = event.clientY + document.body.scrollTop - fusenVar['offsetY'];
			} else {
				var x = (e.pageX - fusenVar['offsetX']);
				var y = (e.pageY - fusenVar['offsetY']);
			}
			fusenMovingObj.style.left = x + "px";
			fusenMovingObj.style.top = y + "px";
			//window.status = fusenMsgs['fusen']+" "+id+" "+fusenMsgs['moving']+"[ X:"+x+", Y:"+y+" ]";
		}
		if (!fusenDustboxFlg) {fusen_setlines(id);}
		return false;
	}
}

function fusen_onmouseup(e) {
	if (!fusenDustboxFlg && fusenMovingFlg && fusenMovingObj && fusenMovingObj.id.indexOf('fusen_id') == 0) {
		var id = fusenMovingObj.id.replace('fusen_id','');
		if (!fusenResizeFlg && fusenObj[id].fix) {
			fusenMovingObj = null;
			fusen_show_full(id,'close');
		}
		fusen_setpos(id,0);
	}
	fusenMovingObj = null;
	//window.status = "";
	fusenMovingFlg = false;
	fusenResizeFlg = false;
	fusen_set_timer();
}

function fusen_ondblclick(e)
{
	var id = parseInt(this.id.replace('fusen_id',''));
	
	if (id) {
		if (!fusenFullFlg[id]  && fusenObj[id].fix == 1) {
			fusenDblClick = true;
			fusen_show_full(id,'open');
		} else if (!fusenObj[id].lk) {
			fusenMovingObj = null;
			fusenDblClick = true;
			fusen_edit(id);
		}
	}
	return;
}

function fusen_moving_off()
{
	if (fusenMovingFlg) return true;
	fusenNowMovingOff = true;
	fusenMovingObj = null;
}

function fusen_moving_on()
{
	if (fusenMovingFlg) return true;
	fusenNowMovingOff = false;
}

function fusen_set_onmousedown(obj,id)
{
	if (!id) return;
	
	if (fusenObj[id].lk) {
		obj.onmousedown = null;
	} else {
		obj.onmousedown = fusen_onmousedown;
	}

}
function fusen_onmouseover(e)
{
	var id = parseInt(this.id.replace('fusen_id',''));
	if (fusenFullTimerID[id]) clearTimeout(fusenFullTimerID[id]);
	if (fusenFullFlg[id]) {
		//if (fusenFullTimerID[id]) clearTimeout(fusenFullTimerID[id]);
	} else {
		if (fusenObj[id].fix && (fusenObj[id].w == fusenMinWidth || fusenObj[id].h == fusenMinHeight)) {
			eval('fusenFullTimerID[' + id + ']=setInterval("fusen_show_full(' + id + ',\'open\')", 500);');
		}
	}
	return;
}

function fusen_onmouseout(e)
{
	var id = parseInt(this.id.replace('fusen_id',''));

	if (id && !fusenMovingObj) {
		if (fusenObj[id].fix) {
			if (fusenFullTimerID[id]) clearTimeout(fusenFullTimerID[id]);
			if (fusenFullFlg[id]) {
				eval('fusenFullTimerID[' + id + ']=setInterval("fusen_show_full(' + id + ',\'close\')", 500);');
			}
		}
	}
	return;
}

function fusen_show_full(id,mode)
{
	var obj = $('fusen_id' + id);
	
	if (fusenFullTimerID[id]) clearTimeout(fusenFullTimerID[id]);
	
	if (fusenMovingObj) return;
	
	if (fusenObj[id].fix)
	{
		if (mode == 'open') {
			fusenFullFlg[id] = true;
			obj.style.height = 'auto';
			obj.style.zIndex = 90;
 			obj.title = (!fusenObj[id].auth || fusenObj[id].lk)? '' : fusenMsgs['dbc2edit'];
			if (fusenObj[id].w < 50) {
				fusen_size_init(obj);
			}
		} else {
			if (fusenObj[id].w && fusenObj[id].h) {
				obj.style.overflow = 'hidden';
				obj.style.whiteSpace = 'normal';
				obj.style.width = fusenObj[id].w + 'px';
				obj.style.height = fusenObj[id].h + 'px';
				obj.style.zIndex = 1;
				obj.title = (fusenObj[id].fix == 1)? fusenMsgs['dbc2showall'] : '';
			}
			fusenFullFlg[id] = false;
			fusenDblClick = false;
		}
		fusen_setlines();
	}
}

function fusen_select(selectid,nolist)
{
	if (!fusenObj[selectid]) return;
	
	var top = parseInt($('fusen_id' + selectid).style.top);
	var left = parseInt($('fusen_id' + selectid).style.left);
	
	var dustbox = fusenObj[selectid].del;
	
	if (fusenObj[selectid].fix && (fusenObj[selectid].w <= fusenMinWidth || fusenObj[selectid].h <= fusenMinHeight)) {
		fusen_show_full(selectid,'open');
		eval('fusenFullTimerID[' + selectid + ']=setInterval("fusen_show_full(' + selectid + ',\'close\')", 10000);');
	}
	
	fusen_editbox_hide();
	fusen_select_clear('on');
	$('fusen_id' + selectid).style.border = fusenVar['BorderObj']['select'];
	$('fusen_id' + selectid).className = 'fusen_body';
	$('fusen_id' + selectid).style.zIndex = 150;
	if (!nolist) {
		$('fusen_list').style.top = top + 'px';
		$('fusen_list').style.left = (left + $('fusen_id' + selectid).offsetWidth + 1) + 'px';left + 'px';
	}
	window.scrollTo(left,top);
	
	fusenDustboxFlg = !dustbox;
	fusen_dustbox();
}

function fusen_select_clear(mode)
{
	if (mode == 'on') {
		fusenBodyStyle = 'fusen_body';
	} else {
		fusenBodyStyle = 'fusen_body_trans';
	}
	fusen_transparent();
	
	for(var id in fusenObj) {
		if (!isNaN(id) && $('fusen_id' + id)) {
			if (fusenObj[id].del) {
				border = fusenVar['BorderObj']['del'];
			} else  if (fusenObj[id].lk) {
				border = fusenVar['BorderObj']['lock'];
			} else {
				border = fusenVar['BorderObj']['normal'];
			}
			$('fusen_id' + id).style.border = border;
			$('fusen_id' + id).style.zIndex = 1;
		}
	}
}

function fusen_winScroll(x,y)
{
	wy+=1;
	if( wy > y ){return;}
	window.scrollTo(x,wy);
	eval('setTimeout("fusen_winScroll(' + x + ',' + y + ')",1);');
}

// Initialize

function fusen_init(mode)
{
	if (fusenRetTimerID) clearTimeout(fusenRetTimerID);
	if (mode) {
		fusen_getdata('GET');
	} else {
		fusen_getdata('HEAD');
	}
}

function fusen_set_elements()
{
	var hobj = $('fusen_help');
	var html;
	html = '[<a href="javascript:fusen_hide(\'fusen_help\')" title="' + fusenMsgs['close'] + '">&#215;</a>]'
+ fusenMsgs['help_html'];
	hobj.innerHTML = html;
	hobj.style.width = 'auto';
	hobj.style.width = hobj.offsetWidth + 'px';
	var eobj = $('fusen_editbox');
}

function fusen_size_init(obj)
{
	v_tmp = obj.style.visibility;
	l_tmp = obj.style.left;
	obj.style.visibility = 'hidden'
	obj.style.left = '0px';
	obj.style.overflow = 'visible';
	obj.style.whiteSpace = 'nowrap';
	obj.style.width = 'auto';
	obj.style.width = obj.offsetWidth + 'px';
	obj.style.whiteSpace = 'normal';
	obj.style.left = l_tmp;
	obj.style.visibility = v_tmp;
}

function fusen_list_make()
{
	var listobj = $('fusen_list');
	var listcount = 0;
	var burn = "";
	var delbox;
	var tmp = "";
	var flg_delbox = false;
	
	tmp = '<ul><form>';
	for(var id in fusenObj)
	{
		if (!isNaN(id)) {
			listcount ++;
			
			// Add list
			addtxt = fusenObj[id].disp.replace(/<[^>]+>/g,'').replace(/&nbsp;/g,' ').replace(/[\s]+/g,' ');
			if (addtxt.length > 30) addtxt = addtxt.substr(0,30) + '...';
			if (!addtxt.replace(/^[\s]+$/,'')) addtxt = "- no text -";
			
			dustbox = 0;
			delbox = '';
			if (fusenObj[id].del) {
				addtxt = '(' + fusenMsgs['dustbox'] + ')' + addtxt;
				//dustbox = 1;
			} else {
				if (!fusenVar['ReadOnly'] && fusenObj[id].auth) {
					delbox = '<input type="checkbox" id="list_delbox_'+id+'" style="cursor:auto;" />';
					flg_delbox = true;
				}
			}
			tmp += '<li>'+delbox+'<a href="javascript:fusen_select('+id+')">'+addtxt+'</a></li>';
		}
	}
	
	if (flg_delbox) burn += " [ <a href=\"JavaScript:fusen_del_multi()\" title=\"" + fusenMsgs['burn_checked'] + "\">" + fusenMsgs['dust_checked'] + "</a> ]";
	if (fusenVar['admin']) burn += " [ <a href=\"JavaScript:fusen_burn()\" title=\"" + fusenMsgs['empty'] + "\">" + fusenMsgs['empty'] + "</a> ]";
	
	var menu = '[<a href="javascript:fusen_hide(\'fusen_list\');" title="' + fusenMsgs['close'] + '">&#215;</a>]';
	if (!fusenVar['ReadOnly']) {
		menu += ' [ <a href="javascript:fusen_hide(\'fusen_list\');JavaScript:fusen_new();" title="' + fusenMsgs['newtag'] + '">' + fusenMsgs['new'] + '</a> ]';
	}
	menu += burn + ' [ <a href="JavaScript:fusen_show(\'fusen_help\');" title="' + fusenMsgs['howto'] + '">' + fusenMsgs['help'] + '</a> ]';
	
	tmp = menu + tmp;
	
	if (!listcount) tmp += '<li>' + fusenMsgs['notag'] + '</li>';
	if ($('xpwiki_fusenlist')) {
		if (listcount) {
			var display = '';
			var count_str = '<a href="JavaScript:fusen_show(\'fusen_list\')">' + fusenMsgs['fusen'] + '(' + listcount + ')</a>';
		} else {
			var display = 'none';
			var count_str = '';
		}
		$('xpwiki_fusenlist').style.display = display;
		$('xpwiki_fusenlist').innerHTML = $('xpwiki_fusenlist').innerHTML.replace(/(<!--FU-->).*(<!--SEN-->)/, '$1' + count_str + '$2');
	}
	tmp += '</form></ul>';
	
	listobj.innerHTML = tmp;
	list_left = listobj.style.left;
	list_visibility = listobj.style.visibility;
	listobj.style.visibility = 'hidden';
	listobj.style.left = '0px';
	listobj.style.width = 'auto';
	listobj.style.width = listobj.getWidth() + 'px';
	listobj.style.left = list_left;
	listobj.style.visibility = list_visibility;
	listobj.style.zIndex = 100;
	return;
}

function fusen_onload() {
	//$('fusen_area').style.width = '1000px;'

	if (fusenVar['base']) {
		var elm = $('fusen_container');
		var p = $('fusen_container').parentNode;
		p.removeChild(elm);
		$('xpwiki_body').parentNode.insertBefore(elm, $('xpwiki_body'));
	}
	
	fusen_set_elements();
	fusen_init(1);
	
	var root = (fusenVar['IE'])? document : window;
	
	var _save = root.ondblclick;
	root.ondblclick = function() {
		if (_save) _save();
		if (!fusenDblClick) fusen_new(true);
	};
	
	var _save = root.onmousedown;
	root.onmousedown = function(e) {
		if (_save) _save();
		if (fusenVar['IE'])
		{
			if (document.compatMode && document.compatMode=='CSS1Compat')
			{
				fusenVar['mouseX'] = document.documentElement.scrollLeft + event.clientX;
				fusenVar['mouseY'] = document.documentElement.scrollTop + event.clientY;
			}
			else
			{
				fusenVar['mouseX'] = document.body.scrollLeft + event.clientX;
				fusenVar['mouseY'] = document.body.scrollTop + event.clientY;
			}
		}
		else
		{
			fusenVar['mouseX'] = e.pageX;
			fusenVar['mouseY'] = e.pageY;
		}
	}
}

XpWiki.domInitFunctions.push(fusen_onload);