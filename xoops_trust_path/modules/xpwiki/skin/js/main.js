// for .uniqueID

(function() {
    if (!Prototype.Browser.IE && !!window.HTMLElement) {
    	var nextUniqueID = 1;
	    window.HTMLElement.prototype.__defineGetter__('uniqueID', function() {
	        var uniqueID = 'id' + nextUniqueID++;
	        this.__defineGetter__("uniqueID", function(){return uniqueID});
	        return uniqueID;
	    });
    }
})();

// Init.
var wikihelper_elem;
var wikihelper_mapLoad=0;
var wikihelper_initLoad=0;
var wikihelper_root_url = '$wikihelper_root_url';
var wikihelper_mouseover = false;
var wikihelper_hide_timer;
var wikihelper_over_timer;
var XpWikiModuleUrl = '$module_url';
var XpWikiEncHint = '$encode_hint';
var XpWikiCharSet = '$charset';
var XpWikiIeDomLoadedDisable = $ieDomLoadedDisabled;

if (XpWiki.isIE6) {
	XpWikiIeDomLoadedDisable = true;
}

XpWiki.MyUrl = XpWikiModuleUrl;
XpWiki.EncHint = XpWikiEncHint;
XpWiki.faviconSetClass = '$faviconSetClass';
XpWiki.faviconReplaceClass = '$faviconReplaceClass';
XpWiki.UseWikihelperAtAll = $UseWikihelperAtAll;
XpWiki.RendererDir = '$RendererDir';
XpWiki.RendererPage = '$RendererPage';
XpWiki.FCKSmileys = $fck_smileys;
XpWiki.FCKeditor_path = '$fckeditor_path';
XpWiki.FCKxpwiki_path = '$fckxpwiki_path';
XpWiki.ie6JsPass = $ie6JsPass;
XpWiki.imageDir = '$imageDir';
XpWiki.filemanagerTag = '$filemanagerTag';
$skinname

// Load CSS
XpWiki.addCssInHead('base.css');

var xpwiki_ajax_edit_var = new Object();
xpwiki_ajax_edit_var['id'] = '';
xpwiki_ajax_edit_var['html'] = '';
xpwiki_ajax_edit_var['mode'] = '';
xpwiki_ajax_edit_var['func_post'] = '';

// cookie
var wikihelper_adv;


