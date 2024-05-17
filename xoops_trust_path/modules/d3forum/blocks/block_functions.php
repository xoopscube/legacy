<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Naoki Sawada (aka Nao-pon)
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

function b_d3forum_list_forums_show( $options ) {
	$myts = null;
 $ret = [];
 global $xoopsUser;

	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0];

	$categories = empty( $options[1] ) ? [] : explode( ',', $options[1] );

	$this_template = empty( $options[2] ) ? 'db:' . $mydirname . '_block_list_forums.html' : trim( $options[2] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = MyTextSanitizer::sGetInstance() ) || $myts = ( new MyTextSanitizer )->getInstance();

	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$module_handler = xoops_gethandler( 'module' );
	$module         = $module_handler->getByDirname( $mydirname );
	$config_handler = xoops_gethandler( 'config' );
	$configs        = $config_handler->getConfigList( $module->mid() );

	// forums can be read by current viewer (check by forum_access)
	require_once dirname( __DIR__ ) . '/include/common_functions.php';

	$whr_forum = 'f.forum_id IN (' . implode( ',', d3forum_get_forums_can_read( $mydirname ) ) . ')';

	// categories
	if ( empty( $categories ) ) {
		$whr_categories    = '1';
		$categories4assign = '';
	} else {
		$categories        = array_map( 'intval', $categories );
		$whr_categories    = 'f.cat_id IN (' . implode( ',', $categories ) . ')';
		$categories4assign = implode( ',', $categories );
	}

	$sql = 'SELECT f.forum_id, f.forum_title, f.forum_last_post_time, f.forum_topics_count, f.forum_posts_count, c.cat_id, c.cat_title, c.cat_depth_in_tree FROM '
	       . $db->prefix( $mydirname . '_forums' ) . ' f LEFT JOIN '
	       . $db->prefix( $mydirname . '_categories' ) . " c ON f.cat_id=c.cat_id WHERE ($whr_forum) AND ($whr_categories) ORDER BY c.cat_order_in_tree,f.forum_weight";
//	var_dump( $sql ) ;

	if ( ! $result = $db->query( $sql ) ) {
		return [];
	}

	$constpref = '_MB_' . strtoupper( $mydirname );

	$block = [
		'mydirname'                 => $mydirname,
		'mod_url'                   => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'              => XOOPS_URL . '/modules/' . $mydirname . '/' . $configs['images_dir'],
		'mod_config'                => $configs,
		'categories'                => $categories4assign,
		'lang_forum'                => constant( $constpref . '_FORUM' ),
		'lang_lastpost'             => constant( $constpref . '_LASTPOST' ),
		'lang_lastupdated'          => constant( $constpref . '_LASTUPDATED' ),
		'lang_linktosearch'         => constant( $constpref . '_LINKTOSEARCH' ),
		'lang_linktolistcategories' => constant( $constpref . '_LINKTOLISTCATEGORIES' ),
		'lang_linktolistforums'     => constant( $constpref . '_LINKTOLISTFORUMS' ),
		'lang_linktolisttopics'     => constant( $constpref . '_LINKTOLISTTOPICS' ),
	];

	$cat4assign = [];

//	$prev_cat_id = 0 ;
	while ( $forum_row = $db->fetchArray( $result ) ) {
		$cat_id                                 = (int) $forum_row['cat_id'];
		$cat4assign[ $cat_id ]['id']            = (int) $forum_row['cat_id'];
		$cat4assign[ $cat_id ]['title']         = $myts->makeTboxData4Show( $forum_row['cat_title'] );
		$cat4assign[ $cat_id ]['depth_in_tree'] = (int) $forum_row['cat_depth_in_tree'];

		$cat4assign[ $cat_id ]['forums'][] = [
			'id'                       => (int) $forum_row['forum_id'],
			'title'                    => $myts->makeTboxData4Show( $forum_row['forum_title'] ),
			'topics_count'             => (int) $forum_row['forum_topics_count'],
			'posts_count'              => (int) $forum_row['forum_posts_count'],
			'last_post_time'           => (int) $forum_row['forum_last_post_time'],
			'last_post_time_formatted' => $forum_row['forum_last_post_time'] ? formatTimestamp( $forum_row['forum_last_post_time'] ) : '',
		];
	}
	$block['categories'] = $cat4assign;

	if ( empty( $options['disable_renderer'] ) ) {

		require_once XOOPS_ROOT_PATH . '/class/template.php';
		$tpl = new XoopsTpl();

		$tpl->assign( 'block', $block );

		$ret['content'] = $tpl->fetch( $this_template );

		return $ret;
	}

	return $block;
}


