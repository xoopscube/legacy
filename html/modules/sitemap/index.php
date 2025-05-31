<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * 
 * @package    Sitemap
 * @version    2.5.0
 * @author     Gigamaster, 2020 XCL PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL V2.0
 */

require '../../mainfile.php' ;
require_once XOOPS_ROOT_PATH.'/header.php';

if (!defined('XOOPS_ROOT_PATH')) exit();

$sitemap_configs = $xoopsModuleConfig ;

include_once XOOPS_ROOT_PATH.'/modules/sitemap/include/sitemap.php' ;


// All-time guest mode (backup uid & set as Guest)
if( is_object( $xoopsUser ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
	$backup_uid = $xoopsUser->getVar('uid') ;
	$backup_userisadmin = $xoopsUserIsAdmin ;
	unset($xoopsUser);
    $xoopsUser = null;
	$xoopsUserIsAdmin = false ;
}

$sitemap = sitemap_show();

$myts =& MyTextSanitizer::getInstance();

if( ! empty( $backup_uid ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
	$member_handler =& xoops_gethandler('member');
	$xoopsUser =& $member_handler->getUser( $backup_uid ) ;
	if (is_object($xoopsUser)) { // Check user restored successful
	    $xoopsUserIsAdmin = $xoopsUser->isAdmin($xoopsModule->getVar('mid'));
	} else {
	    // Handle case not restored
	    $xoopsUser = null;
	    $xoopsUserIsAdmin = false;
	}
}

// options map & address
if($sitemap_configs['show_sitename'] == 1) {
	$show_sitename = $myts->makeTboxData4Show($xoopsConfig['sitename']);
}
if($sitemap_configs['show_siteslogan'] == 1) {
	$show_slogan   = $myts->makeTboxData4Show($xoopsConfig['slogan']);
} 
if($sitemap_configs['show_site_map'] == 1) {
	$show_map   = $myts->displayTarea( $sitemap_configs['show_map'] , 1 ) ;
} 
if($sitemap_configs['show_site_address'] == 1) {
	$show_address   = $myts->displayTarea( $sitemap_configs['show_address'] , 1 ) ;
} 

// Render
$xoopsOption['template_main'] = 'sitemap_index.html' ;

$xoopsTpl->assign('sitemap', $sitemap);
$xoopsTpl->assign('msgs', $myts->displayTarea( $sitemap_configs['msgs'] , 1 ) ) ;
$xoopsTpl->assign('show_subcategoris', $sitemap_configs["show_subcategoris"]);

// options
$xoopsTpl->assign('sitename', $show_sitename ?? '') ;
$xoopsTpl->assign('slogan', $show_slogan ?? '') ;
$xoopsTpl->assign('map', $show_map ?? '' ) ;
$xoopsTpl->assign('address', $show_address ?? '') ;

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
