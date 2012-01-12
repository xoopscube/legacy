<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name'] = $mydirname ;
$modversion['version'] = 0.96 ;
$modversion['description'] = constant($constpref.'_DESC') ;
$modversion['credits'] = "Original: GIJOE<br />(http://www.peak.ne.jp/)<br />Daniel Branco<br />(http://bluetopia.homeip.net)<br />Kazumi Ono<br />(http://www.mywebaddons.com/)<br />The XOOPS Project" ;
$modversion['author'] = "KENTARO<br />(http://xoops.iko-ze.net/)" ;
$modversion['help'] = "" ;
$modversion['license'] = "GPL" ;
$modversion['official'] = 0 ;
$modversion['image'] = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname'] = $mydirname ;
$modversion['trust_dirname'] = $mytrustdirname ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin'] = 1 ;
$modversion['adminindex'] = 'admin/index.php' ;
$modversion['adminmenu'] = 'admin/admin_menu.php' ;

// Search
$modversion['hasSearch'] = 1 ;
$modversion['search']['file'] = 'search.php' ;
$modversion['search']['func'] = $mydirname.'_global_search' ;

// Menu
global $xoopsDB , $xoopsUser , $gnavi_catonsubmenu ,$gnavi_usevote, $table_cat ,$gnavi_usegooglemap,$gnavi_indexpage ;
$modversion['hasMain'] = 1 ;

if( is_object( @$GLOBALS['xoopsModule'] ) && $GLOBALS['xoopsModule']->getVar('dirname') == $mydirname ) {
	require_once dirname(__FILE__).'/include/common_functions.php' ;
	$modversion['sub'] = gnavi_get_submenu( $mydirname ) ;

} else {
	$_sub_menu_cache = XOOPS_TRUST_PATH . '/cache/'. urlencode(substr(XOOPS_URL, 7)) . '_' . $mydirname . '_' . (is_object(@$GLOBALS['xoopsUser'])? join('-', $GLOBALS['xoopsUser']->getGroups()):XOOPS_GROUP_ANONYMOUS)  . '_' . $GLOBALS['xoopsConfig']['language'] . '.submenu';
	if (is_file($_sub_menu_cache) && time() - 3600 < filemtime($_sub_menu_cache)) {
		$modversion['sub'] = unserialize(file_get_contents($_sub_menu_cache));
	} else {
		require_once dirname(__FILE__).'/include/common_functions.php' ;
		$modversion['sub'] = gnavi_get_submenu( $mydirname ) ;
		file_put_contents($_sub_menu_cache, serialize($modversion['sub']));
	}
}

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'][1] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_RANDOM') ,
	'description'	=> 'Shows a random photo' ,
	'show_func'		=> 'b_gnavi_ritem_show' ,
	'edit_func'		=> 'b_gnavi_ritem_edit' ,
	'options'		=> "$mydirname|5|20|0|1||1|0|1|0" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][2] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_RECENT') ,
	'description'	=> 'Shows recently added photos' ,
	'show_func'		=> 'b_gnavi_topnews_show' ,
	'edit_func'		=> 'b_gnavi_topnews_edit' ,
	'options'		=> "$mydirname|5|20|0|1||1|0|1|0" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][3] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_HITS') ,
	'description'	=> 'Shows most viewed photos' ,
	'show_func'		=> 'b_gnavi_tophits_show' ,
	'edit_func'		=> 'b_gnavi_tophits_edit' ,
	'options'		=> "$mydirname|5|20|0|1||1|0|1|0" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][4] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_RECENT_P') ,
	'description'	=> 'Shows recently added photos' ,
	'show_func'		=> 'b_gnavi_topnews_show' ,
	'edit_func'		=> 'b_gnavi_topnews_edit' ,
	'options'		=> "$mydirname|5|20|0|1||1|1|1|0" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][5] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_HITS_P') ,
	'description'	=> 'Shows most viewed photos' ,
	'show_func'		=> 'b_gnavi_tophits_show' ,
	'edit_func'		=> 'b_gnavi_tophits_edit' ,
	'options'		=> "$mydirname|5|20|0|1||1|1|1|0" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;
$modversion['blocks'][6] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_MENU') ,
	'description'	=> 'Shows category menu' ,
	'show_func'		=> 'b_gnavi_menu_show' ,
	'edit_func'		=> 'b_gnavi_menu_edit' ,
	'options'		=> "$mydirname|1" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][7] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_ARCHIVE') ,
	'description'	=> 'Shows archive list' ,
	'show_func'		=> 'b_gnavi_archive_show' ,
	'edit_func'		=> '' ,
	'options'		=> "$mydirname" ,
	'template'		=> '' ,
	'can_clone'		=> true ,
) ;

