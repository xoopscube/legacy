<?php
/**
 * X-elFinder module for XCL
 * @package    XelFinder
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Naoki Sawada (aka Nao-pon) <https://github.com/nao-pon>
 * @copyright  (c) 2005-2022 Author
 * @license    GPL V2.0
 */

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';
if ( ! file_exists( $langmanpath ) ) {
	die( 'install the latest altsys' );
}
require_once( $langmanpath );
$langman = D3LanguageManager::getInstance();
$langman->read( 'modinfo.php', $mydirname, $mytrustdirname, false );
$constpref = '_MI_' . strtoupper( $mydirname );

$modversion['dirname']          = $mydirname;
$modversion['trust_dirname']    = $mytrustdirname;
$modversion['name']             = 'xelFinder';
$modversion['version']          = '2.61';
$modversion['detailed_version'] = '2.61.3';
$modversion['description']      = constant( $constpref . '_DESC' );
$modversion['author']           = 'Naoki Sawada (aka Nao-pon) <Hypweb.net>';
$modversion['credits']          = 'Naoki Sawada (aka Nao-pon). Nuno Luciano (aka gigamaster) 2020 XCL PHP7';
$modversion['license']          = 'GPL';
$modversion['image']            = '/images/module_xelfinder.svg'; // $modversion['image'] = is_file( $mydirpath.'/images/module_xelfinder.svg' ) ? '/images/module_xelfinder.svg' : 'module_icon.php' ;
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = true;
$modversion['read_any']         = true;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false;
$modversion['tables']  = [];

// Admin
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// Search
$modversion['hasSearch'] = 0;
//$modversion['search']['file'] = 'search.php' ;
//$modversion['search']['func'] = $mydirname.'_global_search' ;

// Menu
$modversion['hasMain'] = 1 ;

// Submenu (just for mainmenu)
$modversion['sub'] = [];

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = [
//	[ 'file' => '{dirname}_main.html', 'description' => 'Main' ],
];

// Blocks
$modversion['blocks'] = [];

// Comments
$modversion['hasComments'] = 0;

