<?php
// Desc: Sitemap Plugin for xcgal v1.0 21-Mar-2005
// Author: karedokx (karedokx@yahoo.com)

function b_sitemap_xcgal(){
$xoopsDB =& Database::getInstance();
$block = sitemap_get_categories_map($xoopsDB->prefix("xcgal_categories"), "cid", "parent", "name", "index.php?cat=", "pos");
return $block;
}
