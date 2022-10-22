<?php
function b_sitemap_news(){
	$xoopsDB =& Database::getInstance();

	// news
    $block = sitemap_get_categories_map($xoopsDB->prefix("topics"), "topic_id", "topic_pid", "topic_title", "index.php?storytopic=", "topic_title");

	return $block;
}
