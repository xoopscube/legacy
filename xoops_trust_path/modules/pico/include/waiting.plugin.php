<?php

function b_waiting_pico( $mydirname )
{
	$db =& Database::getInstance();
	$ret = array() ;

	$sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_contents")." WHERE approval=0" ;
	if( $result = $db->query($sql) ) {
		list( $waiting_count ) = $db->fetchRow( $result ) ;
		$ret = array(
			'adminlink' => XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?page=contents&amp;cat_id=-1' ,
			'pendingnum' => intval( $waiting_count ) ,
			'lang_linkname' => _PI_WAITING_WAITINGS ,
		) ;
	}
	
	return $ret ;
}

?>