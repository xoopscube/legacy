<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: pukiwiki.ini.php,v 1.119 2012/01/30 11:59:09 nao-pon Exp $
// Copyright (C)
//   2002-2006 PukiWiki Developers Team
//   2001-2002 Originally written by yu-ji
// License: GPL v2 or (at your option) any later version
//
// PukiWiki main setting file

/////////////////////////////////////////////////
// Variable initialize
$root->ext_autolinks = array();	// External AutoLink
$root->page_aliases = array(); // Pagename aliases
$root->page_aliases_i = array(); // Pagename aliases (case-insensitive)

/////////////////////////////////////////////////
// Functionality settings

// PKWK_OPTIMISE - Ignore verbose but understandable checking and warning
//   If you end testing this PukiWiki, set '1'.
//   If you feel in trouble about this PukiWiki, set '0'.
$const['PKWK_OPTIMISE'] = 0;

/////////////////////////////////////////////////
// Security settings

// PKWK_SAFE_MODE - Prohibits some unsafe(but compatible) functions
// 'auto': Safe mode( The administer is excluded. )
//     1 : Safe mode
//     0 : Normal mode
$const['PKWK_SAFE_MODE'] = 'auto';

// PKWK_DISABLE_INLINE_IMAGE_FROM_URI - Disallow using inline-image-tag for URIs
//   Inline-image-tag for URIs may allow leakage of Wiki readers' information
//   (in short, 'Web bug') or external malicious CGI (looks like an image's URL)
//   attack to Wiki readers, but easy way to show images.
$const['PKWK_DISABLE_INLINE_IMAGE_FROM_URI'] = 0;

// $const['PKWK_DISABLE_INLINE_IMAGE_FROM_URI'] = 0 の時、
// 外部サイトのファイルは ref プラグインを使用して表示する
$const['SHOW_EXTIMG_BY_REF'] = TRUE;

// ref で内部サイトとみなす URL の正規表現 (PCRE)
$const['NO_REF_EXTIMG_REG'] = '#^http://[^/]+\.(?:static\.?flickr\.com|photozou\.jp)#i';

// In-line display setting of Flash file
// The file owner is ... Disable of all: 0, Only the manager: 1, Only the registered user :2, Allow of all: 3.
// 0 or 1 is strongly encouraged.
// Flash ファイルのインライン表示設定
// ファイルオーナーが...すべて禁止:0 , 管理人のみ:1 , 登録ユーザーのみ:2 , すべて許可:3
// セキュリティ上、0 or 1 での運用を強く奨励
$const['PLUGIN_REF_FLASH_INLINE'] = 1;

// SWF Object でリクエストする Flash バージョン
$const['PLUGIN_REF_FLASH_VERSION'] = '10.0.45.2';

// ref でインライン表示させる MIME タイプと使用プラグインまたはテンプレート名
// インライン表示可能なのは管理人所有のファイルと、管理人が許可したファイルのみ
$const['PLUGIN_REF_MIME_INLINE'] = array();
$const['PLUGIN_REF_MIME_INLINE']['image/svg+xml']               = 'minimum';
$const['PLUGIN_REF_MIME_INLINE']['video/x-flv']                 = 'flash';
$const['PLUGIN_REF_MIME_INLINE']['video/3gpp']                  = 'quicktime';
$const['PLUGIN_REF_MIME_INLINE']['video/3gpp2']                 = 'quicktime';
$const['PLUGIN_REF_MIME_INLINE']['video/mp4']                   = 'quicktime';
$const['PLUGIN_REF_MIME_INLINE']['video/quicktime']             = 'quicktime';
$const['PLUGIN_REF_MIME_INLINE']['video/mpeg']                  = 'quicktime';
$const['PLUGIN_REF_MIME_INLINE']['video/x-ms-asf']              = 'wmp6.4';
$const['PLUGIN_REF_MIME_INLINE']['video/x-ms-wmv']              = 'wmp6.4';
$const['PLUGIN_REF_MIME_INLINE']['video/avi']                   = 'wmp6.4';
$const['PLUGIN_REF_MIME_INLINE']['video/divx']                  = 'divx';
$const['PLUGIN_REF_MIME_INLINE']['video/x-matroska']            = 'divx';
$const['PLUGIN_REF_MIME_INLINE']['application/vnd.rn-realmedia']= 'real';
$const['PLUGIN_REF_MIME_INLINE']['video/ogg']                   = 'html5_video';
$const['PLUGIN_REF_MIME_INLINE']['video/webm']                  = 'html5_video';
$const['PLUGIN_REF_MIME_INLINE']['application/pdf']             = 'google_document_viewer';
$const['PLUGIN_REF_MIME_INLINE']['application/ms-powerpoint']   = 'google_document_viewer';

// プラグインプレーヤーの設定 (<object> 用)
$const['PLUGIN_REF_PLAYERS'] = array();
$const['PLUGIN_REF_PLAYERS']['wmp'] = array(
	'classid'  => 'clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6',
	'codebase' => '',
	'height+'  => 45,
	'width+'   => 0,
	'types'    => 'video/x-ms-wmv video/x-ms-wvx video/x-ms-wm video/x-ms-asf video/x-ms-asf-plugin',
	'banner'   => ''
);

$const['PLUGIN_REF_PLAYERS']['wmp6.4'] = array(
	'classid'  => 'clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95',
	'codebase' => '',
	'height+'  => 45,
	'width+'   => 0,
	'types'    => 'video/x-ms-wmv video/x-ms-wvx video/x-ms-wm video/x-ms-asf video/x-ms-asf-plugin',
	'banner'   => ''
);

$const['PLUGIN_REF_PLAYERS']['quicktime'] = array(
	'classid'  => 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
	'codebase' => 'http://www.apple.com/qtactivex/qtplugin.cab',
	'height+'  => 16,
	'width+'   => 0,
	'types'    => 'video/quicktime video/mp4 video/sd-video video/x-m4v video/3gpp2 video/3gpp video/mpeg video/x-mpeg',
	'banner'   => ''
);

$const['PLUGIN_REF_PLAYERS']['divx'] = array(
	'classid'  => 'clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616',
	'codebase' => 'http://go.divx.com/plugin/DivXBrowserPlugin.cab',
	'height+'  => 20,
	'width+'   => 0,
	'types'    => 'video/divx',
	'banner'   => '<a href="http://www.divx.com/divx/webplayer/"><img src="http://labs.divx.com/files/DivX_Plus_Labs_Banner_Small_en.png"></a>'
);

