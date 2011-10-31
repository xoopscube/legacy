<?php
include dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$lid = empty( $_GET['com_itemid'] ) ? 0 : intval( $_GET['com_itemid'] ) ;
if( $lid > 0 ) {
	$rs = $xoopsDB->query( "SELECT title FROM $table_photos WHERE lid=$lid" ) ;
	list( $title ) = $xoopsDB->fetchRow( $rs ) ;
	$com_replytitle = $title ;

	if( ! $title ) die( "invalid lid" ) ;

	require_once XOOPS_ROOT_PATH.'/include/comment_new.php';
}
?>