<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

function d3forum_get_rssdata( $mydirname, $limit = 0, $offset = 0, $forum_id = 0, $cat_ids = [], $last_post = false, $_show_hidden_topic = null ) {
	// Settings
	$module_handler = xoops_gethandler( 'module' );

	$module = $module_handler->getByDirname( $mydirname );

	$config_handler = xoops_gethandler( 'config' );

	$configs = $config_handler->getConfigList( $module->mid() );

	if ( ! empty( $configs['rss_show_hidden'] ) ) {

		if ( empty( $configs['rss_hidden_title'] ) ) {
			$show_hidden_topic = true;
		} else {
			$show_hidden_topic = $configs['rss_hidden_title'];
		}
	} else {
		$show_hidden_topic = false;
	}

	// Get as guest
	$GLOBALS['xoopsUser'] = false;

	if ( empty( $cat_ids ) ) {
		// all topics in the module
		$whr_cat_ids = '';
	} else if ( 1 === count( $cat_ids ) ) {
		// topics under the specified category
		$whr_cat_ids = 'f.cat_id=' . $cat_ids[0];
	} else {
		// topics under categories separated with commma
		$whr_cat_ids = 'f.cat_id IN (' . implode( ',', $cat_ids ) . ')';
	}

	require_once dirname( __DIR__ ) . '/class/d3forum.textsanitizer.php';

	( method_exists( 'D3forumTextSanitizer', 'sGetInstance' ) and $myts = D3forumTextSanitizer::sGetInstance() ) || $myts = ( new D3forumTextSanitizer )->getInstance();

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$forum_id = ( $forum_id ) ? ' AND f.forum_id=' . (int) $forum_id : '';

	$cat_id = ( $whr_cat_ids ) ? ' AND ' . $whr_cat_ids : '';

	$last_post = ( $last_post ) ? ' AND t.topic_last_post_id = p.post_id' : '';

	require_once __DIR__ . '/common_functions.php';

	$whr_forum = 't.forum_id IN (' . implode( ',', d3forum_get_forums_can_read( $mydirname ) ) . ')';

	$sql = 'SELECT c.cat_title, f.forum_id, f.forum_title, p.post_id, p.topic_id, p.post_time, p.uid, p.subject, p.html, p.smiley, p.xcode, p.br, p.guest_name, t.topic_views, t.topic_posts_count, p.post_text, f.forum_external_link_format, t.topic_external_link_id FROM ' . $db->prefix( $mydirname . '_posts' ) . ' p LEFT JOIN ' . $db->prefix( $mydirname . '_topics' ) . ' t ON t.topic_id=p.topic_id LEFT JOIN ' . $db->prefix( $mydirname . '_forums' ) . ' f ON f.forum_id=t.forum_id LEFT JOIN ' . $db->prefix( $mydirname . '_categories' ) . ' c ON c.cat_id=f.cat_id WHERE (' . $whr_forum . ') AND ! topic_invisible' . $last_post . $forum_id . $cat_id . ' ORDER BY p.post_time DESC';

	$result = $db->query( $sql, $limit, $offset );

	$ret = [];

	$d3coms = [];

	while ( $row = $db->fetchArray( $result ) ) {
		$is_readable = true;
		if ( ! empty( $row['forum_external_link_format'] ) ) {
			require_once __DIR__ . '/main_functions.php';
			if ( ! isset( $d3coms[ $row['forum_id'] ] ) ) {
				$d3com = $d3coms[ $row['forum_id'] ] = d3forum_main_get_comment_object( $mydirname, $row['forum_external_link_format'], $row['forum_id'] );
			}
			$is_readable = $d3com->validate_id( $row['topic_external_link_id'] );
		}

		if ( true === $show_hidden_topic || false !== $is_readable ) {

			if ( false !== $is_readable ) {

				$html = $myts->displayTarea( $row['post_text'], $row['html'], $row['smiley'], $row['xcode'], 1, $row['br'] );

				// quote `]]>`
				$html = str_replace( ']]>', ']]&gt;', $html );

				// remove tags
				$html = preg_replace( '#<(script|form|embed|object).+?/\\1>#is', '', $html );

				$html = preg_replace( '#<(link|wbr).*?>#is', '', $html );

				// remove relative links
				$html = preg_replace( '#<a[^>]+href=(?!["\']?\w+://)[^>]+>(.*?)</a>#is', '$1', $html );

				// remove attrs
				$_reg = '/(<[^>]*)\s+(?:id|class|name|on[^=]+)=("|\').*?\\2([^>]*>)/s';
				while ( preg_match( $_reg, $html ) ) {
					$html = preg_replace( $_reg, '$1$3', $html );
				}
			} else {
				$html = '';
			}

			$row['description'] = trim( $html );

			$row['link']     = XOOPS_URL . '/modules/' . $mydirname . '/index.php?'
			                   . ( $last_post ?
					'topic_id=' . $row['topic_id'] . '#post_id' . $row['post_id'] :
					'post_id=' . $row['post_id'] );
			$row['cat_link'] = XOOPS_URL . '/modules/' . $mydirname . '/index.php?forum_id=' . $row['forum_id'];
			$ret[]           = $row;

		} else if ( $show_hidden_topic ) {

			$row['subject']     = $show_hidden_topic;
			$row['description'] = '';
			$row['link']        = XOOPS_URL . '/modules/' . $mydirname . '/index.php';
			$row['link']        = XOOPS_URL . '/modules/' . $mydirname . '/index.php?'
			                      . ( $last_post ?
					'topic_id=' . $row['topic_id'] . '#post_id' . $row['post_id'] :
					'post_id=' . $row['post_id'] );
			$row['cat_link']    = XOOPS_URL . '/modules/' . $mydirname . '/index.php?forum_id=' . $row['forum_id'];
			$ret[]              = $row;
		}
	}

	return $ret;

}

