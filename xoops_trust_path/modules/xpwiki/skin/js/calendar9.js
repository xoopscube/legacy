// calendar9.inc.php http://www.sakasama.com/dive/
function xpwiki_cal9_showResponse(orgRequest) {
	xpwiki_ajax_edit_var['func_post'] = '';
	var xmlRes = orgRequest.responseXML;
	if (xmlRes.getElementsByTagName('editform').length) {
		xpwiki_ajax_edit_var['func_post'] = xpwiki_cal9_showResponse;
		var str = xmlRes.getElementsByTagName('editform')[0].firstChild.nodeValue;
		str = str.replace(/wikihelper_msg_nowrap/, wikihelper_msg_nowrap);
		$('xpwiki_cal9_editarea').innerHTML = str;
		var tAreas = $('xpwiki_cal9_editarea').getElementsByTagName('textarea');
		var tArea;
		for (var i = 0; i < tAreas.length; i++) {
			if (tAreas[i].id.match(/xpwiki_edit_textarea$/)) {
				tArea = tAreas[i];
				break;
			}
		}
		tArea.style.height = '250px';
		tArea.setAttribute("rel", "wikihelper");
		Element.update($('xpwiki_cancel_form'), '<button id="c9cancel" onclick="return xpwiki_cal9_day_edit_close()">'+xpwiki_calender9_cancel+'</button>');
		new Resizable(tArea, {mode:'xy'});
		XpWiki.addWrapButton(tArea);
		wikihelper_initTexts($('xpwiki_cal9_editarea'));
		Element.hide($('xpwiki_cal9_loading_base'));
	} else if (xmlRes.getElementsByTagName('xpwiki').length) {
		
		Element.update('xpwiki_cal9_editarea', '');

		var item = xmlRes.getElementsByTagName('xpwiki')[0];
		
		var str = item.getElementsByTagName('content')[0].firstChild.nodeValue;
		var mode = item.getElementsByTagName('mode')[0].firstChild.nodeValue;
		
		
		if (mode == 'read') {
			xpwiki_ajax_edit_var['func_post'] = xpwiki_cal9_showResponse;

/*
			var str = '';
			var linkObj = ins.getElementsByTagName("link");
			var add;
			for (i = 0; i < linkObj.length; i++) {
				if (linkObj[i].getAttribute('rel') == 'stylesheet') {
					if (document.all && document.createStyleSheet) {
  						document.createStyleSheet(linkObj[i].getAttribute('href'));
  					} else {
						add = document.createElement('link');
						add.href = linkObj[i].getAttribute('href');
						add.rel = linkObj[i].getAttribute('rel');
						add.type = linkObj[i].getAttribute('type');
						document.getElementsByTagName('head')[0].appendChild(add);
					}
				}
			}
*/
			var ins;
			ins = document.createElement('div');
			Element.update(ins, item.getElementsByTagName('headPreTag')[0].firstChild.nodeValue);
			$('xpwiki_cal9_editarea').appendChild(ins);

			ins = document.createElement('div');
			Element.update(ins, item.getElementsByTagName('headTag')[0].firstChild.nodeValue);
			$('xpwiki_cal9_editarea').appendChild(ins);

			ins = document.createElement('div');
			ins.innerHTML = item.getElementsByTagName('content')[0].firstChild.nodeValue;
			$('xpwiki_cal9_editarea').appendChild(ins);
			
			var close = document.createElement('input');
			close.type = 'button';
			close.value = 'Close';
			close.onclick = function() { xpwiki_cal9_day_edit_close(); }
			
			ins = document.createElement('form');
			ins.style.textAlign = 'center';
			ins.appendChild(close);
			
			$('xpwiki_cal9_editarea').appendChild(ins);
			wikihelper_initTexts($('xpwiki_cal9_editarea'));
			Element.hide($('xpwiki_cal9_loading_base'));
			xpwiki_ajax_edit_var['id'] = '';
			xpwiki_ajax_edit_var['html'] = '';
		} else if (mode == 'write' || mode == 'delete') {
			xpwiki_ajax_edit_var['html'] = '';
			xpwiki_cal9_thisreload();
		} else if (mode == 'preview'){
			xpwiki_ajax_edit_var['func_post'] = xpwiki_cal9_showResponse;
			str = str.replace(/wikihelper_msg_nowrap/, wikihelper_msg_nowrap);
			$('xpwiki_cal9_editarea').innerHTML = str;
			$('xpwiki_edit_textarea').style.height = '250px';
			Element.update($('xpwiki_cancel_form'), '<button id="c9cancel" onclick="return xpwiki_cal9_day_edit_close()">'+xpwiki_calender9_cancel+'</button>');
			new Resizable('xpwiki_preview_area', {mode:'y'});
			new Resizable('xpwiki_edit_textarea', {mode:'xy'});
			XpWiki.addWrapButton('xpwiki_edit_textarea');
			$('xpwiki_edit_textarea').setAttribute("rel", "wikihelper");
			wikihelper_initTexts($('xpwiki_cal9_editarea'));
			Element.hide($('xpwiki_cal9_loading_base'));
			xpwiki_ajax_edit_var['html'] = true;
		}
	}
}

