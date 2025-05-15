<?php

// Cache Stats
define('_AD_STDCACHE_STATS', 'Cache Statistics');
define('_AD_STDCACHE_COMPILED_FILES', 'Compiled Templates');
define('_AD_STDCACHE_CACHE_FILES', 'Cache Files');
define('_AD_STDCACHE_TOTAL_SIZE', 'Total Size');

// Cache Clear
define('_AD_STDCACHE_CLEAR_CONFIRM', 'Confirm Cache Clear');
define('_AD_STDCACHE_CONFIRM_CLEAR', 'Confirm Clear');
define('_AD_STDCACHE_CLEAR_CACHE', 'Clear Cache');

// Cache Config Message
define('_AD_STDCACHE_CONFIG_SAVED_SUCCESS', 'Configuration saved successfully');
define('_AD_STDCACHE_CONFIG_SAVED_FAIL', 'Failed to save configuration');

define( '_AD_STDCACHE_LANG_INTERVAL_TIMER' , 'Interval timer (min) :');

define('_AD_STDCACHE_ERROR_REQUIRED', 'Required');
define('_AD_STDCACHE_ERROR_MIN', 'Min');
define('_AD_STDCACHE_ERROR_MAX', 'Max');
define('_AD_STDCACHE_ERROR_INT', 'Integer');

/* define('_AD_STDCACHE_CACHE_LIMIT', 'CACHE_LIMIT');
define('_AD_STDCACHE_CACHE_LIMIT_DESC', 'CACHE_LIMIT desc.'); */
// define('_AD_STDCACHE_SAVE_CONFIG', 'Save Config');

define('_AD_STDCACHE_CLEAR_TITLE','Cache Clear');

define('_AD_STDCACHE_CLEAR_FILE_TYPES', 'Select file types to clear:');
define('_AD_STDCACHE_CLEAR_CACHE_HTML', 'Cache HTML files');
define('_AD_STDCACHE_CLEAR_LOGS', 'Log files');
define('_AD_STDCACHE_CLEAR_ALL', 'Clear all files');
define('_AD_STDCACHE_FILES', 'files');
define('_AD_STDCACHE_SELECT_FILE_TYPE', 'Please select at least one file type to clear');
define('_AD_STDCACHE_FILE_TYPES_HTML', 'HTML');
define('_AD_STDCACHE_FILE_TYPES_LOGS', 'Logs');
define('_AD_STDCACHE_FILE_TYPES_ALL', 'All');

define('_AD_STDCACHE_STATS_TITLE','Cache Statistics');
define('_AD_STDCACHE_COMPILED_TEMPLATES', 'Compiled Templates');

define('_AD_STDCACHE_FILES_COUNT', 'Files Count');
define('_AD_STDCACHE_FILES_SIZE', 'Files Size');
define('_AD_STDCACHE_CACHE_STATS', 'Cache Statistics');
define('_AD_STDCACHE_CACHE_STATS_DESC', 'Cache Statistics desc.');
define('_AD_STDCACHE_CACHE_STATS_HTML', 'Cache HTML');
define('_AD_STDCACHE_CACHE_STATS_LOGS', 'Cache Logs');
define('_AD_STDCACHE_SUBDIRS_COUNT', 'Subdirs Count');
define('_AD_STDCACHE_SUBDIRS_SIZE', 'Subdirs Size');
define('_AD_STDCACHE_CACHE_DIR_TITLE', 'Cache Directory');
define('_AD_STDCACHE_CACHE_STATS_ALL_DESC', 'Cache All desc.');

define('_AD_STDCACHE_UPLOAD_DIRECTORY', 'Upload Directory');
define('_AD_STDCACHE_SUMMARY', 'Summary');

define('_AD_STDCACHE_CACHE_LIMIT_UNIT', 'MB');
define('_AD_STDCACHE_CACHE_LIMIT_UNIT_DESC', 'Cache Limit Unit desc.');

