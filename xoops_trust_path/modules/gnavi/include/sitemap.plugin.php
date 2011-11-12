<?php

function b_sitemap_gnavi( $mydirname ){
	$db =& Database::getInstance();
	$ret = sitemap_get_categoires_map($db->prefix($mydirname."_cat"), "cid", "pid", "title", "index.php?cid=", "title");
	return $ret;
}

?>