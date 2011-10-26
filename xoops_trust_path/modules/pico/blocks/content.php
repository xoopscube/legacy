<?php

function b_pico_content_show( $options )
{
	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$content_id = intval( @$options[1] ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_content.html' : trim( $options[2] ) ;
	$process_body = empty( $options[3] ) ? false : true ;

	// mydirname check
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// $contentObj
	$contentObj = new PicoContent( $mydirname , $content_id ) ;
	$content_data = $contentObj->getData() ;

	// permission check
	if( empty( $content_data['can_read'] ) || empty( $content_data['can_readfull'] ) ) {
		return array() ;
	}

	// check existence
	if( $contentObj->isError() ) {
		return array( 'content' => 'invalid block id' ) ;
	}

	// module config (overridden)
	$configs = $contentObj->categoryObj->getOverriddenModConfig() ;

	// constpref
	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	// assigning (process_body)
	$content4assign = $contentObj->getData4html( $process_body ) ;

	// convert links from relative to absolute (wraps mode only)
	if( $configs['use_wraps_mode'] ) {
		$content_url = XOOPS_URL.'/modules/'.$mydirname.'/'.$content4assign['link'] ;
		$wrap_base_url = substr( $content_url , 0 , strrpos( $content_url , '/' ) ) ;
		$pattern = "/(\s+href|\s+src)\=(\"|\')?(?![a-z]+:|\/|\#)([^, \r\n\"\(\)'<>]+)/i" ;
		$replacement = "\\1=\\2$wrap_base_url/\\3" ;
		$content4assign['body'] = preg_replace( $pattern , $replacement , $content4assign['body'] ) ;
	}

	// make an array named 'block'
	$block = array( 
		'mytrustdirname' => $mytrustdirname ,
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$configs['images_dir'] ,
		'mod_config' => $configs ,
		'content' => $content4assign ,
	) ;

	if( empty( $options['disable_renderer'] ) ) {
		// render it
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl = new XoopsTpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		// just assign it
		return $block ;
	}
}



function b_pico_content_edit( $options )
{
	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$content_id = intval( @$options[1] ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_content.html' : trim( $options[2] ) ;
	$process_body = empty( $options[3] ) ? false : true ;

	// mydirname check
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// get content_title
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$contents = array( 0 => '--' ) ;
	$result = $db->query( "SELECT content_id,subject,c.cat_depth_in_tree FROM ".$db->prefix($mydirname."_contents")." o LEFT JOIN ".$db->prefix($mydirname."_categories")." c ON o.cat_id=c.cat_id ORDER BY c.cat_order_in_tree,o.weight" ) ;
	while( list( $id , $sbj , $depth ) = $db->fetchRow( $result ) ) {
		$contents[ $id ] = sprintf('%06d',$id).': '.str_repeat('--',$depth).$myts->makeTboxData4Show( $sbj , 1 , 1 ) ;
	}

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'contents' => $contents ,
		'content_id' => $content_id ,
		'this_template' => $this_template ,
		'process_body' => $process_body ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_content.html' ) ;
}



?>