function b_d3forum_list_forums_edit( $options ) {
	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0];

	$categories = empty( $options[1] ) ? [] : explode( ',', $options[1] );

	$this_template = empty( $options[2] ) ? 'db:' . $mydirname . '_block_list_forums.html' : trim( $options[2] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	foreach ( $categories as $i => $iValue ) {
		$categories[ $i ] = (int) $iValue;
	}

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname'>
		<label for='categories'>" . _MB_D3FORUM_CATLIMIT . "</label>&nbsp;:
		<input type='text' size='20' name='options[1]' id='categories' value='" . implode( ',', $categories ) . "'>" . _MB_D3FORUM_CATLIMITDSC . "
		<br>
		<label for='this_template'>" . _MB_D3FORUM_THISTEMPLATE . "</label>&nbsp;:
		<input type='text' size='60' name='options[2]' id='this_template' value='" . htmlspecialchars( $this_template, ENT_QUOTES ) . "'>
		<br>
	\n";

	return $form;
}


function b_d3forum_list_topics_show( $options ) {
	$myts = null;
 $ret = [];
 global $xoopsUser;

	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0];

	$max_topics = empty( $options[1] ) ? 10 : (int) $options[1];

	$show_fullsize = ! empty( $options[2] );

	$now_order = empty( $options[3] ) ? 'time' : trim( $options[3] );

	$is_markup = ! empty( $options[4] );

	$categories = empty( $options[5] ) ? [] : explode( ',', $options[5] );

	$forums = empty( $options[7] ) ? [] : explode( ',', $options[7] );

	$this_template = empty( $options[6] ) ? 'db:' . $mydirname . '_block_list_topics.html' : trim( $options[6] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = MyTextSanitizer::sGetInstance() ) || $myts = ( new MyTextSanitizer )->getInstance();

	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$module_handler = xoops_gethandler( 'module' );
	$module         = $module_handler->getByDirname( $mydirname );
	$config_handler = xoops_gethandler( 'config' );
	$configs        = $config_handler->getConfigList( $module->mid() );

	// naao from
	// get all forums
	$sql = 'SELECT forum_id, forum_external_link_format FROM ' . $db->prefix( $mydirname . '_forums' );

	$frs = $db->query( $sql );

	$d3com = [];

	while ( $forum_row = $db->fetchArray( $frs ) ) {
		// d3comment object
		$temp_forum_id = (int) $forum_row['forum_id'];
		if ( ! empty( $forum_row['forum_external_link_format'] ) ) {
			$d3com[ $temp_forum_id ] = d3forum_b_get_comment_object( $mydirname, $forum_row['forum_external_link_format'], $temp_forum_id );
		} else {
			$d3com[ $temp_forum_id ] = false;
		}
	}    // naao to

	// allow markup or not
	if ( empty( $configs['allow_mark'] ) ) {
		$is_markup = false;
	}

	// use solved or not
	if ( empty( $configs['use_solved'] ) ) {
		$sel_solved = '1 AS topic_solved';
	} else {
		$sel_solved = 't.topic_solved';
	}

	// order
	$whr_order = '1';

	switch ( $now_order ) {
		case 'views':
			$odr = 't.topic_views DESC';
			break;
		case 'replies':
			$odr = 't.topic_posts_count DESC';
			break;
		case 'votes':
			$odr = 't.topic_votes_count DESC';
			break;
		case 'points':
			$odr = 't.topic_votes_sum DESC';
			break;
		case 'average':
			$odr       = 't.topic_votes_sum/t.topic_votes_count DESC, topic_votes_count DESC';
			$whr_order = 't.topic_votes_count>0';
			break;
		case 'time':
		default:
			$odr = 't.topic_last_post_time DESC';
			break;
	}

	// forums can be read by current viewer (check by forum_access)
	require_once dirname( __DIR__ ) . '/include/common_functions.php';

	$whr_forum = 't.forum_id IN (' . implode( ',', d3forum_get_forums_can_read( $mydirname ) ) . ')';

	// check option "auto" by nao-pon
	$_hasAuto = false;

	$_globalKey = 'D3forum_' . $mydirname;

	$categories = array_map( 'b_d3forum_check_limits', $categories );

	if ( $categories && false !== ( $_key = array_search( 'auto', $categories, true ) ) ) {

		$_hasAuto = true;

		if ( isset( $GLOBALS[ $_globalKey ]['category'] ) ) {
			$categories[ $_key ] = $GLOBALS[ $_globalKey ]['category']['id'];
		} else {
			unset( $categories[ $_key ] );
		}
	}

	$forums = array_map( 'b_d3forum_check_limits', $forums );

	if ( $forums && false !== ( $_key = array_search( 'auto', $forums, true ) ) ) {

		$_hasAuto = true;

		if ( isset( $GLOBALS[ $_globalKey ]['forum'] ) ) {
			$forums[ $_key ] = $GLOBALS[ $_globalKey ]['forum']['id'];
		} else {
			unset( $forums[ $_key ] );
		}
	}
	if ( $_hasAuto && ! $categories && ! $forums ) {
		// disables this block
		return false;
	}

	// topic ( with out current topic )
	$current_topic_id = ( isset( $GLOBALS[ $_globalKey ]['topic'] ) ) ? (int) $GLOBALS[ $_globalKey ]['topic']['id'] : 0;

	$whr_topic = '1';

	if ( isset( $GLOBALS[ $_globalKey ]['topic'] ) ) {
		//$whr_topic = 't.topic_id != '.intval($GLOBALS[$_globalKey]['topic']['id']);
	}

	// categories
	$categories4assign = implode( ',', $categories );

	$whr_categories = empty( $categories ) ? '1' : 'f.cat_id IN (' . implode( ',', $categories ) . ')';

	// forums
	$forums4assign = implode( ',', $forums );

	$whr_forums = empty( $forums ) ? '1' : 'f.forum_id IN (' . implode( ',', $forums ) . ')';

	// naao from
	if ( $uid > 0 && $is_markup ) {

		$sql = "SELECT t.topic_id, t.topic_title, t.topic_last_uid, t.topic_last_post_id, t.topic_last_post_time,
			t.topic_views, t.topic_votes_count, t.topic_votes_sum, t.topic_posts_count, t.topic_external_link_id,
			$sel_solved, t.forum_id,
			p.post_id, p.subject, p.post_text, p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path,
			f.forum_title, u2t.u2t_marked FROM "
		       . $db->prefix( $mydirname . '_topics' ) . ' t LEFT JOIN '
		       . $db->prefix( $mydirname . '_forums' ) . ' f ON f.forum_id=t.forum_id LEFT JOIN '
		       . $db->prefix( $mydirname . '_posts' ) . ' p ON t.topic_last_post_id=p.post_id LEFT JOIN '
		       . $db->prefix( $mydirname . '_users2topics' ) . " u2t ON u2t.topic_id=t.topic_id AND u2t.uid=$uid
			WHERE ! t.topic_invisible AND ($whr_forum) AND ($whr_categories) AND ($whr_forums)
			AND ($whr_topic) AND ($whr_order) ORDER BY u2t.u2t_marked<=>1 DESC , $odr";
	} else {
		$sql = "SELECT t.topic_id, t.topic_title, t.topic_last_uid, t.topic_last_post_id, t.topic_last_post_time,
			t.topic_views, t.topic_votes_count, t.topic_votes_sum, t.topic_posts_count, t.topic_external_link_id,
			$sel_solved, t.forum_id,
			p.post_id, p.subject, p.post_text, p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path,
			f.forum_title, 0 AS u2t_marked FROM "
		       . $db->prefix( $mydirname . '_topics' ) . ' t LEFT JOIN '
		       . $db->prefix( $mydirname . '_forums' ) . ' f ON f.forum_id=t.forum_id LEFT JOIN '
		       . $db->prefix( $mydirname . '_posts' ) . " p ON t.topic_last_post_id=p.post_id
			 WHERE ! t.topic_invisible AND ($whr_forum) AND ($whr_categories) AND ($whr_forums)
			 AND ($whr_topic) AND ($whr_order) ORDER BY $odr";
	}
	// naao to
	//	var_dump( $sql ) ;

	if ( ! $result = $db->query( $sql, $max_topics, 0 ) ) {
		return [];
	}

	$constpref = '_MB_' . strtoupper( $mydirname );

	$block = [
		'mydirname'                 => $mydirname,
		'mod_url'                   => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'              => XOOPS_URL . '/modules/' . $mydirname . '/' . $configs['images_dir'],
		'mod_config'                => $configs,
		'categories'                => $categories4assign,
		'forums'                    => $forums4assign,
		'full_view'                 => $show_fullsize,
		'current_topic_id'          => $current_topic_id,
		'lang_forum'                => constant( $constpref . '_FORUM' ),
		'lang_topic'                => constant( $constpref . '_TOPIC' ),
		'lang_replies'              => constant( $constpref . '_REPLIES' ),
		'lang_views'                => constant( $constpref . '_VIEWS' ),
		'lang_votescount'           => constant( $constpref . '_VOTESCOUNT' ),
		'lang_votessum'             => constant( $constpref . '_VOTESSUM' ),
		'lang_lastpost'             => constant( $constpref . '_LASTPOST' ),
		'lang_linktosearch'         => constant( $constpref . '_LINKTOSEARCH' ),
		'lang_linktolistcategories' => constant( $constpref . '_LINKTOLISTCATEGORIES' ),
		'lang_linktolistforums'     => constant( $constpref . '_LINKTOLISTFORUMS' ),
		'lang_linktolisttopics'     => constant( $constpref . '_LINKTOLISTTOPICS' ),
		'lang_alt_unsolved'         => constant( $constpref . '_ALT_UNSOLVED' ),
		'lang_alt_marked'           => constant( $constpref . '_ALT_MARKED' ),
	];

	while ( $topic_row = $db->fetchArray( $result ) ) {
		// naao from
		// d3comment overridings
		$can_display = true;    //default

		if ( is_object( $d3com[ (int) $topic_row['forum_id'] ] ) ) {
			$d3com_obj        = $d3com[ (int) $topic_row['forum_id'] ];
			$external_link_id = (int) $topic_row['topic_external_link_id'];
			if ( false === ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) ) {
				$can_display = false;
			}
		}    // naao to

		if ( true === $can_display ) {    // naao

			$topic4assign      = [
				'id'                       => (int) $topic_row['topic_id'],
				'title'                    => $myts->makeTboxData4Show( $topic_row['topic_title'] ),
				'forum_id'                 => (int) $topic_row['forum_id'],
				'forum_title'              => $myts->makeTboxData4Show( $topic_row['forum_title'] ),
				'replies'                  => $topic_row['topic_posts_count'] - 1,
				'views'                    => (int) $topic_row['topic_views'],
				'votes_count'              => $topic_row['topic_votes_count'],
				'votes_sum'                => (int) $topic_row['topic_votes_sum'],
				'last_post_id'             => (int) $topic_row['topic_last_post_id'],
				'last_post_time'           => (int) $topic_row['topic_last_post_time'],
				'last_post_time_formatted' => formatTimestamp( $topic_row['topic_last_post_time'], 'm' ),
				'last_uid'                 => (int) $topic_row['topic_last_uid'],
				'last_uname'               => XoopsUser::getUnameFromId( $topic_row['topic_last_uid'], $configs['use_name'] ),
				//naao usereal=1
				'solved'                   => (int) $topic_row['topic_solved'],
				'u2t_marked'               => (int) $topic_row['u2t_marked'],
				'external_link_id'         => (int) $topic_row['topic_external_link_id'],
				//naao
				'post_text'                => strip_tags( $myts->displayTarea( strip_tags( $topic_row['post_text'] ), $topic_row['html'], $topic_row['smiley'], $topic_row['xcode'], 1, $topic_row['br'] ) ),
				//naao
				'guest_name'               => htmlspecialchars( $topic_row['guest_name'] ),
				//naao
			];
			$block['topics'][] = $topic4assign;
		}    // naao
	}

	if ( empty( $options['disable_renderer'] ) ) {

		require_once XOOPS_ROOT_PATH . '/class/template.php';

		$tpl = new XoopsTpl();

		$tpl->assign( 'block', $block );

		$ret['content'] = $tpl->fetch( $this_template );

		return $ret;
	}

	return $block;
}


