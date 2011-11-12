<?php

$topic_id = intval( @$_GET['topic_id'] ) ;

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname(__FILE__).'/process_this_topic.inc.php' ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(__FILE__).'/process_this_forum.inc.php' ) redirect_header( XOOPS_URL.'/user.php' , 3 , _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(__FILE__).'/process_this_category.inc.php' ) redirect_header( XOOPS_URL.'/user.php' , 3 , _MD_D3FORUM_ERR_READCATEGORY ) ;


// post order
switch( $postorder ) {
	case 3 :
		$postorder4sql = 'post_id DESC' ;
		break ;
	case 2 :
		$postorder4sql = 'post_id' ;
		break ;
	case 1 :
		$postorder4sql = 'order_in_tree DESC,post_id DESC' ;
		break ;
	case 0 :
	default :
		$postorder4sql = 'order_in_tree,post_id' ;
		break ;
}

// post_hits ~ pagenavi //naao from

	$sql = "SELECT COUNT(post_id) FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id='$topic_id'" ;
	if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	list( $post_hits ) = $db->fetchRow( $prs ) ;

	// pagenav
	$pagenav = '' ;
	$query4nav = 'topic_id=' . $topic_id ;
	// LIMIT
	$num = $xoopsModuleConfig['viewallbreak'] < 5 ? 5 : intval( $xoopsModuleConfig['viewallbreak'] ) ;
	$pos = 0 ;
	if( $post_hits > $num ) {
		// POS
		//$pos = isset( $_GET['pos'] ) ? intval( $_GET['pos'] ) : (($postorder != 3) ? (int)(($post_hits-1) / $num) * $num : 0) ;
		$pos = isset( $_GET['pos'] ) ? intval( $_GET['pos'] )
			: (($postorder == 0) || ($postorder == 2) ? (int)(($post_hits-1) / $num) * $num : 0) ;
		require_once dirname( dirname(__FILE__) ).'/class/D3forumPagenav.class.php' ;
		$pagenav_obj = new D3forumPagenav( $post_hits , $num , $pos , 'pos', $query4nav ) ;
		$pagenav = $pagenav_obj->getNav() ;
	}

// post_hits ~ pagenavi //naao to

// posts loop
$max_post_time = 0 ;
$last_post_offset = 0 ;
$posts = array() ;
//$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id ORDER BY order_in_tree,post_id LIMIT $pos,$num" ; //naao
$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE topic_id=$topic_id ORDER BY $postorder4sql LIMIT $pos,$num" ; //naao
if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

while( $post_row = $db->fetchArray( $prs ) ) {

	// get poster's information ($poster_*), $can_reply, $can_edit, $can_delete
	include dirname(__FILE__).'/process_eachpost.inc.php' ;

	// get row of last_post
	if( $post_row['post_time'] > $max_post_time ) $last_post_offset = sizeof( $posts ) ;

	// posts array
	$posts[] = array(
		'id' => intval( $post_row['post_id'] ) ,
		'subject' => $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] , $post_row['special_entity'] ) ,
		'subject_raw' => $post_row['subject'] ,
		'pid' => intval( $post_row['pid'] ),
		'topic_id' => intval( $post_row['topic_id'] ),
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

// save "the first post"
$first_post = $posts[0] ;

// rebuild tree informations
$posts = d3forum_make_treeinformations( $posts ) ;

// post order
switch( $postorder ) {
	case 3 :
		usort( $posts , create_function( '$a,$b' , 'return $a["id"] > $b["id"] ? -1 : 1 ;' ) ) ;
		break ;
	case 2 :
		usort( $posts , create_function( '$a,$b' , 'return $a["id"] > $b["id"] ? 1 : -1 ;' ) ) ;
		break ;
	case 1 :
		rsort( $posts ) ;
		break ;
	case 0 :
	default :
		break ;
}

