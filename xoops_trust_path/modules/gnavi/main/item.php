<?php
// ------------------------------------------------------------------------- //
//                      GNAVI - XOOPS area guide +                           //
//                        <http://xoops.iko-ze.net/>                         //
//                 based on  myAlbum-P - XOOPS photo album                   //
//                        <http://www.peak.ne.jp/>                           //
// ------------------------------------------------------------------------- //
include_once dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
$myts =& MyTextSanitizer::getInstance() ; // MyTextSanitizer object
$cattree = new XoopsTree( $table_cat , "cid" , "pid" ) ;

include XOOPS_ROOT_PATH . "/header.php" ;

//d3forum or xoops comments
if(@$gnavi_configs['com_rule'])include_once XOOPS_ROOT_PATH.'/include/comment_view.php';

//Hook Constant Value::templete assign for print
$myprint = @$_GET['page']=="print";
if($myprint){
	$gnavi_middlepixel="600x600";
	$gnavi_liquidimg=1;
}

$lid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;
$cid = empty( $_GET['cid'] ) ? 0 : intval( $_GET['cid'] ) ;

$xoopsTpl->assign( $gnavi_assign_globals ) ;

if( $global_perms & GNAV_GPERM_INSERTABLE ) $xoopsTpl->assign( 'lang_add_photo' , _MD_GNAV_CAT_ADDITEM ) ;

// update hit count
$xoopsDB->queryF( "UPDATE $table_photos SET hits=hits+1 WHERE lid='$lid' AND status>0" ) ;

$prs = $xoopsDB->query( "SELECT l.lid, l.cid,l.cid1,l.cid2,l.cid3,l.cid4, l.title, l.poster_name,l.icd, l.ext, l.res_x, l.res_y,l.ext1, l.res_x1, l.res_y1,l.ext2, l.res_x2, l.res_y2, l.caption,l.caption1,l.caption2, l.status, l.date, l.hits, l.rating, l.votes, l.comments, l.submitter,l.url,l.tel,l.fax,l.zip,l.address,l.rss,l.lat,l.lng,l.zoom,l.mtype,t.description,t.arrowhtml,t.addinfo FROM $table_photos l LEFT JOIN $table_text t ON l.lid=t.lid WHERE l.lid=$lid AND status>0" ) ;
$photo = $xoopsDB->fetchArray( $prs ) ;
if( $photo == false ) {
	redirect_header( $mod_url.'/' , 3 , _MD_GNAV_MSG_NOMATCH ) ;
	exit ;
}
$photo = gnavi_get_array_for_photo_assign( $photo ) ;
$photo = gnavi_photo_assign($photo);
$xoopsTpl->assign( 'lang_rating' , _MD_GNAV_RAT_RATINGI ) ;
// <title></title>
$xoopsTpl->assign( 'xoops_pagetitle' , $photo['title'] ) ;
$xoopsTpl->assign_by_ref( 'photo' , $photo ) ;




if($gnavi_mobile_useqr){
	if(!is_file("$qrimg_dir/$lid.png")){
		if(is_file($gnavi_qrcode_path)){
			require_once $gnavi_qrcode_path ;
			$qrimage=new Qrcode_image;
			$qrimage->set_module_size($gnavi_mobile_useqr); 
			$qrimage->qrcode_image_out( "$mod_url/?lid=$lid","png","$qrimg_dir/$lid.png");
			$xoopsTpl->assign( 'qrimg' , "$qrimg_url/$lid.png" ) ;
		}
	}else{
		$xoopsTpl->assign( 'qrimg' , "$qrimg_url/$lid.png" ) ;
	}
}

// Category Information
if($cid){
	if(intval( $photo['cid'])!=$cid && intval( $photo['cid1'])!=$cid && intval( $photo['cid2'])!=$cid && intval( $photo['cid3'])!=$cid && intval( $photo['cid4'])!=$cid)
	$cid = empty( $photo['cid'] ) ? $cid : $photo['cid'] ;
}else{
	$cid = empty( $photo['cid'] ) ? $cid : $photo['cid'] ;
}
if( $cid > 0 ) {
	$rs = $xoopsDB->query( "SELECT title,imgurl,kmlurl,description FROM $table_cat WHERE cid='$cid'" ) ;
	list( $cat_title,$imgurl,$kmlurl,$description ) = $xoopsDB->fetchRow( $rs ) ;
	$xoopsTpl->assign( 'cattitle' , $cat_title ) ;
	$xoopsTpl->assign( 'catimgurl' , $imgurl ) ;
	$xoopsTpl->assign( 'catdescription' , $description ) ;
}

$get_append='cid='.$cid.'&lid='.$lid;
$xoopsTpl->assign( 'link_option' , ($get_append ? "?".$get_append : '' )) ;
$mapget_append = $get_append ? $get_append.'&page=map' : 'page=map' ;
$xoopsTpl->assign( 'maplink_option' , ($mapget_append ? "?".$mapget_append : '' )) ;

