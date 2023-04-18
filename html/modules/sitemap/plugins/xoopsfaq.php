<?php
function b_sitemap_xoopsfaq(){

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$result = $db->query("SELECT category_id, category_title FROM ".$db->prefix("xoopsfaq_categories")." ORDER BY category_order");

	$ret = [];
	while([$id, $name] = $db->fetchRow($result)){
		$ret["parent"][] = [
			"id" => $id,
			"title" => $myts->makeTboxData4Show($name),
			"url" => "index.php?cat_id=$id"
        ];
	}

	return $ret;
}
