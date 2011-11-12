<?php

function b_waiting_gnavi( $mydirname )
{
	$db =& Database::getInstance();
	$ret = array();

	$sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_photos")." WHERE status=0";
	if( $result = $db->query($sql) ) {
		list( $waiting_count ) = $db->fetchRow( $result ) ;
		$ret = array(
			'adminlink' => XOOPS_URL.'/modules/'.$mydirname.'/admin/index.php?page=admission' ,
			'pendingnum' => intval( $waiting_count ) ,
			'lang_linkname' => _PI_WAITING_WAITINGS ,
		) ;
	}

	return $ret;
}

?>