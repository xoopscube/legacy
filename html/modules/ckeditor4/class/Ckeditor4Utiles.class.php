<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
	exit;
}

class Ckeditor4_Utils
{
	const DIRNAME = 'ckeditor4';
	const DHTMLTAREA_DEFAULT_COLS = 50;
	const DHTMLTAREA_DEFAULT_ROWS = 15;
	const DHTMLTAREA_DEFID_PREFIX = 'ckeditor4_form_';

	private static $cnt = 0;

	/**
	 * getModuleConfig
	 *
	 * @param   string  $key
	 *
	 * @return  XoopsObjectHandler
	 **/
	public static function getModuleConfig($key = null)
	{
		static $conf;

		if (is_null($conf)) {
			$handler = self::getXoopsHandler('config');
			if (method_exists($handler, 'getConfigsByDirname')) {
				$conf = $handler->getConfigsByDirname(self::DIRNAME);
			} else {
				global $xoopsDB;
				$conf = array();
				$modules_tbl = $xoopsDB->prefix("modules");
				$config_tbl = $xoopsDB->prefix("config");
				$sql = 'SELECT conf_name, conf_value FROM ' . $config_tbl . ' c, ' . $modules_tbl . ' m WHERE c.conf_modid=m.mid AND m.dirname=\'' . self::DIRNAME . '\'';
				if ($result = $xoopsDB->query($sql)) {
					while ($arr = $xoopsDB->fetchRow($result)) {
						$conf[$arr[0]] = $arr[1];
					}
				}
			}
		}
		if ($key) {
			return $conf[ $key ] ?? null;
		} else {
			return $conf;
		}
	}

	/**
	 * &getXoopsHandler
	 *
	 * @param   string  $name
	 * @param   bool  $optional
	 *
	 * @return  XoopsObjectHandler
	 **/
	public static function &getXoopsHandler(
	/*** string ***/
	$name,
	/*** bool ***/
	$optional = false)
	{
		// TODO will be emulated xoops_gethandler
		return xoops_gethandler($name, $optional);
	}

	public static function getMid()
	{
		$mHandler = &self::getXoopsHandler('module');
		$xoopsModule = $mHandler->getByDirname(self::DIRNAME);
		return $xoopsModule->getVar('mid');
	}