$xoopsTpl->assign( 'category_id' , $cid ) ;
$cids = $cattree->getAllChildId( $cid ) ;


//breadcrumbs
$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
$xoops_breadcrumbs= gnavi_add_breadcrumbs( $cid , "index.php?",$xoops_breadcrumbs);
$xoops_breadcrumbs[] = array( 'name' => $photo['title'] ) ;
$xoopsTpl->assign( 'xoops_breadcrumbs' , $xoops_breadcrumbs) ;

//get icon
$icon = $photo['icd'] > 0 ? $photo['icd'] : gnavi_get_icon($cattree,$photo['cid'],$photo['cid1'],$photo['cid2'],$photo['cid3'],$photo['cid4'],$cid);
$arricon = gnavi_get_gicon($icon);

	//kml settings
	$mykmls="";
	if($photo['mykmls']){
		$kmlurl = $kmlurl ? $photo['mykmls'].",'".$kmlurl."'" : $photo['mykmls'] ;
	}else{
		$kmlurl = $kmlurl ? "'".$kmlurl."'" : "" ;
	}
	if($kmlurl){
		$mykmls = "gn_mykmls = new Array(".$kmlurl.");";
	}

//xoops_module_header
$xoops_module_header = $xoopsTpl->get_template_vars( "xoops_module_header" ) ."\n" ."<link rel='stylesheet' type='text/css' href='css/gnavi.css'/>";
if($gnavi_usegooglemap && (floatval($photo['lng'])<>0 || floatval($photo['lat'])<>0 )){
$xoopsTpl->assign( 'map' , _MD_GNAV_MAP_SHOW ) ;
$xoops_module_header .="<script src='".$gnavi_googlemap_url."/maps?file=api&amp;v=2&amp;key=$gnavi_googlemapapi_key' type='text/javascript' charset='utf-8'></script>
<script src='js/map.js' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
//<![CDATA[
	$gnavi_lang_java
	$mykmls
	gn_ilt=".$photo['lat'].";
	gn_ilg=".$photo['lng'].";
	gn_iz=".$photo['zoom'].";
	gn_it='".$photo['mtype']."';
	".$arricon."
	window.onload = ShowItemGMap;
//]]>
</script>";
}

if($gnavi_use_rss>0 && $photo['rss']!=""){
$xoops_module_header .="<script src='http://www.google.com/jsapi?key=$gnavi_googlemapapi_key' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
//<![CDATA[
	var gn_feedlink='".$photo['rss']."';
	var gn_feednum=$gnavi_use_rss;
    google.load('feeds', '1');
    google.setOnLoadCallback(gn_feedLoader);
//]]>
</script>";
}

$xoopsTpl->assign('xoops_module_header',$xoops_module_header);

// meta_description
$gnavi_meta_description = preg_replace('/[\r\n\t]/','',htmlspecialchars(mb_substr(strip_tags($photo['description']),0,120, _CHARSET),ENT_QUOTES)); // naao

$xoopsTpl->assign('xoops_meta_description', $gnavi_meta_description);

// Orders
require_once dirname(dirname(__FILE__)).'/include/item_orders.php' ;

if( isset( $_GET['orderby'] ) && isset( $gnavi_orders[ $_GET['orderby'] ] ) ) $orderby = $_GET['orderby'] ;
else if( isset( $gnavi_orders[ $gnavi_defaultorder ] ) ) $orderby = $gnavi_defaultorder ;
else $orderby = 'lidA' ;

// create category navigation
$fullcountresult = $xoopsDB->query( "SELECT lid FROM $table_photos WHERE (cid=$cid OR cid1=$cid OR cid2=$cid OR cid3=$cid OR cid4=$cid) AND status>0 ORDER BY {$gnavi_orders[$orderby][0]}" ) ;
$ids = array() ;
while( list( $id ) = $xoopsDB->fetchRow( $fullcountresult ) ) {
	$ids[] = $id ;
}