// Comments
$modversion['hasComments'] = 0 ;
// Comments
/* if use Xoops Comments.
$modversion['hasComments'] = 1;
$modversion['comments']['itemName'] = 'lid';
$modversion['comments']['pageName'] = 'index.php';
// Comment callback functions
$modversion['comments']['callbackFile'] = 'comment.php';
$modversion['comments']['callback']['approve'] = "{$mydirname}_comments_approve";
$modversion['comments']['callback']['update'] = "{$mydirname}_comments_update";
*/

// Configs
$modversion['config'][] = array(
	'name'			=> 'gnavi_photospath' ,
	'title'			=> $constpref.'_CFG_PHOTOSPATH' ,
	'description'	=> $constpref.'_CFG_DESCPHOTOSPATH' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> "/uploads/{$mydirname}" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_thumbspath' ,
	'title'			=> $constpref.'_CFG_THUMBSPATH' ,
	'description'	=> $constpref.'_CFG_DESCTHUMBSPATH' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> "/uploads/{$mydirname}/thumbs" ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_imagingpipe' ,
	'title'			=> $constpref.'_CFG_IMAGINGPIPE' ,
	'description'	=> $constpref.'_CFG_DESCIMAGINGPIPE' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array( 'GD' => 0 , 'ImageMagick' => 1 , 'NetPBM' => 2 )
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_forcegd2' ,
	'title'			=> $constpref.'_CFG_FORCEGD2' ,
	'description'	=> $constpref.'_CFG_DESCFORCEGD2' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_imagickpath' ,
	'title'			=> $constpref.'_CFG_IMAGICKPATH' ,
	'description'	=> $constpref.'_CFG_DESCIMAGICKPATH' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_netpbmpath' ,
	'title'			=> $constpref.'_CFG_NETPBMPATH' ,
	'description'	=> $constpref.'_CFG_DESCNETPBMPATH' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_width' ,
	'title'			=> $constpref.'_CFG_WIDTH' ,
	'description'	=> $constpref.'_CFG_DESCWIDTH' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '1024' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_height' ,
	'title'			=> $constpref.'_CFG_HEIGHT' ,
	'description'	=> $constpref.'_CFG_DESCHEIGHT' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '1024' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_fsize' ,
	'title'			=> $constpref.'_CFG_FSIZE' ,
	'description'	=> $constpref.'_CFG_DESCFSIZE' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '100000' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_middlepixel' ,
	'title'			=> $constpref.'_CFG_MIDDLEPIXEL' ,
	'description'	=> $constpref.'_CFG_DESCMIDDLEPIXEL' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '480x480' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_liquidimg' ,
	'title'			=> $constpref.'_CFG_LIQUIDIMG' ,
	'description'	=> $constpref.'_CFG_DESCLIQUIDIMG' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_allownoimage' ,
	'title'			=> $constpref.'_CFG_ALLOWNOIMAGE' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '1' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_makethumb' ,
	'title'			=> $constpref.'_CFG_MAKETHUMB' ,
	'description'	=> $constpref.'_CFG_DESCMAKETHUMB' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '1' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_thumbsize' ,
	'title'			=> $constpref.'_CFG_THUMBSIZE' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '140' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_thumbrule' ,
	'title'			=> $constpref.'_CFG_THUMBRULE' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'w' ,
	'options'		=> array(
		 $constpref.'_OPT_CALCFROMWIDTH' => 'w' ,  $constpref.'_OPT_CALCFROMHEIGHT' => 'h' ,  $constpref.'_OPT_CALCWHINSIDEBOX' => 'b' )
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_popular' ,
	'title'			=> $constpref.'_CFG_POPULAR' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '100' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_newdays' ,
	'title'			=> $constpref.'_CFG_NEWDAYS' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '7' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_newphotos' ,
	'title'			=> $constpref.'_CFG_NEWPHOTOS' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '10' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_defaultorder' ,
	'title'			=> $constpref.'_CFG_DEFAULTORDER' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'dateD' ,
	'options'		=> array(
		"photo_id ASC" => 'lidA' ,
		"title ASC" => 'titleA' ,
		"date ASC" => 'dateA' ,
		"hits ASC" => 'hitsA' ,
		"rating ASC" => 'ratingA' ,
		"photo_id DESC" => 'lidD' ,
		"title DESC" => 'titleD' ,
		"date DESC" => 'dateD' ,
		"hits DESC" => 'hitsD' ,
		"rating DESC" => 'ratingD'
		)
	) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_addposts' ,
	'title'			=> $constpref.'_CFG_ADDPOSTS' ,
	'description'	=> $constpref.'_CFG_DESCADDPOSTS' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '1' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_catonsubmenu' ,
	'title'			=> $constpref.'_CFG_CATONSUBMENU' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_nameoruname' ,
	'title'			=> $constpref.'_CFG_NAMEORUNAME' ,
	'description'	=> $constpref.'_CFG_DESCNAMEORUNAME' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'uname' ,
	'options'		=> array( $constpref.'_OPT_USENAME'=>'name', $constpref.'_OPT_USEUNAME'=>'uname')
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_indexpage' ,
	'title'			=> $constpref.'_CFG_INDEXPAGE' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'category' ,
	'options'		=> array( 'Category'=>'category','Map'=>'map')
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_viewcattype' ,
	'title'			=> $constpref.'_CFG_VIEWCATTYPE' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'list' ,
	'options'		=> array( $constpref.'_OPT_VIEWLIST'=>'list', $constpref.'_OPT_VIEWTABLE'=>'table')
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_colsoftableview' ,
	'title'			=> $constpref.'_CFG_COLSOFTABLEVIEW' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '4' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_showparent' ,
	'title'			=> $constpref.'_CFG_SHOWPARENT' ,
	'description'	=> $constpref.'_CFG_DESCSHOWPARENT' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_allowedexts' ,
	'title'			=> $constpref.'_CFG_ALLOWEDEXTS' ,
	'description'	=> $constpref.'_CFG_DESCALLOWEDEXTS' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'jpg|jpeg|gif|png|kml|kmz' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_allowedmime' ,
	'title'			=> $constpref.'_CFG_ALLOWEDMIME' ,
	'description'	=> $constpref.'_CFG_DESCALLOWEDMIME' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'image/gif|image/pjpeg|image/jpeg|image/x-png|image/png|application/vnd.google-earth.kml+xml|application/vnd.google-earth.kmz' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_body_editor' ,
	'title'			=> $constpref.'_CFG_BODY_EDITOR' ,
	'description'	=> $constpref.'_CFG_DESCBODY_EDITOR' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'xoopsdhtml' ,
	'options'		=> array( 'xoopsdhtml' => 'xoopsdhtml' ,'Pure HTML' => 'pure_html' , 'common/spaw' => 'common_spaw' , 'common/fckeditor' => 'common_fckeditor' )
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_addinfo' ,
	'title'			=> $constpref.'_CFG_ADDINFO' ,
	'description'	=> $constpref.'_CFG_DESCADDINFO' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_usevote' ,
	'title'			=> $constpref.'_CFG_USEVOTE' ,
	'description'	=> $constpref.'_CFG_DESCUSEVOTE' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_usegooglemap' ,
	'title'			=> $constpref.'_CFG_USEGMAP' ,
	'description'	=> $constpref.'_CFG_DESCGMAP' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_googlemapapi_key' ,
	'title'			=> $constpref.'_CFG_GMAPKEY' ,
	'description'	=> $constpref.'_CFG_DESCGMAPKEY' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_defaultlat' ,
	'title'			=> $constpref.'_CFG_DEFLAT' ,
	'description'	=> $constpref.'_CFG_DESCDEFLAT' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '35.631610' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_defaultlng' ,
	'title'			=> $constpref.'_CFG_DEFLNG' ,
	'description'	=> $constpref.'_CFG_DESCDEFLNG' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '139.881277' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_defaultzoom' ,
	'title'			=> $constpref.'_CFG_DEFZOOM' ,
	'description'	=> $constpref.'_CFG_DESCDEFZOOM' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '10' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_defaultmtype' ,
	'title'			=> $constpref.'_CFG_DEFMTYPE' ,
	'description'	=> $constpref.'_CFG_DESCDEFMTYPE' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'G_NORMAL_MAP' ,
	'options'		=> array('G_NORMAL_MAP'=>'G_NORMAL_MAP',
							'G_SATELLITE_MAP'=>'G_SATELLITE_MAP',
							'G_HYBRID_MAP'=>'G_HYBRID_MAP',
							'G_PHYSICAL_MAP'=>'G_PHYSICAL_MAP',
							'G_HYBRID_PHYSICAL_MAP'=>'G_HYBRID_PHYSICAL_MAP',
							'G_MOON_ELEVATION_MAP'=>'G_MOON_ELEVATION_MAP',
							'G_MOON_VISIBLE_MAP'=>'G_MOON_VISIBLE_MAP',
							'G_MARS_ELEVATION_MAP'=>'G_MARS_ELEVATION_MAP',
							'G_MARS_VISIBLE_MAP'=>'G_MARS_VISIBLE_MAP',
							'G_MARS_INFRARED_MAP'=>'G_MARS_INFRARED_MAP',
							'G_SKY_VISIBLE_MAP'=>'G_SKY_VISIBLE_MAP')
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_map_draw' ,
	'title'			=> $constpref.'_MAP_DRAW' ,
	'description'	=> $constpref.'_DESC_MAP_DRAW',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_include_map' ,
	'title'			=> $constpref.'_INCLUDE_KML' ,
	'description'	=> $constpref.'_DESC_INCLUDE_KML',
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_use_rss' ,
	'title'			=> $constpref.'_CFG_USE_RSS' ,
	'description'	=> $constpref.'_CFG_DESC_USE_RSS',
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_pe_appkey' ,
	'title'			=> $constpref.'_CFG_PE_APPKEY' ,
	'description'	=> $constpref.'_CFG_DESC_PE_APPKEY',
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_icon_by_lid' ,
	'title'			=> $constpref.'_ICON_BYLID' ,
	'description'	=> '',
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_mobile_mapsize' ,
	'title'			=> $constpref.'_CFG_MOBILEMAPSIZE' ,
	'description'	=> $constpref.'_CFG_DESCMOBILEMAPSIZE' ,
	'formtype'		=> '' ,
	'valuetype'		=> 'textbox' ,
	'default'		=> '220x200' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_mobile_agent' ,
	'title'			=> $constpref.'_CFG_MOBILEAGENT' ,
	'description'	=> $constpref.'_CFG_DESCMOBILEAGENT' ,
	'formtype'		=> '' ,
	'valuetype'		=> 'textbox' ,
	'default'		=> '/(DoCoMo|J-PHONE|Vodafone|MOT|SoftBank|KDDI|UP.Browser|PDXGW|DDIPOCKET|WILLCOM|EMULATOR|emulator)/' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_mobile_encording' ,
	'title'			=> $constpref.'_CFG_MOBILEENCORDING' ,
	'description'	=> $constpref.'_CFG_DESCMOBILEENCORDING' ,
	'formtype'		=> '' ,
	'valuetype'		=> 'textbox' ,
	'default'		=> ($langman->language=='japanese'||$langman->language=='ja_utf8' ? 'SJIS' : '') ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_mobile_useqr' ,
	'title'			=> $constpref.'_CFG_MOBILEUSEQRC' ,
	'description'	=> $constpref.'_CFG_DESCMOBILEUSEQRC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'gnavi_comment_dirname' ,
	'title'			=> $constpref.'_COM_DIRNAME' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_comment_forum_id' ,
	'title'			=> $constpref.'_COM_FORUM_ID' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;
