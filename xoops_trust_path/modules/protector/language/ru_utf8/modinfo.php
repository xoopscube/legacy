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
	define( $constpref . '_DESC', 'This module protects your application platform from various attacks like DoS, SQL Injection, and contamination of global variables.' );

	// Menu
	define( $constpref . '_ADMININDEX', 'Protect Center' );
	define( $constpref . '_ADVISORY', 'Security Advisor' );
	define( $constpref . '_PREFIXMANAGER', 'DB Prefix Manager' );
	define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Permissions' );

	// Configs
	define( $constpref . '_GLOBAL_DISBL', 'Temporary disabled' );
	define( $constpref . '_GLOBAL_DISBLDSC', 'All protections are temporary disabled.<br>Remember to turn this off after solving any trouble.' );

	define( $constpref . '_DEFAULT_LANG', 'Default language' );
	define( $constpref . '_DEFAULT_LANGDSC', 'Specify the language set to display messages before processing common.php' );

	define( $constpref . '_RELIABLE_IPS', 'Reliable IPs' );
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
}
