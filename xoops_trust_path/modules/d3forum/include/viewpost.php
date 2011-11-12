<?php

$post_id = intval( @$_GET['post_id'] ) ;

// get this "post" from given $post_id
$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
if( $db->getRowsNum( $prs ) <= 0 ) die( _MD_D3FORUM_ERR_READPOST ) ;
$post_row = $db->fetchArray( $prs ) ;
$topic_id = intval( $post_row['topic_id'] ) ;

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname(__FILE__).'/process_this_topic.inc.php' ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(__FILE__).'/process_this_forum.inc.php' ) redirect_header( XOOPS_URL.'/user.php' , 3 , _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(__FILE__).'/process_this_category.inc.php' ) redirect_header( XOOPS_URL.'/user.php' , 3 , _MD_D3FORUM_ERR_READCATEGORY ) ;

// get $post4assign
include dirname(__FILE__).'/process_this_post.inc.php' ;

$d3forum_meta_description = preg_replace('/[\r\n\t]/','',htmlspecialchars(mb_substr(strip_tags($post4assign['post_text']),0,120, _CHARSET),ENT_QUOTES)); // naao

// posts loop
$posts = array() ;
$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id ORDER BY order_in_tree,post_id" ; // TODO
if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
while( $post_row = $db->fetchArray( $prs ) ) {

	// get poster's information ($poster_*), $can_reply, $can_edit, $can_delete
	include dirname(__FILE__).'/process_eachpost.inc.php' ;

	// posts array
	$posts[] = array(
		'id' => intval( $post_row['post_id'] ) ,
		'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] , $post_row['special_entity'] ) ,
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
		'uid_hidden' => intval( $post_row['uid_hidden'] ) ,
		'depth_in_tree' => intval( $post_row['depth_in_tree'] ) ,
		'order_in_tree' => intval( $post_row['order_in_tree'] ) ,
		'unique_path' => htmlspecialchars( substr( $post_row['unique_path'] , 1 ) , ENT_QUOTES ) ,
		'votes_count' => intval( $post_row['votes_count'] ) ,
		'votes_sum' => intval( $post_row['votes_sum'] ) ,
		'votes_avg' => round( $post_row['votes_sum'] / ( $post_row['votes_count'] - 0.0000001 ) , 2 ) ,
		'past_vote' => -1 , // TODO
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
}

// rebuild tree informations
$posts = d3forum_make_treeinformations( $posts ) ;

// copy some tree informations from $posts into $post
foreach( $posts as $eachpost ) {
	if( $eachpost['id'] == $post_id ) {
		$post4assign['next_id'] = @$eachpost['next_id'] ;
		$post4assign['prev_id'] = @$eachpost['prev_id'] ;
		$post4assign['first_child_id'] = @$eachpost['first_child_id'] ;
		$post4assign['f1s'] = @$eachpost['f1s'] ;
		break ;
	}
}

// for notification...
$_GET['topic_id'] = $topic_id ;

	// naao from
if( is_object( $xoopsUser ) ) {
	if ($xoopsModuleConfig['use_name'] == 1 && $xoopsUser->getVar( 'name' ) ) {
		$poster_uname4disp = $xoopsUser->getVar( 'name' ) ;
	} else {
		$poster_uname4disp = $xoopsUser->getVar( 'uname' ) ;
	}

} else { $poster_uname4disp = '' ;}

$tree = array();
$topics_count=0;
if( $topic4assign['external_link_id'] >0 ) {

	$sql = "SELECT p.*, t.topic_locked, t.topic_id, t.forum_id, t.topic_last_uid, t.topic_last_post_time
		FROM ".$db->prefix($mydirname."_topics")." t
		LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON p.topic_id=t.topic_id
		WHERE t.forum_id='".(int)$forum4assign['id']."' AND p.depth_in_tree='0'
			AND (t.topic_external_link_id='".(int)$topic4assign['external_link_id']."'
			OR t.topic_id=$topic_id )" ;

	if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	while( $post_row = $db->fetchArray( $prs ) ) {
		// topics array
		$topic_last_uid = intval( $post_row['topic_last_uid'] ) ;
		$topic_last_post_time = intval( $post_row['topic_last_post_time'] ) ;
		$topic_last_uname = XoopsUser::getUnameFromId( $topic_last_uid , $xoopsModuleConfig['use_name']) ; //naao usereal=1
		$topic_last_uname = $topic_last_uid > 0 ? $topic_last_uname : $myts->makeTboxData4Show( $post_row['guest_name'] ) ;

		$tree[] = array(
			'id' => intval( $post_row['post_id'] ) ,
			'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] ,
					 $post_row['special_entity'] ) ,
			'post_time_formatted' => formatTimestamp( $post_row['post_time'] , 'm' ) ,
			'poster_uid' => $topic_last_uid ,
			'poster_uname' => $topic_last_uname ,
			'icon' => intval( $post_row['icon'] ) ,
			'depth_in_tree' => intval( $post_row['depth_in_tree'] ) ,
			'order_in_tree' => intval( $post_row['order_in_tree'] ) ,
			'topic_id' => intval( $post_row['topic_id'] ) ,
			'ul_in' => '<ul><li>' ,
			'ul_out' => '</li></ul>' ,
		);
	}
		$topics_count = count($tree) ;
}
	// naao to

$xoopsOption['template_main'] = $mydirname.'_main_viewpost.html' ;
include XOOPS_ROOT_PATH.'/header.php' ;

unset( $xoops_breadcrumbs[ sizeof( $xoops_breadcrumbs ) - 1 ]['url'] ) ;
$xoopsTpl->assign(
	array(
		'category' => $category4assign ,
		'forum' => $forum4assign ,
		'topic' => $topic4assign ,
		'next_topic' => $next_topic4assign ,
		'prev_topic' => $prev_topic4assign ,
		'post' => $post4assign ,
		'posts' => $posts ,
		'tree' => $tree ,	// naao
		'tree_tp_count' => $topics_count ,	// naao
		'page' => 'viewpost' ,
		'ret_name' => 'post_id' ,
		'ret_val' => $post_id ,
		'uname' => $poster_uname4disp ,
		'xoops_pagetitle' => join(' - ', array($post4assign['subject'], $forum4assign['title'], $xoopsModule->getVar('name'))) ,
		'xoops_meta_description' => $d3forum_meta_description ,	// naao
		'xoops_breadcrumbs' => $xoops_breadcrumbs ,
	)
) ;


?>