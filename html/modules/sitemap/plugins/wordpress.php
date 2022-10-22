<?php
// $Id: wordpress.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// FILE		::	wordpress.php
// AUTHOR	::	Ryuji AMANO <info@ryus.co.jp>
// WEB		::	Ryu's Planning <http://ryus.co.jp/>
//

function b_sitemap_wordpress(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("wp_categories"), "cat_ID", "category_parent", "cat_name", "index.php?cat=", "cat_name");

	return $block;
}
