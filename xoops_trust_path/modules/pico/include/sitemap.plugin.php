<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

function b_sitemap_pico( $mydirname ) {
	include_once __DIR__ . '/common_functions.php';

	$submenus = pico_common_get_submenu( $mydirname, 'sitemap_plugin' );

	$show_subcat = @$GLOBALS['sitemap_configs']['show_subcategoris'];

	$ret = [];

	$p_count = 0;

	foreach ( $submenus as $submenu ) {
		$ret['parent'][ $p_count ] = [
			'title' => $submenu['name'],
			'url'   => $submenu['url'],
			'image' => 1,
		];
		if ( $show_subcat && ! empty( $submenu['sub'] ) ) {
			$ret['parent'][ $p_count ]['child'] = b_sitemap_pico_crawl_submenu( $submenu['sub'], 2 );
		}
		$p_count ++;
	}

	return $ret;
}

function b_sitemap_pico_crawl_submenu( $submenus, $depth = 2 ) {
	$ret = [];

	if ( $depth > 4 ) {
		$depth = 4;
	}
	foreach ( $submenus as $subsubmenu ) {
		$ret[] = [
			'title' => $subsubmenu['name'],
			'url'   => $subsubmenu['url'],
			'image' => $depth,
		];
		if ( ! empty( $subsubmenu['sub'] ) ) {
			$ret = array_merge( $ret, b_sitemap_pico_crawl_submenu( $subsubmenu['sub'], $depth + 1 ) );
		}
	}

	return $ret;
}
