<?php

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname , false ) ;

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$modversion['name'] = constant($constpref.'_NAME') ;
$modversion['description'] = constant($constpref.'_DESC') ;
$modversion['version'] = 0.86 ;
$modversion['credits'] = "PEAK Corp. and JIDAIKOBO";
$modversion['author'] = "GIJ=CHECKMATE and JIDAIKOBO and hackd naao, nao-pon, domifara" ;
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
$modversion['hasMain'] = 1 ;
$modversion['read_any'] = true ;

// Submenu (just for mainmenu)
$modversion['sub'] = array() ;
if( is_object( @$GLOBALS['xoopsModule'] ) && $GLOBALS['xoopsModule']->getVar('dirname') == $mydirname ) {
	require_once dirname(__FILE__).'/include/common_functions.php' ;
	$modversion['sub'] = d3forum_get_submenu( $mydirname ) ;
} else {
	$_sub_menu_cache = XOOPS_TRUST_PATH . '/cache/'. urlencode(substr(XOOPS_URL, 7)) . '_' . $mydirname . '_' . (is_object(@$GLOBALS['xoopsUser'])? join('-', $GLOBALS['xoopsUser']->getGroups()):XOOPS_GROUP_ANONYMOUS)  . '_' . $GLOBALS['xoopsConfig']['language'] . '.submenu';
	if (is_file($_sub_menu_cache) && time() - 3600 < filemtime($_sub_menu_cache)) {
		$modversion['sub'] = unserialize(file_get_contents($_sub_menu_cache));
	} else {
		require_once dirname(__FILE__).'/include/common_functions.php' ;
		$modversion['sub'] = d3forum_get_submenu( $mydirname ) ;
		file_put_contents($_sub_menu_cache, serialize($modversion['sub']));
	}
}

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = array() ;

// Blocks
$modversion['blocks'][1] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_LIST_TOPICS') ,
	'description'	=> constant($constpref.'_BDESC_LIST_TOPICS') ,
	'show_func'		=> 'b_d3forum_list_topics_show' ,
	'edit_func'		=> 'b_d3forum_list_topics_edit' ,
	'options'		=> "$mydirname|10|1|time|1|0||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][2] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_LIST_POSTS') ,
	'description'	=> '' ,
	'show_func'		=> 'b_d3forum_list_posts_show' ,
	'edit_func'		=> 'b_d3forum_list_posts_edit' ,
	'options'		=> "$mydirname|10|time|0||" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

$modversion['blocks'][3] = array(
	'file'			=> 'blocks.php' ,
	'name'			=> constant($constpref.'_BNAME_LIST_FORUMS') ,
	'description'	=> '' ,
	'show_func'		=> 'b_d3forum_list_forums_show' ,
	'edit_func'		=> 'b_d3forum_list_forums_edit' ,
	'options'		=> "$mydirname|0|" ,
	'template'		=> '' , // use "module" template instead
	'can_clone'		=> true ,
) ;

// Comments
$modversion['hasComments'] = 0 ;

// Configs
$modversion['config'][1] = array(
	'name'			=> 'top_message' ,
	'title'			=> $constpref.'_TOP_MESSAGE' ,
	'description'	=> '' ,
	'formtype'		=> 'textarea' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant($constpref.'_TOP_MESSAGEDEFAULT') ,
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
	'name'			=> 'default_options' ,
	'title'			=> $constpref.'_DEFAULT_OPTIONS' ,
	'description'	=> $constpref.'_DEFAULT_OPTIONSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'smiley,xcode,br,number_entity' ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'use_name' ,
	'title'			=> $constpref.'_USENAME' ,
	'description'		=> $constpref.'_USENAMEDESC' ,
	'formtype'		=> 'select',
	'valuetype'		=> 'int',
	'default'		=> '0',
	'options'		=> array( $constpref.'_USENAME_UNAME' => 0, $constpref.'_USENAME_NAME' => 1)
);

