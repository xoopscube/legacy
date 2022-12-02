<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Naoki Sawada (aka Nao-pon)
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

// language file (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH . '/libs/altsys/class/D3LanguageManager.class.php';

if ( ! file_exists( $langmanpath ) ) {
	die( 'install the module UI Components' );
}

require_once( $langmanpath );

$langman = D3LanguageManager::getInstance();
$langman->read( 'modinfo.php', $mydirname, $mytrustdirname, false );

$constpref = '_MI_' . strtoupper( $mydirname );

// Manifesto
$modversion['dirname']          = $mydirname;
$modversion['trust_dirname']    = $mytrustdirname;
$modversion['name']             = constant( $constpref . '_NAME' );
$modversion['version']          = '2.31';
$modversion['detailed_version'] = '2.31.4';
$modversion['description']      = constant( $constpref . '_DESC' );
$modversion['author']           = 'Gijoe (peak.ne.jp) and Jidaikbo, @nao-pon Naoki Sawada';
$modversion['credits']          = '@domifara, @naao Naoki Okino, @nao-pon Naoki Sawada, @gigamaster (XCL/PHP7)';
$modversion['license']          = 'GPL';
$modversion['image']            = '/images/module_forum.svg';
$modversion['icon']             = 'images/module_icon.svg';
$modversion['help']             = 'help.html';
$modversion['official']         = 0;
$modversion['cube_style']       = true;

// SQL
// Any tables can't be touched by modulesadmin.
$modversion['sqlfile'] = false;
$modversion['tables']  = [];

// Admin
$modversion['hasAdmin']   = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu']  = 'admin/admin_menu.php';

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'search.php';
$modversion['search']['func'] = $mydirname . '_global_search';

// Menu
$modversion['hasMain']  = 1;
$modversion['read_any'] = true;

// Submenu (just for mainmenu)
$modversion['sub'] = [];

if ( is_object( @$GLOBALS['xoopsModule'] ) && $GLOBALS['xoopsModule']->getVar( 'dirname' ) == $mydirname ) {
	require_once __DIR__ . '/include/common_functions.php';
	$modversion['sub'] = d3forum_get_submenu( $mydirname );
} else {
	$_sub_menu_cache = XOOPS_TRUST_PATH . '/cache/' . urlencode( substr( XOOPS_URL, 7 ) ) . '_' . $mydirname . '_' . ( is_object( @$GLOBALS['xoopsUser'] ) ? implode( '-', $GLOBALS['xoopsUser']->getGroups() ) : XOOPS_GROUP_ANONYMOUS ) . '_' . $GLOBALS['xoopsConfig']['language'] . '.submenu';
	if ( is_file( $_sub_menu_cache ) && time() - 3600 < filemtime( $_sub_menu_cache ) ) {
		$modversion['sub'] = unserialize( file_get_contents( $_sub_menu_cache ) );
	} else {
		require_once __DIR__ . '/include/common_functions.php';
		$modversion['sub'] = d3forum_get_submenu( $mydirname );
		file_put_contents( $_sub_menu_cache, serialize( $modversion['sub'] ) );
	}
}

// All Templates can't be touched by modulesadmin.
$modversion['templates'] = [];

// Blocks
$modversion['blocks'][1] = [
	'file'        => 'blocks.php',
	'name'        => constant( $constpref . '_BNAME_LIST_TOPICS' ),
	'description' => constant( $constpref . '_BDESC_LIST_TOPICS' ),
	'show_func'   => 'b_d3forum_list_topics_show',
	'edit_func'   => 'b_d3forum_list_topics_edit',
	'options'     => "$mydirname|10|1|time|1|0||",
	'template'    => '', // use "module" template instead
	'can_clone'   => true,
];

$modversion['blocks'][2] = [
	'file'        => 'blocks.php',
	'name'        => constant( $constpref . '_BNAME_LIST_POSTS' ),
	'description' => '',
	'show_func'   => 'b_d3forum_list_posts_show',
	'edit_func'   => 'b_d3forum_list_posts_edit',
	'options'     => "$mydirname|10|time|0||",
	'template'    => '', // use "module" template instead
	'can_clone'   => true,
];