function b_d3forum_list_topics_edit( $options ) {
	$mydirname = empty( $options[0] ) ? $GLOBALS['mydirname'] : $options[0];

	$max_topics = empty( $options[1] ) ? 10 : (int) $options[1];

	$show_fullsize = empty( $options[2] ) ? false : true;

	$now_order = empty( $options[3] ) ? 'time' : trim( $options[3] );

	$is_markup = empty( $options[4] ) ? false : true;

	$categories = empty( $options[5] ) ? [] : explode( ',', $options[5] );

	$forums = empty( $options[7] ) ? [] : explode( ',', $options[7] );

	$this_template = empty( $options[6] ) ? 'db:' . $mydirname . '_block_list_topics.html' : trim( $options[6] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	if ( $show_fullsize ) {
		$fullyes_checked = "checked='checked'";
		$fullno_checked  = '';
	} else {
		$fullno_checked  = "checked='checked'";
		$fullyes_checked = '';
	}

	if ( $is_markup ) {
		$markupyes_checked = "checked='checked'";
		$markupno_checked  = '';
	} else {
		$markupno_checked  = "checked='checked'";
		$markupyes_checked = '';
	}

	$categories = array_map( 'b_d3forum_check_limits', $categories );

	$forums = array_map( 'b_d3forum_check_limits', $forums );

	$orders = [
		'time'    => _MB_D3FORUM_ORDERTIMED,
		'views'   => _MB_D3FORUM_ORDERVIEWSD,
		'replies' => _MB_D3FORUM_ORDERREPLIESD,
		'votes'   => _MB_D3FORUM_ORDERVOTESD,
		'points'  => _MB_D3FORUM_ORDERPOINTSD,
		'average' => _MB_D3FORUM_ORDERAVERAGED,
	];

	$order_options = '';

	foreach ( $orders as $order_value => $order_name ) {
		$selected      = $order_value === $now_order ? "selected='selected'" : '';
		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n";
	}

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname'>
		<label for='o1'>" . sprintf( _MB_D3FORUM_DISPLAY, "</label>
        <input type='text' size='4' name='options[1]' id='o1' value='$max_topics' style='text-align:right;'>" ) . '
		<br>
		' . _MB_D3FORUM_DISPLAYF . "&nbsp;:
		<input type='radio' name='options[2]' id='o21' value='1' $fullyes_checked><label for='o21'>" . _YES . "</label>
		<input type='radio' name='options[2]' id='o20' value='0' $fullno_checked><label for='o20'>" . _NO . "</label>
		<br>
		<label for='orderrule'>" . _MB_D3FORUM_ORDERRULE . "</label>&nbsp;:
		<select name='options[3]' id='orderrule'>
			$order_options
		</select>
		<br>
		" . _MB_D3FORUM_MARKISUP . "&nbsp;:
		<input type='radio' name='options[4]' id='markupyes' value='1' $markupyes_checked><label for='markupyes'>" . _YES . "</label>
		<input type='radio' name='options[4]' id='markupno' value='0' $markupno_checked><label for='markupno'>" . _NO . "</label>
		<br>
		<label for='categories'>" . _MB_D3FORUM_CATLIMIT . "</label>&nbsp;:
		<input type='text' size='20' name='options[5]' id='categories' value='" . implode( ',', $categories ) . "'>" . _MB_D3FORUM_CATLIMITDSC . "
		<br>
		<label for='this_template'>" . _MB_D3FORUM_THISTEMPLATE . "</label>&nbsp;:
		<input type='text' size='60' name='options[6]' id='this_template' value='" . htmlspecialchars( $this_template, ENT_QUOTES ) . "'>
		<br>
		<label for='forums'>" . _MB_D3FORUM_FORUMLIMIT . "</label>&nbsp;:
		<input type='text' size='20' name='options[7]' id='forums' value='" . implode( ',', $forums ) . "'>" . _MB_D3FORUM_FORUMLIMITDSC . "
		<br>
	\n";

	return $form;
}


function b_d3forum_list_posts_show( $options ) {
	$myts = null;
 $ret = [];
 global $xoopsUser;

	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0];

	$max_posts = empty( $options[1] ) ? 10 : (int) $options[1];

	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] );

	$categories = empty( $options[3] ) ? [] : explode( ',', $options[3] );

	$forums = empty( $options[5] ) ? [] : explode( ',', $options[5] );

	$this_template = empty( $options[4] ) ? 'db:' . $mydirname . '_block_list_posts.html' : trim( $options[4] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = MyTextSanitizer::sGetInstance() ) || $myts = ( new MyTextSanitizer )->getInstance();

	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$module_handler = xoops_gethandler( 'module' );
	$module         = $module_handler->getByDirname( $mydirname );
	$config_handler = xoops_gethandler( 'config' );
	$configs        = $config_handler->getConfigList( $module->mid() );

	// naao from
	// get all forums
	$sql = 'SELECT forum_id, forum_external_link_format FROM ' . $db->prefix( $mydirname . '_forums' );

	$frs = $db->query( $sql );

	$d3com = [];

	while ( $forum_row = $db->fetchArray( $frs ) ) {
		// d3comment object
		$temp_forum_id = (int) $forum_row['forum_id'];

		if ( ! empty( $forum_row['forum_external_link_format'] ) ) {
			$d3com[ $temp_forum_id ] = d3forum_b_get_comment_object( $mydirname, $forum_row['forum_external_link_format'], $temp_forum_id );
		} else {
			$d3com[ $temp_forum_id ] = false;
		}
	}    // naao to

	// order
	$whr_order = '1';

	switch ( $now_order ) {
		case 'votes':
			$odr = 'p.votes_count DESC';
			break;
		case 'points':
			$odr = 'p.votes_sum DESC';
			break;
		case 'average':
			$odr       = 'p.votes_sum/p.votes_count DESC, p.votes_count DESC';
			$whr_order = 'p.votes_count>0';
			break;
		case 'time':
		default:
			$odr = 'p.post_time DESC';
			break;
	}

	// forums can be read by current viewer (check by forum_access)
	require_once dirname( __DIR__ ) . '/include/common_functions.php';

	$whr_forum = 't.forum_id IN (' . implode( ',', d3forum_get_forums_can_read( $mydirname ) ) . ')';

	// check option "auto" by nao-pon
	$_hasAuto = false;

	$_globalKey = 'D3forum_' . $mydirname;

	$categories = array_map( 'b_d3forum_check_limits', $categories );

	if ( $categories && false !== ( $_key = array_search( 'auto', $categories, true ) ) ) {

		$_hasAuto = true;

		if ( isset( $GLOBALS[ $_globalKey ]['category'] ) ) {
			$categories[ $_key ] = $GLOBALS[ $_globalKey ]['category']['id'];
		} else {
			unset( $categories[ $_key ] );
		}
	}

	$forums = array_map( 'b_d3forum_check_limits', $forums );

	if ( $forums && false !== ( $_key = array_search( 'auto', $forums, true ) ) ) {

		$_hasAuto = true;

		if ( isset( $GLOBALS[ $_globalKey ]['forum'] ) ) {
			$forums[ $_key ] = $GLOBALS[ $_globalKey ]['forum']['id'];
		} else {
			unset( $forums[ $_key ] );
		}
	}

	if ( $_hasAuto && ! $categories && ! $forums ) {
		// disables this block
		return false;
	}

	// categories
	$categories4assign = implode( ',', $categories );

	$whr_categories = empty( $categories ) ? '1' : 'f.cat_id IN (' . implode( ',', $categories ) . ')';

	// forums
	$forums4assign = implode( ',', $forums );

	$whr_forums = empty( $forums ) ? '1' : 'f.forum_id IN (' . implode( ',', $forums ) . ')';

	// naao
	$sql = 'SELECT p.post_id, p.subject, p.votes_sum, p.votes_count, p.post_time, p.post_text, p.uid,
		p.guest_name, p.html, p.smiley, p.xcode, p.br, p.unique_path,
		f.forum_id, f.forum_title, t.topic_external_link_id FROM '
	       . $db->prefix( $mydirname . '_posts' ) . ' p LEFT JOIN '
	       . $db->prefix( $mydirname . '_topics' ) . ' t ON p.topic_id=t.topic_id LEFT JOIN '
	       . $db->prefix( $mydirname . '_forums' ) . " f ON f.forum_id=t.forum_id
		WHERE ! t.topic_invisible AND ($whr_forum) AND ($whr_categories) AND ($whr_forums)
		AND ($whr_order) ORDER BY $odr";

