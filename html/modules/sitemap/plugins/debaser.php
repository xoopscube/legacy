<?php
// Desc     ::  Sitemap Plugin for debaser 0.92
// AUTHOR	::	Proshack <webmaster@proshack.net>
// WEB		::	http://www.proshack.net

function b_sitemap_debaser(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("debaser_genre"), "genreid", "subgenreid", "genretitle", "genre.php?genreid=", "genretitle");

	return $block;
}
