<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Forum");

// A brief description of this module
define($constpref."_DESC","Forum module for XOOPS");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","Topics");
define($constpref."_BDESC_LIST_TOPICS","This block can be used for multi-purpose. Of course, you can put it multiplly.");
define($constpref."_BNAME_LIST_POSTS","Posts");
define($constpref."_BNAME_LIST_FORUMS","Forums");

// admin menu
define($constpref.'_ADMENU_CATEGORYACCESS','Permissions of Categories');
define($constpref.'_ADMENU_FORUMACCESS','Permissions of Forums');
define($constpref.'_ADMENU_ADVANCEDADMIN','Advanced');
define($constpref.'_ADMENU_POSTHISTORIES','Histories');
define($constpref.'_ADMENU_MYLANGADMIN','Languages');
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define($constpref.'_ADMENU_MYPREFERENCES','Preferences');

// configurations
define($constpref.'_TOP_MESSAGE','Message in forum top');
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Forum Top</h1><p class="d3f_welcome">To start viewing messages, select the category and forum that you want to visit from the selection below.</p>');
define($constpref.'_SHOW_BREADCRUMBS','Display breadcrumbs');
define($constpref.'_DEFAULT_OPTIONS','Default checked in post form');
define($constpref.'_DEFAULT_OPTIONSDSC','List checked options separated by comma(,).<br />eg) smiley,xcode,br,number_entity<br />You can add these options: special_entity html attachsig u2t_marked');
define($constpref."_USENAME","display name");
define($constpref."_USENAMEDESC","which name to use display name 'uname'(user ID) or 'name'(Real name). <br /> xoops default is 'uname'(user ID)");
define($constpref."_USENAME_UNAME","use'uname'(user ID)");
define($constpref."_USENAME_NAME","use'name'(Real name)");
define($constpref.'_ALLOW_HTML','Allow HTML');
define($constpref.'_ALLOW_HTMLDSC','Don\'t turn this on casually. It cause Script Insertion vulnerability if malicious user can post.');
define($constpref.'_ALLOW_TEXTIMG','Allow to dipslay external images in the post');
define($constpref.'_ALLOW_TEXTIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define($constpref.'_ALLOW_SIG','Allow Signature');
define($constpref.'_ALLOW_SIGDSC','');
define($constpref.'_ALLOW_SIGIMG','Allow to display external images in the signature');
define($constpref.'_ALLOW_SIGIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define($constpref.'_USE_VOTE','use the feature of VOTE');
define($constpref.'_USE_SOLVED','use the feature of SOLVED');
define($constpref.'_ALLOW_MARK','use the feature of MARKING');
define($constpref.'_ALLOW_HIDEUID','Allow a registered user can post without his/her name');
define($constpref.'_POSTS_PER_TOPIC','Max posts in a topic');
define($constpref.'_POSTS_PER_TOPICDSC','A topic having this number of posts will be locked automatically.');
define($constpref.'_HOT_THRESHOLD','Hot Topic Threshold');
define($constpref.'_HOT_THRESHOLDDSC','');
define($constpref.'_TOPICS_PER_PAGE','Topics per a page in the view of a forum');
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','Topics per a page in the view crossing forums');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','Time limit for users edit (sec)');
define($constpref.'_SELFEDITLIMITDSC','To allow normal users can edit his/her posts, set plus value as seconds. To disallow normal users can edit it, set 0.');
define($constpref.'_SELFDELLIMIT','Time limit for users delete (sec)');
define($constpref.'_SELFDELLIMITDSC','To allow normal users can delete his/her posts, set plus value as seconds. To disallow normal users can delete it, set 0. Anyway any parent posts cannot be removed.');
define($constpref.'_CSS_URI','URI of CSS file for this module');
define($constpref.'_CSS_URIDSC','relative or absolute path can be set. default: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','Directory for image files');
define($constpref.'_IMAGES_DIRDSC','relative path should be set in the module directory. default: images');
define($constpref.'_BODY_EDITOR','Body Editor');
define($constpref.'_BODY_EDITORDSC','WYSIWYG editor will be enabled under only forums allowing HTML. With forums escaping HTML specialchars, xoopsdhtml will be displayed automatically.');
define($constpref.'_ANONYMOUS_NAME','Anonymous Name');
define($constpref.'_ANONYMOUS_NAMEDSC','');
define($constpref.'_ICON_MEANINGS','Meanings of icons');
define($constpref.'_ICON_MEANINGSDSC','Specify ALTs of icons. each alts should be separated by pipe(|). The first alt corresponds "posticon0.gif".');
define($constpref.'_ICON_MEANINGSDEF','none|normal|unhappy|happy|lower it|raise it|report|question');
define($constpref.'_GUESTVOTE_IVL','Vote from guests');
define($constpref.'_GUESTVOTE_IVLDSC','Set this 0, to disable voting from guest. The other this number means time(sec.) to allow second post from the same IP.');
define($constpref.'_ANTISPAM_GROUPS','Groups should be checked anti-SPAM');
define($constpref.'_ANTISPAM_GROUPSDSC','Usually set all blank.');
define($constpref.'_ANTISPAM_CLASS','Class name of anti-SPAM');
define($constpref.'_ANTISPAM_CLASSDSC','Default value is "default". If you disable anti-SPAM against guests even, set it blank');


// Notify Categories
define($constpref.'_NOTCAT_TOPIC', 'This topic'); 
define($constpref.'_NOTCAT_TOPICDSC', 'Notifications about the targetted topic');
define($constpref.'_NOTCAT_FORUM', 'This forum'); 
define($constpref.'_NOTCAT_FORUMDSC', 'Notifications about the targetted forum');
define($constpref.'_NOTCAT_CAT', 'This category');
define($constpref.'_NOTCAT_CATDSC', 'Notifications about the targetted category');
define($constpref.'_NOTCAT_GLOBAL', 'All categories');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notifications about all categories');

// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'New post in the topic');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Notify me of new posts in the current topic.');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} New post in topic {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWPOST', 'New post in the forum');
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Notify me of new posts in the current forum.');
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New post in forum {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'New topic in the forum');
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Notify me of new topics in the current forum.');
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New topic in forum {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWPOST', 'New post in the category');
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Notify me of new posts in the current category.');
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New post in category {POST_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'New topic in the category');
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Notify me of new topics in the current category.');
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New topic in category {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWFORUM', 'New forum in the category');
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Notify me of new forums in the current category.');
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New forum in category');

define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'New post into any category');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Notify me of new posts into any category.');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: New post {POST_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'New topic into any category');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Notify me of new topics into any category.');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: New topic {TOPIC_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'New forum into any category');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Notify me of new forums into any category.');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: New forum');

define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'New Post (Full Text)');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Notify me of any new posts (include full text in message).');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
define($constpref.'_NOTIFY_GLOBAL_WAITING', 'New waiting');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Notify me of new posts waiting approval. For admins only');
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: New waiting {POST_TITLE}');

}

?>