function wikihelper_show_fontset_img()
{
	if (!wikihelper_mapLoad)
	{
		wikihelper_mapLoad = 1;
		var map='<div id="wikihelper_map"><map name="map_button">'+
			'<area shape="rect" coords="0,0,22,16" title="URL" alt="URL" href="#" onClick="javascript:wikihelper_linkPrompt(\'url\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="24,0,40,16" title="B" alt="B" href="#" onClick="javascript:wikihelper_tag(\'b\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="43,0,59,16" title="I" alt="I" href="#" onClick="javascript:wikihelper_tag(\'i\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="62,0,79,16" title="U" alt="U" href="#" onClick="javascript:wikihelper_tag(\'u\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="81,0,103,16" title="SIZE" alt="SIZE" href="#" onClick="javascript:wikihelper_tag(\'size\'); return false;" '+'/'+'>'+
			'<'+'/'+'map>'+
			'<map name="map_color">'+
			'<area shape="rect" coords="0,0,8,8" title="Black" alt="Black" href="#" onClick="javascript:wikihelper_tag(\'Black\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="8,0,16,8" title="Maroon" alt="Maroon" href="#" onClick="javascript:wikihelper_tag(\'Maroon\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="16,0,24,8" title="Green" alt="Green" href="#" onClick="javascript:wikihelper_tag(\'Green\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="24,0,32,8" title="Olive" alt="Olive" href="#" onClick="javascript:wikihelper_tag(\'Olive\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="32,0,40,8" title="Navy" alt="Navy" href="#" onClick="javascript:wikihelper_tag(\'Navy\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="40,0,48,8" title="Purple" alt="Purple" href="#" onClick="javascript:wikihelper_tag(\'Purple\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="48,0,55,8" title="Teal" alt="Teal" href="#" onClick="javascript:wikihelper_tag(\'Teal\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="56,0,64,8" title="Gray" alt="Gray" href="#" onClick="javascript:wikihelper_tag(\'Gray\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="0,8,8,16" title="Silver" alt="Silver" href="#" onClick="javascript:wikihelper_tag(\'Silver\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="8,8,16,16" title="Red" alt="Red" href="#" onClick="javascript:wikihelper_tag(\'Red\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="16,8,24,16" title="Lime" alt="Lime" href="#" onClick="javascript:wikihelper_tag(\'Lime\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="24,8,32,16" title="Yellow" alt="Yellow" href="#" onClick="javascript:wikihelper_tag(\'Yellow\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="32,8,40,16" title="Blue" alt="Blue" href="#" onClick="javascript:wikihelper_tag(\'Blue\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="40,8,48,16" title="Fuchsia" alt="Fuchsia" href="#" onClick="javascript:wikihelper_tag(\'Fuchsia\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="48,8,56,16" title="Aqua" alt="Aqua" href="#" onClick="javascript:wikihelper_tag(\'Aqua\'); return false;" '+'/'+'>'+
			'<area shape="rect" coords="56,8,64,16" title="White" alt="White" href="#" onClick="javascript:wikihelper_tag(\'White\'); return false;" '+'/'+'>'+
			'<'+'/'+'map><'+'/'+'div>'+
			'<div id="wikihelper_base" class="image_button_base" style="position:absolute;display:none;"><'+'/'+'div>';

		var src;

		src = document.createElement('link');
		src.href = '$wikihelper_root_url/skin/loader.php?src=wikihelper.css';
		src.rel  = 'stylesheet';
		src.type = 'text/css';
		document.getElementsByTagName('head')[0].appendChild(src);

		src = document.createElement('div');
		src.innerHTML = map;
		src.zIndex = Math.max(1000, XpWiki.iframeLargestZIndex);
		if (! Prototype.Browser.IE) {
			src.observe('mouseover', function(){wikihelper_mouseover = true;});
			src.observe('mouseout', function(){wikihelper_mouseover = false;});
		}
		XpWiki.getDomBody().appendChild(src);

		$('wikihelper_base').observe('mouseover', function(){wikihelper_mouseover = true;});
		$('wikihelper_base').observe('mouseout', function(){wikihelper_mouseover = false;});

		$('wikihelper_map').observe('mouseover', function(){wikihelper_mouseover = true;});
		$('wikihelper_map').observe('mouseout', function(){wikihelper_mouseover = false;});

		if (Prototype.Browser.IE) {
			$('wikihelper_base').observe('mousedown', function(){wikihwlper_caretPos();});
		}

		new Draggable('wikihelper_base');
/*
		if (XpWiki.useJQueryMobile) {
			jQuery(function() {
				$('wikihelper_base').style.height = '50px';
				$('wikihelper_base').style.overflow = 'auto';
				jQuery('#wikihelper_base').flickable();
			});
		}
*/
	}

	// Helper image tag set
	var wikihelper_adv_tag = '';

	var str = '<span class="button" onclick="wikihelper_show_hint()">' + wikihelper_msg_hint + '<'+'/'+'span>';

	if (wikihelper_adv == "on")
	{
		str = str + '<span class="button" title="'+wikihelper_msg_to_easy_t+'" onclick="wikihelper_adv_swich()">' + 'Easy' + '<'+'/'+'span>';

		wikihelper_adv_tag =
			'<img src="' + XpWiki.imageDir + 'ncr.gif" width="22" height="16" border="0" title="'+wikihelper_msg_to_ncr+'" alt="'+wikihelper_msg_to_ncr+'" onClick="javascript:wikihelper_charcode(); return false;" '+'/'+'>'+
			'<img src="' + XpWiki.imageDir + 'br.gif" width="18" height="16" border="0" title="&amp;br;" alt="&amp;br;" onClick="javascript:wikihelper_ins(\'&br;\'); return false;" '+'/'+'>'+
			'<img src="' + XpWiki.imageDir + 'iplugin.gif" width="18" height="16" border="0" title="Inline Plugin" alt="Inline Plugin" onClick="javascript:wikihelper_ins(\'&(){};\'); return false;" '+'/'+'>';
	} else {
		str = str + '<span class="button" title="'+wikihelper_msg_to_adv_t+'" onclick="wikihelper_adv_swich()">' + 'Adv.' + '<'+'/'+'span>';
	}

	str += ' <a href="#" title="Close" onclick="javascript:wikihelper_mouseover=false;wikihelper_hide_helper();return false;"><img src="$wikihelper_root_url/skin/loader.php?src=close.gif" border="0" alt="Close" '+'/'+'><'+'/'+'a>';

	var wikihelper_helper_img =
		'<img src="' + XpWiki.imageDir + 'buttons.gif" width="103" height="16" border="0" usemap="#map_button" tabindex="-1" '+'/'+'>'+
		'<img src="' + XpWiki.imageDir + 'clip.png" width="18" height="16" border="0" title="'+wikihelper_msg_attach+'" alt="&amp;ref;" onClick="javascript:wikihelper_ins(\'&ref();\'); return false;" '+'/'+'>'+
		XpWiki.filemanagerTag +
		' '+
		wikihelper_adv_tag +
		' '+
		'<img class="img_zoom4" src="' + XpWiki.imageDir + 'colors.gif" width="64" height="16" border="0" usemap="#map_color" tabindex="-1" '+'/'+'> '+
		str+
		'<br '+'/'+'>';

	if (wikihelper_adv == "on") {
		wikihelper_helper_img += $face_tag_full;
	} else {
		wikihelper_helper_img += $face_tag;
	}

	$("wikihelper_base").style.width = 'auto';
	$("wikihelper_base").innerHTML = wikihelper_helper_img;

	$("wikihelper_base").Resizable_done = false;
	new Resizable('wikihelper_base', {mode:'x'});

}