if ( defined( 'LEGACY_BASE_VERSION' ) ) {
	if ( ! defined( 'XOOPSX_COREPACK_VERSION' ) && defined( '_MI_LEGACY_DETAILED_VERSION' ) && 'CorePack ' === substr( _MI_LEGACY_DETAILED_VERSION, 0, 9 ) ) {
		define( 'XOOPSX_COREPACK_VERSION', substr( _MI_LEGACY_DETAILED_VERSION, 9 ) );
	}
	$_encrypt = defined( 'XOOPSX_COREPACK_VERSION' ) ? ( version_compare( XOOPSX_COREPACK_VERSION, '20140129', '>=' ) ? 'encrypt' : 'string' ) : ( version_compare( LEGACY_BASE_VERSION, '2.2.2.3', '>' ) ? 'encrypt' : 'string' );
} else {
	$_encrypt = 'string';
}
$_group_multi = ( ( defined( '_MI_LEGACY_DETAILED_VERSION' ) && version_compare( _MI_LEGACY_DETAILED_VERSION, 'CorePack 20120825', '>=' ) ) ? 'group_checkbox' : 'group_multi' );
// Configs
$modversion['config'] = [
	[
		'name'        => 'manager_title',
		'title'       => $constpref . '_MANAGER_TITLE',
		'description' => $constpref . '_MANAGER_TITLE_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'File Manager - X-elFinder'
	],
	[
		'name'        => 'volume_setting',
		'title'       => $constpref . '_VOLUME_SETTING',
		'description' => $constpref . '_VOLUME_SETTING_DESC',
		'formtype'    => 'textarea',
		'valuetype'   => 'string',
		'default'     => $mydirname . ':xelfinder_db:uploads/xelfinder:' . constant( $constpref . '_SHARE_FOLDER' ) . '
' . $mydirname . ':xelfinder:uploads/elfinder:elFinder:gid=1
#xelfinder:xelfinder:/:html:gid=1|chmod=1
#xelfinder:xelfinder:[trust]/:xoops_trust_path:gid=1|chmod=1
#xelfinder:xelfinder:[trust]/cache:TrustCache:gid=1
#xelfinder:xelfinder:preload:Preload:gid=1'
	],
	[
		'name'        => 'disabled_cmds_by_gids',
		'title'       => $constpref . '_DISABLED_CMDS_BY_GID',
		'description' => $constpref . '_DISABLED_CMDS_BY_GID_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '3=mkdir,paste,archive,extract'
	],
	[
		'name'        => 'disable_writes_guest',
		'title'       => $constpref . '_DISABLE_WRITES_GUEST',
		'description' => $constpref . '_DISABLE_WRITES_GUEST_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '1'
	],
	[
		'name'        => 'disable_writes_user',
		'title'       => $constpref . '_DISABLE_WRITES_USER',
		'description' => $constpref . '_DISABLE_WRITES_USER_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '0'
	],
	[
		'name'        => 'enable_imagemagick_ps',
		'title'       => $constpref . '_ENABLE_IMAGICK_PS',
		'description' => $constpref . '_ENABLE_IMAGICK_PS_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '0'
	],
	[
		'name'        => 'use_sharecad_preview',
		'title'       => $constpref . '_USE_SHARECAD_PREVIEW',
		'description' => $constpref . '_USE_SHARECAD_PREVIEW_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '1'
	],
	[
		'name'        => 'use_google_preview',
		'title'       => $constpref . '_USE_GOOGLE_PREVIEW',
		'description' => $constpref . '_USE_GOOGLE_PREVIEW_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '1'
	],
	[
		'name'        => 'use_office_preview',
		'title'       => $constpref . '_USE_OFFICE_PREVIEW',
		'description' => $constpref . '_USE_OFFICE_PREVIEW_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '1'
	],
	[
		'name'        => 'mail_notify_guest',
		'title'       => $constpref . '_MAIL_NOTIFY_GUEST',
		'description' => $constpref . '_MAIL_NOTIFY_GUEST_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '0'
	],
	[
		'name'        => 'mail_notify_group',
		'title'       => $constpref . '_MAIL_NOTIFY_GROUP',
		'description' => $constpref . '_MAIL_NOTIFY_GROUP_DESC',
		'formtype'    => $_group_multi,
		'valuetype'   => 'array',
		'default'     => ''
	],
	[
		'name'        => 'ftp_name',
		'title'       => $constpref . '_FTP_NAME',
		'description' => $constpref . '_FTP_NAME_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => 'Local-FTP'
	],
	[
		'name'        => 'ftp_host',
		'title'       => $constpref . '_FTP_HOST',
		'description' => $constpref . '_FTP_HOST_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => 'localhost'
	],
	[
		'name'        => 'ftp_port',
		'title'       => $constpref . '_FTP_PORT',
		'description' => $constpref . '_FTP_PORT_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => '21'
	],
	[
		'name'        => 'ftp_path',
		'title'       => $constpref . '_FTP_PATH',
		'description' => $constpref . '_FTP_PATH_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => XOOPS_ROOT_PATH
	],
	[
		'name'        => 'ftp_user',
		'title'       => $constpref . '_FTP_USER',
		'description' => $constpref . '_FTP_USER_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'ftp_pass',
		'title'       => $constpref . '_FTP_PASS',
		'description' => $constpref . '_FTP_PASS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'ftp_search',
		'title'       => $constpref . '_FTP_SEARCH',
		'description' => $constpref . '_FTP_SEARCH_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => '0'
	],
	[
		'name'        => 'boxapi_id',
		'title'       => $constpref . '_BOXAPI_ID',
		'description' => $constpref . '_BOXAPI_ID_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'boxapi_secret',
		'title'       => $constpref . '_BOXAPI_SECRET',
		'description' => $constpref . '_BOXAPI_SECRET_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'googleapi_id',
		'title'       => $constpref . '_GOOGLEAPI_ID',
		'description' => $constpref . '_GOOGLEAPI_ID_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'googleapi_secret',
		'title'       => $constpref . '_GOOGLEAPI_SECRET',
		'description' => $constpref . '_GOOGLEAPI_SECRET_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'onedriveapi_id',
		'title'       => $constpref . '_ONEDRIVEAPI_ID',
		'description' => $constpref . '_ONEDRIVEAPI_ID_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'onedriveapi_secret',
		'title'       => $constpref . '_ONEDRIVEAPI_SECRET',
		'description' => $constpref . '_ONEDRIVEAPI_SECRET_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'dropbox_token',
		'title'       => $constpref . '_DROPBOX_TOKEN',
		'description' => $constpref . '_DROPBOX_TOKEN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'dropbox_seckey',
		'title'       => $constpref . '_DROPBOX_SECKEY',
		'description' => $constpref . '_DROPBOX_SECKEY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'dropbox_acc_token',
		'title'       => $constpref . '_DROPBOX_ACC_TOKEN',
		'description' => $constpref . '_DROPBOX_ACC_TOKEN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'dropbox_acc_seckey',
		'title'       => $constpref . '_DROPBOX_ACC_SECKEY',
		'description' => $constpref . '_DROPBOX_ACC_SECKEY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'dropbox_name',
		'title'       => $constpref . '_DROPBOX_NAME',
		'description' => $constpref . '_DROPBOX_NAME_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'Dropbox'
	],
	[
		'name'        => 'dropbox_path',
		'title'       => $constpref . '_DROPBOX_PATH',
		'description' => $constpref . '_DROPBOX_PATH_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '/'
	],
	[
		'name'        => 'dropbox_hidden_ext',
		'title'       => $constpref . '_DROPBOX_HIDDEN_EXT',
		'description' => $constpref . '_DROPBOX_HIDDEN_EXT_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'dropbox_writable_groups',
		'title'       => $constpref . '_DROPBOX_WRITABLE_GROUPS',
		'description' => $constpref . '_DROPBOX_WRITABLE_GROUPS_DESC',
		'formtype'    => $_group_multi,
		'valuetype'   => 'array',
		'default'     => ''
	],
	[
		'name'        => 'dropbox_upload_mime',
		'title'       => $constpref . '_DROPBOX_UPLOAD_MIME',
		'description' => $constpref . '_DROPBOX_UPLOAD_MIME_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'image,text/plain'
	],
	[
		'name'        => 'dropbox_write_ext',
		'title'       => $constpref . '_DROPBOX_WRITE_EXT',
		'description' => $constpref . '_DROPBOX_WRITE_EXT_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '/,.jpeg,.jpg,.gif,.png,.txt'
	],
	[
		'name'        => 'dropbox_unlock_ext',
		'title'       => $constpref . '_DROPBOX_UNLOCK_EXT',
		'description' => $constpref . '_DROPBOX_UNLOCK_EXT_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'jquery',
		'title'       => $constpref . '_JQUERY',
		'description' => $constpref . '_JQUERY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => XOOPS_URL . '/common/js/jquery.min.js'
	],
	[
		'name'        => 'jquery_ui',
		'title'       => $constpref . '_JQUERY_UI',
		'description' => $constpref . '_JQUERY_UI_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => XOOPS_URL . '/common/js/jquery-ui.min.js'
	],
	[
		'name'        => 'jquery_ui_css',
		'title'       => $constpref . '_JQUERY_UI_CSS',
		'description' => $constpref . '_JQUERY_UI_CSS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => XOOPS_URL . '/common/js/jquery-ui.min.css'
	],
	[
		'name'        => 'jquery_ui_theme',
		'title'       => $constpref . '_JQUERY_UI_THEME',
		'description' => $constpref . '_JQUERY_UI_THEME_DESC',
		'formtype'    => 'select',
		'valuetype'   => 'string',
		'default'     => 'dark-hive',
		'options'     => [
			'black-tie'    => 'black-tie',
			'blitzer'      => 'blitzer',
			'cupertino'    => 'cupertino',
			'dark-hive'    => 'dark-hive',
			'dot-luv'      => 'dot-luv',
			'eggplant'     => 'eggplant',
			'excite-bike'  => 'excite-bike',
			'flick'        => 'flick',
			'hot-sneaks'   => 'hot-sneaks',
			'humanity'     => 'humanity',
			'le-frog'      => 'le-frog',
			'mint-choc'    => 'mint-choc',
			'overcast'     => 'overcast',
			'pepper-grinder',
			'redmond'      => 'redmond',
			'smoothness'   => 'smoothness',
			'south-street' => 'south-street',
			'start'        => 'start',
			'sunny'        => 'sunny',
			'swanky-purse' => 'swanky-purse',
			'trontastic'   => 'trontastic',
			'ui-darkness'  => 'ui-darkness',
			'ui-lightness' => 'ui-lightness',
			'vader'        => 'vader'
		]
	],
	[
		'name'        => 'gmaps_apikey',
		'title'       => $constpref . '_GMAPS_APIKEY',
		'description' => $constpref . '_GMAPS_APIKEY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'zoho_apikey',
		'title'       => $constpref . '_ZOHO_APIKEY',
		'description' => $constpref . '_ZOHO_APIKEY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => $_encrypt,
		'default'     => ''
	],
	[
		'name'        => 'online_convert_apikey',
		'title'       => $constpref . '_ONLINE_CONVERT_APIKEY',
		'description' => $constpref . '_ONLINE_CONVERT_APIKEY_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'editors_js',
		'title'       => $constpref . '_EDITORS_JS',
		'description' => $constpref . '_EDITORS_JS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'ui_options_js',
		'title'       => $constpref . '_UI_OPTIONS_JS',
		'description' => $constpref . '_UI_OPTIONS_JS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'thumbnail_size',
		'title'       => $constpref . '_THUMBNAIL_SIZE',
		'description' => $constpref . '_THUMBNAIL_SIZE_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '160'
	],
	[
		'name'        => 'default_item_perm',
		'title'       => $constpref . '_DEFAULT_ITEM_PERM',
		'description' => $constpref . '_DEFAULT_ITEM_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '744'
	],
	[
		'name'        => 'use_users_dir',
		'title'       => $constpref . '_USE_USERS_DIR',
		'description' => $constpref . '_USE_USERS_DIR_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'users_dir_perm',
		'title'       => $constpref . '_USERS_DIR_PERM',
		'description' => $constpref . '_USERS_DIR_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '7cc'
	],
	[
		'name'        => 'users_dir_item_perm',
		'title'       => $constpref . '_USERS_DIR_ITEM_PERM',
		'description' => $constpref . '_USERS_DIR_ITEM_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '7cc'
	],
	[
		'name'        => 'use_guest_dir',
		'title'       => $constpref . '_USE_GUEST_DIR',
		'description' => $constpref . '_USE_GUEST_DIR_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'guest_dir_perm',
		'title'       => $constpref . '_GUEST_DIR_PERM',
		'description' => $constpref . '_GUEST_DIR_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '766'
	],
	[
		'name'        => 'guest_dir_item_perm',
		'title'       => $constpref . '_GUEST_DIR_ITEM_PERM',
		'description' => $constpref . '_GUEST_DIR_ITEM_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '777'
	],
	[
		'name'        => 'use_group_dir',
		'title'       => $constpref . '_USE_GROUP_DIR',
		'description' => $constpref . '_USE_GROUP_DIR_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'group_dir_parent',
		'title'       => $constpref . '_GROUP_DIR_PARENT',
		'description' => $constpref . '_GROUP_DIR_PARENT_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => constant( $constpref . '_GROUP_DIR_PARENT_NAME' )
	],
	[
		'name'        => 'group_dir_perm',
		'title'       => $constpref . '_GROUP_DIR_PERM',
		'description' => $constpref . '_GROUP_DIR_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '768'
	],
	[
		'name'        => 'group_dir_item_perm',
		'title'       => $constpref . '_GROUP_DIR_ITEM_PERM',
		'description' => $constpref . '_GROUP_DIR_ITEM_PERM_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '748'
	],
	[
		'name'        => 'upload_allow_admin',
		'title'       => $constpref . '_UPLOAD_ALLOW_ADMIN',
		'description' => $constpref . '_UPLOAD_ALLOW_ADMIN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'all'
	],
	[
		'name'        => 'auto_resize_admin',
		'title'       => $constpref . '_AUTO_RESIZE_ADMIN',
		'description' => $constpref . '_AUTO_RESIZE_ADMIN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'upload_max_admin',
		'title'       => $constpref . '_UPLOAD_MAX_ADMIN',
		'description' => $constpref . '_UPLOAD_MAX_ADMIN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'special_groups',
		'title'       => $constpref . '_SPECIAL_GROUPS',
		'description' => $constpref . '_SPECIAL_GROUPS_DESC',
		'formtype'    => $_group_multi,
		'valuetype'   => 'array',
		'default'     => ''
	],
	[
		'name'        => 'upload_allow_spgroups',
		'title'       => $constpref . '_UPLOAD_ALLOW_SPGROUPS',
		'description' => $constpref . '_UPLOAD_ALLOW_SPGROUPS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'all'
	],
	[
		'name'        => 'auto_resize_spgroups',
		'title'       => $constpref . '_AUTO_RESIZE_SPGROUPS',
		'description' => $constpref . '_AUTO_RESIZE_SPGROUPS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'upload_max_spgroups',
		'title'       => $constpref . '_UPLOAD_MAX_SPGROUPS',
		'description' => $constpref . '_UPLOAD_MAX_SPGROUPS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'upload_allow_user',
		'title'       => $constpref . '_UPLOAD_ALLOW_USER',
		'description' => $constpref . '_UPLOAD_ALLOW_USER_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'image text/plain'
	],
	[
		'name'        => 'auto_resize_user',
		'title'       => $constpref . '_AUTO_RESIZE_USER',
		'description' => $constpref . '_AUTO_RESIZE_USER_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '1024'
	],
	[
		'name'        => 'upload_max_user',
		'title'       => $constpref . '_UPLOAD_MAX_USER',
		'description' => $constpref . '_UPLOAD_MAX_USER_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'upload_allow_guest',
		'title'       => $constpref . '_UPLOAD_ALLOW_GUEST',
		'description' => $constpref . '_UPLOAD_ALLOW_GUEST_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'image'
	],
	[
		'name'        => 'auto_resize_guest',
		'title'       => $constpref . '_AUTO_RESIZE_GUEST',
		'description' => $constpref . '_AUTO_RESIZE_GUEST_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => '1024'
	],
	[
		'name'        => 'upload_max_guest',
		'title'       => $constpref . '_UPLOAD_MAX_GUEST',
		'description' => $constpref . '_UPLOAD_MAX_GUEST_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'disable_pathinfo',
		'title'       => $constpref . '_DISABLE_PATHINFO',
		'description' => $constpref . '_DISABLE_PATHINFO_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'edit_disable_linked',
		'title'       => $constpref . '_EDIT_DISABLE_LINKED',
		'description' => $constpref . '_EDIT_DISABLE_LINKED_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 1
	],
	[
		'name'		  => 'check_name_view' ,
		'title'		  => $constpref.'_CHECK_NAME_VIEW',
		'description' => $constpref.'_CHECK_NAME_VIEW_DESC',
		'formtype'	  => 'yesno' ,
		'valuetype'	  => 'int' ,
		'default'	  => 0
	],
	[
		'name'        => 'connector_url',
		'title'       => $constpref . '_CONNECTOR_URL',
		'description' => $constpref . '_CONNECTOR_URL_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'conn_url_is_ext',
		'title'       => $constpref . '_CONN_URL_IS_EXT',
		'description' => $constpref . '_CONN_URL_IS_EXT_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'allow_origins',
		'title'       => $constpref . '_ALLOW_ORIGINS',
		'description' => $constpref . '_ALLOW_ORIGINS_DESC',
		'formtype'    => 'textarea',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'unzip_lang_value',
		'title'       => $constpref . '_UNZIP_LANG_VALUE',
		'description' => $constpref . '_UNZIP_LANG_VALUE_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => ''
	],
	[
		'name'        => 'autosync_sec_admin',
		'title'       => $constpref . '_AUTOSYNC_SEC_ADMIN',
		'description' => $constpref . '_AUTOSYNC_SEC_ADMIN_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'autosync_sec_spgroups',
		'title'       => $constpref . '_AUTOSYNC_SEC_SPGROUPS',
		'description' => $constpref . '_AUTOSYNC_SEC_SPGROUPS_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'autosync_sec_user',
		'title'       => $constpref . '_AUTOSYNC_SEC_USER',
		'description' => $constpref . '_AUTOSYNC_SEC_USER_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'autosync_sec_guest',
		'title'       => $constpref . '_AUTOSYNC_SEC_GUEST',
		'description' => $constpref . '_AUTOSYNC_SEC_GUEST_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'int',
		'default'     => 0
	],
	[
		'name'        => 'autosync_start',
		'title'       => $constpref . '_AUTOSYNC_START',
		'description' => $constpref . '_AUTOSYNC_START_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 1
	],
	[
		'name'        => 'ffmpeg_path',
		'title'       => $constpref . '_FFMPEG_PATH',
		'description' => $constpref . '_FFMPEG_PATH_DESC',
		'formtype'    => 'textbox',
		'valuetype'   => 'string',
		'default'     => 'ffmpeg'
	],
	[
		'name'        => 'debug',
		'title'       => $constpref . '_DEBUG',
		'description' => $constpref . '_DEBUG_DESC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0
	]
]; // configs end

// Notification
$modversion['hasNotification'] = 0;
$modversion['notification']    = [];

$modversion['onInstall']   = 'oninstall.php';
$modversion['onUpdate']    = 'onupdate.php';
$modversion['onUninstall'] = 'onuninstall.php';

// keep block's options
if ( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION, 6, 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && 'modulesadmin' == $_POST['fct'] && 'update_ok' == $_POST['op'] && $_POST['dirname'] == $modversion['dirname'] ) {
	include __DIR__ . '/include/x20_keepblockoptions.inc.php';
}

