<?php
// FILE		::	myalbum.php
// AUTHOR	::	suin <tms@s10.xrea.com>
// WEB		::	AmethystBlue <http://www.suin.jp/>
// DATE		::	2005-02-15
function b_sitemap_myalbum(){
	$db =& Database::getInstance();
	$block = sitemap_get_categories_map($db->prefix("myalbum_cat"), "cid", "pid", "title", "viewcat.php?cid=", "title");
	return $block;
}
