<?php
// Module Name
define('_MI_STDCACHE_NAME', 'Standard Cache');
define('_MI_STDCACHE_NAME_DESC', 'Cache management module for XOOPS Cube Legacy');

define('_MI_STDCACHE_BLOCK_CACHECLEAR' , "Clear Cache");
// Admin Menu
define('_MI_STDCACHE_ADMIN', 'Cache Management');
define('_MI_STDCACHE_STATS', 'Cache Statistics');
define('_MI_STDCACHE_CLEAR', 'Clear Cache');
define('_MI_STDCACHE_CONFIG', 'Cache Configuration');



// Block
define('_MI_STDCACHE_BLOCK_NAME', 'Cache Clear');
define('_MI_STDCACHE_BLOCK_DESC', 'Quick cache clearing block');


// Help
define('_MI_STDCACHE_HELP_HEADER', __FILE__);
define('_MI_STDCACHE_HELP_OVERVIEW', 'overview.html');
define('_MI_STDCACHE_HELP_DISCLAIMER', 'disclaimer.html');
define('_MI_STDCACHE_HELP_LICENSE', 'license.html');
define('_MI_STDCACHE_HELP_SUPPORT', 'support.html');

// language/english/modinfo.php
// Admin Menu
define('_MI_STDCACHE_ADMENU_STATS', 'Cache Statistics');
define('_MI_STDCACHE_ADMENU_CLEAR', 'Clear Cache');
define('_MI_STDCACHE_ADMENU_CONFIG', 'Cache Configuration');

define('_MI_STDCACHE_CLEAR_CACHE', 'Clear Cache');
define('_MI_STDCACHE_CONFIRM_CLEAR', 'Confirm Clear');
define('_MI_STDCACHE_SAVE_CONFIG', 'Save Configuration');

// TODO
define('_MI_STDCACHE_COMPILED_FILES', 'Compiled Templates');
define('_MI_STDCACHE_CACHE_FILES', 'Cache Files');


// Module config
define('_MI_STDCACHE_CACHE_LIMIT', 'Cache Size Limit');
define('_MI_STDCACHE_CACHE_LIMIT_DESC', 'Maximum allowed size for the cache directory in bytes. Minimum: 10MB, Maximum: 1GB');
define('_MI_STDCACHE_CACHE_NOTIFICATION_LIMIT', 'Notification Threshold');
define('_MI_STDCACHE_CACHE_NOTIFICATION_LIMIT_DESC', 'Send notification to administrators when cache size exceeds this value');
define('_MI_STDCACHE_CACHE_CLEANUP_LIMIT', 'Auto-Cleanup Threshold');
define('_MI_STDCACHE_CACHE_CLEANUP_LIMIT_DESC', 'Automatically clean up oldest cache files when cache size exceeds this value');
define('_MI_STDCACHE_COMPILED_TEMPLATES_LIMIT', 'Compiled Templates Size Limit');
define('_MI_STDCACHE_COMPILED_TEMPLATES_LIMIT_DESC', 'Maximum allowed size for compiled templates directory in bytes');
define('_MI_STDCACHE_NOTIFICATION_ENABLED', 'Enable Email Notifications');
define('_MI_STDCACHE_NOTIFICATION_ENABLED_DESC', 'Send email notifications to administrators when cache size exceeds the notification threshold');

// Cache notification settings
define('_MI_STDCACHE_LAST_NOTIFICATION_TIME', 'Last Notification Time');
define('_MI_STDCACHE_LAST_NOTIFICATION_TIME_DESC', 'Timestamp of the last sent notification (system managed)');