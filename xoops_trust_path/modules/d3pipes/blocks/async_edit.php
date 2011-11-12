<?php

// edit func for async
function b_d3pipes_async_edit( $options )
{
	return b_d3pipes_blockedit_common( $options , 'async' ) ;
}


// edit func both for async and sync
function b_d3pipes_blockedit_common( $options , $type = 'async' )
{
	$mydirname = empty( $options[0] ) ? 'd3pipes' : $options[0] ;
	//$unique_id = empty( $options[1] ) ? uniqid(rand()) : $options[1] ;
	$pipe_ids = empty( $options[2] ) ? array('') : explode( ',' , preg_replace( '/[^0-9,:]/' , '' ,  $options[2] ) ) ;
	$max_entries = empty( $options[3] ) ? 0 : intval( $options[3] ) ;
	$this_template = empty( $options[4] ) ? '' : trim( $options[4] ) ;
	$union_class = @$options[5] == 'separated' ? 'separated' : 'mergesort' ;
	$link2clipping = empty( $options[6] ) ? false : true ;
	$keep_pipeinfo = empty( $options[7] ) ? false : true ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
	$tpl = new D3Tpl() ;
	$tpl->assign( array(
		'mydirname' => $mydirname ,
		'type' => $type ,
		'uniqid' => uniqid(rand()) ,
		'pipe_ids' => $pipe_ids ,
		'pipe_options' => b_d3pipes_get_pipe_options( $mydirname ) ,
		'max_entries' => $max_entries ,
		'this_template' => $this_template ,
		'union_class' => $union_class ,
		'link2clipping' => $link2clipping ,
		'keep_pipeinfo' => $keep_pipeinfo ,
		'union_options' => array( 'separated' => _MB_D3PIPES_UNIONOPTION_SEPARATED , 'mergesort' => _MB_D3PIPES_UNIONOPTION_MERGESORT ) ,
	) ) ;
	return $tpl->fetch( 'db:'.$mydirname.'_blockedit_async.html' ) ;
}


// make options for selecting pipes
function b_d3pipes_get_pipe_options( $mydirname )
{
	$mytrustdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
	require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;

	require_once( XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ) ;
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'admin.php' , $mydirname , $mytrustdirname ) ;

	$db =& Database::getInstance() ;

	$result = $db->query( "SELECT pipe_id,name,joints FROM ".$db->prefix($mydirname."_pipes")." WHERE block_disp ORDER BY weight,pipe_id" ) ;
	$pipe_options = array( '' => '----' ) ;
	while( $myrow = $db->fetchArray( $result ) ) {
		$joints = unserialize( $myrow['joints'] ) ;
		$pipe_options[ intval( $myrow['pipe_id'] ) ] = htmlspecialchars( '(' . $myrow['pipe_id'] . ') ' . d3pipes_admin_judge_type_of_pipe( $joints ) . ' - ' . $myrow['name'] , ENT_QUOTES ) ;
	}
	
	return $pipe_options ;
}

?>