$const['PLUGIN_REF_PLAYERS']['real'] = array(
	'classid'  => 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA',
	'codebase' => '',
	'height+'  => 0,
	'width+'   => 0,
	'types'    => 'audio/x-pn-realaudio-plugin',
	'banner'   => ''
);

//$const['PLUGIN_REF_PLAYERS']['silverlight'] = array(
//	'classid'  => '',
//	'codebase' => '',
//	'data'     => 'data:application/x-silverlight-2,',
//	'height+'  => 0,
//	'width+'   => 0,
//	'types'    => 'application/x-silverlight-2',
//	'banner'   => ''
//);

// フラッシュプレーヤーの設定
// プレーヤーの配置先は "trust/modules/xpwiki/skin/swf"
//// http://flowplayer.org/
$const['PLUGIN_REF_FLV_PLAYER'] = 'flowplayer-3.2.2.swf';
$const['PLUGIN_REF_FLV_PLAYER_VARS'] = '{"config":\'{"clip":{"url":"$url","autoPlay":false},"plugins":{"controls":{"url":"$srcurlflowplayer.controls-3.2.1.swf"}}}}\'}';
$const['PLUGIN_REF_FLV_PLAYER_CTR_WIDTH'] = 0;
$const['PLUGIN_REF_FLV_PLAYER_CTR_HEIGHT'] = 0;
//// http://rexef.com/webtool/flaver3/
//$const['PLUGIN_REF_FLV_PLAYER'] = 'flaver.swf';
//$const['PLUGIN_REF_FLV_PLAYER_VARS'] = '{"file":"$url","title":"$title"}';
//$const['PLUGIN_REF_FLV_PLAYER_CTR_WIDTH'] = 10;
//$const['PLUGIN_REF_FLV_PLAYER_CTR_HEIGHT'] = 50;

// ネットビデオ(共有サービースの設定)
$const['PLUGIN_REF_NETVIDEOS'] = array();
$const['PLUGIN_REF_NETVIDEOS']['niconico'] = array(
	'regex'     => '#^http://www\.nicovideo\.jp/watch/([0-9a-z_-]+)#i',
	'type'      => 'html',
	'src'       => '<script type="text/javascript" src="http://ext.nicovideo.jp/thumb_watch/$1"></script>'
);

//$const['PLUGIN_REF_NETVIDEOS']['youtube'] = array(
//	'regex'     => '#^http://www\.youtube\.com/watch\?v=([0-9a-z]+)#i',
//	'type'      => 'flash',
//	'src'       => 'http://www.youtube.com/v/$1&hl=ja_JP&fs=1',
//	'width'     => 640,
//	'height'    => 385,
//	'attribute' => 'allowfullscreen="true" allowscriptaccess="always"'
//);

$const['PLUGIN_REF_NETVIDEOS']['youtube'] = array(
	'regex'     => '#^http://www\.youtube\.com/watch\?.*?v=([0-9a-z_-]+)#i',
	'type'      => 'html',
	'src'       => '<iframe class="youtube-player" type="text/html"$size src="http://www.youtube.com/embed/$1?wmode=transparent&amp;autohide=1" frameborder="0"><noiframe>$link</noiframe></iframe>',
	'src_keitai'=> '$link',
	'width'     => 480,
	'height'    => 270,
);

$const['PLUGIN_REF_NETVIDEOS']['google'] = array(
	'regex'     => '#^http://video\.google\.com/videoplay\?docid=([0-9-]+)#i',
	'type'      => 'flash',
	'src'       => 'http://video.google.com/googleplayer.swf?docid=$1&hl=&fs=true',
	'width'     => 480,
	'height'    => 296,
	'attribute' => 'allowfullscreen="true" allowscriptaccess="always"'
);

$const['PLUGIN_REF_NETVIDEOS']['ustream'] = array(
	'regex'     => '#^http://www.ustream.tv/recorded/([0-9]+)#i',
	'type'      => 'flash',
	'src'       => 'http://www.ustream.tv/flash/video/$1',
	'width'     => 480,
	'height'    => 296,
	'attribute' => 'flashvars="loc=%2F&autoplay=false&vid=$1&locale=" allowfullscreen="true" allowscriptaccess="always"'
);


//$const['PLUGIN_REF_NETVIDEOS']['vimeo'] = array(
//	'regex'     => '#^http://vimeo\.com/([0-9]+)#i',
//	'type'      => 'flash',
//	'src'       => 'http://vimeo.com/moogaloop.swf?clip_id=$1&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1',
//	'width'     => 480,
//	'height'    => 270,
//	'attribute' => 'allowFullScreen="true" allowScriptAccess="always"'
//);

$const['PLUGIN_REF_NETVIDEOS']['vimeo'] = array(
	'regex'     => '#^http://vimeo\.com/([0-9]+)#i',
	'type'      => 'html',
	'src'       => '<iframe src="http://player.vimeo.com/video/$1?title=0&amp;byline=0&amp;portrait=0&amp;color=c9ff23"$size " frameborder="0"><noiframe>$link</noiframe></iframe>',
	'src_keitai'=> '$link',
	'width'     => 480,
	'height'    => 270,
);

$const['PLUGIN_REF_NETVIDEOS']['veoh'] = array(
	'regex'     => '#^http://www\.veoh\.com/(?:[a-z\/]+/)?watch/([a-z0-9]+)#i',
	'type'      => 'flash',
	'src'       => 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.5.2.1030&permalinkId=$1&player=videodetailsembedded&videoAutoPlay=0&id=anonymous',
	'width'     => 480,
	'height'    => 296,
	'attribute' => 'allowFullScreen="true" allowScriptAccess="always"'
);

$const['PLUGIN_REF_NETVIDEOS']['pandora'] = array(
	'regex'     => '#^http://channel\.pandora\.tv/channel/video\.ptv\?.+?(userid=[0-9a-z]+&prgid=[0-9]+)#i',
	'type'      => 'flash',
	'src'       => 'http://flvr.pandora.tv/flv2pan/flvmovie.dll/$1&countryChk=jp&skin=1',
	'width'     => 480,
	'height'    => 300,
	'attribute' => 'allowFullScreen="true" allowScriptAccess="always"'
);

