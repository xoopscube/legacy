<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$xoopsOption['template_main'] = $mydirname.'_main_eachpipe.html' ;

// xoops header
include XOOPS_ROOT_PATH.'/header.php';

// fetch pipe_row
$pipe_id = intval( @$_GET['pipe_id'] ) ;
$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id ) ;

// specialcheck for eachpipe
if( empty( $pipe4assign['main_disp'] ) ) {
	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/' , 3 , _MD_D3PIPES_ERR_INVALIDPIPEID ) ;
	exit ;
}

// parse the pipe once
$entries = d3pipes_common_fetch_entries( $mydirname , $pipe4assign , $xoopsModuleConfig['entries_per_eachpipe'] , $errors , $xoopsModuleConfig ) ;

// pagetitle & xoops_breadcrumbs
$pagetitle4assign = empty( $pipe4assign['name'] ) ? _MD_D3PIPES_H2_EACHPIPE : $pipe4assign['name'] ;
$xoops_breadcrumbs[] = array( 'name' => $pagetitle4assign ) ;

// assign
$xoopsTpl->assign(
	array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'xoops_config' => $xoopsConfig ,
		'mod_config' => $xoopsModuleConfig ,
		'xoops_breadcrumbs' => $xoops_breadcrumbs ,
		'xoops_pagetitle' => $pagetitle4assign ,
		'errors' => $errors ,
		'clipping_count' => d3pipes_main_get_clipping_count_moduledb( $mydirname , $pipe_id ) ,
		'pipe' => $pipe4assign ,
		'entries' => $entries ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'xoops_module_header' => d3pipes_main_get_link2rss( $mydirname , $pipe_id , $pipe4assign ) . d3pipes_main_get_link2maincss( $mydirname ) . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>