function wikihelper_adv_swich()
{
	if (wikihelper_adv == "on")	{
		wikihelper_adv = "off";
	} else {
		wikihelper_adv = "on";
	}
	wikihelper_save_cookie("__whlp",wikihelper_adv,90,"/");
	wikihelper_show_fontset_img();
	$('wikihelper_base').style.width = 'auto';
	$('wikihelper_base').style.height = 'auto';
	$('wikihelper_base').style.width = $('wikihelper_base').getStyle('width');
	wikihelper_elem.focus();
}

function wikihelper_save_cookie(arg1,arg2,arg3,arg4){
	XpWiki.cookieSave(arg1, arg2, arg3, arg4);
}

function wikihelper_load_cookie(arg){
	return XpWiki.cookieLoad(arg);
}

function wikihelper_area_highlite(id,mode) {
	if (mode) {
		$(id).className += ' highlight';
	} else {
		$(id).className = $(id).className.replace(/ ?highlight$/, '');
	}

}

function wikihelper_check(f) {
	if (wikihelper_elem && wikihelper_elem.type == "text") {
		if (!confirm(wikihelper_msg_submit)) {
			wikihelper_elem.focus();
			return false;
		}
	}

	for (i = 0; i < f.elements.length; i++) {
		oElement = f.elements[i];
		if (oElement.type == "submit" && (!oElement.name || oElement.name == "comment")) {
			oElement.disabled = true;
		}
	}

	return true;

}

function wikihelper_cumulativeOffset(forElement) {

	var valueT = 0, valueL = 0;
	var base = XpWiki.getDomBody();
	var element = forElement;
	do {
		if (Element.getStyle(element, 'position') == 'absolute') {
			base = element;
			break;
		}
		valueT += element.offsetTop  || 0;
		valueL += element.offsetLeft || 0;
	} while (element = element.offsetParent);

	element = forElement;
	do {
		if (element != forElement) {
			valueT -= element.scrollTop  || 0;
			valueL -= element.scrollLeft || 0;
		}
		if (element.parentNode == base) break;
	} while (element = element.parentNode);

	var helper = $('wikihelper_base');
	Element.remove($('wikihelper_base'));
	base.appendChild(helper);

	return Element._returnOffset(valueL, valueT);
}

