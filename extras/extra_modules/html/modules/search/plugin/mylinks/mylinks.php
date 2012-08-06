<?php
// $Id: mylinks.php,v 1.1 2005/02/02 06:04:00 suin 
// FILE		::	mylinks.php
// AUTHOR	::	suin <sim@suin.jp>
// WEB		::	AmethystBlue <http://suin.asia>
//
function b_search_mylinks($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$sql = "SELECT l.lid,l.cid,l.title,l.submitter,l.date,t.description FROM ".$xoopsDB->prefix("mylinks_links")." l LEFT JOIN ".$xoopsDB->prefix("mylinks_text")." t ON t.lid=l.lid WHERE status>0";
	}else{
		$sql = "SELECT l.lid,l.cid,l.title,l.submitter,l.date FROM ".$xoopsDB->prefix("mylinks_links")." l LEFT JOIN ".$xoopsDB->prefix("mylinks_text")." t ON t.lid=l.lid WHERE status>0";
	}
	if ( $userid != 0 ) {
		$sql .= " AND l.submitter=".$userid." ";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((l.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(l.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY l.date DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;

	//本文のサニタイズ用に追記
	$myts =& MyTextSanitizer::getInstance();

 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['image'] = "images/home.gif";
		$ret[$i]['link'] = "singlelink.php?cid=".$myrow['cid']."&amp;lid=".$myrow['lid']."";
		$ret[$i]['title'] = $myrow['title'];
		$ret[$i]['time'] = $myrow['date'];
		$ret[$i]['uid'] = $myrow['submitter'];
		if( !empty( $myrow['description'] ) ){
	 		//本文始め
			$context = $myrow['description'];
			$context = strip_tags($myts->displayTarea(strip_tags($context)));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
			//本文終わり
		}
		$i++;
	}
	return $ret;
}
?>
