$().ready(function() {

	$().toastmessage( { sticky : true } );

	elFinder.prototype.i18.en.messages.ntfperm = 'Changing permission';
	elFinder.prototype.i18.en.messages.cmdperm = 'Chage permission';
	elFinder.prototype.i18.en.messages.newitem = 'New item';
	elFinder.prototype.i18.en.messages.owner   = 'Owner';
	elFinder.prototype.i18.en.messages.group   = 'Group';
	elFinder.prototype.i18.en.messages.guest   = 'Guest';
	elFinder.prototype.i18.en.messages.perm    = 'Permission';
	elFinder.prototype.i18.en.messages.unlock  = 'Unlock';
	elFinder.prototype.i18.en.messages.hidden  = 'Hidden';
	elFinder.prototype.i18.en.messages.targetgroups  = 'Target groups';
	elFinder.prototype.i18.en.messages.mimeserach    = 'MIME type Serach';

	if (typeof elFinder.prototype.i18.jp != "undefined") {
		elFinder.prototype.i18.jp.messages.ntfperm = 'アイテム属性を変更';
		elFinder.prototype.i18.jp.messages.cmdperm = '属性変更';
		elFinder.prototype.i18.jp.messages.newitem = '新規アイテム';
		elFinder.prototype.i18.jp.messages.owner   = 'オーナー';
		elFinder.prototype.i18.jp.messages.group   = 'グループ';
		elFinder.prototype.i18.jp.messages.guest   = 'ゲスト';
		elFinder.prototype.i18.jp.messages.perm    = 'パーミッション';
		elFinder.prototype.i18.jp.messages.unlock  = 'ロック解除';
		elFinder.prototype.i18.jp.messages.hidden  = '非表示';
		elFinder.prototype.i18.jp.messages.targetgroups  = '対象グループ';
		elFinder.prototype.i18.jp.messages.mimeserach    = 'MIMEタイプで検索';

		elFinder.prototype.i18.ja = elFinder.prototype.i18.jp;
	}
	
	var customData = { admin : adminMode };
	if (! connectorUrl) {
		connectorUrl = myUrl + 'connector.php';
	} else {
		customData.xoopsUrl = rootUrl;
	}
	if (sessionName) {
		var reg = new RegExp(sessionName+'=([^;]+)');
		if (document.cookie.match(reg)) {
			customData.sessionId = RegExp.$1;
		}
	}
	
	$('#elfinder').elfinder({
		lang: lang,
		url : connectorUrl,
		urlUpload : myUrl + 'connector.php',
		customData : customData,
		requestType : 'POST',
		height: $(window).height() - 20,
		getFileCallback : callbackFunc,
		uiOptions : {
			// toolbar configuration
			toolbar : [
				['back', 'forward'],
				['netmount'],
				// ['reload'],
				// ['home', 'up'],
				['mkdir', 'mkfile', 'upload'],
				['open', 'download', 'getfile'],
				['info', 'perm'],
				['quicklook'],
				['copy', 'cut', 'paste'],
				['rm'],
				['duplicate', 'rename', 'edit', 'resize', 'pixlr'],
				['extract', 'archive'],
				['search'],
				['view', 'sort'],
				['help']
			],
			// directories tree options
			tree : {
				// expand current root on init
				openRootOnLoad : true,
				// auto load current dir parents
				syncTree : true
			},
			// navbar options
			navbar : {
				minWidth : 150,
				maxWidth : 500
			}
		},
		commands : [
    		'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
    		'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
    		'cut', 'paste', 'edit',
    		'extract', 'archive',
    		'search', 'info', 'view', 'help', 'resize', 'sort', 'netmount', 'pixlr', 'perm'
    	],
		commandsOptions : {
			  getfile : {
			    onlyURL : false,
			    multiple : false,
			    folders : false
			  }
		},
		contextmenu : {
			// navbarfolder menu
			navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info', 'perm'],
			// current directory menu
			cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'sort', '|', 'info', 'perm'],
			// current directory file menu
			files  : ['getfile', '|','open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'edit', 'rename', 'resize', 'pixlr',
			          '|', 'archive', 'extract',
			          '|', 'info', 'perm']
		}
	}).elfinder('instance');

});

$.fn.extend({
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
			if (jQuery.browser.msie) {
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
			try {
				pa.jQuery.modal.close();
			} catch(e) {
				window.close();
			}
		}
	}
});

