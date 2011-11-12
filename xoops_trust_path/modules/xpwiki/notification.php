<?php

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return xpwiki_notify_base( "'.$mydirname.'" , $category , $item_id ) ;
}
' ) ;

if( ! function_exists( 'xpwiki_notify_base' ) ) {

function xpwiki_notify_base( $mydirname , $category , $item_id )
{

	if( $category == 'global' ) {
		$item['name'] = '';
		$item['url'] = '';
		return $item ;
	}

	include_once dirname(__FILE__).'/include.php' ;

	$xpwiki =& XpWiki::getInitedSingleton($mydirname) ;

	if( substr($category, 0, 4) === 'page' ) {
		// Assume we have a valid $item_id
		$item['name'] = $xpwiki->func->get_name_by_pgid($item_id);
		$item['url'] = $xpwiki->func->get_page_uri($item['name'], true);
		return $item ;
	}
	
}

}

?>