$modversion['blocks'][3] = [
	'file'        => 'blocks.php',
	'name'        => constant( $constpref . '_BNAME_LIST_FORUMS' ),
	'description' => '',
	'show_func'   => 'b_d3forum_list_forums_show',
	'edit_func'   => 'b_d3forum_list_forums_edit',
	'options'     => "$mydirname|0|",
	'template'    => '', // use "module" template instead
	'can_clone'   => true,
];

// Comments
$modversion['hasComments'] = 0;

// Configs
$modversion['config'][1] = [
	'name'        => 'top_message',
	'title'       => $constpref . '_TOP_MESSAGE',
	'description' => '',
	'formtype'    => 'textarea',
	'valuetype'   => 'text',
	'default'     => constant( $constpref . '_TOP_MESSAGEDEFAULT' ),
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'show_breadcrumbs',
	'title'       => $constpref . '_SHOW_BREADCRUMBS',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
    'name'        => 'show_rss',
    'title'       => $constpref . '_SHOW_RSS',
    'description' => '',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
    'options'     => []
];

$modversion['config'][] = [
	'name'        => 'default_options',
	'title'       => $constpref . '_DEFAULT_OPTIONS',
	'description' => $constpref . '_DEFAULT_OPTIONSDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'smiley,xcode,br,number_entity',
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'use_name',
	'title'       => $constpref . '_USENAME',
	'description' => $constpref . '_USENAMEDESC',
	'formtype'    => 'select',
	'valuetype'   => 'int',
	'default'     => '0',
	'options'     => [ $constpref . '_USENAME_UNAME' => 0, $constpref . '_USENAME_NAME' => 1 ]
];

$modversion['config'][] = [
	'name'        => 'allow_html',
	'title'       => $constpref . '_ALLOW_HTML',
	'description' => $constpref . '_ALLOW_HTMLDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'allow_textimg',
	'title'       => $constpref . '_ALLOW_TEXTIMG',
	'description' => $constpref . '_ALLOW_TEXTIMGDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'allow_sig',
	'title'       => $constpref . '_ALLOW_SIG',
	'description' => $constpref . '_ALLOW_SIGDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'allow_sigimg',
	'title'       => $constpref . '_ALLOW_SIGIMG',
	'description' => $constpref . '_ALLOW_SIGIMGDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'posts_per_topic',
	'title'       => $constpref . '_POSTS_PER_TOPIC',
	'description' => $constpref . '_POSTS_PER_TOPICDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 25,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'hot_threshold',
	'title'       => $constpref . '_HOT_THRESHOLD',
	'description' => $constpref . '_HOT_THRESHOLDDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 10,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'topics_per_page',
	'title'       => $constpref . '_TOPICS_PER_PAGE',
	'description' => $constpref . '_TOPICS_PER_PAGEDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 10,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'use_vote',
	'title'       => $constpref . '_USE_VOTE',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'use_solved',
	'title'       => $constpref . '_USE_SOLVED',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'allow_mark',
	'title'       => $constpref . '_ALLOW_MARK',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 1,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'allow_hideuid',
	'title'       => $constpref . '_ALLOW_HIDEUID',
	'description' => '',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'viewallbreak',
	'title'       => $constpref . '_VIEWALLBREAK',
	'description' => $constpref . '_VIEWALLBREAKDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 10,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'selfeditlimit',
	'title'       => $constpref . '_SELFEDITLIMIT',
	'description' => $constpref . '_SELFEDITLIMITDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 31536000,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'selfdellimit',
	'title'       => $constpref . '_SELFDELLIMIT',
	'description' => $constpref . '_SELFDELLIMITDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'css_uri',
	'title'       => $constpref . '_CSS_URI',
	'description' => $constpref . '_CSS_URIDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => '{mod_url}/index.php?page=main_css',
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'images_dir',
	'title'       => $constpref . '_IMAGES_DIR',
	'description' => $constpref . '_IMAGES_DIRDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'images',
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'body_editor',
	'title'       => $constpref . '_BODY_EDITOR',
	'description' => $constpref . '_BODY_EDITORDSC',
	'formtype'    => 'select',
	'valuetype'   => 'text',
	'default'     => 'xoopsdhtml',
	'options'     => [ 'xoopsdhtml' => 'xoopsdhtml', /*'common/fckeditor' => 'common_fckeditor'*/ ]
];

