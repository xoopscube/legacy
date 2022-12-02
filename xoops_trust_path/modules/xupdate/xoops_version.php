<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Other authors Gigamaster XCL/PHP7
 * @author Naoki Sawada, Naoki Okino
 * @copyright  (c) 2005-2022 Authors
 * @license GPL V2
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

if ( ! defined( 'XUPDATE_TRUST_PATH' ) ) {
	define( 'XUPDATE_TRUST_PATH', XOOPS_TRUST_PATH . '/modules/xupdate' );
}

require_once XUPDATE_TRUST_PATH . '/class/XupdateUtils.class.php';

$dirname  = __DIR__;
$basename = basename( $dirname );

// Manifesto
$modversion['dirname']          = $myDirName;
$modversion['trust_dirname']    = $basename;
$modversion['name']             = _MI_XUPDATE_LANG_XUPDATE;
$modversion['version']          = '2.32';
$modversion['detailed_version'] = '2.32.0';
$modversion['description']      = _MI_XUPDATE_DESC_XUPDATE;
$modversion['author']           = _MI_XUPDATE_LANG_AUTHOR;
$modversion['credits']          = _MI_XUPDATE_LANG_CREDITS;
$modversion['cube_style']       = true;
$modversion['help']             = 'help.html';
$modversion['license']          = 'GPL';
$modversion['official']         = 0;
// $modversion['image'] = 'module_icon.php';
$modversion['image']            = 'images/module_xupdate.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['cube_style']       = true;

// SQL Install
$modversion['legacy_installer']             = [
	'installer'   => [
		'class'     => 'Installer',
		'namespace' => 'Xupdate',
		'filepath'  => XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateInstaller.class.php'
	],
	'uninstaller' => [
		'class'     => 'Uninstaller',
		'namespace' => 'Xupdate',
		'filepath'  => XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateUninstaller.class.php'
	],
	'updater'     => [
		'class'     => 'Updater',
		'namespace' => 'Xupdate',
		'filepath'  => XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateUpdater.class.php'
	]
];
$modversion['disable_legacy_2nd_installer'] = false;

// SQL
$modversion['sqlfile']['mysql']     = 'sql/mysql.sql';
$modversion['sqlfile']['pdo_pgsql'] = 'sql/pdo_pgsql.sql';
$modversion['tables']               = [
	//	  '{prefix}_{dirname}_xxxx',
	##[cubson:tables]
	'{prefix}_{dirname}_store',
	'{prefix}_{dirname}_modulestore',
	##[/cubson:tables]
];

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
$modversion['templates'] = [
	/*
	array(
		'file'		  => '{dirname}_xxx.html',
		'description' => _MI_XUPDATE_TPL_XXX
	),
*/
	##[cubson:templates]
	//array('file' => '{dirname}_admin_storeview.html','admin' => 'adminmenu'),
	[ 'file' => '{dirname}_modulestore_inc.html', 'description' => _MI_XUPDATE_TPL_MODULESTORE_INC ],
	##[/cubson:templates]
];

//
// Admin panel setting
//

$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php?action=ModuleView';

