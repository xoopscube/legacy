<?php

// get poster's information ($poster_*), $can_reply, $can_edit, $can_delete
include dirname(__FILE__).'/process_eachpost.inc.php' ;

// vote history
if( $uid ) {
	list( $past_vote ) = $db->fetchRow( $db->query( "SELECT vote_point FROM ".$db->prefix($mydirname."_post_votes")." WHERE post_id=$post_id AND uid=$uid" ) ) ;
}
$past_vote = isset( $past_vote ) ? intval( $past_vote ) : -1 ;

// posts array
$post4assign = array(
	'id' => intval( $post_row['post_id'] ) ,
	'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] , $post_row['special_entity'] ) ,
	'subject_raw' => $post_row['subject'] ,
	'pid' => intval( $post_row['pid'] ),
	'post_time' => intval( $post_row['post_time'] ) ,
	'post_time_formatted' => formatTimestamp( $post_row['post_time'] , 'm' ) ,
	'modified_time' => intval( $post_row['modified_time'] ) ,
	'modified_time_formatted' => formatTimestamp( $post_row['modified_time'] , 'm' ) ,
	'poster_uid' => intval( $post_row['uid'] ) ,
	'poster_uname' => $poster_uname4disp ,
	'poster_ip' => htmlspecialchars( $post_row['poster_ip'] , ENT_QUOTES ) ,
	'poster_rank_title' => $poster_rank_title4disp ,
	'poster_rank_image' => $poster_rank_image4disp ,
	'poster_is_online' => $poster_is_online ,
	'poster_avatar' => $poster_avatar ,
	'poster_posts_count' => $poster_posts_count ,
	'poster_regdate' => $poster_regdate ,
	'poster_regdate_formatted' => formatTimestamp( $poster_regdate , 's' ) ,
	'poster_from' => $poster_from4disp ,
	'modifier_ip' => htmlspecialchars( $post_row['poster_ip'] , ENT_QUOTES ) ,
	'html' => intval( $post_row['html'] ) ,
	'smiley' => intval( $post_row['smiley'] ) ,
	'br' => intval( $post_row['br'] ) ,
	'xcode' => intval( $post_row['xcode'] ) ,
	'icon' => intval( $post_row['icon'] ) ,
	'attachsig' => intval( $post_row['attachsig'] ) ,
	'signature' => $signature4disp ,
	'invisible' => intval( $post_row['invisible'] ) ,
	'approval' => intval( $post_row['approval'] ) ,
	'uid_hidden' => intval( $post_row['uid_hidden'] ) ,
	'depth_in_tree' => intval( $post_row['depth_in_tree'] ) ,
	'order_in_tree' => intval( $post_row['order_in_tree'] ) ,
	'unique_path' => htmlspecialchars( substr( $post_row['unique_path'] , 1 ) , ENT_QUOTES ) ,
	'votes_count' => intval( $post_row['votes_count'] ) ,
	'votes_sum' => intval( $post_row['votes_sum'] ) ,
	'votes_avg' => round( $post_row['votes_sum'] / ( $post_row['votes_count'] - 0.0000001 ) , 2 ) ,
	'past_vote' => $past_vote ,
	'guest_name' => $myts->makeTboxData4Show( $post_row['guest_name'] ) ,
	'guest_email' => $myts->makeTboxData4Show( $post_row['guest_email'] ) ,
	'guest_url' => $myts->makeTboxUrl4Show( $post_row['guest_url'] ) ,
	'guest_trip' => $myts->makeTboxData4Show( $post_row['guest_trip'] ) ,
	'post_text' => $myts->displayTarea( $post_row['post_text'] , $post_row['html'] , $post_row['smiley'] , $post_row['xcode'] , $xoopsModuleConfig['allow_textimg'] , $post_row['br'] , 0 , $post_row['number_entity'] , $post_row['special_entity'] ) ,
	'post_text_raw' => $post_row['post_text'] , // caution
	'can_edit' => $can_edit ,
	'can_delete' => $can_delete ,
	'can_reply' => $can_reply ,
	'can_vote' => $can_vote ,
) ;

// assign breadcrumbs of this forum
$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?post_id='.$post_id , 'name' => $post4assign['subject'] ) ;

?>