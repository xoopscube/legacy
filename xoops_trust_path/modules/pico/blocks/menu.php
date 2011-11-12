<?php

function b_pico_menu_show( $options )
{
	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$cat_ids = trim( @$options[1] ) === '' ? array() : array_map( 'intval' , explode( ',' , $options[1] ) ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_menu.html' : trim( $options[2] ) ;

	// mydirname check
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// category handler
	$categoryHandler = new PicoCategoryHandler( $mydirname ) ;

	// category objects
	if( $cat_ids === array() ) {
		$categoryObjs = $categoryHandler->getAllCategories() ;
	} else {
		$categoryObjs = array() ;
		foreach( $cat_ids as $cat_id ) {
			$categoryObjs[] = $categoryHandler->get( $cat_id ) ;
		}
	}

	// categories loop
	$categories4assign = array() ;
	foreach( $categoryObjs as $cat_id => $categoryObj ) {
		// assign categories
		$categories4assign[ $cat_id ] = $categoryObj->getData4html() ;

		// contents loop
		$contentObjs = $categoryObj->getContents() ;
		foreach( $contentObjs as $contentObj ) {
			$content_data = $contentObj->getData() ;
			if( $content_data['show_in_menu'] ) $categories4assign[ $cat_id ]['contents'][] = $contentObj->getData4html() ;
		}
	}

	// module config (not overridden yet)
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
		'categories' => $categories4assign ,
		'lang_category' => constant($constpref.'_CATEGORY') ,
		'lang_topcategory' => constant($constpref.'_TOPCATEGORY') ,
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



function b_pico_menu_edit( $options )
{
	// options
	$mytrustdirname = basename(dirname(dirname(__FILE__))) ;
	$mydirname = empty( $options[0] ) ? $mytrustdirname : $options[0] ;
	$cat_ids = trim( @$options[1] ) === '' ? array() : array_map( 'intval' , explode( ',' , $options[1] ) ) ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_menu.html' : trim( $options[2] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'categories' => $cat_ids ,
		'categories_imploded' => implode( ',' , $cat_ids ) ,
		'order_options' => b_pico_list_allowed_order() ,
		'this_template' => $this_template ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_menu.html' ) ;
}

?>