<?php
function b_sitemap_articles(){
	$xoopsDB =& Database::getInstance();
    $block = sitemap_get_categories_map($xoopsDB->prefix("articles_cat"),
"id", "cat_parent_id", "cat_name", "index.php?cat_id=", "cat_name");
	return $block;
}
