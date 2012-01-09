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

//GET
$cid = empty( $_GET['cid'] ) ? 0 : intval( $_GET['cid'] ) ;
$uid = empty( $_GET['uid'] ) ? 0 : intval( $_GET['uid'] ) ;
$lid = empty( $_GET['lid'] ) ? 0 : intval( $_GET['lid'] ) ;

$default_lat  = empty( $_GET['lat'] )  ? $gnavi_defaultlat  : floatval( $_GET['lat'] ) ;
$default_lng  = empty( $_GET['lng'] )  ? $gnavi_defaultlng  : floatval( $_GET['lng'] ) ;
$default_zoom = empty( $_GET['zoom'] ) ? $gnavi_defaultzoom : intval( $_GET['zoom'] ) ;
$default_mtype = !in_array(@$_GET['mtype'],$gnavi_maptypes) ? $gnavi_defaultmtype : $_GET['mtype'] ;
$default_mtype = !in_array(@$_GET['mtype'],$gnavi_maptypes) ? $gnavi_defaultmtype : $_GET['mtype'] ;

$query = empty( $_GET['q'] ) ? false : $_GET['q'] ;

$query = mb_convert_encoding($query, _CHARSET , 'auto');


$latlng = address2latlng($query);
if($latlng){
	$default_lat=$latlng[1];
	$default_lng=$latlng[0];
	$default_zoom=15;
	}

//Set template

$xoopsOption['template_main'] = "{$mydirname}_map.html" ;

include XOOPS_ROOT_PATH . "/header.php" ;

//Assign
$xoopsTpl->assign( $gnavi_assign_globals ) ;
$xoopsTpl->assign( 'map' , _MD_GNAV_MAP_SHOW ) ;
$xoopsTpl->assign( 'rowlist' , _MD_GNAV_CAT_LIST ) ;
$xoopsTpl->assign('cat_link',$mod_url."/index.php?".( $page_map ? $page_map.'&':''));
if( $global_perms & GNAV_GPERM_INSERTABLE ) $xoopsTpl->assign( 'lang_add_photo' , _MD_GNAV_CAT_ADDITEM ) ;

