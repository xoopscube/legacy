<?php
/**
 * D3Forum module for XCL
 * This file can be included only from main or admin (not from blocks)
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 * @brief      add fields for tree structure into $posts or $categories
 */

function d3forum_make_treeinformations( $data ) {
	$previous_depth = - 1;

	$path_to_i = [];

	foreach ( $data as $i => $iValue ) {
		$unique_path               = $data[ $i ]['unique_path'];
		$path_to_i[ $unique_path ] = $i;
		$parent_path               = substr( $unique_path, 0, strrpos( $unique_path, '.' ) );
		if ( $parent_path && isset( $path_to_i[ $parent_path ] ) ) {
			$data[ $path_to_i[ $parent_path ] ]['f1s'][ $iValue['id'] ] = strrchr( $iValue['unique_path'], '.' );
		}

		$depth_diff = $iValue['depth_in_tree'] - @$previous_depth;

		$previous_depth = $data[ $i ]['depth_in_tree'];

		$data[ $i ]['ul_in'] = '';

		$data[ $i ]['ul_out'] = '';

		if ( $depth_diff > 0 ) {
			if ( $i > 0 ) {
				$data[ $i - 1 ]['first_child_id'] = $data[ $i ]['id'];
			}
			for ( $j = 0; $j < $depth_diff; $j ++ ) {
				$data[ $i ]['ul_in'] .= '<ul><li>';
			}
		} else if ( $depth_diff < 0 ) {
			for ( $j = 0; $j < - $depth_diff; $j ++ ) {
				$data[ $i - 1 ]['ul_out'] .= '</li></ul>';
			}
			$data[ $i - 1 ]['ul_out'] .= '</li>';
			$data[ $i ]['ul_in']      = '<li>';
		} else {
			$data[ $i - 1 ]['ul_out'] .= '</li>';
			$data[ $i ]['ul_in']      = '<li>';
		}
		if ( $i > 0 ) {
			$data[ $i - 1 ]['next_id'] = $data[ $i ]['id'];
			$data[ $i ]['prev_id']     = $data[ $i - 1 ]['id'];
		}
	}

	$data[ count( $data ) - 1 ]['ul_out'] = str_repeat( '</li></ul>', $previous_depth + 1 );

	return $data;
}


// check done
function d3forum_get_forum_permissions_of_current_user( $mydirname ) {
	global $xoopsUser;

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	if ( is_object( $xoopsUser ) ) {
		$uid    = (int) $xoopsUser->getVar( 'uid' );
		$groups = $xoopsUser->getGroups();
		if ( ! empty( $groups ) ) {
			$whr = "`uid`=$uid || `groupid` IN (" . implode( ',', $groups ) . ')';
		} else {
			$whr = "`uid`=$uid";
		}
	} else {
		$whr = '`groupid`=' . (int) XOOPS_GROUP_ANONYMOUS;
	}

	$sql = 'SELECT forum_id,SUM(can_post) AS can_post,SUM(can_edit) AS can_edit,SUM(can_delete) AS can_delete,SUM(post_auto_approved) AS post_auto_approved,SUM(is_moderator) AS is_moderator FROM ' . $db->prefix( $mydirname . '_forum_access' ) . " WHERE ($whr) GROUP BY forum_id";

	$result = $db->query( $sql );

	if ( $result ) {
		while ( $row = $db->fetchArray( $result ) ) {
			$ret[ $row['forum_id'] ] = $row;
		}
	}

	if ( empty( $ret ) ) {
		return [ 0 => [] ];
	} else {
		return $ret;
	}
}


// check done
function d3forum_get_category_permissions_of_current_user( $mydirname ) {
	global $xoopsUser;

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	if ( is_object( $xoopsUser ) ) {

		$uid = (int) $xoopsUser->getVar( 'uid' );

		$groups = $xoopsUser->getGroups();

		if ( ! empty( $groups ) ) {
			$whr = "`uid`=$uid || `groupid` IN (" . implode( ',', $groups ) . ')';
		} else {
			$whr = "`uid`=$uid";
		}
	} else {
		$whr = '`groupid`=' . (int) XOOPS_GROUP_ANONYMOUS;
	}

	$sql = 'SELECT cat_id,SUM(can_makeforum) AS can_makeforum,SUM(is_moderator) AS is_moderator FROM ' . $db->prefix( $mydirname . '_category_access' ) . " WHERE ($whr) GROUP BY cat_id";

	$result = $db->query( $sql );

	if ( $result ) {
		while ( $row = $db->fetchArray( $result ) ) {
			$ret[ $row['cat_id'] ] = $row;
		}
	}

	if ( empty( $ret ) ) {
		return [ 0 => [] ];
	}

	return $ret;
}


