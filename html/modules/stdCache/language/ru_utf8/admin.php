<?php
/**
 * Standard Cache - Admin Language File (English)
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

define('_AD_STDCACHE_STATS', 'Cache Statistics'); 
define('_AD_STDCACHE_CONFIG', 'Cache Configuration');
define('_AD_STDCACHE_SAVE_CONFIG', 'Save Configuration');
define('_AD_STDCACHE_STATS_TITLE', 'Cache Statistics');
define('_AD_STDCACHE_SUMMARY', 'Summary');
define('_AD_STDCACHE_TOTAL_SIZE', 'Total Size');
define('_AD_STDCACHE_CACHE_DIR_TITLE', 'Cache Directory');
define('_AD_STDCACHE_CACHE_FILES', 'Cache Files');
define('_AD_STDCACHE_FILES_COUNT', 'Files Count');
define('_AD_STDCACHE_FILES_SIZE', 'Total Size');
define('_AD_STDCACHE_SUBDIRS_COUNT', 'Subdirectories Count');
define('_AD_STDCACHE_COMPILED_TEMPLATES', 'Compiled Templates Directory');
define('_AD_STDCACHE_LOG_FILES_SECTION', 'Log Files Directory');
define('_AD_STDCACHE_UPLOAD_DIRECTORY', 'Uploads Directory');
define('_AD_STDCACHE_CLEAR_TITLE', 'Clear Cache');
define('_AD_STDCACHE_CLEAR_CONFIRM', 'Confirm Cache Clear');
define('_AD_STDCACHE_CLEAR_FILE_TYPES', 'Select cache types to clear:');
define('_AD_STDCACHE_CLEAR_SMARTY_CACHE_OPTION', 'Clear Smarty Cache (HTML)');
define('_AD_STDCACHE_CLEAR_COMPILED_TEMPLATES_OPTION', 'Clear Compiled Templates');
define('_AD_STDCACHE_CLEAR_LOG_FILES_OPTION', 'Clear Log Files');
define('_AD_STDCACHE_CLEAR_UPLOADS_OPTION', 'Clear Uploads Directory Content');
define('_AD_STDCACHE_CLEAR_OLDER_THAN', 'Clear files older than:');
define('_AD_STDCACHE_CLEAR_ALL_FILES', 'All Files (ignore age)');
define('_AD_STDCACHE_CLEAR_OLDER_1_DAY', '1 Day');
define('_AD_STDCACHE_CLEAR_OLDER_7_DAYS', '7 Days');
define('_AD_STDCACHE_CLEAR_OLDER_30_DAYS', '30 Days');
define('_AD_STDCACHE_CLEARED_FILES_FROM', 'Cleared %d files from %s%s.'); // %s for type, %s for age info if any
define('_AD_STDCACHE_JS_CONFIRM_CLEAR_UPLOADS', 'Confirm to delete all files from the uploads directory. This action cannot be undone.');
define('_AD_STDCACHE_CACHE_SETTINGS', 'Cache Management Settings');
define('_AD_STDCACHE_CACHE_LIMIT', 'Smarty Cache Size Limit (Bytes)');
define('_AD_STDCACHE_CACHE_LIMIT_DESC', 'Maximum allowed size for the Smarty cache directory (TRUST_PATH/cache).');
define('_AD_STDCACHE_CACHE_CLEANUP_LIMIT', 'Auto-Cleanup Threshold (Bytes)');
define('_AD_STDCACHE_CACHE_CLEANUP_LIMIT_DESC', 'Automatically clean up oldest Smarty cache files when its size exceeds this value.');
define('_AD_STDCACHE_COMPILED_TEMPLATES_LIMIT', 'Compiled Templates Size Limit (Bytes)');
define('_AD_STDCACHE_COMPILED_TEMPLATES_LIMIT_DESC', 'Maximum allowed size for compiled templates directory (TRUST_PATH/templates_c).');
// Messages
define('_AD_STDCACHE_CONFIG_SAVED_SUCCESS', 'Configuration saved successfully.');
define('_AD_STDCACHE_CONFIG_SAVED_FAIL', 'Failed to save configuration.');
define('_AD_STDCACHE_CONFIRM_CLEAR', 'Select at least one cache type to clear.');
define('_AD_STDCACHE_SUCCESS_CLEAR', 'Selected cache areas cleared successfully.');
define('_AD_STDCACHE_ERROR_CLEAR', 'An error occurred while clearing cache.');
define('_AD_STDCACHE_ERROR_REQUIRED', 'Required');
define('_AD_STDCACHE_WARNING_CLEAR_UPLOADS', 'Warning: This will delete all uploaded files from the uploads directory and cannot be undone. Use with extreme caution!');
// Test Email
define('_AD_STDCACHE_TEST_TITLE','Test Admin Email Notification');
define('_AD_STDCACHE_TEST_DESC','Click the button below to send a test admin email notification. This simulates a cache limit warning.');
define('_AD_STDCACHE_TEST_BUTTON','Send Test Email');
define('_AD_STDCACHE_TEST_MAIL_SUCCESS','Test notification email sent successfully. Please check the administrator\'s email.');
define('_AD_STDCACHE_TEST_MAIL_ERROR','Failed to send the test notification email. Please check mailer settings and logs.');
define('_AD_STDCACHE_TEST_ALERT_DISABLED','Notifications are disabled in module settings. The test email cannot be sent.');
define('_AD_STDCACHE_TEST_ALERT_ENABLED', 'Cache limit notifications are currently ENABLED in module settings.');
// Mail (used by AdminNotificationMailBuilder)
define('_AD_STDCACHE_MAIL_SUBJECT_CACHE_LIMIT', 'Cache Usage Alert on [ %s ]'); // %s for sitename
define('_AD_STDCACHE_MAIL_MSG_LIMIT_REACHED', "The cache usage is currently %s. This is approaching or has exceeded the configured notification limit of %s.");
define('_AD_STDCACHE_MAIL_MSG_GENERIC', "Please review the system status for the stdCache module.");
