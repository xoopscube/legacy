<?php

eval( '

function '.$mydirname.'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	return gnavi_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'gnavi_global_search_base' ) ) {

function gnavi_global_search_base( $mydirname , $keywords , $andor , $limit , $offset , $userid )
{
	// not implemented for uid specifications
	//if( ! empty( $userid ) ) {
	//	return array() ;
	//}

	$db =& Database::getInstance() ;

	// XOOPS Search module
	$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1 ;
	$select4con = $showcontext ? "t.description" : "'' AS description" ;

	$sql = "SELECT l.lid,l.cid,l.title,l.caption,l.caption1,l.caption2,l.poster_name,l.submitter,l.date,$select4con FROM ".$db->prefix($mydirname."_photos")." l LEFT JOIN ".$db->prefix($mydirname."_text")." t ON t.lid=l.lid LEFT JOIN ".$db->prefix("users")." u ON u.uid=l.submitter WHERE status>0" ;

	if( $userid > 0 ) {
		$sql .= " AND l.submitter=".$userid." ";
	}

	$whr = "" ;
	if( is_array( $keywords ) && count( $keywords ) > 0 ) {
		$whr = "AND (" ;
		switch( strtolower( $andor ) ) {
			case "and" :
				foreach( $keywords as $keyword ) {
					$whr .= "CONCAT(l.title,' ',l.caption,' ',l.caption1,' ',l.caption2,' ',t.description,' ',t.addinfo,' ',IFNULL(u.uname,''),' ',l.poster_name) LIKE '%$keyword%' AND " ;
				}
				$whr = substr( $whr , 0 , -5 ) ;
				break ;
			case "or" :
				foreach( $keywords as $keyword ) {
					$whr .= "CONCAT(l.title,' ',l.caption,' ',l.caption1,' ',l.caption2,' ',t.description,' ',t.addinfo,' ',IFNULL(u.uname,''),' ',l.poster_name) LIKE '%$keyword%' OR " ;
				}
				$whr = substr( $whr , 0 , -4 ) ;
				break ;
			default :
				$whr .= "CONCAT(l.title,' ',l.caption,' ',l.caption1,' ',l.caption2,' ',t.description,' ',t.addinfo,' ',IFNULL(u.uname,''),' ',l.poster_name) LIKE '%{$keywords[0]}%'" ;
				break ;
		}
		$whr .= ")" ;
	}

	$sql = "$sql $whr ORDER BY l.date DESC";
	$result = $db->query( $sql , $limit , $offset ) ;
	$ret = array() ;
	$context = '' ;
	while( $myrow = $db->fetchArray($result) ) {

		// get context for module "search"
		if( function_exists( 'search_make_context' ) && $showcontext ) {
			$full_context = strip_tags( $myrow['description'] ) ;
			if( function_exists( 'easiestml' ) ) $full_context = easiestml( $full_context ) ;
			$context = search_make_context( $full_context , $keywords ) ;
		}

		$ret[] = array(
			"image" => "images/pict.gif" ,
			"link" => "index.php?lid=".$myrow["lid"] ,
			"title" => $myrow["title"] ,
			"time" => $myrow["date"] ,
			"uid" => $myrow["submitter"] ,
			"context" => $context
		) ;
	}
	return $ret;
}

}


?>