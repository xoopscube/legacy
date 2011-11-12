<?php
// $Id: functions.php,v 1.1 2008/03/09 02:26:08 minahito Exp $
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
function getLanguage() {
	$language_array = array(
			'en' => 'english',
			'ja' => 'japanese',
			'fr' => 'french',
			'de' => 'german',
			'nl' => 'dutch',
			'es' => 'spanish',
			'tw' => 'tw_big5',
			'cn' => 'schinese',
			'ro' => 'romanian'
	);

	$language = 'tw_big5';
	if ( !empty($_POST['lang']) ) {
	    $language = $_POST['lang'];
	} else {
	    if (isset($_COOKIE['install_lang'])) {
	        $language = $_COOKIE['install_lang'];
	    } else {
	        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'ja,en-us;q=0.7,zh-TW;q=0.6';
	        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	            $accept_langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	            foreach ($accept_langs as $al) {
	                $al = strtolower($al);
	                $al_len = strlen($al);
	                if ($al_len > 2) {
	                    if (preg_match('/([a-z]{2});q=[0-9.]+$/', $al, $al_match)) {
	                        $al = $al_match[1];
	                    } else {
	                        continue;
	                    }
	                }
	                if (isset($language_array[$al])) {
	                    $language = $language_array[$al];
	                    break;
	                }
	            }
	        }
	    }
	}
	if ( !file_exists('./language/'.$language.'/install.php') ) {
		$language = 'tw_big5';
	}
	setcookie('install_lang', $language);
	return $language;
}

/*
 * gets list of name of directories inside a directory
 */
function getDirList($dirname)
{
    $dirlist = array();
    if (is_dir($dirname) && $handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if ( !preg_match('/^[.]{1,2}$/',$file) ) {
                if (strtolower($file) != 'cvs' && is_dir($dirname.$file) ) {
                    $dirlist[$file]=$file;
                }
            }
        }
        closedir($handle);
        asort($dirlist);
        reset($dirlist);
    }
    return $dirlist;
}

/*
 * gets list of name of files within a directory
 */
function getImageFileList($dirname)
{
    $filelist = array();
    if (is_dir($dirname) && $handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if (!preg_match('/^[.]{1,2}$/', $file) && preg_match('/[.gif|.jpg|.png]$/i', $file) ) {
                    $filelist[$file]=$file;
            }
        }
        closedir($handle);
        asort($filelist);
        reset($filelist);
    }
    return $filelist;
}

function &xoops_module_gettemplate($dirname, $template, $block=false)
{
    if ($block) {
        $path = XOOPS_ROOT_PATH.'/modules/'.$dirname.'/templates/blocks/'.$template;
    } else {
        $path = XOOPS_ROOT_PATH.'/modules/'.$dirname.'/templates/'.$template;
    }
    if (!file_exists($path)) {
    	$ret = false;
        return $ret;
    } else {
        $lines = file($path);
    }
    if (!$lines) {
    	$ret = false;
        return $ret;
    }
    $ret = '';
    $count = count($lines);
    for ($i = 0; $i < $count; $i++) {
        $ret .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $lines[$i]));
    }
    return $ret;
}

function check_language($language){
    if ( file_exists('./language/'.$language.'/install.php') ) {
        return $language;
    } else {
        return 'english';
    }
}

function b_back($option = null)
{
    if(!isset($option) || !is_array($option)) return '';
    $content = '';
    if(isset($option[0]) && $option[0] != ''){
        $content .= '<input type="button" value="'._INSTALL_L42.'"'.
                    ' onclick="location=\'index.php?op='.htmlspecialchars($option[0]).'\'" />';
    }else{
        $content .= '<input type="button" value="'._INSTALL_L42.'"'.
                    ' onclick="javascript:history.back();" />';
    }
    if(isset($option[1]) && $option[1] != ''){
        $content .= '<span style="font-size:90%;"> &lt;&lt; '.htmlspecialchars($option[1]).'</span>';
    }
    return $content;
}

function b_reload($option=''){
    if(empty($option)) return '';
    if (!defined('_INSTALL_L200')) {
        define('_INSTALL_L200', '重新載入');
    }
    return  '<input type="button" value="'._INSTALL_L200.'" onclick="location.reload();" />';
}

function b_next($option=null){
    if(!isset($option) || !is_array($option)) return '';
    $content = '';
    if(isset($option[1]) && $option[1] != ''){
        $content .= '<span style="font-size:90%;">'.htmlspecialchars($option[1]).' &gt;&gt; </span>';
    }
    $content .= '<input type="hidden" name="op" value="'.htmlspecialchars($option[0]).'" />';
    $content .= '<input type="submit" name="submit" value="'._INSTALL_L47.'" />';
    return $content;
}

?>
