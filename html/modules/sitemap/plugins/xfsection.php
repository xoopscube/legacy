<?php

function b_sitemap_xfsection(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("xfs_category"), "id", "pid", "title", "index.php?category=", "title");

	return $block;
}
