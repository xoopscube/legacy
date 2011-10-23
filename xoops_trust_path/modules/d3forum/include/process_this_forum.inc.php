<?php

// get this "forum" from given $forum_id
$sql = "SELECT * FROM ".$db->prefix($mydirname."_forums")." f WHERE ($whr_read4forum) AND f.forum_id=$forum_id" ;
if( ! $frs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
//if( $db->getRowsNum( $frs ) <= 0 ) die( _MD_D3FORUM_ERR_READFORUM ) ;
if( $db->getRowsNum( $frs ) <= 0 ) return false ;
$forum_row = $db->fetchArray( $frs ) ;
$cat_id = intval( $forum_row['cat_id'] ) ;
$isadminormod = (boolean)$forum_permissions[ $forum_id ]['is_moderator'] || $isadmin ;
$can_post = (boolean)$forum_permissions[ $forum_id ]['can_post'] || $isadminormod ;
$can_edit = (boolean)$forum_permissions[ $forum_id ]['can_edit'] || $isadminormod ;
$can_delete = (boolean)$forum_permissions[ $forum_id ]['can_delete'] || $isadminormod ;
$need_approve = ! (boolean)$forum_permissions[ $forum_id ]['post_auto_approved'] && ! $isadminormod ;
$forum4assign = array(
	'id' => $forum_row['forum_id'] ,
	'title' => $myts->makeTboxData4Show( $forum_row['forum_title'] ) ,
	'desc' => $myts->displayTarea( $forum_row['forum_desc'] ) ,
	'external_link_format' => htmlspecialchars( $forum_row['forum_external_link_format'] , ENT_QUOTES ) ,
	'topics_count' => intval( $forum_row['forum_topics_count'] ) ,
	'posts_count' => intval( $forum_row['forum_posts_count'] ) ,
	'last_post_time' => intval( $forum_row['forum_last_post_time'] ) ,
	'last_post_time_formatted' => formatTimestamp( $forum_row['forum_last_post_time'] , 'm' ) ,
	'last_post_id' => intval( $forum_row['forum_last_post_id'] ) ,
	'moderate_groups' => d3forum_get_forum_moderate_groups4show( $mydirname , $forum_row['forum_id'] ) ,
	'moderate_users' => d3forum_get_forum_moderate_users4show( $mydirname , $forum_row['forum_id'] ) ,
	'need_approve' => $need_approve ,
	'can_post' => $can_post ,
	'isadminormod' => $isadminormod ,
) ;

// assign link or free description (by class) as comment
if( ! empty( $external_link_id ) ) {
	$topic4assign['comment_link'] = d3forum_get_comment_link( $forum_row['forum_external_link_format'] , $external_link_id ) ;
	$topic4assign['comment_description'] = d3forum_get_comment_description( $mydirname , $forum_row['forum_external_link_format'] , $external_link_id ) ;
}

// assign breadcrumbs of this forum
array_splice( $xoops_breadcrumbs , 1 , 0 , array( array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?forum_id='.$forum_id , 'name' => $forum4assign['title'] ) ) ) ;

?>