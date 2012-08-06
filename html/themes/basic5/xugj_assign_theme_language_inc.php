<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;
$theme_name = basename( dirname(__FILE__) ) ;
if ( file_exists(XOOPS_THEME_PATH.'/'.$theme_name.'/language/'.$GLOBALS['xoopsConfig']['language'].'.php') ) {
 include_once XOOPS_THEME_PATH.'/'.$theme_name.'/language/'.$GLOBALS['xoopsConfig']['language'].'.php';
}else{
 include_once XOOPS_THEME_PATH.'/'.$theme_name.'/language/english.php';
}

?>