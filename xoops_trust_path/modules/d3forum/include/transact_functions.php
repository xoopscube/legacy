<?php

// this file can be included from transaction procedures

// call back for comment integration
function d3forum_main_d3comment_callback( $mydirname , $topic_id , $mode = 'update' , $post_id = 0 )
{
	$db =& Database::getInstance() ;

	$topic_id = intval( $topic_id ) ;

	list( $external_link_format , $external_link_id , $forum_id ) = $db->fetchRow( $db->query( "SELECT f.forum_external_link_format,t.topic_external_link_id,t.forum_id FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_forums")." f ON f.forum_id=t.forum_id WHERE topic_id=$topic_id" ) ) ;

	if( ! empty( $external_link_format ) && ! empty( $external_link_id ) ) {
		$d3com =& d3forum_main_get_comment_object( $mydirname , $external_link_format ) ;
		if( is_object( @$d3com ) ) {
			$d3com->onUpdate( $mode , $external_link_id , $forum_id , $topic_id , $post_id ) ;
		}
	}
}


// delete posts recursively
function d3forum_delete_post_recursive( $mydirname , $post_id )
{
	$db =& Database::getInstance() ;

	$post_id = intval( $post_id ) ;

	list( $topic_id ) = $db->fetchRow( $db->query( "SELECT topic_id FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ) ) ;

	$sql = "SELECT post_id FROM ".$db->prefix($mydirname."_posts")." WHERE pid=$post_id" ;
	if( ! $result = $db->query( $sql ) ) die( "DB ERROR in delete posts" ) ;
	while( list( $child_post_id ) = $db->fetchRow( $result ) ) {
		d3forum_delete_post_recursive( $mydirname , $child_post_id ) ;
	}

	/* list( $uid ) = $db->fetchRow( $db->query( "SELECT uid FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ) ) ;
	if( ! empty( $uid ) ) {
		// decrement user's posts
		$db->query( "UPDATE ".$db->prefix("users")." SET posts=posts-1 WHERE uid=$uid" ) ;
	} */

	d3forum_transact_make_post_history( $mydirname , $post_id , true ) ;
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ) ;
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_post_votes")." WHERE post_id=$post_id" ) ;
	
	// call back to the target of comment
	d3forum_main_d3comment_callback( $mydirname , $topic_id , 'delete' , $post_id ) ;
}


// delete a topic 
function d3forum_delete_topic( $mydirname , $topic_id , $delete_also_posts = true )
{
	global $xoopsModule ;

	$db =& Database::getInstance() ;

	$topic_id = intval( $topic_id ) ;

	// delete posts
	if( $delete_also_posts ) {
		$sql = "SELECT post_id FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id" ;
		if( ! $result = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		while( list( $post_id ) = $db->fetchRow( $result ) ) {
			d3forum_delete_post_recursive( $mydirname , $post_id ) ;
		}
	}

	// delete notifications about this topic
	$notification_handler =& xoops_gethandler( 'notification' ) ;
	$notification_handler->unsubscribeByItem( $xoopsModule->getVar( 'mid' ) , 'topic' , $topic_id ) ;

	// delete topic
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	// delete u2t
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_users2topics")." WHERE topic_id=$topic_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
}


// delete a forum
function d3forum_delete_forum( $mydirname , $forum_id , $delete_also_topics = true )
{
	global $xoopsModule ;

	$db =& Database::getInstance() ;

	$forum_id = intval( $forum_id ) ;

	// delete topics
	if( $delete_also_topics ) {
		$sql = "SELECT topic_id FROM ".$db->prefix($mydirname."_topics")." WHERE forum_id=$forum_id" ;
		if( ! $result = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		while( list( $topic_id ) = $db->fetchRow( $result ) ) {
			d3forum_delete_topic( $mydirname , $topic_id ) ;
		}
	}

	// delete notifications about this forum
	$notification_handler =& xoops_gethandler( 'notification' ) ;
	$notification_handler->unsubscribeByItem( $xoopsModule->getVar( 'mid' ) , 'forum' , $forum_id ) ;

	// delete forum
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_forums")." WHERE forum_id=$forum_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	// delete forum_access
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_forum_access")." WHERE forum_id=$forum_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
}


// delete a category
function d3forum_delete_category( $mydirname , $cat_id , $delete_also_forums = true )
{
	global $xoopsModule ;

	$db =& Database::getInstance() ;

	$cat_id = intval( $cat_id ) ;

	// delete forums
	if( $delete_also_forums ) {
		$sql = "SELECT forum_id FROM ".$db->prefix($mydirname."_forums")." WHERE cat_id=$cat_id" ;
		if( ! $result = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		while( list( $forum_id ) = $db->fetchRow( $result ) ) {
			d3forum_delete_forum( $mydirname , $forum_id ) ;
		}
	}

	// delete notifications about this category
	$notification_handler =& xoops_gethandler( 'notification' ) ;
	$notification_handler->unsubscribeByItem( $xoopsModule->getVar( 'mid' ) , 'category' , $cat_id ) ;

	// delete category
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$cat_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	// delete category_access
	if( ! $db->query( "DELETE FROM ".$db->prefix($mydirname."_category_access")." WHERE cat_id=$cat_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
}


// store redundant informations to a category from its forums
function d3forum_sync_category( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;

	$cat_id = intval( $cat_id ) ;

	// get children
	include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
	$mytree = new XoopsTree( $db->prefix($mydirname."_categories") , "cat_id" , "pid" ) ;
	$children = $mytree->getAllChildId( $cat_id ) ;
	$children[] = $cat_id ;
	$children = array_map( 'intval' , $children ) ;

	// topics/posts information belonging this category directly
	$sql = "SELECT MAX(forum_last_post_id),MAX(forum_last_post_time),SUM(forum_topics_count),SUM(forum_posts_count) FROM ".$db->prefix($mydirname."_forums")." WHERE cat_id=$cat_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT forum in sync category" ) ;
	list( $last_post_id , $last_post_time , $topics_count , $posts_count ) = $db->fetchRow( $result ) ;

	// topics/posts information belonging this category and/or subcategories
	$sql = "SELECT MAX(forum_last_post_id),MAX(forum_last_post_time),SUM(forum_topics_count),SUM(forum_posts_count) FROM ".$db->prefix($mydirname."_forums")." WHERE cat_id IN (".implode(",",$children).")" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT forum in sync category" ) ;
	list( $last_post_id_in_tree , $last_post_time_in_tree , $topics_count_in_tree , $posts_count_in_tree ) = $db->fetchRow( $result ) ;

	// update query
	if( ! $result = $db->queryF( "UPDATE ".$db->prefix($mydirname."_categories")." SET cat_topics_count=".intval($topics_count).",cat_posts_count=".intval($posts_count).", cat_last_post_id=".intval($last_post_id).", cat_last_post_time=".intval($last_post_time).",cat_topics_count_in_tree=".intval($topics_count_in_tree).",cat_posts_count_in_tree=".intval($posts_count_in_tree).", cat_last_post_id_in_tree=".intval($last_post_id_in_tree).", cat_last_post_time_in_tree=".intval($last_post_time_in_tree)." WHERE cat_id=$cat_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	// do sync parents
	list( $pid ) = $db->fetchRow( $db->query( "SELECT pid FROM ".$db->prefix($mydirname."_categories")." WHERE cat_id=$cat_id" ) ) ;
	if( $pid != $cat_id && $pid > 0 ) {
		d3forum_sync_category( $mydirname , $pid ) ;
	}

	return true ;
}


function d3forum_sync_cattree( $mydirname )
{
	$db =& Database::getInstance() ;

	// rebuild tree informations
	$tree_array = d3forum_makecattree_recursive( $db->prefix($mydirname."_categories") , 0 ) ;
	array_shift( $tree_array ) ;
	$paths = array() ;
	$previous_depth = 0 ;
	if( ! empty( $tree_array ) ) foreach( $tree_array as $key => $val ) {
		$depth_diff = $val['depth'] - $previous_depth ;
		$previous_depth = $val['depth'] ;
		if( $depth_diff > 0 ) {
			for( $i = 0 ; $i < $depth_diff ; $i ++ ) {
				$paths[ $val['cat_id'] ] = $val['cat_title'] ;
			}
		} else {
			for( $i = 0 ; $i < - $depth_diff + 1 ; $i ++ ) {
				array_pop( $paths ) ;
			}
			$paths[ $val['cat_id'] ] = $val['cat_title'] ;
		}

		$db->queryF( "UPDATE ".$db->prefix($mydirname."_categories")." SET cat_depth_in_tree=".($val['depth']-1).", cat_order_in_tree=".($key).", cat_path_in_tree='".addslashes(serialize($paths))."' WHERE cat_id=".$val['cat_id'] ) ;
	}
}


function d3forum_makecattree_recursive( $tablename , $cat_id , $order = 'cat_weight' , $parray = array() , $depth = 0 , $cat_title = '' )
{
	$db =& Database::getInstance() ;

	$parray[] = array( 'cat_id' => $cat_id , 'depth' => $depth , 'cat_title' => $cat_title ) ;

	$sql = "SELECT cat_id,cat_title FROM $tablename WHERE pid=$cat_id ORDER BY $order" ;
	$result = $db->query( $sql ) ;
	if( $db->getRowsNum( $result ) == 0 ) {
		return $parray ;
	}
	while( list( $new_cat_id , $new_cat_title ) = $db->fetchRow( $result ) ) {
		$parray = d3forum_makecattree_recursive( $tablename , $new_cat_id , $order , $parray , $depth + 1 , $new_cat_title ) ;
	}
	return $parray ;
}


// store redundant informations to a forum from its topics
function d3forum_sync_forum( $mydirname , $forum_id , $sync_also_category = true )
{
	$db =& Database::getInstance() ;

	$forum_id = intval( $forum_id ) ;

	$sql = "SELECT cat_id FROM ".$db->prefix($mydirname."_forums")." WHERE forum_id=$forum_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT forum in sync forum" ) ;
	list( $cat_id ) = $db->fetchRow( $result ) ;

	$sql = "SELECT MAX(topic_last_post_id),MAX(topic_last_post_time),COUNT(topic_id),SUM(topic_posts_count) FROM ".$db->prefix($mydirname."_topics")." WHERE forum_id=$forum_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT topics in sync forum" ) ;
	list( $last_post_id , $last_post_time , $topics_count , $posts_count ) = $db->fetchRow( $result ) ;

	if( ! $result = $db->queryF( "UPDATE ".$db->prefix($mydirname."_forums")." SET forum_topics_count=".intval($topics_count).",forum_posts_count=".intval($posts_count).", forum_last_post_id=".intval($last_post_id).", forum_last_post_time=".intval($last_post_time)." WHERE forum_id=$forum_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	if( $sync_also_category ) return d3forum_sync_category( $mydirname , $cat_id ) ;
	else return true ;
}


// store redundant informations to a topic from its posts
// and rebuild tree informations (depth, order_in_tree)
function d3forum_sync_topic( $mydirname , $topic_id , $sync_also_forum = true , $sync_topic_title = false )
{
	$db =& Database::getInstance() ;

	$topic_id = intval( $topic_id ) ;

	$sql = "SELECT forum_id FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT topic in sync topic" ) ;
	list( $forum_id ) = $db->fetchRow( $result ) ;

	// get first_post_id
	$sql = "SELECT post_id FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id AND pid=0" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT first_post in sync topic" ) ;
	list( $first_post_id ) = $db->fetchRow( $result ) ;

	// get last_post_id and total_posts
	$sql = "SELECT MAX(post_id),COUNT(post_id) FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT last_post in sync topic" ) ;
	list( $last_post_id , $total_posts ) = $db->fetchRow( $result ) ;

	if( empty( $total_posts ) ) {
		// this is empty topic should be removed
		d3forum_delete_topic( $mydirname , $topic_id ) ;

	} else {

		// update redundant columns in topics table
		list( $first_post_time , $first_uid , $first_subject , $unique_path ) = $db->fetchRow( $db->query( "SELECT post_time,uid,subject,unique_path FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$first_post_id" ) ) ;
		list( $last_post_time , $last_uid ) = $db->fetchRow( $db->query( "SELECT post_time,uid FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$last_post_id" ) ) ;

		// sync topic_title same as first post's subject if specified
		$topictitle4set = $sync_topic_title ? "topic_title='".addslashes($first_subject)."'," : "" ;

		if( ! $db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET {$topictitle4set} topic_posts_count=$total_posts, topic_first_uid=$first_uid, topic_first_post_id=$first_post_id, topic_first_post_time=$first_post_time, topic_last_uid=$last_uid, topic_last_post_id=$last_post_id, topic_last_post_time=$last_post_time WHERE topic_id=$topic_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

		// rebuild tree informations
		$tree_array = d3forum_maketree_recursive( $db->prefix($mydirname."_posts") , intval( $first_post_id ) , 'post_id' , array() , 0 , empty( $unique_path ) ? '.1' : $unique_path ) ;
		if( ! empty( $tree_array ) ) foreach( $tree_array as $key => $val ) {
			$db->queryF( "UPDATE ".$db->prefix($mydirname."_posts")." SET depth_in_tree=".$val['depth'].", order_in_tree=".($key+1).", unique_path='".addslashes($val['unique_path'])."' WHERE post_id=".$val['post_id'] ) ;
		}
	}

	if( $sync_also_forum ) return d3forum_sync_forum( $mydirname , $forum_id ) ;
	else return true ;
}


// store redundant informations to a topic from its posts
function d3forum_sync_topic_votes( $mydirname , $topic_id )
{
	$db =& Database::getInstance() ;

	$topic_id = intval( $topic_id ) ;

	/* $sql = "SELECT forum_id FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT topic in sync topic_votes" ) ;
	list( $forum_id ) = $db->fetchRow( $result ) ;*/

	$sql = "SELECT SUM(votes_count),SUM(votes_sum) FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT topic_votes in sync topic_votes" ) ;
	list( $votes_count , $votes_sum ) = $db->fetchRow( $result ) ;

	if( ! $db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_votes_count=".intval($votes_count).",topic_votes_sum=".intval($votes_sum)." WHERE topic_id=$topic_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	//if( $sync_also_topic_votes ) return d3forum_sync_forum_votes( $mydirname , $forum_id ) ;
	return true ;
}


// store redundant informations to a post from its post_votes
function d3forum_sync_post_votes( $mydirname , $post_id , $sync_also_topic_votes = true )
{
	$db =& Database::getInstance() ;

	$post_id = intval( $post_id ) ;

	$sql = "SELECT topic_id FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT post in sync post_votes" ) ;
	list( $topic_id ) = $db->fetchRow( $result ) ;

	$sql = "SELECT COUNT(*),SUM(vote_point) FROM ".$db->prefix($mydirname."_post_votes")." WHERE post_id=$post_id" ;
	if( ! $result = $db->query( $sql ) ) die( "ERROR SELECT post_votes in sync post_votes" ) ;
	list( $votes_count , $votes_sum ) = $db->fetchRow( $result ) ;

	if( ! $db->queryF( "UPDATE ".$db->prefix($mydirname."_posts")." SET votes_count=".intval($votes_count).",votes_sum=".intval($votes_sum)." WHERE post_id=$post_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	if( $sync_also_topic_votes ) return d3forum_sync_topic_votes( $mydirname , $topic_id ) ;
	else return true ;
}


function d3forum_maketree_recursive( $tablename , $post_id , $order = 'post_id' , $parray = array() , $depth = 0 , $unique_path = '.1' )
{
	$db =& Database::getInstance() ;

	$parray[] = array( 'post_id' => $post_id , 'depth' => $depth , 'unique_path' => $unique_path ) ;

	$sql = "SELECT post_id,unique_path FROM $tablename WHERE pid=$post_id ORDER BY $order" ;
	$result = $db->query( $sql ) ;
	if( $db->getRowsNum( $result ) == 0 ) {
		return $parray ;
	}
	$new_post_ids = array() ;
	$max_count_of_last_level = 0 ;
	while( list( $new_post_id , $new_unique_path ) = $db->fetchRow( $result ) ) {
		$new_post_ids[ intval( $new_post_id ) ] = $new_unique_path ;
		if( ! empty( $new_unique_path ) ) {
			$count_of_last_level = intval( substr( strrchr( $new_unique_path , '.' ) , 1 ) ) ;
			if( $max_count_of_last_level < $count_of_last_level ) {
				$max_count_of_last_level = $count_of_last_level ;
			}
		}
	}
	foreach( $new_post_ids as $new_post_id => $new_unique_path ) {
		if( empty( $new_unique_path ) ) {
			$new_unique_path = $unique_path . '.' . ++ $max_count_of_last_level ;
		}
		$parray = d3forum_maketree_recursive( $tablename , $new_post_id , $order , $parray , $depth + 1 , $new_unique_path ) ;
	}
	return $parray ;
}


function d3forum_cutpasteposts( $mydirname , $post_id , $pid , $new_forum_id , $forum_permissions , $isadmin )
{
	$db =& Database::getInstance() ;

	$post_id = intval( $post_id ) ;
	$pid = intval( $pid ) ;
	$new_forum_id = intval( $new_forum_id ) ;

	// get children
	include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
	$mytree = new XoopsTree( $db->prefix($mydirname."_posts") , "post_id" , "pid" ) ;
	$children = $mytree->getAllChildId( $post_id ) ;
	$children[] = $post_id ;

	if( $pid == 0 ) {
		// check validation to $new_forum_id
		list( $new_forum_id , $new_forum_external_link_format ) = $db->fetchRow( $db->query( "SELECT forum_id,forum_external_link_format FROM ".$db->prefix($mydirname."_forums")." WHERE forum_id=$new_forum_id" ) ) ;
		if( empty( $new_forum_id ) ) die( _MD_D3FORUM_ERR_READFORUM ) ;

		// check the user is distinated forum's admin or mod
		if( ! $isadmin && ! $forum_permissions[ $new_forum_id ]['is_moderator'] ) die( _MD_D3FORUM_ERR_CUTPASTENOTADMINOFDESTINATION ) ;

		// check the post is the top or not
		list( $pid , $topic_id , $subject ) = $db->fetchRow( $db->query( "SELECT pid,topic_id,subject FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ) ) ;

		if( $pid ) {
			// get external_link_id of the current topic
			list( $external_link_id ) = $db->fetchRow( $db->query( "SELECT topic_external_link_id FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id" ) ) ;
			// create a new topic and copy subject to topic_title in sync
			if( ! $db->query( "INSERT INTO ".$db->prefix($mydirname."_topics")." SET forum_id=$new_forum_id, topic_title='".addslashes($subject)."', topic_external_link_id='".addslashes($external_link_id)."'" ) ) die( "DB ERROR in INSERT topic" ) ;
			$new_topic_id = $db->getInsertId() ;
			$new_topic_created = true ;
			if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_posts")." SET pid=0 WHERE post_id=$post_id" ) ) die( "DB ERROR in UPDATE post" ) ;
		} else if( $topic_id ) {
			// change forum_id of the topic
			$new_topic_id = $topic_id ;
			if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_topics")." SET forum_id=$new_forum_id WHERE topic_id=$topic_id" ) ) die( "DB ERROR in UPDATE topic" ) ;
		} else {
			die( "DB ERROR in SELECT topic" ) ;
		}

		// clear topic_external_link_id if the new forum has no external_link_fmt
		if( $new_forum_external_link_format == '' ) {
			if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_external_link_id='' WHERE topic_id=$new_topic_id" ) ) die( "DB ERROR in UPDATE topic".__LINE__ ) ;
		}
	} else {
		// get topic_id from post_id
		list( $pid , $new_topic_id , $new_forum_id ) = $db->fetchRow( $db->query( "SELECT p.post_id,t.topic_id,t.forum_id FROM ".$db->prefix($mydirname."_posts")." p LEFT JOIN ".$db->prefix($mydirname."_topics")." t ON p.topic_id=t.topic_id LEFT JOIN ".$db->prefix($mydirname."_forums")." f ON t.forum_id=f.forum_id WHERE p.post_id=$pid" ) ) ;
		if( empty( $pid ) ) die( _MD_D3FORUM_ERR_PIDNOTEXIST ) ;

		// check the user is distinated forum's admin or mod
		if( ! $isadmin && ! $forum_permissions[ $new_forum_id ]['is_moderator'] ) die( _MD_D3FORUM_ERR_CUTPASTENOTADMINOFDESTINATION ) ;

		// loop check
		if( in_array( $pid , $children ) ) die( _MD_D3FORUM_ERR_PIDLOOP ) ;
		if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_posts")." SET pid=$pid WHERE post_id=$post_id" ) ) die( "DB ERROR IN UPDATE post" ) ;
	}
	foreach( $children as $child_post_id ) {
		$child_post_id = intval( $child_post_id ) ;
		$sql = "UPDATE ".$db->prefix($mydirname."_posts")." SET topic_id=$new_topic_id WHERE post_id=$child_post_id" ;
		$db->query( $sql ) ;
	}

	return array( $new_topic_id , $new_forum_id ) ;
}


// done
function d3forum_update_topic_from_post( $mydirname , $topic_id , $forum_id , $forum_permissions , $isadmin ) 
{
	global $myts ;

	$db =& Database::getInstance() ;

	$sql4set = '' ;

	$topic_id = intval( $topic_id ) ;
	$new_forum_id = intval( @$_POST['forum_id'] ) ;

	// prefetch for forum
	list( $new_forum_external_link_format ) = $db->fetchRow( $db->query( "SELECT forum_external_link_format FROM ".$db->prefix($mydirname."_forums")." WHERE forum_id=$new_forum_id" ) ) ;

	// check the user is destined forum's admin or mod
	if( ! $isadmin && ! $forum_permissions[ $new_forum_id ]['is_moderator'] ) die( _MD_D3FORUM_ERR_CUTPASTENOTADMINOFDESTINATION ) ;

	$topic_title4sql = addslashes( $myts->stripSlashesGPC( @$_POST['topic_title'] ) ) ;
	$topic_sticky = intval( @$_POST['topic_sticky'] ) ;
	$topic_locked = intval( @$_POST['topic_locked'] ) ;
	$topic_invisible = intval( @$_POST['topic_invisible'] ) ;
	$topic_solved = intval( @$_POST['topic_solved'] ) ;
	$external_link_id = $myts->stripSlashesGPC( @$_POST['topic_external_link_id'] ) ;

	// do update
	if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_topics")." SET $sql4set topic_title='$topic_title4sql', forum_id='$new_forum_id', topic_sticky='$topic_sticky', topic_locked='$topic_locked', topic_invisible='$topic_invisible', topic_solved='$topic_solved', topic_external_link_id='".addslashes($external_link_id)."' WHERE topic_id=$topic_id" ) ) die( "DB ERROR IN UPDATE topic".__LINE__ ) ;

	// clear topic_external_link_id if the new forum has no external_link_fmt
	if( $new_forum_external_link_format == '' ) {
		if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_external_link_id='' WHERE topic_id=$topic_id" ) ) die( "DB ERROR in UPDATE topic".__LINE__ ) ;
	}

	// call back to the target of comment
	if( ! empty( $external_link_format ) && ! empty( $external_link_id ) ) {
		$d3com =& d3forum_main_get_comment_object( $mydirname , $external_link_format ) ;
		if( is_object( @$d3com ) ) {
			$d3com->onUpdate( 'update' , $external_link_id , $forum_id , $topic_id ) ;
		}
	}

	d3forum_sync_forum( $mydirname , $forum_id ) ;
	d3forum_sync_forum( $mydirname , $new_forum_id ) ;
}


// get requests for forum's sql (parse options)
function d3forum_get_requests4sql_forum( $mydirname )
{
	global $myts , $xoopsModuleConfig ;

	$db =& Database::getInstance() ;

	include dirname(dirname(__FILE__)).'/include/constant_can_override.inc.php' ;
	$options = array() ;
	foreach( $xoopsModuleConfig as $key => $val ) {
		if( empty( $d3forum_configs_can_be_override[ $key ] ) ) continue ;
		foreach( explode( "\n" , @$_POST['options'] ) as $line ) {
			if( preg_match( '/^'.$key.'\:(.{1,100})$/' , $line , $regs ) ) {
				switch( $d3forum_configs_can_be_override[ $key ] ) {
					case 'text' :
						$options[ $key ] = trim( $regs[1] ) ;
						break ;
					case 'int' :
						$options[ $key ] = intval( $regs[1] ) ;
						break ;
					case 'bool' :
						$options[ $key ] = intval( $regs[1] ) > 0 ? 1 : 0 ;
						break ;
				}
			}
		}
	}

	// check $cat_id
	$cat_id = empty( $_POST['cat_id'] ) ? intval( @$_GET['cat_id'] ) : intval( @$_POST['cat_id'] ) ;
	$sql = "SELECT * FROM ".$db->prefix($mydirname."_categories")." c WHERE c.cat_id=$cat_id" ;
	if( ! $crs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	if( $db->getRowsNum( $crs ) <= 0 ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

	return array( 
		'title' => addslashes( $myts->stripSlashesGPC( @$_POST['title'] ) ) ,
		'desc' => addslashes( $myts->stripSlashesGPC( @$_POST['desc'] ) ) ,
		'weight' => intval( @$_POST['weight'] ) ,
		'external_link_format' => addslashes( $myts->stripSlashesGPC( @$_POST['external_link_format'] ) ) ,
		'cat_id' => $cat_id ,
		'options' => addslashes( serialize( $options ) ) ,
	) ;
}


// create a forum
function d3forum_makeforum( $mydirname , $cat_id , $isadmin )
{
	$db =& Database::getInstance() ;

	$requests = d3forum_get_requests4sql_forum( $mydirname ) ;

	$set4admin = $isadmin ? ", forum_weight='{$requests['weight']}', forum_options='{$requests['options']}', forum_external_link_format='{$requests['external_link_format']}'" : '' ;
	if( ! $db->query( "INSERT INTO ".$db->prefix($mydirname."_forums")." SET forum_title='{$requests['title']}', forum_desc='{$requests['desc']}', cat_id=$cat_id $set4admin" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	$new_forum_id = $db->getInsertId() ;

	// permissions are set same as the parent category. (also moderator)
	$sql = "INSERT INTO ".$db->prefix($mydirname."_forum_access")." (forum_id,uid,groupid,can_post,can_edit,can_delete,post_auto_approved,is_moderator) SELECT $new_forum_id,uid,groupid,can_post,can_edit,can_delete,post_auto_approved,is_moderator FROM ".$db->prefix($mydirname."_category_access")." WHERE cat_id=$cat_id" ;
	if( ! $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	return array( $new_forum_id , stripslashes( $requests['title'] ) ) ;
}


// update a forum
function d3forum_updateforum( $mydirname , $forum_id , $isadmin )
{
	$db =& Database::getInstance() ;

	$requests = d3forum_get_requests4sql_forum( $mydirname ) ;

	$set4admin = $isadmin ? ", forum_weight='{$requests['weight']}', forum_options='{$requests['options']}', forum_external_link_format='{$requests['external_link_format']}', cat_id='{$requests['cat_id']}'" : '' ;
	if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_forums")." SET forum_title='{$requests['title']}', forum_desc='{$requests['desc']}' $set4admin WHERE forum_id=$forum_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	return $forum_id ;
}


// get requests for category's sql (parse options)
function d3forum_get_requests4sql_category( $mydirname )
{
	global $myts , $xoopsModuleConfig ;

	$db =& Database::getInstance() ;

	include dirname(dirname(__FILE__)).'/include/constant_can_override.inc.php' ;
	$options = array() ;
	foreach( $xoopsModuleConfig as $key => $val ) {
		if( empty( $d3forum_configs_can_be_override[ $key ] ) ) continue ;
		foreach( explode( "\n" , @$_POST['options'] ) as $line ) {
			if( preg_match( '/^'.$key.'\:(.{1,100})$/' , $line , $regs ) ) {
				switch( $d3forum_configs_can_be_override[ $key ] ) {
					case 'text' :
						$options[ $key ] = trim( $regs[1] ) ;
						break ;
					case 'int' :
						$options[ $key ] = intval( $regs[1] ) ;
						break ;
					case 'bool' :
						$options[ $key ] = intval( $regs[1] ) > 0 ? 1 : 0 ;
						break ;
				}
			}
		}
	}

	// check $pid
	$pid = intval( @$_POST['pid'] ) ;
	if( $pid ) {
		$sql = "SELECT * FROM ".$db->prefix($mydirname."_categories")." c WHERE c.cat_id=$pid" ;
		if( ! $crs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		if( $db->getRowsNum( $crs ) <= 0 ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;
	}

	return array( 
		'title' => addslashes( $myts->stripSlashesGPC( @$_POST['title'] ) ) ,
		'desc' => addslashes( $myts->stripSlashesGPC( @$_POST['desc'] ) ) ,
		'weight' => intval( @$_POST['weight'] ) ,
		'pid' => $pid ,
		'options' => addslashes( serialize( $options ) ) ,
	) ;
}


// create a category
function d3forum_makecategory( $mydirname )
{
	global $xoopsUser ;

	$db =& Database::getInstance() ;

	$requests = d3forum_get_requests4sql_category( $mydirname ) ;

	if( ! $db->query( "INSERT INTO ".$db->prefix($mydirname."_categories")." SET cat_title='{$requests['title']}', cat_desc='{$requests['desc']}', cat_weight='{$requests['weight']}', cat_options='{$requests['options']}', pid={$requests['pid']}" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	$new_cat_id = $db->getInsertId() ;

	if( $requests['pid'] ) {
		// permissions are set same as the parent category. (also moderator)
		$sql = "SELECT uid,groupid,can_post,can_edit,can_delete,post_auto_approved,can_makeforum,is_moderator FROM ".$db->prefix($mydirname."_category_access")." WHERE cat_id={$requests['pid']}" ;
		if( ! $result = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		while( $row = $db->fetchArray( $result ) ) {
			$uid4sql = empty( $row['uid'] ) ? 'null' : intval( $row['uid'] ) ;
			$groupid4sql = empty( $row['groupid'] ) ? 'null' : intval( $row['groupid'] ) ;
			$sql = "INSERT INTO ".$db->prefix($mydirname."_category_access")." (cat_id,uid,groupid,can_post,can_edit,can_delete,post_auto_approved,can_makeforum,is_moderator) VALUES ($new_cat_id,$uid4sql,$groupid4sql,{$row['can_post']},{$row['can_edit']},{$row['can_delete']},{$row['post_auto_approved']},{$row['can_makeforum']},{$row['is_moderator']})" ;
			if( ! $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		}
	} else {
		// default permissioning for top category
		$groups = $xoopsUser->getGroups() ;
		foreach( $groups as $groupid ) {
			$adminflag = $groupid == 1 ? 1 : 0 ;
			$sql = "INSERT INTO ".$db->prefix($mydirname."_category_access")." (cat_id,uid,groupid,can_post,can_edit,can_delete,post_auto_approved,can_makeforum,is_moderator) VALUES ($new_cat_id,null,$groupid,1,1,1,1,$adminflag,$adminflag)" ;
			if( ! $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
		}
	}

	// rebuild category tree
	d3forum_sync_cattree( $mydirname ) ;

	return $new_cat_id ;
}


// update a category
function d3forum_updatecategory( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;

	$requests = d3forum_get_requests4sql_category( $mydirname ) ;

	// get children
	include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
	$mytree = new XoopsTree( $db->prefix($mydirname."_categories") , "cat_id" , "pid" ) ;
	$children = $mytree->getAllChildId( $cat_id ) ;
	$children[] = $cat_id ;

	// loop check
	if( in_array( $requests['pid'] , $children ) ) die( _MD_D3FORUM_ERR_PIDLOOP ) ;

	if( ! $db->query( "UPDATE ".$db->prefix($mydirname."_categories")." SET cat_title='{$requests['title']}', cat_desc='{$requests['desc']}', cat_weight='{$requests['weight']}', cat_options='{$requests['options']}', pid='{$requests['pid']}' WHERE cat_id=$cat_id" ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

	// rebuild category tree
	d3forum_sync_cattree( $mydirname ) ;

	return $cat_id ;
}


// make a new history entry for a post
function d3forum_transact_make_post_history( $mydirname , $post_id , $full_backup = false )
{
	$db =& Database::getInstance() ;
	$post_id = intval( $post_id ) ;

	$result = $db->query( "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ) ;
	if( ! $result || $db->getRowsNum( $result ) == 0 ) return ;
	$post_row = $db->fetchArray( $result ) ;
	$data = array() ;
	$indexes = $full_backup ? array_keys( $post_row ) : array( 'subject' , 'post_text' ) ;
	foreach( $indexes as $index ) {
		$data[ $index ] = $post_row[ $index ] ;
	}

	// check the latest data in history
	$result = $db->query( "SELECT data FROM ".$db->prefix($mydirname."_post_histories")." WHERE post_id=$post_id ORDER BY history_time DESC" ) ;
	if( $db->getRowsNum( $result ) > 0 ) {
		list( $old_data_serialized ) = $db->fetchRow( $result ) ;
		$old_data = unserialize( $old_data_serialized ) ;
		if( $old_data == $data ) return ;
	}

	if( ! $db->queryF( "INSERT INTO ".$db->prefix($mydirname."_post_histories")." SET post_id=$post_id, history_time=UNIX_TIMESTAMP(), data='".mysql_real_escape_string( serialize( $data ) )."'" ) ) die( "DB ERROR ON making post_history".__LINE__ ) ;
}


// turning topic_solved of all topics in the category on (batch action)
function d3forum_transact_turnsolvedon_in_category( $mydirname , $cat_id )
{
	$db =& Database::getInstance() ;
	$cat_id = intval( $cat_id ) ;

	$sql = "SELECT forum_id FROM ".$db->prefix($mydirname."_forums")." WHERE cat_id=$cat_id" ;
	$result = $db->query( $sql ) ;
	while( list( $forum_id ) = $db->fetchRow( $result ) ) {
		d3forum_transact_turnsolvedon_in_forum( $mydirname , $forum_id ) ;
	}
}


// turning topic_solved of all topics in the forum on (batch action)
function d3forum_transact_turnsolvedon_in_forum( $mydirname , $forum_id )
{
	$db =& Database::getInstance() ;
	$forum_id = intval( $forum_id ) ;

	$sql = "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_solved=1 WHERE forum_id=$forum_id" ;
	if( ! $db->query( $sql ) ) die( 'ERROR IN TURNSOLVEDON '.__LINE__ ) ;
}


// return purified HTML
function d3forum_transact_htmlpurify( $text )
{
	if( substr( PHP_VERSION , 0 , 1 ) != 4 && file_exists( XOOPS_TRUST_PATH.'/modules/protector/library/HTMLPurifier.auto.php' ) ) {
		require_once XOOPS_TRUST_PATH.'/modules/protector/library/HTMLPurifier.auto.php' ;
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache', 'SerializerPath', XOOPS_TRUST_PATH.'/modules/protector/configs');
		$config->set('Core', 'Encoding', _CHARSET);
		//$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
		$purifier = new HTMLPurifier($config);
		$text = $purifier->purify( $text ) ;
	}
	return $text ;
}


?>