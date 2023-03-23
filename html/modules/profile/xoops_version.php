<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Other Authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$mydirpath = basename(__DIR__) ;


// Manifesto
$modversion['dirname']          = 'profile';
$modversion['name']             = _MI_PROFILE_LANG_PROFILE;
$modversion['version']          = '2.33';
$modversion['detailed_version'] = '2.33.0';
$modversion['description']      = _MI_PROFILE_DESC_PROFILE;
$modversion['author']           = 'Kilica. Gigamaster (XCL23/PHP7';
$modversion['credits']          = 'Kilica, The XOOPSCube Project';
$modversion['license']          = 'GPL';
$modversion['image']            = 'images/module_profile.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = true;

$modversion['disable_legacy_2nd_installer'] = false;

// SQL
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';

$modversion['tables'][0] = '{prefix}_profile_definitions';
$modversion['tables'][1] = '{prefix}_profile_data';

##[cubson:templates]
$modversion['templates'][1]['file'] = 'profile_data_edit.html';
$modversion['templates'][2]['file'] = 'profile_data_delete.html';
$modversion['templates'][3]['file'] = 'profile_data_view.html';
$modversion['templates'][4]['file'] = 'profile_data_list.html';
$modversion['templates'][5]['file'] = 'profile_inc_data_edit.html';
$modversion['templates'][6]['file'] = 'profile_inc_data_view.html';
##[/cubson:templates]

// Admin panel setting
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php?action=DefinitionsList';
$modversion['adminmenu']  = 'admin/menu.php';

// Public side control setting
$modversion['hasMain'] = 0;
// $modversion['sub'][]['name'] = "";
// $modversion['sub'][]['url'] = "";
