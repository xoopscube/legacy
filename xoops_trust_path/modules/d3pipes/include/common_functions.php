<?php

function d3pipes_common_convert_encoding_utf8toie( $mydirname , $string_utf8 )
{
	if( _CHARSET == 'UTF-8' ) {
		return $string_utf8 ;
	} else if( function_exists( 'mb_convert_encoding' ) ) {
		return mb_convert_encoding( $string_utf8 , _CHARSET , 'UTF-8' ) ;
	} else if( function_exists( 'iconv' ) ) {
		return iconv( $string_utf8 , 'UTF-8' , _CHARSET ) ;
	} else {
		return utf8_decode( $string_utf8 ) ;
	}
}


function d3pipes_common_convert_encoding_ietoutf8( $mydirname , $string_ie )
{
	if( _CHARSET == 'UTF-8' ) {
		return $string_ie ;
	} else if( function_exists( 'mb_convert_encoding' ) ) {
		return mb_convert_encoding( $string_ie , 'UTF-8' , _CHARSET ) ;
	} else if( function_exists( 'iconv' ) ) {
		return iconv( $string_ie , _CHARSET , 'UTF-8' ) ;
	} else {
		return utf8_encode( $string_ie ) ;
	}
}


function d3pipes_common_filter_ietoutf8( $string_ie )
{
	if( is_string( $string_ie ) ) return d3pipes_common_convert_encoding_ietoutf8( '' , $string_ie ) ;
	return $string_ie ;
}


function d3pipes_common_get_submenu( $mydirname , $caller = 'xoops_version' )
{
	$module_handler =& xoops_gethandler('module') ;
	$module =& $module_handler->getByDirname( $mydirname ) ;
	if( ! is_object( $module ) ) return array() ;
	$config_handler =& xoops_gethandler('config') ;
	$mod_config =& $config_handler->getConfigsByCat( 0 , $module->getVar('mid') ) ;

	$db =& Database::getInstance() ;
	$myts =& MyTextSanitizer::getInstance();

	// pipes query
	$sql = "SELECT pipe_id,name FROM ".$db->prefix($mydirname."_pipes")." WHERE in_submenu ORDER BY weight" ;
	$prs = $db->query( $sql ) ;
	$submenus = array() ;
	if( $prs ) while( $pipe_row = $db->fetchArray( $prs ) ) {
		$pipe_id = intval( $pipe_row['pipe_id'] ) ;
		$submenus[ $pipe_id ] = array(
			'name' => $myts->makeTboxData4Show( $pipe_row['name'] ) ,
			'url' => 'index.php?page=eachpipe&amp;pipe_id='.$pipe_id ,
		) ;
	}

	return $submenus ;
}


function d3pipes_common_update_joint_option( $mydirname , $pipe_id , $joint_type , $option )
{
	$db =& Database::getInstance() ;

	$pipe_id = intval( $pipe_id ) ;

	list( $joints_serialized ) = $db->fetchRow( $db->query( "SELECT joints FROM ".$db->prefix($mydirname."_pipes")." WHERE pipe_id=$pipe_id" ) ) ;
	if( ! empty( $joints_serialized ) && $joints = unserialize( $joints_serialized ) ) {
		foreach( array_keys( $joints ) as $i ) {
			if( $joints[ $i ]['joint'] == $joint_type ) {
				$joints[ $i ][ 'option' ] = $option ;
			}
		}
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_pipes")." SET joints='".mysql_real_escape_string(serialize($joints))."' WHERE pipe_id=$pipe_id" ) ;
	}
}


function d3pipes_common_get_default_joint_class( $mydirname , $joint_type )
{
	$db =& Database::getInstance() ;

	list( $ret ) = $db->fetchRow( $db->query( "SELECT default_class FROM ".$db->prefix($mydirname."_joints")." WHERE joint_type='".mysql_real_escape_string($joint_type)."'" ) ) ;
	if( empty( $ret ) ) {
		$classes_base = XOOPS_TRUST_PATH.'/modules/d3pipes/joints/'.$joint_type ;
		if( $handler = @opendir( $classes_base ) ) {
			while( ( $file = readdir( $handler ) ) !== false ) {
				if( substr( $file , 0 , 1 ) == '.' ) continue ;
				$file = str_replace( '..' , '' , $file ) ;
				if( file_exists( $classes_base . '/' . $file ) ) {
					$ret = strtolower( substr( $file , strlen( 'D3pipes'.$joint_type ) , - strlen( '.class.php' ) ) ) ;
					break ;
				}
			}
		}
	}

	return $ret ;
}


