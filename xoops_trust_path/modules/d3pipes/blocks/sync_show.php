<?php

function b_d3pipes_sync_show( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3pipes' : $options[0] ;
	$unique_id = empty( $options[1] ) ? uniqid( rand() ) : htmlspecialchars( $options[1] , ENT_QUOTES ) ; // just dummy
	$pipe_ids = empty( $options[2] ) ? array(0) : explode( ',' , preg_replace( '/[^0-9,:]/' , '' ,  $options[2] ) ) ;
	$max_entries = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$this_template = empty( $options[4] ) ? 'db:'.$mydirname.'_block_sync.html' : trim( $options[4] ) ;
	$union_class = @$options[5] == 'separated' ? 'separated' : 'mergesort' ;
	$link2clipping = empty( $options[6] ) ? false : true ;
	$keep_pipeinfo = empty( $options[7] ) ? false : true ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$configs = $config_handler->getConfigList( $module->mid() ) ;

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	// Union object
	$union_obj =& d3pipes_common_get_joint_object( $mydirname , 'union' , $union_class , sizeof( $pipe_ids ) == 1 ? $pipe_ids[0].':'.$max_entries : implode( ',' , $pipe_ids ) . '||' . ($keep_pipeinfo?1:0) ) ;
	$union_obj->setModConfigs( $configs ) ;
	$entries = $union_obj->execute( array() , $max_entries ) ;
	$pipes_entries = method_exists( $union_obj , 'getPipesEntries' ) ? $union_obj->getPipesEntries() : array() ;
	$errors = $union_obj->getErrors() ;

	// language file of main.php
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'main.php' , $mydirname , basename(dirname(dirname(__FILE__))) ) ;

	$block = array( 
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$configs['images_dir'] ,
		'xoops_config' => $GLOBALS['xoopsConfig'] ,
		'mod_config' => $configs ,
		'pipe_ids' => $pipe_ids ,
		'max_entries' => $max_entries ,
		'union_class' => $union_class ,
		'link2clipping' => $link2clipping ,
		'keep_pipeinfo' => $keep_pipeinfo ,
		'errors' => $errors ,
		'entries' => $entries ,
		'pipes_entries' => $pipes_entries ,
		'timezone_offset' => xoops_getUserTimestamp( 0 ) ,
	) ;

	if( empty( $options['disable_renderer'] ) ) {
		require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
		$tpl = new D3Tpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
		return $ret ;
	} else {
		return $block ;
	}
}

?>