$modversion['config'][] = array(
	'name'			=> 'allow_html' ,
	'title'			=> $constpref.'_ALLOW_HTML' ,
	'description'	=> $constpref.'_ALLOW_HTMLDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_textimg' ,
	'title'			=> $constpref.'_ALLOW_TEXTIMG' ,
	'description'	=> $constpref.'_ALLOW_TEXTIMGDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_sig' ,
	'title'			=> $constpref.'_ALLOW_SIG' ,
	'description'	=> $constpref.'_ALLOW_SIGDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 1 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_sigimg' ,
	'title'			=> $constpref.'_ALLOW_SIGIMG' ,
	'description'	=> $constpref.'_ALLOW_SIGIMGDSC' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'posts_per_topic' ,
	'title'			=> $constpref.'_POSTS_PER_TOPIC' ,
	'description'	=> $constpref.'_POSTS_PER_TOPICDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 50 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'hot_threshold' ,
	'title'			=> $constpref.'_HOT_THRESHOLD' ,
	'description'	=> $constpref.'_HOT_THRESHOLDDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 10 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'topics_per_page' ,
	'title'			=> $constpref.'_TOPICS_PER_PAGE' ,
	'description'	=> $constpref.'_TOPICS_PER_PAGEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 20 ,
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
	'name'			=> 'use_solved' ,
	'title'			=> $constpref.'_USE_SOLVED' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_mark' ,
	'title'			=> $constpref.'_ALLOW_MARK' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'allow_hideuid' ,
	'title'			=> $constpref.'_ALLOW_HIDEUID' ,
	'description'	=> '' ,
	'formtype'		=> 'yesno' ,
	'valuetype'		=> 'int' ,
	'default'		=> 0 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'viewallbreak' ,
	'title'			=> $constpref.'_VIEWALLBREAK' ,
	'description'	=> $constpref.'_VIEWALLBREAKDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 10 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'selfeditlimit' ,
	'title'			=> $constpref.'_SELFEDITLIMIT' ,
	'description'	=> $constpref.'_SELFEDITLIMITDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'int' ,
	'default'		=> 31536000 ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'selfdellimit' ,
	'title'			=> $constpref.'_SELFDELLIMIT' ,
	'description'	=> $constpref.'_SELFDELLIMITDSC' ,
	'formtype'		=> 'textbox' ,
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
	'description'	=> $constpref.'_BODY_EDITORDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'xoopsdhtml' ,
	'options'		=> array( 'xoopsdhtml' => 'xoopsdhtml' /*, 'common/spaw' => 'common_spaw' */, 'common/fckeditor' => 'common_fckeditor' )
) ;

$modversion['config'][] = array(
	'name'			=> 'anonymous_name' ,
	'title'			=> $constpref.'_ANONYMOUS_NAME' ,
	'description'	=> $constpref.'_ANONYMOUS_NAMEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> _GUESTS ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'icon_meanings' ,
	'title'			=> $constpref.'_ICON_MEANINGS' ,
	'description'	=> $constpref.'_ICON_MEANINGSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> constant( $constpref.'_ICON_MEANINGSDEF' ) ,
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
	'name'			=> 'antispam_groups' ,
	'title'			=> $constpref.'_ANTISPAM_GROUPS' ,
	'description'	=> $constpref.'_ANTISPAM_GROUPSDSC' ,
	'formtype'		=> 'group_multi' ,
	'valuetype'		=> 'array' ,
	'default'		=> array(3) ,
	'options'		=> array()
) ;

$modversion['config'][] = array(
	'name'			=> 'antispam_class' ,
	'title'			=> $constpref.'_ANTISPAM_CLASS' ,
	'description'	=> $constpref.'_ANTISPAM_CLASSDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'defaultmobilesmart' ,
	'options'		=> array()
) ;



