<?php

function b_pico_subcategories_show( $options )
{
	global $xoopsUser ;

	$mydirname = empty( $options[0] ) ? 'pico' : $options[0] ;
	$categories = trim( @$options[1] ) === '' ? array() : array_map( 'intval' , explode( ',' , $options[1] ) ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_subcategories.html' : trim( $options[2] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	// categories can be read by current viewer (check by category_permissions)
	$whr_read4cat = 'c.`cat_id` IN (' . implode( "," , pico_common_get_categories_can_read( $mydirname ) ) . ')' ;

	// categories
	if( $categories === array() ) {
		$whr_categories = 'WHERE pid=0' ;
		$categories4assign = '' ;
	} else {
		$whr_categories = 'c.pid IN ('.implode(',',$categories).')' ;
		$categories4assign = implode(',',$categories) ;
	}

	$sql = "SELECT c.cat_id,c.cat_title,c.cat_vpath FROM ".$db->prefix($mydirname."_categories")." c WHERE ($whr_read4cat) AND ($whr_categories) ORDER BY c.cat_weight" ;
	if( ! $result = $db->query( $sql ) ) {
		echo $db->logger->dumpQueries() ;
		exit ;
	}

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	$block = array( 
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$configs['images_dir'] ,
		'mod_config' => $configs ,
		'categories' => $categories4assign ,
		'lang_category' => constant($constpref.'_CATEGORY') ,
		'lang_topcategory' => constant($constpref.'_TOPCATEGORY') ,
	) ;

	$cat4assign = array() ;
	while( $cat_row = $db->fetchArray( $result ) ) {
		$cat4assign[] = array( 
			'id' => intval( $cat_row['cat_id'] ) ,
			'link' => pico_common_make_category_link4html( $configs , $cat_row ) ,
			'title' => $myts->makeTboxData4Show( $cat_row['cat_title'] , 1 , 1 ) ,
		) ;
	}
	$block['categories'] = $cat4assign ;

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl = new XoopsTpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		return $block ;
	}
}



function b_pico_subcategories_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'pico' : $options[0] ;
	$categories = trim( @$options[1] ) === '' ? array() : array_map( 'intval' , explode( ',' , $options[1] ) ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_subcategories.html' : trim( $options[2] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'categories' => $categories ,
		'categories_imploded' => implode( ',' , $categories ) ,
		'order_options' => b_pico_list_allowed_order() ,
		'this_template' => $this_template ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_subcategories.html' ) ;
}

?>