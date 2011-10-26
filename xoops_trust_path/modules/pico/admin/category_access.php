<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

// get info of the category
$cat_id = intval( @$_GET['cat_id'] ) ;
list( $cat_id , $pid , $cat_title , $redundants_serialized , $cat_permission_id ) = $db->fetchRow( $db->query( "SELECT cat_id,pid,cat_title,cat_redundants,cat_permission_id FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$cat_id" ) ) ;
if( empty( $cat_id ) ) {
	$cat_id = 0 ;
	$cat_title = _MD_PICO_TOP ;
}
$redundants = pico_common_unserialize( $redundants_serialized ) ;

include dirname(dirname(__FILE__)).'/include/category_permissions.inc.php' ;

//
// transaction stage
//

// independent permission update
if( ! empty( $_POST['independentpermission_update'] ) && $cat_id != 0 ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	if( ! empty( $_POST['independentpermission'] ) ) {
		// update permission_id of categories has the same permission_id and childlen of the category
		$whr_cid = ! empty( $redundants['subcategories_ids_cs'] ) ? "cat_id IN (".$redundants['subcategories_ids_cs'].$cat_id.")" : 'cat_id='.$cat_id ;
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_categories")." SET cat_permission_id=$cat_id WHERE cat_permission_id=$cat_permission_id AND ($whr_cid)" ) ;
	} else {
		// remove all category_permissions of the cat_id
		$db->queryF( "DELETE FROM ".$db->prefix($mydirname."_category_permissions")." WHERE cat_id=$cat_id" ) ;
		// get cat_permission_id of the parent category
		list( $cat_permission_id ) = $db->fetchRow( $db->query( "SELECT cat_permission_id FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$pid" ) ) ;
		// update permission_id of categories which permission_id is the cat_id
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_categories")." SET cat_permission_id=".intval($cat_permission_id)." WHERE cat_permission_id=$cat_id" ) ;
	}
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id" , 3 , _MD_PICO_MSG_UPDATED ) ;
	exit ;
}


