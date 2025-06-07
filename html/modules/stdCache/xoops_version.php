<?php
/**
 * Standard Cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8 
 * @author     Kazuhisa Minato aka minahito, Core developer
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 * @since      1.11 minahito 2008/10/12 03:55:38  Exp $
 **/

// Manifesto
$modversion['dirname']          = 'stdCache';
$modversion['name']             = _MI_STDCACHE_NAME;
$modversion['version']          = '2.50';
$modversion['detailed_version'] = '2.50.0';
$modversion['description']      = _MI_STDCACHE_NAME_DESC;
$modversion['author']           = 'Kazuhisa Minato, Nuno Luciano (XCL)';
$modversion['credits']          = 'The XOOPSCube Project';
$modversion['license']          = 'GPL see LICENSE';
$modversion['image']            = 'images/module_cache.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['cube_style']       = true;

// install function
$modversion['onInstall'] = 'oninstall.php';
$modversion['onUpdate'] = 'onupdate.php'; 

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Templates
// $modversion['templates'][1]['file'] = 'stdcache_cache_stats.html';
// $modversion['templates'][1]['description'] = 'Cache statistics page';

// Notifications - Using MailBuilder
$modversion['hasNotification'] = 0;

// Blocks
$modversion['blocks'][1]['func_num'] = 1;
$modversion['blocks'][1]['file'] = 'cacheclear.php';
$modversion['blocks'][1]['name'] = _MI_STDCACHE_BLOCK_CACHECLEAR;
$modversion['blocks'][1]['description'] = 'Clear cache';
$modversion['blocks'][1]['class'] = 'CacheclearBlock';
$modversion['blocks'][1]['template'] = 'stdcache_block_cacheclear.html';
$modversion['blocks'][1]['options'] = '60';

// Config
$modversion['config'] = [
    [
        'name'        => 'cache_limit_smarty',
        'title'       => '_MI_STDCACHE_CONF_CACHE_LIMIT',
        'description' => '_MI_STDCACHE_CONF_CACHE_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 50000000 // 50MB (50 * 1024 * 1024)
    ],
    [
        'name'        => 'cache_limit_cleanup',
        'title'       => '_MI_STDCACHE_CONF_CACHE_CLEANUP_LIMIT',
        'description' => '_MI_STDCACHE_CONF_CACHE_CLEANUP_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 45000000 // 45MB
    ],
    [
        'name'        => 'cache_limit_compiled',
        'title'       => '_MI_STDCACHE_CONF_COMPILED_TEMPLATES_LIMIT',
        'description' => '_MI_STDCACHE_CONF_COMPILED_TEMPLATES_LIMIT_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 20000000 // 20MB
    ],
    [
        'name'        => 'cache_limit_alert_trigger',
        'title'       => '_MI_STDCACHE_ALERT_TRIGGER',
        'description' => '_MI_STDCACHE_ALERT_TRIGGER_DESC',
        'formtype'    => 'textbox',
        'valuetype'   => 'int',
        'default'     => 40000000 // e.g., 40MB
    ],
    [
        'name'        => 'cache_limit_alert_enable',
        'title'       => '_MI_STDCACHE_ALERT_ENABLED',
        'description' => '_MI_STDCACHE_ALERT_ENABLED_DESC',
        'formtype'    => 'yesno',
        'valuetype'   => 'int',
        'default'     => 1
    ],
    [
        'name'        => 'last_cache_alert_time',
        'title'       => '_MI_STDCACHE_CONF_ALERT_LAST_TIME',
        'description' => '_MI_STDCACHE_CONF_ALERT_LAST_TIME_DESC',
        'formtype'    => 'textbox', // CANNOT USE HIDDEN (error required) Not user-editable via standard prefs
        'valuetype'   => 'int',
        'default'     => '1740669925'
    ],
];