function wikihelper_initTexts(obj)
{
	if (!obj) {
		if (wikihelper_initLoad) return;
		obj = document;
	}
	var oElements;
	var oElement;
	wikihelper_initLoad = 1;
	wikihelper_elem = null;
	wikihelper_show_fontset_img();

	if (Prototype.Browser.IE) {
		var oElements = obj.getElementsByTagName("form");
		for (i = 0; i < oElements.length; i++)
		{
			oElement = oElements[i];
			var onkeyup = oElement.onkeyup;
			var onmouseup = oElement.onmouseup;
			oElement.onkeyup = function()
			{
				if (onkeyup) onkeyup();
				wikihelper_pos();
			};
			oElement.onmouseup = function()
			{
				if (onmouseup) onmouseup();
				wikihelper_pos();
			};
		}
	}

	var helperOn = function(oElement) {
		Element.observe(oElement, 'focus',
			function(elm){
				return	function(){
					elm._focused = true;
					wikihelper_setActive(elm, false);
				};
			}(oElement)
		);
		Element.observe(oElement, 'mouseover',
			function(elm){
				return	function(){
					if (elm._focused) wikihelper_setActive(elm, false);
				};
			}(oElement)
		);
		Element.observe(oElement, 'blur',
			function(elm){
				return	function(){
					elm._focused = false;
					wikihelper_hide_helper();
				}
			}(oElement)
		);
		Element.observe(oElement, 'mouseout',
			function(){
				wikihelper_mouseover = false;
				wikihelper_hide_helper(500);
			}
		);
	};

	var helperOff = function(oElement) {
		Element.observe(oElement, 'focus',
			function(){
				wikihelper_mouseover = false;
				wikihelper_hide_helper();
			}
		);
	};

	var x = document.evaluate('descendant::input[@type!="hidden"] | descendant::textarea[@rel="wikihelper"] | descendant::select', obj, null, 6, null);
	var n = 0;
	for (var i = 0; i < x.snapshotLength; i++) {
		var elm = x.snapshotItem(i);
		if (String(elm.getAttribute('rel')) == 'wikihelper') {
			helperOn(elm);
		} else {
			helperOff(elm);
		}
	}
}

function wikihelper_setActive(elem, istimer)
{
	if (! istimer) {
		wikihelper_mouseover = true;
		setTimeout(function(elem){return function(){wikihelper_setActive(elem, true)}}(elem), 500);
		return;
	}

	if (! wikihelper_mouseover) return;
	//if (Prototype.Browser.IE) {alert('hoge');}
	var helper = $("wikihelper_base");
	if (helper.style.display == 'none' || wikihelper_elem != elem) {
		if (! elem._focused) {
			elem.focus();
			return;
		}

		XpWiki.UploadDir = '';
		XpWiki.UploadPage = '';
		if ($('XpWikiPopup')) {
		//	Element.hide('XpWikiPopup');
		}

		Element.show(helper);
		if (wikihelper_elem != elem) {
			wikihelper_elem = elem;
			var offset = wikihelper_cumulativeOffset(wikihelper_elem);
			helper.style.left = (XpWiki.useJQueryMobile? 12 : offset[0]) + "px";
			helper.style.top = ( offset[1] - helper.offsetHeight - 1 ) + "px";
			wikihelper_pos();
		}

		XpWiki.setUploadVar(wikihelper_elem);

		if (XpWiki.isIE6) {
			oElements = document.getElementsByTagName("select");
			for (i = 0; i < oElements.length; i++)
			{
				oElement = oElements[i];
				oElement.style.visibility = "hidden";
			}
		}

		if (XpWiki.useJQueryMobile) {
			jQuery('iframe.youtube-player').css('visibility', 'hidden');
		}
	}
}

function wikihelper_hide_helper(time) {
	if (wikihelper_hide_timer) {
		clearTimeout(wikihelper_hide_timer);
	}

	if (wikihelper_mouseover) {
		wikihelper_hide_timer = setTimeout(wikihelper_hide_helper, 500);
		return;
	}

	if (typeof time == 'number' && time) {
		wikihelper_hide_timer = setTimeout(wikihelper_hide_helper, time);
		return;
	}

	var helper = $("wikihelper_base");
	if (helper) {
		Element.hide(helper);
		if (wikihelper_WinIE && XpWiki.isIE6) {
			oElements = document.getElementsByTagName("select");
			for (i = 0; i < oElements.length; i++)
			{
				oElement = oElements[i];
				oElement.style.visibility = "";
			}
		}
		if (XpWiki.useJQueryMobile) {
			jQuery('iframe.youtube-player').css('visibility', '');
		}
	}
}

