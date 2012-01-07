<?php
// $Id: xoops_version.php,v 1.1 2004/01/29 14:45:48 buennagel Exp $
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

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$mydirname = basename( dirname( __FILE__ ) ) ;

$modversion['name'] = _MI_SEARCH_NAME;
$modversion['version'] = 2.05;
$modversion['description'] = _MI_SEARCH_DESC;
$modversion['author'] = "suin(<a href=\"http://suin.asia/\" target=\"_blank\">http://suin.asia/</a>)";
$modversion['credits'] = "suin";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
if(preg_match("/^XOOPS Cube/",XOOPS_VERSION))
	$modversion['image'] = "images/search_xcl.png";
else
	$modversion['image'] = "images/search_logo.png";
$modversion['dirname'] = $mydirname;

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

$modversion['hasMain'] = 1;

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] = "search";

$modversion['blocks'][1]['file'] = "search.php";
$modversion['blocks'][1]['name'] = _MI_SEARCH_BLICK1;
$modversion['blocks'][1]['description'] = _MI_SEARCH_BLICK_DESC1;
$modversion['blocks'][1]['show_func'] = "b_search_search_show";
$modversion['blocks'][1]['template'] = "search_block_search.html";
$modversion['blocks'][1]['can_clone'] = true ;
$modversion['blocks'][2]['file'] = "search.php";
$modversion['blocks'][2]['name'] = _MI_SEARCH_BLICK2;
$modversion['blocks'][2]['description'] = _MI_SEARCH_BLICK_DESC2;
$modversion['blocks'][2]['show_func'] = "b_search_redirect";

$modversion['templates'][1]['file'] = "search_result.html";
$modversion['templates'][1]['description'] = _MI_SEARCH_TEMPLATE_DESC1;
$modversion['templates'][2]['file'] = "search_result_all.html";
$modversion['templates'][2]['description'] = _MI_SEARCH_TEMPLATE_DESC2;
$modversion['templates'][3]['file'] = "search_index.html";
$modversion['templates'][3]['description'] = _MI_SEARCH_TEMPLATE_DESC3;

$modversion['config'][1]['name'] = 'search_display_text';
$modversion['config'][1]['title'] = '_MI_SEARCH_CONFIG1';
$modversion['config'][1]['description'] = '_MI_SEARCH_CONFIG_DESC1';
$modversion['config'][1]['formtype'] = 'yesno';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = 1;
?>