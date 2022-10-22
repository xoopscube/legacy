<?php

function b_sitemap_wfsection(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("wfs_category"), "id", "pid", "title", "viewarticles.php?category=", "title");

	return $block;
}
