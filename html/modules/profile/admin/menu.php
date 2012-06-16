<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

//
// The menu in control panel. You must never change [cubson] chunk to get the help of cubson.
//
// $adminmenu[]['title'] = CONSTRACT;
// $adminmenu[]['link'] = URL;
// $adminmenu[]['keywords'] = CONSTRACT;
// $adminmenu[]['show'] = bool;
//
##[cubson:adminmenu]
$adminmenu[0]['title'] = _MI_PROFILE_LANG_DEFINITIONS_LIST;
$adminmenu[0]['link'] = 'admin/index.php?action=DefinitionsList';
$adminmenu[0]['keywords'] = _MI_PROFILE_KEYWORD_DEFINITIONS_LIST;
$adminmenu[0]['show'] = true;

$adminmenu[1]['title'] = _MI_PROFILE_LANG_ADD_A_NEW_DEFINITIONS;
$adminmenu[1]['link'] = 'admin/index.php?action=DefinitionsEdit';
$adminmenu[1]['keywords'] = _MI_PROFILE_KEYWORD_DEFINITIONS_CREATE;
$adminmenu[1]['show'] = true;

$adminmenu[2]['title'] = _MI_PROFILE_LANG_DOWNLOAD;
$adminmenu[2]['link'] = 'admin/index.php?action=DataDownload';
$adminmenu[2]['keywords'] = _MI_PROFILE_KEYWORD_DOWNLOAD;
$adminmenu[2]['show'] = true;

##[/cubson:adminmenu]

?>