	public static function getJS(&$params)
	{
		static $finder, $isAdmin, $isUser, $inSpecialGroup, $confCss, $confHeadCss, $xoopsUrl, $moduleUrl, $uploadTo, $imgSize;

		self::$cnt++;

		$params['name'] = trim($params['name']);
		$params['class'] = isset($params['class']) ? trim($params['class']) : '';
		$params['cols'] = isset($params['cols']) ? (int) $params['cols'] : self::DHTMLTAREA_DEFAULT_COLS;
		$params['rows'] = isset($params['rows']) ? (int) $params['rows'] : self::DHTMLTAREA_DEFAULT_ROWS;
		$params['value'] = $params['value'] ?? '';
		$params['id'] = isset($params['id']) ? trim($params['id']) : self::DHTMLTAREA_DEFID_PREFIX . $params['name'];
		$params['editor'] = isset($params['editor']) ? trim($params['editor']) : 'bbcode';
		$params['toolbar'] = isset($params['toolbar']) ? trim($params['toolbar']) : null;
        $params['uiColor'] = isset($params['uiColor']) ? trim($params['uiColor']) : '';
		$params['style'] = isset($params['style']) ? trim($params['style']) : '';
		$params['allowhtml'] = !empty($params['allowhtml']);
		$params['switcher'] = isset($params['switcher']) ? trim($params['switcher']) : null;
		$params['onload'] = isset($params['onload']) ? trim($params['onload']) : null;
		$params['onready'] = isset($params['onready']) ? trim($params['onready']) : null;

		if (!empty($params['editor']) && $params['editor'] !== 'none' && (!$params['class'] || !preg_match('/\b' . preg_quote($params['editor']) . '\b/', $params['class']))) {
			if (!$params['class']) {
				$params['class'] = $params['editor'];
			} else {
				$params['class'] .= ' ' . $params['editor'];
			}
		}

		// lazy registering & call pre build delegate
		if (defined('XOOPS_CUBE_LEGACY')) {
			$delegate = new XCube_Delegate();
			$delegate->register('Ckeditor4.Utils.PreBuild_ckconfig');
			$delegate->call(new XCube_Ref($params));
		} else {
			self::doFilter('config', 'PreBuild', $params);
		}

		$script = '';
		if ($params['editor'] !== 'plain' && $params['editor'] !== 'none' && $params['editor'] !== 'source') {

			$editor = ($params['editor'] === 'html') ? 'html' : 'bbcode';
			$conf = self::getModuleConfig();
			$imageUploadJS = '';

			if (is_null($finder)) {

				// Get X-elFinder module
				$mHandler = &self::getXoopsHandler('module');
				$mObj = $mHandler->getByDirname($conf['xelfinder']);
				$finder = is_object($mObj) ? $conf['xelfinder'] : '';

				if ($finder) {
					require_once XOOPS_TRUST_PATH . '/modules/xelfinder/class/xelFinderMisc.class.php';
					$xelMisc = new xelFinderMisc($finder);
					if (!empty($conf['uploadHash'])) {
						$uploadTo = trim($conf['uploadHash']);
					} else {
						if (!$uploadTo = $xelMisc->getUserHome()) {
							$uploadTo = $xelMisc->getGroupHome();
						}
						if ($uploadTo) {
							$uploadTo = $xelMisc->getHash($uploadTo);
						}
					}
					$imgSize = ( (int) $conf['imgShowSize'] ) ? 200 : (int) $conf['imgShowSize'];
				} else {
					$uploadTo = false;
				}

				if (defined('XOOPS_CUBE_LEGACY')) {
					$root = &XCube_Root::getSingleton();
					$xoopsUser = $root->mContext->mXoopsUser;
					$inAdminPanel = ($root->mContext->mBaseRenderSystemName === 'Legacy_AdminRenderSystem');
				} else {
					global $xoopsUser;
					$inAdminPanel = defined('_AD_NORIGHT'); // html/language/[LANG]/admin.php
				}

				// Check in a group
				$isAdmin = false;
				$isUser = false;
				$mGroups = array(XOOPS_GROUP_ANONYMOUS);
				if (is_object($xoopsUser)) {
					if ($xoopsUser->isAdmin(self::getMid())) {
						$isAdmin = true;
					}
					$isUser = true;
					$mGroups = $xoopsUser->getGroups();
				}
				$inSpecialGroup = (array_intersect($mGroups, (!empty($conf['special_groups']) ? $conf['special_groups'] : array())));

				// xoopsUrl
				$xoopsUrl = XOOPS_URL;

				// moduleUrl
				$moduleUrl = defined('XOOPS_MODULE_URL') ? XOOPS_MODULE_URL : XOOPS_URL . '/modules';

				// make CSS data
				$confCss = array();
				$confHeadCss = 'false';
				$conf['contentsCss'] = trim($conf['contentsCss']);
				if ($conf['contentsCss']) {
					foreach (preg_split('/[\r\n]+/', $conf['contentsCss']) as $_css) {
						$_css = trim($_css);
						if (!$inAdminPanel && $_css === '<head>') {
							$confHeadCss = 'true';
						} else if ($_css) {
							$confCss[] = $_css;
						}
					}
				}

				// theme contents.css
				//$_themeCss = '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/ckeditor4/contents.css';
				// @igamaster /themes/ theme_set / css /contents.css
                $_themeCss = '/themes/' . $GLOBALS['xoopsConfig']['theme_set'] . '/css/contents.css';
				if (is_file(XOOPS_ROOT_PATH . $_themeCss)) {
					$confCss[] = XOOPS_URL . $_themeCss;
				}

				// editor_reset.css
				$confCss[] = $moduleUrl . '/ckeditor4/templates/editor_reset.css';
			}

			// Make config
			$config = array();
			$modeconf = array(
				'html' => array(),
				'bbcode' => array(),
                'source' => array()
			);

			$config['contentsCss'] = array();
			$config['removePlugins'] = 'save,newpage,preview,print,about';  // Todo @gigamaster remove unused plugins
			$config['extraPlugins'] = '';
			if (defined('XOOPS_CUBE_LEGACY')) {
				$delegate->register('Ckeditor4.Utils.PreParseBuild_ckconfig');
				$delegate->call(new XCube_Ref($config), $params);
				if ($config['contentsCss'] && !is_array($config['contentsCss'])) {
					$config['contentsCss'] = array($config['contentsCss']);
				}
			} else {
				self::doFilter('config', 'PreParseBuild', $config, $params);
			}

			// Parse params
			if (!is_null($params['toolbar'])) {
				$config['toolbar'] = $params['toolbar'];
			}


            if (!is_null($params['uiColor'])) {
				$config['uiColor'] = $params['uiColor'];
			 }


			$config['xoopscodeXoopsUrl'] = XOOPS_URL . '/';

			if ($finder) {
				$config['filebrowserBrowseUrl'] = $moduleUrl . '/' . $finder . '/manager.php?cb=ckeditor';
				if ($uploadTo) {
					$config['filebrowserBrowseUrl'] .= '&start=' . $uploadTo;
					$config['uploadUrl'] = $config['filebrowserUploadUrl'] = $moduleUrl . '/' . $finder . '/connector.php';
					if (!isset($_SESSION['XELFINDER_CTOKEN'])) {
						$_SESSION['XELFINDER_CTOKEN'] = md5(session_id() . XOOPS_ROOT_PATH . (defined(XOOPS_SALT) ? XOOPS_SALT : XOOPS_DB_PASS));
					}
					$imageUploadJS = <<<EOD

	ckon("instanceReady",function(e){
		var editor = e.editor;
		editor.widgets.registered.uploadimage.onUploaded = function(img){
			var self = this;
			getShowImgSize(img.url, function(s,r) {
				var elm,
					tag = '<img src="'+encodeURI(img.url)+'" width="'+s.width+'" height="'+s.height+'">';
				if (r) {
					tag = '<a href="'+encodeURI(img.url)+'" target="_blank">'+tag+'</a>';
				}
				self.replaceWith(tag);
				editor.getSelection().removeAllRanges();
			});
		}
	});
	ckon("fileUploadRequest",function(e){
		e.stop();
		var fileLoader = e.data.fileLoader,
			formData = new FormData(),
			xhr = fileLoader.xhr;
		xhr.open('POST', fileLoader.uploadUrl, true);
		formData.append('cmd', 'upload');
		formData.append('overwrite', 0);
		formData.append('target', '{$uploadTo}');
		formData.append('ctoken', '{$_SESSION['XELFINDER_CTOKEN']}');
		formData.append('upload[]', fileLoader.file, fileLoader.fileName);
		fileLoader.xhr.send(formData);
	}, null, null, 4);
	ckon("fileUploadResponse",function(e){
		e.stop();
		var data = e.data,
			res = JSON.parse(data.fileLoader.xhr.responseText);
		if (!res.added || res.added.length < 1) {
			data.message = 'Can not upload.';
			e.cancel();
		} else {
			var file   = res.added[0];
			data.url = file.url? file.url :
				(data.url = file._localpath? file._localpath.replace(/^R/, '{$xoopsUrl}') : '');
			data.url = data.url.replace(location.protocol+'//'+location.host, '');
			try {
				data.url = decodeURIComponent(data.url);
			} catch(e) {}
		}
	});
EOD;
				}
			}

			$config['removePlugins'] = ($config['removePlugins'] ? (',' . trim($config['removePlugins'], ',')) : '');

			// build bbcode conf
			$modeconf['bbcode']['fontSize_sizes'] = 'xx-small;x-small;small;medium;large;x-large;xx-large';
			$modeconf['bbcode']['extraPlugins'] = (trim($conf['extraPlugins']) ? ('xoopscode,' . trim($conf['extraPlugins'])) : 'xoopscode') . ($config['extraPlugins'] ? (',' . trim($config['extraPlugins'], ',')) : '');
			$modeconf['bbcode']['enterMode'] = 2;
			$modeconf['bbcode']['shiftEnterMode'] = 2;
			if ($editor !== 'bbcode' || !isset($config['toolbar'])) {
				$modeconf['bbcode']['toolbar'] = trim($conf['toolbar_bbcode']);
			} else {
				$modeconf['bbcode']['toolbar'] = $config['toolbar'];
			}

			// build html conf
			$modeconf['html']['fontSize_sizes'] = '8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px';
			$modeconf['html']['extraPlugins'] = trim($conf['extraPlugins']) . ($config['extraPlugins'] ? (',' . trim($config['extraPlugins'], ',')) : '');
			$modeconf['html']['enterMode'] = (int) $conf['enterMode'];
			$modeconf['html']['shiftEnterMode'] = (int) $conf['shiftEnterMode'];
			if ($editor !== 'html' || !isset($config['toolbar'])) {
				if ($isAdmin) {
					$modeconf['html']['toolbar'] = trim($conf['toolbar_admin']);
				} else if ($inSpecialGroup) {
					$modeconf['html']['toolbar'] = trim($conf['toolbar_special_group']);
				} else if ($isUser) {
					$modeconf['html']['toolbar'] = trim($conf['toolbar_user']);
				} else {
					$modeconf['html']['toolbar'] = trim($conf['toolbar_guest']);
				}
				if (strtolower($modeconf['html']['toolbar']) === 'full') {
					$modeconf['html']['toolbar'] = null;
				}
			} else {
				$modeconf['html']['toolbar'] = $config['toolbar'];
			}

			$config['customConfig'] = trim($conf['customConfig']);

            $config['uiColor'] =  trim($conf['uiColor']);


			if ($conf['allowedContent']) $config['allowedContent'] = true;
			$config['autoParagraph'] = (bool) $conf['autoParagraph'];

			$config['contentsCss'] = array_merge($config['contentsCss'], $confCss);

			self::setCKConfigSmiley($config);

			// $modeSource = 0;
  //          $modeSource['contentsCss'] = array_merge($config['contentsCss'], $confCss);
            $modeconf['source']['startupMode'] = 'source';
            $modeconf['source']['enterMode'] = (int) $conf['enterMode'];
            $modeconf['source']['shiftEnterMode'] = (int) $conf['shiftEnterMode'];
            $modeconf['source']['disableAutoInline'] = true;
            $modeconf['source']['extraPlugins'] = 'codemirror';
            $modeconf['source']['removePlugins'] = 'sourcearea,sourcedialog';
            if ($editor !== 'source' || !isset($config['toolbar'])) {
                $modeconf['source']['toolbar'] = trim($conf['toolbar_bbcode']);
            } else {
                $modeconf['source']['toolbar'] = null;
            }
			$params['source'] = str_replace('&lt;!--ckeditor4FlgSource--&gt;', '', $params['value'], $modeSource);
           // $params['source'] =
            if ($modeconf['source']) {
                $modeconf['source']['toolbar'] = null;

            }


            // set $modeconf as $config['_modeconf'] for delegate
			$config['_modeconf'] = $modeconf;

			// lazy registering & call post build delegate
			if (defined('XOOPS_CUBE_LEGACY')) {
				$delegate->register('Ckeditor4.Utils.PostBuild_ckconfig');
				$delegate->call(new XCube_Ref($config), $params);
			} else {
				self::doFilter('config', 'PostBuild', $config, $params);
			}

			// restore $modeconf from $config['_modeconf']
			$modeconf = $config['_modeconf'];
			unset($config['_modeconf']);

			// merge editor config
			$config = array_merge($config, $modeconf[$editor]);

			// Make config json
			$config_json = array();
			foreach ($config as $key => $val) {
				if (!is_string($val) || !$val || $val[0] !== '[') {
					$val = json_encode($val);
				}
				$config_json[] = '"' . $key . '":' . $val;
			}
            //$config_json = '{' . join($config_json, ',') . '}';
            $config_json = '{' . implode( ',', $config_json ) . '}';

			foreach (array('html', 'bbcode', 'source') as $mode) {
				$name = 'config_json_' . $mode;
				$$name = array();
				foreach ($modeconf[$mode] as $key => $val) {
					if (!is_string($val) || !$val || $val[0] !== '[') {
						$val = json_encode($val);
					}
					array_push($$name, '"' . $key . '":' . $val);
				}
                //$$name = '{' . join($$name, ',') . '}';
                $$name = '{' . implode( ',', $$name ) . '}';
			}

			// allow html
			$allowhtml = ($params['allowhtml'] || $editor === 'html') ? 'true' : 'false';

			// Make Script
			$id = $params['id'];

			// build switcher
			if (is_null($params['switcher'])) {
				// default switcher
				$switcher = <<<EOD

	// local func
	var ck,ta = $("#{$id}"),
	set = function(name, check, disable) {
		var elm = eval(name+"_c");
		if (elm) {
			(check !== null) && elm.prop("checked", check);
			(disable !== null) && elm.prop("disabled", disable);
		}
	},
	find_c = function(name){
		var f = ta.closest("form");
		var elm = f.find('input[type="checkbox"][name="do'+name+'"]');
		(elm.length === 1) || (elm = f.find('input[type="checkbox"][name$="'+name+'"]'));
		(elm.length === 1) || (elm = f.find('input[type="checkbox"][name*="'+name+'"]'));
		return (elm.length === 1) ? elm : null;
	},
	// checkbox
	html_c = find_c('html'),
	bbcode_c = find_c('xcode'),
	br_c = find_c('br');
	// dohtml checkbox
	if (html_c) {
		html_c.change(function(){
			if (!$(this).is(":focus")) return;
			var obj = CKEDITOR.instances.{$id};
			obj && obj.destroy();
			br_c && br_c.prop("disabled", false);
			if ($(this).is(":checked")) {
				set("bbcode", false);
				set("br", false , true);
				ta.data("editor", "html");
				ck = CKEDITOR.replace("{$id}", $.extend({}, ta.data("ckconfig"), ta.data("ckconfig_html")));
			} else if (!bbcode_c || bbcode_c.is(":checked")) {
				set("br", true, true);
				ta.data("editor", "bbcode");
				ck = CKEDITOR.replace("{$id}", $.extend({}, ta.data("ckconfig"), ta.data("ckconfig_bbcode")));
			} else {
				return;
			}
			ta.data("ckon_restore")();
		});
	}
	// doxcode checkbox
	if (bbcode_c) {
		bbcode_c.change(function(){
			if (!$(this).is(":focus")) return;
			var obj = CKEDITOR.instances.{$id},
			conf = ta.data("ckconfig"),
			change = false;
			if ($(this).is(":checked")) {
				if (!html_c || (html_c && !html_c.is(":checked"))) {
					change = 'bbcode';
					conf = $.extend(conf, ta.data("ckconfig_bbcode"));
				}
			} else if ((!html_c && ta.data("allowhtml")) || (html_c && html_c.is(":checked"))) {
				if (ta.data("editor") != "html") {
					change = 'html';
					conf = $.extend(conf, ta.data("ckconfig_html"));
				}
			} else {
				change = 'none';
			}
			if (change) {
				obj && obj.destroy();
				ta.data("editor", change);
				if (change != "none") {
					set("br", (change == 'bbcode'), true);
					ck = CKEDITOR.replace("{$id}", conf);
					ta.data("ckon_restore")();
				} else {
					set("br", null, false);
				}
			}
		});
	}
	// form submit
	ta.closest("form").bind("submit", function(){
		var e = ta.data("editor");
		set("br", ((e == "bbcode")? true : ((e == "html")? false : null)), false);
	});
	// custom block editor (legacy or alysys)
	var html_s = ta.closest("form").find("select[name='c_type'],[name='ctypes[0]']");
	if (html_s && html_s.length == 1) {
		html_s.change(function(){
			var obj = CKEDITOR.instances.{$id}, conf;
			conf = ta.data("ckconfig");
			obj && obj.destroy();
			conf = ($(this).val() == "H")? $.extend(conf, ta.data("ckconfig_html")) : $.extend(conf, ta.data("ckconfig_bbcode"));
			if ($(this).val() != "P") {
				conf =	($(this).val() == "T")? $.extend(conf, {removePlugins:'smiley,'+conf.removePlugins}) : $.extend(conf, {removePlugins: conf.removePlugins.replace('smiley,', '')});
				ck = CKEDITOR.replace("{$id}", conf);
				ta.data("ckon_restore")();
			} else {
				conf =	($(this).val() == "P")? $.extend(conf, {removePlugins:'sourcearea,sourcedialog,'+conf.removePlugins}) : $.extend(conf, {removePlugins: conf.removePlugins.replace('sourcearea,sourcedialog,', '')});
				ck = CKEDITOR.replace("{$id}", conf);
				ta.data("ckon_restore")();
			}
		});
	}
EOD;
    } else {
        // custom switcher (by params)
        $switcher = 'try{ ' . $params['switcher'] . ' } catch(e) { console && console.log(e); }';
    }
    $onload = ($params['onload']) ? 'try{ ' . $params['onload'] . ' } catch(e) { console && console.log(e); }' : '';
    $onready = ($params['onready']) ? 'try{ ' . $params['onready'] . ' } catch(e) { console && console.log(e); }' : '';

    if (self::$cnt === 1) {
        $script_1st = <<<EOD

	if (typeof xoopsInsertText != 'undefined') {
		var xit = xoopsInsertText;
		xoopsInsertText = function(obj, str){
			if (obj.id && CKEDITOR.instances[obj.id]) {
				CKEDITOR.instances[obj.id].insertText(str);
			} else {
				xit(obj, str);
			}
		}
	}
	if (typeof xoopsCodeSmilie != 'undefined') {
		var xcs = xoopsCodeSmilie;
		xoopsCodeSmilie = function(id, str){
			if (CKEDITOR.instances[id]) {
				CKEDITOR.instances[id].insertText(str);
			} else {
				xcs(id, str);
			}
		}
	}
EOD;
    if ($finder) {
        $script_1st .= <<<EOD

getShowImgSize = function(url, callback) {
var ret = {};
$('<img alt=""/>').attr('src', url).on('load', function() {
var w = this.naturalWidth,
    h = this.naturalHeight,
    s = {$imgSize},
    resized = false;
if (w > s || h > s) {
    resized = true;
    if (w > h) {
        h = Math.round(h * (s / w));
        w = s;
    } else {
        w = Math.round(w * (s / h));
        h = s;
    }
}
callback({width: w, height: h}, resized);
});
};
CKEDITOR.on('dialogDefinition', function (event) {
var editor = event.editor,
dialogDefinition = event.data.definition,
tabCount = dialogDefinition.contents.length,
uploadButton, submitButton, inputId,
// elFinder configs
elfDirHashMap = { // Dialog name / elFinder holder hash Map
    image : '',
    flash : '',
    files : '',
    link  : '',
    fb    : '{$uploadTo}' // fallback target
},
customData = { ctoken: '{$_SESSION['XELFINDER_CTOKEN']}' }; // any custom data to post
for (var i = 0; i < tabCount; i++) {
try {
    uploadButton = dialogDefinition.contents[i].get('upload');
    submitButton = dialogDefinition.contents[i].get('uploadButton');
} catch(e) {
    uploadButton = submitButton = null;
}
if (uploadButton !== null && submitButton !== null) {
    uploadButton.hidden = false;
    submitButton.hidden = false;
    uploadButton.onChange = function() {
        inputId = this.domId;
    }
    submitButton.onClick = function(e) {
        var dialogName = CKEDITOR.dialog.getCurrent()._.name,
            target = elfDirHashMap[dialogName]? elfDirHashMap[dialogName] : elfDirHashMap['fb'],
            name   = $('#'+inputId),
            btn    = $('#'+this.domId),
            input  = name.find('iframe').contents().find('form').find('input:file'),
            spinner= $('<img src="{$xoopsUrl}/common/elfinder/img/spinner-mini.gif" width="16" height="16" style="vertical-align:middle"/>'),
            error  = function(err) {
                alert(err.replace('<br>', '\\n'));
            };
        if (input.val() && ! btn.hasClass('cke_button_disabled')) {
            var fd = new FormData();
            fd.append('cmd', 'upload');
            fd.append('overwrite', 0); // disable upload overwrite to make to increment file name
            fd.append('target', target);
            $.each(customData, function(key, val) {
                fd.append(key, val);
            });
            fd.append('upload[]', input[0].files[0]);
            btn.addClass('cke_button_disabled').append(spinner);
            $.ajax({
                url: editor.config.filebrowserUploadUrl,
                type: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                dataType: 'json'
            })
            .done(function( data ) {
                if (data.added && data.added[0]) {
                    var url = data.added[0].url,
                        dialog = CKEDITOR.dialog.getCurrent(),
                        tabName = dialog._.currentTabId,
                        urlObj;
                    if (dialogName == 'image') {
                        urlObj = 'txtUrl';
                    } else if (dialogName == 'flash') {
                        urlObj = 'src';
                    } else if (dialogName == 'files' || dialogName == 'link') {
                        urlObj = 'url';
                    } else {
                        return;
                    }
                    dialog.selectPage('info');
                    dialog.setValueOf('info', urlObj, url);
                    if (dialogName == 'image' && tabName == 'info') {
                        getShowImgSize(url, function(s,r) {
                            if (r) {
                                try {
                                    dialog.setValueOf('info', 'txtWidth', s.width);
                                    dialog.setValueOf('info', 'txtHeight', s.height);
                                    dialog.preview.$.style.width = s.width+'px';
                                    dialog.preview.$.style.height = s.height+'px';
                                    dialog.setValueOf('Link', 'txtUrl', url);
                                    dialog.setValueOf('Link', 'cmbTarget', '_blank');
                                } catch(e) {}
                            }
                        });
                    }
                    if (dialogName == 'files' || dialogName == 'link') {
                        try {
                            dialog.setValueOf('info', 'linkDisplayText', data.added[0].name);
                        } catch(e) {}
                    }
                } else {
                    error(data.error || data.warning || 'errUploadFile');
                }
            })
            .fail(function() {
                error('errUploadFile');
            })
            .always(function() {
                input.val('');
                spinner.remove();
                btn.removeClass('cke_button_disabled');
            });
        }
        return false;
    }
}
}
});
EOD;
    }
    } else {
        $script_1st = '';
    }
    $script = <<<EOD

(function(){
	{$onload}{$script_1st}
	var ckconfig_{$id},ckconfig_html_{$id},ckconfig_bbcode_{$id};// for compat
	var ck,
	conf = {$config_json},
	id = "{$id}",
	ta = $("#{$id}")
	.data("editor", "{$editor}")
	.data("allowhtml", {$allowhtml})
	.data("ckconfig_html", {$config_json_html})
	.data("ckconfig_bbcode", {$config_json_bbcode})
	.data("ckon", function(name,func){
		var ckev = (ta.data("ckev") || {});
		ckev[name] = (ckev[name] || []);
		ckev[name].push(func);
		ta.data("ckev", ckev);
		CKEDITOR.instances[id].on(name, func);
	})
	.data("ckon_restore", function(){
		var ck = CKEDITOR.instances[id];
		$.each(ta.data("ckev"), function(name, fs){
			$.each(fs, function(i, func){ ck.on(name, func); });
		});
	});
	if (! conf.width) conf.width = ta.parent().width() + 'px';
	var headCss = $.map($("head link[rel='stylesheet']").filter("[media!='print'][media!='handheld']"), function(o){ return o.href; });
	if ({$confHeadCss} && headCss) conf.contentsCss = headCss.concat(conf.contentsCss);
	ta.data("ckconfig", conf);
	ckconfig_{$id} = conf;
	ckconfig_html_{$id} = ta.data("ckconfig_html");
	ckconfig_bbcode_{$id} = ta.data("ckconfig_bbcode");
	ck = CKEDITOR.replace("{$id}", conf);
	var ckon = ta.data("ckon");
	ckon("focus",function(e){ta.trigger("focus");});
	ckon("blur",function(e){
		e.editor.updateElement();
		ta.trigger("blur");
	});
	ckon("instanceReady",function(e){{$onready}});
	ckon("getData",function(e){
		if (e.editor.mode == 'source') e.data.dataValue += '<!--ckeditor4FlgSource-->';
	});
	ckon("setData",function(e){
		e.data.dataValue = e.data.dataValue.replace('<!--ckeditor4FlgSource-->', '');
	});{$imageUploadJS}
	ta.closest("form").find("input").on("mousedown", function(){
		ck && ck.updateElement();
	});
	{$switcher}
})();
// @gigamaster added XD v231 - Dialog for copy/paste
CKEDITOR.on("instanceReady", function(event) {
    event.editor.on("beforeCommandExec", function(event) {
        // Show the paste dialog for the paste buttons and right-click paste
        if (event.data.name == "paste") {
            event.editor._.forcePasteDialog = true;
        }
        // Don't show the paste dialog for Ctrl+Shift+V
        if (event.data.name == "pastetext" && event.data.commandData.from == "keystrokeHandler") {
            event.cancel();
        }
    })
});
EOD;
		}
		return $script;
	}

