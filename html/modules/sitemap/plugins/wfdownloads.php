<?php
// $Id: wfdownloads.php,v 17.1 2005/01/15 15:35:46 HMN
// FILE		::	wfdownloads.php
// AUTHOR	::	HMN <pc-ressources@fr.st>
// WEB		::	pc-ressources <http://hmn.no-ip.com>
//

function b_sitemap_wfdownloads(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("wfdownloads_cat"), "cid", "pid", "title", "viewcat.php?cid=", "title");

	return $block;
}
