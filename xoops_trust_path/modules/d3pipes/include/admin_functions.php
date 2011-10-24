<?php

function d3pipes_admin_fetch_joints( $mydirname )
{
	$GLOBALS['joint_type_weights'] = array(
		'fetch' => -20 ,
		'parse' => -10 ,
		'block' => -8 ,
		'utf8to' => -7 ,
		'utf8from' => -6 ,
		'filter' => 0 ,
		'reassign' => 0 ,
		'replace' => 0 ,
		'clip' => 20 ,
		'cache' => 21 ,
		'ping' => 22 ,
		'sort' => 30 ,
		'union' => 40 ,
	) ;

	$ret = array() ;

	$joints_base = XOOPS_TRUST_PATH.'/modules/d3pipes/joints' ;

	if( $handler = @opendir( $joints_base ) ) {
		while( ( $dir = readdir( $handler ) ) !== false ) {
			if( substr( $dir , 0 , 1 ) == '.' ) continue ;
			$dir = preg_replace( '/[^0-9a-zA-Z_]/' , '' , $dir ) ;
			if( ! is_dir( $joints_base . '/' . $dir ) ) continue ;
			$lang_joint = defined( '_MD_D3PIPES_JOINT_'.strtoupper($dir) ) ? constant( '_MD_D3PIPES_JOINT_'.strtoupper($dir) ) : $dir ;
			$ret[ $dir ] = $lang_joint ;
		}
	}

	uksort( $ret , create_function( '$a,$b' , 'return @$GLOBALS["joint_type_weights"][$a] > @$GLOBALS["joint_type_weights"][$b] ; ' ) ) ;

	return $ret ;
}


function d3pipes_admin_fetch_classes( $mydirname , $joint_type )
{
	$classes_base = XOOPS_TRUST_PATH.'/modules/d3pipes/joints/'.$joint_type ;

	$ret = array() ;

	if( $handler = @opendir( $classes_base ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file = str_replace( '..' , '' , $file ) ;
			if( ! file_exists( $classes_base . '/' . $file ) ) continue ;
			$joint_class = strtolower( substr( $file , strlen( 'D3pipes'.$joint_type ) , - strlen( '.class.php' ) ) ) ;
			$ret[ $joint_class ] = $joint_class ;
		}
	}

	return $ret ;
}


function d3pipes_admin_judge_type_of_pipe( $joints )
{
	if( $joints[0]['joint'] == 'fetch' ) {
		foreach( $joints as $joint ) {
			if( $joint['joint'] == 'clip' ) {
				return _MD_A_D3PIPES_TYPE_FETCH . _MD_A_D3PIPES_TYPE_CLIP ;
			}
		}
		return _MD_A_D3PIPES_TYPE_FETCH ;
	} else if( $joints[0]['joint'] == 'block' ) {
		return _MD_A_D3PIPES_TYPE_BLOCK ;
	} else if( $joints[0]['joint'] == 'local' ) {
		return _MD_A_D3PIPES_TYPE_LOCAL ;
	} else if( $joints[0]['joint'] == 'union' ) {
		return _MD_A_D3PIPES_TYPE_UNION ;
	} else {
		return _MD_A_D3PIPES_TYPE_OTHER ;
	}
}


function d3pipes_admin_disp2raw( $value , $type )
{
	switch( $type ) {
		case 'text' :
			// fix a bug(?) of InPlaceEditor
			$value = str_replace( '<br>' , '<br />' , $value ) ;
			break ;
		case 'time' :
			$value = strtotime( $value ) ;
			if( empty( $value ) ) $value = time() ;
			$tz_offset = xoops_getUserTimestamp( 0 ) ;
			$value -= $tz_offset ;
			break ;
	}

	return $value ;
}

?>