// check done
function d3forum_get_users_can_read_forum( $mydirname, $forum_id, $cat_id = null ) {
	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$forum_id = (int) $forum_id;

	$forum_uids = [];

	$cat_uids = [];

	if ( null === $cat_id ) {
		// get $cat_id from $forum_id
		[ $cat_id ] = $db->fetchRow( $db->query( 'SELECT `cat_id` FROM ' . $db->prefix( $mydirname . '_forums' ) . " WHERE `forum_id`=$forum_id" ) );
	}

	$sql = 'SELECT `uid` FROM ' . $db->prefix( $mydirname . '_category_access' ) . " WHERE `cat_id`=$cat_id AND `uid` IS NOT NULL";

	$result = $db->query( $sql );

	while ( list( $uid ) = $db->fetchRow( $result ) ) {
		$cat_uids[] = $uid;
	}

	$sql = 'SELECT distinct g.uid FROM ' . $db->prefix( $mydirname . '_category_access' ) . ' x , ' . $db->prefix( 'groups_users_link' ) . " g WHERE x.groupid=g.groupid AND x.`cat_id`=$cat_id AND x.`groupid` IS NOT NULL";

	$result = $db->query( $sql );

	while ( list( $uid ) = $db->fetchRow( $result ) ) {
		$cat_uids[] = $uid;
	}

	$cat_uids = array_unique( $cat_uids );

	$sql = 'SELECT `uid` FROM ' . $db->prefix( $mydirname . '_forum_access' ) . " WHERE `forum_id`=$forum_id AND `uid` IS NOT NULL";

	$result = $db->query( $sql );

	while ( list( $uid ) = $db->fetchRow( $result ) ) {
		$forum_uids[] = $uid;
	}

	$sql = 'SELECT distinct g.uid FROM ' . $db->prefix( $mydirname . '_forum_access' ) . ' x , ' . $db->prefix( 'groups_users_link' ) . " g WHERE x.groupid=g.groupid AND x.`forum_id`=$forum_id AND x.`groupid` IS NOT NULL";

	$result = $db->query( $sql );

	while ( list( $uid ) = $db->fetchRow( $result ) ) {
		$forum_uids[] = $uid;
	}

	$forum_uids = array_unique( $forum_uids );

	return array_intersect( $forum_uids, $cat_uids );
}


// check done
function d3forum_get_forum_moderate_groups4show( $mydirname, $forum_id ) {
	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$forum_id = (int) $forum_id;

	$ret = [];

	$sql = 'SELECT g.groupid, g.name FROM ' . $db->prefix( $mydirname . '_forum_access' ) . ' fa LEFT JOIN ' . $db->prefix( 'groups' ) . ' g ON fa.groupid=g.groupid WHERE fa.groupid IS NOT NULL AND fa.is_moderator AND forum_id=' . $forum_id;

	$mrs = $db->query( $sql );

	while ( list( $mod_gid, $mod_gname ) = $db->fetchRow( $mrs ) ) {
		$ret[] = [
			'gid'   => $mod_gid,
			'gname' => htmlspecialchars( $mod_gname, ENT_QUOTES ),
		];
	}

	return $ret;
}


// check done
function d3forum_get_forum_moderate_users4show( $mydirname, $forum_id ) {
	global $xoopsUser, $xoopsModuleConfig;    // naao edited

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$forum_id = (int) $forum_id;

	$ret = [];

	$sql = 'SELECT u.uid, u.uname, u.name FROM ' . $db->prefix( $mydirname . '_forum_access' ) . ' fa LEFT JOIN ' . $db->prefix( 'users' ) . ' u ON fa.uid=u.uid WHERE fa.uid IS NOT NULL AND fa.is_moderator AND forum_id=' . $forum_id;

	$mrs = $db->query( $sql );

	// naao from
	while ( list( $mod_uid, $mod_uname, $mod_name ) = $db->fetchRow( $mrs ) ) {
		if ( 1 === $xoopsModuleConfig['use_name'] && $mod_name ) {
			$mod_uname = $mod_name;
		}
		// naao to
		$ret[] = [
			'uid'   => $mod_uid,
			'uname' => htmlspecialchars( $mod_uname, ENT_QUOTES ),
		];
	}

	return $ret;
}


