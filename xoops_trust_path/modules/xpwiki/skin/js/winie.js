function h_wikihelper_make_copy_button(arg)
{
	document.write ("<input class=\"copyButton\" type=\"button\" value=\"COPY\" onclick=\"h_wikihelper_doCopy('" + arg + "')\"><br />");
}

function h_wikihelper_doCopy(arg)
{
	var doc = document.body.createTextRange();
	doc.moveToElementText(document.all(arg));
	doc.execCommand("copy");
	alert(wikihelper_msg_copyed);
}

function wikihelper_pos(){
	var et = document.activeElement.type;
	if (!(et == "text" || et == "textarea"))
	{
		if (et == "submit") wikihelper_elem = null;
		return;
	}

	//wikihelper_elem = document.activeElement;
}

function wikihwlper_caretPos() {
	if (!!wikihelper_elem && !!document.selection) {
		wikihelper_elem.caretPos = document.selection.createRange().duplicate();
	}
}

function wikihelper_eclr(){
	wikihelper_elem = null;
}

function wikihelper_ins(v)
{
	if(!wikihelper_elem)
	{
		alert(wikihelper_msg_elem);
		return;
	}

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
		if (XpWiki.UploadDir && XpWiki.UploadPage) {
			XpWiki.fileupFormPopup(XpWiki.UploadDir, XpWiki.UploadPage);
			return;
		}
		inp = prompt(wikihelper_msg_thumbsize, '');
		if (inp == null) { return; }
		inp = XpWiki.z2h_digit(inp);
		var size = '';
		if (inp.match(/[\d]{1,3}[^\d]+[\d]{1,3}/)) {
			size = inp.replace(/([\d]{1,3})[^\d]+([\d]{1,3})/, ",mw:$1,mh:$2");
		} else if (inp.match(/[\d]{1,3}/)) {
			size = inp.replace(/([\d]{1,3})/, ",mw:$1,mh:$1");
		}

		v = "&ref(UNQ_" + xpwiki_getDateStr() + size + ");";
	}

	wikihelper_elem.caretPos.text = v;
	wikihelper_elem.focus();
}

function wikihelper_face(v)
{
	if(!wikihelper_elem)
	{
		alert(wikihelper_msg_elem);
		wikihelper_elem.focus();
		return;
	}

	if (wikihelper_elem.caretPos.offsetLeft == wikihelper_elem.createTextRange().offsetLeft)
		wikihelper_elem.caretPos.text = '&nbsp; ' + v + ' ';
	else
		wikihelper_elem.caretPos.text = ' ' + v + ' ';

	wikihelper_elem.focus();
}

function wikihelper_tag(v)
{
	if (!wikihelper_elem || !wikihelper_elem.caretPos)
	{
		alert(wikihelper_msg_elem);
		wikihelper_elem.focus();
		return;
	}

	var str = wikihelper_elem.caretPos.text;
	if (!str)
	{
		alert(wikihelper_msg_select);
		wikihelper_elem.focus();
		return;
	}

	if (! (str = wikihelper_tagset(str, v))) return;

	wikihelper_elem.caretPos.text = str;
	wikihelper_elem.focus();
}

function wikihelper_linkPrompt(v)
{
	if (!document.selection || !wikihelper_elem)
	{
		alert(wikihelper_msg_elem);
		wikihelper_elem.focus();
		return;
	}

	var str = document.selection.createRange().text;
	if (!str)
	{
		str = prompt(wikihelper_msg_link, '');
		if (str == null) {wikihelper_elem.focus();return;}
	}
	var default_url = "http://";
	regex = "^s?https?://[-_.!~*'()a-zA-Z0-9;/?:@&=+$,%#]+$";
	var cbText = clipboardData.getData("Text");
	if(cbText && cbText.match(regex))
		default_url = cbText;
	var my_link = prompt('URL: ', default_url);
	if (my_link != null) {
		if (!document.selection.createRange().text) {
			wikihelper_elem.caretPos.text = '[[' + str + ':' + my_link + ']]';
		} else {
			document.selection.createRange().text = '[[' + str + ':' + my_link + ']]';
		}
	}
	wikihelper_elem.focus();
}

function wikihelper_charcode()
{
	if (!document.selection || !wikihelper_elem)
	{
		alert(wikihelper_msg_elem);
		wikihelper_elem.focus();
		return;
	}

	var str = document.selection.createRange().text;
	if (!str)
	{
		alert(wikihelper_msg_select);
		wikihelper_elem.focus();
		return;
	}

	var j ="";
	for(var n = 0; n < str.length; n++) j += ("&#"+(str.charCodeAt(n))+";");
	str = j;

	document.selection.createRange().text = str;
	wikihelper_elem.focus();
}

function wikihelper_show_hint()
{
	alert(wikihelper_msg_winie_hint_text);

	if (wikihelper_elem != null) wikihelper_elem.focus();
}
