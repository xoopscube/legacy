<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;

$topic_id = intval( @$_GET['topic_id'] ) ;

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname(dirname(__FILE__)).'/include/process_this_topic.inc.php' ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_forum.inc.php' ) die( _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

// special check for update_topic_solved
if( ! $isadminormod ) die( _MD_D3FORUM_ERR_MODERATETOPIC ) ;

if( empty( $xoopsModuleConfig['use_solved'] ) ) {
	// force topic_solved=1 if "solved" is disable
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_solved=1 WHERE topic_id=$topic_id" ) ;
} else {
	// flip topic_solved
	$db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_solved = ! topic_solved WHERE topic_id=$topic_id" ) ;
}

$allowed_identifiers = array( 'post_id' , 'topic_id' , 'forum_id' , 'cat_ids' ) ;

if( in_array( $_GET['ret_name'] , $allowed_identifiers ) ) {
	$ret_request = $_GET['ret_name'] . '=' . preg_replace( '/[^0-9,]/' , '' , $_GET['ret_val'] ) ;
} else {
	$ret_request = "topic_id=$topic_id" ;
}

redirect_header( XOOPS_URL."/modules/$mydirname/index.php?$ret_request" , 0 , _MD_D3FORUM_MSG_UPDATED ) ;
exit ;

?>