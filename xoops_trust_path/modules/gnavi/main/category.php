<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //

include_once dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
require_once dirname(dirname(__FILE__)).'/include/item_orders.php' ;
include_once XOOPS_ROOT_PATH . '/class/pagenav.php' ;

$myts =& MyTextSanitizer::getInstance() ; // MyTextSanitizer object
$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;

//GET
$cid = empty( $_GET['cid'] ) ? 0 : intval( $_GET['cid'] ) ;
$month = empty( $_GET['m'] ) ? "" : (strlen($_GET['m'])==6 ? $_GET['m'] : "" ) ;
$uid = empty( $_GET['uid'] ) ? 0 : intval( $_GET['uid'] ) ;
$pos = empty( $_GET['pos'] ) ? 0 : intval( $_GET['pos'] ) ;
$view = empty( $_GET['view'] ) ? $gnavi_viewcattype : $_GET['view'] ;
$num = empty( $_GET['num'] ) ? intval( $gnavi_newphotos ) : intval( $_GET['num'] ) ;
$num = $num < 1 ?  10 : $num ;

if( isset( $_GET['orderby'] ) && isset( $gnavi_orders[ $_GET['orderby'] ] ) ) $orderby = $_GET['orderby'] ;
else if( isset( $gnavi_orders[ $gnavi_defaultorder ] ) ) $orderby = $gnavi_defaultorder ;
else $orderby = 'titleA' ;

//Set template
if( $view == 'table' ) {
	$xoopsOption['template_main'] = "{$mydirname}_viewcat_table.html" ;
} else {
	$xoopsOption['template_main'] = "{$mydirname}_viewcat_list.html" ;
}
	$function_assigning = 'gnavi_get_array_for_photo_assign' ;

include XOOPS_ROOT_PATH . "/header.php" ;

//Assign
$xoopsTpl->assign( $gnavi_assign_globals ) ;
if($gnavi_usegooglemap){
	$xoopsTpl->assign( 'map' , _MD_GNAV_MAP_SHOW ) ;
}

$xoopsTpl->assign('cat_link',$mod_url."/index.php?".( $page_cat ? $page_cat.'&':''));

if( $global_perms & GNAV_GPERM_INSERTABLE ) $xoopsTpl->assign( 'lang_add_photo' , _MD_GNAV_CAT_ADDITEM ) ;

//module header
$xoops_module_header = $xoopsTpl->get_template_vars( "xoops_module_header" ) ."\n" ."<link rel='stylesheet' type='text/css' href='css/gnavi.css'/>";
$xoopsTpl->assign('xoops_module_header',$xoops_module_header);


