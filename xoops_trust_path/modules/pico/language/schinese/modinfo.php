<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {












// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 18:29:25
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-02 16:22:08
define($constpref.'_AUTOREGISTCLASS','class name to register/unregister HTML wrapped files');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-19 04:29:55
define($constpref.'_ADMENU_TAGS','Tags');

// Appended by Xoops Language Checker -GIJOE- in 2008-10-01 12:11:22
define($constpref.'_URIM_CLASS','class mapping URI');
define($constpref.'_URIM_CLASSDSC','Change it if you want to override the URI mapper. The default value is PicoUriMapper');

// Appended by Xoops Language Checker -GIJOE- in 2008-09-07 05:14:31
define($constpref.'_EF_CLASS','class for extra_fields');
define($constpref.'_EF_CLASSDSC','Change it if you want to override the handler for extra_fields. default value is PicoExtraFields');
define($constpref.'_EFIMAGES_DIR','directory for extra_fields');
define($constpref.'_EFIMAGES_DIRDSC','set relative path from XOOPS_ROOT_PATH. Create and chmod 777 the directory first. default) uploads/(module dirname)');
define($constpref.'_EFIMAGES_SIZE','pixels for extra images');
define($constpref.'_EFIMAGES_SIZEDSC','(main_width)x(main_height) (small_width)x(small_height) default) 480x480 150x150');
define($constpref.'_IMAGICK_PATH','Path for ImageMagick binaries');
define($constpref.'_IMAGICK_PATHDSC','Leave blank normal, or set it like /usr/X11R6/bin/');
define($constpref.'_NOTCAT_CATEGORY','category');
define($constpref.'_NOTCAT_CATEGORYDSC','notifications under this category');
define($constpref.'_NOTCAT_CONTENT','content');
define($constpref.'_NOTCAT_CONTENTDSC','notifications about this content');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENT','new content');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP','Notify if a new content is registered. (approved contents only)');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} a new content {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CONTENT_COMMENT','new comment');
define($constpref.'_NOTIFY_CONTENT_COMMENTCAP','Notify if a new comment is posted. (approved comments only)');
define($constpref.'_NOTIFY_CONTENT_COMMENTSBJ','[{X_SITENAME}] {X_MODULE} : a new comment');

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:12
define($constpref.'_ALLOWEACHHEAD','specify HTML headers for each contents');
define($constpref.'_BNAME_TAGS','Tags');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-22 03:55:47
define($constpref.'_ADMENU_EXTRAS','Extra');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-18 10:36:05
define($constpref.'_HTMLPR_EXCEPT','Groups can avoid purification by HTMLPurifier');
define($constpref.'_HTMLPR_EXCEPTDSC','Post from users who are not belonged these groups will be forced to purified as sanitized HTML by HTMLPurifier in Protector>=3.14. This purification cannot work with PHP4');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-12 17:00:59
define($constpref.'_BNAME_MYWAITINGS','My waiting posts');

// Appended by Xoops Language Checker -GIJOE- in 2007-06-15 05:03:02
define($constpref.'_BNAME_SUBCATEGORIES','Subcategories');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT','new content');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP','Notify if a new content is registered. (approved contents only)');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE} : New content');

