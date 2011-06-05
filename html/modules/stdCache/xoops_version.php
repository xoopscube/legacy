<?php
// $Id: xoops_version.php,v 1.11 2008/10/12 03:55:38 minahito Exp $

$modversion['name'] = _MI_STDCACHE_NAME;
$modversion['version'] = 2.01; // 2.2
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
$modversion['blocks'][1]['description'] = "Clear cache";
$modversion['blocks'][1]['class'] = "CacheclearBlock";
$modversion['blocks'][1]['template'] = 'stdcache_block_cacheclear.html';
$modversion['blocks'][1]['options'] = '60';

?>