// group update
if( ! empty( $_POST['group_update'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->queryF( "DELETE FROM ".$db->prefix($mydirname."_category_permissions")." WHERE cat_id=$cat_id AND groupid>0" ) ;
	$result = $db->query( "SELECT groupid FROM ".$db->prefix("groups") ) ;
	while( list( $gid ) = $db->fetchRow( $result ) ) {
		if( ! empty( $_POST['can_read'][$gid] ) ) {
			$perms = array() ;
			foreach( $pico_category_permissions as $perm_name ) {
				$perms[$perm_name] = empty( $_POST[$perm_name][$gid] ) ? 0 : 1 ;
			}
			$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_category_permissions")." (cat_id,groupid,permissions) VALUES ($cat_id,$gid,'".mysql_real_escape_string(serialize($perms))."')" ) ;
		}
	}
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id" , 3 , _MD_PICO_MSG_UPDATED ) ;
	exit ;
}

// user update
if( ! empty( $_POST['user_update'] ) ) {
	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->queryF( "DELETE FROM ".$db->prefix($mydirname."_category_permissions")." WHERE cat_id=$cat_id AND uid>0" ) ;

	if( is_array( @$_POST['can_read'] ) ) foreach( $_POST['can_read'] as $uid => $can_read ) {
		$uid = intval( $uid ) ;
		if( $can_read ) {
			$perms = array() ;
			foreach( $pico_category_permissions as $perm_name ) {
				$perms[$perm_name] = empty( $_POST[$perm_name][$uid] ) ? 0 : 1 ;
			}
			$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_category_permissions")." (cat_id,uid,permissions) VALUES ($cat_id,$uid,'".mysql_real_escape_string(serialize($perms))."')" ) ;
		}
	}
	
	$member_hander =& xoops_gethandler( 'member' ) ;
	if( is_array( @$_POST['new_uids'] ) ) foreach( array_keys( $_POST['new_uids'] ) as $i ) {
		if( empty( $_POST['new_can_read'][$i] ) ) continue ;
		if( empty( $_POST['new_uids'][$i] ) ) {
			// add new user by uname
			$criteria = new Criteria( 'uname' , mysql_real_escape_string( @$_POST['new_unames'][$i] ) ) ;
			@list( $user ) = $member_handler->getUsers( $criteria ) ;
		} else {
			// add new user by uid
			$user =& $member_handler->getUser( intval( $_POST['new_uids'][$i] ) ) ;
		}
		// check the user is valid
		if( ! is_object( $user ) ) continue ;
		$uid = $user->getVar( 'uid' ) ;

		$perms = array( 'can_read' => 1 ) ;
		foreach( $pico_category_permissions as $perm_name ) {
			$perms[$perm_name] = empty( $_POST['new_'.$perm_name][$i] ) ? 0 : 1 ;
		}
		$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_category_permissions")." (cat_id,uid,permissions) VALUES ($cat_id,$uid,'".mysql_real_escape_string(serialize($perms))."')" ) ;
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id" , 3 , _MD_PICO_MSG_UPDATED ) ;
	exit ;
}



//
// form stage
//

// category options as array
$cat_options = pico_common_get_cat_options( $mydirname ) ;

// create permissions4assign
$permissions4assign = array() ;
foreach( $pico_category_permissions as $perm_name ) {
	$permissions4assign[$perm_name] = constant( '_MD_PICO_PERMS_'.strtoupper( $perm_name ) ) ;
}

// create group form
$group_handler =& xoops_gethandler( 'group' ) ;
$groups =& $group_handler->getObjects() ;
$groups4assign = array() ;
foreach( $groups as $group ) {
	$gid = $group->getVar('groupid') ;

	$cprs = $db->query( "SELECT permissions FROM ".$db->prefix($mydirname."_category_permissions")." WHERE groupid=".$group->getVar('groupid')." AND cat_id=$cat_permission_id" ) ;
	if( $db->getRowsNum( $cprs ) > 0 ) {
		list( $serialized_gpermissions ) = $db->fetchRow( $cprs ) ;
		$gpermissions = pico_common_unserialize( $serialized_gpermissions ) ;
	} else {
		$gpermissions = array() ;
	}

	$groups4assign[] = array(
		'gid' => $gid ,
		'name' => $group->getVar('name') ,
		'perms' => $gpermissions ,
	) ;
}


// create user form
$users4assign = array() ;
$cprs = $db->query( "SELECT u.uid,u.uname,cp.permissions FROM ".$db->prefix($mydirname."_category_permissions")." cp LEFT JOIN ".$db->prefix("users")." u ON cp.uid=u.uid WHERE cp.cat_id=$cat_permission_id AND cp.groupid IS NULL ORDER BY u.uid ASC" ) ;
$user_trs = '' ;
while( list( $uid , $uname , $serialized_upermissions ) = $db->fetchRow( $cprs ) ) {

	$uid = intval( $uid ) ;
	$upermissions = pico_common_unserialize( $serialized_upermissions ) ;

	$users4assign[] = array(
		'uid' => $uid ,
		'name' => htmlspecialchars( $uname , ENT_QUOTES ) ,
		'perms' => $upermissions ,
	) ;
}


// create new user form
$new_users4assign = array() ;
for( $i = 0 ; $i < 5 ; $i ++ ) {
	$new_users4assign[] = array(
		'nid' => $i ,
		'perms' => array( 'can_read' => 1 ) ,
	) ;
}


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'cat_id' => $cat_id ,
	'cat_permission_id' => $cat_permission_id ,
	'cat_link' => pico_common_make_category_link4html( $xoopsModuleConfig , $cat_id , $mydirname ) ,
	'cat_title' => htmlspecialchars( $cat_title , ENT_QUOTES ) ,
	'cat_options' => $cat_options ,
	'permissions' => $permissions4assign ,
	'groups' => $groups4assign ,
	'users' => $users4assign ,
	'new_users' => $new_users4assign ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'pico_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_category_access.html' ) ;
xoops_cp_footer();

?>