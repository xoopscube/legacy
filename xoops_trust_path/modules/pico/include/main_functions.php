<?php

// this file can be included only from main or admin (not from blocks)


// add fields for tree structure into $categories
function pico_main_make_treeinformations( $data )
{
	$previous_depth = -1 ;
	$path_to_i = array() ;

	for( $i = 0 ; $i < sizeof( $data ) ; $i ++ ) {
		$unique_path = $data[$i]['unique_path'] ;
		$path_to_i[ $unique_path ] = $i ;
		$parent_path = substr( $unique_path , 0 , strrpos( $unique_path , '.' ) ) ;
		if( $parent_path && isset( $path_to_i[ $parent_path ] ) ) {
			$data[ $path_to_i[ $parent_path ] ]['f1s'][ $data[$i]['id'] ] = strrchr( $data[$i]['unique_path'] , '.' ) ;
		}

		$depth_diff = $data[$i]['depth_in_tree'] - @$previous_depth ;
		$previous_depth = $data[$i]['depth_in_tree'] ;
		$data[$i]['ul_in'] = '' ;
		$data[$i]['ul_out'] = '' ;
		if( $depth_diff > 0 ) {
			if( $i > 0 ) {
				$data[$i-1]['first_child_id'] = $data[$i]['id'] ;
			}
			for( $j = 0 ; $j < $depth_diff ; $j ++ ) {
				$data[$i]['ul_in'] .= '<ul><li>' ;
			}
		} else if( $depth_diff < 0 ) {
			for( $j = 0 ; $j < - $depth_diff ; $j ++ ) {
				$data[$i-1]['ul_out'] .= '</li></ul>' ;
			}
			$data[$i-1]['ul_out'] .= '</li>' ;
			$data[$i]['ul_in'] = '<li>' ;
		} else {
			$data[$i-1]['ul_out'] .= '</li>' ;
			$data[$i]['ul_in'] = '<li>' ;
		}
		if( $i > 0 ) {
			$data[$i-1]['next_id'] = $data[$i]['id'] ;
			$data[$i]['prev_id'] = $data[$i-1]['id'] ;
		}
	}
	$data[ sizeof( $data ) - 1 ]['ul_out'] = str_repeat( '</li></ul>' , $previous_depth + 1 ) ;

	return $data ;
}


// get permissions of current user
function pico_main_get_category_permissions_of_current_user( $mydirname , $uid = null )
{
	$db =& Database::getInstance() ;

	if( $uid > 0 ) {
		$user_handler =& xoops_gethandler( 'user' ) ;
		$user =& $user_handler->get( $uid ) ;
	} else {
		$user = @$GLOBALS['xoopsUser'] ;
	}

	if( is_object( $user ) ) {
		$uid = intval( $user->getVar('uid') ) ;
		$groups = $user->getGroups() ;
		if( ! empty( $groups ) ) $whr = "`uid`=$uid || `groupid` IN (".implode(",",$groups).")" ;
		else $whr = "`uid`=$uid" ;
	} else {
		$whr = "`groupid`=".intval(XOOPS_GROUP_ANONYMOUS) ;
	}

	$sql = "SELECT c.cat_id,cp.permissions FROM ".$db->prefix($mydirname."_categories")." c LEFT JOIN ".$db->prefix($mydirname."_category_permissions")." cp ON c.cat_permission_id=cp.cat_id  WHERE ($whr)" ;
	$result = $db->query( $sql ) ;
	if( $result ) while( list( $cat_id , $serialized_permissions ) = $db->fetchRow( $result ) ) {
		$permissions = pico_common_unserialize( $serialized_permissions ) ;
		if( is_array( @$ret[ $cat_id ] ) ) {
			foreach( $permissions as $perm_name => $value ) {
				@$ret[ $cat_id ][ $perm_name ] |= $value ;
			}
		} else {
			$ret[ $cat_id ] = $permissions ;
		}
	}

	if( empty( $ret ) ) return array( 0 => array() ) ;
	else return $ret ;
}


// moderator groups
function pico_main_get_category_moderate_groups4show( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;

	$cat_id = intval( $cat_id ) ;

	$ret = array() ;
	$sql = "SELECT g.groupid, g.name FROM ".$db->prefix($mydirname."_categories")." c LEFT JOIN ".$db->prefix($mydirname."_category_permissions")." cp ON c.cat_permission_id=cp.cat_id LEFT JOIN ".$db->prefix("groups")." g ON cp.groupid=g.groupid WHERE cp.groupid IS NOT NULL AND c.cat_id=".$cat_id." AND cp.permissions LIKE '%s:12:\"is\\_moderator\";i:1;%'" ;

	$mrs = $db->query( $sql ) ;
	while( list( $mod_gid , $mod_gname ) = $db->fetchRow( $mrs ) ) {
		$ret[] = array(
			'gid' => $mod_gid ,
			'gname' => htmlspecialchars( $mod_gname , ENT_QUOTES ) ,
		) ;
	}

	return $ret ;
}


