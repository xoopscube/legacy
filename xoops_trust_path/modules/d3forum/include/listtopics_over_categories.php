<?php

$cat_ids = array() ;
foreach( explode( ',' , $_GET['cat_ids'] ) as $cat_id ) {
	if( $cat_id > 0 ) {
		$cat_ids[] = intval( $cat_id ) ;
	}
}

if( empty( $cat_ids ) ) {
	// all topics in the module
	$pagetitle = _MD_D3FORUM_LISTALLTOPICS ;
	$category4assign = array() ;
	$cat_ids4param = '0' ;
	$whr_cat_ids = '1' ;
	$isadminorcatmod = $isadmin ;
} else if( sizeof( $cat_ids ) == 1 ) {
	// topics under the specified category
	$pagetitle = _MD_D3FORUM_LISTTOPICSINCATEGORY ;
	$cat_id = $cat_ids[0] ;
	$_GET['cat_id'] = $cat_id ; // for notification
	$cat_ids4param = $cat_id ;
	$whr_cat_ids = 'c.cat_id='.$cat_id ;
	// get&check this category ($category4assign, $category_row), override options
	if( ! include dirname(__FILE__).'/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;
} else {
	// topics under categories separated with commma
	sort( $cat_ids ) ;
	$pagetitle = _MD_D3FORUM_LISTTOPICSINCATEGORIES ;
	$category4assign = array() ;
	$cat_ids4param = implode( ',' , $cat_ids ) ;
	$whr_cat_ids = 'c.cat_id IN ('.$cat_ids4param.')' ;
	$isadminorcatmod = $isadmin ;
}

// naao
// get all "forum"s
$sql = "SELECT forum_id, forum_external_link_format FROM ".$db->prefix($mydirname."_forums") ;
$frs = $db->query( $sql ) ;
$d3com = array() ;
while( $forum_row = $db->fetchArray( $frs ) ) {
	// d3comment object
	$temp_forum_id = intval($forum_row['forum_id']);
	if( ! empty( $forum_row['forum_external_link_format'] ) ) $d3com[$temp_forum_id] =& d3forum_main_get_comment_object( $mydirname , $forum_row['forum_external_link_format'] ) ;
	else $d3com[$temp_forum_id] = false ;
}

// get $odr_options, $solved_options, $query4assign
$query4nav = 'cat_ids='.$cat_ids4param ;
include dirname(__FILE__).'/process_query4topics.inc.php' ;

// INVISIBLE
$whr_invisible = $isadminorcatmod ? '1' : '! t.topic_invisible' ;

// number query
$sql = "SELECT COUNT(t.topic_id) FROM ".$db->prefix($mydirname."_topics")." t LEFT JOIN ".$db->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN ".$db->prefix($mydirname."_posts")." lp ON lp.post_id=t.topic_last_post_id LEFT JOIN ".$db->prefix($mydirname."_posts")." fp ON fp.post_id=t.topic_first_post_id LEFT JOIN ".$db->prefix($mydirname."_forums")." f ON f.forum_id=t.forum_id LEFT JOIN ".$db->prefix($mydirname."_categories")." c ON c.cat_id=f.cat_id WHERE ($whr_invisible) AND ($whr_solved) AND ($whr_txt) AND ($whr_read4forum) AND ($whr_read4cat) AND ($whr_cat_ids)" ;
if( ! $trs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
list( $topic_hits ) = $db->fetchRow( $trs ) ;

// pagenav
if( $topic_hits > $num ) {
	require_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
	$pagenav_obj = new XoopsPageNav( $topic_hits , $num , $pos , 'pos', $query4nav ) ;
	$pagenav = $pagenav_obj->renderNav() ;
}

// naao
$sql = "SELECT t.*, lp.post_text AS lp_post_text, lp.subject AS lp_subject, lp.icon AS lp_icon,
	lp.number_entity AS lp_number_entity, lp.special_entity AS lp_special_entity,
	lp.guest_name AS lp_guest_name, fp.subject AS fp_subject, fp.icon AS fp_icon,
	fp.number_entity AS fp_number_entity, fp.special_entity AS fp_special_entity,
	fp.guest_name AS fp_guest_name, u2t.u2t_time, u2t.u2t_marked, u2t.u2t_rsv,
	c.cat_id, c.cat_title,f.forum_id, f.forum_title, f.forum_external_link_format FROM "
	.$db->prefix($mydirname."_topics")." t LEFT JOIN "
	.$db->prefix($mydirname."_users2topics")." u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN "
	.$db->prefix($mydirname."_posts")." lp ON lp.post_id=t.topic_last_post_id LEFT JOIN "
	.$db->prefix($mydirname."_posts")." fp ON fp.post_id=t.topic_first_post_id LEFT JOIN "
	.$db->prefix($mydirname."_forums")." f ON f.forum_id=t.forum_id LEFT JOIN "
	.$db->prefix($mydirname."_categories")." c ON c.cat_id=f.cat_id
	WHERE ($whr_invisible) AND ($whr_solved) AND ($whr_txt) AND ($whr_read4forum)
	AND ($whr_read4cat) AND ($whr_cat_ids) ORDER BY $odr_query LIMIT $pos,$num" ;

if( ! $trs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;

// topics loop
$topics = array() ;
while( $topic_row = $db->fetchArray( $trs ) ) {

	$topic_id = intval( $topic_row['topic_id'] ) ;

	// get last poster's object
	$user_handler =& xoops_gethandler( 'user' ) ;
	$last_poster_obj =& $user_handler->get( intval( $topic_row['topic_last_uid'] ) ) ;
	$first_poster_obj =& $user_handler->get( intval( $topic_row['topic_first_uid'] ) ) ;
	// naao from
	//$last_post_uname4html = is_object( $last_poster_obj ) ? $last_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
	if (is_object( $last_poster_obj )) {
		if ($xoopsModuleConfig['use_name'] == 1 && $last_poster_obj->getVar( 'name' ) ) {
			$last_post_uname4html =  $last_poster_obj->getVar( 'name' ) ;
		} else {
			$last_post_uname4html =  $last_poster_obj->getVar( 'uname' ) ;
		}
	} else {
			$last_post_uname4html =  $xoopsConfig['anonymous'] ;
	}

	//$first_post_uname4html = is_object( $first_poster_obj ) ? $first_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
	if (is_object( $first_poster_obj )) {
		if ($xoopsModuleConfig['use_name'] == 1 && $first_poster_obj->getVar( 'name' ) ) {
			$first_post_uname4html =  $first_poster_obj->getVar( 'name' ) ;
		} else {
			$first_post_uname4html =  $first_poster_obj->getVar( 'uname' ) ;
		}
	} else {
			$first_post_uname4html =  $xoopsConfig['anonymous'] ;
	}
	// naao to

	// naao from
	// d3comment overridings
	$can_display = true;	//default
	if( is_object( $d3com[intval($topic_row['forum_id'])]) ) {
		$d3com_obj = $d3com[intval($topic_row['forum_id'])];
		$external_link_id = intval($topic_row['topic_external_link_id']);
		if( ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) === false && ! $isadminormod ) {
			$can_display = false;
		}
	}	// naao to

	// topics array
	if($can_display == true) {	// naao
	    $topics[] = array(
		'id' => $topic_row['topic_id'] ,
		'title' => $myts->makeTboxData4Show( $topic_row['topic_title'] , $topic_row['fp_number_entity'] , $topic_row['fp_special_entity'] ) ,
		'forum_id' => $topic_row['forum_id'] ,
		'forum_title' => $myts->makeTboxData4Show( $topic_row['forum_title'] ) ,
		'forum_isadminormod' => (boolean)$forum_permissions[ $topic_row['forum_id'] ]['is_moderator'] || $isadmin ,
		'cat_id' => $topic_row['cat_id'] ,
		'cat_title' => $myts->makeTboxData4Show( $topic_row['cat_title'] ) ,
		'replies' => intval( $topic_row['topic_posts_count'] ) - 1 ,
		'views' => intval( $topic_row['topic_views'] ) ,
		'last_post_time' => intval( $topic_row['topic_last_post_time'] ) ,
		'last_post_time_formatted' => formatTimestamp( $topic_row['topic_last_post_time'] , 'm' ) ,
		'last_post_id' => intval( $topic_row['topic_last_post_id'] ) ,
		'last_post_icon' => intval( $topic_row['lp_icon'] ) ,
		'last_post_text_raw' => $topic_row['lp_post_text'] ,
		'last_post_subject' => $myts->makeTboxData4Show( $topic_row['lp_subject'] , $topic_row['lp_number_entity'] , $topic_row['lp_special_entity'] ) ,
		'last_post_uid' => intval( $topic_row['topic_last_uid'] ) ,
		'last_post_uname' => $last_post_uname4html ,
		'first_post_time' => intval( $topic_row['topic_first_post_time'] ) ,
		'first_post_time_formatted' => formatTimestamp( $topic_row['topic_first_post_time'] , 'm' ) ,
		'first_post_id' => intval( $topic_row['topic_first_post_id'] ) ,
		'first_post_icon' => intval( $topic_row['fp_icon'] ) ,
		'first_post_subject' => $myts->makeTboxData4Show( $topic_row['fp_subject'] , $topic_row['fp_number_entity'] , $topic_row['fp_special_entity'] ) ,
		'first_post_uid' => intval( $topic_row['topic_first_uid'] ) ,
		'first_post_uname' => $first_post_uname4html ,
		'bit_new' => $topic_row['topic_last_post_time'] > @$topic_row['u2t_time'] ? 1 : 0 ,
		'bit_hot' => $topic_row['topic_posts_count'] > $xoopsModuleConfig['hot_threshold'] ? 1 : 0 ,
		'locked' => intval( $topic_row['topic_locked'] ) ,
		'sticky' => intval( $topic_row['topic_sticky'] ) ,
		'solved' => intval( $topic_row['topic_solved'] ) ,
		'invisible' => intval( $topic_row['topic_invisible'] ) ,
		'u2t_time' => intval( @$topic_row['u2t_time'] ) ,
		'u2t_marked' => intval( @$topic_row['u2t_marked'] ) ,
		'votes_count' => intval( $topic_row['topic_votes_count'] ) ,
		'votes_sum' => intval( $topic_row['topic_votes_sum'] ) ,
		'votes_avg' => round( $topic_row['topic_votes_sum'] / ( $topic_row['topic_votes_count'] - 0.0000001 ) , 2 ) ,
		'external_link_id' => intval( $topic_row['topic_external_link_id'] ) , //naao
		'last_post_gname' => $myts->makeTboxData4Show( $topic_row['lp_guest_name'] , $topic_row['lp_number_entity'] , $topic_row['lp_special_entity'] ) , //naao
		'first_post_gname' => $myts->makeTboxData4Show( $topic_row['fp_guest_name'] , $topic_row['lp_number_entity'] , $topic_row['lp_special_entity'] ) , //naao
	    ) ;
	}	//naao
}

$xoopsOption['template_main'] = $mydirname.'_main_listtopics_over_categories.html' ;
include XOOPS_ROOT_PATH.'/header.php' ;

$xoopsTpl->assign(
	array(
		'category' => $category4assign ,
		'topics' => $topics ,
		'topic_hits' => intval( $topic_hits ) ,
		'odr_options' => $odr_options ,
		'solved_options' => $solved_options ,
		'query' => $query4assign ,
		'cat_ids' => $cat_ids4param ,
		'pagenav' => @$pagenav ,
		'page' => 'listtopics_over_categories' ,
		'pagetitle' => $pagetitle ,
		'xoops_pagetitle' => join(' - ', array($pagetitle, $category4assign['title'], $xoopsModule->getVar('name'))) ,
	)
) ;


?>