<?php
// FILE		::	mybbs.php
// AUTHOR	::	suin <tms@s10.xrea.com>
// WEB		::	AmethystBlue <http://www.suin.jp/>
// DATE		::	2005-02-15
function b_sitemap_mybbs(){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$sitemap = [];
	$i = 0;
	$url = "index.php?bbs_id=";

	$sql = 'SELECT bbs_id, bbs_name FROM '.$db->prefix('mybbs_master').' WHERE status=1 ORDER BY sort_order';
	$result = $db->query($sql);
	while ( [$catid, $name] = $db->fetchRow($result) ) {
		$sitemap['parent'][$i]['id'] = $catid;
		$sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($name);
		$sitemap['parent'][$i]['url'] = $url.$catid;
		$i++;
	}
	return $sitemap;
}
