<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$xoopsOption['template_main'] = $mydirname.'_main_cliplist.html' ;

// xoops header
include XOOPS_ROOT_PATH.'/header.php';

// fetch pipe_row
$pipe_id = intval( @$_GET['pipe_id'] ) ;
$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id ) ;

// specialcheck for cliplist
if( empty( $pipe4assign['main_disp'] ) ) {
	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/' , 3 , _MD_D3PIPES_ERR_INVALIDPIPEID ) ;
	exit ;
}

// make page navigation
$pos = intval( @$_GET['pos'] ) ;
$clipping_count = d3pipes_main_get_clipping_count_moduledb( $mydirname , $pipe_id ) ;
require_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
$pagenav = new XoopsPageNav( $clipping_count , $xoopsModuleConfig['entries_per_cliplist'] , $pos , 'pos' , "page=cliplist&amp;pipe_id=$pipe_id" ) ;
$pagenav4assign = $pagenav->renderNav( 10 ) ;

// entries from clipping
$entries = d3pipes_main_get_clippings_moduledb( $mydirname , $pipe_id , $xoopsModuleConfig['entries_per_cliplist'] , $pos ) ;


// pagetitle & xoops_breadcrumbs
$pagetitle4assign = empty( $pipe4assign['name'] ) ? _MD_D3PIPES_H2_CLIPLIST : $pipe4assign['name'].' - '._MD_D3PIPES_H2_CLIPLIST ;
$xoops_breadcrumbs[] = array( 'name' => @$pipe4assign['name'] , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=eachpipe&amp;pipe_id='.$pipe4assign['pipe_id'] ) ;
$xoops_breadcrumbs[] = array( 'name' => _MD_D3PIPES_H2_CLIPLIST ) ;

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
//		'errors' => $errors ,
		'clipping_pagenav' => $pagenav4assign ,
		'pipe' => $pipe4assign ,
		'entries' => $entries ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'xoops_module_header' => d3pipes_main_get_link2rss( $mydirname , $pipe_id , $pipe4assign ) . d3pipes_main_get_link2maincss( $mydirname ) . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>