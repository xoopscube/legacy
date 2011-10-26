<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

$mydirpath = basename( dirname( __FILE__ ) ) ;

//
// Define a basic manifesto.
//
$modversion['name'] = _MI_PROFILE_LANG_PROFILE;
$modversion['version'] = 2.01;
$modversion['description'] = _MI_PROFILE_DESC_PROFILE;
$modversion['author'] = "kilica";
$modversion['credits'] = "";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL";
$modversion['official'] = 0;
$modversion['image']	   = 'images/module_icon.png';
$modversion['dirname'] = "profile";

$modversion['cube_style'] = true;
$modversion['disable_legacy_2nd_installer'] = false;

// TODO After you made your SQL, remove the following comment-out.
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
##[cubson:tables]
$modversion['tables'][0] = "{prefix}_profile_definitions";
$modversion['tables'][1] = "{prefix}_profile_data";
##[/cubson:tables]

//
// Templates. You must never change [cubson] chunk to get the help of cubson.
//
//$modversion['templates'][]['file'] = 'profile'_xxxxx.html';
//$modversion['templates'][]['description'] = 'profile'_xxxxx.html';
##[cubson:templates]
$modversion['templates'][1]['file'] = 'profile_data_edit.html';
$modversion['templates'][2]['file'] = 'profile_data_delete.html';
$modversion['templates'][3]['file'] = 'profile_data_view.html';
$modversion['templates'][4]['file'] = 'profile_data_list.html';
$modversion['templates'][5]['file'] = 'profile_inc_data_edit.html';
$modversion['templates'][6]['file'] = 'profile_inc_data_view.html';
##[/cubson:templates]

//
// Admin panel setting
//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php?action=DefinitionsList";
$modversion['adminmenu'] = "admin/menu.php";

//
// Public side control setting
//
$modversion['hasMain'] = 0;
// $modversion['sub'][]['name'] = "";
// $modversion['sub'][]['url'] = "";

?>
