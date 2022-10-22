<?php
// Desc: Sitemap Plugin for smartsection v1.0 21-Mar-2005
// Author: karedokx (karedokx@yahoo.com)

function b_sitemap_smartsection(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("smartsection_categories"), "categoryid", "parentid", "name", "category.php?categoryid=", "weight");

	return $block;
}
