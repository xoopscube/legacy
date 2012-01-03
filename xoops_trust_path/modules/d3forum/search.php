<?php

eval( '

function '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return d3forum_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'd3forum_global_search_base' ) ) {

function d3forum_global_search_base( $mydirname , $keywords , $andor , $limit , $offset , $userid )
{


	$myts =& MyTextsanitizer::getInstance() ;
	$db =& Database::getInstance() ;

	$andor = strtoupper( $andor ) ;
	$userid = intval( $userid ) ;

	// naao from
	require_once dirname(__FILE__).'/include/main_functions.php' ;
	// get all forums
	$sql = "SELECT forum_id, forum_external_link_format FROM ".$db->prefix($mydirname."_forums") ;
	$frs = $db->query( $sql ) ;
	$d3com = array() ;
	while( $forum_row = $db->fetchArray( $frs ) ) {
		// d3comment object
		$temp_forum_id = intval($forum_row['forum_id']);
		if( ! empty( $forum_row['forum_external_link_format'] ) ) {
			$d3com[$temp_forum_id] =& d3forum_main_get_comment_object( $mydirname , $forum_row['forum_external_link_format'] ) ;
		} else {
			$d3com[$temp_forum_id] = false ;
		}
	}
	// naao to

	$charset = strtoupper(_CHARSET);

	// XOOPS Search module
	$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1 ;
	$select4con = $showcontext ? "p.post_text" : "'' AS post_text" ;

	require_once dirname(__FILE__).'/include/common_functions.php' ;
	$whr_forum = "t.forum_id IN (".implode(",",d3forum_get_forums_can_read( $mydirname )).")" ;

	$whr_uid = $userid > 0 ? "p.uid=$userid" : "1" ;

	$whr_query = $andor == 'OR' ? '0' : '1' ;
	if( is_array( $keywords ) ) {
		// I know this is not a right escaping, but I can't believe $keywords :-)
		$keywords = array_map('stripslashes', $keywords);
		foreach( $keywords as $word ) {
			$word4sql = addslashes($word);
			$word_or = array('p.subject LIKE \'%'.$word4sql.'%\' OR p.post_text LIKE \'%'.$word4sql.'%\'');
			if (($charset === 'UTF-8' || $charset === 'EUC-JP') && function_exists('mb_convert_kana')) {
				foreach(array('a', 'A', 'k', 'KV', 'h', 'HV', 'c', 'C') as $_op) {
					$_word = mb_convert_kana($word, $_op, $charset);
					if ($_word !== $word) {
						$word4sql = addslashes($_word);
						$word_or[] = 'p.subject LIKE \'%'.$word4sql.'%\' OR p.post_text LIKE \'%'.$word4sql.'%\'';
					}
				}
			}
			$word4sql = join(' OR ', $word_or);
			$whr_query .= $andor == 'EXACT' ? ' AND' : ' '.$andor ;
			$whr_query .= ' (' . $word4sql . ')' ;
		}
	}
	//$sql = "SELECT p.post_id,p.topic_id,p.post_time,p.uid,p.subject,p.html,p.smiley,p.xcode,p.br,$select4con FROM ".$db->prefix($mydirname."_posts")." p LEFT JOIN ".$db->prefix($mydirname."_topics")." t ON t.topic_id=p.topic_id WHERE ($whr_forum) AND ($whr_uid) AND ($whr_query) AND ! topic_invisible ORDER BY p.post_time DESC" ;

	//naao
	$sql = "SELECT p.post_id,p.topic_id,p.post_time,p.uid,p.subject,p.html,p.smiley,p.xcode,p.br,$select4con,t.topic_external_link_id,f.forum_id FROM ".$db->prefix($mydirname."_posts")." p LEFT JOIN ".$db->prefix($mydirname."_topics")." t ON t.topic_id=p.topic_id  LEFT JOIN ".$db->prefix($mydirname."_forums")." f ON t.forum_id = f.forum_id WHERE ($whr_forum) AND ($whr_uid) AND ($whr_query) AND ! topic_invisible ORDER BY p.post_time DESC" ;

	$result = $db->query( $sql , $limit , $offset ) ;
	$ret = array() ;
	$context = '' ;

	// nao-pon
	$make_context_func = function_exists( 'xoops_make_context' )? 'xoops_make_context' : (function_exists( 'search_make_context' )? 'search_make_context' : '');

 	while( list( $post_id , $topic_id , $post_time , $uid , $subject , $html , $smiley , $xcode , $br , $text , $external_link_id, $forum_id ) = $db->fetchRow( $result ) ) {

		// naao from
		$can_display = true;	//default
		if( is_object( $d3com[intval($forum_id)]) ) {
			$d3com_obj = $d3com[intval($forum_id)];
			if( ( $external_link_id = $d3com_obj->validate_id( $external_link_id ) ) === false ) {
				$can_display = false;
			}
		}
		if ($can_display == true) { // naao to
			// get context for module "search"

			// nao-pon
			//if( function_exists('search_make_context') && $showcontext ) {
			if( $make_context_func && $showcontext ) {
				if( function_exists( 'easiestml' ) ) $text = easiestml( $text ) ;
				$full_context = strip_tags( $myts->displayTarea( $text , $html , $smiley , $xcode , 1 , $br ) ) ;
				// nao-pon
				//$context = search_make_context( $full_context , $keywords ) ;
				$context = $make_context_func( $full_context , $keywords ) ;
			}

			$ret[] = array(
				'link' => "index.php?post_id=$post_id" ,
				'title' => htmlspecialchars( $subject, ENT_QUOTES ) ,
				'time' => $post_time ,
				'uid' => $uid ,
				'context' => $context ,
			) ;
		}	// naao
	}
	// for xoops search module
	$GLOBALS['md_search_flg_zenhan_support'] = true;

	return $ret;

}

}

?>