$modversion['config'][] = [
	'name'        => 'anonymous_name',
	'title'       => $constpref . '_ANONYMOUS_NAME',
	'description' => $constpref . '_ANONYMOUS_NAMEDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => _GUESTS,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'icon_meanings',
	'title'       => $constpref . '_ICON_MEANINGS',
	'description' => $constpref . '_ICON_MEANINGSDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => constant( $constpref . '_ICON_MEANINGSDEF' ),
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'guest_vote_interval',
	'title'       => $constpref . '_GUESTVOTE_IVL',
	'description' => $constpref . '_GUESTVOTE_IVLDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'int',
	'default'     => 86400,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'antispam_groups',
	'title'       => $constpref . '_ANTISPAM_GROUPS',
	'description' => $constpref . '_ANTISPAM_GROUPSDSC',
	'formtype'    => 'group_multi',
	'valuetype'   => 'array',
	'default'     => [ 3 ],
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'antispam_class',
	'title'       => $constpref . '_ANTISPAM_CLASS',
	'description' => $constpref . '_ANTISPAM_CLASSDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => 'defaultmobilesmart',
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'rss_show_hidden',
	'title'       => $constpref . '_RSS_SHOW_HIDDEN',
	'description' => $constpref . '_RSS_SHOW_HIDDENDSC',
	'formtype'    => 'yesno',
	'valuetype'   => 'int',
	'default'     => 0,
	'options'     => []
];

$modversion['config'][] = [
	'name'        => 'rss_hidden_title',
	'title'       => $constpref . '_RSS_HIDDEN_TITLE',
	'description' => $constpref . '_RSS_HIDDEN_TITLEDSC',
	'formtype'    => 'textbox',
	'valuetype'   => 'text',
	'default'     => '',
	'options'     => []
];

// Notification
$modversion['hasNotification'] = 1;

