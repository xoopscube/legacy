<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

include dirname( __DIR__ ) . '/include/common_prepend.php';

require_once dirname( __DIR__ ) . '/include/common_functions.php';

if ( ! isset( $_GET['submit'] ) ) {

	$results4assign = [];

} else {

	// naao from
	// get all forums
	$sql = 'SELECT forum_id, forum_external_link_format FROM ' . $db->prefix( $mydirname . '_forums' );

	$frs = $db->query( $sql );

	$d3com = [];

	while ( $forum_row = $db->fetchArray( $frs ) ) {

		// d3comment object
		$temp_forum_id = (int) $forum_row['forum_id'];

		if ( ! empty( $forum_row['forum_external_link_format'] ) ) {
			$d3com[ $temp_forum_id ] = d3forum_main_get_comment_object( $mydirname, $forum_row['forum_external_link_format'], $temp_forum_id );
		} else {
			$d3com[ $temp_forum_id ] = false;
		}
	}
	// naao to

	if ( ! empty( $_GET['keyword'] ) ) {

		if ( 'or' == @$_GET['andor'] ) {
			$andor4sql      = '|| ';
			$andor_selected = 'or';
		} else {
			$andor4sql      = '&& ';
			$andor_selected = 'and';
		}

		$keyword = $myts->stripSlashesGPC( $_GET['keyword'] );

		$keyword4disp = htmlspecialchars( $keyword, ENT_QUOTES );

		if ( defined( '_MD_D3FORUM_MULTIBYTESPACES' ) ) {
			$keyword = str_replace( explode( ',', _MD_D3FORUM_MULTIBYTESPACES ), ' ', $keyword );
		}

		$words = explode( ' ', $keyword );

		$whr_keyword = '';

		foreach ( $words as $word ) {

			$word4sql = addslashes( $word );

			switch ( @$_GET['target'] ) {
				default:
				case 'both':
					$whr_keyword     .= " (p.subject LIKE '%$word4sql%' OR p.post_text LIKE '%$word4sql%') $andor4sql";
					$target_selected = 'both';
					break;
				case 'subject':
					$whr_keyword     .= " (p.subject LIKE '%$word4sql%') $andor4sql";
					$target_selected = 'subject';
					break;
				case 'body':
					$whr_keyword     .= " (p.post_text LIKE '%$word4sql%') $andor4sql";
					$target_selected = 'body';
					break;
			}
		}

		$whr_keyword = substr( $whr_keyword, 0, - 3 );

	} else {
		$whr_keyword  = '1';
		$keyword4disp = '';
	}

	// forum_id
	$forum_id = (int) @$_GET['forum_id'];

	if ( ! empty( $forum_id ) ) {
		$whr_forum = "f.forum_id=$forum_id";
	} else {
		$whr_forum = '1';
	}

	// uname
	if ( ! empty( $_GET['search_username'] ) ) {
		$uname      = $myts->stripSlashesGPC( $_GET['search_username'] );
		$uname4disp = htmlspecialchars( $uname, ENT_QUOTES );
		$uname4sql  = addslashes( $uname );
		$whr_uname  = "u.uname='$uname4sql'";
	} else {
		$whr_uname  = '1';
		$uname4disp = '';
	}

	$allowed_sortbys = [
		'p.uid',
		'p.uid desc',
		'p.post_time',
		'p.post_time desc',
		't.topic_title',
		't.topic_title desc',
		't.topic_views',
		't.topic_views desc',
		't.topic_sticky',
		't.topic_sticky desc',
		't.topic_locked',
		't.topic_locked desc',
		't.topic_solved',
		't.topic_solved desc',
		't.topic_posts_count',
		't.topic_posts_count desc',
		'f.forum_id',
		'f.forum_id desc',
		'f.forum_title',
		'f.forum_title desc',
		'c.cat_id',
		'c.cat_id desc',
		'c.cat_title',
		'c.cat_title desc',
		'u.uname',
		'u.uname desc',
	];

	$sortby = in_array( @$_GET['sortby'], $allowed_sortbys ) ? $_GET['sortby'] : 'p.post_time desc';

	//$sql = 'SELECT u.uid,u.uname,p.post_id,p.subject,p.post_time,p.icon,LENGTH(p.post_text) AS body_length,p.votes_count,p.votes_sum,t.topic_id,t.topic_title,t.topic_views,t.topic_posts_count,f.forum_id,f.forum_title,c.cat_id,c.cat_title FROM '.$db->prefix($mydirname.'_posts').' p LEFT JOIN '.$db->prefix('users').' u ON p.uid=u.uid LEFT JOIN '.$db->prefix($mydirname.'_topics').' t ON p.topic_id = t.topic_id LEFT JOIN '.$db->prefix($mydirname.'_forums').' f ON t.forum_id = f.forum_id LEFT JOIN '.$db->prefix($mydirname.'_categories')." c ON f.cat_id = c.cat_id WHERE ($whr_keyword) AND ($whr_forum) AND ($whr_uname) AND ($whr_read4forum) AND ($whr_read4cat) ORDER BY $sortby" ;

	// naao mod
	$sql = 'SELECT u.uid,u.uname,u.name,p.post_id,p.subject,p.post_time,p.icon,LENGTH(p.post_text) AS body_length,p.votes_count,p.votes_sum,t.topic_id,t.topic_title,t.topic_views,t.topic_posts_count,t.topic_external_link_id,f.forum_id,f.forum_title,c.cat_id,c.cat_title FROM ' . $db->prefix( $mydirname . '_posts' ) . ' p LEFT JOIN ' . $db->prefix( 'users' ) . ' u ON p.uid=u.uid LEFT JOIN ' . $db->prefix( $mydirname . '_topics' ) . ' t ON p.topic_id = t.topic_id LEFT JOIN ' . $db->prefix( $mydirname . '_forums' ) . ' f ON t.forum_id = f.forum_id LEFT JOIN ' . $db->prefix( $mydirname . '_categories' ) . " c ON f.cat_id = c.cat_id WHERE ($whr_keyword) AND ($whr_forum) AND ($whr_uname) AND ($whr_read4forum) AND ($whr_read4cat) ORDER BY $sortby";

	// TODO :-)
	if ( ! $result = $db->query( $sql, 100, 0 ) ) {
		die( _MD_D3FORUM_ERR_SQL . __LINE__ );
	}

	$results4assign = [];

	$hits_count = $db->getRowsNum( $result );

	while ( $row = $db->fetchArray( $result ) ) {

		// naao from
		$can_display = true;    //default

		if ( is_object( $d3com[ (int) $row['forum_id'] ] ) ) {
			$d3com_obj        = $d3com[ (int) $row['forum_id'] ];
			$external_link_id = (int) $row['topic_external_link_id'];
			if ( false === ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) ) {
				$can_display = false;
			}
		}    // naao to

		if ( true == $can_display ) {
			// naao
			$results4assign[] = [
				                    'cat_title'           => $myts->makeTboxData4Show( $row['cat_title'] ),
				                    'cat_id'              => (int) $row['cat_id'],
				                    'forum_title'         => $myts->makeTboxData4Show( $row['forum_title'] ),
				                    'forum_id'            => (int) $row['forum_id'],
				                    'topic_title'         => $myts->makeTboxData4Show( $row['topic_title'] ),
				                    'topic_id'            => (int) $row['topic_id'],
				                    'topic_replies'       => $row['topic_posts_count'] - 1,
				                    'topic_views'         => (int) $row['topic_views'],
				                    'post_id'             => (int) $row['post_id'],
				                    'subject'             => $myts->makeTboxData4Show( $row['subject'] ),
				                    'icon'                => (int) $row['icon'],
				                    'body_length'         => (int) $row['body_length'],
				                    'poster_uid'          => (int) $row['uid'],
				                    'poster_uname'        => $myts->makeTboxData4Show( $row['uname'] ),
				                    'poster_name'         => $myts->makeTboxData4Show( $row['name'] ),    //naao added
				                    'post_time'           => (int) $row['post_time'],
				                    'post_time_formatted' => formatTimestamp( $row['post_time'], 'm' ),
				                    'votes_avg'           => $row['votes_count'] ? $row['votes_sum'] / (double) $row['votes_count'] : 0,
			                    ] + $row;
		}    // naao
	}

}