function insertCode(align, thumb, format) {
	$('.toast-item-close').click();
	$('.toast-item').css('background-image','');
	var code = '';
	var size = '';
	var isImg = (itemObject.mime.match(/^image/));
	var imgTag = useSiteImg? 'siteimg' : 'img';
	if (isImg && $('#resize_px')) {
		size = $('#resize_px').val();
		if (size && ! size.match(/[\d]{1,4}/)) {
			size = '';
		}
	}
	if (! format) {
		if (isImg) {
			if (imgThumb.match(/_tmbsize_/)) {
				if (size) {
					imgThumb = imgThumb.replace('_tmbsize_', size);
				} else {
					imgThumb = '';
				}
			}
			if (thumb && imgThumb) {
				code = '[siteurl='+itemPath+']['+imgTag+' align='+align+']'+ (useSiteImg? '' : rootUrl+'/') + imgThumb + '[/'+imgTag+'][/siteurl]';
			} else {
				code = '['+imgTag+' align='+align+']' + (useSiteImg? '' : rootUrl+'/') + itemPath + '[/'+imgTag+']';
			}
		} else {
			code = '[siteurl='+itemPath+']'+itemObject.name+'[/siteurl]';
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

		if (isImg) {
			if (size) {
				size = ',mw:'+size+',mh:'+size
			}
			if (thumb || o.tagName != 'TEXTAREA') {
				code = '&ref(site://'+itemPath+','+align+size+');';
			} else {
				code = '\n#ref(site://'+itemPath+','+align+size+')\n';
			}
		} else {
			code = '[['+itemObject.name+':site://'+itemPath+']]';
		}
	}
	$().insertAtCaret(code);
}

var getFileCallback_bbcode = function (file, fm) {
	if (!target) {
		fm.exec(fm.OS == 'mac' ? 'rename' : 'open')
		return;
	}
	var path = file.url.replace(rootUrl+'/', '');
	var basename = path.replace( /^.*\//, '' );
	var modules_basename = moduleUrl.replace(rootUrl, '').replace(/\//g, '');
	var reg = new RegExp('^.*?(?:'+modules_basename+'|uploads)\/([^\/]+)\/.*$');
	var module = path.replace(reg, '$1');
	var thumb = '';
	var isImg = (file.mime.match(/^image/))? true : false;
	if (isImg && module.match(/^[a-zA-Z0-9_-]+$/)) {
		eval('if (typeof get_thumb_'+module+' == "function" ){' +
			'thumb = get_thumb_'+module+'(basename, file);}' );
	}
	imgThumb = encodeURI(thumb);
	itemPath = encodeURI(path);
	itemObject = file;

	if (isImg) {
		var buttons = '<span onclick="insertCode(\'left\',1);"><img src="'+imgUrl+'alignleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',1)"><img src="'+imgUrl+'aligncenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',1)"><img src="'+imgUrl+'alignright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span onclick="insertCode(\'left\',0);"><img src="'+imgUrl+'alignbigleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',0)"><img src="'+imgUrl+'alignbigcenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',0)"><img src="'+imgUrl+'alignbigright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span class="file_info">Size: ' + file.width + 'x' + file.height+'</span>';
		if (file.url.match(/\bview\b/)) {
			buttons += '<br>'
					+ '<span class="file_info">Resize:<input id="resize_px" style="width: 3em" class="button_input" value="'+defaultTmbSize+'">px</span>';
		}
		
		$().toastmessage( 'removeToast', $('.toast-item'));
		$().toastmessage( 'showSuccessToast', buttons);
		$('.toast-item').css('background-image','url("'+file.url+'")');
	} else {
		insertCode('',0);
	}
};

var getFileCallback_xpwiki = function (file, fm) {
	if (!target) {
		fm.exec(fm.OS == 'mac' ? 'rename' : 'open')
		return;
	}
	var path = file.url.replace(rootUrl+'/', '');
	var basename = path.replace( /^.*\//, '' );
	var modules_basename = moduleUrl.replace(rootUrl, '').replace(/\//g, '');
	var reg = new RegExp('^.*?(?:'+modules_basename+'|uploads)\/([^\/]+)\/.*$');
	var module = path.replace(reg, '$1');
	var thumb = '';
	var isImg = (file.mime.match(/^image/))? true : false;
	if (isImg && module.match(/^[a-zA-Z0-9_-]+$/)) {
		eval('if (typeof get_thumb_'+module+' == "function" ){' +
			'thumb = get_thumb_'+module+'(basename, file);}' );
	}
	imgThumb = encodeURI(thumb);
	itemPath = encodeURI(path);
	itemObject = file;

	if (isImg) {
		var buttons = '<span onclick="insertCode(\'left\',1,\'xpwiki\');"><img src="'+imgUrl+'alignleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',1,\'xpwiki\')"><img src="'+imgUrl+'aligncenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',1,\'xpwiki\')"><img src="'+imgUrl+'alignright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span onclick="insertCode(\'left\',0,\'xpwiki\');"><img src="'+imgUrl+'alignbigleft.gif" alt="" /></span> <span onclick="insertCode(\'center\',0,\'xpwiki\')"><img src="'+imgUrl+'alignbigcenter.gif" alt="" /></span> <span onclick="insertCode(\'right\',0,\'xpwiki\')"><img src="'+imgUrl+'alignbigright.gif" alt="" /></span>'
					+ '<br>'
					+ '<span class="file_info">Size: ' + file.width + 'x' + file.height+'</span>'
					+ '<br>'
					+ '<span class="file_info">Resize:<input id="resize_px" style="width: 3em" class="button_input" value="'+defaultTmbSize+'">px</span>';
	
		$('.toast-item-close').click();
		$().toastmessage( 'showSuccessToast', buttons);
		$('.toast-item').css('background-image','url("'+file.url+'")');
	} else {
		insertCode('',0,'xpwiki');
	}
};

// for FCKEditor
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=fckeditor'
var getFileCallback_fckeditor = function (file, fm) {
	window.opener.SetUrl(file.url) ;
	window.close();
};

// for CKEditor
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=ckeditor'
var getFileCallback_ckeditor = function (file, fm) {
	var funcNum = window.location.search.replace(/^.*CKEditorFuncNum=(\d+).*$/, "$1");
	window.opener.CKEDITOR.tools.callFunction(funcNum, file.url);
	window.close();
};

// for tinyMCE
// Url: '[XOOPS_URL]/modules/xelfinder/manager.php?cb=tinymce'
var getFileCallback_tinymce = function (file, fm) {
	window.tinymceFileWin.document.forms[0].elements[window.tinymceFileField].value = file.url;
	window.tinymceFileWin.focus();
	window.close();
};

