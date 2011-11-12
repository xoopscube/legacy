<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //
include dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
$myts =& MyTextSanitizer::getInstance() ;

if( ! ( $global_perms & GNAV_GPERM_RATEVOTE ) ) {
	redirect_header(XOOPS_URL.'/index.php', 1, _NOPERM);
	exit ;
}

$lid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;

if( ! empty( $_POST['submit'] ) ) {

	$ratinguser = $my_uid ;

	//Make sure only 1 anonymous from an IP in a single day.
	$anonwaitdays = 1 ;
	$ip = getenv( "REMOTE_ADDR" ) ;
	$lid = intval( $_POST['lid'] ) ;
	$rating = intval( $_POST['rating'] ) ;
	// Check if rating is valid
	if( $rating <= 0 || $rating > 10 ) {
		redirect_header( "index.php?page=vote&lid=$lid" , 4 , _MD_GNAV_MSG_NORATING ) ;
		exit ;
	}

	if( $ratinguser != 0 ) {

		// Check if Photo POSTER is voting
		$rs = $xoopsDB->query( "SELECT COUNT(*) FROM $table_photos WHERE lid=$lid AND submitter=$ratinguser" ) ;
		list( $is_my_photo ) = $xoopsDB->fetchRow( $rs ) ;
		if( $is_my_photo ) {
			redirect_header( "index.php" , 4 , _MD_GNAV_MSG_CANTVOTEOWN ) ;
			exit ;
		}

		// Check if REG user is trying to vote twice.
		$rs = $xoopsDB->query( "SELECT COUNT(*) FROM $table_votedata WHERE lid=$lid AND ratinguser=$ratinguser" ) ;
		list( $has_already_rated ) = $xoopsDB->fetchRow( $rs ) ;
		if( $has_already_rated ) {
			redirect_header( "index.php" , 4 , _MD_GNAV_MSG_VOTEONCE2 ) ;
			exit ;
		}

	} else {
		// Check if ANONYMOUS user is trying to vote more than once per day.
		$yesterday = ( time() - (86400 * $anonwaitdays ) ) ;
		$rs = $xoopsDB->query( "SELECT COUNT(*) FROM $table_votedata WHERE lid=$lid AND ratinguser=0 AND ratinghostname='$ip' AND ratingtimestamp > $yesterday");
		list( $anonvotecount ) = $xoopsDB->fetchRow( $rs ) ;
		if( $anonvotecount ) {
			redirect_header( "index.php" , 4 , _MD_GNAV_MSG_VOTEONCE2 ) ;
			exit ;
		}
	}

	// All is well.  Add to Line Item Rate to DB.
	$newid = $xoopsDB->genId( $table_votedata . "_ratingid_seq" ) ;
	$datetime = time() ;
	$xoopsDB->query( "INSERT INTO $table_votedata (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES ($newid, $lid, $ratinguser, $rating, '$ip', $datetime)") or die( "DB error: INSERT votedata table" ) ;

	//All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
	gnavi_updaterating( $lid ) ;
	$ratemessage = _MD_GNAV_RAT_VOTEAPPRE."<br />".sprintf( _MD_GNAV_RAT_THANKURATE , $xoopsConfig['sitename'] ) ;
	if( ! empty( $_SESSION["{$mydirname}_uri4return"] ) ) {
		redirect_header( $_SESSION["{$mydirname}_uri4return"] , 2 , $ratemessage ) ;
		unset( $_SESSION["{$mydirname}_uri4return"] ) ;
	} else {
		redirect_header( "index.php" , 2 , $ratemessage ) ;
	}
	exit ;

} else {

	$xoopsOption['template_main'] = "{$mydirname}_vote.html" ;
	include( XOOPS_ROOT_PATH."/header.php" ) ;

	// store the referer
	if( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
		$_SESSION["{$mydirname}_uri4return"] = $_SERVER['HTTP_REFERER'] ;
	}

	$xoops_module_header = $xoopsTpl->get_template_vars( "xoops_module_header" ) ."\n" ."<link rel='stylesheet' type='text/css' href='css/gnavi.css'/>";
	$xoopsTpl->assign('xoops_module_header',$xoops_module_header);

	$result = $xoopsDB->query( "SELECT l.lid, l.cid,l.cid1,l.cid2,l.cid3,l.cid4, l.title,l.caption,l.caption1,l.caption2, l.poster_name,l.icd,l.url,l.tel,l.fax,l.zip,l.address,l.rss,l.lat,l.lng,l.zoom,l.mtype, l.ext,l.ext1,l.ext2, l.res_x, l.res_y,l.res_x1, l.res_y1,l.res_x2, l.res_y2, l.status, l.date, l.hits, l.rating, l.votes, l.comments, l.submitter, t.description,t.arrowhtml,t.addinfo FROM $table_photos l LEFT JOIN $table_text t ON l.lid=t.lid WHERE l.lid=$lid" ) ;
	$photo = $xoopsDB->fetchArray( $result ) ;

	// Display
	$photo = gnavi_get_array_for_photo_assign( $photo ) ;
	$photo = gnavi_photo_assign($photo);
	$xoopsTpl->assign( 'photo' ,$photo ) ;
	$xoopsTpl->assign( $gnavi_assign_globals ) ;

	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	$xoops_breadcrumbs[] = array( 'name' => _MD_GNAV_RAT_VOTE ) ;
	$xoopsTpl->assign( 'xoops_breadcrumbs' , $xoops_breadcrumbs) ;

	$xoopsTpl->assign( 'lang_voteonce' , _MD_GNAV_RAT_VOTEONCE ) ;
	$xoopsTpl->assign( 'lang_ratingscale' , _MD_GNAV_RAT_RATINGSCALE ) ;
	$xoopsTpl->assign( 'lang_beobjective' , _MD_GNAV_RAT_BEOBJECTIVE ) ;
	$xoopsTpl->assign( 'lang_donotvote' , _MD_GNAV_RAT_DONOTVOTE ) ;
	$xoopsTpl->assign( 'lang_rateit' , _MD_GNAV_RAT_RATEIT ) ;
	$xoopsTpl->assign( 'lang_cancel' , _CANCEL ) ;

	include( XOOPS_ROOT_PATH . "/footer.php" ) ;

}
?>