$lat=$lng=0;
$kmlurl='';
//make data by GET values
if( $cid > 0 ) {

	$rs = $xoopsDB->query( "SELECT title,imgurl,kmlurl,description,pid,lat,lng,zoom,mtype FROM $table_cat WHERE cid='$cid'" ) ;
	list( $cat_title,$imgurl,$kmlurl,$description,$pid,$lat,$lng,$zoom,$mtype ) = $xoopsDB->fetchRow( $rs ) ;

	$pagetitle = $myts->makeTboxData4show( $cat_title ) ;

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
	$kmlurl= preg_match('/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $kmlurl) ? $kmlurl :"";
	$xoopsTpl->assign( 'category_imgurl' , $imgurl ) ;

	$xoopsTpl->assign( 'parent_id' , $pid ) ;
	$xoopsTpl->assign( 'category_id' , $cid ) ;
	$xoopsTpl->assign( 'subcategories' , gnavi_get_sub_categories( $cid , $cattree,"AND (lat<>0 OR lng<>0)" ) ) ;
	$xoopsTpl->assign( 'category_options' , gnavi_get_cat_options() ) ;

	$cids = $cattree->getAllChildId( $cid ) ;
	array_push( $cids , $cid ) ;

	//counts
	$photo_num_total = gnavi_get_photo_total_sum_from_cats( $cids , "status>0 AND (lat<>0 OR lng<>0)" ) ;
	$xoopsTpl->assign( 'photo_num_total' , $photo_num_total.'&nbsp;'._MD_GNAV_C_DATA ) ;

	//breadcrumbs
	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	if($gnavi_indexpage!='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=map' , 'name' => _MD_GNAV_MAP_SHOW ) ;
	}
	$xoops_breadcrumbs = gnavi_add_breadcrumbs( $pid , "index.php".( $page_map ? '?'.$page_map:''),$xoops_breadcrumbs);
	$xoops_breadcrumbs[] = array( 'name' => $myts->makeTboxData4show( $cat_title )) ;


	$xoopsTpl->assign( 'imgurl' , $imgurl ) ;
	$xoopsTpl->assign( 'description' , $description ) ;

	$where = "";
	foreach( $cids as $cidw ) {
		$where .= "$cidw," ;
	}
	$where =substr($where, 0, -1);
	$where = "(cid IN($where) or cid1 IN($where) or cid2 IN($where) or cid3 IN($where) or cid4 IN($where) )";

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
	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE $where AND status>0 AND (lat<>0 OR lng<>0)" ) ;
	list( $photo_num_total ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_num_total' , $photo_num_total.'&nbsp;'._MD_GNAV_C_DATA ) ;

	//breadcrumbs
	$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
	if($gnavi_indexpage!='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php?page=map' , 'name' => _MD_GNAV_MAP_SHOW ) ;
	}
	$xoops_breadcrumbs[] = array( 'name' => $pagetitle ) ;

} else {

	$xoopsTpl->assign( 'indexpage' , true ) ;
	//count
	$prs = $xoopsDB->query( "SELECT COUNT(lid) FROM $table_photos WHERE status>0 AND (lat<>0 OR lng<>0)" ) ;
	list( $photo_num_total ) = $xoopsDB->fetchRow( $prs ) ;
	$xoopsTpl->assign( 'photo_num_total' , sprintf( _MD_GNAV_CAT_MAKERTHEREARE , $photo_num_total ) ) ;

	//breadcrumbs
	if($gnavi_indexpage!='map'){
		$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;
		$xoops_breadcrumbs[] = array( 'name' => _MD_GNAV_MAP_SHOW ) ;
		$pagetitle = _MD_GNAV_MAP_SHOW ;
	}else{
		$xoops_breadcrumbs[] = array( 'name' => $xoopsModule->getVar( 'name' ) ) ;
		$pagetitle = $xoopsModule->getVar('name') ;
	}
	$xoopsTpl->assign( 'subcategories' , gnavi_get_sub_categories( 0 , $cattree,"AND (lat<>0 OR lng<>0)" ) ) ;
	$xoopsTpl->assign( 'category_options' , gnavi_get_cat_options() ) ;

	$where = "cid > 0";
	$get_append = "";
}

//more assign
$xoopsTpl->assign( 'xoops_breadcrumbs' , $xoops_breadcrumbs) ;
$xoopsTpl->assign( 'link_option' , ($get_append ? "?".$get_append : '' )) ;

$xoopsTpl->assign(array(
	"lng_map_show"=>_MD_GNAV_MAP_SEARCH,
	"lng_map_show_desc"=>_MD_GNAV_MAP_SEARCH_DESC,
	"lng_mobile_mklist"=>_MD_GNAV_MOBILE_MKLIST,
	"lng_mobile_nomark"=>_MD_GNAV_MOBILE_NOMARK,
	"lng_mobile_search"=>_MD_GNAV_MOBILE_SEARCH,
	"lng_mobile_search_desc"=>_MD_GNAV_MOBILE_SEARCH_DESC,
	"lng_mobile_updir"=>_MD_GNAV_MOBILE_UPDIR,
	)) ;



$catget_append = $page_cat ? ( $get_append ? $get_append.'&'.$page_cat : $page_cat ) : $get_append ;
$xoopsTpl->assign( 'catlink_option' , ($catget_append ? "?".$catget_append : '' )) ;

$get_append = $page_map ? ( $get_append ? $get_append.'&'.$page_map : $page_map ) : $get_append ;

