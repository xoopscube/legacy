<?php
// Desc: Sitemap Plugin for X-PHPBB v1.0 11-Mar-2005
// Author: karedokx (karedokx@yahoo.com)

function b_sitemap_pbboard(){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$sitemap = [];
	$i = 0;
	$url = "index.php?c=";

	$sql = 'SELECT cat_id, cat_title FROM '.$db->prefix('pbb_categories').' ORDER BY cat_order';
	$result = $db->query($sql);
	while ( list($catid, $name) = $db->fetchRow($result) ) {
		$sitemap['parent'][$i]['id'] = $catid;
		$sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($name);
		$sitemap['parent'][$i]['url'] = $url.$catid;
		$i++;
	}
	return $sitemap;
}
