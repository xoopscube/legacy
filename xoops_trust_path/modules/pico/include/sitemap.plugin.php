<?php

function b_sitemap_pico( $mydirname )
{
	include_once dirname(__FILE__).'/common_functions.php' ;

	$submenus = pico_common_get_submenu( $mydirname , 'sitemap_plugin' ) ;
	$show_subcat = @$GLOBALS['sitemap_configs']['show_subcategoris'] ? true : false ;
	$ret = array() ;
	$p_count = 0 ;
	foreach( $submenus as $submenu ) {
		$ret['parent'][$p_count] = array(
			'title' => $submenu['name'] ,
			'url' => $submenu['url'] ,
			'image' => 1 ,
		) ;
		if( $show_subcat && ! empty( $submenu['sub'] ) ) {
			$ret['parent'][$p_count]['child'] = b_sitemap_pico_crawl_submenu( $submenu['sub'] , 2 ) ;
		}
		$p_count ++ ;
	}

	return $ret;
}

function b_sitemap_pico_crawl_submenu( $submenus , $depth = 2 )
{
	$ret = array() ;
	if( $depth > 4 ) $depth = 4 ;
	foreach( $submenus as $subsubmenu ) {
		$ret[] = array(
			'title' => $subsubmenu['name'] ,
			'url' => $subsubmenu['url'] ,
			'image' => $depth ,
		) ;
		if( ! empty( $subsubmenu['sub'] ) ) $ret = array_merge( $ret , b_sitemap_pico_crawl_submenu( $subsubmenu['sub'] , $depth + 1 ) ) ;
	}
	return $ret ;
}

?>