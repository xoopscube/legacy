<?php
function b_search_newbb($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$sql = "SELECT p.post_id,p.topic_id,p.forum_id,p.post_time,p.uid,p.subject,p.nohtml,p.nosmiley,t.post_text FROM ".$xoopsDB->prefix("bb_posts")." p LEFT JOIN ".$xoopsDB->prefix("bb_posts_text")." t ON t.post_id=p.post_id LEFT JOIN ".$xoopsDB->prefix("bb_forums")." f ON f.forum_id=p.forum_id WHERE f.forum_type=0";
	}else{
		$sql = "SELECT p.post_id,p.topic_id,p.forum_id,p.post_time,p.uid,p.subject,p.nohtml,p.nosmiley FROM ".$xoopsDB->prefix("bb_posts")." p LEFT JOIN ".$xoopsDB->prefix("bb_posts_text")." t ON t.post_id=p.post_id LEFT JOIN ".$xoopsDB->prefix("bb_forums")." f ON f.forum_id=p.forum_id WHERE f.forum_type=0";
	}
	if ( $userid != 0 ) {
		$sql .= " AND p.uid=".$userid." ";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((p.subject LIKE '%$queryarray[0]%' OR t.post_text LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(p.subject LIKE '%$queryarray[$i]%' OR t.post_text LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY p.post_time DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
	
	$myts =& MyTextSanitizer::getInstance();
 	
 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['link'] = "viewtopic.php?topic_id=".$myrow['topic_id']."&amp;forum=".$myrow['forum_id']."&amp;post_id=".$myrow['post_id']."#forumpost".$myrow['post_id'];
		$ret[$i]['title'] = $myrow['subject'];
		$ret[$i]['time'] = $myrow['post_time'];
		$ret[$i]['uid'] = $myrow['uid'];
		if( !empty($myrow['post_text']) ){
	 		$context =strip_tags($myts->displayTarea($myrow['post_text'],$myrow['nohtml'],$myrow['nosmiley'],1));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
		}
		$i++;
	}
	return $ret;
}
?>