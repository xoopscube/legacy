<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/class/gtickets.php';

( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &( new MyTextSanitizer )->getInstance();
$db = XoopsDatabaseFactory::getDatabaseConnection();

// get info of the category
$cat_id = (int) @$_GET['cat_id'];
[
	$cat_id,
	$pid,
	$cat_title,
	$redundants_serialized,
	$cat_permission_id
] = $db->fetchRow( $db->query( 'SELECT cat_id,pid,cat_title,cat_redundants,cat_permission_id FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE cat_id=$cat_id" ) );
if ( empty( $cat_id ) ) {
	$cat_id    = 0;
	$cat_title = _MD_PICO_TOP;
}
$redundants = pico_common_unserialize( $redundants_serialized );

include dirname( __DIR__ ) . '/include/category_permissions.inc.php';

//
// transaction stage
//

// independent permission update
if ( !empty( $_POST['independentpermission_update'] ) && 0 != $cat_id) {
	if ( !$xoopsGTicket->check( true, 'pico_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 2, $xoopsGTicket->getErrors() );
	}
	if ( !empty( $_POST['independentpermission'] ) ) {
		// update permission_id of categories has the same permission_id and children of the category
		$whr_cid = ! empty( $redundants['subcategories_ids_cs'] ) ? 'cat_id IN (' . $redundants['subcategories_ids_cs'] . $cat_id . ')' : 'cat_id=' . $cat_id;
		$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_categories' ) . " SET cat_permission_id=$cat_id WHERE cat_permission_id=$cat_permission_id AND ($whr_cid)" );
	} else {
		// remove all category_permissions of the cat_id
		$db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . " WHERE cat_id=$cat_id" );
		// get cat_permission_id of the parent category
		[ $cat_permission_id ] = $db->fetchRow( $db->query( 'SELECT cat_permission_id FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE cat_id=$pid" ) );
		// update permission_id of categories which permission_id is the cat_id
		$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_categories' ) . ' SET cat_permission_id=' . (int) $cat_permission_id . " WHERE cat_permission_id=$cat_id" );
	}
	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id", 1, _MD_PICO_MSG_UPDATED );
	exit;
}


// group update
if ( !empty( $_POST['group_update'] ) ) {
	if ( !$xoopsGTicket->check( true, 'pico_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 2, $xoopsGTicket->getErrors() );
	}
	$db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . " WHERE cat_id=$cat_id AND groupid>0" );
	$result = $db->query( 'SELECT groupid FROM ' . $db->prefix( 'groups' ) );
	while ( [$gid] = $db->fetchRow( $result ) ) {
		if ( !empty( $_POST['can_read'][ $gid ] ) ) {
			$perms = [];
			foreach ( $pico_category_permissions as $perm_name ) {
				$perms[ $perm_name ] = empty( $_POST[ $perm_name ][ $gid ] ) ? 0 : 1;
			}
			$db->queryF( 'INSERT INTO ' . $db->prefix( $mydirname . '_category_permissions' ) . " (cat_id,groupid,permissions) VALUES ($cat_id,$gid," . $db->quoteString( serialize( $perms ) ) . ')' );
		}
	}
	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id", 1, _MD_PICO_MSG_UPDATED );
	exit;
}

// user update
if ( !empty( $_POST['user_update'] ) ) {
	if ( !$xoopsGTicket->check( true, 'pico_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 2, $xoopsGTicket->getErrors() );
	}
	$db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . " WHERE cat_id=$cat_id AND uid>0" );

	if ( is_array( @$_POST['can_read'] ) ) {
		foreach ( $_POST['can_read'] as $uid => $can_read ) {
			$uid = (int) $uid;
			if ( $can_read ) {
				$perms = [];
				foreach ( $pico_category_permissions as $perm_name ) {
					$perms[ $perm_name ] = empty( $_POST[ $perm_name ][ $uid ] ) ? 0 : 1;
				}
				$db->queryF( 'INSERT INTO ' . $db->prefix( $mydirname . '_category_permissions' ) . " (cat_id,uid,permissions) VALUES ($cat_id,$uid," . $db->quoteString( serialize( $perms ) ) . ')' );
			}
		}
	}

	$member_hander = &xoops_gethandler( 'member' );
	if ( is_array( @$_POST['new_uids'] ) ) {
		foreach ( array_keys( $_POST['new_uids'] ) as $i ) {
			if ( empty( $_POST['new_can_read'][ $i ] ) ) {
				continue;
			}
			if ( empty( $_POST['new_uids'][ $i ] ) ) {
				// add new user by uname
				$uname    = $db->quoteString( @$_POST['new_unames'][ $i ] );
				$uname    = substr( $uname, 1, - 1 );
				$criteria = new Criteria( 'uname', $uname );
				@[$user] = $member_handler->getUsers( $criteria );
			} else {
				// add new user by uid
				$user = &$member_handler->getUser( (int) $_POST['new_uids'][ $i ] );
			}
			// check the user is valid
			if ( !is_object( $user ) ) {
				continue;
			}
			$uid = $user->getVar( 'uid' );

			$perms = [ 'can_read' => 1 ];
			foreach ( $pico_category_permissions as $perm_name ) {
				$perms[ $perm_name ] = empty( $_POST[ 'new_' . $perm_name ][ $i ] ) ? 0 : 1;
			}
			$db->queryF( 'INSERT INTO ' . $db->prefix( $mydirname . '_category_permissions' ) . " (cat_id,uid,permissions) VALUES ($cat_id,$uid," . $db->quoteString( serialize( $perms ) ) . ')' );
		}
	}

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=category_access&amp;cat_id=$cat_id", 1, _MD_PICO_MSG_UPDATED );
	exit;
}

//
// form stage
//

// category options as array
$cat_options = pico_common_get_cat_options( $mydirname );

// create permissions4assign
$permissions4assign = [];
foreach ( $pico_category_permissions as $perm_name ) {
	$permissions4assign[ $perm_name ] = constant( '_MD_PICO_PERMS_' . strtoupper( $perm_name ) );
}

// create group form
$group_handler = &xoops_gethandler( 'group' );
$groups        = &$group_handler->getObjects();
$groups4assign = [];
foreach ( $groups as $group ) {
	$gid = $group->getVar( 'groupid' );

	$cprs = $db->query( 'SELECT permissions FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . ' WHERE groupid=' . $group->getVar( 'groupid' ) . " AND cat_id=$cat_permission_id" );
	if ( $db->getRowsNum( $cprs ) > 0 ) {
		[ $serialized_gpermissions ] = $db->fetchRow( $cprs );
		$gpermissions = pico_common_unserialize( $serialized_gpermissions );
	} else {
		$gpermissions = [];
	}

	$groups4assign[] = [
		'gid'   => $gid,
		'name'  => $group->getVar( 'name' ),
		'perms' => $gpermissions,
	];
}


// create user form
$users4assign = [];
$cprs         = $db->query( 'SELECT u.uid,u.uname,cp.permissions FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . ' cp LEFT JOIN ' . $db->prefix( 'users' ) . " u ON cp.uid=u.uid WHERE cp.cat_id=$cat_permission_id AND cp.groupid IS NULL ORDER BY u.uid " );
$user_trs     = '';
while ( [$uid, $uname, $serialized_upermissions] = $db->fetchRow( $cprs ) ) {

	$uid          = (int) $uid;
	$upermissions = pico_common_unserialize( $serialized_upermissions );

	$users4assign[] = [
		'uid'   => $uid,
		'name'  => htmlspecialchars( $uname, ENT_QUOTES ),
		'perms' => $upermissions,
	];
}


// create new user form
$new_users4assign = [];
for ( $i = 0; $i < 5; $i ++ ) {
	$new_users4assign[] = [
		'nid'   => $i,
		'perms' => [ 'can_read' => 1 ],
	];
}


// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';
$tpl = new XoopsTpl();
$tpl->assign(
	[
		'mydirname'         => $mydirname,
		'mod_name'          => $xoopsModule->getVar( 'name' ),
		'mod_url'           => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'      => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'        => $xoopsModuleConfig,
		'cat_id'            => $cat_id,
		'cat_permission_id' => $cat_permission_id,
		'cat_link'          => pico_common_make_category_link4html( $xoopsModuleConfig, $cat_id, $mydirname ),
		'cat_title'         => htmlspecialchars( $cat_title, ENT_QUOTES ),
		'cat_options'       => $cat_options,
		'permissions'       => $permissions4assign,
		'groups'            => $groups4assign,
		'users'             => $users4assign,
		'new_users'         => $new_users4assign,
		'gticket_hidden'    => $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'pico_admin' ),
	]
);
$tpl->display( 'db:' . $mydirname . '_admin_category_access.html' );

xoops_cp_footer();