function xpwiki_cal9_day_edit(id,mode,event) {

	if (!!event) {
		if (Prototype.Browser.IE) {
			event.cancelBubble = true;
			event.returnValue = false;
		} else {
			Event.stop(event);
		}
	}

	if (!mode) mode = 'edit';

	xpwiki_ajax_edit_var['id'] = 'xpwiki_cal9_popupmain';
	
	var args = id.split(":");
	var dir = args[0];
	id = args[1];

	// HTML BODYオブジェクト取得
	var objBody = XpWiki.getDomBody();
	
	// 背景半透明オブジェクト作成
	if (!$('xpwiki_cal9_popupback')) {
		var objBack = document.createElement('div');
		objBack.setAttribute('id', 'xpwiki_cal9_popupback');
		objBack.onclick = function() { xpwiki_cal9_day_edit_close(); }
		Element.setStyle(objBack, {display: 'none'});
		Element.setStyle(objBack, {position: 'absolute'});
		Element.setStyle(objBack, {zIndex: '90'});
		Element.setStyle(objBack, {textAlign: 'center'});
		Element.setStyle(objBack, {backgroundColor: 'black'});
		Element.setStyle(objBack, {filter: 'alpha(opacity=50)'});
		Element.setStyle(objBack, {opacity: '0.5'});
		
		objBack.style.top = 0;
		objBack.style.left = 0;
		objBack.style.width = '100%';
		objBack.style.height = objBody.offsetHeight + 'px';
		objBody.appendChild(objBack);
	} else {
		var objBack = $('xpwiki_cal9_popupback');
	}
	
	// 入力ボックスオブジェクト作成
	if (!$('xpwiki_cal9_popupmain')) {
		var objPopup = document.createElement('div');
		objPopup.setAttribute('id', 'xpwiki_cal9_popupmain');
	
		var insobj = document.createElement('div');
		insobj.setAttribute('id','xpwiki_cal9_editarea');
		objPopup.appendChild(insobj);

		insobj = document.createElement('div');
		insobj.setAttribute('id','xpwiki_cal9_loading_base');
		insobj.style.height = '100px';
		insobj.style.padding = '50px';
		insobj.style.textAlign = 'center';
		
		var objLoadingImage = document.createElement('img');
		objLoadingImage.setAttribute('src', XpWiki.MyUrl + '/' + dir + '/skin/loader.php?src=loading.gif');
		insobj.appendChild(objLoadingImage);
		
		objPopup.appendChild(insobj);
	
		Element.setStyle(objPopup, {display: 'none'});
		Element.setStyle(objPopup, {position: 'absolute'});
		Element.setStyle(objPopup, {zIndex: '100'});
		Element.setStyle(objPopup, {border: '2px #eee8aa solid'});
		Element.setStyle(objPopup, {backgroundColor: 'white'});
		Element.setStyle(objPopup, {padding: '20px'});
		Element.setStyle(objPopup, {overflow: 'auto'});
		
		$('xpwiki_body').appendChild(objPopup);

	} else {
		var objPopup = $('xpwiki_cal9_popupmain');
	}
	
	var viewport = document.viewport.getDimensions();
	var viewoffset = document.viewport.getScrollOffsets();
	viewport.width  = (viewport.width || 1024);
	viewport.height = (viewport.height || 768);

	var popupW = (viewport.width - 300);
	var popupH = (viewport.height - 80);
	
	var editHtml = '<div style="text-align:center;"> [ <span id="pagename">' + id + '</span> ] Now loading...</div>';
	Element.update($('xpwiki_cal9_editarea'), editHtml);

	objPopup.style.top = (viewoffset.top + 20) + 'px';
	objPopup.style.width = popupW + 'px';
	objPopup.style.maxHeight = popupH + 'px';
	objPopup.style.left = ((viewport.width - popupW) / 2) + 'px';
	
	wikihelper_hide_helper();
	Element.show(objBack);
	Element.show(objPopup);
	Element.show($('xpwiki_cal9_loading_base'));

	//Element.setStyle(objPopup, 'position: fixed');
	
	// ページ情報を読込み反映する
	var url = XpWiki.MyUrl + '/' + dir + '/?cmd=' + mode;
	var pars = '';
	pars += 'page=' + encodeURIComponent(id);
	pars += '&ajax=1';
	pars += '&nonconvert=1';
	pars += '&encode_hint=' + encodeURIComponent(xpwiki_calender9_hint);
	
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get',
			parameters: pars,
			onComplete: xpwiki_cal9_showResponse
		});
	return false;
}

