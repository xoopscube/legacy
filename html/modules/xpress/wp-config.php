<?php
/** It is an additional loading for XPressME. */
require_once dirname( __FILE__ ).'/include/add_xpress_config.php' ;

/**
 * mb_language() sets language. If language  is omitted, it returns current language as string.
 * language setting is used for encoding e-mail messages. 
 * Valid languages are "Japanese", "ja","English","en" and "uni" (UTF-8). 
 * mb_send_mail() uses this setting to encode e-mail.
 * Language and its setting is ISO-2022-JP/Base64 for Japanese, UTF-8/Base64 for uni, ISO-8859-1/quoted printable for English. 
 */
 if (function_exists("mb_language")) mb_language('uni');


// ** MySQL settings - You can get this info from your web host ** //
// Do not change  'DB_NAME','DB_USER','DB_PASSWORD' & 'DB_HOST'
// because copies a set value of XOOPS. 

/** Do not change. The name of the database for WordPress */
define('DB_NAME', $xoops_config->xoops_db_name);

/** Do not change. MySQL database username */
define('DB_USER', $xoops_config->xoops_db_user);

/** Do not change. MySQL database password */
define('DB_PASSWORD', $xoops_config->xoops_db_pass);

/** Do not change. MySQL hostname */
define('DB_HOST', $xoops_config->xoops_db_host);
	
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

//define('WP_DEBUG' ,true);

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'put your unique phrase here');
define('SECURE_AUTH_KEY', 'put your unique phrase here');
define('LOGGED_IN_KEY', 'put your unique phrase here');
define('NONCE_KEY', 'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
// Do not change. $table_prefix is generated from XOOPS DB Priefix and the module directory name. 
$table_prefix  = $xoops_config->module_db_prefix;

/**
 * WordPress Localized Language, defaults to Japanese.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 *
 * Example:
 * define ('WPLANG', '');		// language support to English
 */
define ('WPLANG', 'ja');		// language support to Japanese

/* That's all, stop editing! Happy blogging. */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__).'/');

/** Processing for XPressME is done.*/
require_once( ABSPATH .'/include/add_xpress_process.php');

require_once(ABSPATH.'wp-settings.php');
?>