function wikihelper_tagset (str, v) {
	if ( v == 'size' ) {
		var default_size = "%";
		v = prompt(wikihelper_msg_fontsize, default_size);
		if (!v) return false;
		if (!v.match(/(%|pt)$/))
			v += "pt";
		if (!v.match(/\d+(%|pt)/))
			return false;
	}
	if ( v == 'b') {
		str = '\'\'' + str.replace(/(\r\n|\r|\n)/g, "&br;") + '\'\'';
	} else if ( v == 'i') {
		str = '\'\'\'' + str.replace(/(\r\n|\r|\n)/g, "&br;") + '\'\'\'';
	} else if (str.match(/^&font\([^\)]*\)\{.*\};$/)) {
		str = str.replace(/^(&font\([^\)]*)(\)\{.*\};)$/,"$1," + v.replace(/(\r\n|\r|\n)/g, "&br;") + "$2");
	} else {
		str = '&font(' + v + '){' + str.replace(/(\r\n|\r|\n)/g, "&br;") + '};';
	}

	return str;
}

function xpwiki_now_loading(mode, id) {
	if (mode) {
		var objSrc = $(id);
		if (!id || !objSrc) {
			id = 'xpwiki_body';
			objSrc = $(id);
		}
		if (!objSrc) return;

		wikihelper_hide_helper();
		if (!$("xpwiki_loading")) {
			var objBody = XpWiki.getDomBody();
			var objBack = document.createElement("div");
			objBack.id = 'xpwiki_loading';
			objBack.style.display = 'none';
			objBack.style.position = 'absolute';
			objBack.style.zIndex = Math.max(1000, XpWiki.iframeLargestZIndex);
			var txtBox = document.createElement("div");
			txtBox.innerHTML = 'Now loading...';
			txtBox.setAttribute('id', 'xpwiki_loading_text');
			objBack.appendChild(txtBox);
			objBody.appendChild(objBack);
		} else {
			var objBack = $("xpwiki_loading");
		}

		//var pos = objSrc.positionedOffset();
		var pos = XpWiki.cumulativeOffset(objSrc);

		objBack.style.left = pos[0] + 'px';
		objBack.style.top = pos[1] + 'px';
		objBack.style.width = objSrc.offsetWidth + 'px';
		objBack.style.height = objSrc.offsetHeight + 'px';

		//Element.clonePosition('xpwiki_loading', id);

		Element.show('xpwiki_loading');
	} else {
		Element.hide('xpwiki_loading');
	}
}

function xpwiki_ajax_edit(url, id) {
	//if (XpWiki.useJQueryMobile) return true;

	url = location.pathname.replace(/[^\/]+$/, '')+'?page='+url;
	if (xpwiki_ajax_edit_var['id'] && xpwiki_ajax_edit_var['id'] != id) {
		if (! confirm(wikihelper_msg_notsave)) {
			return false;
		}
		XpWiki.removeFCK(xpwiki_ajax_edit_var['id']);
		$(xpwiki_ajax_edit_var['id']).innerHTML = xpwiki_ajax_edit_var['html'];
		$(xpwiki_ajax_edit_var['id']).style.clear = xpwiki_ajax_edit_var['clear'];
	}
	if ($(id)) {
		wikihelper_area_highlite(id, 0);
		xpwiki_ajax_edit_var['id'] = id;
	} else {
		xpwiki_ajax_edit_var['id'] = 'xpwiki_body';
		id = '';
	}

	xpwiki_now_loading(true, id);

	var pars = '';
	pars += 'cmd=edit';
	if (id) pars += '&paraid=' + encodeURIComponent(id);
	pars += '&ajax=1';
	var myAjax = new Ajax.Request(
		url,
		{
			method: 'get',
			parameters: pars,
			onSuccess: xpwiki_ajax_edit_show,
			onFailure: function(){	location.href = url + '&' + pars.replace('&ajax=1', ''); }
		});
	return false;
}

