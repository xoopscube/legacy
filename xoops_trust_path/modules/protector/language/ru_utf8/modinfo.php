<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'protector';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {
	define( $constpref . '_LOADED', 1 );

	// The name of this module
	define( $constpref . '_NAME', 'Protector' );

	// A brief description of this module
	define( $constpref . '_DESC', 'Этот модуль защищает вашу платформу приложений от различных атак, таких как DoS, SQL-инъекции и заражение глобальных переменных.' );

	// Menu
	define( $constpref . '_DASHBOARD', 'Dashboard' );
	define( $constpref . '_ADVISORY', 'Советник по безопасности' );
	define( $constpref . '_LOGLIST', 'Security Log' );
	define( $constpref . '_LOGCLEARED', 'Log Cleared' );
	define( $constpref . '_IPBAN', 'Запрещенные IP' );
	define( $constpref . '_PREFIXMANAGER', 'Префикс БД ' );
	define( $constpref . '_SAFELIST', 'IP авторизован' );
	define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Разрешения' );
	define( $constpref . '_CSP_REPORTS', 'CSP Reports');

	// Configs
	define( $constpref . '_GLOBAL_DISBL', '<h5>🚧 Временно отключен</h5>' );
	define( $constpref . '_GLOBAL_DISBLDSC', 'Все средства защиты временно отключены.<br>Не забудьте включить его после устранения какой-либо неполадки.' );

	define( $constpref . '_DEFAULT_LANG', 'Язык по умолчанию' );
	define( $constpref . '_DEFAULT_LANGDSC', 'Укажите язык, на котором будет отображаться сообщение перед обработкой common.php' );

	define( $constpref . '_RELIABLE_IPS', 'Надежные IP-адреса' );
	define( $constpref . '_RELIABLE_IPSDSC', 'Add an IP address that you can rely separated with:<br> | . ^ to match first digits<br> $  to match last digits of string.' );

	define( $constpref . '_LOG_LEVEL', 'Logging level' );
	define( $constpref . '_LOG_LEVELDSC', '' );

	define( $constpref . '_BANIP_TIME0', 'Banned IP - suspension time in secondes.' );

	define( $constpref . '_BANIP_IPV6PREFIX', 'IPv6 deny list registration prefix' );
	define( $constpref . '_BANIP_IPV6PREFIXDSC', 'Number of prefix bit at IPv6 address registration (128 bit to all bits)' );

	define( $constpref . '_LOGLEVEL0', 'none' );
	define( $constpref . '_LOGLEVEL15', 'Quiet' );
	define( $constpref . '_LOGLEVEL63', 'quiet' );
	define( $constpref . '_LOGLEVEL255', 'full' );

	define( $constpref . '_HIJACK_TOPBIT', 'Protected IP bits for the session(IPv4)' );
	define( $constpref . '_HIJACK_TOPBITDSC', 'Anti Session Hijacking:<br>Default 32(bit). (All bits are protected)<br>When your IP is not static, set the IP range by number of the bits.<br>(eg) If your IP is dynamic and in the range of 192.168.0.0-192.168.0.255, set the value here to: 24' );
	define( $constpref . '_HIJACK_TOPBITV6', 'Protected IP bits for the session(IPv6)' );
	define( $constpref . '_HIJACK_TOPBITV6DSC', 'Anti Session Hijacking:<br>Default 128(bit). (All bits are protected)<br>When your IP is not static, set the IP range by number of the bits.' );
	define( $constpref . '_HIJACK_DENYGP', 'Groups disallowed IP moving in a session' );
	define( $constpref . '_HIJACK_DENYGPDSC', 'Anti Session Hijacking:<br>Select groups which is disallowed to move their IP in a session.<br>(I recommend to turn Administrator on.)' );
	define( $constpref . '_SAN_NULLBYTE', 'Sanitizing null-bytes' );
	define( $constpref . '_SAN_NULLBYTEDSC', 'The null character is often represented as the escape sequence "\\0" and it is often used in malicious attacks.<br>Protector will change a null-byte to a space.<br>It is highly recomment to turn this option: On' );
	define( $constpref . '_DIE_NULLBYTE', 'Exit if null bytes are found' );
	define( $constpref . '_DIE_NULLBYTEDSC', 'The terminating character "\\0" is often used in malicious attacks.<br>(highly recommended as On)' );
	define( $constpref . '_DIE_BADEXT', 'Exit if the file extension is not allowed to upload' );
	define( $constpref . '_DIE_BADEXTDSC', 'If someone tries to upload unauthorized file extension (eg .php), this module terminates its execution by making an exit.<br>If you authorize the attachment of php files (eg module Wiki), turn this option: Off.' );
	define( $constpref . '_CONTAMI_ACTION', 'Action to trigger if a contamination is found' );
	define( $constpref . '_CONTAMI_ACTIONDS', 'Select the action to trigger for any attempt to contaminate global variables in your system.<br>The recommended option is : blank screen' );
	define( $constpref . '_ISOCOM_ACTION', 'Action to trigger if an isolated comment-in is found' );
	define( $constpref . '_ISOCOM_ACTIONDSC', 'Anti SQL Injection:<br>Select the action to trigger when an isolated "/*" is found.<br>"Sanitizing" means adding another "*/" to the tail.<br>The recommended option is : Sanitizing' );
	define( $constpref . '_UNION_ACTION', 'Action to trigger if a SQL UNION is found' );
	define( $constpref . '_UNION_ACTIONDSC', 'Anti SQL Injection:<br>Select the action to trigger when a SQL syntax like UNION is found.<br>"Sanitizing" means changing "union" to "uni-on".<br>The recommended option is : Sanitizing' );
	define( $constpref . '_ID_INTVAL', 'Force intval to variables like id' );
	define( $constpref . '_ID_INTVALDSC', 'All requests named "*id" will be treated as integer.<br>This option protects you from some XSS and SQL Injections.<br>Though it is recommended to turn this option on, it can cause problems to some modules.' );
	define( $constpref . '_FILE_DOTDOT', 'Prevent directory traversal attacks' );
	define( $constpref . '_FILE_DOTDOTDSC', 'Protector eliminates the ".." and attributes often used for a path traversal attack (also known as directory traversal attacks).' );

	define( $constpref . '_BF_COUNT', 'Anti Brute Force' );
	define( $constpref . '_BF_COUNTDSC', 'Set the max failed attemps to login a guest is allowed within 10 minutes. If someone fails to login more than this number, the guest IP will be banned.' );

	define( $constpref . '_BWLIMIT_COUNT', 'Bandwidth limitation' );
	define( $constpref . '_BWLIMIT_COUNTDSC', 'Specify the max number of connections allowed to mainfile.php at the same time.<br>This value should be set to "0" for a normal environment with enough bandwidth and CPU.<br>Less than 10 will be ignored.' );

	define( $constpref . '_DOS_SKIPMODS', 'Safe Modules for DoS/Crawler checker' );
	define( $constpref . '_DOS_SKIPMODSDSC', 'Set the dirnames of the modules separated with |.<br>This option is useful for modules with a large volume of requests at once: chat, videoconference, etc.' );

	define( $constpref . '_DOS_EXPIRE', 'Load Time Optimization (sec)' );
	define( $constpref . '_DOS_EXPIREDSC', 'Set a value to monitor and limit the frequent reloading (F5 attack) and high loading crawlers.' );

	define( $constpref . '_DOS_F5COUNT', 'Limit F5 Attack' );
	define( $constpref . '_DOS_F5COUNTDSC', 'Set a max number of reloads to prevent DoS attacks.<br>This value specifies the reloading limit to be considered as a malicious attack.' );
	define( $constpref . '_DOS_F5ACTION', 'Action to trigger against F5 Attacks' );

	define( $constpref . '_DOS_CRCOUNT', 'Limit for Bad Crawlers' );
	define( $constpref . '_DOS_CRCOUNTDSC', 'Prevent high loading by bad crawlers.<br>This value specifies the max connections to be considered as a bad-manner crawler.' );
	define( $constpref . '_DOS_CRACTION', 'Action to trigger against high loading by Bad Crawlers' );

	define( $constpref . '_DOS_CRSAFE', 'Welcomed User-Agent' );
	define( $constpref . '_DOS_CRSAFEDSC', 'A perl regex pattern for User-Agent.<br>If it matches, the crawler is never considered as a high loading bad crawler.<br>Example: /(msnbot|Googlebot|Yahoo! Slurp)/i' );

	define( $constpref . '_OPT_NONE', 'None (only logging)' );
	define( $constpref . '_OPT_SAN', 'Sanitizing' );
	define( $constpref . '_OPT_EXIT', 'Blank Screen' );
	define( $constpref . '_OPT_BIP', 'Ban the IP (No limit)' );
	define( $constpref . '_OPT_BIPTIME0', 'Ban the IP (moratorium)' );

	define( $constpref . '_DOSOPT_NONE', 'None (only logging)' );
	define( $constpref . '_DOSOPT_SLEEP', 'Sleep' );
	define( $constpref . '_DOSOPT_EXIT', 'Blank Screen' );
	define( $constpref . '_DOSOPT_BIP', 'Ban the IP (No limit)' );
	define( $constpref . '_DOSOPT_BIPTIME0', 'Ban the IP (moratorium)' );
	define( $constpref . '_DOSOPT_HTA', 'DENY by .htaccess (Experimental)' );

	define( $constpref . '_BIP_EXCEPT', 'Safe Groups' );
	define( $constpref . '_BIP_EXCEPTDSC', 'Users from the selected groups are not registered as Bad IP.<br>It is recommended to select the group of administrators: Webmasters' );

	define( $constpref . '_DISABLES', 'Disable XML-RPC to avoid a brute force attack. It is recommend to check for such dangerous feature on older XOOPS versions and modules (eg. Xpress, Wordpress).' );

	define( $constpref . '_DBLAYERTRAP', 'Enable DB Layer trapping Anti-SQL-Injection' );
	define( $constpref . '_DBLAYERTRAPDSC', 'Most SQL injection attacks will be negated by this feature. This feature requires databasefactory support. You can check it on the Security Notices page. This setting must be enabled. Never turn it off accidentally!' );
	define( $constpref . '_DBTRAPWOSRV', 'Never check _SERVER for Anti-SQL-Injection' );
	define( $constpref . '_DBTRAPWOSRVDSC', 'Some servers still enable DB Layer trapping. This causes bad detections like an SQL injection attack. If you have such errors, enable this option. You should be aware that this option weakens the security of DB Layer trapping anti-SQL-Injection.' );

	define( $constpref . '_BIGUMBRELLA', 'Enable Anti-XSS (BigUmbrella)' );
	define( $constpref . '_BIGUMBRELLADSC', 'This protects you from almost all attacks via XSS vulnerabilities. But it is not 100% sure' );

	define( $constpref . '_SPAMURI4U', 'Anti-SPAM: Limit the number of URLs for user posts' );
	define( $constpref . '_SPAMURI4UDSC', 'If this number of URLs is in the POST data of users other than the administrator, the POST is considered SPAM. 0 means disable this feature.' );
	define( $constpref . '_SPAMURI4G', 'Anti-SPAM: Limit the number of URLs for guests' );
	define( $constpref . '_SPAMURI4GDSC', 'If this number of URLs are found in POST data from guests, the POST is considered as SPAM. 0 means disabling this feature.' );

	define( $constpref . '_FILTERS', 'Enable filters' );
	define( $constpref . '_FILTERSDSC', 'Specify each file name inside of /filters_byconfig/ with a new line.<br> e.g. postcommon_post_deny_by_httpbl.php' );

	define( $constpref . '_MANIPUCHECK', '<b>Enable check files change</b>' );
	define( $constpref . '_MANIPUCHECKDSC', '🔔 Notify administrators if the root folder or index changes.' );
	define( $constpref . '_MANIPUVALUE', '<b>Value to check files change</b>' );
	define( $constpref . '_MANIPUVALUEDSC', '⛔ Warning, do not change this field !' );

	// Threat Intelligence settings
	define( $constpref . '_HTTPBL_ENABLED', '<h5>🔶 Enable HTTP:BL</h5>');
	define( $constpref . '_HTTPBL_ENABLED_DESC', 'Enable Project Honeypot HTTP:BL service for IP reputation checking');
	define( $constpref . '_HTTPBL_KEY', 'HTTP:BL API Key');
	define( $constpref . '_HTTPBL_KEY_DESC', 'Enter your Project Honeypot HTTP:BL API key. Get one at projecthoneypot.org');
	define( $constpref . '_HTTPBL_THREAT_THRESHOLD', 'Threat Threshold');
	define( $constpref . '_HTTPBL_THREAT_THRESHOLD_DESC', 'IPs with a threat score above this value will be blocked (0-255). Recommended: 25-50');

	define( $constpref . '_FEED_URLS', 'Threat Feed URLs');
	define( $constpref . '_FEED_URLS_DESC', 'Enter URLs for IP blacklists, one per line. Supported formats: CSV, TXT with one IP per line');

	define( $constpref . '_CHECK_LOGIN', 'Check Login Attempts');
	define( $constpref . '_CHECK_LOGIN_DESC', 'Verify IPs against threat intelligence during login attempts');
	define( $constpref . '_CHECK_REGISTER', 'Check Registration');
	define( $constpref . '_CHECK_REGISTER_DESC', 'Verify IPs against threat intelligence during user registration');
	define( $constpref . '_CHECK_FORMS', 'Check Form Submissions');
	define( $constpref . '_CHECK_FORMS_DESC', 'Verify IPs against threat intelligence during any form submission (may impact performance)');
	define( $constpref . '_CHECK_ADMIN', 'Check Admin Access');
	define( $constpref . '_CHECK_ADMIN_DESC', 'Verify IPs against threat intelligence during admin area access');

	define( $constpref . '_CACHE_DURATION', 'Cache Duration');
	define( $constpref . '_CACHE_DURATION_DESC', 'How long to cache threat intelligence results');
	define( $constpref . '_CACHE_1HOUR', '1 Hour');
	define( $constpref . '_CACHE_6HOURS', '6 Hours');
	define( $constpref . '_CACHE_1DAY', '1 Day');
	define( $constpref . '_CACHE_1WEEK', '1 Week');

	// Threat Intelligence Dashboard
define( $constpref . '_THREAT_INTELLIGENCE_DASHBOARD', 'Threat Intelligence Dashboard');
define( $constpref . '_THREAT_INTELLIGENCE_SETTINGS', 'Threat Intelligence Settings');
define( $constpref . '_NOTHREATSTATS', 'No threat intelligence events recorded yet');
define( $constpref . '_DATE', 'Date/Time');
define( $constpref . '_IP', 'IP Address');
define( $constpref . '_AGENT', 'User Agent');
define( $constpref . '_DESCRIPTION', 'Description');

// Proxy settings
define('_MI_PROTECTOR_PROXY_ENABLED', '<h5><a id="enable-proxy">🌐</a> Enable Web Proxy</h5>');
define('_MI_PROTECTOR_PROXY_ENABLED_DESC', 'Enable the web proxy functionality');

define('_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS', 'Allowed Domains');
define('_MI_PROTECTOR_PROXY_ALLOWED_DOMAINS_DESC', 'Enter one domain per line. Leave empty to allow all domains not in the blocked list. Use .example.com to match all subdomains.');

define('_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS', 'Blocked Domains');
define('_MI_PROTECTOR_PROXY_BLOCKED_DOMAINS_DESC', 'Enter one domain per line. These domains will always be blocked. Use .example.com to match all subdomains.');

define('_MI_PROTECTOR_PROXY_CACHE_ENABLED', 'Enable Caching');
define('_MI_PROTECTOR_PROXY_CACHE_ENABLED_DESC', 'Cache proxied content to improve performance');

define('_MI_PROTECTOR_PROXY_CACHE_TIME', 'Cache Time (seconds)');
define('_MI_PROTECTOR_PROXY_CACHE_TIME_DESC', 'How long to keep cached content (in seconds)');

define('_MI_PROTECTOR_PROXY_LOG_REQUESTS', 'Log Requests');
define('_MI_PROTECTOR_PROXY_LOG_REQUESTS_DESC', 'Log all proxy requests');

define('_MI_PROTECTOR_PROXY_STRIP_JS', 'Strip JavaScript');
define('_MI_PROTECTOR_PROXY_STRIP_JS_DESC', 'Remove JavaScript from proxied content');

define('_MI_PROTECTOR_PROXY_STRIP_COOKIES', 'Strip Cookies');
define('_MI_PROTECTOR_PROXY_STRIP_COOKIES_DESC', 'Do not forward cookies from proxied sites');

define('_MI_PROTECTOR_PROXY_USER_AGENT', 'Custom User Agent');
define('_MI_PROTECTOR_PROXY_USER_AGENT_DESC', 'Set a custom user agent for proxy requests. Leave empty to use the default.');

define('_MI_PROTECTOR_PROXY_PLUGINS_ENABLED', 'Enabled Plugins');
define('_MI_PROTECTOR_PROXY_PLUGINS_ENABLED_DESC', 'Select which proxy plugins to enable');

// Module access permissions
define('_MI_PROTECTOR_MODULE_ACCESS_GROUPS', 'Module Access Groups');
define('_MI_PROTECTOR_MODULE_ACCESS_GROUPS_DESC', 'Select which groups can access the Protector module administration');

// Proxy access permissions
define('_MI_PROTECTOR_PROXY_ACCESS_GROUPS', 'Proxy Access Groups');
define('_MI_PROTECTOR_PROXY_ACCESS_GROUPS_DESC', 'Select which groups can use the web proxy functionality');

// Notification related constants
define('_MI_PROTECTOR_NOTIFY_GLOBAL', 'Global Notifications');
define('_MI_PROTECTOR_NOTIFY_GLOBAL_DESC', 'Global Protector notifications');

define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT', 'Security Threat Detected');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_CAP', 'Notify me when security threats are detected');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_DESC', 'Receive notifications when the system detects security threats');
define('_MI_PROTECTOR_NOTIFY_SECURITY_THREAT_SUBJECT', 'Security Threat Alert: {SITE_NAME}');