$modversion['notification'] = [
	'lookup_file' => 'notification.php',
	'lookup_func' => "{$mydirname}_notify_iteminfo",
	'category'    => [
		[
			'name'           => 'topic',
			'title'          => constant( $constpref . '_NOTCAT_TOPIC' ),
			'description'    => constant( $constpref . '_NOTCAT_TOPICDSC' ),
			'subscribe_from' => 'index.php',
			'item_name'      => 'topic_id',
			'allow_bookmark' => 1,
		],
		[
			'name'           => 'forum',
			'title'          => constant( $constpref . '_NOTCAT_FORUM' ),
			'description'    => constant( $constpref . '_NOTCAT_FORUMDSC' ),
			'subscribe_from' => 'index.php',
			'item_name'      => 'forum_id',
			'allow_bookmark' => 1,
		],
		[
			'name'           => 'category',
			'title'          => constant( $constpref . '_NOTCAT_CAT' ),
			'description'    => constant( $constpref . '_NOTCAT_CATDSC' ),
			'subscribe_from' => 'index.php',
			'item_name'      => 'cat_id',
			'allow_bookmark' => 1,
		],
		[
			'name'           => 'global',
			'title'          => constant( $constpref . '_NOTCAT_GLOBAL' ),
			'description'    => constant( $constpref . '_NOTCAT_GLOBALDSC' ),
			'subscribe_from' => 'index.php',
		],
	],
	'event'       => [
		[
			'name'          => 'newpost',
			'category'      => 'topic',
			'title'         => constant( $constpref . '_NOTIFY_TOPIC_NEWPOST' ),
			'caption'       => constant( $constpref . '_NOTIFY_TOPIC_NEWPOSTCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_TOPIC_NEWPOSTCAP' ),
			'mail_template' => 'topic_newpost',
			'mail_subject'  => constant( $constpref . '_NOTIFY_TOPIC_NEWPOSTSBJ' ),
		],
		[
			'name'          => 'newpost',
			'category'      => 'forum',
			'title'         => constant( $constpref . '_NOTIFY_FORUM_NEWPOST' ),
			'caption'       => constant( $constpref . '_NOTIFY_FORUM_NEWPOSTCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_FORUM_NEWPOSTCAP' ),
			'mail_template' => 'forum_newpost',
			'mail_subject'  => constant( $constpref . '_NOTIFY_FORUM_NEWPOSTSBJ' ),
		],
		[
			'name'          => 'newtopic',
			'category'      => 'forum',
			'title'         => constant( $constpref . '_NOTIFY_FORUM_NEWTOPIC' ),
			'caption'       => constant( $constpref . '_NOTIFY_FORUM_NEWTOPICCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_FORUM_NEWTOPICCAP' ),
			'mail_template' => 'forum_newtopic',
			'mail_subject'  => constant( $constpref . '_NOTIFY_FORUM_NEWTOPICSBJ' ),
		],
		[
			'name'          => 'newpost',
			'category'      => 'category',
			'title'         => constant( $constpref . '_NOTIFY_CAT_NEWPOST' ),
			'caption'       => constant( $constpref . '_NOTIFY_CAT_NEWPOSTCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_CAT_NEWPOSTCAP' ),
			'mail_template' => 'category_newpost',
			'mail_subject'  => constant( $constpref . '_NOTIFY_CAT_NEWPOSTSBJ' ),
		],
		[
			'name'          => 'newtopic',
			'category'      => 'category',
			'title'         => constant( $constpref . '_NOTIFY_CAT_NEWTOPIC' ),
			'caption'       => constant( $constpref . '_NOTIFY_CAT_NEWTOPICCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_CAT_NEWTOPICCAP' ),
			'mail_template' => 'category_newtopic',
			'mail_subject'  => constant( $constpref . '_NOTIFY_CAT_NEWTOPICSBJ' ),
		],
		[
			'name'          => 'newforum',
			'category'      => 'category',
			'title'         => constant( $constpref . '_NOTIFY_CAT_NEWFORUM' ),
			'caption'       => constant( $constpref . '_NOTIFY_CAT_NEWFORUMCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_CAT_NEWFORUMCAP' ),
			'mail_template' => 'category_newforum',
			'mail_subject'  => constant( $constpref . '_NOTIFY_CAT_NEWFORUMSBJ' ),
		],
		[
			'name'          => 'newpost',
			'category'      => 'global',
			'title'         => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOST' ),
			'caption'       => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTCAP' ),
			'mail_template' => 'global_newpost',
			'mail_subject'  => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTSBJ' ),
		],
		[
			'name'          => 'newtopic',
			'category'      => 'global',
			'title'         => constant( $constpref . '_NOTIFY_GLOBAL_NEWTOPIC' ),
			'caption'       => constant( $constpref . '_NOTIFY_GLOBAL_NEWTOPICCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_GLOBAL_NEWTOPICCAP' ),
			'mail_template' => 'global_newtopic',
			'mail_subject'  => constant( $constpref . '_NOTIFY_GLOBAL_NEWTOPICSBJ' ),
		],
		[
			'name'          => 'newforum',
			'category'      => 'global',
			'title'         => constant( $constpref . '_NOTIFY_GLOBAL_NEWFORUM' ),
			'caption'       => constant( $constpref . '_NOTIFY_GLOBAL_NEWFORUMCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_GLOBAL_NEWFORUMCAP' ),
			'mail_template' => 'global_newforum',
			'mail_subject'  => constant( $constpref . '_NOTIFY_GLOBAL_NEWFORUMSBJ' ),
		],
		[
			'name'          => 'newpostfull',
			'category'      => 'global',
			'title'         => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTFULL' ),
			'caption'       => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLCAP' ),
			'mail_template' => 'global_newpostfull',
			'mail_subject'  => constant( $constpref . '_NOTIFY_GLOBAL_NEWPOSTFULLSBJ' ),
		],
		[
			'name'          => 'waiting',
			'category'      => 'global',
			'title'         => constant( $constpref . '_NOTIFY_GLOBAL_WAITING' ),
			'caption'       => constant( $constpref . '_NOTIFY_GLOBAL_WAITINGCAP' ),
			'description'   => constant( $constpref . '_NOTIFY_GLOBAL_WAITINGCAP' ),
			'mail_template' => 'global_waiting',
			'mail_subject'  => constant( $constpref . '_NOTIFY_GLOBAL_WAITINGSBJ' ),
			'admin_only'    => 1,
		],
	],
];

$modversion['onInstall']   = 'oninstall.php';
$modversion['onUpdate']    = 'onupdate.php';
$modversion['onUninstall'] = 'onuninstall.php';

// keep block's options
/*if (!defined('XOOPS_CUBE_LEGACY') && substr(XOOPS_VERSION, 6, 3) < 2.1 && !empty($_POST['fct']) && !empty($_POST['op']) && 'modulesadmin' == $_POST['fct'] && 'update_ok' == $_POST['op'] && $_POST['dirname'] == $modversion['dirname']) {
	include __DIR__ . '/include/x20_keepblockoptions.inc.php';
}*/