$const['PLUGIN_REF_NETVIDEOS']['slideboom'] = array(
	'regex'     => '#^http://www\.slideboom\.com/presentations/(\d+)/#i',
	'type'      => 'flash',
	'src'       => 'http://www.slideboom.com/player/player.swf?id_resource=$1',
	'width'     => 480,
	'height'    => 417,
	'attribute' => 'allowFullScreen="true" allowScriptAccess="always" quality="high" bgcolor="#ffffff"'
);

$const['PLUGIN_REF_NETVIDEOS']['slidesix'] = array(
	'regex'     => '#^http://slidesix.com/view/([a-z0-9-_]+)#i',
	'type'      => 'flash',
	'src'       => 'http://slidesix.com/viewer/SlideSixViewer.swf?alias=$1',
	'width'     => 480,
	'height'    => 380,
	'attribute' => 'allowFullScreen="true" allowScriptAccess="always" wmode="transparent" quality="best"'
);

// image, video, audio の添付ファイルオープン時にリファラをチェックする
// 0:チェックしない, 1:未定義は許可, 2:未定義も不許可
// 未設定 = URL直打ち, ノートンなどでリファラを遮断 など。
$const['OPEN_MEDIA_REFCHECK'] = 1;

// ref でのファイル参照にShortUrl を使用する
// .htaccess での設定が必要
//   RewriteEngine on
//   RewriteRule ^ref/([^/]+)/([^/]+)$ gate.php?way=ref&_nodos&_noumb&page=$1&src=$2 [L]
$const['PLUGIN_REF_SHORTURL'] = 0;

// ref でのファイルダウンロードリンクにShortUrl を使用する
// .htaccess での設定が必要
//   RewriteEngine on
//   RewriteRule ^ref([01])/([^/]+)/([^/]+)$ gate.php?way=attach&_noumb&ni=$1&refer=$2&openfile=$3 [L]
$const['PLUGIN_ATTACH_SHORTURL'] = 0;


// PKWK_QUERY_STRING_MAX
//   Max length of GET method, prohibits some worm attack ASAP
//   NOTE: Keep (page-name + attach-file-name) <= PKWK_QUERY_STRING_MAX
$const['PKWK_QUERY_STRING_MAX'] = 0; // Bytes, 0 = OFF

// ref, attach ブラウザキャッシュ (秒)
$const['BROWSER_CACHE_MAX_AGE'] = 864000; // 10 days

/////////////////////////////////////////////////
// Experimental features

// Multiline plugin hack (See BugTrack2/84)
// EXAMPLE(with a known BUG):
//   #plugin(args1,args2,...,argsN){{
//   argsN+1
//   argsN+1
//   #memo(foo)
//   argsN+1
//   }}
//   #memo(This makes '#memo(foo)' to this)
$const['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK'] = 0; // 1 = Disabled

// 整形済みマルチラインプラグイン
// Multiline PRE plugins
$root->multiline_pre_plugins = array('pre', 'code');

/////////////////////////////////////////////////
// Description Auto Discovery

// ignore inline plugin (split with ',')
$root->description_discovery_ignores_inline = 'calendar2,subnote,edit,tag';

// ignore block plugin (split with ',')
$root->description_discovery_ignores_block = 'back,calendar,calendar2,calendar9,chat,code,footnotes,navi,tag';

// description length
// for cache
$root->description_max_length_save = 500;
// for RSS
$root->description_max_length_rss = 250;
// for <head> meta
$root->description_max_length_meta = 120;

/////////////////////////////////////////////////
// UI LANG Auto Discovery

// Accept Lang
$const['ACCEPT_LANG_REGEX'] = '/(?:^|\W)([a-z]{2}(?:-[a-z]+)?)/i';

// GET QUERY's key of set lang.
$const['SETLANG'] = $this->get_setlang('setlang');

// COOKIE's key of set lang.
$const['SETLANG_C'] = $this->get_setlang_c('');

/////////////////////////////////////////////////
// Directory settings I (ended with '/', permission '777')

// You may hide these directories (from web browsers)
// by setting $const['DATA_HOME'] at index.php.

$const['DATA_DIR']         = $const['DATA_HOME'] . 'private/wiki/';       // Latest wiki texts
$const['DIFF_DIR']         = $const['DATA_HOME'] . 'private/diff/';       // Latest diffs
$const['BACKUP_DIR']       = $const['DATA_HOME'] . 'private/backup/';     // Backups
$const['CACHE_DIR']        = $const['DATA_HOME'] . 'private/cache/';      // Some sort of caches
$const['UPLOAD_DIR']       = $const['DATA_HOME'] . 'attach/';             // Attached files and logs
$const['COUNTER_DIR']      = $const['DATA_HOME'] . 'private/counter/';    // Counter plugin's counts
$const['TRACKBACK_DIR']    = $const['DATA_HOME'] . 'private/trackback/';  // TrackBack logs
$const['PLUGIN_DIR']       = $const['DATA_HOME'] . 'private/plugin/';     // Plugin directory
$const['RENDER_CACHE_DIR'] = $const['DATA_HOME'] . 'private/cache/';      // Rander caches

/////////////////////////////////////////////////
// Directory settings II (ended with '/')

// tDiary theme directory
$const['TDIARY_DIR'] = 'skin/tdiary_theme/';

// Static image files
$const['IMAGE_DIR'] = $const['HOME_URL'].'image/';
// Keep this directory shown via web browsers like
// Image pack name ( ex. 'extra' is $const['IMAGE_DIR'] become "$const['IMAGE_DIR']extra/" )
$root->image_pack_name = '';

// loader.php URL
$const['LOADER_URL'] = $const['HOME_URL'].'skin/loader.php';

/////////////////////////////////////////////////
// Local time setting

//$const['ZONETIME'] = 9 * 3600; // JST = GMT + 9
$const['ZONETIME'] = $this->get_zonetime();
//$const['ZONE'] = 'JST';
$const['ZONE'] = $this->get_zone_by_time($const['ZONETIME'] / 3600);

/////////////////////////////////////////////////
// Title of your Wikisite (Name this)
// Also used as RSS feed's channel name etc
$root->module_title = $root->module['title'] ;

// Specifies title formatting rule. (Regex)
// The first pattern match part is used.
$root->title_setting_string = 'TITLE:';
$root->title_setting_regex = '/^TITLE:(.*)(\r\n|\r|\n)?$/m';

// Specify PukiWiki URL (default: auto)
//$root->script = 'http://example.com/pukiwiki/';

