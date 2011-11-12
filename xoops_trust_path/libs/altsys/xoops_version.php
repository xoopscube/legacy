<?php

include_once dirname(__FILE__).'/include/altsys_functions.php' ;

// language file (modinfo.php)
altsys_include_language_file( 'modinfo' ) ;

$modversion['name'] = _MI_ALTSYS_MODULENAME ;
$modversion['version'] = '0.71' ;
$modversion['description'] = _MI_ALTSYS_MODULEDESC ;
$modversion['credits'] = "PEAK Corp.";
$modversion['author'] = "GIJ=CHECKMATE<br />PEAK Corp.(http://www.peak.ne.jp/)" ;
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
$modversion['image'] = "altsys_slogo.png";
$modversion['dirname'] = "altsys";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php" ;
$modversion['adminmenu'] = "admin/admin_menu.php";

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'][1] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> _MI_ALTSYS_BNAME_ADMIN_MENU ,
	'description'	=> '' ,
	'show_func'		=> 'b_altsys_admin_menu_show' ,
	'edit_func'		=> 'b_altsys_admin_menu_edit' ,
	'options'		=> "$mydirname" ,
	'template'		=> '' , // use "module" template instead
) ;

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Configurations
$modversion['config'][1] = array(
	'name'			=> 'adminmenu_hack_ft' ,
	'title'			=> '_MI_ALTSYS_ADMINMENU_HFT' ,
	'description'	=> '_MI_ALTSYS_ADMINMENU_HFTDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array( '_NONE' => 0 , '_MI_ALTSYS_AMHFT_OPT_2COL' => 1 , '_MI_ALTSYS_AMHFT_OPT_NOIMG' => 2 , '_MI_ALTSYS_AMHFT_OPT_XCSTY' => 3 )
) ;

$modversion['config'][] = array(
	'name'			=> 'adminmenu_insert_mymenu' ,
	'title'			=> '_MI_ALTSYS_ADMINMENU_IM' ,
	'description'	=> '_MI_ALTSYS_ADMINMENU_IMDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'admin_in_theme' ,
	'title'			=> '_MI_ALTSYS_ADMIN_IN_THEME' ,
	'description'	=> '_MI_ALTSYS_ADMIN_IN_THEMEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'default' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'enable_force_clone' ,
	'title'			=> '_MI_ALTSYS_ENABLEFORCECLONE' ,
	'description'	=> '_MI_ALTSYS_ENABLEFORCECLONEDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'images_dir' ,
	'title'			=> '_MI_ALTSYS_IMAGES_DIR' ,
	'description'	=> '_MI_ALTSYS_IMAGES_DIRDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'images' ,
	'options'		=> array()
) ;

// Notification

$modversion['hasNotification'] = 0;

$modversion['onInstall'] = 'include/oninstall.php' ;
$modversion['onUpdate'] = 'include/onupdate.php' ;
$modversion['onUninstall'] = 'include/onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

?>