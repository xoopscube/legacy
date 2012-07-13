<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

if(!defined('XUPDATE_TRUST_PATH'))
{
	define('XUPDATE_TRUST_PATH',XOOPS_TRUST_PATH . '/modules/xupdate');
}

require_once XUPDATE_TRUST_PATH . '/class/XupdateUtils.class.php';

$dirname  = dirname(__FILE__);
$basename = basename($dirname);
//
// Define a basic manifesto.
//
$modversion['name'] = _MI_XUPDATE_LANG_XUPDATE;
$modversion['version'] = '0.08';//alpha3
$modversion['description'] = _MI_XUPDATE_DESC_XUPDATE;
$modversion['author'] = _MI_XUPDATE_LANG_AUTHOR;
$modversion['credits'] = _MI_XUPDATE_LANG_CREDITS;
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'module_icon.php';
$modversion['dirname'] = $myDirName;
$modversion['trust_dirname'] = $basename;

$modversion['cube_style'] = true;
$modversion['legacy_installer'] = array(
	'installer'   => array(
		'class' 	=> 'Installer',
		'namespace' => 'Xupdate',
		'filepath'	=> XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateInstaller.class.php'
	),
	'uninstaller' => array(
		'class' 	=> 'Uninstaller',
		'namespace' => 'Xupdate',
		'filepath'	=> XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateUninstaller.class.php'
	),
	'updater' => array(
		'class' 	=> 'Updater',
		'namespace' => 'Xupdate',
		'filepath'	=> XUPDATE_TRUST_PATH . '/admin/class/installer/XupdateUpdater.class.php'
	)
);
$modversion['disable_legacy_2nd_installer'] = false;

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = array(
//	  '{prefix}_{dirname}_xxxx',
##[cubson:tables]
	'{prefix}_{dirname}_store',
	'{prefix}_{dirname}_modulestore',
##[/cubson:tables]
);

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
$modversion['templates'] = array(
/*
	array(
		'file'		  => '{dirname}_xxx.html',
		'description' => _MI_XUPDATE_TPL_XXX
	),
*/
##[cubson:templates]
		//array('file' => '{dirname}_admin_storeview.html','admin' => 'adminmenu'),
		array('file' => '{dirname}_store_delete.html','description' => _MI_XUPDATE_TPL_STORE_DELETE),
		array('file' => '{dirname}_store_edit.html','description' => _MI_XUPDATE_TPL_STORE_EDIT),
		array('file' => '{dirname}_store_list.html','description' => _MI_XUPDATE_TPL_STORE_LIST),
		array('file' => '{dirname}_store_view.html','description' => _MI_XUPDATE_TPL_STORE_VIEW),
##[/cubson:templates]
);

//
// Admin panel setting
//

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php?action=ModuleView';

##[cubson:adminmenu]
$modversion['adminmenu'] = array(
	array(
			'title'		=> _MI_XUPDATE_ADMENU_STORELIST,
			'link'	=> 'admin/index.php?action=ModuleView',
			'keywords'	=> _MI_XUPDATE_ADMENU_STORELIST,
			'show'	=> true,
			'absolute' => false
	),
	array(
		'title'		=> _MI_XUPDATE_ADMENU_MODULE,
		'link'	=> 'admin/index.php?action=ModuleStore',
		'keywords'	=> _MI_XUPDATE_ADMENU_MODULE,
		'show'	=> true,
		'absolute' => false
	),
	array(
		'title'		=> _MI_XUPDATE_ADMENU_THEME,
		//'link'	=> 'admin/index.php?action=ThemeStore',
		'link'	=> 'admin/index.php?action=ThemeStore',
		'keywords'	=> _MI_XUPDATE_ADMENU_THEME,
		'show'	=> true,
		'absolute' => false
	),
	array(
		'title'		=> _MI_XUPDATE_ADMENU_THEMEFINDER,
		'link'	=> 'admin/index.php?action=ThemeFinder',
		'keywords'	=> _MI_XUPDATE_ADMENU_THEMEFINDER,
		'show'	=> true,
		'absolute' => false
	)
	);
