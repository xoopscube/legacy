<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$xoopsOption['template_main'] = $mydirname.'_main_index.html' ;

// xoops header
include XOOPS_ROOT_PATH.'/header.php';

// fetch pipes as heading
$result = $db->query( "SELECT pipe_id FROM ".$db->prefix($mydirname."_pipes")." WHERE main_list ORDER BY weight" ) ;
$headpipes4assign = array() ;
while( list( $pipe_id ) = $db->fetchRow( $result ) ) {
	$headpipes4assign[] = d3pipes_common_get_pipe4assign( $mydirname , intval( $pipe_id ) ) ;
}

$entries = d3pipes_main_fetch_entries_main_aggr( $mydirname , $errors , $xoopsModuleConfig['index_total'] ) ;

// pagetitle & xoops_breadcrumbs
$pagetitle4assign = $xoopsModule->getVar('name') ;

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
		'errors' => $errors ,
		'headpipes' => $headpipes4assign ,
		'entries' => $entries ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'xoops_module_header' => d3pipes_main_get_link2rss( $mydirname , 0 ) . d3pipes_main_get_link2maincss( $mydirname ) . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

include XOOPS_ROOT_PATH.'/footer.php';

?>