// moderator users
function pico_main_get_category_moderate_users4show( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;

	$cat_id = intval( $cat_id ) ;

	$ret = array() ;
	$sql = "SELECT u.uid, u.uname FROM ".$db->prefix($mydirname."_categories")." c LEFT JOIN ".$db->prefix($mydirname."_category_permissions")." cp ON c.cat_permission_id=cp.cat_id LEFT JOIN ".$db->prefix("users")." u ON cp.uid=u.uid WHERE cp.uid IS NOT NULL AND c.cat_id=".$cat_id." AND cp.permissions LIKE '%s:12:\"is\\_moderator\";i:1;%'" ;

	$mrs = $db->query( $sql ) ;
	while( list( $mod_uid , $mod_uname ) = $db->fetchRow( $mrs ) ) {
		$ret[] = array(
			'uid' => $mod_uid ,
			'uname' => htmlspecialchars( $mod_uname , ENT_QUOTES ) ,
		) ;
	}

	return $ret ;
}


// select box for jumping into a specified category
function pico_main_make_cat_jumpbox_options( $mydirname , $whr4cat , $cat_selected = 0 )
{
	global $myts ;

	$db =& Database::getInstance() ;

	$ret = "" ;
	$sql = "SELECT c.cat_id, c.cat_title, c.cat_depth_in_tree FROM ".$db->prefix($mydirname."_categories")." c WHERE ($whr4cat) ORDER BY c.cat_order_in_tree" ;
	if( $result = $db->query( $sql ) ) {
		while( list( $cat_id , $cat_title , $cat_depth ) = $db->fetchRow( $result ) ) {
			$selected = $cat_id == $cat_selected ? 'selected="selected"' : '' ;
			$ret .= "<option value='$cat_id' $selected>".str_repeat('--',$cat_depth).$myts->makeTboxData4Show($cat_title,1,1)."</option>\n" ;
		}
	} else {
		$ret = "<option value=\"-1\">ERROR</option>\n";
	}

	return $ret ;
}


// trigger event for D3
function pico_main_trigger_event( $mydirname , $category , $item_id , $event , $extra_tags=array() , $user_list=array() , $omit_user_id=null )
{
	require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3NotificationHandler.class.php' ;

	$not_handler =& D3NotificationHandler::getInstance() ;
	$not_handler->triggerEvent( $mydirname , 'pico' , $category , $item_id , $event , $extra_tags , $user_list , $omit_user_id ) ;
}


// get category's moderators as array
function pico_main_get_moderators( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;
	$cat_id = intval( $cat_id ) ;
	$cat_uids = array() ;

	// get uid directly
	$sql = "SELECT `uid` FROM ".$db->prefix($mydirname."_categories")." c LEFT JOIN ".$db->prefix($mydirname."_category_permissions")." cp ON c.cat_permission_id=cp.cat_id WHERE c.`cat_id`=$cat_id AND `uid` IS NOT NULL AND permissions LIKE '%is\\_moderator\";i:1%'" ;
	$result = $db->query( $sql ) ;
	while( list( $uid ) = $db->fetchRow( $result ) ) {
		$cat_uids[] = $uid ;
	}

	// get uid via groupid
	$sql = "SELECT distinct cp.groupid FROM ".$db->prefix($mydirname."_categories")." c LEFT JOIN ".$db->prefix($mydirname."_category_permissions")." cp ON c.cat_permission_id=cp.cat_id WHERE c.`cat_id`=$cat_id AND cp.`groupid` IS NOT NULL AND permissions LIKE '%is\\_moderator\";i:1%'" ;
	$result = $db->query( $sql ) ;
	$groupids = array() ;
	while( list( $groupid ) = $db->fetchRow( $result ) ) {
		$groupids[] = $groupid ;
	}
	if( ! empty( $groupids ) ) {
		$sql = "SELECT distinct uid FROM ".$db->prefix("groups_users_link")." WHERE groupid IN (".implode(",",$groupids).")" ;
		$result = $db->query( $sql ) ;
		while( list( $uid ) = $db->fetchRow( $result ) ) {
			$cat_uids[] = $uid ;
		}
	}

	return array_unique( $cat_uids ) ;
}



