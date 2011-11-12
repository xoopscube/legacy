<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;

// singleton
class PicoPermission {

var $db = null ;  // Database instance
var $uid = 0 ; // intval
var $permissions = array() ; // [dirname][permission_id] or [dirname]['is_module_admin']

function PicoPermission()
{
	global $xoopsUser ;

	$this->db =& Database::getInstance() ;
	$this->uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;
}

function &getInstance()
{
	static $instance ;
	if( ! isset( $instance ) ) {
		$instance = new PicoPermission() ;
	}
	return $instance ;
}

function getPermissions( $mydirname )
{
	if( empty( $this->permissions[ $mydirname ] ) ) {
		$this->permissions[ $mydirname ] = $this->queryPermissions( $mydirname ) ;
	}
	return @$this->permissions[ $mydirname ] ;
}

function queryPermissions( $mydirname )
{
	$ret = array() ;

	if( $this->uid > 0 ) {
		$user_handler =& xoops_gethandler( 'user' ) ;
		$user =& $user_handler->get( $this->uid ) ;
	}

	$is_module_admin = false ;
	if( is_object( @$user ) ) {
		// is_module_admin
		$module_handler =& xoops_gethandler( 'module' ) ;
		$moduleObj =& $module_handler->getByDirname( $mydirname ) ;
		if( is_object( $moduleObj ) && $user->isAdmin( $moduleObj->getVar('mid') ) ) {
			$is_module_admin = true ;
		}
	}

	if( is_object( @$user ) ) {
		$groups = $user->getGroups() ;
		if( ! empty( $groups ) ) $whr = "`uid`=$this->uid || `groupid` IN (".implode(",",$groups).")" ;
		else $whr = "`uid`=$this->uid" ;
	} else {
		$whr = "`groupid`=".intval(XOOPS_GROUP_ANONYMOUS) ;
	}

	$sql = "SELECT cat_id,permissions FROM ".$this->db->prefix($mydirname."_category_permissions")." WHERE ($whr)" ;
	$result = $this->db->query( $sql ) ;
	if( $result ) while( list( $cat_id , $serialized_permissions ) = $this->db->fetchRow( $result ) ) {
		$permissions = pico_common_unserialize( $serialized_permissions ) ;
		if( is_array( @$ret[ $cat_id ] ) ) {
			foreach( $permissions as $perm_name => $value ) {
				@$ret[ $cat_id ][ $perm_name ] |= $value ;
			}
		} else {
			$ret[ $cat_id ] = $permissions ;
		}
	}

	if( empty( $ret ) ) return array( 0 => array() , 'is_module_admin' => $is_module_admin ) ;
	else return $ret + array( 'is_module_admin' => $is_module_admin ) ;
}


function getUidsFromCatid( $mydirname , $cat_id , $permission_type = '' )
{
	// prepare $type
	$whr_type = $permission_type ? "permissions LIKE '%".$permission_type."\";i:1%'" : '1' ;

	// get permission_id
	$cat_id = intval( $cat_id ) ;
	$sql = "SELECT cat_permission_id FROM ".$this->db->prefix($mydirname."_categories")." WHERE cat_id=$cat_id" ;
	list( $permission_id ) = $this->db->fetchRow( $this->db->query( $sql ) ) ;

	// uid
	$uids = array() ;
	$sql = "SELECT uid FROM ".$this->db->prefix($mydirname."_category_permissions")." WHERE cat_id=$permission_id AND uid IS NOT NULL AND ($whr_type)" ;
	$result = $this->db->query( $sql ) ;
	while( list( $uid ) = $this->db->fetchRow( $result ) ) {
		$uids[] = $uid ;
	}

	// groupid * groups_users_link
	$sql = "SELECT distinct g.uid FROM ".$this->db->prefix($mydirname."_category_permissions")." x , ".$this->db->prefix("groups_users_link")." g WHERE x.groupid=g.groupid AND x.cat_id=$permission_id AND x.groupid IS NOT NULL AND ($whr_type)" ;
	$result = $this->db->query( $sql ) ;
	while( list( $uid ) = $this->db->fetchRow( $result ) ) {
		$uids[] = $uid ;
	}
	$uids = array_unique( $uids ) ;

	return $uids ;
}

}



?>