<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name']        = constant($constpref.'_NAME');
$modversion['version']     = 3.0; // Pack2011 Version
$modversion['description'] = constant($constpref.'_DESC');
$modversion['credits']     = 'suin';
$modversion['help']        = '';
$modversion['license']     = 'GPL see LICENSE';
$modversion['official']    = 0;
$modversion['image']       = file_exists( $mydirpath.'/module_icon.png' ) ? 'module_icon.png' : 'module_icon.php' ;
$modversion['dirname']     = $mydirname;
$modversion['trust_dirname'] = $mytrustdirname ;

// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false ;
$modversion['tables'] = array() ;

// Admin things
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$i = 1;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME1');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC1');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_topics_show";
$modversion['blocks'][$i]['options']     = $mydirname;
$modversion['blocks'][$i]['template']    = "{$mydirname}_block_topics.html";
$i++;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME2');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC2');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_bigstory_show";
$modversion['blocks'][$i]['edit_func']   = "b_bulletin_bigstory_edit";//ver3.0 added
$modversion['blocks'][$i]['options']     = "$mydirname|0";//ver3.0 changed
$modversion['blocks'][$i]['template']    = "{$mydirname}_block_bigstory.html";
$i++;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME3');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC3');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_calendar_show";
$modversion['blocks'][$i]['options']     = $mydirname;
$i++;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME4');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC4');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_new_show";
$modversion['blocks'][$i]['edit_func']   = "b_bulletin_new_edit";
$modversion['blocks'][$i]['options']     = "$mydirname|published DESC|10|255|0|0";//ver3.0 changed
$modversion['blocks'][$i]['template']    = "{$mydirname}_block_new.html";
$modversion['blocks'][$i]['can_clone']   = true ;
$i++;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME5');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC5');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_category_new_show";
$modversion['blocks'][$i]['edit_func']   = "b_bulletin_category_new_edit";
$modversion['blocks'][$i]['options']     = "$mydirname|published DESC|5|255|0|0|0";//ver3.0 changed
$modversion['blocks'][$i]['template']    = "{$mydirname}_block_category_new.html";
$modversion['blocks'][$i]['can_clone']   = true ;
$i++;
$modversion['blocks'][$i]['file']        = "blocks.php";
$modversion['blocks'][$i]['name']        = constant($constpref.'_BNAME6');
$modversion['blocks'][$i]['description'] = constant($constpref.'_BDESC6');
$modversion['blocks'][$i]['show_func']   = "b_bulletin_recent_comments_show";
$modversion['blocks'][$i]['edit_func']   = "b_bulletin_recent_comments_edit";//ver3.0 added
$modversion['blocks'][$i]['options']     = "$mydirname|0";//ver3.0 changed
$modversion['blocks'][$i]['template']    = "{$mydirname}_block_comments.html";

// Menu
$modversion['hasMain'] = 1;
$modversion['read_any'] = true ; // nonsense for other than XCL2.1
$modversion['sub'][1]['name'] = constant($constpref.'_SMNAME1');
$modversion['sub'][1]['url']  = 'index.php?page=submit';
$modversion['sub'][2]['name'] = constant($constpref.'_SMNAME2');
$modversion['sub'][2]['url']  = 'index.php?page=archive';

// Submenu (just for mainmenu)
$modversion['sub'] = array() ;
if( is_object( @$GLOBALS['xoopsModule'] ) && $GLOBALS['xoopsModule']->getVar('dirname') == $mydirname ) {
	require_once dirname(__FILE__).'/include/common_functions.php' ;
	$modversion['sub'] = bulletin_get_submenu( $mydirname ) ;
} else {
	$_sub_menu_cache = XOOPS_TRUST_PATH . '/cache/'. urlencode(substr(XOOPS_URL, 7)) . '_' . $mydirname . '_' . (is_object(@$GLOBALS['xoopsUser'])? join('-', $GLOBALS['xoopsUser']->getGroups()):XOOPS_GROUP_ANONYMOUS)  . '_' . $GLOBALS['xoopsConfig']['language'] . '.submenu';
	if (is_file($_sub_menu_cache) && time() - 3600 < filemtime($_sub_menu_cache)) {
		$modversion['sub'] = unserialize(file_get_contents($_sub_menu_cache));
	} else {
		require_once dirname(__FILE__).'/include/common_functions.php' ;
		$modversion['sub'] = bulletin_get_submenu( $mydirname ) ;
		file_put_contents($_sub_menu_cache, serialize($modversion['sub']));
	}
}

// Search
$modversion['hasSearch'] = 1 ;
$modversion['search']['file'] = 'search.php' ;
$modversion['search']['func'] = $mydirname.'_global_search' ;

// Comments
$modversion['hasComments'] = 1;
$modversion['comments']['pageName'] = 'index.php';
$modversion['comments']['itemName'] = 'storyid';
$modversion['comments']['extraParams'] = array('page');
// Comment callback functions
$modversion['comments']['callbackFile'] = 'comment_functions.php';
$modversion['comments']['callback']['approve'] = 'bulletin_com_approve';
$modversion['comments']['callback']['update']  = 'bulletin_com_update';

