<?php
function b_sitemap_pages(){

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$result = $db->query("SELECT CID, pagetitle, FROM " . $db->prefix("pages") . " WHERE mainpage <>0 OR defaultpage =1 ORDER BY weight, pagetitle ASC");

	$ret = [];
	while(list($id, $name) = $db->fetchRow($result)){
		$ret["parent"][] = [
			"id" => $id,
			"title" => $myts->makeTboxData4Show($name),
			"url" => "pagenum.php?id=$id"
        ];
	}

	return $ret;
}
