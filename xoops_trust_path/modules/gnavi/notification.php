<?php

eval( '
function '.$mydirname.'_notify_iteminfo( $category, $item_id )
{
	return gnavi_notify_base( "'.$mydirname.'" , $category , $item_id ) ;
}
' ) ;

if( ! function_exists( 'gnavi_notify_base' ) ) {

function gnavi_notify_base( $mydirname , $category , $item_id )
{
	//include_once dirname(__FILE__).'/include/common_functions.php' ;

	$db =& Database::getInstance() ;

	$module_handler =& xoops_gethandler("module");
	$module =& $module_handler->getByDirname($mydirname);

	$mod_url = XOOPS_URL . "/modules/" . $mydirname ;

	$item = array();

	if ($category=="global") {

		$item["name"] = "";
		$item["url"] = "";

	} else if( $category == "category" ) {

		// Assume we have a valid cid
		$sql = "SELECT title FROM ".$db->prefix($mydirname."_cat")." WHERE cid=$item_id";

		$rs = $db->query( $sql ) ;
		list( $title ) = $db->fetchRow( $rs ) ;
		$item["name"] = $title ;
		$item["url"] = "$mod_url/index.php?cid=$item_id" ;

	} else if( $category == "item" ) {

		// Assume we have a valid lid
		$sql = "SELECT title FROM ".$db->prefix($mydirname."_photos")." WHERE lid=$item_id";
		$rs = $db->query( $sql ) ;
		list( $title ) = $db->fetchRow( $rs ) ;
		$item["name"] = $title ;
		$item["url"] = "$mod_url/index.php?lid=$item_id" ;

	}

	return $item;
}

}

?>