// Shorten $root->script: Cut its file name (default: not cut)
//$root->script_directory_index = 'index.php';

// Default page name
$root->whatsnew     = 'RecentChanges'; // Modified page list
$root->whatsdeleted = 'RecentDeleted'; // Removeed page list
$root->interwiki    = 'InterWikiName'; // Set InterWiki definition here
$root->aliaspage    = 'AutoAliasName'; // Set AutoAlias definition here
$root->menubar      = 'MenuBar';       // Menu
$root->render_attach= ':RenderAttaches';
$root->notepage     = ':Note';

$const['PLUGIN_RENAME_LOGPAGE'] = ':RenameLog'; // Rename Log page

// InterWiki scheme regex (delimiter is '/')
$root->interwikinameRegex = '(?:(?:https?|ftp|news):\/\/|\.\.?\/|skype:)';

// ファイル添付用として表示するページ(複数は # で区切る)
// Page displayed for file uploading. ( The plural is delimited by "#" )
$root->pages_for_attach = '';

// Guest user's name (It will be overwrite by xoops setting.)
$root->anonymous = 'anonymous';

// ページポップアップ CSS
$root->page_popup_position = array(
	// Array values are value of the CSS.
	'top'    => '',
	'bottom' => '',
	'left'   => '',
	'right'  => '',
	'width'  => '',
	'height' => ''
);

// Noteポップアップ CSS
$root->note_popup_position = array(
	// Array values are value of the CSS.
	'top'    => '0px',
	'bottom' => '',
	'left'   => '0px',
	'right'  => '',
	'width'  => '40%',
	'height' => '300px'
);

// root_image_manager window size
$root->root_image_manager_width  = 400;
$root->root_image_manager_height = 430;

// str_rules extensions (join with ',')
// Auto load "bbcode_image" when "root->use_root_image_manager = 1"
$root->rules_extentions = '';

/////////////////////////////////////////////////
// Always output "nofollow,noindex" attribute
$root->nofollow = 0; // 1 = Try hiding from search engines

/////////////////////////////////////////////////
// PKWK_ALLOW_JAVASCRIPT - Allow / Prohibit using JavaScript
$const['PKWK_ALLOW_JAVASCRIPT'] = 1;

/////////////////////////////////////////////////
// TrackBack feature

// Enable Trackback
$root->trackback = 0;

// Show trackbacks with an another window (using JavaScript)
$root->trackback_javascript = 0;

/////////////////////////////////////////////////
// Disable slashes comment out
$root->no_slashes_commentout = 0;

/////////////////////////////////////////////////
// PATH_INFO 使用時 (static_url = 2 or 3) のファイル名
// "index" 以外にする場合は、.htaccess の書き換えと次の内容のファイルを置く
/* 「スクリプト名」で保存する
<?php
include 'index.php';
 */
$root->path_info_script = 'index';

/////////////////////////////////////////////////
// URLエンコードされていないGETクエリを受け入れる
// URL encoding is not GET queries to accept
$root->accept_not_encoded_query = 0;

/////////////////////////////////////////////////
// favicon auto set class name
$root->favicon_set_classname = 'ext';

// favicon auto replace class name
$root->favicon_replace_classname = 'extWithFavicon';

/////////////////////////////////////////////////
// AutoLink feature

// Matches only words
// 英数字は単語単位でマッチさせる
$root->autolink_as_word = 1;

// An upper layer hierarchical name is priority when assuming that it is possible to omit it.
// 上層階層名は省略可能とした場合の優先度
$root->autolink_omissible_upper_priority = 60; // 優先度(通常のAutolink=50)

/////////////////////////////////////////////////
// External AutoLink
// AutoLink to external site's page.

//// Auto link for hypweb's xpwiki/keyword/[ANY]
//$root->ext_autolinks[] = array(
//	'target'  => '' , 				// Target pages split with '&' (prefix search)
//	'priority'=> 40 ,				// Priority (Intenal AutoLink = 50)
//	'url'     => 'http://xoops.hypweb.net/modules/xpwiki/' , // '' means own wiki, 'DirctoryName' for other xpWiki in this site.
//	'urldat'  => 0 ,				// url is autolink's data.(0:No, 1:Yes)
//	'case_i'  => 1 ,				// Case insensitive
//	'base'    => 'keyword' ,		// base directory ('' means all pages)
//	'len'     => 3 ,				// minimum length of link text
//	'enc'     => 'EUC-JP' ,			// character encoding
//	'cache'   => 180 ,				// cache minutes (minimum: 10min)
//	'title'   => 'hypweb:[KEY]' ,	// title attr ([KEY] replaced a target word)
//	'pat'     => '' ,				// Link pattern. (can use [URL_ENCODE], [WIKI_ENCODE], [EWORDS_ENCODE])
//	'a_target'=> '' ,				// <A> attribute 'target'.
//	'a_class' => '' ,				// <A> attribute 'class'.
//);

//// Auto link for kaunet.biz
//$root->ext_autolinks[] = array(
//	'target'  => '' , 				// Target pages split with '&' (prefix search)
//	'priority'=> 40 ,				// Priority (Intenal AutoLink = 50)
//	'url'     => 'http://www.kaunet.biz/dat/autolink.dat' , // '' means own wiki, 'DirctoryName' for other xpWiki in this site.
//	'urldat'  => 1 ,				// url is autolink's data.(0:No, 1:Yes)
//	'case_i'  => 1 ,				// Case insensitive
//	'base'    => '' ,				// base directory ('' means all pages)
//	'len'     => 3 ,				// minimum length of link text
//	'enc'     => 'UTF-8' ,			// character encoding
//	'cache'   => 180 ,				// cache minutes (minimum: 10min)
//	'title'   => 'Kaunet:[KEY]' ,	// title attr ([KEY] replaced a target word)
//	'pat'     => 'http://www.kaunet.biz/[WIKI_ENCODE].html' ,// Link pattern. (can use [URL_ENCODE], [WIKI_ENCODE], [EWORDS_ENCODE])
//	'a_target'=> '' ,				// <A> attribute 'target'.
//	'a_class' => '' ,				// <A> attribute 'class'.
//);