// Messages
define('_AD_STDCACHE_ERROR_CLEAR', 'Error clearing cache');
define('_AD_STDCACHE_SUCCESS_CLEAR', 'Cache cleared successfully');
define('_AD_STDCACHE_ERROR_CONFIG', 'Error saving configuration');
define('_AD_STDCACHE_SUCCESS_CONFIG', 'Configuration saved successfully');
define('_AD_STDCACHE_ERROR_INVALID_TYPE', 'Invalid type');


// Cache config
define('_AD_STDCACHE_CONFIG', 'Cache Configuration');
define('_AD_STDCACHE_SAVE_CONFIG', 'Save Configuration');
define('_AD_STDCACHE_CACHE_SETTINGS', 'Cache Settings');
define('_AD_STDCACHE_CACHE_LIMIT', 'Cache Size Limit');
define('_AD_STDCACHE_CACHE_LIMIT_DESC', 'Maximum allowed size for the cache directory in bytes');
define('_AD_STDCACHE_CACHE_NOTIFICATION_LIMIT', 'Notification Threshold');
define('_AD_STDCACHE_CACHE_NOTIFICATION_LIMIT_DESC', 'Send notification to administrators when cache size exceeds this value');
define('_AD_STDCACHE_CACHE_CLEANUP_LIMIT', 'Auto-Cleanup Threshold');
define('_AD_STDCACHE_CACHE_CLEANUP_LIMIT_DESC', 'Automatically clean up oldest cache files when cache size exceeds this value');
define('_AD_STDCACHE_COMPILED_TEMPLATES_LIMIT', 'Compiled Templates Size Limit');
define('_AD_STDCACHE_COMPILED_TEMPLATES_LIMIT_DESC', 'Maximum allowed size for compiled templates directory in bytes');
define('_AD_STDCACHE_NOTIFICATION_ENABLED', 'Enable Email Notifications');
define('_AD_STDCACHE_NOTIFICATION_ENABLED_DESC', 'Send email notifications to administrators when cache size exceeds the notification threshold');

// Email notification
define('_AD_STDCACHE_MAIL_SUBJECT', 'Cache Size Warning: %s');

// define('_AD_STDCACHE_CLEARED_FILES', 'Cleared Files');
define('_AD_STDCACHE_SMARTY_CACHE', 'Smarty Cache');
define('_AD_STDCACHE_CLEARED_FILES', 'Cleared Files');

// Clear options

define('_AD_STDCACHE_LOG_FILES_SECTION', 'Log Files'); // For stats display
define('_AD_STDCACHE_CLEAR_LOG_FILES_OPTION', 'Clear Log Files'); // For clear cache form
define('_AD_STDCACHE_CLEARED_FILES_FROM', 'Cleared %d files from %s.'); // Generic message

define('_AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR', 'Please select at least one cache type to clear.');
define('_AD_STDCACHE_CLEAR_SMARTY_CACHE_OPTION', 'Clear Smarty Cache (HTML)');
define('_AD_STDCACHE_CLEAR_COMPILED_TEMPLATES_OPTION', 'Clear Compiled Templates');
define('_AD_STDCACHE_CLEAR_UPLOADS_OPTION', 'Clear Uploads Directory Content'); // Be careful with this one!

// Clear

 

define('_AD_STDCACHE_WARNING_CLEAR_UPLOADS', 'Warning: This will delete all user-uploaded files (avatars, images, etc.) and cannot be undone. Use with extreme caution!');
define('_AD_STDCACHE_JS_CONFIRM_CLEAR_UPLOADS', 'Are you absolutely sure you want to delete all files from the uploads directory? This action cannot be undone.');

define('_AD_STDCACHE_CLEAR_OLDER_THAN','Clear Older Than');
define('_AD_STDCACHE_CLEAR_ALL_FILES','Clear All Files');
define('_AD_STDCACHE_CLEAR_OLDER_1_DAY','Clear 1 day');
define('_AD_STDCACHE_CLEAR_OLDER_7_DAYS','Clear 7 days');
define('_AD_STDCACHE_CLEAR_OLDER_30_DAYS','Clear 30 days');