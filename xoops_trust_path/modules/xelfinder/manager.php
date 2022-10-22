<?php
/**
 * X-elFinder module for XCL
 * @package    XelFinder
 * @version    XCL 2.3.1
 * @author     Naoki Sawada (aka Nao-pon) <https://github.com/nao-pon>
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! defined( 'XOOPS_TRUST_PATH' ) ) {
	exit;
}
if ( ! defined( 'XOOPS_MODULE_PATH' ) ) {
	define( 'XOOPS_MODULE_PATH', XOOPS_ROOT_PATH . '/modules' );
}
if ( ! defined( 'XOOPS_MODULE_URL' ) ) {
	define( 'XOOPS_MODULE_URL', XOOPS_URL . '/modules' );
}

$target = isset( $_GET['target'] ) ? ( preg_match( '/^[a-zA-Z0-9_:.-]+$/', $_GET['target'] ) ? $_GET['target'] : '' ) : '';

if ( ! isset( $_GET['cb'] ) && isset( $_GET['getfile'] ) && 'ckeditor' === $_GET['getfile'] ) {
	$_GET['cb'] = $_GET['getfile'];
}
$callback = isset( $_GET['cb'] ) ? ( preg_match( '/^[a-zA-Z0-9_]+$/', $_GET['cb'] ) ? $_GET['cb'] : '' ) : '';
$callback = $callback ? 'getFileCallback_' . $callback : 'void 0';

$siteimg = ( empty( $_GET['si'] ) && empty( $use_bbcode_siteimg ) ) ? 0 : 1;

$admin = ( isset( $_GET['admin'] ) ) ? 1 : 0;

$myurl            = XOOPS_MODULE_URL . '/' . $mydirname;
$elfurl           = XOOPS_URL . '/common/elfinder';

$modules_basename = trim( str_replace( XOOPS_URL, '', XOOPS_MODULE_URL ), '/' );

$module_handler  = xoops_getHandler( 'module' );
$xelfinderModule = $module_handler->getByDirname( $mydirname );
$xelfVer         = $xelfinderModule->getVar( 'version' );
$config_handler  = xoops_getHandler( 'config' );
$config          = $config_handler->getConfigsByCat( 0, $xelfinderModule->getVar( 'mid' ) );

// load xoops_elFinder
include_once __DIR__ . '/class/xoops_elFinder.class.php';

$xoops_elFinder = new xoops_elFinder( $mydirname );
$xoops_elFinder->setConfig( $config );

$conector_url = $conn_is_ext = '';
if ( ! empty( $config['connector_url'] ) ) {
	$conector_url = $config['connector_url'];
	! $config['conn_url_is_ext'] || $conn_is_ext = 1;
}
$managerJs      = '';
$_plugin_dir    = __DIR__ . '/plugins/';

$_js_cache_path = $_js_cache_times = [];
foreach ( explode( "\n", $config['volume_setting'] ) as $_vol ) {
	$_vol = trim( $_vol );
	if ( ! $_vol || '#' === $_vol[0] ) {
		continue;
	}
	list( , $_plugin, $_dirname ) = explode( ':', $_vol );
	$_plugin = trim( $_plugin );
	if ( preg_match( '#(?:uploads|' . $modules_basename . ')/([^/]+)#i', trim( $_dirname ), $_match ) ) {
		$_dirname = $_match[1];
	} else {
		$_dirname = $_plugin;
	}
	$_key = ( $_dirname !== $_plugin ) ? ( $_dirname . '!' . $_plugin ) : $_dirname;
	$_js  = $_plugin_dir . $_plugin . '/manager.js';
	if ( is_file( $_js ) ) {
		$_js_cache_times[ $_key ] = filemtime( $_js );
		$_js_cache_path[ $_key ]  = $_js;
	}
}
if ( $_js_cache_path ) {
	ksort( $_js_cache_path );
	$_keys      = array_keys( $_js_cache_path );
	$_managerJs = '/cache/' . implode( ',', $_keys ) . '_manager.js';
	$_js_cacahe = $mydirpath . $_managerJs;
	if ( ! is_file( $_js_cacahe ) || filemtime( $_js_cacahe ) < max( $_js_cache_times ) ) {
		$_src = '';
		foreach ( $_keys as $_key ) {
			list( $_dirname ) = explode( '!', $_key );
			$_src .= str_replace( '$dirname', $_dirname, file_get_contents( $_js_cache_path[ $_key ] ) );
		}
		file_put_contents( $_js_cacahe, $_src );
	}
	$managerJs = '<script src="' . $myurl . $_managerJs . '?v=' . $xelfVer . '" charset="UTF-8"></script>' . "\n";
}

$default_tmbsize = isset( $config['thumbnail_size'] ) ? (int) $config['thumbnail_size'] : '160';
$debug           = ( ! empty( $config['debug'] ) );
// cToken uses for CSRF protection
$cToken = $xoops_elFinder->getCToken();

$viewport = ( preg_match( '/Mobile|Android/i', $_SERVER['HTTP_USER_AGENT'] ) ) ? '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />' : '';

$userLang = xelfinder_detect_lang();

if ( empty( $config['jquery'] ) ) {
	$jQueryVersion = ( false === strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/4.0' ) ) ? '3.5.1' : '1.12.4';
	$jQueryCDN     = '//cdnjs.cloudflare.com/ajax/libs/jquery/%s/jquery.min.js';
	$jQueryUrl     = sprintf( $jQueryCDN, $jQueryVersion );
} else {
	$jQueryUrl = trim( $config['jquery'] );
}

if ( empty( $config['jquery_ui'] ) ) {
	$jQueryUIVersion = '1.12.1';
	$jQueryUICDN     = '//cdnjs.cloudflare.com/ajax/libs/jqueryui/%s';
	$jQueryUIUrl     = sprintf( $jQueryUICDN, $jQueryUIVersion ) . '/jquery-ui.min.js';
} else {
	$jQueryUIUrl = trim( $config['jquery_ui'] );
}

if ( empty( $config['jquery_ui_css'] ) ) {
	if ( ! $jQueryUiTheme = @$config['jquery_ui_theme'] ) {
		$jQueryUiTheme = 'smoothness';
	} else {
		if ( 'base' === $jQueryUiTheme && version_compare( $jQueryUIVersion, '1.10.1', '>' ) ) {
			$jQueryUiTheme = 'smoothness';
		}
	}
	// CDN
	if ( ! preg_match( '#^(?:https?:)?//#i', $jQueryUiTheme ) ) {
		$jQueryUiTheme = sprintf( $jQueryUICDN, $jQueryUIVersion ) . '/themes/' . $jQueryUiTheme . '/jquery-ui.min.css';
	}
} else {
	$jQueryUiTheme = trim( $config['jquery_ui_css'] );
}

$editorsJs = ! empty( $config['editors_js'] ) ? trim( $config['editors_js'] ) : ( $elfurl . '/js/extras/editors.default' . ( $debug ? '' : '.min' ) . '.js?v=' . $xelfVer );

$optionsJs = ! empty( $config['ui_options_js'] ) ? trim( $config['ui_options_js'] ) : ( $myurl . '/include/js/xelfinderUiOptions.default.js?v=' . $xelfVer );
if ( ! preg_match( '~^(?:/|http)~i', $optionsJs ) ) {
	$optionsJs = XOOPS_URL . '/' . $optionsJs;
}

$title = mb_convert_encoding( $config['manager_title'], 'UTF-8', _CHARSET );

$start = ( ! empty( $_GET['start'] ) && preg_match( '/^[a-zA-Z0-9_-]+$/', $_GET['start'] ) ) ? $_GET['start'] : '';

while ( ob_get_level() && @ob_end_clean() ) {}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta charset="utf-8">
        <title><?php echo $title ?></title>
		<?php echo $viewport ?>

        <link rel="stylesheet" href="<?php echo $jQueryUiTheme ?>" type="text/css">

		<?php if ( $debug ) { ?>
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/commands.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/common.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/contextmenu.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/cwd.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/dialog.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/fonts.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/navbar.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/places.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/quicklook.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/statusbar.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/toast.css" type="text/css">
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/toolbar.css" type="text/css">
		<?php } else { ?>
            <link rel="stylesheet" href="<?php echo $elfurl ?>/css/elfinder.min.css?v=<?php echo $xelfVer ?>" type="text/css">
		<?php } ?>

        <script src="<?php echo $jQueryUrl ?>"></script>
        <script src="<?php echo $jQueryUIUrl ?>"></script>

		<?php if ( $debug ) { ?>
            <!-- elfinder core -->
            <script src="<?php echo $elfurl ?>/js/elFinder.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.version.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/jquery.elfinder.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.mimetypes.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.options.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.options.netmount.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.history.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.command.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/elFinder.resources.js" charset="UTF-8"></script>

            <!-- elfinder ui -->
            <script src="<?php echo $elfurl ?>/js/ui/button.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/contextmenu.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/cwd.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/dialog.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/fullscreenbutton.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/navbar.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/navdock.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/overlay.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/panel.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/path.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/places.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/searchbutton.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/sortbutton.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/stat.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/toast.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/toolbar.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/tree.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/uploadButton.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/viewbutton.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/ui/workzone.js" charset="UTF-8"></script>

            <!-- elfinder commands -->
            <script src="<?php echo $elfurl ?>/js/commands/archive.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/back.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/chmod.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/colwidth.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/copy.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/cut.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/download.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/duplicate.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/edit.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/empty.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/extract.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/forward.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/fullscreen.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/getfile.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/help.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/hidden.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/hide.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/home.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/info.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/mkdir.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/mkfile.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/netmount.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/open.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/opendir.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/opennew.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/paste.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/places.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/preference.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/quicklook.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/quicklook.plugins.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/reload.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/rename.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/resize.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/restore.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/rm.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/search.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/selectall.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/selectinvert.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/selectnone.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/sort.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/undo.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/up.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/upload.js" charset="UTF-8"></script>
            <script src="<?php echo $elfurl ?>/js/commands/view.js" charset="UTF-8"></script>

            <!-- elfinder languages -->
            <script src="<?php echo $elfurl ?>/js/i18n/elfinder.en.js" charset="UTF-8"></script>

            <!-- elfinder dialog -->
            <script src="<?php echo $elfurl ?>/js/jquery.dialogelfinder.js" charset="UTF-8"></script>

		<?php } else { ?>
            <script src="<?php echo $elfurl ?>/js/elfinder.min.js?v=<?php echo $xelfVer ?>" charset="UTF-8"></script>
		<?php } ?>

        <script src="<?php echo $elfurl ?>/js/extras/quicklook.googledocs<?php if ( ! $debug ) { ?>.min<?php } ?>.js?v=<?php echo $xelfVer ?>" charset="UTF-8"></script>
        <script src="<?php echo $editorsJs ?>" charset="UTF-8"></script>

        <!-- elFinder initialization (REQUIRED) -->
        <link rel="stylesheet" href="<?php echo $myurl ?>/include/css/manager.css?v=<?php echo $xelfVer ?>" type="text/css">
        <script type="text/javascript">
            var target = '<?php echo $target ?>';
            var rootUrl = '<?php echo XOOPS_URL ?>';
            var moduleUrl = '<?php echo XOOPS_MODULE_URL ?>';
            var myUrl = moduleUrl + '/<?php echo $mydirname?>/';
            var imgUrl = myUrl + 'images/';
            var baseUrl = '<?php echo $elfurl?>/';
            var connectorUrl = '<?php echo $conector_url?>';
            var connIsExt = <?php echo (int) $conn_is_ext?>;
            var useSiteImg = <?php echo $siteimg ?>;
            var imgThumb = '';
            var itemPath = '';
            var itemObject = [];
            var defaultTmbSize = <?php echo $default_tmbsize?>;
            var lang = '<?php echo $userLang?>';
            var xoopsUid = '<?php echo $xoops_elFinder->getUid()?>';
            var adminMode = <?php echo $admin?>;
            var cToken = '<?php echo $cToken?>';
            var startPathHash = '<?php echo $start?>';
            var autoSyncSec = <?php echo $xoops_elFinder->getAutoSyncSec()?>;
            var autoSyncStart = <?php echo( empty( $config['autosync_start'] ) ? 'false' : 'true' )?>;
            var useSharecadPreview = <?php echo( empty( $config['use_sharecad_preview'] ) ? 'false' : 'true' )?>;
            var useGoogleDocsPreview = <?php echo( empty( $config['use_google_preview'] ) ? 'false' : 'true' )?>;
            var useOfficePreview = <?php echo( empty( $config['use_office_preview'] ) ? 'false' : 'true' )?>;
            var googleMapsApiKey = <?php echo( empty( $config['gmaps_apikey'] ) ? 'void 0' : '\'' . $config['gmaps_apikey'] . '\'' )?>;
            var creativeCloudApikey = <?php echo( empty( $config['creative_cloud_apikey'] ) ? 'void 0' : '\'' . $config['creative_cloud_apikey'] . '\'' )?>;
        </script>

        <script src="<?php echo $myurl ?>/include/js/commands/perm.js?v=<?php echo $xelfVer ?>"></script>
        <script src="<?php echo $myurl ?>/include/js/commands/auth.js?v=<?php echo $xelfVer ?>"></script>
        <script src="<?php echo $optionsJs ?>" charset="UTF-8"></script>
        <script src="<?php echo $myurl ?>/include/js/manager.js?v=<?php echo $xelfVer ?>" charset="UTF-8"></script>
        <script type="text/javascript" charset="UTF-8">
            var callbackFunc = <?php echo $callback ?>;
        </script>
		<?php echo $managerJs ?>

    </head>
    <body style="margin:0;padding:0;">
    <!-- Element where elFinder will be created (REQUIRED) -->
    <div id="elfinder" style="height:100%;border:none;"></div>
    </body>
    </html>
<?php exit();

function xelfinder_detect_lang() {
	if ( $accept = @ $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) {
		if ( preg_match_all( "/([\w_-]+)/i", $accept, $match, PREG_PATTERN_ORDER ) ) {
			foreach ( $match[1] as $lang ) {
				list( $l, $c ) = array_pad( preg_split( '/[_-]/', $lang ), 2, '' );
				$lang = strtolower( $l );
				if ( $c ) {
					$lang .= '_' . strtoupper( $c );
				}
				if ( is_file( XOOPS_ROOT_PATH . '/common/elfinder/js/i18n/elfinder.' . $lang . '.js' ) ) {
					return $lang;
				} else if ( is_file( XOOPS_ROOT_PATH . '/common/elfinder/js/i18n/elfinder.' . substr( $lang, 0, 2 ) . '.js' ) ) {
					return substr( $lang, 0, 2 );
				}
			}
		}
	}

	return ''; // to detect by JavaScript in manager.js
}
