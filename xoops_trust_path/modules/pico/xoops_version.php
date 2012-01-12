<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name'] = $mydirname ;
$modversion['description'] = constant($constpref.'_DESC') ;
$modversion['version'] = 1.82 ;
$modversion['credits'] = "PEAK Corp.";
$modversion['author'] = "GIJ=CHECKMATE<br />PEAK Corp.(http://www.peak.ne.jp/)" ;
$modversion['help'] = "" ;
$modversion['license'] = "GPL" ;
$modversion['official'] = 0 ;
$modversion['image'] = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname'] = $mydirname ;
$modversion['trust_dirname'] = $mytrustdirname ;
$modversion['read_any'] = true ;

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
$modversion['hasMain'] = 1 ;

// Submenu (just for mainmenu)
$modversion['sub'] = array() ;
if( is_object( @$GLOBALS['xoopsModule'] ) && $GLOBALS['xoopsModule']->getVar('dirname') == $mydirname ) {
	require_once dirname(__FILE__).'/include/common_functions.php' ;
	$modversion['sub'] = pico_common_get_submenu( $mydirname ) ;
} else {
	$_sub_menu_cache = XOOPS_TRUST_PATH . '/cache/'. urlencode(substr(XOOPS_URL, 7)) . '_' . $mydirname . '_' . (is_object(@$GLOBALS['xoopsUser'])? join('-', $GLOBALS['xoopsUser']->getGroups()):XOOPS_GROUP_ANONYMOUS)  . '_' . $GLOBALS['xoopsConfig']['language'] . '.submenu';
	if (is_file($_sub_menu_cache) && time() - 3600 < filemtime($_sub_menu_cache)) {
		$modversion['sub'] = unserialize(file_get_contents($_sub_menu_cache));
	} else {
		require_once dirname(__FILE__).'/include/common_functions.php' ;
		$modversion['sub'] = pico_common_get_submenu( $mydirname ) ;
		file_put_contents($_sub_menu_cache, serialize($modversion['sub']));
	}
}

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'][1] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_MENU') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_menu_show' ,
	'edit_func'		=> 'b_pico_menu_edit' ,
	'options'		=> "$mydirname||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][2] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_CONTENT') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_content_show' ,
	'edit_func'		=> 'b_pico_content_edit' ,
	'options'		=> "$mydirname|1||1" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][3] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_LIST') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_list_show' ,
	'edit_func'		=> 'b_pico_list_edit' ,
	'options'		=> "$mydirname||o.created_time DESC|10||0" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][4] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_SUBCATEGORIES') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_subcategories_show' ,
	'edit_func'		=> 'b_pico_subcategories_edit' ,
	'options'		=> "$mydirname|0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][5] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_MYWAITINGS') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_mywaitings_show' ,
	'edit_func'		=> 'b_pico_mywaitings_edit' ,
	'options'		=> "$mydirname|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][6] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_TAGS') ,
	'description'	=> '' ,
	'show_func'		=> 'b_pico_tags_show' ,
	'edit_func'		=> 'b_pico_tags_edit' ,
	'options'		=> "$mydirname|30|count DESC|count DESC|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

// Comments
$modversion['hasComments'] = 0 ;

// Configs
$modversion['config'][1] = array(
	'name'			=> 'use_wraps_mode' ,
	'title'			=> $constpref.'_USE_WRAPSMODE' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'use_rewrite' ,
	'title'			=> $constpref.'_USE_REWRITE' ,
	'description'	=> $constpref.'_USE_REWRITEDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'wraps_auto_register' ,
	'title'			=> $constpref.'_WRAPSAUTOREGIST' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'auto_register_class' ,
	'title'			=> $constpref.'_AUTOREGISTCLASS' ,
	'description'	=> '' ,
	'formtype'		=> 'text' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'PicoAutoRegisterWraps' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'top_message' ,
	'title'			=> $constpref.'_TOP_MESSAGE' ,
	'description'	=> '' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_TOP_MESSAGEDEFAULT') ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_menuinmoduletop' ,
	'title'			=> $constpref.'_MENUINMODULETOP' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_listasindex' ,
	'title'			=> $constpref.'_LISTASINDEX' ,
	'description'	=> $constpref.'_LISTASINDEXDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_breadcrumbs' ,
	'title'			=> $constpref.'_SHOW_BREADCRUMBS' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_pagenavi' ,
	'title'			=> $constpref.'_SHOW_PAGENAVI' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_printicon' ,
	'title'			=> $constpref.'_SHOW_PRINTICON' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'show_tellafriend' ,
	'title'			=> $constpref.'_SHOW_TELLAFRIEND' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'use_taf_module' ,
	'title'			=> $constpref.'_USE_TAFMODULE' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'search_by_uid' ,
	'title'			=> $constpref.'_SEARCHBYUID' ,
	'description'	=> $constpref.'_SEARCHBYUIDDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'filters' ,
	'title'			=> $constpref.'_FILTERS' ,
	'description'	=> $constpref.'_FILTERSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_FILTERSDEFAULT') ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'filters_forced' ,
	'title'			=> $constpref.'_FILTERSF' ,
	'description'	=> $constpref.'_FILTERSFDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'filters_prohibited' ,
	'title'			=> $constpref.'_FILTERSP' ,
	'description'	=> $constpref.'_FILTERSPDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'submenu_showcontents' ,
	'title'			=> $constpref.'_SUBMENU_SC' ,
	'description'	=> $constpref.'_SUBMENU_SCDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'sitemap_showcontents' ,
	'title'			=> $constpref.'_SITEMAP_SC' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'use_vote' ,
	'title'			=> $constpref.'_USE_VOTE' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'guest_vote_interval' ,
	'title'			=> $constpref.'_GUESTVOTE_IVL' ,
	'description'	=> $constpref.'_GUESTVOTE_IVLDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 86400 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'htmlheader' ,
	'title'			=> $constpref.'_HTMLHEADER' ,
	'description'	=> '' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_each_htmlheader' ,
	'title'			=> $constpref.'_ALLOWEACHHEAD' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'css_uri' ,
	'title'			=> $constpref.'_CSS_URI' ,
	'description'	=> $constpref.'_CSS_URIDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '{mod_url}/index.php?page=main_css' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'images_dir' ,
	'title'			=> $constpref.'_IMAGES_DIR' ,
	'description'	=> $constpref.'_IMAGES_DIRDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'images' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'body_editor' ,
	'title'			=> $constpref.'_BODY_EDITOR' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'xoopsdhtml' ,
	'options'		=> array( 'xoopsdhtml' => 'xoopsdhtml' , 'common/fckeditor' => 'common_fckeditor' )
) ;

