<?php
/**
 * menu.php
 * Show menu items true : false
 * Keywords for action search provided by language constant
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

//$adminmenu[10]['title']= _MI_LEGACY_NAME.' '._CPHOME; /* _MI_LEGACY_MENU_XOOPS_CONFIG; */
$adminmenu[10]['title']=_MI_LEGACY_MENU_CONFIGURATION;
$adminmenu[10]['link']= 'admin/index.php?action=PreferenceList';
$adminmenu[10]['show']=true;

$adminmenu[20]['title']=_MI_LEGACY_MENU_ACTIONSEARCH;
$adminmenu[20]['link']= 'admin/index.php?action=ActSearch';
$adminmenu[20]['show']=true;

$adminmenu[30]['title']=_MI_LEGACY_MENU_MODULELIST;
$adminmenu[30]['keywords']=_MI_LEGACY_KEYWORD_MODULELIST;
$adminmenu[30]['link']= 'admin/index.php?action=ModuleList';
$adminmenu[30]['show']=true;

$adminmenu[40]['title']=_MI_LEGACY_MENU_MODULEINSTALL;
$adminmenu[40]['keywords']=_MI_LEGACY_KEYWORD_MODULEINSTALL;
$adminmenu[40]['link']= 'admin/index.php?action=InstallList';
$adminmenu[40]['show']=false;

$adminmenu[50]['title']=_MI_LEGACY_MENU_BLOCKLIST;
$adminmenu[50]['keywords']=_MI_LEGACY_KEYWORD_BLOCKLIST;
$adminmenu[50]['link']= 'admin/index.php?action=BlockList';
$adminmenu[50]['show']=true;

$adminmenu[60]['title']=_MI_LEGACY_MENU_BLOCKINSTALL;
$adminmenu[60]['keywords']=_MI_LEGACY_KEYWORD_BLOCKINSTALL;
$adminmenu[60]['link']= 'admin/index.php?action=BlockInstallList';
$adminmenu[60]['show']=false;

$adminmenu[70]['title']=_MI_LEGACY_MENU_CREATE_SMILES;
$adminmenu[70]['link']= 'admin/index.php?action=SmilesEdit';
$adminmenu[70]['keywords']=_MI_LEGACY_KEYWORD_CREATE_SMILES;
$adminmenu[70]['show']=false;

$adminmenu[80]['title']=_MI_LEGACY_MENU_SMILES_MANAGE;
$adminmenu[80]['keywords']=_MI_LEGACY_KEYWORD_SMILES_MANAGE;
$adminmenu[80]['link']= 'admin/index.php?action=SmilesList';
$adminmenu[80]['show']=true;

$adminmenu[90]['title']=_MI_LEGACY_MENU_COMMENT_MANAGE;
$adminmenu[90]['link']= 'admin/index.php?action=CommentList';
$adminmenu[90]['show']=true;

$adminmenu[100]['title']=_MI_LEGACY_MENU_IMAGE_MANAGE;
$adminmenu[100]['link']= 'admin/index.php?action=ImagecategoryList';
$adminmenu[100]['show']=true;

$adminmenu[110]['title']=_MI_LEGACY_MENU_THEME_MANAGE;
$adminmenu[110]['link']= 'admin/index.php?action=ThemeList';
$adminmenu[110]['show']=true;

//$adminmenu[120]['title']=_MI_LEGACY_MENU_GENERAL_SETTINGS;
$adminmenu[120]['title']=_MI_LEGACY_MENU_PREFERENCE;
$adminmenu[120]['link']= 'admin/index.php?action=PreferenceEdit&confcat_id=1';
$adminmenu[120]['show']=true;
