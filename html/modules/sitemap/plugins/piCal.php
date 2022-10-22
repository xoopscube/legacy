<?php
function b_sitemap_piCal(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("pical_cat"), "cid", "pid", "cat_title", "index.php?cid=", "weight");

	return $block;
}
