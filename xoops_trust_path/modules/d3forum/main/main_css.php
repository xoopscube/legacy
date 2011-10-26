<?php

// send header
if( ! headers_sent() ) {
	$cache_limit = 3600 ;

	session_cache_limiter('public');
	header("Expires: ".date('r',intval(time()/$cache_limit)*$cache_limit+$cache_limit));
	header("Cache-Control: public, max-age=$cache_limit");
	header("Last-Modified: ".date('r',intval(time()/$cache_limit)*$cache_limit));
	header('Content-Type: text/css') ;
}

if( is_object( $xoopsUser ) ) {
	$xoops_isuser = true ;
	$xoops_userid = $xoopsUser->getVar('uid') ;
	$xoops_uname = $xoopsUser->getVar('uname') ;
	$xoops_isadmin = $xoopsUserIsAdmin ;
} else {
	$xoops_isuser = false ;
	$xoops_userid = 0 ;
	$xoops_uname = '' ;
	$xoops_isadmin = false ;
}


require_once XOOPS_ROOT_PATH.'/class/template.php' ;
$tpl =& new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'xoops_config' => $xoopsConfig ,
	'mod_config' => $xoopsModuleConfig ,
	'xoops_theme' => $xoopsConfig['theme_set'] ,
	'xoops_imageurl' => XOOPS_THEME_URL.'/'.$xoopsConfig['theme_set'].'/' ,
	'xoops_themecss' => xoops_getcss($xoopsConfig['theme_set']) ,
	'xoops_isuser' => $xoops_isuser ,
	'xoops_userid' => $xoops_userid ,
	'xoops_uname' => $xoops_uname ,
	'xoops_isadmin' => $xoops_isadmin ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_main.css' ) ;
exit ;

?>