//// Auto link for e-words.jp
//$root->ext_autolinks[] = array(
//	'target'  => '' , 				// Target pages split with '&' (prefix search)
//	'priority'=> 40 ,				// Priority (Intenal AutoLink = 50)
//	'url'     => 'http://xoops.hypweb.net/download/e-words.autolink.dat', // '' means own wiki, 'DirctoryName' for other xpWiki in this site.
//	'urldat'  => 1 ,				// url is autolink's data.
//	'case_i'  => 1 ,				// Case insensitive
//	'base'    => '' ,				// base directory ('' means all pages)
//	'len'     => 3 ,				// minimum length of page name
//	'enc'     => 'UTF-8',			// character encoding
//	'cache'   => 10 ,				// cache minutes (minimum: 10min)
//	'title'   => 'e-Words:[KEY]' ,	// title attr ([KEY] replaced a target word)
//	'pat'     => 'http://e-words.jp/w/[EWORDS_ENCODE].html' ,	// Link pattern. (can use [URL_ENCODE], [WIKI_ENCODE], [EWORDS_ENCODE])
//	'a_target'=> '' ,				// <A> attribute 'target'.
//	'a_class' => '' ,				// <A> attribute 'class'.
//);

/////////////////////////////////////////////////
// Allow to use 'Do not change timestamp' checkbox
// (0:Disable, 1:For everyone,  2:Only for the administrator)
$root->notimeupdate = 1;

/////////////////////////////////////////////////
// User definition
$root->auth_users = array(
	// Username => password
	'foo'	=> 'foo_passwd', // Cleartext
	'bar'	=> '{x-php-md5}f53ae779077e987718cc285b14dfbe86', // PHP md5() 'bar_passwd'
	'hoge'	=> '{SMD5}OzJo/boHwM4q5R+g7LCOx2xGMkFKRVEx',      // LDAP SMD5 'hoge_passwd'
);

/////////////////////////////////////////////////
// Authentication method

$root->auth_method_type	= 'pagename';	// By Page name
//$root->auth_method_type	= 'contents';	// By Page contents

/////////////////////////////////////////////////
// Read auth (0:Disable, 1:Enable)
$root->read_auth = 0;

$root->read_auth_pages = array(
	// Regex		   Username
	'#HogeHoge#'		=> 'hoge',
	'#(NETABARE|NetaBare)#'	=> 'foo,bar,hoge',
);

/////////////////////////////////////////////////
// Edit auth (0:Disable, 1:Enable)
$root->edit_auth = 0;

$root->edit_auth_pages = array(
	// Regex		   Username
	'#BarDiary#'		=> 'bar',
	'#HogeHoge#'		=> 'hoge',
	'#(NETABARE|NetaBare)#'	=> 'foo,bar,hoge',
);

// Q & A 認証 (使用しない = 0, ゲストのみ = 1, 管理者以外 = 2)
$root->riddle_auth = 1;

// Directory path of fckxpwiki (Remove $this->cont['ROOT_PATH'])
$root->fckxpwiki_path = 'common/fckxpwiki';

/////////////////////////////////////////////////
// Users pages separate by '#' or top level is '/'.
// 個別ユーザー専用エリアとする親ページ(#区切り) ["親ページ/ログインID" 以下はそのユーザー専用ページとなる]
// :config/user は自動的に追加されます
$root->users_page = '';


/////////////////////////////////////////////////
// Search auth
// 0: Disabled (Search read-prohibited page contents)
// 1: Enabled  (Search only permitted pages for the user)
$root->search_auth = 0;

/////////////////////////////////////////////////
// $root->whatsnew: Max number of RecentChanges
$root->maxshow = 60;

// $root->whatsdeleted: Max number of RecentDeleted
// (0 = Disabled)
$root->maxshow_deleted = 60;

/////////////////////////////////////////////////
// Page names can't be edit via PukiWiki
$root->cantedit = array( $root->whatsnew );

/////////////////////////////////////////////////
// HTTP: Output Last-Modified header
$root->lastmod = 0;

/////////////////////////////////////////////////
// Date format
$root->date_format = 'Y-m-d';

// Time format
$root->time_format = 'H:i:s';

// no date
$root->no_date = ' - no date - ';

/////////////////////////////////////////////////
// Max number of RSS feed
$root->rss_max = 15;

/////////////////////////////////////////////////
// Backup related settings

// Enable backup
$root->do_backup = 1;

// When a page had been removed, remove its backup too?
$root->del_backup = 0;

// Bacukp interval and generation
$root->cycle  =   3; // Wait N hours between backup (0 = no wait)
$root->maxage = 120; // Stock latest N backups

// NOTE: $cycle x $root->maxage / 24 = Minimum days to lost your data
//          3   x   120   / 24 = 15

// Make backup every time if different user at last time.
$root->backup_everytime_others = 1;

// Splitter of backup data (NOTE: Too dangerous to change)
$const['PKWK_SPLITTER'] = '>>>>>>>>>>';

// Use lightdox function(with JavaScript) for open a image.
$root->ref_use_lightbox = 1;

// Enable easy ref syntax {{...}}
$root->easy_ref_syntax = 1;

// Edit summary format by plugin.
$root->plugin_edit_summary = 'With "$name" plugin.';

/////////////////////////////////////////////////
// Command execution per update

$const['PKWK_UPDATE_EXEC'] = '';

// Sample: Namazu (Search engine)
//$root->target     = '/var/www/wiki/';
//$root->mknmz      = '/usr/bin/mknmz';
//$root->output_dir = '/var/lib/namazu/index/';
//define('PKWK_UPDATE_EXEC',
//	$root->mknmz . ' --media-type=text/pukiwiki' .
//	' -O ' . $root->output_dir . ' -L ja -c -K ' . $root->target);


/////////////////////////////////////////////////
// If this web server can't connect to WWW then set 1;
$root->can_not_connect_www = 0;

/////////////////////////////////////////////////
// HTTP proxy setting (for TrackBack etc)

// Use HTTP proxy server to get remote data
$root->use_proxy = 0;

$root->proxy_host = 'proxy.example.com';
$root->proxy_port = 8080;

// Do Basic authentication
$root->need_proxy_auth = 0;
$root->proxy_auth_user = 'username';
$root->proxy_auth_pass = 'password';

// Hosts that proxy server will not be needed
$root->no_proxy = array(
	'localhost',	// localhost
	'127.0.0.0/8',	// loopback
//	'10.0.0.0/8'	// private class A
//	'172.16.0.0/12'	// private class B
//	'192.168.0.0/16'	// private class C
//	'no-proxy.com',
);

////////////////////////////////////////////////
// Show system notification in SKIN
$root->show_system_notification_skin = 0;

