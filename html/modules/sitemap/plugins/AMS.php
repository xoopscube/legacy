<?php
function b_sitemap_AMS(){
	$xoopsDB =& Database::getInstance();

	// news
//     $maptree = new SitemapTree($xoopsDB->prefix("topics"), "topic_id", "topic_pid");
//     $block = $maptree->getCategoriesMap("topic_title", "topic_title");
    $block = sitemap_get_categories_map($xoopsDB->prefix("ams_topics"), "topic_id", "topic_pid", "topic_title", "index.php?storytopic=", "topic_title");
    //$block["path"] = "index.php?storytopic=";

	return $block;
}