	private static function getSmiley()
	{
		static $smiley;
		if (is_null($smiley)) {
			$smiley = array();
			$db = &XoopsDatabaseFactory::getDatabaseConnection();
			if (_CHARSET !== 'UTF-8') self::setDbClientEncoding('utf8');
			if ($res = $db->query('SELECT code, smile_url, emotion FROM ' . $db->prefix('smiles') . ' ORDER BY display DESC, id ASC')) {
				$baseUrl = str_replace(XOOPS_URL . '/', '', XOOPS_UPLOAD_URL) . '/';
				while ($smile = $db->fetchArray($res)) {
					$smiley['smile_url'][] = $baseUrl . $smile['smile_url'];
					$smiley['emotion'][] = $smile['emotion'];
					$smiley['smileyMap'][$smile['emotion']] = ' ' . $smile['code'];
				}
			}
			if (_CHARSET !== 'UTF-8') self::restoreDbClientEncoding();
		}
		return $smiley;
	}

	private static function setCKConfigSmiley(&$config)
	{
		if ($smileys = self::getSmiley()) {
			$config['smiley_path'] = XOOPS_URL . '/';
			$config['smiley_images'] = $smileys['smile_url'];
			$config['smiley_descriptions'] = $smileys['emotion'];
			$config['xoopscode_smileyMap'] = $smileys['smileyMap'];
		}
	}