##[cubson:adminmenu]
$modversion['adminmenu'] = [
	[
		'title'    => _MI_XUPDATE_ADMENU_STORELIST,
		'link'     => 'admin/index.php?action=ModuleView',
		'keywords' => _MI_XUPDATE_ADMENU_STORELIST,
		'show'     => true,
		'absolute' => false
	],
	//	array(
	//		'title'		=> _MI_XUPDATE_ADMENU_PACKAGE,
	//		'link'	=> 'admin/index.php?action=PackageStore',
	//		'keywords'	=> _MI_XUPDATE_ADMENU_PACKAGE,
	//		'show'	=> true,
	//		'absolute' => false
	//	),
	[
		'title'    => _MI_XUPDATE_ADMENU_MODULE,
		'link'     => 'admin/index.php?action=ModuleStore',
		'keywords' => _MI_XUPDATE_ADMENU_MODULE,
		'show'     => true,
		'absolute' => false
	],
	[
		'title'    => _MI_XUPDATE_ADMENU_THEME,
		//'link'	=> 'admin/index.php?action=ThemeStore',
		'link'     => 'admin/index.php?action=ThemeStore',
		'keywords' => _MI_XUPDATE_ADMENU_THEME,
		'show'     => true,
		'absolute' => false
	],
	[
		'title'    => _MI_XUPDATE_ADMENU_THEMEFINDER,
		'link'     => 'admin/index.php?action=ThemeFinder',
		'keywords' => _MI_XUPDATE_ADMENU_THEMEFINDER,
		'show'     => true,
		'absolute' => false
	],
	[
		'title'    => _MI_XUPDATE_ADMENU_PRELOAD,
		'link'     => 'admin/index.php?action=PreloadStore',
		'keywords' => _MI_XUPDATE_ADMENU_PRELOAD,
		'show'     => true,
		'absolute' => false
	]
];
//
// Public side control setting
//
$modversion['hasMain']   = 0;
$modversion['hasSearch'] = 0;
$modversion['sub']       = [
	/*
	array(
		'name' => _MI_XUPDATE_LANG_SUB_XXX,
		'url'  => 'index.php?action=XXX'
	),
*/
	##[cubson:submenu]
	##[/cubson:submenu]
];

##[/cubson:adminmenu]