function d3forum_whatsnew_base( $mydirname, $limit = 0, $offset = 0 ) {
	foreach ( d3forum_get_rssdata( $mydirname, $limit, $offset, 0, 0, true, false ) as $row ) {
		$ret[] = [
			'link'        => $row['link'],
			'cat_link'    => $row['cat_link'],
			'title'       => $row['subject'],
			'cat_name'    => $row['forum_title'],
			'time'        => $row['post_time'],
			'hits'        => $row['topic_views'],
			'replies'     => $row['topic_posts_count'] - 1,
			'uid'         => $row['uid'],
			'id'          => $row['post_id'],
			'guest_name'  => $row['guest_name'],
			'description' => $row['description']
		];
	}

	return $ret;
}

if ( ! function_exists( 'd3forum_make_context' ) ) {

	function d3forum_make_context( $text, $words = [], $l = 191 ) {
		static $strcut = '';

		if ( ! $strcut ) {
			$strcut = create_function( '$a,$b,$c', ( function_exists( 'mb_strcut' ) ) ?
				'return mb_strcut($a,$b,$c);' :
				'return strcut($a,$b,$c);' );
		}

		$text = str_replace( [ '&lt;', '&gt;', '&amp;', '&quot;', '&#039;' ], [ '<', '>', '&', '"', "'" ], $text );

		if ( ! is_array( $words ) ) {
			$words = [];
		}

		$ret = '';

		$q_word = str_replace( ' ', '|', preg_quote( implode( ' ', $words ), '/' ) );

		$match = [];

		if ( preg_match( "/$q_word/i", $text, $match ) ) {

			$ret = ltrim( preg_replace( '/\s+/', ' ', $text ) );

			[ $pre, $aft ] = array_pad( preg_split( "/$q_word/i", $ret, 2 ), 2, '' );

			$m = (int) ( $l / 2 );

			$ret = ( strlen( $pre ) > $m ) ? '... ' : '';

			$ret .= $strcut( $pre, max( strlen( $pre ) - $m + 1, 0 ), $m ) . $match[0];

			$m = $l - strlen( $ret );

			$ret .= $strcut( $aft, 0, min( strlen( $aft ), $m ) );

			if ( strlen( $aft ) > $m ) {
				$ret .= ' ...';
			}
		}

		if ( ! $ret ) {
			$ret = $strcut( $text, 0, $l );
			$ret = preg_replace( '/&([^;]+)?$/', '', $ret );
		}

		return htmlspecialchars( $ret, ENT_NOQUOTES );
	}
}
