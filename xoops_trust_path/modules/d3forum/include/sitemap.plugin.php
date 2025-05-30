<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL_V2
 */

function b_sitemap_d3forum( $mydirname ) {
	$myts = null;
 $db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = MyTextSanitizer::sGetInstance() ) || $myts = ( new MyTextSanitizer )->getInstance();
	$ret = [];

	include_once __DIR__ . '/common_functions.php';

	$whr_forum = 'forum_id IN (' . implode( ',', d3forum_get_forums_can_read( $mydirname ) ) . ')';

	$sql = 'SELECT forum_id,forum_title FROM ' . $db->prefix( $mydirname . '_forums' ) . " WHERE ($whr_forum)";

	$result = $db->query( $sql );

	while ( [$forum_id, $forum_title] = $db->fetchRow( $result ) ) {
		$ret['parent'][] = [
			'id'    => (int) $forum_id,
			'title' => $myts->makeTboxData4Show( $forum_title ),
			'url'   => 'index.php?forum_id=' . (int) $forum_id,
		];
	}

	return $ret;
}