//make data by GET values
if( $cid > 0 ) {

	$rs = $xoopsDB->query( "SELECT title,imgurl,description,pid FROM $table_cat WHERE cid='$cid'" ) ;
	list( $cat_title,$imgurl,$description,$pid ) = $xoopsDB->fetchRow( $rs ) ;

	$pagetitle =  $myts->makeTboxData4show( $cat_title ) ;

	// Category Specified

	//assign category description ->  "category_desc"
	if( $gnavi_body_editor == 'common_fckeditor' ||
		($gnavi_body_editor == 'common_spaw' && file_exists( XOOPS_ROOT_PATH.'/common/spaw/spaw_control.class.php' )) ||
		$gnavi_body_editor == 'pure_html'){
		$arrow_html=1;
		$arrow_br=0;
	} else {
		$arrow_html=0;
		$arrow_br=1;
	}
	$xoopsTpl->assign( 'category_desc' , $myts->displayTarea( $description , $arrow_html , 1 , 1 , 1 , $arrow_br , 1 ) ) ;
	$imgurl= preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $imgurl) ? $imgurl :"";
	$xoopsTpl->assign( 'category_imgurl' , $imgurl ) ;

	$xoopsTpl->assign( 'parent_id' , $pid ) ;
	$xoopsTpl->assign( 'category_id' , $cid ) ;
	$xoopsTpl->assign( 'subcategories' , gnavi_get_sub_categories( $cid , $cattree ) ) ;
	$xoopsTpl->assign( 'category_options' , gnavi_get_cat_options() ) ;



	$cids = $cattree->getAllChildId( $cid ) ;
	array_push( $cids , $cid ) ;

	//counts
	$photo_num_total = gnavi_get_photo_total_sum_from_cats( $cids , "status>0" ) ;
	$xoopsTpl->assign( 'photo_num_total' , $photo_num_total.'&nbsp;'._MD_GNAV_C_DATA ) ;

	if($gnavi_showparent){

		$where = "";
		foreach( $cids as $cidw ) {
			$where .= "$cidw," ;
		}
		$where =substr($where, 0, -1);
		$where = "(cid IN($where) or cid1 IN($where) or cid2 IN($where) or cid3 IN($where) or cid4 IN($where) )";


	}else{

		$where = "( cid=$cid or cid1=$cid or cid2=$cid or cid3=$cid or cid4=$cid )";

	}

	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE $where AND status>0" ) ;
	list( $photo_small_sum ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_small_sum' , $photo_small_sum ) ;

	//breadcrumbs
	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	if($gnavi_indexpage=='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=category' , 'name' => _MD_GNAV_CAT_ITEMLIST ) ;
	}
	$xoops_breadcrumbs = gnavi_add_breadcrumbs( $pid , "index.php".( $page_cat ? '?'.$page_cat:''),$xoops_breadcrumbs);
	$xoops_breadcrumbs[] = array( 'name' => $myts->makeTboxData4show( $cat_title )) ;
	$xoopsTpl->assign( 'imgurl' , $imgurl ) ;
	$xoopsTpl->assign( 'description' , $description ) ;

	$get_append = "cid=$cid";

} else if( $uid != 0 ) {

	if( $uid < 0 ) {
		// This means 'my photo'
		$where = "submitter=$my_uid" ;
		$get_append = "uid=-1" ;
		$xoopsTpl->assign( 'uid' , -1 ) ;
		$pagetitle = _MD_GNAV_CAT_MYPOST  ;
	} else {
		// uid Specified
		$where = "submitter=$uid" ;
		$get_append = "uid=$uid" ;
		$xoopsTpl->assign( 'uid' , $uid ) ;
		$pagetitle = sprintf( _MD_GNAV_CAT_MOREPHOTOS,gnavi_get_name_from_uid( $uid )) ;
	}

	//count
	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE $where AND status>0" ) ;
	list( $photo_num_total ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_num_total' , $photo_num_total.'&nbsp;'._MD_GNAV_C_DATA ) ;
	$photo_small_sum = $photo_num_total ;

	//breadcrumbs
	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	if($gnavi_indexpage=='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=category' , 'name' => _MD_GNAV_CAT_ITEMLIST ) ;
	}
	$xoops_breadcrumbs[] = array( 'name' => $pagetitle ) ;

} else if( $month ) {

	$gettime=intval(strtotime(substr($month,0,4)."-".substr($month,4,2)."-01"));
	$getetime=intval(strtotime(( intval(substr($month,4,2))==12 ? intval(substr($month,0,4))+1 : intval(substr($month,0,4)) )."-".( intval(substr($month,4,2))==12 ? 1 : intval(substr($month,4,2))+1)."-01"))+24*60*60;

	// uid Specified
	$where = "date>=$gettime AND $getetime>date" ;
	$get_append = "m=$month" ;
	$_year = date("Y",$gettime);
	$_month = date("m",$gettime);
	$pagetitle = sprintf( constant('_MD_GNAV_ARCH_POSTMONTH'), $_year, $_month ) ;

	//count
	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE $where " ) ;
	list( $photo_num_total ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_num_total' , $photo_num_total.'&nbsp;'._MD_GNAV_C_DATA ) ;
	$photo_small_sum = $photo_num_total ;

	//breadcrumbs
	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	if($gnavi_indexpage=='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=category' , 'name' => _MD_GNAV_CAT_ITEMLIST ) ;
	}
	$xoops_breadcrumbs[] = array( 'name' => $pagetitle ) ;

} else {

	$xoopsTpl->assign( 'indexpage' , true ) ;
	//count
	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE status>0" ) ;
	list( $photo_num_total ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_num_total' , sprintf( _MD_GNAV_CAT_THEREARE , $photo_num_total ) ) ;
	$photo_small_sum = $photo_num_total ;

	//breadcrumbs
	if($gnavi_indexpage=='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
		$xoops_breadcrumbs[] = array( 'name' => _MD_GNAV_CAT_ITEMLIST ) ;
		$pagetitle = _MD_GNAV_CAT_ITEMLIST ;
	}else{
		$xoops_breadcrumbs[] = array( 'name' => $xoopsModule->getVar( 'name' ) ) ;
		$pagetitle = $xoopsModule->getVar('name') ;
	}
	$xoopsTpl->assign( 'subcategories' , gnavi_get_sub_categories( 0 , $cattree ) ) ;
	$xoopsTpl->assign( 'category_options' , gnavi_get_cat_options() ) ;

	if($gnavi_mobile_useqr){
		if(!is_file("$qrimg_dir/index.png")){
			gnavi_check_folders();
			if(is_file($gnavi_qrcode_path)){
				require_once $gnavi_qrcode_path ;
				$qrimage=new Qrcode_image;
				$qrimage->set_module_size($gnavi_mobile_useqr);
				$qrimage->qrcode_image_out( "$mod_url/","png","$qrimg_dir/index.png");
				$xoopsTpl->assign( 'qrimg' , "$qrimg_url/index.png" ) ;
			}
		}else{
			$xoopsTpl->assign( 'qrimg' , "$qrimg_url/index.png" ) ;
		}
	}

	$where = "cid > 0";
	$get_append = "";
}

//more assign
$xoopsTpl->assign( 'xoops_breadcrumbs' , $xoops_breadcrumbs) ;

$xoopsTpl->assign( 'link_option' , ($get_append ? "?".$get_append : '' )) ;
$mapget_append = $page_map ? ( $get_append ? $get_append.'&'.$page_map : $page_map ) : $get_append ;
$xoopsTpl->assign( 'maplink_option' , ($mapget_append ? "?".$mapget_append : '' )) ;

$get_append = $page_cat ? ( $get_append ? $get_append.'&'.$page_cat : $page_cat ) : $get_append ;
$get_append = $view == $gnavi_viewcattype ? $get_append : ( $get_append ? $get_append.'&' : '' )."view=".$view ;
$xoopsTpl->assign( 'catlink_option' , ($get_append ? "&".$get_append : '' )) ;

$xoopsTpl->assign( 'album_sub_title' , $pagetitle) ;
$xoopsTpl->assign( 'xoops_pagetitle' , $pagetitle) ;
$xoopsTpl->assign( 'photo_small_sum' , $photo_small_sum ) ;

$xoopsTpl->assign(array(
	"lng_show_mobile"=>_MD_GNAV_SHOW_MOBILE,
	"lng_send_mobile"=>_MD_GNAV_SEND_MOBILE,
	"lng_mobile_updir"=>_MD_GNAV_MOBILE_UPDIR,
	)) ;


if( $photo_small_sum > 0 ) {

	$prs = $xoopsDB->query( "SELECT l.lid, l.cid,l.cid1,l.cid2,l.cid3,l.cid4, l.title, l.poster_name,l.icd, l.ext, l.res_x, l.res_y, l.ext1, l.res_x1, l.res_y1,l.ext2, l.res_x2, l.res_y2, l.status, l.date, l.hits, l.rating, l.votes, l.comments,l.caption,l.caption1,l.caption2, l.submitter,l.url,l.tel,l.fax,l.zip,l.address,l.rss,l.lat,l.lng,l.zoom,l.mtype,  t.description,t.arrowhtml,t.addinfo FROM $table_photos l USE INDEX (date) INNER JOIN $table_text t ON l.lid=t.lid WHERE $where AND l.status>0 ORDER BY {$gnavi_orders[$orderby][0]}" , $num , $pos ) ;
	if( ! $prs ) {
		$prs = $xoopsDB->query( "SELECT l.lid, l.cid,l.cid1,l.cid2,l.cid3,l.cid4, l.title, l.poster_name,l.icd, l.ext, l.res_x, l.res_y, l.ext1, l.res_x1, l.res_y1,l.ext2, l.res_x2, l.res_y2, l.status, l.date, l.hits, l.rating, l.votes, l.comments,l.caption,l.caption1,l.caption2, l.submitter,l.url,l.tel,l.fax,l.zip,l.address,l.rss,l.lat,l.lng,l.zoom,l.mtype,  t.description,t.arrowhtml,t.addinfo FROM $table_photos l INNER JOIN $table_text t ON l.lid=t.lid WHERE $where AND l.status>0 ORDER BY {$gnavi_orders[$orderby][0]}" , $num , $pos ) ;
	}

	//if 2 or more items in result, num the navigation menu
	if( $photo_small_sum > 1 ) {

		// Assign navigations like order and division
		$xoopsTpl->assign( 'show_nav' ,  true ) ;
		$xoopsTpl->assign( 'lang_sortby' , _MD_GNAV_ODR_SORTBY ) ;
		$xoopsTpl->assign( 'lang_title' , _MD_GNAV_ITM_TITLE ) ;
		$xoopsTpl->assign( 'lang_date' , _MD_GNAV_ODR_DATE ) ;
		$xoopsTpl->assign( 'lang_rating' , _MD_GNAV_RAT_RATING ) ;
		$xoopsTpl->assign( 'lang_popularity' , _MD_GNAV_ODR_POPULARITY ) ;
		$xoopsTpl->assign( 'lang_cursortedby' , sprintf( _MD_GNAV_ODR_CURSORTEDBY , $gnavi_orders[$orderby][1] ) ) ;
		$xoopsTpl->assign( 'my_order' , $orderby ) ;

		$nav = new XoopsPageNav( $photo_small_sum , $num , $pos , 'pos' , "$get_append&num=$num&orderby=$orderby" ) ;
		$nav_html = $nav->renderNav(7) ;
		$last = $pos + $num ;
		if( $last > $photo_small_sum ) $last = $photo_small_sum ;
		$photonavinfo = sprintf( _MD_GNAV_NAV_ITEMPOS , $pos + 1 , $last , $photo_small_sum ) ;

		//customize navihtml
		$nav_html = preg_replace( "%<u>&laquo;</u>%" , _MD_GNAV_NAV_PREVIOUS , $nav_html ) ;
		if (!preg_match("%<u>&raquo;</u>%", $nav_html)){
			if($nav_html)$nav_html .= " "._MD_GNAV_NAV_NEXT ;
		}else{
			$nav_html = preg_replace( "%<u>&raquo;</u>%" , _MD_GNAV_NAV_NEXT , $nav_html ) ;
		}
		$nav_html = preg_replace( "%<b>\(1\)</b>%" ,_MD_GNAV_NAV_PREVIOUS." <b>(1)</b>" , $nav_html ) ;
		$nav_html = preg_replace( "%<b>\((.+?)\)</b>%" ,"<span>\\1</span>" , $nav_html ) ;


		$xoopsTpl->assign( 'photonav' , $nav_html ) ;
		$xoopsTpl->assign( 'photonavinfo' , $photonavinfo ) ;
	}

	// Display photos
	$count = 1 ;
	while( $fetched_result_array = $xoopsDB->fetchArray( $prs ) ) {
		$photo = $function_assigning( $fetched_result_array ) + array( 'count' => $count ++ , true ) ;
		$xoopsTpl->append( 'photos' , $photo ) ;
	}
}

// comments
if($GNAVI_MOBILE){
	gnavi_mobile_templete_disp("db:{$mydirname}_mobile_viewcat.html");
}else{
	include( XOOPS_ROOT_PATH . "/footer.php" ) ;
}

?>