<?php
function b_sitemap_liaise() {
	$xoopsDB = & Database :: getInstance();

	$sitemap = [];
	$myts = & MyTextSanitizer :: getInstance();

	$table = $xoopsDB->prefix("liaise_forms");

	$i = 0;
	$sql = "SELECT `form_id`,`form_title` FROM `$table` WHERE `form_order`!=0 ORDER BY `form_order`";
	$result = $xoopsDB->query($sql);
	while ([$id, $title] = $xoopsDB->fetchRow($result)) {
		$sitemap['parent'][$i]['id'] = $id;
		$sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($title);
		$sitemap['parent'][$i]['url'] = 'index.php?form_id=' . $id;

		$i++;
	}
	return $sitemap;
}
