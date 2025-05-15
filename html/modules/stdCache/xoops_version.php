<?php
/**
 * Standard Cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @author     Kazuhisa Minato aka minahito, Core developer
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/xpress
 * @since      1.11 minahito 2008/10/12 03:55:38  Exp $
 **/

// Manifesto
$modversion['dirname']          = 'stdCache';
$modversion['name']             = _MI_STDCACHE_NAME;
$modversion['version']          = '2.50';
$modversion['detailed_version'] = '2.50.0';
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
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
/* $modversion['templates'][1]['file'] = 'stdcache_admin_cache_stats.html';
$modversion['templates'][1]['description'] = 'Cache statistics page';
$modversion['templates'][2]['file'] = 'stdcache_admin_cache_clear.html';
$modversion['templates'][2]['description'] = 'Cache clear confirmation page';
$modversion['templates'][3]['file'] = 'stdcache_admin_cache_config.html';
$modversion['templates'][3]['description'] = 'Cache configuration page'; */

// Config
$modversion['config'] = [
    [
        'name'        => 'cache_limit',
        'title'       => '_MI_STDCACHE_CACHE_LIMIT',
        'description' => '_MI_STDCACHE_CACHE_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 50000000 // 50MB (50 * 1024 * 1024)
    ],
    [
        'name'        => 'cache_notification_limit',
        'title'       => '_MI_STDCACHE_CACHE_NOTIFICATION_LIMIT',
        'description' => '_MI_STDCACHE_CACHE_NOTIFICATION_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 40000000 // 40MB
    ],
    [
        'name'        => 'cache_cleanup_limit',
        'title'       => '_MI_STDCACHE_CACHE_CLEANUP_LIMIT',
        'description' => '_MI_STDCACHE_CACHE_CLEANUP_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 45000000 // 45MB
    ],
    [
        'name'        => 'compiled_templates_limit',
        'title'       => '_MI_STDCACHE_COMPILED_TEMPLATES_LIMIT',
        'description' => '_MI_STDCACHE_COMPILED_TEMPLATES_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 20000000 // 20MB
    ],
    [
        'name'        => 'notification_enabled',
        'title'       => '_MI_STDCACHE_NOTIFICATION_ENABLED',
        'description' => '_MI_STDCACHE_NOTIFICATION_ENABLED_DESC',
        'formtype'    => 'yesno',
        'valuetype'   => 'int',
        'default'     => 1
    ],


    // last_notification_time defined here too,
    // even if system-managed, so it exists in the config table.
    [
        'name'        => 'last_notification_time',
        'title'       => '_MI_STDCACHE_LAST_NOTIFICATION_TIME',
        'description' => '_MI_STDCACHE_LAST_NOTIFICATION_TIME_DESC',
        'formtype'    => 'textbox', // Or 'textbox' if you want admins to see it (read-only in effect)
        'valuetype'   => 'int',
        'default'     => 0
    ]
    ];
// Blocks
$modversion['blocks'][1]['func_num'] = 1;
$modversion['blocks'][1]['file'] = 'cacheclear.php';
$modversion['blocks'][1]['name'] = _MI_STDCACHE_BLOCK_CACHECLEAR;
$modversion['blocks'][1]['description'] = 'Clear cache';
$modversion['blocks'][1]['class'] = 'CacheclearBlock';
$modversion['blocks'][1]['template'] = 'stdcache_block_cacheclear.html';
$modversion['blocks'][1]['options'] = '60';
