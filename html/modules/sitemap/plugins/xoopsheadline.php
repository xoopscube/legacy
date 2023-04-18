<?php

function b_sitemap_xoopsheadline(){

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$result = $db->query( "SELECT headline_id,headline_name FROM ".$db->prefix("xoopsheadline")." WHERE headline_display=1 ORDER BY headline_weight" ) ;

	$ret = [];
	while( [$id, $name] = $db->fetchRow( $result ) ) {

		$ret["parent"][] = [
			"id" => $id ,
			"title" => $myts->makeTboxData4Show( $name ) ,
			"url" => "index.php?id=$id"
        ];

	}

	return $ret ;
}