// check done
function d3forum_get_category_moderate_groups4show( $mydirname, $cat_id ) {
	// $db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$cat_id = (int) $cat_id;

	$ret = [];

	$sql = 'SELECT g.groupid, g.name FROM ' . $db->prefix( $mydirname . '_category_access' ) . ' ca LEFT JOIN ' . $db->prefix( 'groups' ) . ' g ON ca.groupid=g.groupid WHERE ca.groupid IS NOT NULL AND ca.is_moderator AND cat_id=' . $cat_id;

	$mrs = $db->query( $sql );

	while ( list( $mod_gid, $mod_gname ) = $db->fetchRow( $mrs ) ) {
		$ret[] = [
			'gid'   => $mod_gid,
			'gname' => htmlspecialchars( $mod_gname, ENT_QUOTES ),
		];
	}

	return $ret;
}


// check done
function d3forum_get_category_moderate_users4show( $mydirname, $cat_id ) {
	global $xoopsUser, $xoopsModuleConfig;    // naao edited

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$cat_id = (int) $cat_id;

	$ret = [];

	$sql = 'SELECT u.uid, u.uname, u.name FROM ' . $db->prefix( $mydirname . '_category_access' ) . ' ca LEFT JOIN ' . $db->prefix( 'users' ) . ' u ON ca.uid=u.uid WHERE ca.uid IS NOT NULL AND ca.is_moderator AND cat_id=' . $cat_id;

	$mrs = $db->query( $sql );

	// naao from
	while ( list( $mod_uid, $mod_uname, $mod_name ) = $db->fetchRow( $mrs ) ) {

		if ( 1 === $xoopsModuleConfig['use_name'] && $mod_name ) {
			$mod_uname = $mod_name;
		}
		// naao to
		$ret[] = [
			'uid'   => $mod_uid,
			'uname' => htmlspecialchars( $mod_uname, ENT_QUOTES ),
		];
	}

	return $ret;
}


// select box for jumping into a specified forum
function d3forum_make_jumpbox_options( $mydirname, $whr4cat, $whr4forum, $forum_selected = 0 ) {
	global $myts;

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$ret = '';

	$sql = 'SELECT c.cat_id, c.cat_title, c.cat_depth_in_tree, f.forum_id, f.forum_title FROM ' . $db->prefix( $mydirname . '_categories' ) . ' c LEFT JOIN ' . $db->prefix( $mydirname . '_forums' ) . " f ON f.cat_id=c.cat_id WHERE ($whr4cat) AND ($whr4forum) ORDER BY c.cat_order_in_tree, f.forum_weight";

	if ( $result = $db->query( $sql ) ) {
		while ( list( $cat_id, $cat_title, $cat_depth, $forum_id, $forum_title ) = $db->fetchRow( $result ) ) {
			$selected = $forum_id == $forum_selected ? 'selected="selected"' : '';
			$ret      .= "<option value='$forum_id' $selected>" . str_repeat( '--', $cat_depth ) . $myts->makeTboxData4Show( $cat_title ) . ' - ' . $myts->makeTboxData4Show( $forum_title ) . "</option>\n";
		}
	} else {
		$ret = "<option value=\"-1\">ERROR</option>\n";
	}

	return $ret;
}


// select box for jumping into a specified category
function d3forum_make_cat_jumpbox_options( $mydirname, $whr4cat, $cat_selected = 0 ) {
	global $myts;

	//$db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	$ret = '';

	$sql = 'SELECT c.cat_id, c.cat_title, c.cat_depth_in_tree FROM ' . $db->prefix( $mydirname . '_categories' ) . " c WHERE ($whr4cat) ORDER BY c.cat_order_in_tree";

	if ( $result = $db->query( $sql ) ) {
		while ( list( $cat_id, $cat_title, $cat_depth ) = $db->fetchRow( $result ) ) {
			$selected = $cat_id == $cat_selected ? 'selected="selected"' : '';
			$ret      .= "<option value='$cat_id' $selected>" . str_repeat( '--', $cat_depth ) . $myts->makeTboxData4Show( $cat_title ) . "</option>\n";
		}
	} else {
		$ret = "<option value=\"-1\">ERROR</option>\n";
	}

	return $ret;
}


