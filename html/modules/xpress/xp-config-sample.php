<?php
/*
 *	This file is a sample of xp-config.php. 
 *	Please use xp-config.php when XPressME cannot acquire the data base connection information more automatically than XOOPS. 
 *
 *  Each definition sets the same value as the one set to mainfile.php of XOOPS.
 */
 
 
 if ( !defined("XPESS_MAINFILE_INCLUDED") ) {
    define("XPESS_MAINFILE_INCLUDED",1);

    // XOOPS Physical Path
    // Physical path to your main XOOPS directory WITHOUT trailing slash
    // Example: define('XP_XOOPS_ROOT_PATH', '/path/to/xoops/directory');
    define('XP_XOOPS_ROOT_PATH', '');
	
    // XOOPS Trusted Path
    // This is option. If you need this path, input value. The trusted path
    // should be a safety directory which web browsers can't access directly.
    define('XP_XOOPS_TRUST_PATH', '');

    // XOOPS Virtual Path (URL)
    // Virtual path to your main XOOPS directory WITHOUT trailing slash
    // Example: define('XP_XOOPS_URL', 'http://url_to_xoops_directory');
    define('XP_XOOPS_URL', 'http://');

    // Database
    // Choose the database to be used
    define('XP_XOOPS_DB_TYPE', 'mysql');

    // Table Prefix
    // This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default 'xoops'.
    define('XP_XOOPS_DB_PREFIX', '');

	// SALT
	// This plays a supplementary role to generate secret code and token.
    define('XP_XOOPS_SALT', '');

    // Database Hostname
    // Hostname of the database server. If you are unsure, 'localhost' works in most cases.
    define('XP_XOOPS_DB_HOST', 'localhost');

    // Database Username
    // Your database user account on the host
    define('XP_XOOPS_DB_USER', '');

    // Database Password
    // Password for your database user account
    define('XP_XOOPS_DB_PASS', '');

    // Database Name
    // The name of database on the host. The installer will attempt to create the database if not exist
    define('XP_XOOPS_DB_NAME', '');

	// Password Salt Key $mainSalt
	// This salt will be appended to passwords in the icms_encryptPass() function.
	// Do NOT change this once your site is Live, doing so will invalidate everyones Password.
	define( 'XP_XOOPS_DB_SALT', '' );
}
?>