<?php

// count total topics
$sql = 'SELECT COUNT(*) FROM '.$db->prefix($mydirname.'_topics') ;
list( $total_topics_count ) = $db->fetchRow( $db->query( $sql ) ) ;

// count total posts
$sql = 'SELECT COUNT(*) FROM '.$db->prefix($mydirname.'_posts') ;
list( $total_posts_count ) = $db->fetchRow( $db->query( $sql ) ) ;

// get last visit
if( $uid > 0 ) {
	$db =& Database::getInstance() ;
	$lv_result = $db->query( "SELECT MAX(u2t_time) FROM ".$db->prefix($mydirname.'_users2topics')." WHERE uid='$uid'" ) ;
	list( $last_visit ) = $db->fetchRow( $lv_result ) ;
}
if( empty( $last_visit ) ) $last_visit = time() ;

// categories loop
$categories4assign = array() ;
$previous_depth = -1 ;
$sql = "SELECT * FROM ".$db->prefix($mydirname."_categories")." c WHERE ($whr_read4cat) ORDER BY cat_order_in_tree" ;
if( ! $crs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
while( $cat_row = $db->fetchArray( $crs ) ) {

	$cat_id = intval( $cat_row['cat_id'] ) ;

	// forums loop
	$forums = array() ;
	$sql = "SELECT * FROM ".$db->prefix($mydirname."_forums")." f WHERE f.cat_id=".$cat_id." AND ($whr_read4forum) ORDER BY f.forum_weight, f.forum_id" ;
	if( ! $frs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
	while( $forum_row = $db->fetchArray( $frs ) ) {

		$forum_id = intval( $forum_row['forum_id'] ) ;

		// get last visit each forums
		if( $uid > 0 ) {
			$sql = 'SELECT u2t.u2t_time FROM '.$db->prefix($mydirname.'_posts').' p LEFT JOIN '.$db->prefix($mydirname.'_users2topics').' u2t ON u2t.topic_id=p.topic_id WHERE p.post_id='.intval($forum_row['forum_last_post_id']).' AND u2t.uid='.$uid ;
			list( $u2t_time ) = $db->fetchRow( $db->query( $sql ) ) ;
		}
		if( empty( $u2t_time ) ) $u2t_time = 0 ;

		// forums array
		$forums[] = array(
			'id' => $forum_row['forum_id'] ,
			'title' => $myts->makeTboxData4Show( $forum_row['forum_title'] ) ,
			'desc' => $myts->displayTarea( $forum_row['forum_desc'] ) ,
			'topics_count' => intval( $forum_row['forum_topics_count'] ) ,
			'posts_count' => intval( $forum_row['forum_posts_count'] ) ,
			'last_post_time' => intval( $forum_row['forum_last_post_time'] ) ,
			'last_post_time_formatted' => formatTimestamp( $forum_row['forum_last_post_time'] , 'm' ) ,
			'last_post_id' => intval( $forum_row['forum_last_post_id'] ) ,
			'bit_new' => $forum_row['forum_last_post_time'] > $u2t_time && ! empty( $forum_row['forum_topics_count'] ) ? 1 : 0 ,
		) ;
	}

	// tree structure of this category (ul)
	$depth_diff = $cat_row['cat_depth_in_tree'] - @$previous_depth ;
	$previous_depth = $cat_row['cat_depth_in_tree'] ;
	$ul_in = $ul_out = '' ;
	if( $depth_diff > 0 ) {
		for( $i = 0 ; $i < $depth_diff ; $i ++ ) {
			$ul_in .= '<ul><li>' ;
		}
	} else if( $depth_diff < 0 ) {
		for( $i = 0 ; $i < - $depth_diff ; $i ++ ) {
			$ul_out .= '</li></ul>' ;
		}
	} else {
		$ul_in = '<li>' ;
		$ul_out .= '</li>' ;
	}

	// categories array
	$categories4assign[] = array(
		'id' => $cat_id ,
		'pid' => $cat_row['pid'] ,
		'title' => $myts->makeTboxData4Show( $cat_row['cat_title'] ) ,
		'desc' => $myts->displayTarea( $cat_row['cat_desc'] ) ,
		'topics_count' => intval( $cat_row['cat_topics_count'] ) ,
		'posts_count' => intval( $cat_row['cat_posts_count'] ) ,
		'last_post_time' => intval( $cat_row['cat_last_post_time'] ) ,
		'last_post_time_formatted' => formatTimestamp( $cat_row['cat_last_post_time'] , 'm' ) ,
		'topics_count_in_tree' => intval( $cat_row['cat_topics_count_in_tree'] ) ,
		'posts_count_in_tree' => intval( $cat_row['cat_posts_count_in_tree'] ) ,
		'last_post_time_in_tree' => intval( $cat_row['cat_last_post_time_in_tree'] ) ,
		'last_post_time_in_tree_formatted' => formatTimestamp( $cat_row['cat_last_post_time_in_tree'] , 'm' ) ,
		'bit_new' => 0 , // TODO
		'last_post_id' => intval( $cat_row['cat_last_post_id'] ) ,
		'last_post_id_in_tree' => intval( $cat_row['cat_last_post_id_in_tree'] ) ,
		'depth_in_tree' => intval( $cat_row['cat_depth_in_tree'] ) ,
		'order_in_tree' => intval( $cat_row['cat_order_in_tree'] ) ,
		'ul_in' => $ul_in ,
		'ul_out' => $ul_out ,
		'depth_diff' => $depth_diff ,
		'forums' => $forums ,
		'moderate_groups' => d3forum_get_category_moderate_groups4show( $mydirname , $cat_row['cat_id'] ) ,
		'moderate_users' => d3forum_get_category_moderate_users4show( $mydirname , $cat_row['cat_id'] ) ,
		'can_makeforum' => ( $isadmin || @$category_permissions[ $cat_id ]['can_makeforum'] || @$category_permissions[ $cat_id ]['is_moderator'] ) ,
		'paths_raw' => unserialize( $cat_row['cat_path_in_tree'] ) ,
	) ;
}

// extract $top_categories and thier subcategories (F1) from $categories
$top_categories4assign = array() ;
foreach( $categories4assign as $category ) {
	if( $category['pid'] == 0 ) {
		$category['subcategories'] = array() ;
		foreach( $categories4assign as $subcategory ) {
			if( $subcategory['pid'] == $category['id'] ) {
				$category['subcategories'][] = $subcategory ;
			}
		}
		$top_categories4assign[] = $category ;
	}
}

$xoopsOption['template_main'] = $mydirname.'_main_listcategories.html' ;
include XOOPS_ROOT_PATH.'/header.php' ;

unset( $xoops_breadcrumbs[ sizeof( $xoops_breadcrumbs ) - 1 ]['url'] ) ;
$xoopsTpl->assign(
	array(
		'total_topics_count' => $total_topics_count ,
		'total_posts_count' => $total_posts_count ,
		'lastvisit' => $last_visit ,
		'lastvisit_formatted' => formatTimestamp( $last_visit , 'm' ) ,
		'currenttime' => time() ,
		'currenttime_formatted' => formatTimestamp( time() , 'm' ) ,
		'selected_category' => @$selected_category4assign ,
		'categories' => $categories4assign ,
		'top_categories' => $top_categories4assign ,
		'categories_ul_out_last' => str_repeat( '</li></ul>' , $previous_depth + 1 ) ,
		'page' => 'listcategories' ,
		'xoops_breadcrumbs' => $xoops_breadcrumbs ,
	)
) ;

?>