function d3forum_trigger_event( $mydirname, $category, $item_id, $event, $extra_tags = [], $user_list = [], $omit_user_id = null ) {
	require_once XOOPS_TRUST_PATH . '/libs/altsys/class/D3NotificationHandler.class.php';

	$not_handler = D3NotificationHandler::getInstance();

	$not_handler->triggerEvent( $mydirname, 'd3forum', $category, $item_id, $event, $extra_tags, $user_list, $omit_user_id );
}


// started from {XOOPS_URL} for conventional modules
function d3forum_get_comment_link( $external_link_format, $external_link_id ) {
	if ( '{XOOPS_URL}' !== substr( $external_link_format, 0, 11 ) ) {
		return '';
	}

	$format = str_replace( '{XOOPS_URL}', XOOPS_URL, $external_link_format );

	return sprintf( $format, urlencode( $external_link_id ) );
}


// started from class:: for native d3comment modules
function d3forum_get_comment_description( $mydirname, $external_link_format, $external_link_id, $forum_id = null ) {
	$d3com = d3forum_main_get_comment_object( $mydirname, $external_link_format, $forum_id );
	if ( ! is_object( $d3com ) ) {
		return '';
	}

	$description = $d3com->fetchDescription( $external_link_id );

	if ( $description ) {
		return $description;
	}

	return $d3com->fetchSummary( $external_link_id );
}

// get object for comment integration  // naao modified
function d3forum_main_get_comment_object( $forum_dirname, $external_link_format, $forum_id = null ) {
	require_once dirname( __DIR__ ) . '/class/D3commentObj.class.php';

	$params['forum_dirname'] = $forum_dirname;

	[
		$params['external_dirname'],
		$params['classname'],
		$params['external_trustdirname']
	] = explode( '::', $external_link_format );

	$obj = D3commentObj::getInstance( $params );

	if ( ! empty( $forum_id ) ) {
		$obj->d3comObj->setForumId( $forum_id );
	}

	return $obj->d3comObj;
}

// get samples of category options
function d3forum_main_get_categoryoptions4edit( $d3forum_configs_can_be_override ) {
	global $xoopsModuleConfig;

	$lines = [];

	foreach ( $d3forum_configs_can_be_override as $key => $type ) {
		if ( isset( $xoopsModuleConfig[ $key ] ) ) {
			$val = $xoopsModuleConfig[ $key ];
			if ( 'int' == $type || 'bool' == $type ) {
				$val = (int) $val;
			}
			$lines[] = htmlspecialchars( $key . ':' . $val, ENT_QUOTES );
		}
	}

	return implode( '<br>', $lines );
}


// hook topic_id/external_link_id into $_POST['mode'] = 'reply' , $_POST['post_id']
function d3forum_main_posthook_sametopic( $mydirname ) {
	// $db =& Database::getInstance();
	$db = &XoopsDatabaseFactory::getDatabaseConnection();

	if ( ! empty( $_POST['external_link_id'] ) ) {

		// search the first post of the latest topic with the external_link_id
		$external_link_id4sql = addslashes( @$_POST['external_link_id'] );

		$forum_id = (int) @$_POST['forum_id'];

		$result = $db->query( 'SELECT topic_first_post_id,topic_locked FROM ' . $db->prefix( $mydirname . '_topics' ) . " WHERE topic_external_link_id='$external_link_id4sql' AND forum_id=$forum_id AND ! topic_invisible ORDER BY topic_last_post_time DESC LIMIT 1" );

	} else if ( ! empty( $_POST['topic_id'] ) ) {
		// search the first post of the topic with the topic_id
		$topic_id = (int) @$_POST['topic_id'];

		$result = $db->query( 'SELECT topic_first_post_id,topic_locked FROM ' . $db->prefix( $mydirname . '_topics' ) . " WHERE topic_id=$topic_id AND ! topic_invisible" );
	}

	if ( empty( $result ) ) {
		return;
	}

	[ $pid, $topic_locked ] = $db->fetchRow( $result );
	if ( $pid > 0 && ! $topic_locked ) {
		// hook to reply
		$_POST['mode'] = 'reply';
		$_POST['pid']  = $pid;
	}
}