// Appended by Xoops Language Checker -GIJOE- in 2007-05-29 16:39:07
define($constpref.'_COM_VIEW','View of Comment-integration');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","a module for staic contents");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , '文章管理' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , '类别管理' ) ;
define( $constpref.'_ADMENU_IMPORT' , '导入/同步' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN' , '语言管理' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , '模板管理' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , '区块/权限管理' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , '参数设置' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','启用嵌入模式');
define($constpref.'_USE_REWRITE','启用mod_rewrite模式');
define($constpref.'_USE_REWRITEDSC','依赖于您的环境。如果您启用此项，请将XOOPS_ROOT_PATH/modules/(dirname)/.htaccess.rewrite_wraps (with wraps) 或 .htaccess.rewrite_normal (without wraps) 改名为 .htaccess');
define($constpref.'_WRAPSAUTOREGIST','启用自动寄存嵌入数据库的文件的HTML作为文章');
define($constpref.'_TOP_MESSAGE','模块首页描述');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','在此模块的首页显示菜单 (索引)');
define($constpref.'_LISTASINDEX',"在类别首页显示文章索引");
define($constpref.'_LISTASINDEXDSC','“是”表示在类别首页显示文章列表。“否”则表示显示为最新的文章');
define($constpref.'_SHOW_BREADCRUMBS','显示位置导航');
define($constpref.'_SHOW_PAGENAVI','显示文章的前后链接');
define($constpref.'_SHOW_PRINTICON','显示“打印”图标');
define($constpref.'_SHOW_TELLAFRIEND','显示“转告朋友”图标');
define($constpref.'_SEARCHBYUID','记录到作者资料');
define($constpref.'_SEARCHBYUIDDSC','文章将出现于其作者的用户资料中。如果您作为静态内容使用此模块，请关闭此项。');
define($constpref.'_USE_TAFMODULE','使用“Tellafriend”模块');
define($constpref.'_FILTERS','默认过滤器设定');
define($constpref.'_FILTERSDSC','输入过滤器名，并以竖线 (|) 分隔');
define($constpref.'_FILTERSDEFAULT','htmlspecialchars|xcode|smiley|nl2br');
define($constpref.'_FILTERSF','强制的过滤器');
define($constpref.'_FILTERSFDSC','输入过滤器名，并以逗号 (,) 分隔。过滤器：LAST指过滤器是在最后阶段通过的，其它的过滤器是在首阶段通过的。');
define($constpref.'_FILTERSP','禁止的过滤器');
define($constpref.'_FILTERSPDSC','输入过滤器名，并以逗号 (,) 分隔。');
define($constpref.'_SUBMENU_SC','在菜单中显示文章');
define($constpref.'_SUBMENU_SCDSC','默认为仅显示类别名称。如果您启用此项，则标记有“菜单”的文章标题也将显示。');
define($constpref.'_SITEMAP_SC','显示文章于网站地图模块');
define($constpref.'_USE_VOTE','启用投票功能');
define($constpref.'_GUESTVOTE_IVL','来自于访客的投票');
define($constpref.'_GUESTVOTE_IVLDSC','设为0，禁止访客投票。其它数字指允许来自相同IP地址的再次投票间隔时间 (秒)。');
define($constpref.'_HTMLHEADER','通用HTML头部');
define($constpref.'_CSS_URI','模块CSS文件的URI');
define($constpref.'_CSS_URIDSC','可以设定相对或绝对路径。默认值：{mod_url}/index.css');
define($constpref.'_IMAGES_DIR','图像文件目录');
define($constpref.'_IMAGES_DIRDSC','相对路径应设置为模块目录中。默认值：images');
define($constpref.'_BODY_EDITOR','正文编辑器');
define($constpref.'_HISTORY_P_C','存储于数据库的修订版本数');
define($constpref.'_MLT_HISTORY','各修订本的最小有效时间 (秒)');
define($constpref.'_BRCACHE','图像文件的缓存有效时间 (仅限于嵌入模式)');
define($constpref.'_BRCACHEDSC','在此时间中HTML以外的文件将被WEB浏览器缓存 (0为禁止)');
define($constpref.'_COM_DIRNAME','评论-集成：d3forum目录名');
define($constpref.'_COM_FORUM_ID','评论-集成：forum ID');

// blocks
define($constpref.'_BNAME_MENU','菜单');
define($constpref.'_BNAME_CONTENT','文章');
define($constpref.'_BNAME_LIST','列表');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', '全局');
define($constpref.'_NOTCAT_GLOBALDSC', '关于此模块的通知');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', '等待');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', '有新文章发布或修正等待审核时通知 (仅通知超级管理员或管理员)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}：等待');

}


?>