//	var_dump( $sql ) ;

	if ( ! $result = $db->query( $sql, $max_posts, 0 ) ) {
		return [];
	}

	$constpref = '_MB_' . strtoupper( $mydirname );

	$block = [
		'mydirname'                 => $mydirname,
		'mod_url'                   => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'              => XOOPS_URL . '/modules/' . $mydirname . '/' . $configs['images_dir'],
		'mod_config'                => $configs,
		'categories'                => $categories4assign,
		'forums'                    => $forums4assign,
		'lang_forum'                => constant( $constpref . '_FORUM' ),
		'lang_topic'                => constant( $constpref . '_TOPIC' ),
		'lang_replies'              => constant( $constpref . '_REPLIES' ),
		'lang_views'                => constant( $constpref . '_VIEWS' ),
		'lang_votescount'           => constant( $constpref . '_VOTESCOUNT' ),
		'lang_votessum'             => constant( $constpref . '_VOTESSUM' ),
		'lang_lastpost'             => constant( $constpref . '_LASTPOST' ),
		'lang_linktosearch'         => constant( $constpref . '_LINKTOSEARCH' ),
		'lang_linktolistcategories' => constant( $constpref . '_LINKTOLISTCATEGORIES' ),
		'lang_linktolistforums'     => constant( $constpref . '_LINKTOLISTFORUMS' ),
		'lang_linktolisttopics'     => constant( $constpref . '_LINKTOLISTTOPICS' ),
	];

	while ( $post_row = $db->fetchArray( $result ) ) {
		// naao from
		// d3comment overridings
		$can_display = true;    //default

		if ( is_object( $d3com[ (int) $post_row['forum_id'] ] ) ) {

			$d3com_obj = $d3com[ (int) $post_row['forum_id'] ];

			$external_link_id = (int) $post_row['topic_external_link_id'];

			if ( false === ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) ) {
				$can_display = false;
			}
		} // naao to

		if ( true === $can_display ) {
			// naao
			$post4assign = [
				'id'                  => (int) $post_row['post_id'],
				'subject'             => $myts->makeTboxData4Show( $post_row['subject'] ),
				'forum_id'            => (int) $post_row['forum_id'],
				'forum_title'         => $myts->makeTboxData4Show( $post_row['forum_title'] ),
				'votes_count'         => $post_row['votes_count'],
				'votes_sum'           => (int) $post_row['votes_sum'],
				'post_time'           => (int) $post_row['post_time'],
				'post_time_formatted' => formatTimestamp( $post_row['post_time'], 'm' ),
				'uid'                 => (int) $post_row['uid'],
				'uname'               => XoopsUser::getUnameFromId( $post_row['uid'], $configs['use_name'] ),
				//naao usereal=1
				'external_link_id'    => (int) $post_row['topic_external_link_id'],
				//naao
				'post_text'           => strip_tags( $myts->displayTarea( strip_tags( $post_row['post_text'] ), $post_row['html'], $post_row['smiley'], $post_row['xcode'], 1, $post_row['br'] ) ),
				//naao
				'guest_name'          => htmlspecialchars( $post_row['guest_name'] ),
				//naao
			];

			$block['posts'][] = $post4assign;
		}    //naao
	}

	if ( empty( $options['disable_renderer'] ) ) {

		require_once XOOPS_ROOT_PATH . '/class/template.php';

		$tpl = new XoopsTpl();

		$tpl->assign( 'block', $block );

		$ret['content'] = $tpl->fetch( $this_template );

		return $ret;
	}

	return $block;
}


