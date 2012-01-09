<?php
// $Id: xoopsfaq.php,v 1.0 2005/02/02 06:05:00 suin 
// FILE		::	xoopsfaq.php
// AUTHOR	::	suin <sim@suin.jp>
// WEB		::	AmethystBlue <http://suin.asia>
//
function b_search_xoopsfaq($queryarray, $andor, $limit, $offset, $userid)
{
	global $xoopsDB;
	$ret = array();
	if ( $userid != 0 ) {
		return $ret;
	}
	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1){
		$sql = "SELECT contents_id, category_id, contents_title, contents_contents, contents_time FROM ".$xoopsDB->prefix("xoopsfaq_contents")." WHERE contents_visible=1 ";
	}else{
		$sql = "SELECT contents_id, category_id, contents_title, contents_time FROM ".$xoopsDB->prefix("xoopsfaq_contents")." WHERE contents_visible=1 ";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	$count = count($queryarray);
	if ( $count > 0 && is_array($queryarray) ) {
		$sql .= "AND ((contents_title LIKE '%$queryarray[0]%' OR contents_contents LIKE '%$queryarray[0]%')";
		for ( $i = 1; $i < $count; $i++ ) {
			$sql .= " $andor ";
			$sql .= "(contents_title LIKE '%$queryarray[$i]%' OR contents_contents LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY contents_id DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$i = 0;

	//本文のサニタイズ用に追記
	$myts =& MyTextSanitizer::getInstance();

 	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$ret[$i]['image'] = "images/question2.gif";
		$ret[$i]['link'] = "index.php?cat_id=".$myrow['category_id']."#".$myrow['contents_id'];
		$ret[$i]['title'] = $myrow['contents_title'];
		$ret[$i]['time'] = $myrow['contents_time'];
		//$ret[$i]['uid'] = $myrow['contents_uid'];
		if( !empty($myrow['contents_contents']) ){
			//本文始め
			$context = $myrow['contents_contents'];
			$context = strip_tags($myts->displayTarea(strip_tags($context)));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
			//本文終わり
		}
		$i++;
	}
	return $ret;
}
?>