// Notification
$modversion['hasNotification'] = 1;
$modversion['notification'] = array(
	'lookup_file' => 'notification.php' ,
	'lookup_func' => "{$mydirname}_notify_iteminfo" ,
	'category' => array(
		array(
			'name' => 'topic' ,
			'title' => constant($constpref.'_NOTCAT_TOPIC') ,
			'description' => constant($constpref.'_NOTCAT_TOPICDSC') ,
			'subscribe_from' => 'index.php' ,
			'item_name' => 'topic_id' ,
			'allow_bookmark' => 1 ,
		) ,
		array(
			'name' => 'forum' ,
			'title' => constant($constpref.'_NOTCAT_FORUM') ,
			'description' => constant($constpref.'_NOTCAT_FORUMDSC') ,
			'subscribe_from' => 'index.php' ,
			'item_name' => 'forum_id' ,
			'allow_bookmark' => 1 ,
		) ,
		array(
			'name' => 'category' ,
			'title' => constant($constpref.'_NOTCAT_CAT') ,
			'description' => constant($constpref.'_NOTCAT_CATDSC') ,
			'subscribe_from' => 'index.php' ,
			'item_name' => 'cat_id' ,
			'allow_bookmark' => 1 ,
		) ,
		array(
			'name' => 'global' ,
			'title' => constant($constpref.'_NOTCAT_GLOBAL') ,
			'description' => constant($constpref.'_NOTCAT_GLOBALDSC') ,
			'subscribe_from' => 'index.php' ,
		) ,
	) ,
	'event' => array(
		array(
			'name' => 'newpost' ,
			'category' => 'topic' ,
			'title' => constant($constpref.'_NOTIFY_TOPIC_NEWPOST') ,
			'caption' => constant($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP') ,
			'description' => constant($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP') ,
			'mail_template' => 'topic_newpost' ,
			'mail_subject' => constant($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ') ,
		) ,
		array(
			'name' => 'newpost' ,
			'category' => 'forum' ,
			'title' => constant($constpref.'_NOTIFY_FORUM_NEWPOST') ,
			'caption' => constant($constpref.'_NOTIFY_FORUM_NEWPOSTCAP') ,
			'description' => constant($constpref.'_NOTIFY_FORUM_NEWPOSTCAP') ,
			'mail_template' => 'forum_newpost' ,
			'mail_subject' => constant($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ') ,
		) ,
		array(
			'name' => 'newtopic' ,
			'category' => 'forum' ,
			'title' => constant($constpref.'_NOTIFY_FORUM_NEWTOPIC') ,
			'caption' => constant($constpref.'_NOTIFY_FORUM_NEWTOPICCAP') ,
			'description' => constant($constpref.'_NOTIFY_FORUM_NEWTOPICCAP') ,
			'mail_template' => 'forum_newtopic' ,
			'mail_subject' => constant($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ') ,
		) ,
		array(
			'name' => 'newpost' ,
			'category' => 'category' ,
			'title' => constant($constpref.'_NOTIFY_CAT_NEWPOST') ,
			'caption' => constant($constpref.'_NOTIFY_CAT_NEWPOSTCAP') ,
			'description' => constant($constpref.'_NOTIFY_CAT_NEWPOSTCAP') ,
			'mail_template' => 'category_newpost' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CAT_NEWPOSTSBJ') ,
		) ,
		array(
			'name' => 'newtopic' ,
			'category' => 'category' ,
			'title' => constant($constpref.'_NOTIFY_CAT_NEWTOPIC') ,
			'caption' => constant($constpref.'_NOTIFY_CAT_NEWTOPICCAP') ,
			'description' => constant($constpref.'_NOTIFY_CAT_NEWTOPICCAP') ,
			'mail_template' => 'category_newtopic' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CAT_NEWTOPICSBJ') ,
		) ,
		array(
			'name' => 'newforum' ,
			'category' => 'category' ,
			'title' => constant($constpref.'_NOTIFY_CAT_NEWFORUM') ,
			'caption' => constant($constpref.'_NOTIFY_CAT_NEWFORUMCAP') ,
			'description' => constant($constpref.'_NOTIFY_CAT_NEWFORUMCAP') ,
			'mail_template' => 'category_newforum' ,
			'mail_subject' => constant($constpref.'_NOTIFY_CAT_NEWFORUMSBJ') ,
		) ,
		array(
			'name' => 'newpost' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOST') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP') ,
			'mail_template' => 'global_newpost' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ') ,
		) ,
		array(
			'name' => 'newtopic' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWTOPIC') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP') ,
			'mail_template' => 'global_newtopic' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ') ,
		) ,
		array(
			'name' => 'newforum' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWFORUM') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP') ,
			'mail_template' => 'global_newforum' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ') ,
		) ,
		array(
			'name' => 'newpostfull' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP') ,
			'mail_template' => 'global_newpostfull' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ') ,
		) ,
		array(
			'name' => 'waiting' ,
			'category' => 'global' ,
			'title' => constant($constpref.'_NOTIFY_GLOBAL_WAITING') ,
			'caption' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCAP') ,
			'description' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGCAP') ,
			'mail_template' => 'global_waiting' ,
			'mail_subject' => constant($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ') ,
			'admin_only' => 1 ,
		) ,
	) ,
) ;

$modversion['onInstall'] = 'oninstall.php' ;
$modversion['onUpdate'] = 'onupdate.php' ;
$modversion['onUninstall'] = 'onuninstall.php' ;

// keep block's options
if( ! defined( 'XOOPS_CUBE_LEGACY' ) && substr( XOOPS_VERSION , 6 , 3 ) < 2.1 && ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname(__FILE__).'/include/x20_keepblockoptions.inc.php' ;
}

?>