$xoopsTpl->assign( 'album_sub_title' , $pagetitle) ;
$xoopsTpl->assign( 'xoops_pagetitle' , $pagetitle) ;

	//map setting
	if($lid>0){
		$rs = $xoopsDB->query( "SELECT lat,lng,zoom,mtype FROM $table_photos WHERE lid='$lid'" ) ;
		list( $lat,$lng,$zoom,$mtype ) = $xoopsDB->fetchRow( $rs ) ;
	}

	if($lng!=0 or $lat!=0){
		$default_lat=$lat;
		$default_lng=$lng;
		$default_zoom=$zoom;
		$default_mtype=$mtype;
	}
	$mykmls="";
	if($kmlurl){
		$mykmls = "gn_mykmls = new Array('".$kmlurl."');";
	}

$xoopsTpl->assign('default_lat',$default_lat);
$xoopsTpl->assign('default_lng',$default_lng);
$xoopsTpl->assign('default_zoom',$default_zoom);
$xoopsTpl->assign('default_mtype',$default_mtype);

$xoops_module_header = $xoopsTpl->get_template_vars( "xoops_module_header" ) ."\n" ."<script src='".$gnavi_googlemap_url."/maps?file=api&amp;v=2&amp;key=$gnavi_googlemapapi_key' type='text/javascript' charset='utf-8'></script>
<link rel='stylesheet' type='text/css' href='css/gnavi.css'/>
<script src='js/map.js' type='text/javascript' charset='utf-8'></script>
<script src='js/prototype.js'></script>
<script type='text/javascript'>
//<![CDATA[
	$gnavi_lang_java
	$mykmls
	gn_url = '$mod_url';
	gn_ulop = '$get_append';
	gn_l=$lid;
	gn_ep = ".( $global_perms & GNAV_GPERM_INSERTABLE )." ;
	gn_drkm = $gnavi_map_draw ;
	window.onload = ShowGMap;
	".($gnavi_pe_appkey==""?"":"gn_pekey='".$gnavi_pe_appkey."';")."
//]]>
</script>";

$xoopsTpl->assign('xoops_module_header',$xoops_module_header);

