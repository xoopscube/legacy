<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

// get this "category" from given $cat_id
$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_categories' ) . " c WHERE $whr_read4cat AND c.cat_id=$cat_id";

if ( ! $crs = $db->query( $sql ) ) {
	die( _MD_D3FORUM_ERR_SQL . __LINE__ );
}

//if( $db->getRowsNum( $crs ) <= 0 ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;
if ( $db->getRowsNum( $crs ) <= 0 ) {
	return false;
}

$cat_row = $db->fetchArray( $crs );

$isadminorcatmod = (boolean) $category_permissions[ $cat_id ]['is_moderator'] || $isadmin;

$can_makeforum = (boolean) $category_permissions[ $cat_id ]['can_makeforum'];

$category4assign = [
	'id'                               => (int) $cat_row['cat_id'],
	'pid'                              => $cat_row['pid'],
	'title'                            => $myts->makeTboxData4Show( $cat_row['cat_title'] ),
	'desc'                             => $myts->displayTarea( $cat_row['cat_desc'] ),
	'topics_count'                     => (int) $cat_row['cat_topics_count'],
	'posts_count'                      => (int) $cat_row['cat_posts_count'],
	'last_post_time'                   => (int) $cat_row['cat_last_post_time'],
	'last_post_time_formatted'         => formatTimestamp( $cat_row['cat_last_post_time'], 'm' ),
	'topics_count_in_tree'             => (int) $cat_row['cat_topics_count_in_tree'],
	'posts_count_in_tree'              => (int) $cat_row['cat_posts_count_in_tree'],
	'last_post_time_in_tree'           => (int) $cat_row['cat_last_post_time_in_tree'],
	'last_post_time_in_tree_formatted' => formatTimestamp( $cat_row['cat_last_post_time_in_tree'], 'm' ),
	'bit_new'                          => 0, // TODO
	'last_post_id'                     => (int) $cat_row['cat_last_post_id'],
	'last_post_id_in_tree'             => (int) $cat_row['cat_last_post_id_in_tree'],
	'isadminormod'                     => $isadminorcatmod,
	'can_makeforum'                    => ( $isadmin || @$category_permissions[ $cat_id ]['can_makeforum'] || @$category_permissions[ $cat_id ]['is_moderator'] ),
	'paths_raw'                        => unserialize( $cat_row['cat_path_in_tree'] ),
];

// $xoopsModuleConfig override (module -> cat -> forum)
$cat_configs = @unserialize( @$cat_row['cat_options'] );

if ( is_array( $cat_configs ) ) {
	foreach ( $cat_configs as $key => $val ) {
		if ( isset( $xoopsModuleConfig[ $key ] ) ) {
			$xoopsModuleConfig[ $key ] = $val;
		}
	}
}

$forum_configs = @unserialize( @$forum_row['forum_options'] );

if ( is_array( $forum_configs ) ) {
	foreach ( $forum_configs as $key => $val ) {
		if ( isset( $xoopsModuleConfig[ $key ] ) ) {
			$xoopsModuleConfig[ $key ] = $val;
		}
	}
}

// icon meanings
$d3forum_icon_meanings = explode( '|', $xoopsModuleConfig['icon_meanings'] );

// assign breadcrumbs of this category
foreach ( array_reverse( $category4assign['paths_raw'], true ) as $cat_id_tmp => $cat_title_tmp ) {
	array_splice( $xoops_breadcrumbs, 1, 0, [
		[
			'url'  => XOOPS_URL . '/modules/' . $mydirname . '/index.php?cat_id=' . $cat_id_tmp,
			'name' => $cat_title_tmp
		]
	] );
}
