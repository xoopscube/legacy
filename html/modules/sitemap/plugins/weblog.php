<?php
function b_sitemap_weblog()
{
	$xoopsDB =& Database::getInstance();
	$block = sitemap_get_categories_map($xoopsDB->prefix("weblog_category"), "cat_id", "cat_pid", "cat_title", "index.php?cat_id=", "cat_title");
	return $block;
}