$xoopsOption['template_main'] = $mydirname . '_main_search.html';

include XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign(
	[
		'mydirname'             => $mydirname,
		'mod_url'               => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'          => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'            => $xoopsModuleConfig,
		'hits_count'            => (int) @$hits_count,
		'keyword'               => @$keyword4disp,
		'andor_options'         => [ 'or' => _MD_D3FORUM_LABEL_SEARCHOR, 'and' => _MD_D3FORUM_LABEL_SEARCHAND ],
		'andor_selected'        => empty( $andor_selected ) ? 'or' : $andor_selected,
		'target_options'        => [
			'subject' => _MD_D3FORUM_SUBJECT,
			'body'    => _MD_D3FORUM_BODY,
			'both'    => _MD_D3FORUM_LABEL_TARGETBOTH
		],
		'target_selected'       => empty( $target_selected ) ? 'both' : $target_selected,
		'sortby_options'        => [
			'p.post_time desc' => _MD_D3FORUM_ON,
			't.topic_title'    => _MD_D3FORUM_TOPICTITLE,
			'f.forum_id'       => _MD_D3FORUM_FORUM,
			'u.uname'          => _MD_D3FORUM_POSTER,
		],
		'sortby_selected'       => empty( $sortby_selected ) ? 'p.post_time desc' : $sortby_selected,
		'uname'                 => @$uname4disp,
		'show_results'          => ! empty( $_GET['submit'] ),
		'results'               => $results4assign,
		'forum_jumpbox_options' => d3forum_make_jumpbox_options( $mydirname, $whr_read4cat, $whr_read4forum, @$forum_id ),
		'xoops_module_header'   => '<link rel="stylesheet" type="text/css" media="all" href="' . str_replace( '{mod_url}', XOOPS_URL . '/modules/' . $mydirname, $xoopsModuleConfig['css_uri'] ) . '">' . $xoopsTpl->get_template_vars( 'xoops_module_header' ),
		'xoops_pagetitle'       => _MD_D3FORUM_TITLE_SEARCH,
		'xoops_breadcrumbs'     => array_merge( $xoops_breadcrumbs, [ [ 'name' => _MD_D3FORUM_TITLE_SEARCH ] ] ),
	]
);

include XOOPS_ROOT_PATH . '/footer.php';