function d3pipes_common_cache_path_base( $mydirname )
{
	$salt = substr( md5( XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX ) , 0 , 6 ) ;
	$base = XOOPS_TRUST_PATH.'/cache/'.$mydirname.'_'.$salt.'_' ;

	return $base ;
}


function d3pipes_common_delete_all_cache( $mydirname , $pipe_id = 0 , $with_fetch = true , $with_ping = true )
{
	$base = d3pipes_common_cache_path_base( $mydirname ) ;
	$prefix = substr( strrchr( $base , '/' ) , 1 ) ;
	$prefix_length = strlen( $prefix ) ;
	$basedir = substr( $base , 0 , - $prefix_length ) ;

	if( $handler = @opendir( $basedir ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( strncmp( $file , $prefix , $prefix_length ) === 0 ) {
				// save 'fetch' cache if necessary
				if( ! $with_fetch && substr( $file , -5 ) == 'fetch' ) continue ;

				// save 'ping' cache if necessary
				if( ! $with_ping && substr( $file , -4 ) == 'ping' ) continue ;

				if( $pipe_id > 0 ) {
					// only specified pipe's cache
					if( intval( substr( $file , $prefix_length + 1 ) ) == $pipe_id ) {
						@unlink( $basedir . $file ) ;
					}
				} else {
					// all pipe's cache
					@unlink( $basedir . $file ) ;
				}
			}
		}
	}
}


function d3pipes_common_get_clipping( $mydirname , $clipping_id )
{
	require_once dirname(dirname(__FILE__)).'/joints/clip/D3pipesClipModuledb.class.php' ;

	$clip_obj = new D3pipesClipModuledb( $mydirname , 0 , '' ) ;
	return $clip_obj->getClipping( $clipping_id ) ;
}


function d3pipes_common_get_joint_objects( $mydirname , $joint_type = '' )
{
	if( ! $joint_type ) return array() ;

	$type_base = dirname(dirname(__FILE__)).'/joints/'.$joint_type ;
	$dh = opendir( $type_base ) ;
	$ret = array() ;
	while( $file = readdir( $dh ) ) {
		if( substr( $file , 0 , 7 + strlen( $joint_type ) ) != 'D3pipes'.ucfirst($joint_type) ) continue ;
		if( substr( $file , -10 ) != '.class.php' ) continue ;
		$class_name = substr( $file , 0 , -10 ) ;
		require_once $type_base.'/'.$file ;
		$ret[] = new $class_name( $mydirname , 0 , '' ) ;
	}
	return $ret ;
}


function &d3pipes_common_get_joint_object_default( $mydirname , $joint_type , $option = '' )
{
	$class_name = 'D3pipes'.ucfirst($joint_type).ucfirst(d3pipes_common_get_default_joint_class( $mydirname , $joint_type )) ;
	require_once dirname(dirname(__FILE__)).'/joints/'.$joint_type.'/'.$class_name.'.class.php' ;
	$ret = new $class_name( $mydirname , 0 , $option ) ;
	return $ret ;
}


function &d3pipes_common_get_joint_object( $mydirname , $joint_type , $joint_class , $option = '' )
{
	$class_name = 'D3pipes'.ucfirst($joint_type).ucfirst($joint_class) ;
	require_once dirname(dirname(__FILE__)).'/joints/'.$joint_type.'/'.$class_name.'.class.php' ;
	$ret = new $class_name( $mydirname , 0 , $option ) ;
	return $ret ;
}


