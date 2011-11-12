<?php

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return d3forum_notify_base( "'.$mydirname.'" , $category , $item_id ) ;
}
' ) ;

if( ! function_exists( 'd3forum_notify_base' ) ) {

function d3forum_notify_base( $mydirname , $category , $item_id )
{
	include_once dirname(__FILE__).'/include/common_functions.php' ;

	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $mydirname ) ;

	if( $category == 'global' ) {
		$item['name'] = '';
		$item['url'] = '';
		return $item ;
	}

	if( $category == 'category' ) {
		// Assume we have a valid cat_id
		$whr_cat = 'cat_id IN ('.implode(',',d3forum_get_categories_can_read( $mydirname )).')' ;
		$sql = 'SELECT cat_title FROM ' . $db->prefix($mydirname.'_categories') . ' WHERE cat_id='.$item_id." AND ($whr_cat)" ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['cat_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?cat_id='.$item_id ;
		return $item ;
	}

	if( $category == 'forum' ) {
		// Assume we have a valid forum_id
		$whr_forum = 'forum_id IN ('.implode(',',d3forum_get_forums_can_read( $mydirname )).')' ;
		$sql = 'SELECT forum_title FROM ' . $db->prefix($mydirname.'_forums') . ' WHERE forum_id='.$item_id." AND ($whr_forum)" ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['forum_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?forum_id='.$item_id ;
		return $item ;
	}

	if( $category == 'topic' ) {
		// Assume we have a valid topid_id
		$whr_forum = 'forum_id IN ('.implode(',',d3forum_get_forums_can_read( $mydirname )).')' ;
		$sql = 'SELECT topic_title FROM ' . $db->prefix($mydirname.'_topics') . ' WHERE topic_id='.$item_id." AND ($whr_forum)" ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['topic_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?topic_id='.$item_id ;
		return $item ;
	}

}

}

?>