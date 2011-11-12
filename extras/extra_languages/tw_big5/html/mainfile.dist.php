<?php
// $Id: mainfile.dist.php,v 1.1 2008/03/09 02:26:10 minahito Exp $
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

	// XOOPS 實體路徑
	// XOOPS安裝的實體路徑最後勿加斜線。
	// 範例: define('XOOPS_ROOT_PATH', '/path/to/xoops/directory');
    	define('XOOPS_ROOT_PATH', '');
	
    	// XOOPS 安全路徑
    	// 這是選擇性的項目，如果您需要用到請輸入，
    	// 這個路徑必須式安全而瀏覽器無法直接連接的。
    	define('XOOPS_TRUST_PATH', '');

	// XOOPS 網址 (URL)
	// 安裝XOOPS的網址最後勿加斜線。
	// 範例: define('XOOPS_URL', 'http://url_to_xoops_directory');
    	define('XOOPS_URL', 'http://');

	// 資料庫型式
	// 資料庫的使用類型
	define('XOOPS_DB_TYPE', 'mysql');

	// 資料表前置詞
	// 在每個XOOPS使用的資料表前所使用的前置識別代詞.如果沒有特殊設定.可使用預設的 'xoopscube'.
	define('XOOPS_DB_PREFIX', 'xoopscube');

	// 縮寫
	// 這個是為了補充作用來生成編碼和標記， 您不需改變預設值
    	define('XOOPS_SALT', '');

	// 資料庫伺服器位址
	// XOOPS所使用的資料庫伺服器位址,如果不確定可使用 'localhost' 大多數的狀況下應該可以使用.
	define('XOOPS_DB_HOST', 'localhost');

	// 資料庫帳號
	// 使用資料庫的帳號
	define('XOOPS_DB_USER', '');

	// 資料庫密碼
	// 資料庫帳號所使用的密碼
	define('XOOPS_DB_PASS', '');

	// 資料庫名稱
	// XOOPS所使用的資料庫名稱.如果沒有先建立.安裝程式會幫您建立(要有建立資料庫權限的帳號密碼)
	define('XOOPS_DB_NAME', '');

	// 資料庫使用Pconnect模式? (是=1 否=0)
	// 預設為否.如果不知道請選否
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
        include_once XOOPS_ROOT_PATH.'/include/cubecore_init.php';
        if (!isset($xoopsOption['nocommon']) && !defined('_LEGACY_PREVENT_EXEC_COMMON_')) {
            include XOOPS_ROOT_PATH.'/include/common.php';
        }
    }
}
?>