function b_d3forum_list_posts_edit( $options ) {
	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0];

	$max_posts = empty( $options[1] ) ? 10 : (int) $options[1];

	$now_order = empty( $options[2] ) ? 'time' : trim( $options[2] );

	$categories = empty( $options[3] ) ? [] : explode( ',', $options[3] );

	$forums = empty( $options[5] ) ? [] : explode( ',', $options[5] );

	$this_template = empty( $options[4] ) ? 'db:' . $mydirname . '_block_list_posts.html' : trim( $options[4] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	$categories = array_map( 'b_d3forum_check_limits', $categories );

	$forums = array_map( 'b_d3forum_check_limits', $forums );

	$orders = [
		'time'    => _MB_D3FORUM_ORDERTIMED,
		'votes'   => _MB_D3FORUM_ORDERVOTESD,
		'points'  => _MB_D3FORUM_ORDERPOINTSD,
		'average' => _MB_D3FORUM_ORDERAVERAGED,
	];

	$order_options = '';

	foreach ( $orders as $order_value => $order_name ) {

		$selected = $order_value == $now_order ? "selected='selected'" : '';

		$order_options .= "<option value='$order_value' $selected>$order_name</option>\n";
	}

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname'>
		<label for='o1'>" . sprintf( _MB_D3FORUM_DISPLAY, "</label><input type='text' size='4' name='options[1]' id='o1' value='$max_posts' style='text-align:right;'>" ) . "
		<br>
		<label for='orderrule'>" . _MB_D3FORUM_ORDERRULE . "</label>&nbsp;:
		<select name='options[2]' id='orderrule'>
			$order_options
		</select>
		<br>
		<label for='categories'>" . _MB_D3FORUM_CATLIMIT . "</label>&nbsp;:
		<input type='text' size='20' name='options[3]' id='categories' value='" . implode( ',', $categories ) . "'>" . _MB_D3FORUM_CATLIMITDSC . "
		<br>
		<label for='this_template'>" . _MB_D3FORUM_THISTEMPLATE . "</label>&nbsp;:
		<input type='text' size='60' name='options[4]' id='this_template' value='" . htmlspecialchars( $this_template, ENT_QUOTES ) . "'>
		<br>
		<label for='forums'>" . _MB_D3FORUM_FORUMLIMIT . "</label>&nbsp;:
		<input type='text' size='20' name='options[5]' id='forums' value='" . implode( ',', $forums ) . "'>" . _MB_D3FORUM_FORUMLIMITDSC . "
		<br>
	\n";

	return $form;
}


