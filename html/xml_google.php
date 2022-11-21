<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     chanoir
 * @copyright  Copyright 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/docs/GPL_V2.txt
 */

if( ! defined( 'SITEMAP_ROOT_CONTROLLER_LOADED' ) ) {
	if( ! file_exists( __DIR__ .'/modules/sitemap/xml_google.php' ) ) {
		die( "Don't call this file directly" ) ;
	}
	if( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = str_replace( 'xml_google.php' , 'modules/sitemap/xml_google.php' , $_SERVER['REQUEST_URI'] ) ;
	} else {
		$_SERVER['REQUEST_URI'] = '/modules/sitemap/xml_google.php' ;
	}
	$_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'] ;
	define( 'SITEMAP_ROOT_CONTROLLER_LOADED' , 1 ) ;
	$real_xml_google_path = __DIR__ .'/modules/sitemap/xml_google.php' ;
	chdir( './modules/sitemap/' ) ;
	require $real_xml_google_path ;
	exit ;
    } else {
	require '../../mainfile.php' ;
}

$sitemap_configs = @$xoopsModuleConfig ;
$sitemap_configs['alltime_guest'] = true ;

require_once XOOPS_ROOT_PATH.'/class/template.php' ;

$myts =& MyTextSanitizer::getInstance() ;

$sitemap_configs['with_lastmod'] = true ;


if( function_exists( 'mb_http_output' ) ) {
	mb_http_output('pass');
}
header( 'Content-Type:text/xml; charset=utf-8' ) ;

include_once XOOPS_ROOT_PATH.'/modules/sitemap/include/sitemap.php' ;

$xoopsTpl = new XoopsTpl() ;

// for All-time guest mode (backup uid & set as Guest)
//if( is_object( $xoopsUser ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
//	$backup_uid = $xoopsUser->getVar('uid') ;
//	$xoopsUser = '' ;
//	$xoopsUserIsAdmin = false ;
//	$xoopsTpl->assign(array('xoops_isuser' => false, 'xoops_userid' => 0, 'xoops_uname' => '', 'xoops_isadmin' => false));
//}

$sitemap = sitemap_show();

// for All-time guest mode (restore $xoopsUser*)
//if( ! empty( $backup_uid ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
//	$member_handler =& xoops_gethandler('member');
//	$xoopsUser =& $member_handler->getUser( $backup_uid ) ;
//	$xoopsUserIsAdmin = $xoopsUser->isAdmin();
//}

$xoopsTpl->assign('lastmod', gmdate( 'Y-m-d\TH:i:s\Z' ) ); // TODO
$xoopsTpl->assign('sitemap', $sitemap);
$xoopsTpl->assign('msgs', $myts->displayTarea($msgs,1));
$xoopsTpl->assign('show_subcategoris', $sitemap_configs["show_subcategoris"]);

$xoopsTpl->assign('this', array(
	'mods' => $xoopsModule->getVar('dirname'),
	'name' => $xoopsModule->getVar('name')
));

if( is_object( @$xoopsLogger ) ) {
    $xoopsLogger->activated = false;
}
$xoopsTpl->display( 'db:sitemap_xml_google.html' ) ;
