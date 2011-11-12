function wikihelper_pos()
{
	return;
}
function h_wikihelper_make_copy_button(arg)
{
	document.write("");
}

function wikihelper_face(v)
{
	if (wikihelper_elem != null)
	{
		var ss = wikihelper_getSelectStart(wikihelper_elem);
		
		var se = wikihelper_getSelectEnd(wikihelper_elem);
		var s1 = (wikihelper_elem.value).substring(0,ss);
		var s2 = (wikihelper_elem.value).substring(se,wikihelper_getTextLength(wikihelper_elem));
		var s3 = wikihelper_getMozSelection(wikihelper_elem);
		if (!s1 && !s2 && !s3) s1 = wikihelper_elem.value;
		if ((!s1 || s1.match(/[\r\n]$/)) && !s3) {
			s1 = s1 + "&nbsp;";
			se += 6;
		}
		wikihelper_setText(s1 + s3 + ' ' + v + ' ' + s2);
		se = se + v.length + 2;
		wikihelper_elem.setSelectionRange(se, se);
		wikihelper_elem.focus();
	}
	else
	{
		alert(wikihelper_msg_elem);
		return;	
	}
}

function wikihelper_ins(v)
{
	if (wikihelper_elem != null)
	{
		if (v == "&(){};")
		{
			inp = prompt(wikihelper_msg_inline1, '');
			if (inp == null) {wikihelper_elem.focus();return;}
			v = "&" + inp;
			inp = prompt(wikihelper_msg_inline2, '');
			if (inp == null) {wikihelper_elem.focus();return;}
			v = v + "(" + inp + ")";
			inp = prompt(wikihelper_msg_inline3, '');
			if (inp == null) {wikihelper_elem.focus();return;}
			v = v + "{" + inp + "}";
			v = v + ";";
		}
		
		if (v == "&ref();") {
			inp = prompt(wikihelper_msg_thumbsize, '');
			if (inp == null) { inp = "";}
			var size = '';
			if (inp.match(/[\d]{1,3}[^\d]+[\d]{1,3}/)) {
				size = inp.replace(/([\d]{1,3})[^\d]+([\d]{1,3})/, ",mw:$1,mh:$2");
			} else if (inp.match(/[\d]{1,3}/)) {
				size = inp.replace(/([\d]{1,3})/, ",mw:$1,mh:$1");
			}
			
			v = "&ref(UNQ_" + xpwiki_getDateStr() + size + ");";
		}
		
		var ss = wikihelper_getSelectStart(wikihelper_elem);
		var se = wikihelper_getSelectEnd(wikihelper_elem);
		var s1 = (wikihelper_elem.value).substring(0,ss);
		var s2 = (wikihelper_elem.value).substring(se,wikihelper_getTextLength(wikihelper_elem));
		var s3 = wikihelper_getMozSelection(wikihelper_elem);
		if (!s1 && !s2 && !s3) s1 = wikihelper_elem.value;
		wikihelper_setText(s1 + s3 + v + s2);
		se = se + v.length;
		wikihelper_elem.setSelectionRange(se, se);
		wikihelper_elem.focus();
	}
	else
	{
		alert(wikihelper_msg_elem);
		return;	
	}
}

function wikihelper_tag(v)
{
	if (wikihelper_elem != null)
	{
		var ss = wikihelper_getSelectStart(wikihelper_elem);
		var se = wikihelper_getSelectEnd(wikihelper_elem);
		var s1 = (wikihelper_elem.value).substring(0,ss);
		var s2 = (wikihelper_elem.value).substring(se,wikihelper_getTextLength(wikihelper_elem));
		
		var str = wikihelper_getMozSelection(wikihelper_elem);
		
		if (!s1 && !s2 && !str) s1 = wikihelper_elem.value;
		
		if (!str)
		{
			alert(wikihelper_msg_select);
			return;
		}
		
		if (! (str = wikihelper_tagset(str, v))) return;
		
		wikihelper_setText(s1 + str + s2);
		se = ss + str.length;
		wikihelper_elem.setSelectionRange(ss, se);
		wikihelper_elem.focus();
	}
	else
	{
		alert(wikihelper_msg_elem);
		return;	
	}
}

