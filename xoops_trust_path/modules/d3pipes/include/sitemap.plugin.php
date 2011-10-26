<?php

function b_sitemap_d3pipes( $mydirname ) {

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$ret = array();

	$sql = "SELECT pipe_id,name FROM ".$db->prefix($mydirname."_pipes")." WHERE main_disp=1 ORDER BY weight" ;
	$result = $db->query($sql);

	while( list( $pipe_id , $name ) = $db->fetchRow( $result ) ) {
		$ret["parent"][] = array(
			"id" => intval( $pipe_id ) ,
			"title" => $myts->makeTboxData4Show( $name ) ,
			"url" => "index.php?page=eachpipe&amp;pipe_id=".intval( $pipe_id ) ,
		) ;
	}

	return $ret;
}

?>