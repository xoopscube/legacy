<?php
// $Id: tutorials.php,v 17.1 2005/01/15 15:35:46 HMN
// FILE		::	tutorials.php
// AUTHOR	::	HMN <pc-ressources@fr.st>
// WEB		::	pc-ressources <http://hmn.no-ip.com>
//

function b_sitemap_tutorials(){
	$xoopsDB =& Database::getInstance();

    $block = sitemap_get_categories_map($xoopsDB->prefix("tutorials_categorys"), "cid", "scid", "cname", "listutorials?cid=", "cname");

	return $block;
}
