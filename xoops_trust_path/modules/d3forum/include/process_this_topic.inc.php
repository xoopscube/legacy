<?php

// get this "topic" from given $topic_id
$sql = "SELECT t.*,u2t.u2t_time,u2t.u2t_marked,u2t.u2t_rsv,p.number_entity,p.special_entity FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON t.topic_first_post_id=p.post_id WHERE t.topic_id=$topic_id" ;
if( ! $trs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
if( $db->getRowsNum( $trs ) <= 0 ) die( _MD_D3FORUM_ERR_READTOPIC ) ;
$topic_row = $db->fetchArray( $trs ) ;
$forum_id = intval( $topic_row['forum_id'] ) ;
$isadminormod = (boolean) @$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
$topic4assign = array(
	'id' => $topic_row['topic_id'] ,
	'external_link_id' => htmlspecialchars( $topic_row['topic_external_link_id'] , ENT_QUOTES ) ,
	'title' => $myts->makeTboxData4Show( $topic_row['topic_title'] , $topic_row['number_entity'] , $topic_row['special_entity'] ) ,
	'replies' => intval( $topic_row['topic_posts_count'] ) - 1 ,
	'views' => intval( $topic_row['topic_views'] ) ,
	'last_post_time' => intval( $topic_row['topic_last_post_time'] ) ,
	'last_post_time_formatted' => formatTimestamp( $topic_row['topic_last_post_time'] , 'm' ) ,
	'last_post_id' => intval( $topic_row['topic_last_post_id'] ) ,
	'last_post_uid' => intval( $topic_row['topic_last_uid'] ) ,
	'first_post_time' => intval( $topic_row['topic_first_post_time'] ) ,
	'first_post_time_formatted' => formatTimestamp( $topic_row['topic_first_post_time'] , 'm' ) ,
	'first_post_id' => intval( $topic_row['topic_first_post_id'] ) ,
	'first_post_uid' => intval( $topic_row['topic_first_uid'] ) ,
	'locked' => intval( $topic_row['topic_locked'] ) ,
	'sticky' => intval( $topic_row['topic_sticky'] ) ,
	'solved' => intval( $topic_row['topic_solved'] ) ,
	'invisible' => intval( $topic_row['topic_invisible'] ) ,
	'u2t_time' => intval( @$topic_row['u2t_time'] ) ,
	'u2t_marked' => intval( @$topic_row['u2t_marked'] ) ,
	'isadminormod' => $isadminormod ,
	'votes_count' => intval( $topic_row['topic_votes_count'] ) ,
	'votes_sum' => intval( $topic_row['topic_votes_sum'] ) ,
	'votes_avg' => round( $topic_row['topic_votes_sum'] / ( $topic_row['topic_votes_count'] - 0.0000001 ) , 2 ) ,
) ;


// TOPIC_INVISIBLE (check & make where)
if( $isadminormod ) {
	$whr_topic_invisible = '1' ;
} else {
	if( $topic_row['topic_invisible'] ) die( _MD_D3FORUM_ERR_READTOPIC ) ;
	$whr_topic_invisible = '! topic_invisible' ;
}


// where for comment-integration next&prev
$whr_external_link_id = $topic_row['topic_external_link_id'] ? "topic_external_link_id='".addslashes($topic_row['topic_external_link_id'])."'" : "1" ;

// get next "topic" of the forum
list( $next_topic_id ) = $db->fetchRow( $db->query( "SELECT MAX(topic_id) FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id<$topic_id AND forum_id=$forum_id AND ($whr_topic_invisible) AND ($whr_external_link_id)" ) ) ;
if( empty( $next_topic_id ) ) {
	$next_topic4assign = array() ;
} else {
	$next_topic_row = $db->fetchArray( $db->query( "SELECT t.topic_title,p.number_entity,p.special_entity FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON t.topic_first_post_id=p.post_id WHERE t.topic_id=$next_topic_id AND ($whr_topic_invisible)" ) ) ;
	$next_topic4assign = array(
		'id' => $next_topic_id ,
		'title' => $myts->makeTboxData4Show( $next_topic_row['topic_title'] , $next_topic_row['number_entity'] , $next_topic_row['special_entity'] ) ,
	) ;
}

// get prev "topic" of the forum
list( $prev_topic_id ) = $db->fetchRow( $db->query( "SELECT MIN(topic_id) FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id>$topic_id AND forum_id=$forum_id AND ($whr_topic_invisible) AND ($whr_external_link_id)" ) ) ;
if( empty( $prev_topic_id ) ) {
	$prev_topic4assign = array() ;
} else {
	$prev_topic_row = $db->fetchArray( $db->query( "SELECT t.topic_title,p.number_entity,p.special_entity FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON t.topic_first_post_id=p.post_id WHERE t.topic_id=$prev_topic_id AND ($whr_topic_invisible)" ) ) ;
	$prev_topic4assign = array(
		'id' => $prev_topic_id ,
		'title' => $myts->makeTboxData4Show( $prev_topic_row['topic_title'] , $prev_topic_row['number_entity'] , $prev_topic_row['special_entity'] ) ,
	) ;
}

// count up this topic
if( @$_SESSION[$mydirname.'_last_topic_id'] != $topic_id ) {
	$_SESSION[$mydirname.'_last_topic_id'] = $topic_id ;
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_views=topic_views+1 WHERE topic_id=$topic_id" ) ;
}

// u2t_time update
if( $uid && @$topic_row['u2t_time'] <= $topic_row['topic_last_post_time'] ) {
	// update/insert u2t table
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_users2topics")." SET u2t_time=UNIX_TIMESTAMP() WHERE uid=$uid AND topic_id=$topic_id" ) ;
	if( ! $db->getAffectedRows() ) $db->queryF( "INSERT INTO ".$db->prefix($mydirname."_users2topics")." SET uid=$uid,topic_id=$topic_id,u2t_time=UNIX_TIMESTAMP(),u2t_marked=0" ) ;
}

// $external_link_id
$external_link_id = $topic_row['topic_external_link_id'] ;

// assign breadcrumbs of this forum
array_splice( $xoops_breadcrumbs , 1 , 0 , array( array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?topic_id='.$topic_id , 'name' => $topic4assign['title'] ) ) ) ;


// for debug
// require_once dirname(__FILE__).'/transact_functions.php' ;
// d3forum_sync_topic( $mydirname , $topic_id ) ;

?>