<?php
/**
 * Standard Cache - Module Information Language File (English)
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

// Module Name
define('_MI_STDCACHE_NAME', 'Cache');
define('_MI_STDCACHE_NAME_DESC', 'Cache management module for XOOPSCube Legacy');

// Admin Menu
define('_MI_STDCACHE_ADMIN_TITLE', 'Cache Management');
define('_MI_STDCACHE_ADMENU_STATS', 'Cache Statistics');
define('_MI_STDCACHE_ADMENU_CLEAR', 'Clear Cache');
define('_MI_STDCACHE_ADMENU_CONFIG', 'Configuration');
define('_MI_STDCACHE_ADMENU_NOTIFY', 'Mail Notification');

// Block
define('_MI_STDCACHE_BLOCK_NAME', 'Quick Cache Clear');
define('_MI_STDCACHE_BLOCK_DESC', 'Provides a button to quickly clear configured cache types.');
define('_MI_STDCACHE_BLOCK_CACHECLEAR' , "Clear Cache Block"); 

// Module Config
define('_MI_STDCACHE_CONF_CACHE_LIMIT', 'Cache Size Limit (Bytes)');
define('_MI_STDCACHE_CONF_CACHE_LIMIT_DESC', 'Maximum allowed size for the Smarty cache directory (typically TRUST_PATH/cache). Example: 52428800 for 50MB.');
define('_MI_STDCACHE_CONF_CACHE_CLEANUP_LIMIT', 'Auto-Cleanup Cache Threshold (Bytes)');
define('_MI_STDCACHE_CONF_CACHE_CLEANUP_LIMIT_DESC', 'Automatically clean up oldest Smarty cache files when its size exceeds this value. Example: 47185920 for 45MB.');
define('_MI_STDCACHE_CONF_COMPILED_TEMPLATES_LIMIT', 'Compiled Templates Size Limit (Bytes)');
define('_MI_STDCACHE_CONF_COMPILED_TEMPLATES_LIMIT_DESC', 'Maximum allowed size for compiled templates directory (TRUST_PATH/templates_c). Example: 20971520 for 20MB.');
define('_MI_STDCACHE_CONF_ALERT_LAST_TIME', 'Last Notification Time (system)');
define('_MI_STDCACHE_CONF_ALERT_LAST_TIME_DESC', 'Timestamp of last notification sent: System-managed. Do not change!');

// Email alerts
define('_MI_STDCACHE_ALERT_TRIGGER', 'Email Alert Threshold (Bytes)');
define('_MI_STDCACHE_ALERT_TRIGGER_DESC', 'Send an email alert when the Smarty cache size exceeds this value. Example: 41943040 for 40MB.');
define('_MI_STDCACHE_ALERT_ENABLED','Enable Cache Limit Email Alerts');
define('_MI_STDCACHE_ALERT_ENABLED_DESC','If enabled, an email alert will be sent to administrators when the cache email alert threshold is reached.');
