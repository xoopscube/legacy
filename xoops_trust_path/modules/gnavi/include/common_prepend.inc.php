<?php

//include files
require dirname(dirname(__FILE__)).'/include/read_configs.php' ;
require dirname(dirname(__FILE__)).'/include/get_perms.php' ;
require_once dirname(dirname(__FILE__)).'/include/draw_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_javalang.inc.php' ;
require_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;

// GET admin status
$userid = is_object( @$xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;
$isadmin = $userid > 0 ? $xoopsUser->isAdmin() : false ;

// init xoops_breadcrumbs. this is your Xoops Top
$xoops_breadcrumbs[] = array( 'url' => XOOPS_URL , 'name' => _MD_GNAV_WEBTOP );


// assing global strings
$gnavi_assign_globals = array(
	'mod_url' => $mod_url ,
	'mod_copyright' => $mod_copyright ,
	'lang_submitter' => _MD_GNAV_C_SUBMITTER ,
	'lang_hitsc' => _MD_GNAV_C_RAT_HITSC ,
	'lang_commentsc' => _MD_GNAV_CMT_COMMENTSC ,
	'lang_new' => _MD_GNAV_C_NEW ,
	'lang_updated' => _MD_GNAV_C_UPDATED ,
	'lang_popular' => _MD_GNAV_C_POPULAR ,
	'lang_ratethisphoto' => _MD_GNAV_RAT_RATETHISPHOTO ,
	'lang_editthisphoto' => _MD_GNAV_SMT_EDITITEM ,
	'lang_guestname' => _GNAV_CAPTION_GUESTNAME ,
	'lang_category' => _GNAV_CAPTION_CATEGORY ,
	'lang_nomatch' => _MD_GNAV_MSG_NOMATCH ,
	'lang_directcatsel' => _MD_GNAV_CAT_DIRECTCATSEL ,
	'lang_markerlist' => _MD_GNAV_MAP_MARKERLIST ,
	'lang_loading' => _MD_GNAV_MAP_LOADING ,
	'lang_lat' => _MD_GNAV_MAP_LAT ,
	'lang_lng' => _MD_GNAV_MAP_LNG ,
	'lang_zoom' => _MD_GNAV_MAP_ZOOM ,
	'lang_movepid' => _MD_GNAV_CAT_MOVE_PARENT ,
	'photos_url' => $photos_url ,
	'thumbs_url' => $thumbs_url ,
	'thumbsize' => $gnavi_thumbsize ,
	'colsoftableview' => $gnavi_colsoftableview ,
	'colstbl_width' => ($gnavi_colsoftableview ? 'width:'.intval(100/$gnavi_colsoftableview).'%;' : '' ) ,
	'canrateview' => $global_perms & GNAV_GPERM_RATEVIEW ,
	'canratevote' => $global_perms & GNAV_GPERM_RATEVOTE ,
	'home' => _MD_GNAV_WEBTOP ,
	'canvote'  => $gnavi_usevote,
	'comment_dirname' => $gnavi_comment_dirname,
	'comment_forum_id' => $gnavi_comment_forum_id ,
	'comment_view' => $gnavi_comment_view,
	'mydirname' => $mydirname,
	'am_cat_edit' => ( $isadmin ? _MD_GNAV_CAT_EDIT : '' ) ,
	'lang_itemlist' => _MD_GNAV_CAT_ITEMLIST,
	'lang_url' => _MD_GNAV_ITM_URL,
	'lang_tel' => _MD_GNAV_ITM_TEL,
	'lang_fax' => _MD_GNAV_ITM_FAX,
	'lang_zip' => _MD_GNAV_ITM_ZIP,
	'lang_address' => _MD_GNAV_ITM_ADDRESS,
	'lang_map' => _MD_GNAV_MAP,
	'lang_readmore' => _MD_GNAV_NAV_READMORE,
	'lang_print' => _MD_GNAV_ITM_PRINT ,
	'lang_top_link' => sprintf( _MD_GNAV_MOBILE_TOP , $xoopsModule->getVar( 'name' ) ) ,
) ;

if(!$gnavi_usegooglemap)$gnavi_indexpage=='category';
$page_map = $gnavi_indexpage=='map' ? '' : 'page=map' ;
$page_cat = $gnavi_indexpage=='map' ? 'page=category' : '' ;

//const values
define("G_UPDATE", 1);
define("G_INSERT", 0);
define("G_KML", "kml");
define("G_XML", "xml");
$gnavi_gmap_exts=array('kml','kmz');
$gnavi_ajaxzip_place="/include/ajaxzip2/";

$gnavi_qrcode_path = XOOPS_TRUST_PATH."/libs/qrcode/qrcode_img.php" ;


//agent query

$GNAVI_MOBILE=0;
$GNAVI_MOBILE_MAP=0;

$gnavi_assign_globals['agent']=@$_GET['agent']=='mobile' ? '&agent=mobile':'';
$gnavi_assign_globals['_agent']=@$_GET['agent']=='mobile' ? 'mobile':'';
$agent = @$_SERVER['HTTP_USER_AGENT'];

if(preg_match($gnavi_mobile_agent, $agent) || @$_GET['agent']=='mobile'){
	$GNAVI_MOBILE=1;
	list( $gnavi_mobile_mapsizex , $gnavi_mobile_mapsizey ) = explode( 'x' , $gnavi_mobile_mapsize ) ;
	$gnavi_mobile_mapsizex=intval($gnavi_mobile_mapsizex);
	$gnavi_mobile_mapsizey=intval($gnavi_mobile_mapsizey);
	if($gnavi_mobile_mapsizex>0 && $gnavi_mobile_mapsizey>0)$GNAVI_MOBILE_MAP=1;
	$gnavi_mobile_maptype='mobile';
}



?>