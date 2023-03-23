<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    GPL v2.0
 */

function d3forum_notify_iteminfo( $mydirname, $category, $item_id ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$module_handler = xoops_gethandler( 'module' );
	$module         = $module_handler->getByDirname( $mydirname );

	if ( 'global' === $category ) {
		$item['name'] = '';
		$item['url']  = '';

		return $item;
	}

	if ( 'category' === $category ) {

		// Assume we have a valid cat_id
		$sql = 'SELECT cat_title FROM ' . $db->prefix( $mydirname . '_categories' ) . ' WHERE cat_id=' . $item_id;

		$result = $db->query( $sql );

		$result_array = $db->fetchArray( $result );

		$item['name'] = $result_array['cat_title'];

		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar( 'dirname' ) . '/index.php?cat_id=' . $item_id;

		return $item;
	}

	if ( 'forum' === $category ) {

		// Assume we have a valid forum_id
		$sql = 'SELECT forum_title FROM ' . $db->prefix( $mydirname . '_forums' ) . ' WHERE forum_id=' . $item_id;

		$result = $db->query( $sql );

		$result_array = $db->fetchArray( $result );

		$item['name'] = $result_array['forum_title'];

		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar( 'dirname' ) . '/index.php?forum_id=' . $item_id;

		return $item;
	}

	if ( 'topic' === $category ) {

		// Assume we have a valid topid_id
		$sql = 'SELECT topic_title FROM ' . $db->prefix( $mydirname . '_topics' ) . ' WHERE topic_id=' . $item_id;

		$result = $db->query( $sql );

		$result_array = $db->fetchArray( $result );

		$item['name'] = $result_array['topic_title'];

		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar( 'dirname' ) . '/index.php?topic_id=' . $item_id;

		return $item;
	}
}
