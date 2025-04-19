<?php

// Altsys admin menu and breadcrumbs
define( '_MI_PROTECTOR_ADMENU_MYLANGADMIN' , 'Language');
define( '_MI_PROTECTOR_ADMENU_MYTPLSADMIN' , 'Templates');

// Headings
define( '_AM_TH_DATETIME' , 'Time');
define( '_AM_TH_USER' , 'User');
define( '_AM_TH_IP' , 'IP');
define( '_AM_TH_IP_BAN' , 'Banned IPs');
define( '_AM_TH_AGENT' , 'AGENT');
define( '_AM_TH_TYPE' , 'Type');
define( '_AM_TH_DESC' , 'Description');
define( '_AM_TH_INFO' , 'Overview');
define( '_AM_TH_TIPS' , 'Solution');
define( '_AM_TH_URI' , 'URI');
define( '_AM_PAGE_PREV' , 'Previous');
define( '_AM_PAGE_NEXT' , 'Next');
define( '_AM_CLEARLOG' , 'Clear log');

define( '_AM_TH_BADIPS' , 'The list of banned IPs<br><br>Write each IP address in a different line.<br>Blank means all IPs are allowed<br>The abbreviation of IPv6 address "::" and abbreviation of "0" can not be used.');
define( '_AM_TH_GROUP1IPS' , 'Allowed IPs for Group=1<br><br>Write each IP address in a different line.<br>The abbreviation of IPv6 address "::" and abbreviation of "0" can not be used.<br>192.168. means 192.168.*<br>Blank means all IPs are allowed</span>');

define( '_AM_LABEL_COMPACTLOG' , 'Compact log');
define( '_AM_BUTTON_COMPACTLOG' , 'Compact');
define( '_AM_JS_COMPACTLOGCONFIRM' , 'Duplicated (IP,Type) records will be removed. Do you want to proceed ?');
define( '_AM_LABEL_REMOVEALL' , 'Remove all records');
define( '_AM_BUTTON_REMOVEALL' , 'Remove all');
define( '_AM_JS_REMOVEALLCONFIRM' , 'All logs will be completely removed. Do you want to proceed ?');
define( '_AM_LABEL_REMOVE' , 'Remove the selected records');
define( '_AM_BUTTON_REMOVE' , 'Remove');
define( '_AM_JS_REMOVECONFIRM' , '¨The logs will be completely removed. Do you want to proceed ?');
define( '_AM_MSG_IPFILESUPDATED' , 'Files for IPs have been updated');
define( '_AM_MSG_BADIPSCANTOPEN' , 'The file for badip cannot be opened');
define( '_AM_MSG_GROUP1IPSCANTOPEN' , 'The file for allowing group=1 cannot be opened');
define( '_AM_MSG_REMOVED' , 'Records were removed');
define( '_AM_FMT_CONFIGSNOTWRITABLE' , 'Turn the configs directory writable: %s');

// prefix_manager.php
define( '_AM_H3_PREFIXMAN' , 'Prefix Manager');
define( '_AM_MSG_DBUPDATED' , 'Database Updated Successfully!');
define( '_AM_CONFIRM_DELETE' , 'All data will be dropped. Do you want to proceed ?');
define( '_AM_TXT_HOWTOCHANGEDB' , "<p>Modifying the database prefix requires editing the file :</p><p><code>%s/mainfile.php </code><br><code class='lang-php'>define('XOOPS_DB_PREFIX', '<b>%s</b>');</code></p>");