// get top $content_id from $cat_id
function pico_main_get_top_content_id_from_cat_id( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;

	list( $content_id ) = $db->fetchRow( $db->query( "SELECT o.content_id FROM ".$db->prefix($mydirname."_contents")." o WHERE o.cat_id=".intval($cat_id)." AND o.visible AND o.created_time <= UNIX_TIMESTAMP() AND o.expiring_time > UNIX_TIMESTAMP() ORDER BY o.weight,o.content_id LIMIT 1" ) ) ;

	return intval( $content_id ) ;
}


// escape string for <a href="mailto:..."> (eg. tellafriend)
function pico_main_escape4mailto( $text )
{
	if( function_exists( 'mb_convert_encoding' ) && defined( '_MD_PICO_MAILTOENCODING' ) ) {
		$text = mb_convert_encoding( $text , _MD_PICO_MAILTOENCODING ) ;
	}
	return rawurlencode( $text ) ;
}


// get filter's informations under XOOPS_TRUST_PATH/modules/pico/filters/
function pico_main_get_filter_infos( $filters_separated_pipe , $isadminormod = false )
{
	global $xoopsModuleConfig ;

	// forced & prohibited filters
	$filters_forced = array_map( 'trim' , explode( ',' , str_replace( ':LAST' , '' , @$xoopsModuleConfig['filters_forced'] ) ) ) ;
	$filters_prohibited = array_map( 'trim' , explode( ',' , @$xoopsModuleConfig['filters_prohibited'] ) ) ;

	$filters = array() ;
	$dh = opendir( XOOPS_TRUST_PATH.'/modules/pico/filters' ) ;
	while( ( $file = readdir( $dh ) ) !== false ) {
		if( preg_match( '/^pico\_(.*)\.php$/' , $file , $regs ) ) {
			$name = $regs[1] ;
			$constpref = '_MD_PICO_FILTERS_' . strtoupper( $name ) ;

			require_once dirname(dirname(__FILE__)).'/filters/pico_'.$name.'.php' ;

			// check the filter is secure or not
			if( ! $isadminormod && defined( $constpref.'ISINSECURE' ) ) continue ;
			// prohibited
			if( in_array( $name , $filters_prohibited ) ) continue ;

			$filters[ $name ] = array(
				'title' => defined( $constpref.'TITLE' ) ? constant( $constpref.'TITLE' ) : $name ,
				'desc' => defined( $constpref.'DESC' ) ? constant( $constpref.'DESC' ) : '' ,
				'weight' => defined( $constpref.'INITWEIGHT' ) ? constant( $constpref.'INITWEIGHT' ) : 0 ,
				'enabled' => false ,
			) ;

			// forced
			if( in_array( $name , $filters_forced ) ) {
				$filters[ $name ]['enabled'] = true ;
				$filters[ $name ]['fixed'] = true ;
			}
		}
	}

	$current_filters = explode( '|' , $filters_separated_pipe ) ;
	$weight = 0 ;
	foreach( $current_filters as $current_filter ) {
		if( ! empty( $filters[ $current_filter ] ) ) {
			$weight += 10 ;
			$filters[ $current_filter ]['weight'] = $weight ;
			$filters[ $current_filter ]['enabled'] = true ;
		}
	}

	uasort( $filters , 'pico_main_filter_cmp' ) ;

	return $filters ;
}


// for usort() in pico_main_get_filter_infos()
function pico_main_filter_cmp( $a , $b )
{
	if( $a['enabled'] != $b['enabled'] ) {
		return $a['enabled'] ? -1 : 1 ;
	} else {
		return $a['weight'] > $b['weight'] ? 1 : -1 ;
	}
}


// get return_uri from "ret" after editing
function pico_main_parse_ret2uri( $mydirname , $ret )
{
	if( preg_match( '/^([a-z]{2})([0-9-]*)$/' , $ret , $regs ) ) {
		// specify it by codes inside the module like ret=mm, ret=mc0 or ret=ac0
		$id = intval( $regs[2] ) ;
		switch( $regs[1] ) {
			case 'ac' :
				return XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?page=contents&cat_id='.$id ;
			case 'mc' :
				return XOOPS_URL.'/modules/'.$mydirname.'/index.php?cat_id='.$id ;
			case 'mm' :
				return XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=menu' ;
			default :
				return false ;
		}
	} else if( $ret{0} == '/' ) {
		// specify the relative link inside XOOPS_URL
		return XOOPS_URL.str_replace( '..' , '' , preg_replace( '/[\x00-\x1f]/' , '' , $ret ) ) ;
	} else {
		return false ;
	}
}


