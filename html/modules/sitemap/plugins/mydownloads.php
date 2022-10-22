<?php
// $Id: mydownloads.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// FILE		::	weblinks.php
// AUTHOR	::	Ryuji AMANO <info@ryus.co.jp>
// WEB		::	Ryu's Planning <http://ryus.co.jp/>
//

function b_sitemap_mydownloads(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("mydownloads_cat"), "cid", "pid", "title", "viewcat.php?cid=", "title");

	return $block;
}