//
// Public side control setting
//
$modversion['hasMain'] = 0;
$modversion['hasSearch'] = 0;
$modversion['sub'] = array(
/*
	array(
		'name' => _MI_XUPDATE_LANG_SUB_XXX,
		'url'  => 'index.php?action=XXX'
	),
*/
##[cubson:submenu]
##[/cubson:submenu]
);

##[/cubson:adminmenu]

//
// Config setting
//

$modversion['config'] = array(
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

	array(
		'name'		=> 'temp_path' ,
		'title'		=> '_MI_XUPDATE_TEMP_PATH',
		'description'	=> '_MI_XUPDATE_TEMP_PATHDSC',
		'formtype'	=> 'text',
		'valuetype'	=> 'string',
		'default'	=> 'uploads/xupdate',
		'options'	=> array(),
	) ,

	array(
		'name'		=> 'ftp_method' ,
		'title'		=> '_MI_XUPDATE_FTP_METHOD',
		'description'	=> '_MI_XUPDATE_FTP_METHODDSC',
		'formtype'	=> 'select',
		'valuetype'	=> 'int',
		'default'	=> '0',
		'options'	=> array( '_MI_XUPDATE_CUSTOM_FTP' => 0,
						'_MI_XUPDATE_PHP_FTP' => 1,
						'_MI_XUPDATE_CUSTOM_SFTP' => 2,
						'_MI_XUPDATE_CUSTOM_SSH2' => 3
						)
	) ,

	array(
		'name'		=> 'FTP_SSL',
		'title'		=> '_MI_XUPDATE_FTP_USESSL' ,
		'description'	=> '_MI_XUPDATE_FTP_USESSLDSC' ,
		'formtype'	=> 'yesno' ,
		'valuetype'	=> 'int' ,
		'default'	=> 0 ,
		'options'	=> array()
	) ,

	array(
		'name'		=> 'FTP_UserName' ,
		'title'		=> '_MI_XUPDATE_FTP_UNAME',
		'description'	=> '_MI_XUPDATE_FTP_UNAMEDSC',
		'formtype'	=> 'text',
		'valuetype'	=> 'string',
		'default'	=> '',
		'options'	=> array(),
	) ,

	array(
		'name'		=> 'FTP_password',
		'title'		=> '_MI_XUPDATE_FTP_PASS' ,
		'description'		=> '_MI_XUPDATE_FTP_PASSDSC' ,
		'formtype'	=> 'password',
		'valuetype'	=> 'string',
		'default'	=> '',
		'options'	=> array(),
	) ,

	array(
		'name'		=> 'SSH_port' ,
		'title'		=> '_MI_XUPDATE_SSH_PORT',
		'description'	=> '_MI_XUPDATE_SSH_PORTDSC',
		'formtype'	=> 'text',
		'valuetype'	=> 'string',
		'default'	=> '22',
		'options'	=> array(),
	) ,

	array(
		'name'		=> 'SSH_key' ,
		'title'		=> '_MI_XUPDATE_SSH_KEY',
		'description'	=> '_MI_XUPDATE_SSH_KEYDSC',
		'formtype'	=> 'textarea',
		'valuetype'	=> 'text',
		'default'	=> '',
		'options'	=> array(),
	) ,

	array(
		'name'		=> 'Show_debug',
		'title'		=> '_MI_XUPDATE_DEBUG' ,
		'description'	=> '',
		'formtype'	=> 'yesno',
		'valuetype'	=> 'int',
		'default'	=> 0 ,
		'options'	=> array(),
	) ,
	array(
		'name'		=> 'Theme_download_Url_format',
		'title'		=> '_MI_XUPDATE_FTP_THEME_URL' ,
		'description'	=> '',
		'formtype'	=> 'text',
		'valuetype'	=> 'string',
		'default'	=> 'http://cmsthemefinder.com/modules/lica/index.php?controller=download&id=%u',
		'options'	=> array(),
	) ,

##[cubson:config]
##[/cubson:config]
);

//
// Block setting
//
$modversion['blocks'] = array(
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
##[cubson:block]
##[/cubson:block]
);

?>
