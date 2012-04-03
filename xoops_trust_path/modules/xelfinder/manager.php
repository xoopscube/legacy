<?php
if (! defined('XOOPS_TRUST_PATH')) exit;

$target = isset($_GET['target'])? (preg_match('/^[a-zA-Z0-9_:.-]+$/', $_GET['target'])? $_GET['target'] : '') : '';

$callback = isset($_GET['cb'])? (preg_match('/^[a-zA-Z0-9_]+$/', $_GET['cb'])? $_GET['cb'] : '') : 'bbcode';
$callback = 'getFileCallback_' . $callback;

$myurl = XOOPS_URL . '/modules/' . $mydirname;
$elfurl = XOOPS_URL . '/common/elfinder';

$module_handler =& xoops_gethandler('module');
$xelfinderModule = $module_handler->getByDirname($mydirname);
$config_handler =& xoops_gethandler('config');
$config = $config_handler->getConfigsByCat(0, $xelfinderModule->getVar('mid'));

$managerJs = '';
$_plugin_dir = dirname(__FILE__) . '/plugins/';
$_js_cache_path = $_js_cache_times = array();
foreach(explode("\n", $config['volume_setting']) as $_vol) {
	$_vol = trim($_vol);
	if (! $_vol || $_vol[0] === '#') continue;
	list(, $_plugin, $_dirname) = explode(':', $_vol);
	$_plugin = trim($_plugin);
	if (preg_match('#(?:uploads|modules)/([^/]+)#i', trim($_dirname), $_match)) {
		$_dirname = $_match[1];
	} else {
		$_dirname = $_plugin;
	}
	$_key = ($_dirname !== $_plugin)? ($_dirname.'!'.$_plugin) : $_dirname;
	$_js = $_plugin_dir . $_plugin . '/manager.js';
	if (is_file($_js)) {
		$_js_cache_times[$_key] = filemtime($_js);
		$_js_cache_path[$_key] = $_js;
	}
}
if ($_js_cache_path) {
	ksort($_js_cache_path);
	$_keys = array_keys($_js_cache_path);
	$_managerJs = '/cache/' . join(',', $_keys) . '_manager.js';
	$_js_cacahe = $mydirpath . $_managerJs;
	if (! is_file($_js_cacahe) || filemtime($_js_cacahe) < max($_js_cache_times)) {
		$_src = '';
		foreach($_keys as $_key) {
			list($_dirname) = explode('!', $_key);
			$_src .= str_replace('$dirname', $_dirname, file_get_contents($_js_cache_path[$_key]));
		}
		file_put_contents($_js_cacahe, $_src);
	}
	$managerJs = '<script type="text/javascript" src="'.$myurl.$_managerJs.'" charset="utf-8"></script>' . "\n";
}

$default_tmbsize = isset($config['thumbnail_size'])? (int)$config['thumbnail_size'] : '160';
$debug = (! empty($config['debug']));

$viewport = (preg_match('/Mobile/i', $_SERVER['HTTP_USER_AGENT']))? '<meta name="viewport" content="width=device-width" />' : '';

$userLang = xelfinder_detect_lang();

$jQueryVersion   = '1.7.2';
$jQueryUIVersion = '1.8.18';