function d3pipes_common_get_pipe4assign( $mydirname , $pipe_id )
{
	$db =& Database::getInstance() ;
	$myts =& MyTextSanitizer::getInstance() ;

	// fetch pipe_row
	$pipe_row = $db->fetchArray( $db->query( "SELECT * FROM ".$db->prefix($mydirname."_pipes")." WHERE pipe_id=".intval($pipe_id) ) ) ;
	if( empty( $pipe_row ) ) return false ;

	$pipe4assign = array(
		'dirname' => $mydirname ,
		'id' => intval( $pipe_id ) ,
		'name' => $myts->makeTboxData4Show( $pipe_row['name'] ) ,
		'name4xml' => htmlspecialchars( $pipe_row['name'] , ENT_QUOTES ) ,
		'url' => $myts->makeTboxData4Show( $pipe_row['url'] ) ,
		'url4xml' => htmlspecialchars( $pipe_row['url'] , ENT_QUOTES ) ,
		'image' => $myts->makeTboxData4Show( $pipe_row['image'] ) ,
		'image4xml' => htmlspecialchars( $pipe_row['image'] , ENT_QUOTES ) ,
		'description' => $myts->displayTarea( $pipe_row['description'] ) ,
		'description4xml' => htmlspecialchars( $myts->displayTarea( $pipe_row['description'] ) , ENT_QUOTES ) ,
		'description_raw' => $pipe_row['description'] ,
		'created_time_formatted' => formatTimestamp( $pipe_row['created_time'] , 'm' ) ,
		'modified_time_formatted' => formatTimestamp( $pipe_row['modified_time'] , 'm' ) ,
		'lastfetch_time_formatted' => $pipe_row['lastfetch_time'] ? formatTimestamp( $pipe_row['lastfetch_time'] , 'm' ) : '----' ,
		'joints' => unserialize( $pipe_row['joints'] ) ,
	) + $pipe_row ;

	if( empty( $pipe4assign['joints'] ) ) return false ;
	else return $pipe4assign ;
}



function d3pipes_common_fetch_entries( $mydirname , $pipe_row , $max_entries , &$errors , $mod_configs )
{
	// var_dump( microtime() ) ;

	$errors = array() ;

	if( empty( $pipe_row ) ) return array() ;

	$db =& Database::getInstance() ;
	$myts =& MyTextSanitizer::getInstance() ;

	$joints = $pipe_row['joints'] ;
	$pipe_id = $pipe_row['pipe_id'] ;

	$joints_dir = dirname(dirname(__FILE__)).'/joints' ;
	include dirname(__FILE__).'/start_joints.inc.php' ;

	$objects = array() ;

	// make objects and prefetch of each joints (reversed order)
	$stage = sizeof( $joints ) ;
	foreach( array_reverse( $joints ) as $joint ) {
		-- $stage ;
		$class_name = 'D3pipes'.ucfirst($joint['joint']).ucfirst($joint['joint_class']) ;
		require_once $joints_dir.'/'.$joint['joint'].'/'.$class_name.'.class.php' ;
		if( ! class_exists( $class_name ) ) die( 'Class '.$class_name.' does not exist' ) ;
//COMENT by domifara for php5.2-
/*
		$obj =& new $class_name( $mydirname , $pipe_id , $joint['option'] ) ;
		$obj->setModConfigs( $mod_configs ) ;
		$obj->setStage( $stage ) ;
		$objects[ $stage ] =& $obj ;
		if( $obj->isCached() ) break ;
*/
//HACK by domifara for php5.3+
		$obj = new $class_name( $mydirname , $pipe_id , $joint['option'] ) ;
		$obj->setModConfigs( $mod_configs ) ;
		$obj->setStage( $stage ) ;
		$objects[ $stage ] = $obj ;
		if( $obj->isCached() ) break ;
	}

	if( empty( $objects ) ) return false ;
	ksort( $objects ) ;

	// chain data is initialized
	$data = array() ;

	// joint chains
	foreach( $objects as $obj ) {
		$data = $obj->execute( $data , $max_entries ) ;
		$errors = array_merge( $errors , $obj->getErrors() ) ;
	}

	// var_dump( microtime() ) ;

	return is_array( $data ) ? array_slice( $data , 0 , $max_entries ) : $data ;
}


function d3pipes_common_unserialize( $serialized_data )
{
	if( empty( $serialized_data ) ) return array() ;

	// rightly formatted data
	$data = @unserialize( $serialized_data ) ;
	if( is_array( $data ) ) return $data ;

	// only linear arrays can be parsed
	$elements = preg_split( '/(s\:\d+\:\"|b\:\d+\;|i\:\d+\;)/' , $serialized_data , -1 , PREG_SPLIT_DELIM_CAPTURE ) ;

	$canonical_sdata = '' ;
	foreach( array_keys( $elements ) as $i ) {
		if( preg_match( '/^s\:(\d+)\:\"$/' , $elements[$i] , $regs ) ) {
			$length = strlen( $elements[$i+1] ) - 2 ;
			$canonical_sdata .= str_replace( $regs[1] , $length , $elements[$i] ) ;
		} else {
			$canonical_sdata .= $elements[$i] ;
		}
	}
	//var_dump( $serialized_data ) ;
	//var_dump( $canonical_sdata ) ;

	$data = @unserialize( $canonical_sdata ) ;
	if( is_array( $data ) ) return $data ;
	else return array() ;
}


?>