//// These settings are not used on XOOPS.
// SMTP server (Windows only. Usually specified at php.ini)
$root->smtp_server = 'localhost';

// Mail recipient (To:) and sender (From:)
$root->notify_to   = 'to@example.com';	// To:
$root->notify_from = 'from@example.com';	// From:
//// The above-mentioned setting is not used on XOOPS.

// Subject: ($root->page = Page name wll be replaced)
$root->notify_subject = '['.$this->root->module['name'].'] $page';

// Mail header
// NOTE: Multiple items must be divided by "\r\n", not "\n".
$root->notify_header = '';

/////////////////////////////////////////////////
// Mail: POP / APOP Before SMTP
// These settings are not used on XOOPS.

// Do POP/APOP authentication before send mail
$root->smtp_auth = 0;

$root->pop_server = 'localhost';
$root->pop_port   = 110;
$root->pop_userid = '';
$root->pop_passwd = '';

// Use APOP instead of POP (If server uses)
//   Default = Auto (Use APOP if possible)
//   1       = Always use APOP
//   0       = Always use POP
// $root->pop_auth_use_apop = 1;

/////////////////////////////////////////////////
// Ignore list

// Regex of ignore pages
$root->non_list = '^\:';
// MySQL expr LIKE of non_list (split by #)
$root->non_list_like = ':%';

// Search ignored pages
$root->search_non_list = 1;

// Show page's filelist only admin.
$root->filelist_only_admin = 1;

/////////////////////////////////////////////////
// Template setting

$root->auto_template_func = 1;
$root->auto_template_rules = array(
	'((.+)\/([^\/]+))'         => array('\2/template', ':template/\2', ':template/\3') ,
	'(.+\/([^\/]+)\/([^\/]+))' => array(':template/\2/default') ,
	'(()([^\/]+))'             => array('template', ':template/default') ,
);

// Setting of footnote categories
// ex. $root->footnote_categories = array('Note' => '($1)', 'Reference' => '[$1]');
$root->footnote_categories = array();

/////////////////////////////////////////////////
// Automatically add fixed heading anchor
$root->fixed_heading_anchor = 1;

/////////////////////////////////////////////////
// Remove the first spaces from Preformatted text
$root->preformat_ltrim = 1;

/////////////////////////////////////////////////
// Use extended table format like a PukiWikiMod
$root->extended_table_format = 1;

// Enable text-align of cell by spaces.
$root->space_cell_align = 1;

// Enable text-align of cell by symbols("<", "=" & ">").
$root->symbol_cell_align = 1;

// Enable join cell with empty cell.
$root->empty_cell_join = 1;

// Enable file scheme with brackets.
$root->use_file_scheme = 0;

/////////////////////////////////////////////////
// Use date-time rules (See rules.ini.php)
$root->usedatetime = 1;

// ページ更新時常にページキャッシュを破棄するページ
$root->always_clear_cache_pages = array (
	//$root->defaultpage,
	$root->menubar,
);

// 上位層のページもキャッシュをクリアする
$root->clear_cache_parent = TRUE; // (TRUE or FASLE)

/////////////////////////////////////////////////
// About CSS...

// Main CSS name
$root->main_css = 'main.css';

// CSS ID prefix ( ex. #xo-canvas )
$root->css_prefix = '';

/////////////////////////////////////////////////
// JavaScript setting

// ie で Dom:loaded を使わず window.loaded を使う
$root->ieDomLoadedDisabled = 0;

// IE6 では、いくつかの重い JavaScript を無効にする
$root->ie6JsPass = 1;

/////////////////////////////////////////////////
// レンダラーモード用設定
// For renderer mode.

// レンダリングキャッシュを有効にする
// Enable render cache.
$root->render_use_cache = 0;

// キャッシュの有効時間(分) 0: Wikiページが新規作成・削除されるまで
// Render cache minutes. 0: Until make or delete a page.
$root->render_cache_min = 0;

// ページリンクをポップアップにする
// All page link uses popup. (1=All, 2=AutoLink only)
$root->render_popuplink = 0;

$root->render_popuplink_position = array(
	// Array values are value of the CSS.
	'top'    => '',
	'bottom' => '',
	'left'   => '',
	'right'  => '',
	'width'  => '',
	'height' => ''
);

// Show the Wiki Helper on the site wide.
$root->render_UseWikihelperAtAll = 0;

/////////////////////////////////////////////////
// For XOOPS System

// Update post count when page updating or page deleting
$root->xoops_post_count_up = 1;
$root->xoops_post_count_down = 1;

/////////////////////////////////////////////////
// User-Agent settings
//
// If you want to ignore embedded browsers for rich-content-wikisite,
// remove (or comment-out) all 'keitai' settings.
//
// If you want to to ignore desktop-PC browsers for simple wikisite,
// copy keitai.ini.php to default.ini.php and customize it.

