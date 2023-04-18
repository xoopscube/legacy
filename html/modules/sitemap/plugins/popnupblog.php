<?php
// $Id: popnupblog.php,v 1.0 2005/01/22 17:05:00 Yoshis
// FILE		::	popnupblog.php
// AUTHOR	::	yoshis <webmaster@bluemooninc.biz>
// WEB		::	Bluemoon inc. <http://www.bluemooninc.biz>
//
function b_sitemap_popnupblog(){
    global $sitemap_configs;
    $db =& Database::getInstance();
    $myts =& MyTextSanitizer::getInstance();
    $sitemap = [];

    if($sitemap_configs["show_subcategoris"]){ // Execute with show sub categories by Yoshis
        // Get categories
        $sql = 'SELECT DISTINCT c.* FROM '.$db->prefix('popnupblog_categories').' c, '.$db->prefix("popnupblog_info").
        	' f WHERE f.cat_id=c.cat_id GROUP BY c.cat_id, c.cat_title, c.cat_order ORDER BY c.cat_order';
        $result = $db->query($sql);
        $categories = [];
        while ( $cat_row = $db->fetchArray($result) ) {
            $i = $cat_row["cat_id"];
            $sitemap['parent'][$i]['id'] = $cat_row["cat_id"];
            $sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($cat_row["cat_title"]);
            $sitemap['parent'][$i]['url'] = "index.php?cat_id=".$cat_row["cat_id"];
            $categories[] = $cat_row["cat_id"];
        }
    }
    // Get Blog info.
    $sql = "SELECT f.* FROM ".$db->prefix("popnupblog_info")." f LEFT JOIN ".$db->prefix("popnupblog_categories")." c ON f.cat_id=c.cat_id ORDER BY f.blogid";
    $result = $db->query($sql);
    //$forums = array();
    $i=0;
    while($blog_row = $db->fetchArray($result)){
        //if(in_array($blog_row["cat_id"], $categories)){
            if($sitemap_configs["show_subcategoris"]){ // Execute with show sub categories
                $j = $blog_row["cat_id"];
    			$sitemap['parent'][$j]['child'][$i]['id'] = $blog_row["blogid"];
    			$sitemap['parent'][$j]['child'][$i]['title'] = $myts->makeTboxData4Show($blog_row["title"]);
    			$sitemap['parent'][$j]['child'][$i]['image'] = 2;
    			$sitemap['parent'][$j]['child'][$i]['url'] = "index.php?param=".$blog_row['blogid'];
            }else{
				// No sub categories, Blog only.
                $sitemap['parent'][$i]['id'] = $blog_row["blogid"];
                $sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show($blog_row["title"]);
                $sitemap['parent'][$i]['url'] = "index.php?param=".$blog_row['blogid'];
            }
        $i++;
        //}
    }
    //print_r($categories);
    return $sitemap;
}