function wikihelper_linkPrompt(v)
{
	if (wikihelper_elem != null)
	{
		var ss = wikihelper_getSelectStart(wikihelper_elem);
		var se = wikihelper_getSelectEnd(wikihelper_elem);
		var s1 = (wikihelper_elem.value).substring(0,ss);
		var s2 = (wikihelper_elem.value).substring(se,wikihelper_getTextLength(wikihelper_elem));
		
		var str = wikihelper_getMozSelection(wikihelper_elem);
		
		if (!s1 && !s2 && !str) s1 = wikihelper_elem.value;
		
		if (!str)
		{
			str = prompt(wikihelper_msg_link, '');
			if (str == null) {wikihelper_elem.focus();return;}
		}
		var default_url = "http://";
		regex = "^s?https?://[-_.!~*'()a-zA-Z0-9;/?:@&=+$,%#]+$";
		var my_link = prompt(wikihelper_msg_url, default_url);
		if (my_link != null)
		{
			str = '[[' + str + ':' + my_link + ']]';
			wikihelper_setText(s1 + str + s2);
			se = ss + str.length;
			wikihelper_elem.setSelectionRange(se, se);
			wikihelper_elem.focus();
		
		}
	}
	else
	{
		alert(wikihelper_msg_elem);
		return;	
	}
}

function wikihelper_charcode()
{
	if (wikihelper_elem != null)
	{
		var ss = wikihelper_getSelectStart(wikihelper_elem);
		var se = wikihelper_getSelectEnd(wikihelper_elem);
		var s1 = (wikihelper_elem.value).substring(0,ss);
		var s2 = (wikihelper_elem.value).substring(se,wikihelper_getTextLength(wikihelper_elem));
		
		var str = wikihelper_getMozSelection(wikihelper_elem);
		if (!str)
		{
			alert(wikihelper_msg_select);
			return;
		}
		var j ="";
		for(var n = 0; n < str.length; n++) j += ("&#"+(str.charCodeAt(n))+";");
		str = j;
		
		wikihelper_setText(s1 + str + s2);
		se = ss + str.length;
		wikihelper_elem.setSelectionRange(ss, se);
		wikihelper_elem.focus();
	}
	else
	{
		alert(wikihelper_msg_elem);
		return;	
	}
}

function wikihelper_setActive(e)
{
	if (e.type == "submit")
	{
		wikihelper_elem = null;
	}
	else
	{
		var helper = $("wikihelper_base");
		if (helper.style.display == 'none' || wikihelper_elem != e.target) {
			wikihelper_elem = e.target;
			var offset = wikihelper_cumulativeOffset(wikihelper_elem);
			Element.show(helper);
			helper.style.left = offset[0] + "px";
			helper.style.top = ( offset[1] - helper.offsetHeight - 1 ) + "px";
		}
	}
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
	oElements = obj.getElementsByTagName("input");
	for (i = 0; i < oElements.length; i++)
	{
		oElement = oElements[i];
		var rel = String(oElement.getAttribute('rel'));
		if (rel == "wikihelper") {
			oElement.addEventListener('focus', wikihelper_setActive, true);
		} else {
			oElement.addEventListener('focus', wikihelper_hide_helper, true);
		}
	}
	oElements = obj.getElementsByTagName("textarea");
	for (i = 0; i < oElements.length; i++)
	{
		oElement = oElements[i];
		var rel = String(oElement.getAttribute('rel'));
		if (rel == "wikihelper") {
			oElement.addEventListener('focus', wikihelper_setActive, true);
		} else {
			oElement.addEventListener('focus', wikihelper_hide_helper, true);
		}
	}
	oElements = obj.getElementsByTagName("select");
	for (i = 0; i < oElements.length; i++)
	{
		oElement = oElements[i];
		oElement.addEventListener('focus', wikihelper_hide_helper, true);
	}
}

function wikihelper_getSelectStart(s)
{
	return s.selectionStart;
}

function wikihelper_getSelectEnd(s)
{
	return s.selectionEnd;
}

function wikihelper_getTextLength(s)
{
	return s.textLength;
}

function wikihelper_getMozSelection(s)
{
	return (s.value).substring(wikihelper_getSelectStart(s), wikihelper_getSelectEnd(s))
}

function wikihelper_setMozSelection(a,z)
{
	wikihelper_elem.selectionStart = a;
	wikihelper_elem.selectionEnd = z;
}

function wikihelper_show_hint()
{
	alert(wikihelper_msg_gecko_hint_text);
	
	if (wikihelper_elem != null) wikihelper_elem.focus();
}

function wikihelper_setText(v)
{
	var scrollTop = wikihelper_elem.scrollTop;
	var scrollLeft = wikihelper_elem.scrollLeft;
	wikihelper_elem.value =v;
	wikihelper_elem.scrollTop = scrollTop;
	wikihelper_elem.scrollLeft = scrollLeft;
}
