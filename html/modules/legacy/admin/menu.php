<?php
/**
 *
 * @package Legacy
 * @version $Id: menu.php,v 1.3 2008/09/25 15:12:46 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

$adminmenu[10]['title']=_MI_LEGACY_MENU_ACTIONSEARCH;
$adminmenu[10]['link']="admin/index.php?action=ActSearch";
$adminmenu[10]['show']=true;

$adminmenu[20]['title']=_MI_LEGACY_MENU_MODULELIST;
$adminmenu[20]['keywords']=_MI_LEGACY_KEYWORD_MODULELIST;
$adminmenu[20]['link']="admin/index.php?action=ModuleList";
$adminmenu[20]['show']=true;

$adminmenu[30]['title']=_MI_LEGACY_MENU_MODULEINSTALL;
$adminmenu[30]['keywords']=_MI_LEGACY_KEYWORD_MODULEINSTALL;
$adminmenu[30]['link']="admin/index.php?action=InstallList";
$adminmenu[30]['show']=true;

$adminmenu[40]['title']=_MI_LEGACY_MENU_BLOCKLIST;
$adminmenu[40]['keywords']=_MI_LEGACY_KEYWORD_BLOCKLIST;
$adminmenu[40]['link']="admin/index.php?action=BlockList";
$adminmenu[40]['show']=true;

$adminmenu[50]['title']=_MI_LEGACY_MENU_BLOCKINSTALL;
$adminmenu[50]['keywords']=_MI_LEGACY_KEYWORD_BLOCKINSTALL;
$adminmenu[50]['link']="admin/index.php?action=BlockInstallList";
$adminmenu[50]['show']=true;

$adminmenu[60]['title']=_MI_LEGACY_MENU_CREATE_SMILES;
$adminmenu[60]['link']="admin/index.php?action=SmilesEdit";
$adminmenu[60]['keywords']=_MI_LEGACY_KEYWORD_CREATE_SMILES;
$adminmenu[60]['show']=false;

$adminmenu[70]['title']=_MI_LEGACY_MENU_SMILES_MANAGE;
$adminmenu[70]['keywords']=_MI_LEGACY_KEYWORD_SMILES_MANAGE;
$adminmenu[70]['link']="admin/index.php?action=SmilesList";
$adminmenu[70]['show']=true;

$adminmenu[80]['title']=_MI_LEGACY_MENU_COMMENT_MANAGE;
$adminmenu[80]['link']="admin/index.php?action=CommentList";
$adminmenu[80]['show']=true;

$adminmenu[90]['title']=_MI_LEGACY_MENU_IMAGE_MANAGE;
$adminmenu[90]['link']="admin/index.php?action=ImagecategoryList";
$adminmenu[90]['show']=true;

$adminmenu[100]['title']=_MI_LEGACY_MENU_THEME_MANAGE;
$adminmenu[100]['link']="admin/index.php?action=ThemeList";
$adminmenu[100]['show']=true;

$adminmenu[110]['title']=_MI_LEGACY_MENU_GENERAL_SETTINGS;
$adminmenu[110]['link']="admin/index.php?action=PreferenceEdit&confcat_id=1";
$adminmenu[110]['show']=true;

$adminmenu[120]['title']=_MI_LEGACY_MENU_XOOPS_CONFIG;
$adminmenu[120]['link']="admin/index.php?action=PreferenceList";
$adminmenu[120]['show']=true;

?>
