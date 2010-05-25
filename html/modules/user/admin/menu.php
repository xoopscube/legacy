<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

$adminmenu[10]['title'] = _MI_USER_MENU_CREATE_USER;
$adminmenu[10]['link'] = "admin/index.php?action=UserEdit";
$adminmenu[10]['keywords'] = _MI_USER_KEYWORD_CREATE_USER;
$adminmenu[10]['show'] = false;

$adminmenu[20]['title'] = _MI_USER_ADMENU_LIST;
$adminmenu[20]['keywords'] = _MI_USER_KEYWORD_USER_LIST;
$adminmenu[20]['link'] = "admin/index.php";
$adminmenu[20]['show'] = true;

$adminmenu[30]['title'] = _MI_USER_ADMENU_USER_SEARCH;
$adminmenu[30]['keywords'] = _MI_USER_KEYWORD_USER_SEARCH;
$adminmenu[30]['link'] = "admin/index.php?action=UserSearch";
$adminmenu[30]['show'] = true;

$adminmenu[40]['title'] = _MI_USER_MENU_CREATE_RANK;
$adminmenu[40]['link'] = "admin/index.php?action=RanksEdit";
$adminmenu[40]['keywords'] = _MI_USER_KEYWORD_CREATE_RANK;
$adminmenu[40]['show'] = false;

$adminmenu[50]['title'] = _MI_USER_ADMENU_RANK_LIST;
$adminmenu[50]['link'] = "admin/index.php?action=RanksList";
$adminmenu[50]['show'] = true;

$adminmenu[60]['title'] = _MI_USER_MENU_CREATE_GROUP;
$adminmenu[60]['link'] = "admin/index.php?action=GroupEdit";
$adminmenu[60]['keywords'] = _MI_USER_KEYWORD_CREATE_GROUP;
$adminmenu[60]['show'] = false;

$adminmenu[70]['title'] = _MI_USER_ADMENU_GROUP_LIST;
$adminmenu[70]['keywords'] = _MI_USER_KEYWORD_GROUP_LIST;
$adminmenu[70]['link'] = "admin/index.php?action=GroupList";
$adminmenu[70]['show'] = true;

$adminmenu[80]['title'] = _MI_USER_MENU_CREATE_AVATAR;
$adminmenu[80]['link'] = "admin/index.php?action=AvatarEdit";
$adminmenu[80]['keywords'] = _MI_USER_KEYWORD_CREATE_AVATAR;
$adminmenu[80]['show'] = false;

$adminmenu[90]['title'] = _MI_USER_ADMENU_AVATAR_MANAGE;
$adminmenu[90]['keywords'] = _MI_USER_KEYWORD_AVATAR_MANAGE;
$adminmenu[90]['link'] = "admin/index.php?action=AvatarList";
$adminmenu[90]['show'] = true;

$adminmenu[100]['title'] = _MI_USER_ADMENU_MAILJOB_MANAGE;
$adminmenu[100]['keywords'] = _MI_USER_KEYWORD_MAILJOB_MANAGE;
$adminmenu[100]['link'] = "admin/index.php?action=MailjobList";
$adminmenu[100]['show'] = true;

$adminmenu[110]['title'] = _MI_USER_ADMENU_USER_DATA_DOWNLOAD;
$adminmenu[110]['keywords'] = _MI_USER_KEYWORD_MAILJOB_MANAGE;
$adminmenu[110]['link'] = "admin/index.php?action=UserDataDownload";
$adminmenu[110]['show'] = true;

$adminmenu[120]['title'] = _MI_USER_ADMENU_USER_DATA_CSVUPLOAD;
$adminmenu[120]['keywords'] = _MI_USER_KEYWORD_MAILJOB_MANAGE;
$adminmenu[120]['link'] = "admin/index.php?action=UserDataUpload";
$adminmenu[120]['show'] = true;


?>
