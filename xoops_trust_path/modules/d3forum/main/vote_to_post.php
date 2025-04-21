<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

include dirname( __DIR__ ) . '/include/common_prepend.php';

$post_id = (int) @$_GET['post_id'];

// get this "post" from given $post_id
$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_posts' ) . " WHERE post_id=$post_id";

if ( ! $prs = $db->query( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

if ( $db->getRowsNum( $prs ) <= 0 ) {
	die( _MD_D3FORUM_ERR_READPOST );
}

$post_row = $db->fetchArray( $prs );

$topic_id = (int) $post_row['topic_id'];

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname( __DIR__ ) . '/include/process_this_topic.inc.php';

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_forum.inc.php' ) {
	die( _MD_D3FORUM_ERR_READFORUM );
}

// get&check this category ($category4assign, $category_row), override options
if ( ! include dirname( __DIR__ ) . '/include/process_this_category.inc.php' ) {
	die( _MD_D3FORUM_ERR_READCATEGORY );
}

// get $post4assign
include dirname( __DIR__ ) . '/include/process_this_post.inc.php';

// check if "use_vote" is on
if ( empty( $post4assign['can_vote'] ) ) {
	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?post_id=$post_id", 0, _MD_D3FORUM_MSG_VOTEPERM );
	exit;
}

// avoid crawlers
if ( preg_match( '/(msnbot|Googlebot|Yahoo! Slurp)/i', @$_SERVER['HTTP_USER_AGENT'] ) ) {
	redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?post_id=$post_id", 0, _MD_D3FORUM_ERR_VOTEPERM );
	exit;
}

// get remote_ip
$vote_ip = @$_SERVER['REMOTE_ADDR'];
if ( ! $vote_ip ) {
	die( _MD_D3FORUM_ERR_VOTEINVALID . __LINE__ );
}

// branch users and guests
if ( $uid ) {
	$useridentity4select = "uid=$uid";
	$useridentity4insert = "vote_ip='" . addslashes( $vote_ip ) . "', uid=$uid";
} else {
	$useridentity4select = "vote_ip='" . addslashes( $vote_ip ) . "' AND uid=0 AND vote_time>" . ( time() - @$xoopsModuleConfig['guest_vote_interval'] );
	$useridentity4insert = "vote_ip='" . addslashes( $vote_ip ) . "', uid=0";
}

// get POINT and validation
$point4vote = (int) @$_GET['point'];

if ( $point4vote < 0 || $point4vote > 10 ) {
	die( _MD_D3FORUM_ERR_VOTEINVALID . __LINE__ );
}

// check double voting
$sql = 'SELECT COUNT(*) FROM ' . $db->prefix( $mydirname . '_post_votes' ) . " WHERE post_id=$post_id AND ($useridentity4select)";

if ( ! $result = $db->query( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

[ $count ] = $db->fetchRow( $result );

if ( $count > 0 ) {
	// delete previous post
	$sql = 'DELETE FROM ' . $db->prefix( $mydirname . '_post_votes' ) . " WHERE post_id=$post_id AND ($useridentity4select) LIMIT 1";
	if ( ! $db->queryF( $sql ) ) {
		die( _MD_D3FORUM_ERR_SQL . __LINE__ );
	}
}

// transaction stage
$sql = 'INSERT INTO ' . $db->prefix( $mydirname . '_post_votes' ) . " SET post_id=$post_id, vote_point=$point4vote, vote_time=UNIX_TIMESTAMP(), $useridentity4insert";

if ( ! $db->queryF( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

require_once dirname( __DIR__ ) . '/include/transact_functions.php';

d3forum_sync_post_votes( $mydirname, $post_id );

$allowed_identifiers = [ 'post_id', 'topic_id' ];

if ( in_array( @$_GET['ret_name'], $allowed_identifiers ) ) {
	$ret_request = $_GET['ret_name'] . '=' . (int) @$_GET['ret_val'];
} else {
	$ret_request = "post_id=$post_id";
}

redirect_header( XOOPS_URL . "/modules/$mydirname/index.php?$ret_request", 0, _MD_D3FORUM_MSG_VOTEACCEPTED );
exit;