$photo_nav = "" ;
$numrows = count( $ids ) ;
$xoopsTpl->assign( 'cat_small_sum' , $numrows ) ;
$pos = array_search( $lid , $ids ) ;
if( $numrows > 1 ) {

	$nwin = 7 ; // show count
	if( $numrows > $nwin ) { // window
		if( $pos > $nwin / 2 ) {
			if( $pos > round( $numrows - ( $nwin / 2 ) - 1 ) ) {
				$start = $numrows - $nwin + 1 ;
			} else {
				$start = round( $pos - ( $nwin / 2 ) ) + 1 ;
			}
		} else {
			$start = 1 ;
		}
	} else {
		$start = 1 ;
	}
	
	for( $i = $start; $i < $numrows + 1 && $i < $start + $nwin ; $i++ ) {
		if( $ids[$i-1] == $lid ) {
			$photo_nav .= "<span>$i</span>&nbsp;&nbsp;";
		} else {
			$photo_nav .= "<a href='index.php?lid=".$ids[$i-1]."&cid=$cid'>$i</a>&nbsp;&nbsp;";
		}
	}

	if( $start > 2 ){
		$photo_nav = "<a href='index.php?lid=".$ids[0]."&cid=$cid'>1</a>&nbsp;..&nbsp;&nbsp;".$photo_nav;
	}elseif( $start == 2 ){
		$photo_nav = "<a href='index.php?lid=".$ids[0]."&cid=$cid'>1</a>&nbsp;&nbsp;".$photo_nav;
	}

	if( $start+$nwin < $numrows ){
		$photo_nav .= "..&nbsp;<a href='index.php?lid=".$ids[$numrows-1]."&cid=$cid'>".$numrows."</a>&nbsp;&nbsp;" ;
	}elseif( $start+$nwin == $numrows ){
		$photo_nav .= "<a href='index.php?lid=".$ids[$numrows-1]."&cid=$cid'>".$numrows."</a>&nbsp;&nbsp;" ;
	}

	// prev mark
	if( $ids[0] != $lid ) {
		$photo_nav = "<a href='index.php?lid=".$ids[$pos-1]."&cid=$cid'>"._MD_GNAV_NAV_PREVIOUS."</a>&nbsp;&nbsp;".$photo_nav;
	}else{
		$photo_nav =_MD_GNAV_NAV_PREVIOUS."&nbsp;&nbsp;".$photo_nav;
	}

	// next mark
	if( $ids[$numrows-1] != $lid ) {
		$photo_nav .= "<a href='index.php?lid=".$ids[$pos+1]."&cid=$cid'>"._MD_GNAV_NAV_NEXT."</a>&nbsp;&nbsp;" ;
	}else{
		$photo_nav .=_MD_GNAV_NAV_NEXT;
	}

	$photo_nav = sprintf(_MD_GNAV_NAV_MOVE,$numrows).$photo_nav ;
}

$xoopsTpl->assign( 'photo_nav' , $photo_nav ) ;
$xoopsTpl->assign(array(
	"lng_show_mobile"=>_MD_GNAV_SHOW_MOBILE,
	"lng_send_mobile"=>_MD_GNAV_SEND_MOBILE,
	)) ;

if ($gnavi_meta_description) {
	// For XCL 2.2 Call addMeta
	if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
		$xclRoot =& XCube_Root::getSingleton();
		$headerScript = $xclRoot->mContext->getAttribute('headerScript');
		$headerScript->addMeta('description', $gnavi_meta_description);
	} elseif (isset($xoTheme) && is_object($xoTheme)) {	// for XOOPS 2.3 over
		$xoTheme->addMeta('meta', 'description', $gnavi_meta_description);
	}
}

// comments
if($GNAVI_MOBILE){
	$description=strip_tags($photo['description']);
	$more = empty( $_GET['more'] ) ? 0 : intval( $_GET['more'] ) ;
	$xoopsTpl->assign( 'morepage' ,$more);
 	$xoopsTpl->assign( 'description' , xoops_substr($description,$more*512 ,512,"")) ;
	if($more*512+512 < gnavi_strlen($description))$xoopsTpl->assign( 'more' , $more+1) ;
	$xoopsTpl->assign( 'moreprev' , $more-1) ;

	$xoopsTpl->assign(array(
		"lng_mobile_photo1"=>_MD_GNAV_MOBILE_PHOTO1,
		"lng_mobile_photo2"=>_MD_GNAV_MOBILE_PHOTO2,
		"lng_mobile_photo3"=>_MD_GNAV_MOBILE_PHOTO3,
		"lng_mobile_prev"=>_MD_GNAV_MOBILE_PREV,
		"lng_mobile_next"=>_MD_GNAV_MOBILE_NEXT,
		"lng_mobile_show_area"=>_MD_GNAV_MOBILE_SHOW_AREA,
		)) ;

	if($GNAVI_MOBILE_MAP){
		$gnavi_mobile_maekercolor="blue";
		$google_staticmap=$gnavi_googlemap_url."/staticmap";
		$mymap="$google_staticmap?center=".$photo['lat'].",".$photo['lng']."&zoom=".$photo['zoom']."&size=$gnavi_mobile_mapsize&maptype=$gnavi_mobile_maptype&key=$gnavi_googlemapapi_key";
		$markers=$photo['lat'].",".$photo['lng'].",".$gnavi_mobile_maekercolor;
		if($markers)$markers="&markers=".$markers;
		$xoopsTpl->assign('mymap',$mymap.$markers);
	}

	gnavi_mobile_templete_disp("db:{$mydirname}_mobile_item.html");

}elseif($myprint){
	$xoopsTpl->assign('charset',_CHARSET);
	$xoopsTpl->display("db:{$mydirname}_print.html");
}else{
	$xoopsOption['template_main'] = "{$mydirname}_item.html" ;
	include( XOOPS_ROOT_PATH . "/footer.php" ) ;
}


function gnavi_strlen($str){
	if ( !XOOPS_USE_MULTIBYTES && function_exists('mb_internal_encoding') && @mb_internal_encoding(_CHARSET)) {
		return mb_strlen($str);
	}else{
		return strlen($str);
	}
}

?>