$modversion['config'][] = array(
	'name'			=> 'htmlpurify_except' ,
	'title'			=> $constpref.'_HTMLPR_EXCEPT' ,
	'description'	=> $constpref.'_HTMLPR_EXCEPTDSC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(1,2,4) ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'histories_per_content' ,
	'title'			=> $constpref.'_HISTORY_P_C' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 20 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'minlifetime_per_history' ,
	'title'			=> $constpref.'_MLT_HISTORY' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 300 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'browser_cache' ,
	'title'			=> $constpref.'_BRCACHE' ,
	'description'	=> $constpref.'_BRCACHEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 3600 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'extra_fields_class' ,
	'title'			=> $constpref.'_EF_CLASS' ,
	'description'	=> $constpref.'_EF_CLASSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'PicoExtraFields' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'uri_mapper_class' ,
	'title'			=> $constpref.'_URIM_CLASS' ,
	'description'	=> $constpref.'_URIM_CLASSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'PicoUriMapper' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'extra_images_dir' ,
	'title'			=> $constpref.'_EFIMAGES_DIR' ,
	'description'	=> $constpref.'_EFIMAGES_DIRDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'uploads/'.$mydirname ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'extra_images_size' ,
	'title'			=> $constpref.'_EFIMAGES_SIZE' ,
	'description'	=> $constpref.'_EFIMAGES_SIZEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '480x480 160x160' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'image_magick_path' ,
	'title'			=> $constpref.'_IMAGICK_PATH' ,
	'description'	=> $constpref.'_IMAGICK_PATHDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_dirname' ,
	'title'			=> $constpref.'_COM_DIRNAME' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_forum_id' ,
	'title'			=> $constpref.'_COM_FORUM_ID' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '0' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_order' ,
	'title'			=> $constpref.'_COM_ORDER' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'desc' ,
	'options'		=> array( '_OLDESTFIRST' => 'asc' , '_NEWESTFIRST' => 'desc' )
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_view' ,
	'title'			=> $constpref.'_COM_VIEW' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'listposts_flat' ,
	'options'		=> array( '_FLAT' => 'listposts_flat' , '_THREADED' => 'listtopics' )
) ;

$modversion['config'][] = array(
	'name'			=> 'comment_posts_num' ,
	'title'			=> $constpref.'_COM_POSTSNUM' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '10' ,
	'options'		=> array()
) ;


// Notification
$modversion['hasNotification'] = 1 ;
$modversion['notification'] = array(
	'lookup_file' => 'notification.php' ,
	'lookup_func' => "{$mydirname}_notify_iteminfo" ,
	'category' => array(
		array(
			'name' => 'global' ,
			'title' => constant($constpref.'_NOTCAT_GLOBAL') ,
			'description' => constant($constpref.'_NOTCAT_GLOBALDSC') ,
			'subscribe_from' => 'index.php' ,
		) ,
		array(
			'name' => 'category' ,
			'title' => constant($constpref.'_NOTCAT_CATEGORY') ,
			'description' => constant($constpref.'_NOTCAT_CATEGORYDSC') ,
			'subscribe_from' => 'index.php' ,
			'item_name' => 'cat_id' ,
		) ,
		array(
			'name' => 'content' ,
			'title' => constant($constpref.'_NOTCAT_CONTENT') ,
			'description' => constant($constpref.'_NOTCAT_CONTENTDSC') ,
			'subscribe_from' => 'index.php' ,
			'item_name' => 'content_id' ,
		) ,
	) ,
	'event' => array(
		array(
			'name' => 'waitingcontent' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP') ,
			'mail_template' => 'global_waitingcontent' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ') ,
		) ,
		array(
			'name' => 'newcontent' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWCONTENT') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP') ,
			'mail_template' => 'global_newcontent' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ') ,
		) ,
		array(
			'name' => 'newcontent' ,
			'category' => 'category' ,
			'title' => constant($constpref.'_NOTIFY_CATEGORY_NEWCONTENT') ,
			'caption' => constant($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP') ,
			'description' => constant($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP') ,
			'mail_template' => 'category_newcontent' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CATEGORY_NEWCONTENTSBJ') ,
		) ,
		array(
			'name' => 'comment' ,
			'category' => 'content' ,
			'title' => constant($constpref.'_NOTIFY_CONTENT_COMMENT') ,
			'caption' => constant($constpref.'_NOTIFY_CONTENT_COMMENTCAP') ,
			'description' => constant($constpref.'_NOTIFY_CONTENT_COMMENTCAP') ,
			'mail_template' => 'content_comment' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CONTENT_COMMENTSBJ') ,
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
