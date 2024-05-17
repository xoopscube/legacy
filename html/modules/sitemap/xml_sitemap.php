<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.4.0
 * @author     Gigamaster, 2020 XCL PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

if( ! defined( 'SITEMAP_ROOT_CONTROLLER_LOADED' ) ) {
	if( ! file_exists( __DIR__ .'/modules/sitemap/xml_sitemap.php' ) ) {
		die( "Don't call this file directly" ) ;
	}
	if( ! empty( $_SERVER['REQUEST_URI'] ) ) {
		$_SERVER['REQUEST_URI'] = str_replace( 'xml_sitemap.php' , 'modules/sitemap/xml_sitemap.php' , $_SERVER['REQUEST_URI'] ) ;
	} else {
		$_SERVER['REQUEST_URI'] = '/modules/sitemap/xml_sitemap.php' ;
	}
	$_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'] ;
	define( 'SITEMAP_ROOT_CONTROLLER_LOADED' , 1 ) ;
	$real_xml_google_path = __DIR__ .'/modules/sitemap/xml_sitemap.php' ;
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

$sitemap = sitemap_show();

// Identical to the date() function except that the time returned is Greenwich Mean Time (GMT)
// simple UTC timestamp : gmdate("Y-m-d\TH:i:s\Z");
$xoopsTpl->assign('lastmod', gmdate( 'Y-m-d\TH:i:s\Z' ) ); // TODO
$xoopsTpl->assign('sitemap', $sitemap);
$xoopsTpl->assign('msgs', $myts->displayTarea($msgs,1));
$xoopsTpl->assign('show_subcategoris', $sitemap_configs["show_subcategoris"]);

$xoopsTpl->assign('this', [
'mods' => $xoopsModule->getVar('dirname'), 
'name' => $xoopsModule->getVar('name')
]);

if( is_object( @$xoopsLogger ) ) {
    $xoopsLogger->activated = false;
}
$xoopsTpl->display( 'db:xml_sitemap.html' ) ;
