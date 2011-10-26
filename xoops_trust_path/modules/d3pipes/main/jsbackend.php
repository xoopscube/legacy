<?php

require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

@ini_set( 'display_errors' , 0 ) ;

// fetch unique_id
$unique_id = preg_replace( '/[^0-9a-z]/' , '' , $_GET['unique_id'] ) ;

// fetch max_entries
$max_entries = intval( @$_GET['max_entries'] ) ;
if( $max_entries > 50 ) $max_entries = 50 ;

// fetch union_class
$union_class = $_GET['union_class'] == 'separated' ? 'separated' : 'mergesort' ;

// fetch link2clipping
$link2clipping = empty( $_GET['link2clipping'] ) ? false : true ;

// fetch keep_pipeinfo
$keep_pipeinfo = empty( $_GET['keep_pipeinfo'] ) ? false : true ;

// fetch pipe_row
$pipe_ids = empty( $_GET['pipe_ids'] ) ? array(0) : explode( ',' , preg_replace( '/[^0-9,:]/' , '' ,  $_GET['pipe_ids'] ) ) ;

if( sizeof( $pipe_ids ) == 1 ) {
	// single pipe
	$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , intval( $pipe_ids[0] ) ) ;
	if( empty( $pipe4assign ) ) {
		$entries = array() ;
		$errors = array( 'Invalid pipe_id' ) ;
	} else {
		$entries = d3pipes_common_fetch_entries( $mydirname , $pipe4assign , $max_entries , $errors , $xoopsModuleConfig ) ;
	}
	$pipes_entries = array() ;
} else {
	// Union object
	$union_obj =& d3pipes_common_get_joint_object( $mydirname , 'union' , $union_class , implode( ',' , $pipe_ids ) . '||' . ($keep_pipeinfo?1:0) ) ;
	$union_obj->setModConfigs( $xoopsModuleConfig ) ;
	$entries = $union_obj->execute( array() , $max_entries ) ;
	$pipes_entries = method_exists( $union_obj , 'getPipesEntries' ) ? $union_obj->getPipesEntries() : array() ;
	$errors = $union_obj->getErrors() ;
}

// assign
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$xoopsTpl = new D3Tpl() ;
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
		'entries' => $entries ,
		'pipes_entries' => $pipes_entries ,
		'link2clipping' => $link2clipping ,
		'keep_pipeinfo' => $keep_pipeinfo ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
		'xoops_module_header' => d3pipes_main_get_link2maincss( $mydirname ) . "\n" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
	)
) ;

$html = addslashes( strtr( $xoopsTpl->fetch( 'db:'.$mydirname.'_main_jsbackend.html' ) , "\n\r" , "  " ) ) ;

echo "d3pipes_insert_html('{$mydirname}_async_block_{$unique_id}','$html');" ;

exit ;

?>