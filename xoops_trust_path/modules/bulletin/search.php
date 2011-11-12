<?php

eval( '
function '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return bulletin_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}
' ) ;

if( ! function_exists( 'bulletin_search_base' ) ) {

function bulletin_search_base( $mydirname , $queryarray , $andor , $limit , $offset , $userid ){
	global $xoopsDB;

	$showcontext = isset( $_GET['showcontext'] ) ? $_GET['showcontext'] : 0 ;
	if( $showcontext == 1 && function_exists('search_make_context')){
		$sql = "SELECT storyid,uid,title,published,hometext,bodytext,html,smiley FROM ".$xoopsDB->prefix($mydirname."_stories")." WHERE published > 0 AND published <= ".time()." AND (expired = 0 OR expired >= ".time()." )";
	}else{
		$sql = "SELECT storyid,uid,title,published FROM ".$xoopsDB->prefix($mydirname."_stories")." WHERE published > 0 AND published <= ".time()." AND (expired = 0 OR expired >= ".time()." )";
	}
	
	if ( $userid != 0 ) {
		$sql .= " AND uid=".$userid." ";
	}
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((hometext LIKE '%$queryarray[0]%' OR bodytext LIKE '%$queryarray[0]%' OR title LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(hometext LIKE '%$queryarray[$i]%' OR bodytext LIKE '%$queryarray[$i]%' OR title LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= "ORDER BY published DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
	
	$myts =& MyTextSanitizer::getInstance();
	
	
 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['image'] = 'images/forum.gif';
		$ret[$i]['link']  = 'index.php?page=article&amp;storyid='.$myrow['storyid'];
		$ret[$i]['title'] = $myrow['title'];
		$ret[$i]['time']  = $myrow['published'];
		$ret[$i]['uid']   = $myrow['uid'];
		if( !empty( $myrow['hometext'] ) ){
			$context = $myrow['hometext'].$myrow['bodytext'];
			$context = strip_tags($myts->displayTarea(strip_tags($context),$myrow['html'],$myrow['smiley'],1));
			$ret[$i]['context'] = search_make_context($context,$queryarray);
		}
		$i++;
	}
	return $ret;
}

}
?>