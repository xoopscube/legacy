<?php
// search plugin Ver,0.10
// Author: Sting_Band
// URL: http://www.bluish.jp/

function b_search_bluesbb($queryarray, $andor, $limit, $offset, $userid){
	$db =& Database::getInstance();
	global $xoopsUser,$member_handler;
	$myts =& MyTextSanitizer::getInstance();
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$sql = "SELECT b.topic_id,b.sread_id,b.res_id,b.title,b.message,b.post_time,b.uid FROM ".$db->prefix("bluesbb")." b LEFT JOIN ".$db->prefix("bluesbb_topic")." t ON t.topic_id=b.topic_id WHERE";
	}else{
		$sql = "SELECT b.topic_id,b.sread_id,b.res_id,b.title,b.post_time,b.uid FROM ".$db->prefix("bluesbb")." b LEFT JOIN ".$db->prefix("bluesbb_topic")." t ON t.topic_id=b.topic_id WHERE";
	}
	if ( is_object($xoopsUser) ) {
		$sql .= " (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 3 OR t.topic_access = 4 OR t.topic_access = 5";
		$groups =& $member_handler->getGroupsByUser($xoopsUser->getVar('uid'),true);
		foreach ($groups as $group){
			$sql .= " OR t.topic_group = ".$group->getVar('groupid');
		}
		if ( $xoopsUser->isAdmin() ) {
			$sql .= " OR t.topic_access = 6";
		}
	} else {
		$sql .= " (t.topic_access = 1 OR t.topic_access = 2 OR t.topic_access = 5";
	}
	$sql .= ")";
	if ( $userid != 0 ) {
		$sql .= " AND b.uid=".$userid." ";
	}
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((b.name LIKE '%$queryarray[0]%' OR b.mail LIKE '%$queryarray[0]%' OR b.url LIKE '%$queryarray[0]%' OR b.title LIKE '%$queryarray[0]%' OR b.message LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(b.name LIKE '%$queryarray[$i]%' OR b.mail LIKE '%$queryarray[$i]%' OR b.url LIKE '%$queryarray[$i]%' OR b.title LIKE '%$queryarray[$i]%' OR b.message LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY b.post_time DESC";
	$result = $db->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
	while($myrow = $db->fetchArray($result)){
		++$myrow['res_id'];
		$ret[$i]['link'] = "viewsread.php?topic=".$myrow['topic_id']."&amp;sread_id=".$myrow['sread_id']."&amp;number=".$myrow['res_id'];
		$ret[$i]['title'] = $myts->htmlSpecialChars($myrow['title']);
		$ret[$i]['time'] = $myrow['post_time'];
		$ret[$i]['uid'] = $myrow['uid'];
		if( !empty( $myrow['message'] ) ){
			$context =strip_tags($myts->displayTarea($myrow['message'],0,1,1,1,1));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
		}
		$i++;
	}
	return $ret;
}
?>