while(ob_get_level()) {
	if (! ob_end_clean()) break;
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Image Manager - elFinder 2.0</title>
		<?php echo $viewport ?>
		<base href="<?php echo XOOPS_URL ?>/modules/<?php echo $mydirname?>/">

		<script src="//ajax.googleapis.com/ajax/libs/jquery/<?php echo $jQueryVersion?>/jquery.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/<?php echo $jQueryUIVersion?>/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/<?php echo $jQueryUIVersion?>/themes/smoothness/jquery-ui.css" type="text/css" media="screen" charset="utf-8">
	
		<?php if ($debug) {?>
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/common.css"      type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/dialog.css"      type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/toolbar.css"     type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/navbar.css"      type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/statusbar.css"   type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/contextmenu.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/cwd.css"         type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/quicklook.css"   type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/commands.css"    type="text/css" media="screen" charset="utf-8">
		<?php } else {?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $elfurl ?>/css/elfinder.min.css">
		<?php }?>
	
		<link rel="stylesheet" href="<?php echo $elfurl ?>/css/theme.css"       type="text/css" media="screen" charset="utf-8">
	
		<?php if ($debug) {?>
		<!-- elfinder core -->
		<script src="<?php echo $elfurl ?>/js/elFinder.js"           type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/elFinder.version.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/jquery.elfinder.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/elFinder.resources.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/elFinder.options.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/elFinder.history.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/elFinder.command.js"   type="text/javascript" charset="utf-8"></script>
	
		<!-- elfinder ui -->
		<script src="<?php echo $elfurl ?>/js/ui/overlay.js"       type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/workzone.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/navbar.js"        type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/dialog.js"        type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/tree.js"          type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/cwd.js"           type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/toolbar.js"       type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/button.js"        type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/uploadButton.js"  type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/viewbutton.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/searchbutton.js"  type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/sortbutton.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/panel.js"         type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/contextmenu.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/path.js"          type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/stat.js"          type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/ui/places.js"        type="text/javascript" charset="utf-8"></script>
	
		<!-- elfinder commands -->
		<script src="<?php echo $elfurl ?>/js/commands/back.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/forward.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/reload.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/up.js"        type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/home.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/copy.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/cut.js"       type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/paste.js"     type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/open.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/rm.js"        type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/info.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/duplicate.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/rename.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/help.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/getfile.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/mkdir.js"     type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/mkfile.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/upload.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/download.js"  type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/edit.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/quicklook.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/quicklook.plugins.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/extract.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/archive.js"   type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/search.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/view.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/resize.js"    type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/sort.js"      type="text/javascript" charset="utf-8"></script>
		<script src="<?php echo $elfurl ?>/js/commands/pixlr.js"     type="text/javascript" charset="utf-8"></script>
	
		<!-- elfinder languages -->
		<script src="<?php echo $elfurl ?>/js/i18n/elfinder.en.js"    type="text/javascript" charset="utf-8"></script>
	
		<!-- elfinder dialog -->
		<script src="<?php echo $elfurl ?>/js/jquery.dialogelfinder.js"     type="text/javascript" charset="utf-8"></script>
		<?php } else {?>
		<script type="text/javascript" src="<?php echo $elfurl ?>/js/elfinder.min.js"></script>
		<?php }?>
		
		<script src="<?php echo $elfurl ?>/js/i18n/elfinder.<?php echo $userLang?>.js"    type="text/javascript" charset="utf-8"></script>

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo XOOPS_URL ?>/common/js/toastmessage/css/jquery.toastmessage.css">
		<script type="text/javascript" src="<?php echo XOOPS_URL ?>/common/js/toastmessage/jquery.toastmessage.js" charset="utf-8"></script>

		<!-- elFinder initialization (REQUIRED) -->
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $myurl ?>/include/css/manager.css">
		<script type="text/javascript" charset="utf-8">
			var target = '<?php echo $target ?>';
			var rootUrl = '<?php echo XOOPS_URL ?>';
			var myUrl = rootUrl + '/modules/<?php echo $mydirname?>/';
			var imgUrl = myUrl + 'images/';
			var imgThumb = '';
			var itemPath = '';
			var itemObject = [];
			var defaultTmbSize = <?php echo $default_tmbsize?>;
			var lang = '<?php echo $userLang?>';
		</script>
		<script type="text/javascript" src="<?php echo $myurl ?>/include/js/commands/perm.js" charset="utf-8"></script>
		<script type="text/javascript" src="<?php echo $myurl ?>/include/js/manager.js" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
			var callbackFunc = <?php echo $callback ?>;
		</script>
		<?php echo $managerJs ?>
	</head>
	<body>

		<!-- Element where elFinder will be created (REQUIRED) -->
		<div id="elfinder"></div>

	</body>
</html>
<?php exit();

function xelfinder_detect_lang() {
	$replaser = array(
		'ja'    => 'jp',
		'ja_JP' => 'jp'
	);
	if ($accept = @ $_SERVER['HTTP_ACCEPT_LANGUAGE']) {
		if (preg_match_all("/([\w_-]+)/i",$accept,$match,PREG_PATTERN_ORDER)) {
			foreach($match[1] as $lang) {
				list($l, $c) = array_pad(preg_split('/[_-]/', $lang), 2, '');
				$lang = strtolower($l);
				if ($c) {
					$lang .= '_' . strtoupper($c);
				}
				if (isset($replaser[$lang])) {
					$lang = $replaser[$lang];
				}
				if (is_file( XOOPS_ROOT_PATH.'/common/elfinder/js/i18n/elfinder.'.$lang.'.js')) {
					return $lang;
				} else if (is_file( XOOPS_ROOT_PATH.'/common/elfinder/js/i18n/elfinder.'.substr($lang, 0, 2).'.js')) {
					return substr($lang, 0, 2);
				}
			}
		}
	}
	return 'en';
}
