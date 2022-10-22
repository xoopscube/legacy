<?php
// $Id: weblinks.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// FILE		::	weblinks.php
// AUTHOR	::	Ryuji AMANO <info@ryus.co.jp>
// WEB		::	Ryu's Planning <http://ryus.co.jp/>
//

function b_sitemap_weblinks(){
	$xoopsDB =& Database::getInstance();

	// news
//     $maptree = new SitemapTree($xoopsDB->prefix("topics"), "topic_id", "topic_pid");
//     $block = $maptree->getCategoriesMap("topic_title", "topic_title");
    $block = sitemap_get_categories_map($xoopsDB->prefix("weblinks_category"), "cid", "pid", "title", "viewcat.php?cid=", "title");
    //$block["path"] = "viewcat.php?cid=";

	return $block;
}
