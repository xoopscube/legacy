<?php
// $Id: catads.php,v 17.1 2005/01/15 15:35:46 HMN
// FILE		::	catads.php
// AUTHOR	::	HMN <pc-ressources@fr.st>
// WEB		::	pc-ressources <http://hmn.no-ip.com>
//

function b_sitemap_catads(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("catads_cat"), "cat_id", "pid", "title", "adslist.php?cat_id=", "title");

	return $block;
}