if($GNAVI_MOBILE){
//for mobile code
	if($GNAVI_MOBILE_MAP){
	//for degug

		$google_staticmap = $gnavi_googlemap_url."/staticmap";
		$mymap="$google_staticmap?center=$default_lat,$default_lng&zoom=$default_zoom&size=$gnavi_mobile_mapsize&maptype=$gnavi_mobile_maptype&key=$gnavi_googlemapapi_key";

		$gnavi_mobile_maekercolor="blue";

		/*緯度は -90度 ? +90度の範囲に、経度は -180度 ? +180度の範囲に収まるように*/

		$movex=$gnavi_mobile_mapsizex/pow(2,$default_zoom);
		$movey=$gnavi_mobile_mapsizey/pow(2,$default_zoom);

		//Amount of movement on Mini map . set bigger than 0 and 1 or less. (0 < value <=1).
		$gnavi_mobile_mapmove_raito = 0.6;

		$latup = gnavi_latlnground($default_lat+$movey * $gnavi_mobile_mapmove_raito);
		$latdown = gnavi_latlnground($default_lat-$movey * $gnavi_mobile_mapmove_raito);
		$lngup = gnavi_latlnground($default_lng+$movex * $gnavi_mobile_mapmove_raito);
		$lngdown = gnavi_latlnground($default_lng-$movex * $gnavi_mobile_mapmove_raito);

		$latup =   $latup   > 90  ? $latup  -180 : ($latup   < -90  ? $latup   + 180 : $latup   );
		$latdown = $latdown > 90  ? $latdown-180 : ($latdown < -90  ? $latdown + 180 : $latdown );
		$lngup =   $lngup   > 180 ? $lngup  -360 : ($lngup   < -180 ? $lngup   + 360 : $lngup   );
		$lngdown = $lngdown > 180 ? $lngdown-360 : ($lngdown < -180 ? $lngdown + 360 : $lngdown );

		$mapkeys=array(
				'zoom' => $default_zoom,
				'zoomdown' => ($default_zoom-1 > 1 ? $default_zoom-1 : 1 ),
				'zoomup' => ($default_zoom+1 < 18 ? $default_zoom+1 : 18) ,
				'doublezoomdown' => ($default_zoom-3 > 1 ? $default_zoom-4 : 1 ),
				'doublezoomup' => ($default_zoom+3 < 18 ? $default_zoom+4 : 18) ,
				'lat' => $default_lat,
				'lng' => $default_lng,
				'latup' =>   $latup  ,
				'latdown' => $latdown  ,
				'lngup' =>   $lngup  ,
				'lngdown' => $lngdown  ,
			);

		$latup = gnavi_latlnground($default_lat+$movey/2);
		$latdown = gnavi_latlnground($default_lat-$movey/2);
		$lngup = gnavi_latlnground($default_lng+$movex/2);
		$lngdown = gnavi_latlnground($default_lng-$movex/2);

		//Get 26 Results
		$mysql="SELECT lid,title,lat,lng,zoom FROM $table_photos WHERE lat<$latup AND lat>$latdown AND lng<$lngup AND lng>$lngdown AND status>0 AND $where ";
		$prs = $xoopsDB->query( $mysql,26,0) ;

		$markers="";
		$markerlist=array();
		$alphabet="abcdefghijklmnopqrstuvwxyz";
		$i=0;
		while( $rs = $xoopsDB->fetchArray( $prs ) ) {
			$title = strip_tags($myts->displayTarea($rs['title'], 1 , 1 , 1 , 1 , 1 , 1 ));
			$title = xoops_substr($title,0,20);
	        $abc = substr($alphabet,$i,1);

			if($markers)$markers.="%7C";
			$markers.=$rs['lat'].",".$rs['lng'].",".$gnavi_mobile_maekercolor.$abc;

			$markerlist[]=array(
							'abc' => strtoupper($abc),
							'lid' => $rs['lid'],
							'title' => $title,
						);
			$i++;
		}

		$xoopsTpl->assign('query',$query);
		if(!$latlng && $query){
			$pagetitle =  "";
			//$xoopsTpl->assign('result',$query."は見つかりません");
			$xoopsTpl->assign('result',sprintf( constant('_MD_GNAV_MOBILE_NOTFOUND'), $query ));
		}elseif($latlng && $query){
			//$xoopsTpl->assign('result',$query."を表示します");
			$xoopsTpl->assign('result',sprintf( constant('_MD_GNAV_MOBILE_SHOW'), $query ));
		}


		if($markers)$markers="&markers=".$markers;
		$xoopsTpl->assign('mymap',$mymap.$markers);
		$xoopsTpl->assign('markerlist',$markerlist);
		//$xoopsTpl->assign('lang_category',"カテゴリー");
		$xoopsTpl->assign('lang_category', constant('_MD_GNAV_MOBILE_CATEGORY') );
		$xoopsTpl->assign('mapkeys', $mapkeys);

		gnavi_mobile_templete_disp("db:{$mydirname}_mobile_map.html");

	}else{
		echo"Disable GoogleMobileMap";
	}
}else{
	include( XOOPS_ROOT_PATH . "/footer.php" ) ;
}


function gnavi_latlnground($value){
	return round(floatval($value)*1000000)/1000000 ;
}


function address2latlng( $address ){
	global $gnavi_googlemap_url;
	if(!trim($address))return false;
	if( ! function_exists('file_get_contents'))return false;
	global $gnavi_googlemapapi_key;
    $address = mb_convert_encoding( $address , "UTF-8" , "auto");
    $address = urlencode( $address );

    $url = $gnavi_googlemap_url."/maps/geo?key=".$gnavi_googlemapapi_key."&q=".$address."&output=xml&ie=UTF-8";
    $file = file_get_contents( $url );
    $file = mb_convert_encoding( $file , _CHARSET, "UTF-8" );

    if(preg_match( "/(<coordinates>)[0-9.,]+<\/coordinates>/" , $file , $latlng)){
	    return explode( "," , strip_tags( $latlng[0] ) );
	}else{
		return false;
	}
}


?>
