<?php

$GLOBALS['pico_blocks_tags_order_options'] = array(
	'count ASC' => 'count ASC' ,
	'count DESC' => 'count DESC' ,
	'weight ASC' => 'weight ASC' ,
	'weight DESC' => 'weight DESC' ,
	'label ASC' => 'label ASC' ,
	'label DESC' => 'label DESC' ,
	'created_time ASC' => 'created_time ASC' ,
	'created_time DESC' => 'created_time DESC' ,
) ;

function b_pico_tags_show( $options )
{
	global $pico_blocks_tags_order_options ;

	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$limit = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$listorder = in_array( @$options[2] , $pico_blocks_tags_order_options ) ? $options[2] : 'count DESC' ;
	$sqlorder = in_array( @$options[3] , $pico_blocks_tags_order_options ) ? $options[3] : 'count DESC' ;
	$this_template = empty( $options[4] ) ? 'db:'.$mydirname.'_block_tags.html' : trim( $options[4] ) ;

	// mydirname check
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	// sql
	$sql = "SELECT label,count FROM ".$db->prefix($mydirname."_tags")." ORDER BY $sqlorder LIMIT $limit" ;
	$result = $db->query( $sql ) ;
	if( $sqlorder != $listorder ) {
		$labels4sql = array() ;
		while( list( $label , ) = $db->fetchRow( $result ) ) {
			$labels4sql[] = "'".addslashes($label)."'" ;
		}
		$sql = "SELECT label,count FROM ".$db->prefix($mydirname."_tags")." WHERE label IN (".implode(",",$labels4sql).") ORDER BY $listorder" ;
		$result = $db->query( $sql ) ;
	}

	// tags4assign
	$tags = array() ;
	$rank = 0 ;
	while( list( $label , $count ) = $db->fetchRow( $result ) ) {
		$tags[ $label ] = array( 
			'label' => $label ,
			'count' => $count ,
			'rank' => $rank ++ ,
		) ;
	}
	//ksort( $tags , SORT_STRING ) ;
	$tags4assign = array_values( $tags ) ;

	// module config
	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	// constpref
	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	// make an array named 'block'
	$block = array( 
		'mytrustdirname' => $mytrustdirname ,
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$configs['images_dir'] ,
		'mod_config' => $configs ,
		'limit' => $limit ,
		'listorder' => $listorder ,
		'sqlorder' => $sqlorder ,
		'tagsnum' => sizeof( $tags4assign ) ,
		'tags' => $tags4assign ,
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



function b_pico_tags_edit( $options )
{
	global $pico_blocks_tags_order_options ;

	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$limit = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
	$listorder = in_array( @$options[2] , $pico_blocks_tags_order_options ) ? $options[2] : 'count DESC' ;
	$sqlorder = in_array( @$options[3] , $pico_blocks_tags_order_options ) ? $options[3] : 'count DESC' ;
	$this_template = empty( $options[4] ) ? 'db:'.$mydirname.'_block_tags.html' : trim( $options[4] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'limit' => $limit ,
		'listorder' => $listorder ,
		'sqlorder' => $sqlorder ,
		'order_options' => $pico_blocks_tags_order_options ,
		'this_template' => $this_template ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_tags.html' ) ;
}

?>