// advisory.php
define( '_AM_ADV_NOTSECURE' , 'Not secure');
define( '_AM_ADV_TITLE' , 'Protector Security Advisor');
define( '_AM_ADV_TITLE_TIP' , 'Protector Security Advisor audits your system configuration for potential security risks and vulnerabilities, offering recommendations and fixes to improve the site security.');
define( '_AM_ADV_NGINX' , 'You should keep in mind, that NginX does not manage php processes like Apache and you might need to configure either php-fpm, or php-cgi');
define( '_AM_ADV_NGINX_VAR' , 'Server software var dump');
define( '_AM_ADV_SERVER' , 'Server Software');
define( '_AM_ADV_ENV' , 'The web server creates the entries bellow. However, not all web servers guarantee to provide every listed variable; 
some may omit entries or include others not shown. You can generally expect the variables defined in the CGI/1.1 specification to be present.');
define( '_AM_ADV_ENV_LABEL' , 'Server and execution environment information');
define( '_AM_ADV_APACHE' , 'The Apache functions are only available when running PHP as an Apache module.<br>
    Furthermore, some web servers configuration might not return the value for');

// Mainfile
define( '_AM_ADV_MAIN_INFO' , 'Your mainfile.php is properly patched and includes both pre-check and post-check Protector security measures. required postcheck!');
define( '_AM_ADV_MAIN_DESC' , 'mainfile.php required postcheck!');
define( '_AM_ADV_MAIN_TIPS' , 'mainfile.php postcheck!');
define( '_AM_ADV_MAINUNPATCHED' , 'Both pre-check and post-check are required. Refer to the module documentation to edit your mainfile.php');
define( '_AM_ADV_MAIN_PRECHECK' , 'Missing required precheck!');
define( '_AM_ADV_MAIN_POSTCHECK' , 'Missing required postcheck!');

// TRUST PATH
define( '_AM_ADV_TRUSTPATH_PUBLIC_LINK' , 'Click here !');
define( '_AM_ADV_TRUSTPATH_PUBLIC' , 'TRUST_PATH is not installed properly if the image -NG- is visible<br>
    or the link does not shows a 403 Forbidden Error.<br>
    The directory must be protected to return an Error 404, 403 or 500 !');
define( '_AM_ADV_TRUSTPATH_DESC' , 'The safest place for the TRUST_PATH is outside of public DocumentRoot.<br>
    If this is not possible, increase security using Apache .htaccess file or equivalent Nginx directives.');
define( '_AM_ADV_TRUSTPATH_TIPS' , 'Create a .htaccess file in your TRUST_PATH directory. Then open the .htaccess file and write this directive "Deny from all"<br>
    Since Nginx does not have an equivalent to the .htaccess file (i.e. no directory level configuration files),
    you need to update the main configuration and reload Nginx for any changes to take effect. By default, the configuration file is named <b>nginx.conf</b> and placed in the directory :
    <ul><li><code>/usr/local/nginx/conf</code>
    <li><code>/etc/nginx</code>
    <li><code>/usr/local/etc/nginx</code>
    </ul>');

// allow_url_fopen
define( '_AM_ADV_FOPEN' , 'Allow url fopen');
define( '_AM_ADV_FOPEN_ON' , 'It is recommended to turn off <b>allow_url_include</b><br>
    This setting allows attackers to execute arbitrary scripts on remote servers. Change this option or claim it to your web hosting service.');
define( '_AM_ADV_FOPEN_DESC' , '<p>The PHP configuration directive allow_url_fopen is enabled.
    When enabled, this directive allows data retrieval from remote locations, allowing files to be included from external sources
    (web site or FTP server). A large number of code injection vulnerabilities reported in PHP-based web applications are caused by the combination
    of enabling allow_url_fopen and bad input filtering.</p>');
define( '_AM_ADV_FOPEN_TIPS' , '<p>You can disable <b>allow_url_fopen</b> from <b>.htaccess</b> or <b>php.ini</b><br>
    if the mod_rewrite module is enabled in Apache, you can insert this line into the .htaccess file of your public root folder:<br>
    <b>php_flag allow_url_fopen off</b></br>
    or disable this php feature in your "php.ini":<br>
    <b>allow_url_fopen , "off"</b></p>');

// session.use_trans_sid
define( '_AM_ADV_SESSION_ERROR' , 'SESSION_ERROR');
define( '_AM_ADV_SESSION_ON' , 'It is recommended to turn off <b>session.use_trans_sid</b><br>
    Otherwise PHP will pass the session ID via the URL.');
define( '_AM_ADV_SESSION_DESC' , 'When use_trans_sid is enabled, PHP will pass the session ID via the URL. This makes the application more vulnerable to session hijacking attacks.
     Session hijacking is basically a form of identity theft wherein a hacker impersonates a legitimate user by stealing his session ID.
     When the session token is transmitted in a cookie, and the request is made over SSL, the token is secure.');
define( '_AM_ADV_SESSION_TIPS' , 'You can disable <b>session.use_trans_sid</b> from <b>.htaccess</b> or <b>php.ini</b><br>
    if the mod_rewrite module is enabled in Apache, you can insert this line into the .htaccess file of your public root folder:<br>
    <b>php_flag session.use_trans_sid off</b><br>
    or disable this php feature in your "php.ini":<br>
    <b>session.use_trans_sid , "off"</b>');

// Database
define( '_AM_ADV_DBPREFIX_ON' , "Changing the default <b>database prefix</b> is recommended for enhanced security and easier multi-site setup.");
define( '_AM_ADV_DBPREFIX_DESC' , "Default database prefixes are common targets. A custom prefix increases security. Use <b>Database Prefix Manager</b> to backup and change the prefix.");
define( '_AM_ADV_DBPREFIX_TIPS' , 'You can use the Prefix Manager to manage, save, and modify the database prefix.<br> <a class="button" href="index.php?page=prefix_manager">Prefix manager</a>');

// Database factory
define( '_AM_ADV_DBFACTORYPATCHED' , 'Your databasefactory is ready for DBLayer Trapping Anti-SQL-Injection');
define( '_AM_ADV_DBFACTORYUNPATCHED' , 'Enable Protector to secure your database!');
define( '_AM_ADV_DBFACTORY_ON' , 'If Protector is enable but your databasefactory is not ready for DBLayer Trapping anti-SQL-Injection, a patch or update is required !');
define( '_AM_ADV_DBFACTORY_DESC' , 'SQL injection (SQLi) refers to an injection attack wherein an attacker can execute malicious SQL statements that control a web app database server.
    Protector ensures parameterized queries when dealing with SQL queries that contains user input.');
define( '_AM_ADV_DBFACTORY_TIPS' , 'Parameterized queries allows the database to understand which parts of the SQL query should be considered as user input, therefore solving SQL injection.
    To enable this feature, an update is required. Or patch the file <b>class/database/databasefactory.php</b>');

// Test Protector
define( '_AM_ADV_SUBTITLECHECK' , 'Test Protector Security');
define( '_AM_ADV_CHECKCONTAMI' , 'Prevent injection vulnerabilities');
define( '_AM_ADV_CHECKISOCOM' , 'Prevent isolated comments');

// Admin constants v4.0
define('_AM_PROTECTOR_EXPORT', 'Export Data');
define('_AM_PROTECTOR_DOWNLOAD', 'Download');
define('_AM_PROTECTOR_DOWNLOAD_TXT', 'Download as Text');
define('_AM_PROTECTOR_DOWNLOAD_CSV', 'Download as CSV');
define('_AM_PROTECTOR_DASHBOARD', 'Dashboard');
define('_AM_PROTECTOR_INFORMATION', 'Module Information');
define('_AM_PROTECTOR_VERSION', 'Version');
define('_AM_PROTECTOR_STATUS', 'Status');
define('_AM_PROTECTOR_ENABLED', 'Enabled');
define('_AM_PROTECTOR_DISABLED', 'Disabled');
define('_AM_PROTECTOR_QUICKLINKS', 'Quick Links');
define('_AM_PROTECTOR_IPSAFELIST', 'IP Safe List');
define('_AM_PROTECTOR_IPSAFELISTDESC', 'IPs listed here will be exempt from Protector\'s security checks');
define('_AM_PROTECTOR_IPSAFELISTFORMAT', 'Each IP should be on a separate line. You can use CIDR notation (e.g. 192.168.1.0/24)');
define('_AM_PROTECTOR_UPDATE', 'Update');
define('_AM_PROTECTOR_UPDATED', 'Settings have been updated');
define('_AM_PROTECTOR_IMPORT', 'Import Data');
define('_AM_PROTECTOR_UPLOAD', 'Upload');
define('_AM_PROTECTOR_EXPORT_TIPS', 'Export your data to a TXT or CSV file so that you can restore them later.');
define('_AM_PROTECTOR_IMPORT_TIPS', 'Upload a previously exported TXT or CSV file to restore your settings.');
define('_AM_PROTECTOR_IMPORT_ERROR', 'Error uploading file. Please try again.');
define('_AM_PROTECTOR_IMPORT_SUCCESS', 'Logs imported successfully.');

// Advisory test
define('_AM_ADV_PROTECTION_DISABLED', 'Protector module is installed but protection is globally disabled!');
define('_AM_ADV_PROTECTION_DISABLED_INFO', 'The Protector module is installed and the database factory is patched, but protection is disabled in the module configuration.');
define('_AM_ADV_PROTECTION_DISABLED_TIPS', 'If this is a production site, it is recommended to enable protection by setting "All protections are temporary disabled" to "No" in the module preferences.');

// Threat Intelligence
define('_AM_PROTECTOR_THREAT_INTELLIGENCE', 'Threat Intelligence');
define('_AM_PROTECTOR_THREAT_INTELLIGENCE_DESC', 'Configure threat intelligence settings to protect your site from malicious IPs and known threats.');
define('_AM_PROTECTOR_THREAT_INTELLIGENCE_DASHBOARD', 'Threat Intelligence Dashboard');
define('_AM_PROTECTOR_THREAT_INTELLIGENCE_SETTINGS', 'Threat Intelligence Settings');
define('_AM_PROTECTOR_NOTHREATSTATS', 'No threat intelligence events recorded yet');

// HTTP:BL Settings
define('_AM_PROTECTOR_HTTPBL_SETTINGS', 'HTTP:BL Settings');
define('_AM_PROTECTOR_HTTPBL_ENABLED', 'Enable HTTP:BL');
define('_AM_PROTECTOR_HTTPBL_KEY', 'HTTP:BL API Key');
define('_AM_PROTECTOR_HTTPBL_KEY_DESC', 'Enter your Project Honeypot HTTP:BL API key. Get one at projecthoneypot.org');
define('_AM_PROTECTOR_HTTPBL_THREAT_THRESHOLD', 'Threat Threshold');
define('_AM_PROTECTOR_HTTPBL_THREAT_THRESHOLD_DESC', 'IPs with a threat score above this value will be blocked (0-255). Recommended: 25-50');
define('_AM_PROTECTOR_HTTPBL_TEST', 'Test Connection');
define('_AM_PROTECTOR_HTTPBL_TEST_SUCCESS', 'HTTP:BL connection successful! Your API key is working correctly.');
define('_AM_PROTECTOR_HTTPBL_TEST_FAILURE', 'HTTP:BL connection failed. Please check your API key and try again.');

// Feed Settings
define('_AM_PROTECTOR_FEED_SETTINGS', 'Threat Feed Settings');
define('_AM_PROTECTOR_FEED_URLS', 'Threat Feed URLs');
define('_AM_PROTECTOR_FEED_URLS_DESC', 'Enter URLs for IP blacklists, one per line. Supported formats: CSV, TXT with one IP per line');

// Check Points
define('_AM_PROTECTOR_CHECK_POINTS', 'Check Points');
define('_AM_PROTECTOR_CHECK_LOGIN', 'Check Login Attempts');
define('_AM_PROTECTOR_CHECK_LOGIN_DESC', 'Verify IPs against threat intelligence during login attempts');
define('_AM_PROTECTOR_CHECK_REGISTER', 'Check Registration');
define('_AM_PROTECTOR_CHECK_REGISTER_DESC', 'Verify IPs against threat intelligence during user registration');
define('_AM_PROTECTOR_CHECK_FORMS', 'Check Form Submissions');
define('_AM_PROTECTOR_CHECK_FORMS_DESC', 'Verify IPs against threat intelligence during any form submission (may impact performance)');
define('_AM_PROTECTOR_CHECK_ADMIN', 'Check Admin Access');
define('_AM_PROTECTOR_CHECK_ADMIN_DESC', 'Verify IPs against threat intelligence during admin area access');

// Cache Settings
define('_AM_PROTECTOR_CACHE_SETTINGS', 'Cache Settings');
define('_AM_PROTECTOR_CACHE_DURATION', 'Cache Duration');
define('_AM_PROTECTOR_CACHE_1HOUR', '1 Hour');
define('_AM_PROTECTOR_CACHE_6HOURS', '6 Hours');
define('_AM_PROTECTOR_CACHE_1DAY', '1 Day');
define('_AM_PROTECTOR_CACHE_1WEEK', '1 Week');

// Notification section
define('_AM_PROTECTOR_NOTIFICATIONS', 'Notifications');
define('_AM_PROTECTOR_NOTIFICATIONS_DESC', 'Protector can send notifications when security events or proxy access attempts occur.');
define('_AM_PROTECTOR_NOTIFICATIONS_AVAILABLE', 'Available Notifications');
define('_AM_PROTECTOR_NOTIFICATIONS_ADMINS', 'Security threats will automatically trigger notifications to all administrators and webmasters.<br>To manage email delivery of these alerts, check your profile preferences ("Notification Method")<br>or the module message ("settings").');
define('_AM_PROTECTOR_NOTIFY_SECURITY_EVENTS', 'Security Events');
define('_AM_PROTECTOR_NOTIFY_SECURITY_EVENTS_DESC', 'Receive notifications when security threats are detected');
define('_AM_PROTECTOR_NOTIFY_PROXY_EVENTS', 'Proxy Access');
define('_AM_PROTECTOR_NOTIFY_PROXY_EVENTS_DESC', 'Receive notifications when someone uses the web proxy');
define('_AM_PROTECTOR_NOTIFICATIONS_MANAGE', 'Manage Your Notifications');
define('_AM_PROTECTOR_MANAGE_NOTIFICATIONS', 'Manage Notifications');
define('_AM_PROTECTOR_SUBSCRIBE_ADMINS', 'Subscribe All Webmasters to Notifications');
define('_AM_PROTECTOR_ADMINS_SUBSCRIBED', 'All webmasters have been subscribed to security notifications.');
define('_AM_PROTECTOR_SUBSCRIPTION_ERROR', 'There was an error subscribing some users to notifications.');

// Notification tabs and tests

define('_AM_PROTECTOR_NOTIFICATION_SUBSCRIBE', 'Subscribe Admins');
define('_AM_PROTECTOR_NOTIFICATION_TEST', 'Test Notifications');
define('_AM_PROTECTOR_NOTIFICATION_TEST_DESC', 'Click on a button below to trigger the notification system for different threat levels.');
define('_AM_PROTECTOR_NOTIFICATION_SENT', 'Test notification for level %d has been sent. Check your email.');

// CSP Violations
define('_AM_PROTECTOR_CSP_VIOLATIONS', 'Content Security Policy Violations');
define('_AM_PROTECTOR_CSP_DISABLED', 'Content Security Policy is currently disabled. Enable it in module preferences.');
define('_AM_PROTECTOR_CSP_NO_VIOLATIONS', 'No CSP violations have been reported.');
define('_AM_PROTECTOR_CSP_CLEAR_ALL', 'Clear All Violations');
define('_AM_PROTECTOR_CSP_CONFIRM_CLEAR', 'Are you sure you want to delete all CSP violation reports?');
define('_AM_PROTECTOR_CSP_CLEARED', 'All CSP violations have been cleared.');
define('_AM_PROTECTOR_CSP_DELETED', 'CSP violation has been deleted.');
define('_AM_PROTECTOR_CSP_INVALID_ID', 'Invalid violation ID.');
define('_AM_PROTECTOR_CSP_NOT_FOUND', 'CSP violation not found.');
define('_AM_PROTECTOR_CSP_VIEW_TITLE', 'CSP Violation Details');
define('_AM_PROTECTOR_CSP_TIME', 'Time');
define('_AM_PROTECTOR_CSP_IP', 'IP Address');
define('_AM_PROTECTOR_CSP_DOCUMENT_URI', 'Document URI');
define('_AM_PROTECTOR_CSP_VIOLATED_DIRECTIVE', 'Violated Directive');
define('_AM_PROTECTOR_CSP_BLOCKED_URI', 'Blocked URI');
define('_AM_PROTECTOR_CSP_SOURCE_FILE', 'Source File');
define('_AM_PROTECTOR_CSP_LINE_NUMBER', 'Line Number');
define('_AM_PROTECTOR_CSP_COLUMN_NUMBER', 'Column Number');
define('_AM_PROTECTOR_CSP_REFERRER', 'Referrer');
define('_AM_PROTECTOR_CSP_USER_AGENT', 'User Agent');
define('_AM_PROTECTOR_CSP_ACTIONS', 'Actions');
define('_AM_PROTECTOR_CSP_VIEW', 'View');
define('_AM_PROTECTOR_CSP_DELETE', 'Delete');
define('_AM_PROTECTOR_BACK', 'Back');