// get <link> to CSS for main
function pico_main_render_moduleheader( $mydirname , $mod_config , $appendix_header4disp = '' )
{
	$css_uri4disp = htmlspecialchars( @$mod_config['css_uri'] , ENT_QUOTES ) ;

	$header4disp = '<link rel="stylesheet" type="text/css" media="all" href="'.$css_uri4disp.'" />'."\n".@$mod_config['htmlheader']."\n".$appendix_header4disp."\n" ;

	$searches = array( '{mod_url}' , '<{$mod_url}>' , '<{$mydirname}>' , '{X_SITEURL}' , '<{$xoops_url}>' ) ;
	$replacements = array( XOOPS_URL.'/modules/'.$mydirname , XOOPS_URL.'/modules/'.$mydirname , $mydirname , XOOPS_URL.'/' , XOOPS_URL ) ;

	return str_replace( $searches , $replacements , $header4disp ) ;
}


// get directories recursively under WRAP
function pico_main_get_wraps_directories_recursively( $mydirname , $dir_path = '/' )
{
	$full_dir_path = XOOPS_TRUST_PATH._MD_PICO_WRAPBASE.'/'.$mydirname.$dir_path ;	if( ! is_dir( $full_dir_path ) ) return array() ;

	$dir_path4key = substr( $dir_path , 0 , -1 ) ;
	$full_dir_path4disp = htmlspecialchars( 'XOOPS_TRUST_PATH'._MD_PICO_WRAPBASE.'/'.$mydirname.$dir_path4key , ENT_QUOTES ) ;

	// make an option will be displayed
	$db =& Database::getInstance() ;
	$myrow = $db->fetchArray( $db->query( "SELECT cat_title,cat_depth_in_tree FROM ".$db->prefix($mydirname."_categories")." WHERE cat_vpath='".addslashes($dir_path4key)."'" ) ) ;
	$ret[ $dir_path4key ] = empty( $myrow ) ? $full_dir_path4disp : $full_dir_path4disp.' ('.str_repeat('--',$myrow['cat_depth_in_tree']).htmlspecialchars( $myrow['cat_title'] , ENT_QUOTES ).')' ;

	// sub directries loop (1)
	$dir_tmps = array() ;
	$dh = opendir( $full_dir_path ) ;
	while( ( $file = readdir( $dh ) ) !== false ) {
		if( substr( $file , 0 , 1 ) == '.' ) continue ;
		if( is_dir( $full_dir_path . $file ) ) {
			$dir_tmps[] = $file ;
		}
	}
	closedir( $dh ) ;

	// sub directries loop (2)
	foreach( $dir_tmps as $dir_tmp ) {
		$ret += pico_main_get_wraps_directories_recursively( $mydirname , $dir_path.$dir_tmp.'/' ) ;
	}

	return $ret ;
}


// get files recursively under WRAP
function pico_main_get_wraps_files_recursively( $mydirname , $dir_path = '/' )
{
	$full_dir_path = XOOPS_TRUST_PATH._MD_PICO_WRAPBASE.'/'.$mydirname.$dir_path ;	if( ! is_dir( $full_dir_path ) ) return array() ;

	$ret = array() ;
	$db =& Database::getInstance() ;

	// parse currenct directry
	$dir_tmps = array() ;
	$file_tmps = array() ;
	$dh = opendir( $full_dir_path ) ;
	while( ( $file = readdir( $dh ) ) !== false ) {
		if( substr( $file , 0 , 1 ) == '.' ) continue ;
		if( is_dir( $full_dir_path . $file ) ) {
			$dir_tmps[] = $file ;
		} else if( is_file( $full_dir_path . $file ) ) {
			$ext = strtolower( substr( strrchr( $file , '.' ) , 1 ) ) ;
			if( in_array( $ext , explode( '|' , _MD_PICO_EXTS4HTMLWRAPPING ) ) ) {
				$file_tmps[] = $file ;
			}
		}
	}
	closedir( $dh ) ;

	// files
	foreach( $file_tmps as $file_tmp ) {
		$file_path4key = $dir_path . $file_tmp ;
		$ret[ $file_path4key ] = htmlspecialchars( 'XOOPS_TRUST_PATH'._MD_PICO_WRAPBASE.'/'.$mydirname.$file_path4key , ENT_QUOTES ) ;
		$myrow = $db->fetchArray( $db->query( "SELECT subject FROM ".$db->prefix($mydirname."_contents")." WHERE vpath='".addslashes($file_path4key)."'" ) ) ;
		if( ! empty( $myrow ) ) {
			$ret[ $file_path4key ] .= ' (' . htmlspecialchars( xoops_substr( $myrow['subject'] , 0 , 20 ) , ENT_QUOTES ) . ')' ;
		}
	}

	// subdirs
	foreach( $dir_tmps as $dir_tmp ) {
		$ret += pico_main_get_wraps_files_recursively( $mydirname , $dir_path.$dir_tmp.'/' ) ;
	}

	return $ret ;
}



?>