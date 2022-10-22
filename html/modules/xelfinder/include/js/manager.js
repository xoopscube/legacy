if (window.parent) {
	$('.simplemodal-wrap', window.parent.document).css({overflow:'hidden'});
}
$(document).ready(function() {
	// keep alive
	var extCheck = connectorUrl;

	setInterval(function(){
		jQuery.ajax({url:myUrl+"/connector.php?keepalive=1",cache:false});
		if (extCheck) {
			jQuery.ajax({url:extCheck+"?keepalive=1",cache:false,xhrFields:{withCredentials:true}});
		}
	}, 300000); // keep alive interval 5min
	
	var customData = { admin : adminMode, ctoken : cToken };
	var cors = false;
	var IElt10;

	if (! connectorUrl) {
		connectorUrl = myUrl + 'connector.php';
	} else {
		cors = true;
		customData.myUrl = myUrl;
		if (! connIsExt) {
			customData.xoopsUrl = rootUrl;
		}
		if (typeof document.uniqueID != 'undefined') {
			(function(){
				var xhr = new XMLHttpRequest();
				if (!('withCredentials' in xhr)) {
					jQuery('<script>').attr('src', myUrl+'/include/js/xdr/jquery.xdr.js').appendTo('head');
					IElt10 = true;
				}
				xhr = null;
			})();
		}
	}
	
	// Detect language if `lang` is empty
	if (typeof lang !== 'string' || !lang) {
		lang = (function() {
			var locq = window.location.search,
				map = {
					'pt' : 'pt_BR',
					'ug' : 'ug_CN',
					'zh' : 'zh_CN'
				},
				full = {
					'zh_tw' : 'zh_TW',
					'zh_cn' : 'zh_CN',
					'fr_ca' : 'fr_CA'
				},
				fullLang, locm, lang;
			if (locq && (locm = locq.match(/lang=([a-zA-Z_-]+)/))) {
				// detection by url query (?lang=xx)
				fullLang = locm[1];
			} else {
				// detection by browser language
				fullLang = (navigator.browserLanguage || navigator.language || navigator.userLanguage || '');
			}
			fullLang = fullLang.replace('-', '_').substr(0,5).toLowerCase();
			if (full[fullLang]) {
				lang = full[fullLang];
			} else {
				lang = (fullLang || 'en').substr(0,2);
				if (map[lang]) {
					lang = map[lang];
				}
			}
			return lang;
		})();
	}

	var opts = {
		handlers : {
			// set extra messages
			i18load : function(e, fm) {
				var mes_en = fm.i18.en.messages;
				mes_en.ntfperm = 'Changing permission';
				mes_en.cmdperm = 'Chage permission';
				mes_en.newitem = 'New item';
				mes_en.guest   = 'Guest';
				mes_en.unlock  = 'Unlock';
				mes_en.hidden  = 'Hidden';
				mes_en.targetgroups  = 'Target groups';
				mes_en.mimeserach    = 'MIME type Serach';
				mes_en.nowrap        = 'No wrap';
				mes_en.wraparound    = 'Wrap around';
				mes_en.inline        = 'Inline';
				mes_en.fullsize      = 'Full Size';
				mes_en.thumbnail     = 'Thumbnail';
				mes_en.continues     = 'Continue more';
				mes_en.imageinsert   = 'Image insert options';
				mes_en.CannotUploadOldIE = '<p>Your browser "IE" cannot upload by this manager.</p><p>Please use the newest browser, when you upload files.</p>';
				mes_en.errPleaseReload = 'Not found access token.<br />Please reload on browser, or re-open popup window.';
				mes_en.errAccessReload = 'There are no token necessary to a connection, so reload this file manager.';

				if (typeof fm.i18.ja !== "undefined") {
					var mes_ja = fm.i18.ja.messages;
					mes_ja.read    = '読取'; // over write
					mes_ja.write   = '書込'; // over write
					mes_ja.ntfperm = 'アイテム属性を変更しています';
					mes_ja.cmdperm = '属性変更';
					mes_ja.newitem = '新規アイテム';
					mes_ja.guest   = 'ゲスト';
					mes_ja.unlock  = 'ロック解除';
					mes_ja.hidden  = '非表示';
					mes_ja.targetgroups  = '対象グループ';
					mes_ja.mimeserach    = 'MIMEタイプで検索';
					mes_ja.nowrap        = '回り込みなし';
					mes_ja.wraparound    = '回り込みあり';
					mes_ja.inline        = 'インライン';
					mes_ja.fullsize      = 'フルサイズ';
					mes_ja.thumbnail     = 'サムネイル';
					mes_ja.continues     = 'さらに続ける';
					mes_ja.imageinsert   = '画像挿入オプション';
					mes_ja.CannotUploadOldIE = '<p>あなたがお使いの IE ブラウザでは、このマネージャーではファイルをアップロードすることができません。</p><p>ファイルをアップロードする場合は、最新のブラウザをご利用下さい。</p>';
					mes_ja.errPleaseReload = '接続に必要なトークンがありません。<br />ブラウザでリロードするかポップアップウィンドウを開きなおしてください。';
					mes_ja.errAccessReload = '接続に必要なトークンがないので、ファイルマネージャーを再読込します。';
					mes_ja.cmdlogin = 'ログイン';
					mes_ja.logout   = '$1: ログアウト';
					mes_ja.username = 'ユーザー名';
					mes_ja.password = 'パスワード';
					mes_ja.loginFaild = 'ログインできません。';
				}
			}
		},
		lang: lang,
		url : connectorUrl,
		baseUrl : baseUrl,
		cssAutoLoad : false,
		customData : customData,
		customHeaders: cors? {'X-Requested-With' : 'XMLHttpRequest'} : {},
		xhrFields: cors? {withCredentials: true} : {},
		requestType : 'POST',
		height: '100%',
		resizable: false,
		getFileCallback : callbackFunc,
		startPathHash : startPathHash,
		sync : autoSyncSec * 1000,
		syncStart : autoSyncStart,
		uiOptions : {
			places : {
				suffix : xoopsUid
			}
		},
		commandsOptions : {
			getfile : {
				onlyURL : false,
				multiple : false,
				folders : false,
				getImgSize: true
			},
			edit : {
				extraOptions : {
					creativeCloudApiKey : creativeCloudApikey
				}
			},
			quicklook : {
				googleMapsApiKey : googleMapsApiKey,
				sharecadMimes : useSharecadPreview? ['image/vnd.dwg', 'image/vnd.dxf', 'model/vnd.dwf', 'application/vnd.hp-hpgl', 'application/plt', 'application/step', 'model/iges', 'application/vnd.ms-pki.stl', 'application/sat', 'image/cgm', 'application/x-msmetafile'] : [],
				googleDocsMimes : useGoogleDocsPreview? ['application/pdf', 'image/tiff', 'application/vnd.ms-office', 'application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/postscript', 'application/rtf'] : [],
				officeOnlineMimes : useOfficePreview? ['application/msword', 'application/vnd.ms-word', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.oasis.opendocument.presentation'] : []
			},
			opennew : {
				url : myUrl + 'manager.php',
				useOriginQuery : false
			}
		},
		themes : {
			default: {
				'name': 'Dark Slim',
				'cssurls': 'themes/dark-slim/css/theme.css',
				'author': 'John Fort',
				'license': 'MIT'
			},
		}
	};

	if (typeof xelfinderUiOptions !== 'undefined' && $.isPlainObject(xelfinderUiOptions)) {
		// Overwrite if opts value is an array
		(function() {
			var arrOv = function(obj, base) {
				if ($.isPlainObject(obj)) {
					$.each(obj, function(k, v) {
						if ($.isPlainObject(v)) {
							if (!base[k]) {
								base[k] = {};
							}
							arrOv(v, base[k]);
						} else {
							base[k] = v;
						}
					});
				}
			};
			arrOv(xelfinderUiOptions, opts);
		})();
	}

	var elfinderInstance = $('#elfinder').elfinder(opts).elfinder('instance');
	
	// Easy refer on file upload
	if (target || elfinderInstance.options.getFileCallback) {
		elfinderInstance.bind('upload', function(e){
			var added, hash;
			if (e.data && (added = e.data.added) && added.length === 1) {
				hash = added[0].hash;
				if (added[0].tmb !== 1) {
					setTimeout(function(){
						elfinderInstance.exec('getfile', [ hash ]);
					}, 100);
				} else {
					elfinderInstance.one('tmb', function(){
						elfinderInstance.exec('getfile', [ hash ]);
					});
				}
			}
		});
	}
	
	// set document.title dynamically etc.
	var title = document.title;
	elfinderInstance.bind('open', function(event) {
		var data = event.data || null;
		var path = '';
		
		if (data) {
			if (data.init && IElt10) {
				var dialog = $('<div class="elfinder-dialog-resize"></div>');
				dialog.append(elfinderInstance.i18n('CannotUploadOldIE'));
				var buttons = {};
				buttons[elfinderInstance.i18n('btnYes')] = function() { dialog.elfinderdialog('close'); };
				elfinderInstance.dialog(dialog, {
						title : elfinderInstance.i18n('cmdupload'),
						width : '400px',
						buttons: buttons,
						destroyOnClose : true,
						modal : true
					});
			}
			
			if (data.cwd) {
				path = elfinderInstance.path(data.cwd.hash) || null;
			}
			document.title =  path? path + ':' + title : title;
		}
	})

	// on error callback
	.bind('error', function(e) {
		if (e.data && e.data.error && e.data.error == 'errPleaseReload') {
			var loc = window.location;
			if (!loc._reload) {
				if (confirm(elfinderInstance.i18n('errAccessReload'))) {
					loc._reload = true;
					setTimeout(function(){
						loc.reload(false);
					}, 100);
				} else {
					delete loc._reload;
				}
			}
		}
	});

});

$.extend({
	insertAtCaret: function(v) {
		var pa = null;
		var o = null;
		try {
			pa = window.opener;
			o = pa.document.getElementById(target);
		} catch(e) {
			try {
				pa = window.parent;
				o = pa.document.getElementById(target);
			} catch(e) {}
		}
		if (o) {
			o.focus();
			if (!!document.uniqueID && document.selection) { // IE
				var r;
				if (typeof o.caretPos == 'object') {
					r = o.caretPos;
				} else {
					r = document.selection.createRange();
				}
				r.text = v;
				r.select();
			} else {
				var s = o.value;
				var p = o.selectionStart;
				var np = p + v.length;
				o.value = s.substr(0, p) + v + s.substr(p);
				o.setSelectionRange(np, np);
			}
			if (! $.insertAtCaret.continue_finder) {
				try {
					pa.jQuery.modal.close();
				} catch(e) {
					window.close();
				}
			}
		}
	},
	openImgInsertDialog: function(buttons, img, fm) {
		var opts  = {
			title : fm.i18n('imageinsert'),
			width : 'auto',
			destroyOnClose : true,
			modal : true
		};
		$.openImgInsertDialog.dialog = fm.dialog('<div class="image-inserter-item" style="background-image:url(\''+img+'\')">'+buttons+'</div>', opts);
		$.openImgInsertDialog.dialog.id = 'ImgInsertDialog';
		$.openImgInsertDialog.parent = $.openImgInsertDialog.dialog.parent();
	}
});

function insertCode(align, thumb) {
	var code = '';
	var size = '';
	var isImg = (itemObject.mime.match(/^image/));
	var urlTag = 'siteurl';
	var imgTag = useSiteImg? 'siteimg' : 'img';
	var format = insertCode.format;
	if (isImg && $('#resize_px')) {
		size = $('#resize_px').val();
		if (size && (! size.match(/[\d]{1,4}/) || (!!insertCode.iSize && insertCode.iSize <= size))) {
			size = '';
		} else {
			$.insertAtCaret.resizePx = size;
		}
	}
	$.insertAtCaret.continue_finder = $("#continue_finder:checked").val()? true : false;

	try {
		if ($.openImgInsertDialog.dialog) {
			$.openImgInsertDialog.dialog.elfinderdialog('close');
			$.openImgInsertDialog.dialog = null;
		}
	} catch(e) {}

	insertCode.iSize = null;
	insertCode.format = null;
	if (! format) {
		if (itemPath.match(/^http/)) {
			urlTag = 'url';
		}
		if (isImg) {
			if (imgThumb.match(/_tmbsize_/)) {
				if (size) {
					imgThumb = imgThumb.replace('_tmbsize_', size);
				} else {
					imgThumb = '';
				}
			}
			if (thumb && imgThumb) {
				code = '['+urlTag+'='+itemPath+']['+imgTag+' align='+align+']'+ (useSiteImg? '' : rootUrl+'/') + imgThumb + '[/'+imgTag+'][/'+urlTag+']';
			} else {
				if (itemPath.match(/^http/)) {
					imgTag = 'img';
					code = '['+imgTag+' align='+align+']' + itemPath + '[/'+imgTag+']';
				} else {
					code = '['+imgTag+' align='+align+']' + (useSiteImg? '' : rootUrl+'/') + itemPath + '[/'+imgTag+']';
				}
			}
		} else {
			code = '['+urlTag+'='+itemPath+']'+itemObject.name+'[/'+urlTag+']';
		}
	} else if (format == 'xpwiki') {
		var pa = null;
		var o = null;
		try {
			pa = window.opener;
			o = pa.document.getElementById(target);
		} catch(e) {
			try {
				pa = window.parent;
				o = pa.document.getElementById(target);
			} catch(e) {}
		}
		
		if (! itemPath.match(/^http/)) {
			itemPath = 'site://' + itemPath;
		}
		
		if (isImg) {
			if (size) {
				size = ',mw:'+size+',mh:'+size;
			}
			var orgAlign = align;
			if (align) {
				align = ',' + align;
			}
			if (thumb || o.tagName != 'TEXTAREA' || o.className.match(/\bnorich\b/)) {
				code = '&ref('+itemPath+align+size+');';
				if (!thumb) {
					code += '&clear';
					if (orgAlign == 'left' || orgAlign == 'right') {
						code += '('+orgAlign+')';
					}
					code += ';';
				}
			} else {
				code = '\n#ref('+itemPath+align+size+')\n';
			}
		} else {
			code = '[['+itemObject.name+':'+itemPath+']]';
		}
	}
	$.insertAtCaret(code);
}

function encodeDecodeURI(str) {
	var ret;
	try {
		ret = encodeURI(decodeURI(str));
	} catch (e) {
		ret = str;
	}
	return ret;
}

function getThumbFallback(file) {
	if (file.tmb && file.tmb != 1) {
		return file.tmb.replace(rootUrl+'/', '');
	} else {
		return '';
	}
}

function getModuleName(file) {
	var modules_basename = moduleUrl.replace(rootUrl, '').replace(/\//g, '');
	var reg = new RegExp('^'+rootUrl.replace(/([.*+?^=!:${}()|[\]\/\\])/g, "\\$1")+'\/(?:(?:'+modules_basename+'|uploads)\/)?([^\/]+)\/.*$');
	var module = file.url.replace(reg, '$1');
	return module;
}

var getFileCallback_bbcode = function (file, fm) {
	if (!target || !file.read) {
		fm.exec('open');
		return;
	}
	var path = file.url.replace(rootUrl+'/', '');
	var basename = path.replace( /^.*\//, '' );
	var module =getModuleName(file);
	var thumb = '';
	var isImg = (file.mime.match(/^image/))? true : false;
	if (isImg && file.tmb && file.tmb != 1) {
		if (module.match(/^[a-zA-Z0-9_-]+$/)) {
			eval('if (typeof get_thumb_'+module+' == "function" ){' +
				'thumb = get_thumb_'+module+'(basename, file);}' );
		}
		if (!thumb) {
			thumb = getThumbFallback(file);
		}
	}
	imgThumb = encodeDecodeURI(thumb);
	itemPath = encodeDecodeURI(path);
	itemObject = file;

	if (isImg) {
		var buttons = '<span onclick="insertCode(\'left\',1);"><img src="'+imgUrl+'alignleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',1)"><img src="'+imgUrl+'aligncenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',1)"><img src="'+imgUrl+'alignright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span onclick="insertCode(\'left\',0);"><img src="'+imgUrl+'alignbigleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',0)"><img src="'+imgUrl+'alignbigcenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',0)"><img src="'+imgUrl+'alignbigright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span class="file_info">'+fm.i18n('size')+': ' + file.width + 'x' + file.height+'</span>';
		if (file.url.match(/\bview\b/)) {
			insertCode.iSize = Math.max(file.width, file.height);
			var tsize = $.insertAtCaret.resizePx || Math.min(insertCode.iSize, defaultTmbSize);
			buttons += '<br>'
					+ '<span class="file_info">'+fm.i18n('resize')+':<input id="resize_px" style="width: 2.5em" class="button_input" value="'+tsize+'">px</span>';
		}
		var continue_checked = (! $.insertAtCaret.continue_finder)? '' : ' checked="checked"';
		buttons += '<br>'
				+ '<span class="file_info"><input id="continue_finder" class="button_input" type="checkbox" value="1"'+continue_checked+'><label for="continue_finder">'+fm.i18n('continues')+'</label></span>';

		$.openImgInsertDialog(buttons, file.url, fm);
	} else {
		insertCode('',0);
	}
};

var getFileCallback_xpwiki = function (file, fm) {
	if (!target || !file.read) {
		fm.exec('open');
		return;
	}
	var path = file.url.replace(rootUrl+'/', '');
	if (file._localalias && file.alias.charAt(0) == 'R') {
		path = file.alias.replace('R/', '');
	}
	var basename = path.replace( /^.*\//, '' );
	var module =getModuleName(file);
	var thumb = '';
	var isImg = (file.mime.match(/^image/))? true : false;
	if (isImg && file.tmb && file.tmb != 1) {
		if (module.match(/^[a-zA-Z0-9_-]+$/)) {
			eval('if (typeof get_thumb_'+module+' == "function" ){' +
				'thumb = get_thumb_'+module+'(basename, file);}' );
		}
		if (!thumb) {
			thumb = getThumbFallback(file);
		}
	}
	imgThumb = encodeDecodeURI(thumb);
	itemPath = encodeDecodeURI(path);
	itemObject = file;
	
	if (itemPath.match(/\?/) && ! itemPath.match(/\.[^.?]+$/)) {
		itemPath += '&' + encodeURI(file.name);
	}
	
	insertCode.format = 'xpwiki';
	if (isImg) {
		var nowrap = ' title="' + fm.i18n('nowrap') + '"';
		var wraparound = ' title="' + fm.i18n('wraparound') + '"';
		var inline = ' title="' + fm.i18n('inline') + '"';
		insertCode.iSize = Math.max(file.width, file.height);
		var tsize = $.insertAtCaret.resizePx || Math.min(insertCode.iSize, defaultTmbSize);
		var buttons = '<span onclick="insertCode(\'left\',1);"'+wraparound+'><img src="'+imgUrl+'alignleft.gif" alt="" /></span> <span onclick="insertCode(\'\',1)"'+inline+'><img src="'+imgUrl+'aligncenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',1)"'+wraparound+'><img src="'+imgUrl+'alignright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span onclick="insertCode(\'left\',0);"'+nowrap+'><img src="'+imgUrl+'alignbigleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',0)"'+nowrap+'><img src="'+imgUrl+'alignbigcenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',0)"'+nowrap+'><img src="'+imgUrl+'alignbigright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span class="file_info">'+fm.i18n('size')+': ' + file.width + 'x' + file.height+'</span>'
					+ '<br>'
					+ '<span class="file_info">'+fm.i18n('resize')+':<input id="resize_px" style="width: 2.5em" class="button_input" value="'+tsize+'">px</span>';
		var continue_checked = (! $.insertAtCaret.continue_finder)? '' : ' checked="checked"';
		buttons += '<br>'
				+ '<span class="file_info"><input id="continue_finder" class="button_input" type="checkbox" value="1"'+continue_checked+'><label for="continue_finder">'+fm.i18n('continues')+'</label></span>';
		$.openImgInsertDialog(buttons, file.url, fm);
	} else {
		insertCode('',0);
	}
};

var getFileCallback_xpwikifck = function (file, fm) {
	var pa = null;
	var x = null;
	try {
		pa = window.opener;
		x = pa.XpWiki;
	} catch(e) {
		try {
			pa = window.parent;
			x = pa.XpWiki;
		} catch(e) {}
	}
	if (x) {
		var path = file.url.replace(rootUrl+'/', '');
		path = encodeDecodeURI(path);
		if (! path.match(/^http/)) {
			path = 'site://' + path;
		}
		x.FCKrefInsert(path);
	}
	setTimeout(function(){
		try {
			pa.jQuery.modal.close();
		} catch(e) {
			window.close();
		}
	}, 100);
};

// for FCKEditor
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=fckeditor'
var getFileCallback_fckeditor = function (file, fm) {
	setTimeout(function(){
		window.opener.SetUrl(file.url) ;
		window.close();
	}, 100);
};

// for CKEditor
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=ckeditor'
function ckeditor4_dialog_update(path, thumb, name) {
	var dialog = window.opener.CKEDITOR.dialog.getCurrent(),
		dName = dialog._.name,
		tName = dialog._.currentTabId,
		url = thumb || path,
		tmb = thumb, size;
	if ($('#resize_px')) {
		size = $('#resize_px').val();
		if (size && ! size.match(/[\d]{1,4}/)) {
			size = '';
		}
		if (url.match(/_tmbsize_/)) {
			if (size) {
				url = url.replace('_tmbsize_', size);
			} else {
				url = path;
				tmb = false;
			}
		}
	}
	if (dName == 'image') {
		var urlObj = 'txtUrl';
	} else if (dName == 'flash') {
		var urlObj = 'src';
	} else if (dName == 'files' || dName == 'link') {
		var urlObj = 'url';
	} else {
		return;
	}
	dialog.setValueOf(tName, urlObj, url);
	if (dName == 'image' && tName == 'info' && tmb) {
		dialog.setValueOf('Link', 'txtUrl', path);
		dialog.setValueOf('Link', 'cmbTarget', '_blank');
	} else if (name && dName == 'files' || dName == 'link') {
		try {
			dialog.setValueOf('info', 'linkDisplayText', name);
		} catch(e) {}
	}
	window.close();
}

var getFileCallback_ckeditor = function (file, fm) {
	var dialog = window.opener.CKEDITOR.dialog.getCurrent();
		path = encodeDecodeURI(file.url),
		basename = path.replace( /^.*\//, '' ),
		name = file.name,
		module = getModuleName(file),
		thumb = '',
		isImg = (file.mime.match(/^image/))? true : false,
		localHostReg = new RegExp('^' + window.location.protocol + '//' + window.location.host);
	if (isImg && file.tmb && file.tmb != 1) {
		if (module.match(/^[a-zA-Z0-9_-]+$/)) {
			eval('if (typeof get_thumb_'+module+' == "function" ){' +
				'thumb = get_thumb_'+module+'(basename, file);}' );
		}
		if (!thumb) {
			thumb = getThumbFallback(file);
		}
	}
	path = path.replace(localHostReg, '');
	if (thumb && dialog._.name == 'image' && dialog._.currentTabId == 'info') {
		thumb = rootUrl+'/'+encodeDecodeURI(thumb);
		thumb = thumb.replace(localHostReg, '');
		var fullsize = ' title="' + fm.i18n('fullsize') + '"';
		var thumbnail = ' title="' + fm.i18n('thumbnail') + '"';
		var buttons = '<span'+thumbnail+' onclick="ckeditor4_dialog_update(\''+path.replace("'", "%27")+'\',\''+thumb.replace("'", "%27")+'\',\''+name.replace("'", "%27")+'\');"><img src="'+imgUrl+'alignleft.gif" alt="" /></span>'
		+ ' &nbsp; '
		+ '<span'+fullsize+' onclick="ckeditor4_dialog_update(\''+path.replace("'", "%27")+'\',\'\',\''+name.replace("'", "%27")+'\');window.close();"><img src="'+imgUrl+'alignbigleft.gif" alt="" /></span>'
		+ '<br><span class="file_info">'+fm.i18n('size')+': ' + file.width + 'x' + file.height+'</span>';
		if (file.url.match(/\bview\b/)) {
			insertCode.iSize = Math.max(file.width, file.height);
			var tsize = $.insertAtCaret.resizePx || Math.min(insertCode.iSize, defaultTmbSize);
			buttons += '<br>'
					+ '<span class="file_info">'+fm.i18n('resize')+':<input id="resize_px" style="width: 2.5em" class="button_input" value="'+tsize+'">px</span>';
		}
		$.openImgInsertDialog(buttons, path, fm);
	} else {
		setTimeout(function(){
			ckeditor4_dialog_update(path, '', name);
		}, 100);
	}
};

// for tinyMCE
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=tinymce'
var getFileCallback_tinymce = function (file, fm) {
	setTimeout(function(){
		window.tinymceFileWin.document.forms[0].elements[window.tinymceFileField].value = file.url;
		window.tinymceFileWin.focus();
		window.close();
	}, 100);
};