define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS', 'Proxy Access Alert');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_CAP', 'Notify me about proxy access events');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_DESC', 'Receive notifications about proxy access events');
define('_MI_PROTECTOR_NOTIFY_PROXY_ACCESS_SUBJECT', 'Proxy Access Alert: {SITE_NAME}');

define('_MI_PROTECTOR_NOTIFICATION_ENABLED', '<h5><a id="enable-notification">🔔</a> Enable Notifications</h5>');
define('_MI_PROTECTOR_NOTIFICATION_ENABLED_DESC', 'Choose how you want to receive notifications from Protector');
define('_MI_PROTECTOR_NOTIFICATION_DISABLE', 'Disable notifications');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_INBOX', 'Enable inbox notifications only');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_EMAIL', 'Enable email notifications only');
define('_MI_PROTECTOR_NOTIFICATION_ENABLE_BOTH', 'Enable both inbox and email notifications');

define('_MI_PROTECTOR_NOTIFICATION_EVENTS', 'Notification Events');
define('_MI_PROTECTOR_NOTIFICATION_EVENTS_DESC', 'Select which events should trigger notifications');

// CORS Proxy settings
define('_MI_PROTECTOR_PROXY_CORS_ORIGIN', '<h5><a id="enable-cors">☁</a> CORS: Allowed Origins</h5>');
define('_MI_PROTECTOR_PROXY_CORS_ORIGIN_DESC', 'Specify which origins are allowed to access resources through the proxy. Use * for all origins or a comma-separated list of domains.');

