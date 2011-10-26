<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$xoopsOption['template_main'] = $mydirname.'_main_clipping.html' ;

// xoops header
include XOOPS_ROOT_PATH.'/header.php';

// get clipping (raw data)
$clipping_id = intval( @$_GET['clipping_id'] ) ;
$clipping = d3pipes_common_get_clipping( $mydirname , $clipping_id ) ;

if( $clipping === false ) {
	redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/' , 3 , _MD_D3PIPES_ERR_INVALIDCLIPPINGID ) ;
	exit ;
}

// get pipe4assign
$pipe_id = intval( $clipping['pipe_id'] ) ;
$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $clipping['pipe_id'] ) ;

// pagetitle & xoops_breadcrumbs
$pagetitle4assign = empty( $clipping['headline'] ) ? _MD_D3PIPES_H2_CLIPPING : htmlspecialchars( $clipping['headline'] , ENT_QUOTES ) ;
$xoops_breadcrumbs[] = array( 'name' => @$pipe4assign['name'] , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=eachpipe&amp;pipe_id='.$clipping['pipe_id'] ) ;
$xoops_breadcrumbs[] = array( 'name' => _MD_D3PIPES_H2_CLIPLIST , 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=cliplist&amp;pipe_id='.$clipping['pipe_id'] ) ;
$xoops_breadcrumbs[] = array( 'name' => $pagetitle4assign ) ;

// assign
$xoopsTpl->assign(
	array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'xoops_config' => $xoopsConfig ,
		'mod_config' => @$xoopsModuleConfig ,
		'xoops_breadcrumbs' => @$xoops_breadcrumbs ,
		'xoops_pagetitle' => @$pagetitle4assign ,
		'clipping_id' => $clipping_id ,
		'pipe' => $pipe4assign ,
		'entry' => $clipping ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'xoops_module_header' => d3pipes_main_get_link2rss( $mydirname , $pipe_id , $pipe4assign ) . d3pipes_main_get_link2maincss( $mydirname ) . d3pipes_main_get_script2commonlib( $mydirname ) . "\n" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>