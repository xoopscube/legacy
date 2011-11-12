<?php
if( ! defined( 'XP2_MODINFO_LANG_INCLUDED' ) ) {
	define( 'XP2_MODINFO_LANG_INCLUDED' , 1 ) ;

	// The name of this module admin menu
	define("_MI_XP2_MENU_SYS_INFO","System information");
	define("_MI_XP2_MENU_BLOCK_ADMIN","blocks/permissions");
	define("_MI_XP2_MENU_BLOCK_CHECK","blocks check");
	define("_MI_XP2_MENU_WP_ADMIN","WordPress Admin");
	define("_MI_XP2_MOD_ADMIN","Module Admin");

	// The name of this module
	define("_MI_XP2_NAME","blog");

	// A brief description of this module
	define("_MI_XP2_DESC","WordPress For XOOPS Community");

	// Sub menu titles
	define("_MI_XP2_MENU_POST_NEW","New Post");
	define("_MI_XP2_MENU_EDIT","Edit Post");
	define("_MI_XP2_MENU_ADMIN","WordPress Admin");
	define("_MI_XP2_MENU_XPRESS","XPressME Setting");
	define("_MI_XP2_MENU_TO_MODULE","to Modules");
	define("_MI_XP2_TO_UPDATE","Update");

	// Module Config
	define("_MI_LIBXML_PATCH","Force a patch for the libxml2 bug in a block");
	define("_MI_LIBXML_PATCH_DESC","libxml2 Ver 2.70-2.72 have the bug that '<' and '>' are removed. 
XPressME acquires a version of libxml2 automatically, and it is adapted a patch if it is necessary. 
When XPressME cannot acquire a version of libxml2, you can let a patch fit it with this option forcibly.");
	
	define("_MI_MEMORY_LIMIT","Memory size(MB) at least necessary for module");
	define("_MI_MEMORY_LIMIT_DESC","If the memory_limit value of php.ini is smaller than this value. Try the re-setting of memory_limit with ini_set('memory_limit', Value);.");

	// Block Name
	define("_MI_XP2_BLOCK_COMMENTS","Recent Comments");
	define("_MI_XP2_BLOCK_CONTENT","Recent Posts with content");
	define("_MI_XP2_BLOCK_POSTS","Recent Post Title");
	define("_MI_XP2_BLOCK_CALENDER","Calendar");
	define("_MI_XP2_BLOCK_POPULAR","Popular post list");
	define("_MI_XP2_BLOCK_ARCHIVE","Archive");
	define("_MI_XP2_BLOCK_AUTHORS","Author List");
	define("_MI_XP2_BLOCK_PAGE","Page");
	define("_MI_XP2_BLOCK_SEARCH","Search");
	define("_MI_XP2_BLOCK_TAG","Tag Cloud");
	define("_MI_XP2_BLOCK_CATEGORY","Category");
	define("_MI_XP2_BLOCK_META","Meta");
	define("_MI_XP2_BLOCK_SIDEBAR","Sidebar Navigation");
	define("_MI_XP2_BLOCK_WIDGET","Widget");
	define("_MI_XP2_BLOCK_ENHANCED","Enhanced");
	define("_MI_XP2_BLOCK_BLOG_LIST","Blogs List");
	define("_MI_XP2_BLOCK_GLOBAL_POSTS","Recent Posts(All blogs)");
	define("_MI_XP2_BLOCK_GLOBAL_COMM","Recent Comments(All blogs)");
	define("_MI_XP2_BLOCK_GLOBAL_POPU","Popular post(All blogs)");

	// Notify Categories
	define('_MI_XP2_NOTCAT_GLOBAL', 'ALL');
	define('_MI_XP2_NOTCAT_GLOBALDSC', 'Notification option in the entire blog');
	define('_MI_XP2_NOTCAT_CAT', 'Category under selection');
	define('_MI_XP2_NOTCAT_CATDSC', 'Notification option to category under selection');
	define('_MI_XP2_NOTCAT_AUTHOR', 'Author who is selecting it'); 
	define('_MI_XP2_NOTCAT_AUTHORDSC', 'Notification option to author who is selecting it');
	define('_MI_XP2_NOTCAT_POST', 'Article on display'); 
	define('_MI_XP2_NOTCAT_POSTDSC', 'Notification option to article on display');

	// Each Notifications
	define('_MI_XP2_NOTIFY_GLOBAL_WAITING', 'Approval waiting');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGCAP', 'It notifies when the contribution and the edit that requires approving are done. Manager exclusive use');
	define('_MI_XP2_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Approval waiting');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOST', 'Contribution of article');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTCAP', 'It notifies when the article is contributed in either of the entire this blog. ');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]Article: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENT', 'Comment contribution');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTCAP', 'It notifies when the comment is contributed in either of the entire this blog. ');
	define('_MI_XP2_NOTIFY_GLOBAL_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]Comment: "{XPRESS_POST_TITLE}"');

	define('_MI_XP2_NOTIFY_CAT_NEWPOST', 'Article contribution to selection category');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTCAP', 'It notifies when there is an article contribution in this category.');
	define('_MI_XP2_NOTIFY_CAT_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]Article: "{XPRESS_POST_TITLE}" (Condition:Category="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENT', 'Comment contribution to selection category');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTCAP', 'It notifies when there is a comment contribution in this category. ');
	define('_MI_XP2_NOTIFY_CAT_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]Comment: (Article"{XPRESS_POST_TITLE}") (Condition:Category="{XPRESS_CAT_TITLE}")');

	define('_MI_XP2_NOTIFY_AUT_NEWPOST', 'Article contribution by selection contributor');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTCAP', 'It notifies when there is an article contribution from this contributor. ');
	define('_MI_XP2_NOTIFY_AUT_NEWPOSTSBJ', '[{XPRESS_BLOG_NAME}]Article: "{XPRESS_POST_TITLE}" (Condition:Author="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENT', 'Comment contribution to selection contributor article');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTCAP', 'It notifies when the comment contribution is in the article by this contributor. ');
	define('_MI_XP2_NOTIFY_AUT_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]Comment: (Article"{XPRESS_POST_TITLE}") (Condition:Author="{XPRESS_AUTH_NAME}")');

	define('_MI_XP2_NOTIFY_POST_EDITPOST', 'Article change');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTCAP', 'It notifies when there is a change in the article on the display. ');
	define('_MI_XP2_NOTIFY_POST_EDITPOSTSBJ', '[{XPRESS_BLOG_NAME}]Article: "{XPRESS_POST_TITLE}"Change (Condition:Article specification)');

	define('_MI_XP2_NOTIFY_POST_NEWCOMMENT', 'Comment contribution to article');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTCAP', 'It notifies when the comment is contributed in the article on the display. ');
	define('_MI_XP2_NOTIFY_POST_NEWCOMMENTSBJ', '[{XPRESS_BLOG_NAME}]Comment: (Article"{XPRESS_POST_TITLE}") (Condition:Article specification)');

}
?>