$modversion['config'][] = array(
	'name'			=> 'gnavi_comment_view' ,
	'title'			=> $constpref.'_COM_VIEW' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'listposts_flat' ,
	'options'		=> array( '_FLAT' => 'listposts_flat' , '_THREADED' => 'listtopics' )
) ;


// Notification
$modversion['hasNotification'] = 1 ;
$modversion['notification'] = array(
	'lookup_file' => 'notification.php' ,
	'lookup_func' => "{$mydirname}_notify_iteminfo" ,
	'category' => array(
		array(
			'name' => 'global' ,
			'title' => constant($constpref.'_GLOBAL') ,
			'description' => constant($constpref.'_GLOBALDSC') ,
			'subscribe_from' => array('index.php') ,
		) ,
		array(
			'name' => 'category' ,
			'title' => constant($constpref.'_CATEGORY') ,
			'description' => constant($constpref.'_CATEGORYDSC') ,
			'subscribe_from' => array('index.php') ,
			'item_name' => 'cid' ,
			'allow_bookmark' => 1 ,
		) ,
		array(
			'name' => 'item' ,
			'title' => constant($constpref.'_ITEM') ,
			'description' => constant($constpref.'_ITEMDSC') ,
			'subscribe_from' => array('index.php') ,
			'item_name' => 'lid' ,
			'allow_bookmark' => 1 ,
		) ,
	) ,
	'event' => array(
		array(
			'name' => 'new_item' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWITEM') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWITEMCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWITEMCONTENTCAP') ,
			'mail_template' => 'global_newitem_notify' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWITEMBJ') ,
		) ,
		array(
			'name' => 'new_item' ,
			'category' => 'category' ,
			'title' => constant($constpref.'_NOTIFY_CATEGORY_NEWITEM') ,
			'caption' => constant($constpref.'_NOTIFY_CATEGORY_NEWITEMCAP') ,
			'description' => constant($constpref.'_NOTIFY_CATEGORY_NEWITEMCONTENTCAP') ,
			'mail_template' => 'category_newitem_notify' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CATEGORY_NEWITEMBJ') ,
		) ,
	) ,
) ;

// onInstall, onUpdate, onUninstall
$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}
?>