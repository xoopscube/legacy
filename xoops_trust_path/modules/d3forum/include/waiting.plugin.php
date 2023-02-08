<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

function b_waiting_d3forum( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$ret = [];

	$sql = 'SELECT COUNT(*),MIN(post_id) FROM ' . $db->prefix( $mydirname . '_posts' ) . ' WHERE approval=0';

	if ( $result = $db->query( $sql ) ) {
		[ $waiting_count, $post_id ] = $db->fetchRow( $result );
		$ret = [
			'adminlink'     => XOOPS_URL . '/modules/' . $mydirname . '/index.php?post_id=' . (int) $post_id,
			'pendingnum'    => (int) $waiting_count,
			'lang_linkname' => _PI_WAITING_WAITINGS,
		];
	}

	return $ret;
}
