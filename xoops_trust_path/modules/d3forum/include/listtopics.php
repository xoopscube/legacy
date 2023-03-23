<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    GPL v2.0
 */

$forum_id = (int) @$_GET['forum_id'];

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if ( ! include __DIR__ . '/process_this_forum.inc.php' ) {
	redirect_header( XOOPS_URL . '/user.php', 3, _MD_D3FORUM_ERR_READFORUM );
}

// get&check this category ($category4assign, $category_row), override options
if ( ! include __DIR__ . '/process_this_category.inc.php' ) {
	redirect_header( XOOPS_URL . '/user.php', 3, _MD_D3FORUM_ERR_READCATEGORY );
}

// get $odr_options, $solved_options, $query4assign
$query4nav = "forum_id=$forum_id";

include __DIR__ . '/process_query4topics.inc.php';

// INVISIBLE
$whr_invisible = $isadminormod ? '1' : '! t.topic_invisible';

// number query
$sql = 'SELECT COUNT(t.topic_id) FROM '
       . $db->prefix( $mydirname . '_topics' ) . ' t LEFT JOIN '
       . $db->prefix( $mydirname . '_users2topics' ) . " u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN " . $db->prefix( $mydirname . '_posts' ) . ' lp ON lp.post_id=t.topic_last_post_id LEFT JOIN '
       . $db->prefix( $mydirname . '_posts' ) . " fp ON fp.post_id=t.topic_first_post_id WHERE t.forum_id=$forum_id AND ($whr_invisible) AND ($whr_solved) AND ($whr_txt) AND ($whr_external_link_id)";

