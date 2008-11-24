<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  ------------------------------------------------------------------------ //
//                XOOPS Korean xoopsmailerlocal ( by wanikoo[ wani@wanisys.net ])	 //
//                       <http://www.wanisys.net/>                            				 //
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


class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){
		$this->XoopsMailer();
		$this->charSet = 'euc-kr';
		$this->encoding = 'base64';
	}

	function encodeFromName($text){
		return $this->UTF8toEUCKR($text);
	}

	function encodeSubject($text){
		return $this->UTF8toEUCKR($text);
	}

	function encodeBody(&$text){
		$text = $this->UTF8toEUCKR($text);
	}

	
	function UTF8toEUCKR($str){

	if (function_exists('iconv')) {
			$str = is_string($str) ? @iconv("UTF-8","EUC-KR",$str): $str;
			return $str;
	}
	elseif (function_exists('mb_convert_encoding')) {
			$str = is_string($str) ? @mb_convert_encoding($str, "EUC-KR", "UTF-8"): $str;
			return $str;
	}
	else
	{
		return $str;
	}

	}

}
?>