$root->agents = array(
// pattern: A regular-expression that matches device(browser)'s name and version
// profile: A group of browsers

    // Embedded browsers (Rich-clients for PukiWiki)

	//
	array('pattern'=>'#\b(Mobile)\b#', 'profile'=>'mobile'),

	// Windows CE (Microsoft(R) Internet Explorer 5.5 for Windows(R) CE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.5; Windows CE; sigmarion3)" (sigmarion, Hand-held PC)
	array('pattern'=>'#\b(?:MSIE [5-9]).*\b(Windows CE)\b#', 'profile'=>'default'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM, expects to be "Mozilla/4.0"
	// Sample: "Mozilla/4.0 (PS2; PlayStation BB Navigator 1.0) NetFront/3.0" (PlayStation BB Navigator, for SONY PlayStation 2)
	// Sample: "Mozilla/4.0 (PDA; PalmOS/sony/model crdb/Revision:1.1.19) NetFront/3.0" (SONY Clie series)
	// Sample: "Mozilla/4.0 (PDA; SL-A300/1.0,Embedix/Qtopia/1.1.0) NetFront/3.0" (SHARP Zaurus)
	array('pattern'=>'#^(?:Mozilla/4).*\b(NetFront)/([0-9\.]+)#',	'profile'=>'default'),

    // Embedded browsers (Non-rich)

	array('pattern'=>'#^(Vodafone)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#^(SoftBank)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Windows CE (the others)
	// Sample: "Mozilla/2.0 (compatible; MSIE 3.02; Windows CE; 240x320 )" (GFORT, NTT DoCoMo)
	array('pattern'=>'#\b(Windows CE)\b#', 'profile'=>'keitai'),

	// ACCESS "NetFront" / "Compact NetFront" and thier OEM
	// Sample: "Mozilla/3.0 (AveFront/2.6)" ("SUNTAC OnlineStation", USB-Modem for PlayStation 2)
	// Sample: "Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0" (DDI Pocket: AirH" Phone by JRC)
	array('pattern'=>'#\b(NetFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(CNF)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AveFront)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#\b(AVE-Front)/([0-9\.]+)#',	'profile'=>'keitai'), // The same?

	// NTT-DoCoMo, i-mode (embeded Compact NetFront) and FOMA (embedded NetFront) phones
	// Sample: "DoCoMo/1.0/F501i", "DoCoMo/1.0/N504i/c10/TB/serXXXX" // c以降は可変
	// Sample: "DoCoMo/2.0 MST_v_SH2101V(c100;TB;W22H12;serXXXX;iccxxxx)" // ()の中は可変
	array('pattern'=>'#^(DoCoMo)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Vodafone's embedded browser
	// Sample: "J-PHONE/2.0/J-T03"	// 2.0は"ブラウザの"バージョン
	// Sample: "J-PHONE/4.0/J-SH51/SNxxxx SH/0001a Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.1.0"
	array('pattern'=>'#^(J-PHONE)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Openwave(R) Mobile Browser (EZweb, WAP phone, etc)
	// Sample: "OPWV-SDK/62K UP.Browser/6.2.0.5.136 (GUI) MMP/2.0"
	array('pattern'=>'#\b(UP\.Browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Opera, dressing up as other embedded browsers
	// Sample: "Mozilla/3.0(DDIPOCKET;KYOCERA/AH-K3001V/1.4.1.67.000000/0.1/C100) Opera 7.0" (Like CNF at 'keitai'-mode)
	array('pattern'=>'#\b(DDIPOCKET|WILLCOM)\b#',	'profile'=>'keitai'),

	// Planetweb http://www.planetweb.com/
	// Sample: "Mozilla/3.0 (Planetweb/v1.07 Build 141; SPS JP)" ("EGBROWSER", Web browser for PlayStation 2)
	array('pattern'=>'#\b(Planetweb)/v([0-9\.]+)#', 'profile'=>'keitai'),

	// DreamPassport, Web browser for SEGA DreamCast
	// Sample: "Mozilla/3.0 (DreamPassport/3.0)"
	array('pattern'=>'#\b(DreamPassport)/([0-9\.]+)#',	'profile'=>'keitai'),

	// Palm "Web Pro" http://www.palmone.com/us/support/accessories/webpro/
	// Sample: "Mozilla/4.76 [en] (PalmOS; U; WebPro)"
	array('pattern'=>'#\b(WebPro)\b#',	'profile'=>'keitai'),

	// ilinx "Palmscape" / "Xiino" http://www.ilinx.co.jp/
	// Sample: "Xiino/2.1SJ [ja] (v. 4.1; 153x130; c16/d)"
	array('pattern'=>'#^(Palmscape)/([0-9\.]+)#',	'profile'=>'keitai'),
	array('pattern'=>'#^(Xiino)/([0-9\.]+)#',	'profile'=>'keitai'),

	// SHARP PDA Browser (SHARP Zaurus)
	// Sample: "sharp pda browser/6.1[ja](MI-E1/1.0) "
	array('pattern'=>'#^(sharp [a-z]+ browser)/([0-9\.]+)#',	'profile'=>'keitai'),

	// WebTV
	array('pattern'=>'#^(WebTV)/([0-9\.]+)#',	'profile'=>'keitai'),

    // Desktop-PC browsers

	// Opera (for desktop PC, not embedded) -- See BugTrack/743 for detail
	// NOTE: Keep this pattern above MSIE and Mozilla
	// Sample: "Opera/7.0 (OS; U)" (not disguise)
	// Sample: "Mozilla/4.0 (compatible; MSIE 5.0; OS) Opera 6.0" (disguise)
	array('pattern'=>'#\b(Opera)[/ ]([0-9\.]+)\b#',	'profile'=>'default'),

	// MSIE: Microsoft Internet Explorer (or something disguised as MSIE)
	// Sample: "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)"
	array('pattern'=>'#\b(MSIE) ([0-9\.]+)\b#',	'profile'=>'default'),

	// Mozilla Firefox
	// NOTE: Keep this pattern above Mozilla
	// Sample: "Mozilla/5.0 (Windows; U; Windows NT 5.0; ja-JP; rv:1.7) Gecko/20040803 Firefox/0.9.3"
	array('pattern'=>'#\b(Firefox)/([0-9\.]+)\b#',	'profile'=>'default'),

	// Google Chrome
	// Sample: "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.48 Safari/525.19"
    array('pattern'=>'#\b(Chrome)(?:/([0-9\.]+))?\b#',	'profile'=>'default'),

	// Mac Safari
	// Sample: "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; ja-jp) AppleWebKit/416.11 (KHTML, like Gecko) Safari/416.12"
    array('pattern'=>'#\b(Safari)(?:/([0-9\.]+))?\b#',	'profile'=>'default'),

    // Loose default: Including something Mozilla
	array('pattern'=>'#^([a-zA-z0-9 ]+)/([0-9\.]+)\b#',	'profile'=>'default'),

	array('pattern'=>'#^#',	'profile'=>'default'),	// Sentinel
);

$const['PKWK_DTD_XHTML_1_1'] = 17;
$const['PKWK_DTD_XHTML_1_0'] = 16;
$const['PKWK_DTD_XHTML_1_0_STRICT'] = 16;
$const['PKWK_DTD_XHTML_1_0_TRANSITIONAL'] = 15;
$const['PKWK_DTD_XHTML_1_0_FRAMESET'] = 14;
$const['PKWK_DTD_HTML_4_01'] = 3;
$const['PKWK_DTD_HTML_4_01_STRICT'] = 3;
$const['PKWK_DTD_HTML_4_01_TRANSITIONAL'] = 2;
$const['PKWK_DTD_HTML_4_01_FRAMESET'] = 1;
$const['PKWK_DTD_TYPE_XHTML'] = 1;
$const['PKWK_DTD_TYPE_HTML'] = 0;
$const['PKWK_PLUGIN_CALL_TIME_LIMIT'] = 768;
$const['PKWK_HTTP_REQUEST_URL_REDIRECT_MAX'] = 2;
$const['PKWK_CIDR_NETWORK_REGEX'] = '/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';
$const['PLUGIN_TRACKBACK_VERSION'] = 'PukiWiki/TrackBack 0.3';
$const['PKWK_PASSPHRASE_LIMIT_LENGTH'] = 512;
$const['PKWK_DIFF_SHOW_CONFLICT_DETAIL'] = 1;
$const['PKWK_MAXSHOW_ALLOWANCE'] = 10;
$const['PKWK_MAXSHOW_CACHE'] = 'recent.dat';
$const['PKWK_ENTITIES_REGEX_CACHE'] = 'entities.dat';
$const['PKWK_AUTOLINK_REGEX_CACHE'] = 'autolink.dat';
$const['PKWK_AUTOALIAS_REGEX_CACHE'] = 'autoalias.dat';
$const['BACKUP_EXT'] = (extension_loaded('zlib'))? '.gz' : '.txt';
$const['PKWK_DIFF_SHOW_CONFLICT_DETAIL'] = 1;

// Fixed prefix of configuration-page's name
$const['PKWK_CONFIG_PREFIX'] = ':config/';
$const['PKWK_CONFIG_USER'] = 'user';

// 名前欄の仮文字列(コンバート後にユーザー名に置換)
$const['USER_NAME_REPLACE'] = '__uSER_nAME_rEPLACE__';
$const['USER_CODE_REPLACE'] = '__uSER_cODE_rEPLACE__';

// #pginfo の正規表現 (#pginfo削除などに利用)
$const['PKWK_PGINFO_REGEX'] = '/^(?:#pginfo\(.*\)[\r\n]*)+/m';

//////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////
// The following settings are overwrited when the environment of the management screen is set,
// and go in the setting change by an environmental setting, please.
// 以下の設定は、管理画面の環境設定をした場合に上書きされますので、設定変更は環境設定で行ってください。
$const['PKWK_READONLY'] = 0; // 0 or 1
$root->function_freeze = 1;
$root->adminpass = '{x-php-md5}!';
$root->html_head_title = '$content_title [$page_title] - $module_title';
$root->modifier = 'anonymous';
$root->modifierlink = 'http://pukiwiki.example.com/';
$root->notify = 0;
$root->notify_diff_only = 1;
$root->defaultpage  = 'FrontPage';
$root->page_case_insensitive = 0;
$const['SKIN_NAME'] = 'default';
$root->skin_navigator_cmds = 'all';
$root->skin_navigator_disabled = '';
$const['SKIN_CHANGER'] = 1;
$root->referer = 0;
$root->allow_pagecomment = 1;
$root->use_root_image_manager = 0;
$root->use_title_make_search = 0;
$root->nowikiname = 0;
$root->relative_path_bracketname = 'remove'; //'remove', 'full', 'as is'
$root->pagename_num2str = 1;
$root->pagelink_topicpath = 0;
$root->static_url = 0; // 0 or 1, 2, 3
$root->url_encode_utf8 = 0;
$root->link_target = '';
$root->class_extlink = 'ext';
$root->nofollow_extlink = 0;
$root->autolink = 0;
$root->autolink_omissible_upper = 0; // Bytes(need $root->autolink = ON), 0 = OFF
$root->autoalias = 0;
$root->autoalias_max_words = 50;
$root->plugin_follow_editauth = 0;
$root->plugin_follow_freeze = 1;
$root->line_break = 0;
$root->fixed_heading_anchor_edit = 1;
$root->paraedit_partarea = 'compat';
$root->contents_auto_insertion = 4;
$root->pagecache_min = 0;
$root->pre_width = 'auto';
$root->pre_width_ie = '700px';
$root->fckeditor_path = 'common/fckeditor_2.6';
$root->use_moblog_user_pref = 0;
$root->moblog_pop_mail = '';
$root->moblog_pop_host = '';
$root->moblog_pop_port = 110;
$root->moblog_pop_user = '';
$root->moblog_pop_pass = '';
$root->moblog_page_recomend = '';
$root->use_xmlrpc = 0;
$root->xmlrpc_endpoint = '?cmd=xmlrpc';
$root->update_ping = 0;
$root->update_ping_servers = '
http://api.my.yahoo.co.jp/RPC2
http://blog.goo.ne.jp/XMLRPC
http://blogsearch.google.co.jp/ping/RPC2 E
http://feeds.feedburner.com/ArakiNotes E
http://ping.bloggers.jp/rpc/
http://r.hatena.ne.jp/rpc
http://rpc.technorati.com/rpc/ping E
http://rpc.weblogs.com/RPC2 E
http://www.blogpeople.net/servlet/weblogUpdates E
';
$root->pagereading_enable = 0;
$root->pagereading_kanji2kana_converter = 'none';
$root->pagereading_kanji2kana_encoding = 'EUC'; // Default for Unix
$root->pagereading_chasen_path = '/usr/local/bin/chasen';
$root->pagereading_kakasi_path = '/usr/local/bin/kakasi';
$root->pagereading_config_page = ':config/PageReading';
$root->pagereading_config_dict = ':config/PageReading/dict';
$root->amazon_AssociateTag = '';
$root->amazon_AccessKeyId  = '';
$root->amazon_SecretAccessKey = '';
$root->amazon_UseUserPref = 0;
$root->bitly_login = '';
$root->bitly_apiKey = '';
$root->bitly_domain_internal = '';
$root->bitly_domain_external = '';
$root->bitly_clickable = 0;
$root->twitter_consumer_key = '';
$root->twitter_consumer_secret = '';
$root->yahoo_application_id = '';
$root->yahoo_app_upgrade_id = '';

$root->pginfo = array(
	'uid'       => 0,     // UserID
	'ucd'       => '',    // UserCode(by cookie)
	'uname'     => '',    // UserName(by cookie)
	'einherit'  => 3,     // Edit Inherit
	'eaids'     => 'all', // Editable users
	'egids'     => 'all', // Editable groups
	'vinherit'  => 3,     // View Inherit
	'vaids'     => 'all', // Viewable users
	'vgids'     => 'all', // Viewable groups
	'lastuid'   => 0,     // Last editer's uid
	'lastucd'   => '',    // Last editer's ucd(by cookie)
	'lastuname' => '',    // Last editer's name(by cookie)
);
?>