// Config Settings
$i = 1;
$modversion['config'][$i]['name']        = 'storyhome';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG1';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG1_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 5;
$modversion['config'][$i]['options']     = array();
$i++;
$modversion['config'][$i]['name']        = 'displaynav';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG2';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG2_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'post_tray_row';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG3';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG3_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 15;
$i++;
$modversion['config'][$i]['name']        = 'post_tray_col';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG4';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG4_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 60;
$i++;
$modversion['config'][$i]['name']        = 'date_format';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG5';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG5_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = 'Y-m-d H:i:s';
$i++;
$modversion['config'][$i]['name']        = 'plus_posts';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG6';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG6_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
$i++;
$modversion['config'][$i]['name']        = 'topicon_path';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG7';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG7_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = XOOPS_ROOT_PATH . '/modules/'.$mydirname.'/images/topics/';
$i++;
$modversion['config'][$i]['name']        = 'imgurl_on_print';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG8';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG8_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default']     = XOOPS_URL.'/images/logo.gif';
$i++;
$modversion['config'][$i]['name']        = 'titile_as_sitename';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG9';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG9_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'assing_rssurl_head';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG10';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG10_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'disp_print_icon';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG11';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG11_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'disp_tell_icon';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG12';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG12_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'use_tell_a_frined';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG13';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG13_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;
$i++;
$modversion['config'][$i]['name']        = 'disp_rss_link';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG14';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG14_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'feed_as_backend';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG145';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG145_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'use_relations';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG15';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG15_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'disp_list_of_cat';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG16';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG16_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'stories_of_cat';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG17';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG17_D';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 10;
$i++;
$modversion['config'][$i]['name']        = 'use_pankuzu';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG18';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG18_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 1;
$i++;
$modversion['config'][$i]['name']        = 'use_fckeditor';
$modversion['config'][$i]['title']       = $constpref.'_CONFIG19';
$modversion['config'][$i]['description'] = $constpref.'_CONFIG19_D';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default']     = 0;

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
$modversion['hasNotification'] = 1;
$modversion['notification']['lookup_file'] = 'notification.php';
$modversion['notification']['lookup_func'] = '{$mydirname}_notify_iteminfo';

$modversion['notification']['category'][1]['name']           = 'global';
$modversion['notification']['category'][1]['title']          = constant($constpref.'_GLOBAL_NOTIFY');
$modversion['notification']['category'][1]['description']    = constant($constpref.'_GLOBAL_NOTIFYDSC');
$modversion['notification']['category'][1]['subscribe_from'] = array('index.php', 'article.php');

$modversion['notification']['category'][2]['name']           = 'story';
$modversion['notification']['category'][2]['title']          = constant($constpref.'_STORY_NOTIFY');
$modversion['notification']['category'][2]['description']    = constant($constpref.'_STORY_NOTIFYDSC');
$modversion['notification']['category'][2]['subscribe_from'] = array('index.php', 'article.php');
$modversion['notification']['category'][2]['item_name']      = 'storyid';
$modversion['notification']['category'][2]['allow_bookmark'] = 1;

$modversion['notification']['event'][1]['name']          = 'new_category';
$modversion['notification']['event'][1]['category']      = 'global';
$modversion['notification']['event'][1]['title']         = constant($constpref.'_GLOBAL_NEWCATEGORY_NOTIFY');
$modversion['notification']['event'][1]['caption']       = constant($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYCAP');
$modversion['notification']['event'][1]['description']   = constant($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYDSC');
$modversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$modversion['notification']['event'][1]['mail_subject']  = constant($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYSBJ');

$modversion['notification']['event'][2]['name']          = 'story_submit';
$modversion['notification']['event'][2]['category']      = 'global';
//$modversion['notification']['event'][2]['admin_only']    = 1;//ver3.0beta3 changed
$modversion['notification']['event'][2]['title']         = constant($constpref.'_GLOBAL_STORYSUBMIT_NOTIFY');
$modversion['notification']['event'][2]['caption']       = constant($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYCAP');
$modversion['notification']['event'][2]['description']   = constant($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYDSC');
$modversion['notification']['event'][2]['mail_template'] = 'global_storysubmit_notify';
$modversion['notification']['event'][2]['mail_subject']  = constant($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYSBJ');

$modversion['notification']['event'][3]['name']          = 'new_story';
$modversion['notification']['event'][3]['category']      = 'global';
$modversion['notification']['event'][3]['title']         = constant($constpref.'_GLOBAL_NEWSTORY_NOTIFY');
$modversion['notification']['event'][3]['caption']       = constant($constpref.'_GLOBAL_NEWSTORY_NOTIFYCAP');
$modversion['notification']['event'][3]['description']   = constant($constpref.'_GLOBAL_NEWSTORY_NOTIFYDSC');
$modversion['notification']['event'][3]['mail_template'] = 'global_newstory_notify';
$modversion['notification']['event'][3]['mail_subject']  = constant($constpref.'_GLOBAL_NEWSTORY_NOTIFYSBJ');

$modversion['notification']['event'][4]['name']          = 'approve';
$modversion['notification']['event'][4]['category']      = 'story';
$modversion['notification']['event'][4]['invisible']     = 1;
$modversion['notification']['event'][4]['title']         = constant($constpref.'_STORY_APPROVE_NOTIFY');
$modversion['notification']['event'][4]['caption']       = constant($constpref.'_STORY_APPROVE_NOTIFYCAP');
$modversion['notification']['event'][4]['description']   = constant($constpref.'_STORY_APPROVE_NOTIFYDSC');
$modversion['notification']['event'][4]['mail_template'] = 'story_approve_notify';
$modversion['notification']['event'][4]['mail_subject']  = constant($constpref.'_STORY_APPROVE_NOTIFYSBJ');

$modversion['notification']['event'][5]['name']          = 'comment';
$modversion['notification']['event'][5]['category']      = 'story';
$modversion['notification']['event'][5]['title']         = constant($constpref.'_NOTIFY5_TITLE');
$modversion['notification']['event'][5]['caption']       = constant($constpref.'_NOTIFY5_CAPTION');
$modversion['notification']['event'][5]['description']   = constant($constpref.'_NOTIFY5_DESC');
$modversion['notification']['event'][5]['mail_template'] = 'story_comment';
$modversion['notification']['event'][5]['mail_subject']  = constant($constpref.'_NOTIFY5_SUBJECT');

$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

?>