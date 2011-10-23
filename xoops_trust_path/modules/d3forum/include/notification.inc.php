<?php

function d3forum_notify_iteminfo( $mydirname , $category , $item_id )
{
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
		$sql = 'SELECT cat_title FROM ' . $db->prefix($mydirname.'_categories') . ' WHERE cat_id='.$item_id ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['cat_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?cat_id='.$item_id ;
		return $item ;
	}

	if( $category == 'forum' ) {
		// Assume we have a valid forum_id
		$sql = 'SELECT forum_title FROM ' . $db->prefix($mydirname.'_forums') . ' WHERE forum_id='.$item_id ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['forum_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?forum_id='.$item_id ;
		return $item ;
	}

	if( $category == 'topic' ) {
		// Assume we have a valid topid_id
		$sql = 'SELECT topic_title FROM ' . $db->prefix($mydirname.'_topics') . ' WHERE topic_id='.$item_id ;
		$result = $db->query($sql);
		$result_array = $db->fetchArray($result);
		$item['name'] = $result_array['topic_title'];
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/index.php?topic_id='.$item_id ;
		return $item ;
	}
}

?>