<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/include/main_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoPermission.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoModelCategory.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoModelContent.class.php' ;

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return pico_notify_base( "'.$mydirname.'" , $category , $item_id ) ;
}
' ) ;

if( ! function_exists( 'pico_notify_base' ) ) {

function pico_notify_base( $mydirname , $category , $item_id )
{
	include_once dirname(__FILE__).'/include/common_functions.php' ;

	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler( 'module' ) ;
	$module =& $module_handler->getByDirname( $mydirname ) ;

	$picoPermission =& PicoPermission::getInstance() ;
	$permissions = $picoPermission->getPermissions( $mydirname ) ;

	// global
	if( $category == 'global' ) {
		$item['name'] = '' ;
		$item['url'] = '' ;
	}

	// category
	if( $category == 'category' ) {
		// Assume we have a valid cat_id
		$cat_id = intval( $item_id ) ;

		$currentCategoryObj = new PicoCategory( $mydirname , intval( $cat_id ) , $permissions ) ;
		$cat_data = $currentCategoryObj->getData() ;
		if( ! $cat_data['can_read'] ) return false ;

		$category4assign = $currentCategoryObj->getData4html() ;

		// $item should be assigned HTML escaped data
		$item['name'] = $category4assign['cat_title'] ;
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $category4assign['link'] ;
	}

	// content
	if( $category == 'content' ) {
		// Assume we have a valid content_id
		$content_id = intval( $item_id ) ;

		$cat_id = pico_common_get_cat_id_from_content_id( $mydirname , $content_id ) ;

		$currentCategoryObj = new PicoCategory( $mydirname , intval( $cat_id ) , $permissions ) ;
		$cat_data = $currentCategoryObj->getData() ;
		if( ! $cat_data['can_read'] ) return false ;

		$contentObj = new PicoContent( $mydirname , $content_id , $currentCategoryObj ) ;
		$content4assign = $contentObj->getData4html() ;
		if( ! $content4assign['can_read'] ) return false ;

		// $item should be assigned HTML escaped data
		$item['name'] = $content4assign['subject'] ;
		$item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $content4assign['link'] ;
	}

	return $item ;
}

}

?>