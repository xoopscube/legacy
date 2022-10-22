<?php
function b_sitemap_formulaire(){

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$result = $db->query("SELECT f.id_form, f.desc_form, f.help FROM " . $db->prefix("form_id") . " f LEFT JOIN " . $db->prefix("form_menu") . " m on f.id_form = m.menuid WHERE m.status =1 ORDER BY m.position");

	$ret = [];
	while(list($id, $name) = $db->fetchRow($result)){
		$ret["parent"][] = array(
			"id" => $id,
			"title" => $myts->makeTboxData4Show($name),
			"url" => "index.php?id=$id"
		);
	}

	return $ret;
}
