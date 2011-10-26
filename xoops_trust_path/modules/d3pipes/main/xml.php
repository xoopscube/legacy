<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

// GET
$rss_styles = array( 'rss20' , 'rss10' , 'atom' , 'sitemap' ) ;
$style = in_array( @$_GET['style'] , $rss_styles ) ? $_GET['style'] : $rss_styles[0] ;
$max_entries = $style == 'sitemap' ? $xoopsModuleConfig['entries_per_sitemap'] : $xoopsModuleConfig['entries_per_rss'] ;
$link_prefer = @$_GET['link'] == 'clipping' ? 'clipping' : 'original' ;

// fetch pipe_id
$pipe_id = intval( @$_GET['pipe_id'] ) ;

if( $pipe_id == 0 ) {
	// index pipe (main_aggr)
	$pipe4assign = array(
		'link' => XOOPS_URL.'/' ,
		'name4xml' => htmlspecialchars( $xoopsConfig['sitename'] , ENT_QUOTES ) . ' - ' . $xoopsModule->getVar('name') ,
		'lastfetch_time' => time() ,
	) ;
	$entries = d3pipes_main_fetch_entries_main_aggr( $mydirname , $errors , $max_entries ) ;
} else {
	// single pipe
	$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id ) ;
	if( empty( $pipe4assign['main_rss'] ) ) {
		redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/' , 3 , _MD_D3PIPES_ERR_INVALIDPIPEID ) ;
		exit ;
	}
	// fetch entries
	$entries = d3pipes_common_fetch_entries( $mydirname , $pipe4assign , $max_entries , $errors , $xoopsModuleConfig ) ;

	// check lastfetch_time
	if( empty( $pipe4assign['lastfetch_time'] ) ) {
		$lastfetch_time = 0 ;
		foreach( $entries as $entry ) {
			$lastfetch_time = max( @$entry['pipe']['lastfetch_time'] , $lastfetch_time ) ;
		}
		$pipe4assign['lastfetch_time'] = empty( $lastfetch_time ) ? time() : $lastfetch_time ;
	}


}

// get lastmodified of all over of entries
$entries_lastmodified = 0 ;
foreach( $entries as $entry ) {
	$entries_lastmodified = max( $entries_lastmodified , $entry['pubtime'] ) ;
}

// Utf8from object
$utf8from_obj =& d3pipes_common_get_joint_object_default( $mydirname , 'utf8from' , $xoopsModuleConfig['internal_encoding'] ) ;

// assign
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$xoopsTpl = new D3Tpl() ;
$xoopsTpl->assign(
	array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'xoops_config' => $xoopsConfig ,
		'xoops_config_utf8' => array_map( 'd3pipes_common_filter_ietoutf8' , $xoopsConfig ) ,
		'mod_config' => @$xoopsModuleConfig ,
		'xoops_breadcrumbs' => @$xoops_breadcrumbs ,
		'xoops_pagetitle' => @$pagetitle4assign ,
		'errors' => $errors ,
		'pipe' => $utf8from_obj->execute( $pipe4assign ) ,
		'entries' => $utf8from_obj->execute( $entries ) ,
		'entries_lastmodified' => $entries_lastmodified ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'style' => $style ,
		'link_prefer' => $link_prefer ,
		'xoops_module_header' => d3pipes_main_get_link2maincss( $mydirname ) . "\n" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

if( function_exists( 'mb_http_output' ) ) mb_http_output( 'pass' ) ;
header( 'Content-Type:text/xml; charset=utf-8' ) ;

$xoopsTpl->display( 'db:'.$mydirname.'_independent_'.$style.'.html' ) ;

exit ;

?>