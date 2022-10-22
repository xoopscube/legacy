<?php
function b_sitemap_xoopspoll(){
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	global $xoopsConfig;

	if (file_exists(XOOPS_ROOT_PATH . '/modules/xoopspoll/language/' . $xoopsConfig['language'] . '/main.php'))
	{
		include_once(XOOPS_ROOT_PATH . '/modules/xoopspoll/language/' . $xoopsConfig['language'] . '/main.php');
	}

	$sitemap = [];
	$result=$db->query("SELECT poll_id,question FROM ".$db->prefix("xoopspoll_desc")." ORDER BY weight ASC, end_time DESC");
	$i=0;
	while($poll_row=$db->fetchArray($result)){
		$sitemap['parent'][$i]['id']=$poll_row["poll_id"];

		$sitemap['parent'][$i]['title']=$myts->makeTboxData4Show($poll_row["question"]);

		$sitemap['parent'][$i]['url']="index.php?poll_id=".$poll_row["poll_id"];

		$sitemap['parent'][$i]['child'][$i]['id']=$poll_row["poll_id"];
		$sitemap['parent'][$i]['child'][$i]['title']=_PL_RESULTS;
		$sitemap['parent'][$i]['child'][$i]['image']=2;
		$sitemap['parent'][$i]['child'][$i]['url']="pollresults.php?poll_id=".$poll_row["poll_id"];
		++$i;
	}
	return $sitemap;
}