// ポップアップウィンドウを閉じる
function xpwiki_cal9_day_edit_close() {
	wikihelper_hide_helper();
	xpwiki_ajax_edit_var['func_post'] = '';
	xpwiki_ajax_edit_var['html'] = '';
	//Element.setStyle($('xpwiki_cal9_popupmain'), 'position: absolute');
	Element.hide($('xpwiki_cal9_popupback'));
	Element.hide($('xpwiki_cal9_popupmain'));
	Element.hide($('xpwiki_cal9_loading_base'));
	Element.update($('xpwiki_cal9_editarea'), '');
	return false;
}

function xpwiki_cal9_thisreload() {

	xpwiki_cal9_day_edit_close();

	// ページ情報を読込み反映する
	var url = window.location.pathname + window.location.search;
	var pars = 'ajax=1';
	var myAjax = new Ajax.Request(
		url, 
		{
			method: 'get',
			parameters: pars,
			onComplete: xpwiki_cal9_showReload
		});
}

function xpwiki_cal9_showReload(orgRequest) {

	var xmlRes = orgRequest.responseXML;
	if (xmlRes.getElementsByTagName('xpwiki').length) {
		if (!!$('wikihelper_base')) {
			var helper = $('wikihelper_base');
			Element.remove($('wikihelper_base'));
			XpWiki.getDomBody().appendChild(helper);
		}
		Element.remove($('xpwiki_cal9_popupback'));
		Element.remove($('xpwiki_cal9_loading_base'));
		Element.remove($('xpwiki_cal9_popupmain'));
		
		var item = xmlRes.getElementsByTagName('xpwiki')[0];
		$('xpwiki_body').innerHTML = item.getElementsByTagName('content')[0].firstChild.nodeValue;
		wikihelper_initTexts($('xpwiki_body'));
	}
}

function xpwiki_cal9_day_focus(id) {
	Element.setStyle($(id), {'border': 'red 1px solid'});
}

function xpwiki_cal9_day_unfocus(id, orgstyle) {
	Element.setStyle($(id), {'border': '#eeeeee 1px solid'});
}
