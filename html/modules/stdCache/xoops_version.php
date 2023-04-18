<?php
// $Id: xoops_version.php,v 1.11 2008/10/12 03:55:38 minahito Exp $


// Manifesto
$modversion['dirname']          = 'stdCache';
$modversion['name']             = _MI_STDCACHE_NAME;
$modversion['version']          = '2.33';
$modversion['detailed_version'] = '2.33.3';
$modversion['description']      = _MI_STDCACHE_NAME_DESC;
$modversion['author']           = 'The XOOPSCube Project';
$modversion['credits']          = 'The XOOPSCube Project';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_cache.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['cube_style']       = true;

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'menu.php';

// Menu
$modversion['hasMain'] = 0;

// Blocks
$modversion['blocks'][1]['func_num'] = 1;
$modversion['blocks'][1]['file'] = 'cacheclear.php';
$modversion['blocks'][1]['name'] = _MI_STDCACHE_BLOCK_CACHECLEAR;
$modversion['blocks'][1]['description'] = 'Clear cache';
$modversion['blocks'][1]['class'] = 'CacheclearBlock';
$modversion['blocks'][1]['template'] = 'stdcache_block_cacheclear.html';
$modversion['blocks'][1]['options'] = '60';
