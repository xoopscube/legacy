<?php
$modname= $xoops_config->module_name;
$modurl = $xoops_config->module_url;
$hash = md5($modurl);
/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('USER_COOKIE') )
	define('USER_COOKIE', 'wordpress_' . $modname . '_user_' . $hash);

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('PASS_COOKIE') )
	define('PASS_COOKIE', 'wordpress_' . $modname . '_pass_' . $hash);

/**
 * It is possible to define this in wp-config.php
 * @since 2.5.0
 */
if ( !defined('AUTH_COOKIE') )
	define('AUTH_COOKIE', 'wordpress_' . $modname . '_auth_' . $hash);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('SECURE_AUTH_COOKIE') )
	define('SECURE_AUTH_COOKIE', 'wordpress_' . $modname . '_sec_' . $hash);

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('LOGGED_IN_COOKIE') )
	define('LOGGED_IN_COOKIE', 'wordpress_' . $modname . '_logged_in_' . $hash);

/**
 * It is possible to define this in wp-config.php
 * @since 2.3.0
 */
if ( !defined('TEST_COOKIE') )
	define('TEST_COOKIE', 'wordpress_' . $modname . '_test_cookie');

/**
 * It is possible to define this in wp-config.php
 * @since 1.2.0
 */
if ( !defined('COOKIEPATH') )
	define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', $xoops_config->xoops_url . '/' ) );

/**
 * It is possible to define this in wp-config.php
 * @since 1.5.0
 */
if ( !defined('SITECOOKIEPATH') )
	define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', $xoops_config->xoops_url  . '/' ) );

/**
 * It is possible to define this in wp-config.php
 * @since 2.6.0
 */
if ( !defined('ADMIN_COOKIE_PATH') )
	define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH);

/**
 * It is possible to define this in wp-config.php
 * @since 2.0.0
 */
if ( !defined('COOKIE_DOMAIN') )
	define('COOKIE_DOMAIN', false);


?>