// for meta description // naao
$reply_for_description = '';
if ($post_hits) {
	if ($post_hits > 1) $reply_for_description = '['._MD_D3FORUM_REPLIES.($post_hits - 1).']';
	$d3forum_meta_description = mb_substr(trim(strip_tags($posts[0]['post_text'])), 0, 57, _CHARSET);
	$d3forum_meta_description .= '...';
	$d3forum_meta_description .= mb_substr(trim(strip_tags($posts[count($posts)-1]['post_text'])), 0, (60-strlen($reply_for_description)), _CHARSET).$reply_for_description;
} else {
	$d3forum_meta_description = mb_substr(trim(strip_tags($posts[0]['post_text'])), 0, 120, _CHARSET);
}
$d3forum_meta_description = preg_replace('/[\r\n\t]/', '', htmlspecialchars($d3forum_meta_description));

// reassign last_post informations
$topic4assign['last_post_subject'] = @$posts[ $last_post_offset ]['subject'] ;
$topic4assign['last_post_uname'] = @$posts[ $last_post_offset ]['poster_uname'] ;

	// naao from
if( is_object( $xoopsUser ) ) {
	if ($xoopsModuleConfig['use_name'] == 1 && $xoopsUser->getVar( 'name' ) ) {
		$poster_uname4disp = $xoopsUser->getVar( 'name' ) ;
	} else {
		$poster_uname4disp = $xoopsUser->getVar( 'uname' ) ;
	}

} else { $poster_uname4disp = '' ;}

$tree = array();
$topics_count = 0;
if( $topic4assign['external_link_id'] >0 ) {

	$sql = "SELECT p.*, t.topic_locked, t.topic_id, t.forum_id, t.topic_last_uid, t.topic_last_post_time
		FROM ".$db->prefix($mydirname."_topics")." t
		LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON p.topic_id=t.topic_id
		WHERE t.forum_id='".(int)$forum4assign['id']."' AND p.depth_in_tree='0'
			AND (t.topic_external_link_id='".(int)$topic4assign['external_link_id']."'
			OR t.topic_id=$topic_id ) " ;

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

$xoopsOption['template_main'] = $mydirname.'_main_listposts.html' ;
include XOOPS_ROOT_PATH.'/header.php' ;

unset( $xoops_breadcrumbs[ sizeof( $xoops_breadcrumbs ) - 1 ]['url'] ) ;
$xoopsTpl->assign(
	array(
		'category' => $category4assign ,
		'forum' => $forum4assign ,
		'topic' => $topic4assign ,
		'next_topic' => $next_topic4assign ,
		'prev_topic' => $prev_topic4assign ,
		'first_post' => $first_post ,
		'posts' => $posts ,
		'post_hits' => intval( @$post_hits ) ,	// naao
		'tree' => $tree ,			// naao
		'tree_tp_count' => $topics_count ,	// naao
		'page' => 'listposts' ,
		'ret_name' => 'topic_id' ,
		'ret_val' => $topic_id ,
		'uname' => $poster_uname4disp ,
		'pagenav' => $pagenav ,	//naao
		'pos' => $pos ,		//naao
		'xoops_pagetitle' => join(' - ', array($topic4assign['title'], $forum4assign['title'], $xoopsModule->getVar('name'))) ,
		'xoops_meta_description' => $d3forum_meta_description ,	// naao
		'xoops_breadcrumbs' => $xoops_breadcrumbs ,
	)
) ;


/*

// 順番とビューモードの実装

if( is_object( $xoopsUser ) ) {
	$uid = $xoopsUser->getVar('uid') ;
	$viewmode = $viewmode ? $viewmode : $xoopsUser->getVar('umode') ;
	$uorder = $xoopsUser->getVar('uorder') == 1 ? 'DESC' : 'ASC' ;
	$order = $order ? $order : $uorder ;
} else {
	$uid = 0 ;
}

// topic mark on/off 処理

// ページ分割処理は不要・むしろ各フォーラム毎に投稿数上限を設定可とする
　→　仮に実装　naao
　　　TODo :「親投稿」リンクの修正

*/
?>