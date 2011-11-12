<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //
//UPDATED BY KEN

include dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;

$myts =& MyTextSanitizer::getInstance() ; // MyTextSanitizer object
include_once( XOOPS_ROOT_PATH."/class/xoopstree.php" ) ;

$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;

$xoopsOption['template_main'] = "{$mydirname}_topten.html" ;

include( XOOPS_ROOT_PATH . "/header.php" ) ;

$xoopsTpl->assign( $gnavi_assign_globals ) ;

//generates top 10 charts by rating and hits for each main category

if( ! empty( $_GET['rate'] ) && ( $global_perms & GNAV_GPERM_RATEVIEW ) && $gnavi_usevote ) {
	$lang_sortby = _MD_GNAV_RAT_RATING;
	$odr = "rating DESC";
} else {
	$lang_sortby = _MD_GNAV_RAT_HITS;
	$odr = "hits DESC";
}

$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
$xoops_breadcrumbs[] = array( 'name' => sprintf( _MD_GNAV_RAT_TOP10 , $myts->htmlSpecialChars( $lang_sortby )) ) ;
$xoopsTpl->assign( 'xoops_breadcrumbs' , $xoops_breadcrumbs) ;

$xoopsTpl->assign( 'lang_sortby' , $lang_sortby ) ;
$xoopsTpl->assign( 'lang_rank' , _MD_GNAV_RAT_RANK ) ;
$xoopsTpl->assign( 'lang_title' , _MD_GNAV_ITM_TITLE ) ;
$xoopsTpl->assign( 'lang_category' , _MD_GNAV_RAT_CATEGORY ) ;
$xoopsTpl->assign( 'lang_hits' , _MD_GNAV_RAT_HITS ) ;
$xoopsTpl->assign( 'lang_rating' , _MD_GNAV_RAT_RATING ) ;
$xoopsTpl->assign( 'lang_vote' , _MD_GNAV_RAT_VOTE ) ;

$crs = $xoopsDB->query( "SELECT cid,title FROM $table_cat WHERE pid=0 ORDER BY weight,title" ) ;
$rankings = array() ;
$i = 0;
while( list( $cid , $cat_title ) = $xoopsDB->fetchRow( $crs ) ) {

	$rankings[$i] = array(
		'title' => sprintf( _MD_GNAV_RAT_TOP10 , $myts->htmlSpecialChars( $cat_title ) ) ,
		'count' => $i
	) ;

	// get all child cat ids for a given cat id
	$children = $cattree->getAllChildId( $cid ) ;

	//$whr_cid = 'cid IN (' ;
	//foreach( $children as $child ) {
	//	$whr_cid .= "$child," ;
	//}
	//$whr_cid .= "$cid)" ;

	$whr = "";
	foreach( $children as $child ) {
		$whr .= "$child," ;
	}
	$whr .= $cid ;

	$whr_cid = "cid IN($whr) or cid1 IN($whr) or cid2 IN($whr) or cid3 IN($whr) or cid4 IN($whr)";

	array_push($children, $cid);

	$sql = "SELECT lid, cid,cid1,cid2,cid3,cid4, title, poster_name , submitter, hits, rating, votes FROM $table_photos WHERE status>0 AND ($whr_cid) ORDER BY $odr";
	$prs = $xoopsDB->query( $sql , 10 , 0 ) ;
	$rank = 1 ;
	while( list ( $lid , $cid ,$cid1 ,$cid2 ,$cid3 ,$cid4 , $title , $poster_name , $submitter , $hits , $rating , $votes ) = $xoopsDB->fetchRow( $prs ) ) {

		$mycid=$cid;
		if(in_array($cid4, $children))$mycid=$cid4;
		if(in_array($cid3, $children))$mycid=$cid3;
		if(in_array($cid2, $children))$mycid=$cid2;
		if(in_array($cid1, $children))$mycid=$cid1;
		if(in_array($cid, $children))$mycid=$cid;


		$catpath = $cattree->getPathFromId( $mycid , "title" ) ;
		$catpath = substr( $catpath , 1 ) ;
		$catpath = str_replace( "/" , " <span class='fg2'>&raquo;&raquo;</span> " , $catpath ) ;
		$title = $myts->makeTboxData4Show( $title ) ;

		if ($submitter>0){
				$poster_name = gnavi_get_name_from_uid( $submitter );
		}

		$rankings[$i]['photo'][] = array( 'lid' => $lid , 'cid' => $cid , 'rank' => $rank , 'title' => $title , 'submitter' => $submitter , 'submitter_name' => $poster_name , 'category' => $catpath , 'hits' => $hits , 'rating' => number_format( $rating , 2) , 'votes' => $votes ) ;
		$rank ++ ;
	}

	$i++ ;
}

$xoopsTpl->assign_by_ref( 'rankings' , $rankings ) ;

include( XOOPS_ROOT_PATH . "/footer.php" ) ;

?>