define('_MI_PROTECTOR_PROXY_CORS_METHODS', 'CORS: Allowed Methods');
define('_MI_PROTECTOR_PROXY_CORS_METHODS_DESC', 'HTTP methods allowed when accessing the resource. Separate multiple methods with commas.');

define('_MI_PROTECTOR_PROXY_CORS_HEADERS', 'CORS: Allowed Headers');
define('_MI_PROTECTOR_PROXY_CORS_HEADERS_DESC', 'Headers that are allowed to be used with the request. Use * for all headers or a comma-separated list.');

define('_MI_PROTECTOR_PROXY_CORS_DEBUG', 'CORS: Debug Mode');
define('_MI_PROTECTOR_PROXY_CORS_DEBUG_DESC', 'Enable debug logging for CORS requests and responses.');

// Content Security Policy settings
define('_MI_PROTECTOR_ENABLE_CSP', '<h5>🛡️ Enable Content Security Policy</h5>');
define('_MI_PROTECTOR_ENABLE_CSP_DESC', 'Activate Content Security Policy (CSP) to help prevent XSS attacks and other code injection attacks');

define('_MI_PROTECTOR_CSP_LEGACY_SUPPORT', 'Add CSP Meta Tag Support');
define('_MI_PROTECTOR_CSP_LEGACY_SUPPORT_DESC', 'Also add CSP as a meta tag for older browsers that don\'t support CSP headers');

