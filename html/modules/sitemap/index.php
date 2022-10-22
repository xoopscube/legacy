<?php

require '../../mainfile.php' ;


$xoopsOption['template_main'] = 'sitemap_index.html' ;
include XOOPS_ROOT_PATH.'/header.php' ;

$sitemap_configs = $xoopsModuleConfig ;
include_once XOOPS_ROOT_PATH.'/modules/sitemap/include/sitemap.php' ;


// for All-time guest mode (backup uid & set as Guest)
if( is_object( $xoopsUser ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
	$backup_uid = $xoopsUser->getVar('uid') ;
	$backup_userisadmin = $xoopsUserIsAdmin ;
	$xoopsUser = '' ;
	$xoopsUserIsAdmin = false ;
}

$sitemap = sitemap_show();

// for All-time guest mode (restore $xoopsUser*)
if( ! empty( $backup_uid ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
	$member_handler =& xoops_gethandler('member');
	$xoopsUser =& $member_handler->getUser( $backup_uid ) ;
	$xoopsUserIsAdmin = $backup_userisadmin ;
}

$myts =& MyTextSanitizer::getInstance();

$xoopsTpl->assign('sitemap', $sitemap);
$xoopsTpl->assign('msgs', $myts->displayTarea( $sitemap_configs['msgs'] , 1 ) ) ;
$xoopsTpl->assign('show_subcategoris', $sitemap_configs["show_subcategoris"]);

if( $sitemap_configs['alltime_guest'] ) {
	$xoopsTpl->assign( 'isuser' , 0 ) ;
	$xoopsTpl->assign( 'isadmin' , 0 ) ;
} else {
	$xoopsTpl->assign( 'isuser' , is_object( $xoopsUser ) ) ;
	$xoopsTpl->assign( 'isadmin' , $xoopsUserIsAdmin ) ;
}

$xoopsTpl->assign('this', [
	'mods' => $xoopsModule->getVar('dirname'),
	'name' => $xoopsModule->getVar('name')
]);

include XOOPS_ROOT_PATH . '/footer.php';
