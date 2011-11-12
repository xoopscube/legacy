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

if( ! empty( $_POST['do_update'] ) ) {

	foreach( array_keys( $all_joints ) as $joint_type ) {
		$valid_classes = d3pipes_admin_fetch_classes( $mydirname , $joint_type ) ;
		if( ! empty( $_POST['jointclass'][ $joint_type ] ) ) {
			$joint_class = $_POST['jointclass'][ $joint_type ] ;
			if( isset( $valid_classes[ $joint_class ] ) ) {
				$db->query( "DELETE FROM ".$db->prefix($mydirname."_joints")." WHERE joint_type='".mysql_real_escape_string($joint_type)."'" ) ;
				$db->query( "INSERT INTO ".$db->prefix($mydirname."_joints")." (joint_type,default_class) VALUES ('".mysql_real_escape_string($joint_type)."','".mysql_real_escape_string($joint_class)."')" ) ;
			}
		}
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=joint" , 3 , _MD_A_D3PIPES_MSG_CACHEDELETED ) ;
	exit ;
}

//
// form stage
//

$joints4assign = array() ;
foreach( $all_joints as $joint_type => $joint_name4disp ) {
	$joints4assign[ $joint_type ] = array(
		'type' => $joint_type ,
		'name' => $joint_name4disp ,
		'classes' => d3pipes_admin_fetch_classes( $mydirname , $joint_type ) ,
		'selected_class' => d3pipes_common_get_default_joint_class( $mydirname , $joint_type ) ,
	) ;
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
	'joints' => $joints4assign ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_joint.html' ) ;
xoops_cp_footer();

?>