define('_MI_PROTECTOR_CSP_DEFAULT_SRC', 'Default Sources');
define('_MI_PROTECTOR_CSP_DEFAULT_SRC_DESC', 'Default policy for loading content such as JavaScript, Images, CSS, Fonts, AJAX requests, Frames, HTML5 Media');

define('_MI_PROTECTOR_CSP_SCRIPT_SRC', 'Script Sources');
define('_MI_PROTECTOR_CSP_SCRIPT_SRC_DESC', 'Defines valid sources of JavaScript. Use \'unsafe-inline\' to allow inline scripts and \'unsafe-eval\' to allow eval()');

define('_MI_PROTECTOR_CSP_STYLE_SRC', 'Style Sources');
define('_MI_PROTECTOR_CSP_STYLE_SRC_DESC', 'Defines valid sources of stylesheets or CSS. Use \'unsafe-inline\' to allow inline styles');

define('_MI_PROTECTOR_CSP_IMG_SRC', 'Image Sources');
define('_MI_PROTECTOR_CSP_IMG_SRC_DESC', 'Defines valid sources of images. Add \'data:\' to allow data: URIs for images');

define('_MI_PROTECTOR_CSP_CONNECT_SRC', 'Connect Sources');
define('_MI_PROTECTOR_CSP_CONNECT_SRC_DESC', 'Defines valid sources for fetch, XMLHttpRequest, WebSocket, and EventSource connections');

