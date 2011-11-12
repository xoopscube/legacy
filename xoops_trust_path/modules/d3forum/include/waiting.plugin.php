<?php

function b_waiting_d3forum( $mydirname )
{
	$db =& Database::getInstance();
	$ret = array() ;

	$sql = "SELECT COUNT(*),MIN(post_id) FROM ".$db->prefix($mydirname."_posts")." WHERE approval=0" ;
	if( $result = $db->query($sql) ) {
		list( $waiting_count , $post_id ) = $db->fetchRow( $result ) ;
		$ret = array(
			'adminlink' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?post_id='.intval($post_id) ,
			'pendingnum' => intval( $waiting_count ) ,
			'lang_linkname' => _PI_WAITING_WAITINGS ,
		) ;
	}
	
	return $ret ;
}

?>