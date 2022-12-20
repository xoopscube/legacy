<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Authors
 * @license    GPL v2.0
 */

include_once __DIR__ . '/include/altsys_functions.php';

// language file (modinfo.php)
altsys_include_language_file( 'modinfo' );

// Manifesto
$modversion['dirname']          = 'altsys';
$modversion['name']             = _MI_ALTSYS_MODULENAME;
$modversion['version']          = '2.32';
$modversion['detailed_version'] = '2.32.1';
$modversion['description']      = _MI_ALTSYS_MODULEDESC;
$modversion['author']           = 'GIJ=CHECKMATE<br>PEAK Corp.(https://www.peak.ne.jp/)';
$modversion['credits']          = 'PEAK Corp., Gigamaster (XCL/PHP7)';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_ui_components.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = "help.html";
$modversion['official']         = 0;
$modversion['cube_style']       = true;

// Admin
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// Templates can't be touched by modulesadmin.
$modversion['templates'] = [];

// Blocks
$modversion['blocks'][1] = [
	'file'        => 'blocks.php',
	'name'        => _MI_ALTSYS_BNAME_ADMIN_MENU,
	'description' => '',
	'show_func'   => 'b_altsys_admin_menu_show',
	'edit_func'   => 'b_altsys_admin_menu_edit',
	'options'     => $mydirname,
	'template'    => '', // use "module" template instead
];

// Menu
$modversion['hasMain'] = 1;

// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Configurations
/* $modversion['config'][1] = array(
    'name'            => 'adminmenu_hack_ft' ,
    'title'            => '_MI_ALTSYS_ADMINMENU_HFT' ,
    'description'    => '_MI_ALTSYS_ADMINMENU_HFTDSC' ,
    'formtype'        => 'select' ,
    'valuetype'        => 'int' ,
    'default'        => 0 ,
    'options'        => array( '_NONE' => 0 , '_MI_ALTSYS_AMHFT_OPT_2COL' => 1 , '_MI_ALTSYS_AMHFT_OPT_NOIMG' => 2 , '_MI_ALTSYS_AMHFT_OPT_XCSTY' => 3 )
) ; */

/* $modversion['config'][] = array(
    'name'            => 'adminmenu_insert_mymenu',
    'title'            => '_MI_ALTSYS_ADMINMENU_IM',
    'description'    => '_MI_ALTSYS_ADMINMENU_IMDSC',
    'formtype'        => 'yesno',
    'valuetype'        => 'int',
    'default'        => 0,
    'options'        => array()
); */

$modversion['config'][] = [
	'name'        => 'admin_in_theme',
	'title'       => '_MI_ALTSYS_ADMIN_IN_THEME',
	'description' => '_MI_ALTSYS_ADMIN_IN_THEMEDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'xcl_default',
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'enable_force_clone',
	'title'       => '_MI_ALTSYS_ENABLEFORCECLONE',
	'description' => '_MI_ALTSYS_ENABLEFORCECLONEDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'images_dir',
	'title'       => '_MI_ALTSYS_IMAGES_DIR',
	'description' => '_MI_ALTSYS_IMAGES_DIRDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'images',
	'options'     => []
];

// Notification

$modversion['hasNotification'] = 0;

$modversion['onInstall']   = 'include/oninstall.php';
$modversion['onUpdate']    = 'include/onupdate.php';
$modversion['onUninstall'] = 'include/onuninstall.php';