define('_MI_PROTECTOR_CSP_FONT_SRC', 'Font Sources');
define('_MI_PROTECTOR_CSP_FONT_SRC_DESC', 'Defines valid sources for fonts loaded using @font-face');

define('_MI_PROTECTOR_CSP_OBJECT_SRC', 'Object Sources');
define('_MI_PROTECTOR_CSP_OBJECT_SRC_DESC', 'Defines valid sources for the <object>, <embed>, and <applet> elements');

define('_MI_PROTECTOR_CSP_MEDIA_SRC', 'Media Sources');
define('_MI_PROTECTOR_CSP_MEDIA_SRC_DESC', 'Defines valid sources for loading media using the <audio>, <video> and <track> elements');

define('_MI_PROTECTOR_CSP_FRAME_SRC', 'Frame Sources');
define('_MI_PROTECTOR_CSP_FRAME_SRC_DESC', 'Defines valid sources for loading frames');

define('_MI_PROTECTOR_CSP_REPORT_URI', 'Report URI');
define('_MI_PROTECTOR_CSP_REPORT_URI_DESC', 'URI to which the browser sends reports about policy violations');

// CSP Reporting
define('_MI_PROTECTOR_NOTIFY_CSP', 'Notify on Critical CSP Violations');
define('_MI_PROTECTOR_NOTIFY_CSP_DESC', 'Send email notifications for critical Content Security Policy violations (script-src)');
define('_MI_PROTECTOR_NOTIFY_ALL_CSP', 'Notify on All CSP Violations');
define('_MI_PROTECTOR_NOTIFY_ALL_CSP_DESC', 'Send email notifications for all Content Security Policy violations (may generate many emails)');

}
