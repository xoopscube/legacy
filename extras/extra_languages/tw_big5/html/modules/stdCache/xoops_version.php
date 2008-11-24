<?php
// $Id: xoops_version.php,v 1.1 2008/03/09 02:26:15 minahito Exp $

$modversion['name'] = _MI_STDCACHE_NAME;
$modversion['version'] = 1.03;
$modversion['description'] = _MI_STDCACHE_NAME_DESC;
$modversion['author'] = "";
$modversion['credits'] = "XOOPS Cube Project";
$modversion['license'] = "GPL see LICENSE";
$modversion['image'] = "images/stdCache.png";
$modversion['dirname'] = "stdCache";
$modversion['help'] = "help.html";

$modversion['cube_style'] = true;

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "menu.php";

// Menu
$modversion['hasMain'] = 0;

// Blocks
$modversion['blocks'][1]['func_num'] = 1;
$modversion['blocks'][1]['file'] = "cacheclear.php";
$modversion['blocks'][1]['name'] = _MI_STDCACHE_BLOCK_CACHECLEAR;
$modversion['blocks'][1]['description'] = "清除區塊暫存";
$modversion['blocks'][1]['class'] = "CacheclearBlock";
$modversion['blocks'][1]['template'] = 'stdcache_block_cacheclear.html';
$modversion['blocks'][1]['options'] = '60';

?>
