<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;


$all_joints = d3pipes_admin_fetch_joints( $mydirname ) ;


//
// transaction stage
//

if( ( ! empty( $_POST['do_update'] ) || ! empty( $_POST['do_saveas'] ) ) && is_array( @$_POST['joint_weights'] ) ) {

	if ( ! $xoopsGTicket->check( true , 'd3pipes_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$joints = array() ;
	foreach( @$_POST['joint_weights'] as $i => $weight ) {
		$i = intval( $i ) ;
		$weight = intval( $weight ) ;
		@list( $joint_type , $joint_class ) = explode( '::' , $myts->stripSlashesGPC( @$_POST['joint_classes'][$i] ) ) ;
		if( empty( $joint_type ) || ! isset( $all_joints[ $joint_type ] ) ) continue ;
		$joint_class = preg_replace( '/[^0-9a-zA-Z_]/' , '' , @$joint_class ) ;
		$valid_classes = d3pipes_admin_fetch_classes( $mydirname , $joint_type ) ;
		if( ! isset( $valid_classes[ $joint_class ] ) ) {
			$joint_class = d3pipes_common_get_default_joint_class( $mydirname , $joint_type ) ;
			if( empty( $joint_class ) ) list( $joint_class ) = array_keys( $valid_classes ) ;
		}

		// merge options if necessary
		if( empty( $_POST['joint_option'][$i] ) && ! empty( $_POST['joint_options'][$i] ) && is_array( $_POST['joint_options'][$i] ) ) {
			$separator = empty( $_POST['joint_option_separator'][$i] ) ? '|' : $_POST['joint_option_separator'][$i] ;
			$max_index = empty( $_POST['joint_option_max_index'][$i] ) ? max( array_keys( $_POST['joint_options'][$i] ) ) : intval( $_POST['joint_option_max_index'][$i] ) ;
			$joint_options = array() ;
			for( $o = 0 ; $o <= $max_index ; $o ++ ) {
				$joint_options[$o] = $myts->stripSlashesGPC( @$_POST['joint_options'][$i][$o] ) ;
			}
			$joint_option = join( $separator , $joint_options ) ;
		} else {
			$joint_option = $myts->stripSlashesGPC( @$_POST['joint_option'][$i] ) ;
		}

		$joints[ $weight ] = array(
			'joint' => $joint_type ,
			'joint_class' => $joint_class ,
			'option' => $joint_option ,
		) ;
	}
	ksort( $joints ) ;
	$joints = array_values( $joints ) ;

	// check joints interrelations
	require dirname(dirname(__FILE__)).'/include/start_joints.inc.php' ;
	if( ! in_array( @$joints[0]['joint'] , $start_joints ) ) {
		die( sprintf( _MD_A_D3PIPES_ERR_INVALIDSTARTJOINT_FMT , implode( ',' , $start_joints ) ) ) ;
	}
	if( @$joints[0]['joint'] == 'fetch' ) {
		$parse_found = false ;
		foreach( $joints as $joint ) {
			if( $joint['joint'] == 'parse' ) $parse_found = true ;
		}
		if( empty( $parse_found ) ) die( _MD_A_D3PIPES_ERR_CORRESPONDPARSENOTFOUND ) ;
	}

	// make sql
	$set4sql = "`joints`='".mysql_real_escape_string(serialize(array_values($joints)))."',name='".mysql_real_escape_string($myts->stripSlashesGPC(@$_POST['name']))."',url='".mysql_real_escape_string($myts->stripSlashesGPC(@$_POST['url']))."',image='".mysql_real_escape_string($myts->stripSlashesGPC(@$_POST['image']))."',description='".mysql_real_escape_string($myts->stripSlashesGPC(@$_POST['description']))."'" ;
	
	$pipe_id = intval( @$_POST['pipe_id'] ) ;
	if( $pipe_id == 0 || ! empty( $_POST['do_saveas'] ) ) {
		$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_pipes")." (created_time) VALUES (UNIX_TIMESTAMP())" ) ;
		$pipe_id = $db->getInsertId() ;
	}
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_pipes")." SET ".$set4sql.",modified_time=UNIX_TIMESTAMP(),lastfetch_time=0 WHERE `pipe_id`=$pipe_id" ) ;

	// remove cache
	d3pipes_common_delete_all_cache( $mydirname , $pipe_id ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=pipe" , 3 , _MD_A_D3PIPES_MSG_PIPEUPDATED ) ;
	exit ;
}

if( ! empty( $_POST['do_delete'] ) ) {

	if ( ! $xoopsGTicket->check( true , 'd3pipes_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$pipe_id = intval( @$_POST['pipe_id'] ) ;
	$db->queryF( "DELETE FROM ".$db->prefix($mydirname."_pipes")." WHERE pipe_id=$pipe_id" ) ;

	// remove cache
	d3pipes_common_delete_all_cache( $mydirname , $pipe_id ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=pipe" , 3 , _MD_A_D3PIPES_MSG_PIPEUPDATED ) ;
	exit ;
}

if( ! empty( $_POST['do_batchupdate'] ) ) {

	if ( ! $xoopsGTicket->check( true , 'd3pipes_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	foreach( $_POST['name'] as $pipe_id => $name ) {
		$pipe_id = intval( $pipe_id ) ;
		$name4sql = mysql_real_escape_string($myts->stripSlashesGPC( $name )) ;
		$weight4sql = intval( @$_POST['weight'][$pipe_id] ) ;
		$flags4sql = '' ;
		foreach( array( 'main_disp' , 'main_list' , 'main_aggr' , 'main_rss' , 'block_disp' , 'in_submenu' ) as $key ) {
			$flags4sql .= ",`$key`=".( empty( $_POST[$key][$pipe_id] ) ? '0' : '1' ) ;
		}
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_pipes")." SET name='$name4sql',weight='$weight4sql' $flags4sql WHERE pipe_id=$pipe_id" ) ;
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=pipe" , 3 , _MD_A_D3PIPES_MSG_PIPEUPDATED ) ;
	exit ;
}



//
// form stage
//

$result = $db->query( "SELECT pipe_id FROM ".$db->prefix($mydirname."_pipes")." ORDER BY weight" ) ;
$pipes4assign = array() ;
while( list( $pipe_id_tmp ) = $db->fetchRow( $result ) ) {
	$pipes4assign[ $pipe_id_tmp ] = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id_tmp ) ;
	$pipes4assign[ $pipe_id_tmp ]['type'] = d3pipes_admin_judge_type_of_pipe( $pipes4assign[ $pipe_id_tmp ]['joints'] ) ;
}


$pipe_id = intval( @$_GET['pipe_id'] ) ;
$blank_joint = array( 'joint' => '' , 'joint_class' => '' , 'option' => '' ) ;

if( $pipe_id == 0 ) {
	// LIST
	$template = 'admin_pipe_list.html' ;
	$pipe4edit = array() ;
} else if( isset( $pipes4assign[ $pipe_id ] ) ) {
	// EDIT (DETAIL)
	$template = 'admin_pipe_edit.html' ;
	$pipe4edit = $pipes4assign[ $pipe_id ] ;
	$pipe4edit['joints'] = array_merge( $pipe4edit['joints'] , array_fill( 0 , 3 , $blank_joint ) ) ;
} else {
	// NEW
	$pipe_id = -1 ;
	$template = 'admin_pipe_edit.html' ;
	$pipe4edit = array(
		'id' => -1 ,
		'name' => '' ,
		'joints' => array(
			array( 'joint' => 'fetch' , 'joint_class' => d3pipes_common_get_default_joint_class( $mydirname , 'fetch' ) , 'option' => '' ) ,
			array( 'joint' => 'parse' , 'joint_class' => d3pipes_common_get_default_joint_class( $mydirname , 'parse' ) , 'option' => '' ) ,
			array( 'joint' => 'utf8to' , 'joint_class' => d3pipes_common_get_default_joint_class( $mydirname , 'utf8to' ) , 'option' => $xoopsModuleConfig['internal_encoding'] ) ,
			array( 'joint' => 'clip' , 'joint_class' => d3pipes_common_get_default_joint_class( $mydirname , 'clip' ) , 'option' => '' ) ,
		) ,
	) ;
	$pipe4edit['joints'] = array_merge( $pipe4edit['joints'] , array_fill( 0 , 3 , $blank_joint ) ) ;
}

// joint_classes options
$joint_classes_options = array() ;
foreach( $all_joints as $joint_type => $joint_type_name ) {
	$joint_classes_options[ $joint_type_name ] = array() ;
	foreach( d3pipes_admin_fetch_classes( $mydirname , $joint_type ) as $joint_class ) {
		$joint_classes_options[ $joint_type_name ][ $joint_type.'::'.$joint_class ] = defined( '_MD_D3PIPES_CLASS_'.strtoupper($joint_type.$joint_class) ) ? constant( '_MD_D3PIPES_CLASS_'.strtoupper($joint_type.$joint_class) ) : $joint_class ;
	}
}
$joint_classes_options[ _NONE ] = array( '::' => '----' ) ;


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'pipe_id' => $pipe_id ,
	'pipes' => $pipes4assign ,
	'pipe' => $pipe4edit ,
	'all_joints' => $all_joints ,
	'joint_classes_options' => $joint_classes_options ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3pipes_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_'.$template ) ;
xoops_cp_footer();

?>