if ( ! $trs = $db->query( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

[ $topic_hits ] = $db->fetchRow( $trs );

// pagenav
$pagenav = '';

if ( $topic_hits > $num ) {

	require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

	$pagenav_obj = new XoopsPageNav( $topic_hits, $num, $pos, 'pos', $query4nav );

	$pagenav = $pagenav_obj->renderNav();
}

// naao
$sql = 'SELECT t.*, lp.post_text AS lp_post_text, lp.subject AS lp_subject, lp.icon AS lp_icon,
	lp.number_entity AS lp_number_entity, lp.special_entity AS lp_special_entity,
	lp.guest_name AS lp_guest_name, fp.subject AS fp_subject, fp.icon AS fp_icon,
	fp.number_entity AS fp_number_entity, fp.special_entity AS fp_special_entity,
	fp.guest_name AS fp_guest_name, u2t.u2t_time, u2t.u2t_marked, u2t.u2t_rsv FROM '
       . $db->prefix( $mydirname . '_topics' ) . ' t LEFT JOIN '
       . $db->prefix( $mydirname . '_users2topics' ) . " u2t ON t.topic_id=u2t.topic_id AND u2t.uid=$uid LEFT JOIN "
       . $db->prefix( $mydirname . '_posts' ) . ' lp ON lp.post_id=t.topic_last_post_id LEFT JOIN '
       . $db->prefix( $mydirname . '_posts' ) . " fp ON fp.post_id=t.topic_first_post_id
	WHERE t.forum_id=$forum_id AND ($whr_invisible) AND ($whr_solved) AND ($whr_txt)
	AND ($whr_external_link_id) ORDER BY $odr_query LIMIT $pos,$num";

if ( ! $trs = $db->query( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

// naao
// d3comment object
if ( ! empty( $forum_row['forum_external_link_format'] ) ) {
	$d3com = d3forum_main_get_comment_object( $mydirname, $forum_row['forum_external_link_format'], $forum_id );
} else {
	$d3com = false;
}

// topics loop
$topics = [];

while ( $topic_row = $db->fetchArray( $trs ) ) {

	$topic_id = (int) $topic_row['topic_id'];

	// get last poster's object
	$user_handler     = xoops_gethandler( 'user' );
	$last_poster_obj  = $user_handler->get( (int) $topic_row['topic_last_uid'] );
	$first_poster_obj = $user_handler->get( (int) $topic_row['topic_first_uid'] );
	// naao from
	//$last_post_uname4html = is_object( $last_poster_obj ) ? $last_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
	if ( is_object( $last_poster_obj ) ) {
		if ( 1 === $xoopsModuleConfig['use_name'] && $last_poster_obj->getVar( 'name' ) ) {
			$last_post_uname4html = $last_poster_obj->getVar( 'name' );
		} else {
			$last_post_uname4html = $last_poster_obj->getVar( 'uname' );
		}
	} else {
		$last_post_uname4html = $xoopsConfig['anonymous'];
	}

	//$first_post_uname4html = is_object( $first_poster_obj ) ? $first_poster_obj->getVar( 'uname' ) : $xoopsConfig['anonymous'] ;
	if ( is_object( $first_poster_obj ) ) {
		if ( 1 === $xoopsModuleConfig['use_name'] && $first_poster_obj->getVar( 'name' ) ) {
			$first_post_uname4html = $first_poster_obj->getVar( 'name' );
		} else {
			$first_post_uname4html = $first_poster_obj->getVar( 'uname' );
		}
	} else {
		$first_post_uname4html = $xoopsConfig['anonymous'];
	}
	// naao to

	// naao from
	// d3comment overridings
	$can_display = true;    //default

	if ( is_object( $d3com ) ) {
		$external_link_id = (int) $topic_row['topic_external_link_id'];
		if ( false === ( $external_link_id = $d3com->validate_id( $external_link_id ) ) && ! $isadminormod ) {
			$can_display = false;
		}
	}    // naao to

	// topics array
	if ( true === $can_display ) {
		// naao
		$topics[] = [
			'id'                        => $topic_row['topic_id'],
			'title'                     => $myts->makeTboxData4Show( $topic_row['topic_title'], $topic_row['fp_number_entity'], $topic_row['fp_special_entity'] ),
			'replies'                   => (int) $topic_row['topic_posts_count'] - 1,
			'views'                     => (int) $topic_row['topic_views'],
			'last_post_time'            => (int) $topic_row['topic_last_post_time'],
			'last_post_time_formatted'  => formatTimestamp( $topic_row['topic_last_post_time'], 'm' ),
			'last_post_id'              => (int) $topic_row['topic_last_post_id'],
			'last_post_icon'            => (int) $topic_row['lp_icon'],
			'last_post_text_raw'        => $topic_row['lp_post_text'],
			'last_post_subject'         => $myts->makeTboxData4Show( $topic_row['lp_subject'], $topic_row['lp_number_entity'], $topic_row['lp_special_entity'] ),
			'last_post_uid'             => (int) $topic_row['topic_last_uid'],
			'last_post_uname'           => $last_post_uname4html,
			'first_post_time'           => (int) $topic_row['topic_first_post_time'],
			'first_post_time_formatted' => formatTimestamp( $topic_row['topic_first_post_time'], 'm' ),
			'first_post_id'             => (int) $topic_row['topic_first_post_id'],
			'first_post_icon'           => (int) $topic_row['fp_icon'],
			'first_post_subject'        => $myts->makeTboxData4Show( $topic_row['fp_subject'], $topic_row['fp_number_entity'], $topic_row['fp_special_entity'] ),
			'first_post_uid'            => (int) $topic_row['topic_first_uid'],
			'first_post_uname'          => $first_post_uname4html,
			'bit_new'                   => $topic_row['topic_last_post_time'] > @$topic_row['u2t_time'] ? 1 : 0,
			'bit_hot'                   => $topic_row['topic_posts_count'] > $xoopsModuleConfig['hot_threshold'] ? 1 : 0,
			'locked'                    => (int) $topic_row['topic_locked'],
			'sticky'                    => (int) $topic_row['topic_sticky'],
			'solved'                    => (int) $topic_row['topic_solved'],
			'invisible'                 => (int) $topic_row['topic_invisible'],
			'u2t_time'                  => (int) @$topic_row['u2t_time'],
			'u2t_marked'                => (int) @$topic_row['u2t_marked'],
			'votes_count'               => (int) $topic_row['topic_votes_count'],
			'votes_sum'                 => (int) $topic_row['topic_votes_sum'],
			'external_link_id'          => (int) $topic_row['topic_external_link_id'],
			//naao
			'votes_avg'                 => round( $topic_row['topic_votes_sum'] / ( $topic_row['topic_votes_count'] - 0.0000001 ), 2 ),
			'last_post_gname'           => $myts->makeTboxData4Show( $topic_row['lp_guest_name'], $topic_row['lp_number_entity'], $topic_row['lp_special_entity'] ),
			//naao
			'first_post_gname'          => $myts->makeTboxData4Show( $topic_row['fp_guest_name'], $topic_row['lp_number_entity'], $topic_row['lp_special_entity'] ),
			//naao
		];
	}    // naao
}

// assign for block function
$GLOBALS[ 'D3forum_' . $mydirname ] = [
	'category' => $category4assign,
	'forum'    => $forum4assign
];

$xoopsOption['template_main'] = $mydirname . '_main_listtopics.html';

include XOOPS_ROOT_PATH . '/header.php';

unset( $xoops_breadcrumbs[ count( $xoops_breadcrumbs ) - 1 ]['url'] );

$xoopsTpl->assign(
	[
		'category'          => $category4assign,
		'forum'             => $forum4assign,
		'topics'            => $topics,
		'topic_hits'        => (int) $topic_hits,
		'odr_options'       => $odr_options,
		'solved_options'    => $solved_options,
		'query'             => $query4assign,
		'd3comment_info'    => $d3comment_info,
		'pagenav'           => $pagenav,
		'page'              => 'listtopics',
		'xoops_pagetitle'   => implode( ' - ', [ $forum4assign['title'], $xoopsModule->getVar( 'name' ) ] ),
		'xoops_breadcrumbs' => $xoops_breadcrumbs,
	]
);
// TODO
// u2t_marked
