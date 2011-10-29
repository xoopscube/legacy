<?php

// written by starck
function b_waiting_bulletin( $mydirname )
{
	$db =& Database::getInstance();
	$ret = array() ;

	$sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_stories")." WHERE type=0" ;
	if( $result = $db->query($sql) ) {
		list( $waiting_count ) = $db->fetchRow( $result ) ;
		$ret = array(
			'adminlink' => XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?op=list' ,
			'pendingnum' => intval( $waiting_count ) ,
			'lang_linkname' => _PI_WAITING_WAITINGS ,
		) ;
	}
	
	return $ret ;
}

?>