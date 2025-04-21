<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2025 Authors
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
