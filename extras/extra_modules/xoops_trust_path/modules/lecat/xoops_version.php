<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

if(!defined('LECAT_TRUST_PATH'))
{
	define('LECAT_TRUST_PATH',XOOPS_TRUST_PATH . '/modules/lecat');
}

require_once LECAT_TRUST_PATH . '/class/LecatUtils.class.php';

//
// Define a basic manifesto.
//
$modversion['name'] = $myDirName;
$modversion['version'] = 2.01;
$modversion['description'] = _MI_LECAT_DESC_LECAT;
$modversion['author'] = _MI_LECAT_LANG_AUTHOR;
$modversion['credits'] = _MI_LECAT_LANG_CREDITS;
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL';
$modversion['official'] = 0;
$modversion['image'] = 'images/module_icon.png';
$modversion['dirname'] = $myDirName;
$modversion['trust_dirname'] = 'lecat';
$modversion['role'] = 'cat';

$modversion['cube_style'] = true;
$modversion['legacy_installer'] = array(
	'installer'   => array(
		'class' 	=> 'Installer',
		'namespace' => 'Lecat',
		'filepath'	=> LECAT_TRUST_PATH . '/admin/class/installer/LecatInstaller.class.php'
	),
	'uninstaller' => array(
		'class' 	=> 'Uninstaller',
		'namespace' => 'Lecat',
		'filepath'	=> LECAT_TRUST_PATH . '/admin/class/installer/LecatUninstaller.class.php'
	),
	'updater' => array(
		'class' 	=> 'Updater',
		'namespace' => 'Lecat',
		'filepath'	=> LECAT_TRUST_PATH . '/admin/class/installer/LecatUpdater.class.php'
	)
);
$modversion['disable_legacy_2nd_installer'] = false;

$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = array(
//	  '{prefix}_{dirname}_xxxx',
##[cubson:tables]
	'{prefix}_{dirname}_cat',
	'{prefix}_{dirname}_permit',
##[/cubson:tables]
);

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
$modversion['templates'] = array(
/*
	array(
		'file'		  => '{dirname}_xxx.html',
		'description' => _MI_LECAT_TPL_XXX
	),
*/
##[cubson:templates]
	array('file' => '{dirname}_cat_list.html','description' => _MI_LECAT_TPL_CAT_LIST),
	array('file' => '{dirname}_cat_edit.html','description' => _MI_LECAT_TPL_CAT_EDIT),
	array('file' => '{dirname}_cat_delete.html','description' => _MI_LECAT_TPL_CAT_DELETE),
	array('file' => '{dirname}_cat_view.html','description' => _MI_LECAT_TPL_CAT_VIEW),
	array('file' => '{dirname}_default_set.html','description' => _MI_LECAT_TPL_DEFAULT_SET),
	array('file' => '{dirname}_actor_edit.html','description' => _MI_LECAT_TPL_ACTOR_EDIT),
	array('file' => '{dirname}_inc_menu.html','description' => 'module menu'),
##[/cubson:templates]
);

//
// Admin panel setting
//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php?action=Index';
$modversion['adminmenu'] = array(
/*
	array(
		'title'    => _MI_LECAT_LANG_SETTING_EDIT,
		'link'	   => 'admin/index.php?action=SettingEdit',
		'keywords' => _MI_LECAT_KEYWORD_SETTING_EDIT,
		'show'	   => true,
		'absolute' => false
	),
*/
##[cubson:adminmenu]
##[/cubson:adminmenu]
);

//
// Public side control setting
//
$modversion['hasMain'] = 1;
$modversion['hasSearch'] = 0;
$modversion['sub'] = array(
/*
	array(
		'name' => _MI_LECAT_LANG_SUB_XXX,
		'url'  => 'index.php?action=XXX'
	),
*/
##[cubson:submenu]
##[/cubson:submenu]
);

//
// Config setting
//
$modversion['config'] = array(
	array(
		'name'			=> 'css_file' ,
		'title' 		=> "_MI_LECAT_LANG_CSS_FILE" ,
		'description'	=> "_MI_LECAT_DESC_CSS_FILE" ,
		'formtype'		=> 'textbox' ,
		'valuetype' 	=> 'text' ,
		'default'		=> '/modules/'.$myDirName.'/style.css',
		'options'		=> array()
	) ,
	array(
		'name'			=> 'maxdepth' ,
		'title' 		=> "_MI_LECAT_LANG_MAXDEPTH" ,
		'description'	=> "_MI_LECAT_DESC_MAXDEPTH" ,
		'formtype'		=> 'textbox' ,
		'valuetype' 	=> 'int' ,
		'default'		=> '0',
		'options'		=> array()
	) ,
/*
	array(
		'name'			=> 'xxxx',
		'title' 		=> '_MI_LECAT_TITLE_XXXX',
		'description'	=> '_MI_LECAT_DESC_XXXX',
		'formtype'		=> 'xxxx',
		'valuetype' 	=> 'xxx',
		'options'		=> array(xxx => xxx,xxx => xxx),
		'default'		=> 0
	),
*/
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
		'name'				=> _MI_LECAT_BLOCK_NAME_xxx,
		'description'		=> _MI_LECAT_BLOCK_DESC_xxx,
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
