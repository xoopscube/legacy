<?php
function b_sitemap_bluesbb(){
	global $sitemap_configs,$xoopsUser,$member_handler;
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$sitemap = [];
	if($sitemap_configs["show_subcategoris"]){
		$query1='SELECT * FROM '.$db->prefix('bluesbb_categories').' ORDER BY cat_order';
		if (!$result1 = $db->query($query1)) {
			return false;
		}
		while ( $cat_row = $db->fetchArray($result1) ) {
			$i = $cat_row['cat_id'];
			$sitemap['parent'][$i]['id'] = $cat_row['cat_id'];
			$sitemap['parent'][$i]['title'] = $myts->htmlSpecialChars($cat_row['cat_title']);
			$sitemap['parent'][$i]['url'] = "index.php";
		}
	}
	$query='SELECT topic_id, topic_name, cat_id FROM '.$db->prefix('bluesbb_topic').' WHERE';
	if (is_object($xoopsUser)) {
		$query .= ' (topic_access = 1 OR topic_access = 2 OR topic_access = 3 OR topic_access = 4 OR topic_access = 5';
		$groups =& $member_handler->getGroupsByUser($xoopsUser->getVar('uid'),true);
		foreach ($groups as $group){
			$query .= ' OR topic_group = '.$group->getVar('groupid');
		}
		if ( $xoopsUser->isAdmin() ) {
			$query .= ' OR topic_access = 6';
		}
	} else {
		$query .= ' (topic_access = 1 OR topic_access = 2 OR topic_access = 5';
	}
	$query .= ') ORDER BY cat_id, topic_id';
	if (!$result = $db->query($query)) {
		return false;
	}
	$i=0;
	while ( $topic_row = $db->fetchArray($result) ) {
		if($sitemap_configs["show_subcategoris"]){
			$j = $topic_row['cat_id'];
			$sitemap['parent'][$j]['child'][$i]['id'] = $topic_row['topic_id'];
			$sitemap['parent'][$j]['child'][$i]['title'] = $myts->htmlSpecialChars($topic_row['topic_name']);
			$sitemap['parent'][$j]['child'][$i]['image'] = 2;
			$sitemap['parent'][$j]['child'][$i]['url'] = "topic.php?top=".$topic_row['topic_id'];
		}else{
			$sitemap['parent'][$i]['id'] = $topic_row['topic_id'];
			$sitemap['parent'][$i]['title'] = $myts->htmlSpecialChars($topic_row['topic_name']);
			$sitemap['parent'][$i]['url'] = "topic.php?top=".$topic_row['topic_id'];
		}
		$i++;
	}
	return $sitemap;
}