function b_d3forum_check_limits( $var ) {
	return ( 'auto' !== strtolower( trim( $var ) ) ) ? (int) $var : 'auto';
}

// get object for comment integration
if ( ! function_exists( 'd3forum_b_get_comment_object' ) ) {

	function d3forum_b_get_comment_object( $mydirname, $external_link_format, $forum_id = null ) {
		include_once dirname( __DIR__ ) . '/class/D3commentAbstract.class.php';

		[ $external_dirname, $classname, $external_trustdirname ] = preg_split( '/::/', $external_link_format . '::::::');

		if ( empty( $classname ) ) {
			$obj = new D3commentAbstract( $mydirname, '' );
			if ( ! empty( $forum_id ) ) {
				$obj->setForumId( $forum_id );
			}

			return $obj;
		}

		// search the class file
		$class_bases = [
			XOOPS_ROOT_PATH . '/modules/' . $external_dirname . '/class',
			XOOPS_TRUST_PATH . '/modules/' . $external_trustdirname . '/class',
			XOOPS_TRUST_PATH . '/modules/d3forum/class',
		];

		foreach ( $class_bases as $class_base ) {

			if ( file_exists( $class_base . '/' . $classname . '.class.php' ) ) {

				require_once $class_base . '/' . $classname . '.class.php';

				break;
			}
		}

		// check the class
		if ( ! $classname || ! class_exists( $classname ) ) {

			$obj = new D3commentAbstract( $mydirname, $external_dirname );

			if ( ! empty( $forum_id ) ) {
				$obj->setForumId( $forum_id );
			}

			return $obj;
		}

		$obj = new $classname( $mydirname, $external_dirname, $external_trustdirname );

		if ( ! empty( $forum_id ) ) {
			$obj->setForumId( $forum_id );
		}

		return $obj;
	}
}
