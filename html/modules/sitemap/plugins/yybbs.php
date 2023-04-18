<?php
function b_sitemap_yybbs(){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$sitemap = [];
	$i = 0;
	$url = "index.php?bbs_id=";

	$sql = 'SELECT bbs_id, title FROM '.$db->prefix('yybbs_bbs').' WHERE status=0 ORDER BY priority';
	$result = $db->query($sql);
	while ( [$catid, $name] = $db->fetchRow($result) ) {
		$sitemap['parent'][$i]['id'] = $catid;
		$sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($name);
		$sitemap['parent'][$i]['url'] = $url.$catid;
		$i++;
	}
	return $sitemap;
}
