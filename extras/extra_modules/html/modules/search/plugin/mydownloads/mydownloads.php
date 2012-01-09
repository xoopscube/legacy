<?php
// $Id: mydownloads.php,v 1.0 2005/02/02 06:05:00 suin 
// FILE		::	mydownloads.php
// AUTHOR	::	suin <sim@suin.jp>
// WEB		::	AmethystBlue <http://suin.asia>
//
function b_search_mydownloads($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$sql = "SELECT d.lid,d.cid,d.title,d.submitter,d.date,t.description FROM ".$xoopsDB->prefix("mydownloads_downloads")." d LEFT JOIN ".$xoopsDB->prefix("mydownloads_text")." t ON t.lid=d.lid WHERE status>0";
	}else{
		$sql = "SELECT d.lid,d.cid,d.title,d.submitter,d.date FROM ".$xoopsDB->prefix("mydownloads_downloads")." d LEFT JOIN ".$xoopsDB->prefix("mydownloads_text")." t ON t.lid=d.lid WHERE status>0";
	}
	if ( $userid != 0 ) {
		$sql .= " AND d.submitter=".$userid." ";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((d.title LIKE '%$queryarray[0]%' OR t.description LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(d.title LIKE '%$queryarray[$i]%' OR t.description LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY d.date DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;

	$myts =& MyTextSanitizer::getInstance();

 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['image'] = "images/size2.gif";
		$ret[$i]['link'] = "singlefile.php?cid=".$myrow['cid']."&amp;lid=".$myrow['lid']."";
		$ret[$i]['title'] = $myrow['title'];
		$ret[$i]['time'] = $myrow['date'];
		$ret[$i]['uid'] = $myrow['submitter'];
		if( !empty( $myrow['description'] ) ){
			$context = $myrow['description'];
			$context = strip_tags($myts->displayTarea(strip_tags($context)));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
		}
		$i++;
	}
	return $ret;
}
?>
