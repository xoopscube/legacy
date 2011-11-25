<?php
// $Id: mainfile.dist.php,v 1.2 2007/09/22 06:43:42 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

if ( !defined("XOOPS_MAINFILE_INCLUDED") ) {
    define("XOOPS_MAINFILE_INCLUDED",1);

    // XOOPS Physical Path
    // Physical path to your main XOOPS directory WITHOUT trailing slash
    // Example: define('XOOPS_ROOT_PATH', '/path/to/xoops/directory');
    define('XOOPS_ROOT_PATH', '');
	
    // XOOPS Trusted Path
    // This is option. If you need this path, input value. The trusted path
    // should be a safety directory which web browsers can't access directly.
    define('XOOPS_TRUST_PATH', '');

    // XOOPS Virtual Path (URL)
    // Virtual path to your main XOOPS directory WITHOUT trailing slash
    // Example: define('XOOPS_URL', 'http://url_to_xoops_directory');
    define('XOOPS_URL', 'http://');

    // Database
    // Choose the database to be used
    define('XOOPS_DB_TYPE', 'mysql');

    // Table Prefix
    // This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default 'xoops'.
    define('XOOPS_DB_PREFIX', '');

	// SALT
	// This plays a supplementary role to generate secret code and token.
    define('XOOPS_SALT', '');

    // Database Hostname
    // Hostname of the database server. If you are unsure, 'localhost' works in most cases.
    define('XOOPS_DB_HOST', 'localhost');

    // Database Username
    // Your database user account on the host
    define('XOOPS_DB_USER', '');

    // Database Password
    // Password for your database user account
    define('XOOPS_DB_PASS', '');

    // Database Name
    // The name of database on the host. The installer will attempt to create the database if not exist
    define('XOOPS_DB_NAME', '');

    // Use persistent connection? (Yes=1 No=0)
    // Default is 'No'. Choose 'No' if you are unsure.
    define('XOOPS_DB_PCONNECT', 0);

    define("XOOPS_GROUP_ADMIN", "1");
    define("XOOPS_GROUP_USERS", "2");
    define("XOOPS_GROUP_ANONYMOUS", "3");

    // You can select two special module process excuting mode with defining following constants
    //
    //  define('_LEGACY_PREVENT_LOAD_CORE_', 1);
    //    Module process will not load any XOOPS Cube classes.
    //    You cannot use any XOOPS Cube functions and classes.
    //    (eg. It'll be used for reffering only MySQL Database definition.)
    //
    //  define('_LEGACY_PREVENT_EXEC_COMMON_', 1);
    //    Module process will load XOOPS Cube Root class and initialize Controller class.
    //    You can use some XOOPS Cube functions in this mode.
    //    You can use more XOOPS Cube functions (eg. xoops_gethandler), if you write
    //       $root=&XCube_Root::getSingleton();
    //       $root->mController->executeCommonSubset();
    //    after including mainfile.php.
    //    It is synonym of $xoopsOption['nocommon']=1; 
    //    But $xoopsOption['nocommon'] is deprecated.
    //
    if (!defined('_LEGACY_PREVENT_LOAD_CORE_') && XOOPS_ROOT_PATH != '') {
        include XOOPS_TRUST_PATH.'/modules/protector/include/precheck.inc.php' ;
        include_once XOOPS_ROOT_PATH.'/include/cubecore_init.php';
        if (!isset($xoopsOption['nocommon']) && !defined('_LEGACY_PREVENT_EXEC_COMMON_')) {
            include XOOPS_ROOT_PATH.'/include/common.php';
        }
        include XOOPS_TRUST_PATH.'/modules/protector/include/postcheck.inc.php' ;
    }
}
?>