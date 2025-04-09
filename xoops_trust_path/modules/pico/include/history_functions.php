<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/common_functions.php';

function pico_get_content_history_profile( $mydirname, $content_history_id, $content_id = null ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	if ( empty( $content_history_id ) && ! empty( $content_id ) ) {
		// fetch from contents table as the latest content_history
		$history_row = $db->fetchArray( $db->query(
			'SELECT o.*,up.uname AS poster_uname,mp.uname AS modifier_uname FROM ' . $db->prefix( $mydirname . '_contents' ) . ' o LEFT JOIN ' . $db->prefix( 'users' ) . ' up ON o.poster_uid=up.uid LEFT JOIN ' . $db->prefix( 'users' ) . " mp ON o.modifier_uid=mp.uid WHERE o.content_id=$content_id" ) );
	} else {
		// get $history_row and $content_id
		$history_row = $db->fetchArray( $db->query(
			'SELECT oh.*,up.uname AS poster_uname,mp.uname AS modifier_uname FROM '
			. $db->prefix( $mydirname . '_content_histories' ) . ' oh LEFT JOIN '
			. $db->prefix( 'users' ) . ' up ON oh.poster_uid=up.uid LEFT JOIN '
			. $db->prefix( 'users' ) . " mp ON oh.modifier_uid=mp.uid WHERE oh.content_history_id=$content_history_id" ) );
		if ( empty( $history_row['content_id'] ) ) {
			die( 'Invalid content_history_id' );
		}
		$content_id = (int) $history_row['content_id'];
	}

	// get and process $cat_id
	$cat_id = pico_common_get_cat_id_from_content_id( $mydirname, $content_id );

	// unserialize and visualize extra_fields
	$ef4display = print_r( pico_common_unserialize( $history_row['extra_fields'] ), true );

	return [
		$cat_id,
		$content_id,
		"content_id:    {$history_row['content_id']}
        subject:        {$history_row['subject']}
        cat_id:         {$history_row['cat_id']}
        vpath:          {$history_row['vpath']}
        created:        " . formatTimestamp( $history_row['created_time'], 'm' ) .
		" ({$history_row['poster_ip']}) {$history_row['poster_uname']}({$history_row['poster_uid']})
        modified:       " . formatTimestamp( $history_row['modified_time'], 'm' ) .
		" ({$history_row['modifier_ip']}) {$history_row['modifier_uname']}({$history_row['modifier_uid']})
        filters:        {$history_row['filters']}
        tags:           {$history_row['tags']}
        htmlheader:     {$history_row['htmlheader']}
        body:           {$history_row['body']}
        extra_fields:   $ef4display"
	];
}

// get content_histories for form
function pico_get_content_histories4assign( $mydirname, $content_id ) {
	$myts = null;
 $db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

	$ret = [];

	$sql = 'SELECT oh.content_history_id,oh.created_time,oh.modified_time,LENGTH(body) AS body_size,oh.poster_uid,up.uname AS poster_uname,oh.modifier_uid,um.uname AS modifier_uname FROM '
	       . $db->prefix( $mydirname . '_content_histories' ) . ' oh LEFT JOIN '
	       . $db->prefix( 'users' ) . ' up ON oh.poster_uid=up.uid LEFT JOIN '
	       . $db->prefix( 'users' ) . " um ON oh.modifier_uid=um.uid WHERE oh.content_id=$content_id ORDER BY oh.content_history_id DESC";

	$result = $db->query( $sql );

	if ( $result ) {
		while ( $row = $db->fetchArray( $result ) ) {
			$row4assign = [
				'id'                      => (int) $row['content_history_id'],
				'created_time_formatted'  => formatTimestamp( $row['created_time'], 'm' ),
				'modified_time_formatted' => formatTimestamp( $row['modified_time'], 'm' ),
				'poster_uname'            => $row['poster_uid'] ? $myts->makeTboxData4Show( $row['poster_uname'] ) : _MD_PICO_REGISTERED_AUTOMATICALLY,
				'modifier_uname'          => $row['modifier_uid'] ? $myts->makeTboxData4Show( $row['modifier_uname'] ) : _MD_PICO_REGISTERED_AUTOMATICALLY,
			];
			$ret[]      = $row4assign + $row;
		}
	}

	foreach ( array_keys( $ret ) as $i ) {
		if ( empty( $ret[ $i + 1 ] ) ) {
			$ret[ $i ]['prev_id'] = 0;
		} else {
			$ret[ $i ]['prev_id'] = $ret[ $i + 1 ]['id'];
		}
	}

	return $ret;
}
