<?php
// $Id: extcal.php,v 1.0 2005/09/02
// FILE		::	extcal.php
// AUTHOR	::	BONNAUDET Eric <bonnaudet.eric@laposte.net>
// WEB		::	ufolep16 <http://ufolep16.free.fr/>

function b_sitemap_extcal(){

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$result = $db->query("SELECT cat_id, cat_name FROM ".$db->prefix("extcal_cat"));

	$ret = [];
	while(list($id, $name) = $db->fetchRow($result)){
		$ret["parent"][] = [
			"id" => $id,
			"title" => $myts->makeTboxData4Show($name),
			"url" => "calendar.php?cat=$id"
        ];
	}

	return $ret;
}
