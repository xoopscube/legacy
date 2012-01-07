<?php
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
function b_search_search_show()
{
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$block = array();
	$block['lang_search'] = _MB_SEARCH_SEARCH;
	$block['lang_advsearch'] = _MB_SEARCH_ADVS;
	$block['mydirname'] = $mydirname;
	return $block;
}

function b_search_redirect(){
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	$request_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	if( preg_match("|".XOOPS_URL."/search.php|", $request_url ) ){
		//$c = "true";
		header("Location: ".XOOPS_URL."/modules/".$mydirname."/index.php?".$_SERVER['QUERY_STRING']);
		exit();
	}
	//$block['content'] = $c;
	//return $block;
	return false;
}
?>