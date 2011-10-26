<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php' ;

$post_id = intval( @$_GET['post_id'] ) ;

// get this "post" from given $post_id
$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
if( $db->getRowsNum( $prs ) <= 0 ) die( _MD_D3FORUM_ERR_READPOST ) ;
$post_row = $db->fetchArray( $prs ) ;
$topic_id = intval( $post_row['topic_id'] ) ;

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname(dirname(__FILE__)).'/include/process_this_topic.inc.php' ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_forum.inc.php' ) die( _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

// get $post4assign
include dirname(dirname(__FILE__)).'/include/process_this_post.inc.php' ;

// special check for update_post_approval
if( ! $isadminormod ) die( _MD_D3FORUM_ERR_MODERATETOPIC ) ;

// approve the post
$db->queryF( "UPDATE ".$db->prefix($mydirname."_posts")." SET approval=1 WHERE post_id=$post_id" ) ;

// turn topic_invisible off also
$db->queryF( "UPDATE ".$db->prefix($mydirname."_topics")." SET topic_invisible=0 WHERE topic_id=$topic_id" ) ;

$allowed_identifiers = array( 'post_id' , 'topic_id' , 'forum_id' , 'cat_ids' ) ;

if( in_array( $_GET['ret_name'] , $allowed_identifiers ) ) {
	$ret_request = $_GET['ret_name'] . '=' . preg_replace( '/[^0-9,]/' , '' , $_GET['ret_val'] ) ;
} else {
	$ret_request = "topic_id=$topic_id" ;
}

redirect_header( XOOPS_URL."/modules/$mydirname/index.php?$ret_request" , 0 , _MD_D3FORUM_MSG_UPDATED ) ;
exit ;

?>