<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$module_handler =& xoops_gethandler( 'module' ) ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

//
// transaction stage
//

if( ! empty( $_POST['joints'] ) ) {

	if ( ! $xoopsGTicket->check( true , 'd3pipes_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$pipe_ids = array() ;
	list( $weight ) = $db->fetchRow( $db->query( "SELECT MAX(weight) FROM ".$db->prefix($mydirname."_pipes") ) ) ;
	$weight = intval( $weight ) + 1 ;
	foreach( $_POST['joints'] as $joint ) {
		@list( $type , $class , $dirname ) = explode( '::' , $joint ) ;
		if( empty( $class ) ) continue ;
		$module =& $module_handler->getByDirname( $dirname ) ;
		if( ! is_object( $module ) ) continue ;
		$joints = array( array( 'joint' => $type , 'joint_class' => $class , 'option' => $dirname ) ) ;
		// make a sql for each block's pipe
		$values4sql = "UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'".mysql_real_escape_string(serialize($joints))."','".mysql_real_escape_string($module->getVar('name','n'))."','".mysql_real_escape_string(XOOPS_URL.'/modules/'.$module->getVar('dirname').'/')."','$weight'" ;
		$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_pipes")." (created_time,modified_time,joints,name,url,weight) VALUES ($values4sql)" ) ;
		$pipe_ids[] = $db->getInsertId() ;
		$weight ++ ;
	}

	// make a sql for the union pipe
	if( ! empty( $_POST['create_union_pipe'] ) ) {
		$values4sql = "UNIX_TIMESTAMP(),UNIX_TIMESTAMP(),'".mysql_real_escape_string(serialize(array(array('joint'=>'union','joint_class'=>'mergesort','option'=>implode(',',$pipe_ids)))))."','"._MD_A_D3PIPES_TITLE_WIZ_INNERUNION."','".mysql_real_escape_string(XOOPS_URL.'/')."','$weight'" ;
		$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_pipes")." (created_time,modified_time,joints,name,url,weight) VALUES ($values4sql)" ) ;
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=pipe" , 3 , _MD_A_D3PIPES_MSG_PIPEUPDATED ) ;
	exit ;
}

//
// form stage
//

$modules = array() ;
foreach( $module_handler->getList( null , true ) as $dirname => $name ) {
	$modules[ $dirname ] = array(
		'name' => $name ,
		'joints' => array() ,
	) ;
}

$joint_objs = d3pipes_common_get_joint_objects( $mydirname , 'block' ) ;
foreach( $joint_objs as $joint_obj ) {
	foreach( $joint_obj->getValidDirnames() as $dirname ) {
		if( isset( $modules[ $dirname ] ) ) {
			$modules[ $dirname ][ 'joints' ][] = array(
				'type' => 'block' ,
				'class' => strtolower( substr( get_class( $joint_obj ) , strlen( 'D3pipesBlock' ) ) ) ,
				'name' => $joint_obj->getMyname4disp() ,
			) ;
		}
	}
}


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
	'yesno_options' => array( 1 => _YES , 0 => _NO ) ,
	'modules' => $modules ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3pipes_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_wizard_inner.html' ) ;
xoops_cp_footer();

?>