function xpwiki_ajax_edit_show(orgRequest) {
	xpwiki_now_loading(false);
	xpwiki_ajax_edit_var['html'] = $(xpwiki_ajax_edit_var['id']).innerHTML;
	xpwiki_ajax_edit_var['clear'] = $(xpwiki_ajax_edit_var['id']).style.clear;
	var xmlRes = orgRequest.responseXML;
	if(xmlRes.getElementsByTagName("editform").length) {
		var str = xmlRes.getElementsByTagName("editform")[0].firstChild.nodeValue;
		str = str.replace(/wikihelper_msg_nowrap/, wikihelper_msg_nowrap);
		$(xpwiki_ajax_edit_var['id']).style.clear = 'both';
		$(xpwiki_ajax_edit_var['id']).innerHTML = str;

		//if (!!jQuery) {
		//	jQuery("#"+xpwiki_ajax_edit_var['id']).page();
		//	jQuery("#"+xpwiki_ajax_edit_var['id']).removeClass("ui-page ui-body-c");
		//}

		XpWiki.textareaMakeOnAjax($(xpwiki_ajax_edit_var['id']));

		wikihelper_initTexts($(xpwiki_ajax_edit_var['id']));

		new Effect.ScrollTo(xpwiki_ajax_edit_var['id'], {duration:0.3});
	}
	orgRequest = null;
}

function xpwiki_ajax_edit_submit(IsTemplate) {
	xpwiki_now_loading(true, xpwiki_ajax_edit_var['id']);
	url = location.pathname.replace(/[^\/]+$/, '');
	var frm = $('xpwiki_edit_form');
	var re = /input|textarea|select/i;
	var tag = '';
	var postdata = '';

	for (var i = 0; i < frm.length; i++ ) {
		var child = frm[i];
		tag = String(child.tagName);
		if (tag.match(re)) {
			if (child.type == 'checkbox') {
				if (child.checked) {
					if (postdata!='') postdata += '&';
					postdata += encodeURIComponent(child.name) +
						'=' + encodeURIComponent(child.value);
				}
			} else {
				if (child.type == 'textarea') {
					if (typeof FCKeditorAPI != 'undefined') {
						var oEditor = FCKeditorAPI.GetInstance(child.id);
						if (oEditor) {
							child.value = oEditor.GetXHTML(true);
						}
					}
				}
				if (postdata!='') postdata += '&';
				postdata += encodeURIComponent(child.name) +
					'=' + encodeURIComponent(child.value);
			}
		}
	}
	if (!IsTemplate) {
		postdata = postdata.replace(/&template=[^&]+/,'');
	}
	if (xpwiki_ajax_edit_var['mode'] == 'preview') {
		postdata = postdata.replace(/&write=[^&]+/,'');
	} else {
		postdata = postdata.replace(/&preview=[^&]+/,'');
	}
	postdata += '&ajax=1';
	if (location.href.match('&popup=1')) {
		postdata += '&popup=1';
	}

	var failure = false;
	var myAjax = new Ajax.Request(
		url,
		{
			asynchronous: false,
			method: 'post',
			parameters: postdata,
			onSuccess: function(req){
				failure = xpwiki_ajax_edit_post(req);
			},
			onFailure: function(){
					xpwiki_ajax_edit_var['html'] = '';
					failure = true;
				}
		});

	return failure;
}

