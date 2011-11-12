<?php 
// $Id: xoops_version.php,v 1.8 2005/06/03 01:35:02 phppp Exp $
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
// Author: toemon                                                      //
// URL: http://www.toemon.com                       //
// ------------------------------------------------------------------------- //
$dir_path = dirname(dirname(__FILE__));
$dir_name = basename($dir_path);

$lang = @$GLOBALS["xoopsConfig"]['language'];


if( file_exists( $dir_path .'/language/'.$lang.'/modinfo.php' ) ) {
	include_once $dir_path .'/language/'.$lang.'/modinfo.php' ;
} else if( file_exists(  $dir_path .'/language/english/modinfo.php' ) ) {
	include_once $dir_path .'/language/english/modinfo.php' ;
}



$i=0;
$adminmenu[$i]['title'] = _MI_XP2_MENU_SYS_INFO ;
$adminmenu[$i++]['link'] = "admin/index.php";

//$adminmenu[$i]['title'] = _MI_XP2_MENU_BLOCK_ADMIN ;
//$adminmenu[$i++]['link'] = "admin/admin_blocks.php";

$adminmenu[$i]['title'] = _MI_XP2_MENU_BLOCK_CHECK ;
$adminmenu[$i++]['link'] = "admin/block_check.php";

$adminmenu[$i]['title'] = _MI_XP2_MENU_WP_ADMIN ;
$adminmenu[$i++]['link'] = "admin/wp_admin.php";

$adminmenu[$i]['title'] = _MI_XP2_MENU_TO_MODULE ;
$adminmenu[$i++]['link'] = "index.php";

$adminmenu[$i]['title'] = _MI_XP2_TO_UPDATE ;
$adminmenu[$i++]['link'] = "admin/update.php";

?>