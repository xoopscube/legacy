<?php
// $Id: booklists.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// FILE		::	booklists.php
// AUTHOR	::	Ryuji AMANO <info@ryus.co.jp>
// WEB		::	Ryu's Planning <http://ryus.co.jp/>
//

function b_sitemap_booklists(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("mybooks_cat"), "cid", "pid", "title", "viewcat.php?cid=", "title");
    //$block["path"] = "viewcat.php?cid=";

	return $block;
}

