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
$modversion['version'] = 0.68 ;
$modversion['credits'] = "PEAK Corp.";
$modversion['author'] = "GIJ=CHECKMATE<br />PEAK Corp.(http://www.peak.ne.jp/)" ;
$modversion['help'] = "" ;
$modversion['license'] = "GPL" ;
$modversion['official'] = 0 ;
$modversion['image'] = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname'] = $mydirname ;
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
	$modversion['sub'] = d3pipes_common_get_submenu( $mydirname ) ;
}

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'][1] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_ASYNC') ,
	'description'	=> '' ,
	'show_func'		=> 'b_d3pipes_async_show' ,
	'edit_func'		=> 'b_d3pipes_async_edit' ,
	'options'		=> "$mydirname|".uniqid(rand())."|1|10|db:{$mydirname}_block_async.html|mergesort|1|0" ,
	'template'		=> '' , // use "module" template instead
	'visible_any'	=> true ,
	'can_clone'		=> true ,
) ;

$modversion['blocks'][2] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_SYNC') ,
	'description'	=> '' ,
	'show_func'		=> 'b_d3pipes_sync_show' ,
	'edit_func'		=> 'b_d3pipes_sync_edit' , // appropriation
	'options'		=> "$mydirname||1|10|db:{$mydirname}_block_sync.html|mergesort|1|0" ,
	'template'		=> '' , // use "module" template instead
	'visible_any'	=> true ,
	'can_clone'		=> true ,
) ;

// Comments
$modversion['hasComments'] = 0 ;

// Configs
$modversion['config'][1] = array(
	'name'			=> 'index_total' ,
	'title'			=> $constpref.'_INDEXTOTAL' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 10 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'index_each' ,
	'title'			=> $constpref.'_INDEXEACH' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 5 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'index_keeppipe' ,
	'title'			=> $constpref.'_INDEXKEEPPIPE' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'entries_per_eachpipe' ,
	'title'			=> $constpref.'_ENTRIESAPIPE' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 20 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'entries_per_cliplist' ,
	'title'			=> $constpref.'_ENTRIESAPAGE' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 20 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'entries_per_rss' ,
	'title'			=> $constpref.'_ENTRIESARSS' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 20 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'entries_per_sitemap' ,
	'title'			=> $constpref.'_ENTRIESSMAP' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1000 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'removeclips_by_fetched' ,
	'title'			=> $constpref.'_ARCB_FETCHED' ,
	'description'	=> $constpref.'_ARCB_FETCHEDDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 30 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'internal_encoding' ,
	'title'			=> $constpref.'_INTERNALENC' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> _CHARSET ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'fetch_cache_life_time' ,
	'title'			=> $constpref.'_FETCHCACHELT' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 600 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'redirect_warning' ,
	'title'			=> $constpref.'_REDIRECTWARN' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_maxredirs' ,
	'title'			=> $constpref.'_SNP_MAXREDIRS' ,
	'description'	=> $constpref.'_SNP_MAXREDIRSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '5' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_proxy_host' ,
	'title'			=> $constpref.'_SNP_PROXYHOST' ,
	'description'	=> $constpref.'_SNP_PROXYHOSTDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_proxy_port' ,
	'title'			=> $constpref.'_SNP_PROXYPORT' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 8080 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_proxy_user' ,
	'title'			=> $constpref.'_SNP_PROXYUSER' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_proxy_pass' ,
	'title'			=> $constpref.'_SNP_PROXYPASS' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'snoopy_curl_path' ,
	'title'			=> $constpref.'_SNP_CURLPATH' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '/usr/bin/curl' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'tidy_path' ,
	'title'			=> $constpref.'_TIDY_PATH' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '/usr/bin/tidy' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'xsltproc_path' ,
	'title'			=> $constpref.'_XSLTPROC_PATH' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> '/usr/bin/xsltproc' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'update_ping_servers' ,
	'title'			=> $constpref.'_UPING_SERVERS' ,
	'description'	=> $constpref.'_UPING_SERVERSDSC'  ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_UPING_SERVERSDEF') ,
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
	'name'			=> 'comment_dirname' ,
	'title'			=> $constpref.'_COM_DIRNAME' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'd3forum' ,
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
	'name'			=> 'comment_view' ,
	'title'			=> $constpref.'_COM_VIEW' ,
	'description'	=> '' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'listposts_flat' ,
	'options'		=> array( '_FLAT' => 'listposts_flat' , '_THREADED' => 'listtopics' )
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
	'name'			=> 'comment_posts_num' ,
	'title'			=> $constpref.'_COM_POSTSNUM' ,
	'description'	=> '' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> '10' ,
	'options'		=> array()
) ;



// Notification
$modversion['hasNotification'] = 0 ;

// onInstall, onUpdate, onUninstall
$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

?>
