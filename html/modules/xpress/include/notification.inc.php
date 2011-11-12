<?php
function xpress_notify( $category , $item_id )
{
	$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

	if( $category == 'global' ) {
		$item['name'] = '';
		$item['url'] = XOOPS_URL.'/modules/'.$mydirname . '/wp-admin/edit-comments.php?comment_status=moderated';
		return $item ;
	}

	if( $category == 'category' ) {
		// Assume we have a valid cat_id
		$item['name'] = '';
		$item['url'] = '';

		return $item ;
	}
	
	if( $category == 'author' ) {
		// Assume we have a valid cat_id
		$item['name'] = '';
		$item['url'] = '';
		return $item ;
	}

	if( $category == 'post' ) {
		// Assume we have a valid forum_id
		$item['name'] = '';
		$item['url'] = '';
		return $item ;
	}

}
?>