//
// Config setting
//
if ( ! defined( 'XOOPSX_COREPACK_VERSION' ) && defined( '_MI_LEGACY_DETAILED_VERSION' ) && 'CorePack ' === substr( _MI_LEGACY_DETAILED_VERSION, 0, 9 ) ) {
	define( 'XOOPSX_COREPACK_VERSION', substr( _MI_LEGACY_DETAILED_VERSION, 9 ) );
}
$_encrypt             = defined( 'XOOPSX_COREPACK_VERSION' ) ? ( version_compare( XOOPSX_COREPACK_VERSION, '20140125', '>=' ) ? 'encrypt' : 'string' ) : ( version_compare( LEGACY_BASE_VERSION, '2.2.2.3', '>' ) ? 'encrypt' : 'string' );
$modversion['config'] = [
	/*	array(
		'name'			=> 'xxxx',
		'title' 		=> '_MI_XUPDATE_TITLE_XXXX',
		'description'	=> '_MI_XUPDATE_DESC_XXXX',
		'formtype'		=> 'xxxx',
		'valuetype' 	=> 'xxx',
		'options'		=> array(xxx => xxx,xxx => xxx),
		'default'		=> 0
	),
	array(
		'name'			=> 'css_file' ,
		'title' 		=> "_MI_XUPDATE_LANG_CSS_FILE" ,
		'description'	=> "_MI_XUPDATE_DESC_CSS_FILE" ,
		'formtype'		=> 'textbox' ,
		'valuetype' 	=> 'text' ,
		'default'		=> '/modules/'.$myDirName.'/style.css',
		'options'		=> array()
	) ,
*/

	[
		'name'        => 'temp_path',
		'title'       => '_MI_XUPDATE_TEMP_PATH',
		'description' => '_MI_XUPDATE_TEMP_PATHDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => 'uploads/xupdate',
		'options'     => [],
	],

	[
		'name'        => 'ftp_method',
		'title'       => '_MI_XUPDATE_FTP_METHOD',
		'description' => '_MI_XUPDATE_FTP_METHODDSC',
		'formtype'    => defined( 'XOOPSX_COREPACK_VERSION' ) ? ( version_compare( XOOPSX_COREPACK_VERSION, '20121230', '>=' ) ? 'radio' : ( version_compare( XOOPSX_COREPACK_VERSION, '20120825', '>=' ) ? 'radio_br' : 'select' ) ) : ( version_compare( LEGACY_BASE_VERSION, '2.2.2.1', '>=' ) ? 'radio' : 'select' ),
		'valuetype'   => 'int',
		'default'     => '4',
		'options'     => [
			'_MI_XUPDATE_DIRECT'      => 4,
			'_MI_XUPDATE_CUSTOM_FTP'  => 0,
			'_MI_XUPDATE_PHP_FTP'     => 1,
			'_MI_XUPDATE_CUSTOM_SFTP' => 2,
			'_MI_XUPDATE_CUSTOM_SSH2' => 3
		]
	],

	[
		'name'        => 'FTP_SSL',
		'title'       => '_MI_XUPDATE_FTP_USESSL',
		'description' => '_MI_XUPDATE_FTP_USESSLDSC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => []
	],

	[
		'name'        => 'FTP_server',
		'title'       => '_MI_XUPDATE_FTP_SERVER',
		'description' => '_MI_XUPDATE_FTP_SERVERDSC',
		'formtype'    => 'text',
		'valuetype'   => $_encrypt,
		'default'     => '127.0.0.1',
		'options'     => [],
	],

	[
		'name'        => 'FTP_UserName',
		'title'       => '_MI_XUPDATE_FTP_UNAME',
		'description' => '_MI_XUPDATE_FTP_UNAMEDSC',
		'formtype'    => 'text',
		'valuetype'   => $_encrypt,
		'default'     => '',
		'options'     => [],
	],

	[
		'name'        => 'FTP_password',
		'title'       => '_MI_XUPDATE_FTP_PASS',
		'description' => '_MI_XUPDATE_FTP_PASSDSC',
		'formtype'    => 'password',
		'valuetype'   => $_encrypt,
		'default'     => '',
		'options'     => [],
	],

	[
		'name'        => 'SSH_port',
		'title'       => '_MI_XUPDATE_SSH_PORT',
		'description' => '_MI_XUPDATE_SSH_PORTDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '22',
		'options'     => [],
	],

	[
		'name'        => 'SSH_key',
		'title'       => '_MI_XUPDATE_SSH_KEY',
		'description' => '_MI_XUPDATE_SSH_KEYDSC',
		'formtype'    => 'textarea',
		'valuetype'   => $_encrypt,
		'default'     => '',
		'options'     => [],
	],

	[
		'name'        => 'writable_file_perm',
		'title'       => '_MI_XUPDATE_WRITABLE_FILE_PERM',
		'description' => '_MI_XUPDATE_WRITABLE_FILE_PERMDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '666',
		'options'     => [],
	],

	[
		'name'        => 'writable_dir_perm',
		'title'       => '_MI_XUPDATE_WRITABLE_DIR_PERM',
		'description' => '_MI_XUPDATE_WRITABLE_DIR_PERMDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '777',
		'options'     => [],
	],

	[
		'name'        => 'writable_file_perm_t',
		'title'       => '_MI_XUPDATE_WRITABLE_FILE_PERM_T',
		'description' => '_MI_XUPDATE_WRITABLE_FILE_PERM_TDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '666',
		'options'     => [],
	],

	[
		'name'        => 'writable_dir_perm_t',
		'title'       => '_MI_XUPDATE_WRITABLE_DIR_PERM_T',
		'description' => '_MI_XUPDATE_WRITABLE_DIR_PERM_TDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '777',
		'options'     => [],
	],

	[
		'name'        => 'php_perm',
		'title'       => '_MI_XUPDATE_PHP_PERM',
		'description' => '_MI_XUPDATE_PHP_PERMDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => '',
		'options'     => [],
	],

	[
		'name'        => 'only_conf_lang',
		'title'       => '_MI_XUPDATE_ONLY_CONF_LANG',
		'description' => '_MI_XUPDATE_ONLY_CONF_LANGDSC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => [],
	],

	[
		'name'        => 'disabled_items',
		'title'       => '_MI_XUPDATE_DISABLED_ITEMS',
		'description' => '_MI_XUPDATE_DISABLED_ITEMSDSC',
		'formtype'    => 'textarea',
		'valuetype'   => 'string',
		'default'     => '',
		'options'     => [],
	],

	[
		'name'        => 'tag_dirname',
		'title'       => '_MI_XUPDATE_TAG_DIRNAME',
		'description' => '_MI_XUPDATE_TAG_DIRNAMEDSC',
		'formtype'    => 'server_module',
		'valuetype'   => 'text',
		'default'     => '',
		'options'     => [ 'none', 'tag' ]
	],

	[
		'name'        => 'xelfinder_dirname',
		'title'       => '_MI_XUPDATE_XEL_DIRNAME',
		'description' => '_MI_XUPDATE_XEL_DIRNAMEDSC',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => 'xelfinder',
		'options'     => []
	],

	[
		'name'        => 'Show_debug',
		'title'       => '_MI_XUPDATE_DEBUG',
		'description' => '',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => [],
	],

	[
		'name'        => 'Theme_download_Url_format',
		'title'       => '_MI_XUPDATE_FTP_THEME_URL',
		'description' => '',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => 'http://cmsthemefinder.com/modules/lica/index.php?controller=download&id=%u',
		'options'     => [],
	],

	[
		'name'        => 'stores_json_url',
		'title'       => '_MI_XUPDATE_FTP_STORE_URL',
		'description' => '',
		'formtype'    => 'text',
		'valuetype'   => 'string',
		'default'     => 'https://xoopscube.net/uploads/xupdatemaster/stores_json_V1.txt',
		'options'     => [],
	],

	[
		'name'        => 'show_disabled_store',
		'title'       => '_MI_XUPDATE_SHOW_DISABLED_STORE',
		'description' => '_MI_XUPDATE_SHOW_DISABLED_STOREDSC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => [],
	],

	//parallel_fetch_max
	[
		'name'        => 'parallel_fetch_max',
		'title'       => '_MI_XUPDATE_PARALLEL_FETCH_MAX',
		'description' => '_MI_XUPDATE_PARALLEL_FETCH_MAXDSC',
		'formtype'    => 'text',
		'valuetype'   => 'int',
		'default'     => 50,
		'options'     => [],
	],

	//parallel_fetch_max
	[
		'name'        => 'curl_multi_select_not_use',
		'title'       => '_MI_XUPDATE_CURL_MULTI_SELECT',
		'description' => '_MI_XUPDATE_CURL_MULTI_SELECTDSC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => [],
	],

	[
		'name'        => 'curl_ssl_no_verify',
		'title'       => '_MI_XUPDATE_CURL_SSL_NO_VERIFY',
		'description' => '_MI_XUPDATE_CURL_SSL_NO_VERIFYDSC',
		'formtype'    => 'yesno',
		'valuetype'   => 'int',
		'default'     => 0,
		'options'     => [],
	]

	##[cubson:config]
	##[/cubson:config]
];

//
// Block setting
//
$modversion['blocks'] = [
	/*
	x => array(
		'func_num'			=> x,
		'file'				=> 'xxxBlock.class.php',
		'class' 			=> 'xxx',
		'name'				=> _MI_XUPDATE_BLOCK_NAME_xxx,
		'description'		=> _MI_XUPDATE_BLOCK_DESC_xxx,
		'options'			=> '',
		'template'			=> '{dirname}_block_xxx.html',
		'show_all_module'	=> true,
		'visible_any'		=> true
	),
*/
	1 => [
		'func_num'        => 1,
		'file'            => 'NotifyBlock.class.php',
		'class'           => 'NotifyBlock',
		'name'            => 'X-update Notify',
		'description'     => '',
		'options'         => '',
		'template'        => '',
		'show_all_module' => true,
		'can_clone'       => true,
		'visible_any'     => false
	],
	##[cubson:block]
	##[/cubson:block]
];