	private static function setDbClientEncoding($enc)
	{
		self::restoreDbClientEncoding(false);
		$db = &XoopsDatabaseFactory::getDatabaseConnection();
		$link = (is_object($db->conn) && get_class($db->conn) === 'mysqli') ? $db->conn : false;
		if ($link && function_exists('mysqli_set_charset')) {
			mysqli_set_charset($link, $enc);
		// } else if (function_exists('mysql_set_charset')) {
		// 	mysql_set_charset($enc);
		} else {
			$db->queryF('SET NAMES \'' . $enc . '\'');
		}
	}

	private static function restoreDbClientEncoding($set = true)
	{
		static $enc;
		if (is_null($enc)) {
			$db = &XoopsDatabaseFactory::getDatabaseConnection();
			$res = $db->queryF('SHOW VARIABLES LIKE \'character\_set\_client\'');
			list(, $enc) = $db->fetchRow($res);
		}
		if ($set) {
			self::setDbClientEncoding($enc);
		}
	}

	private static function doFilter($base, $phase, &$val, $params = null)
	{
		static $filterPath;

		if (!$filterPath) {
			$filterPath = dirname(__FILE__, 2) . '/filters/';
		}

		if ($filters = @glob($filterPath . $base . '/' . $phase . '*.filter.php')) {
			foreach ($filters as $filter) {
				include($filter);
				$class = 'ckeditor4Filter' . ucfirst($base) . str_replace('.filter.php', '', basename($filter));
				if (class_exists($class)) {
					$cObj = new $class();
					if (method_exists($cObj, 'filter')) {
						$cObj->filter($val, $params);
					}
					$cObj = null;
				}
			}
		}
	}
}

class Ckeditor4_ParentTextArea extends XCube_ActionFilter
{
	/**
	 *	@public
	 */
	public function render(&$html, $params)
	{
		$js = Ckeditor4_Utils::getJS($params);

		$root = &XCube_Root::getSingleton();
		$renderSystem = &$root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

		$renderTarget = &$renderSystem->createRenderTarget('main');
		$renderTarget->setAttribute('legacy_module', 'ckeditor4');
		$renderTarget->setTemplateName("ckeditor4_textarea.html");
		$renderTarget->setAttribute("ckeditor4_params", $params);

		$renderSystem->render($renderTarget);

		$html = $renderTarget->getResult();
		if (strpos($params['value'], '&lt;!--norich--&gt;') === false) {
			// Add script into HEAD
			$jQuery = $root->mContext->getAttribute('headerScript');
			$jQuery->addScript($js);
			$jQuery->addLibrary('/modules/ckeditor4/ckeditor/ckeditor.js');
		}
	}
}