function xpwiki_ajax_edit_post(orgRequest) {
	xpwiki_now_loading(false);
	if (xpwiki_ajax_edit_var['func_post']) {
		xpwiki_ajax_edit_var['func_post'](orgRequest);
		return false;
	} else {
		var xmlRes = orgRequest.responseXML;
		if(xmlRes && xmlRes.getElementsByTagName("xpwiki").length) {
			var item = xmlRes.getElementsByTagName("xpwiki")[0];
			var str = item.getElementsByTagName("content")[0].firstChild.nodeValue;
			xpwiki_ajax_edit_var['mode'] = item.getElementsByTagName("mode")[0].firstChild.nodeValue;
			if (xpwiki_ajax_edit_var['mode'] == 'write') {
				if (xpwiki_ajax_edit_var['id']) {
					new Effect.ScrollTo(xpwiki_ajax_edit_var['id'], {duration:0.3});
				}
				xpwiki_ajax_edit_var['mode'] = '';
				xpwiki_ajax_edit_var['html'] = '';
				if (str.match(/<script[^>]+src=/)) {
					orgRequest = null;
					xpwiki_now_loading(true, xpwiki_ajax_edit_var['id']);
					location.reload(true);
					return false;
				}
				xpwiki_ajax_edit_var['id'] = '';
				$('xpwiki_body').innerHTML = str;
				wikihelper_initTexts($('xpwiki_body'));
			} else if (xpwiki_ajax_edit_var['mode'] == 'delete') {
				$('xpwiki_body').innerHTML = str;
				xpwiki_ajax_edit_var['id'] = '';
				xpwiki_ajax_edit_var['mode'] = '';
				xpwiki_ajax_edit_var['html'] = '';
				location.href = item.getElementsByTagName("url")[0].firstChild.nodeValue;
			} else if (xpwiki_ajax_edit_var['mode'] == 'preview') {
				if (xpwiki_ajax_edit_var['id']) {
					new Effect.ScrollTo(xpwiki_ajax_edit_var['id'], {duration:0.3});
				}
				if (str.match(/<script[^>]+src=/)) {
					xpwiki_ajax_edit_var['html'] = '';
					xpwiki_now_loading(true, xpwiki_ajax_edit_var['id']);
					orgRequest = null;
					return true;
				}
				str = str.replace(/wikihelper_msg_nowrap/, wikihelper_msg_nowrap);
				$(xpwiki_ajax_edit_var['id']).innerHTML = str;

				if (!XpWiki.useJQueryMobile) {
					new Resizable('xpwiki_preview_area', {mode:'y'});
					XpWiki.textareaMakeOnAjax($(xpwiki_ajax_edit_var['id']));
				} else {
					$('xpwiki_preview_area').style.maxHeight = 'none';
				}
				wikihelper_initTexts($(xpwiki_ajax_edit_var['id']));
			}
		} else {
			// alert(orgRequest.responseText); // for dubug
			if (xpwiki_ajax_edit_var['mode'] != 'preview') {
				orgRequest = null;
				xpwiki_ajax_edit_var['html'] = '';
				xpwiki_now_loading(true, xpwiki_ajax_edit_var['id']);
				location.reload();
				return false;
			} else {
				alert('Response error.');
			}
		}
	}
	orgRequest = null;
	return false;
}

function xpwiki_ajax_edit_cancel() {
	if (xpwiki_ajax_edit_var['id']) {
		if (xpwiki_ajax_edit_var['html'].match(/<script[^>]+src=/)) {
			xpwiki_ajax_edit_var['html'] = '';
			location.reload();
			return false;
		}

		var wait = XpWiki.removeFCK(xpwiki_ajax_edit_var['id']);
		// wait for IE (Crash prevention)
		var id = xpwiki_ajax_edit_var['id'];
		var html = xpwiki_ajax_edit_var['html'];
		var clear = xpwiki_ajax_edit_var['clear'];
		setTimeout(function () {
			$(id).innerHTML = html;
			$(id).style.clear = clear;
			new Effect.ScrollTo(id, {duration:0.3});
			wikihelper_initTexts($(id));
		}, wait);
	}
	xpwiki_ajax_edit_var['id'] = '';
	xpwiki_ajax_edit_var['mode'] = '';
	xpwiki_ajax_edit_var['html'] = '';
	return false;
}

function xpwiki_getDateStr() {
	var today = new Date();
	var yy = parseInt(today.getYear());
	if (yy < 2000) {yy = yy+1900;}
	var mm = parseInt(today.getMonth()) + 1;
	if (mm < 10) {mm = "0" + mm;}
	var dd = parseInt(today.getDate());
	if (dd < 10) {dd = "0" + dd;}
	var h = parseInt(today.getHours());
	if (h < 10) {h = "0" + h;}
	var m = parseInt(today.getMinutes());
	if (m < 10) {m = "0" + m;}
	var s = parseInt(today.getSeconds());
	if (s < 10) {s = "0" + s;}
	var ms = parseInt(today.getMilliseconds());
	if (ms < 10) {ms = "00" + ms;}
	else if (ms < 100) {ms = "0" + ms;}

	return ''+yy+mm+dd+h+m+s+ms;
}

_save = (window.onbeforeunload)? window.onbeforeunload : '';
window.onbeforeunload = function(e) {
	e = e || window.event;
	if (_save) _save(e);
	if (xpwiki_ajax_edit_var['html']) {
		xpwiki_ajax_edit_var['html'] = '';
		return wikihelper_msg_notsave;
	}
};

if (Prototype.Browser.IE) {
	Event.observe(window, "load", function() {
		XpWiki.isDomLoaded = false;
		XpWiki.onDomLoaded();
	});
}
if (! Prototype.Browser.IE || !XpWikiIeDomLoadedDisable) {
	document.observe("dom:loaded", function() {
		XpWiki.onDomLoaded();
	});
}
