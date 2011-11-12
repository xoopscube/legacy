<?php
if( ! defined( 'XP2_BLOCK_LANG_INCLUDED' ) ) {
	define( 'XP2_BLOCK_LANG_INCLUDED' , 1 ) ;
// general	
	define("_MB_XP2_COUNT",'Number of displays');
	define("_MB_XP2_COUNT_ZERO_ALL",'Number of displays (Everything is displayed in case of 0)');
	define("_MB_XP2_LENGTH","length");
	define("_MB_XP2_ALL","all");
	define("_MB_XP2_BLOCK_CACHE_ERR","Cash doesn't exist. <br />Please access the %s module first.");
	define("_MB_XP2_SHOW_NUM_OF_POST","The number of entries is displayed.");
	define("_MB_XP2_SHOW_DROP_DOWN","It displays it by the drop down list.");
	define("_MB_XP2_HIDE_EMPTY","hide empty");
	define("_MB_XP2_TITLE","title");
	define("_MB_XP2_PUBLISH_DATE","publish date");
	define("_MB_XP2_SORT_ORDER","sort order");
	define("_MB_XP2_SORT_ASC","ASC");
	define("_MB_XP2_SORT_DESC","DESC");
	define("_MB_XP2_SHOW_DATE_SELECT","Date Display");
	define("_MB_XP2_SHOW_DATE_NONE","Not display");
	define("_MB_XP2_SHOW_POST_DATE","Post Date");
	define("_MB_XP2_SHOW_MODIFY_DATE","Modify Date");
	define("_MB_XP2_SHOW_DATE","The date is displayed.");
	define("_MB_XP2_DATE_FORMAT","Format at date (The setting with WordPress is applied for the blank).");
	define("_MB_XP2_TIME_FORMAT","Format at time (The setting with WordPress is applied for the blank).");
	define("_MB_XP2_FLAT","Flat");
	define("_MB_XP2_LIST","List");
	define("_MB_XP2_FILE_NAME","Files Name");
	define("_MB_XP2_THISTEMPLATE","Template");
	define("_MB_XP2_NO_JSCRIPT","Javascript should be enable by a browser.");
	define("_MB_XP2_CACHE_NOT_WRITABLE","Cache Directory is not writable.");
	
// recent comment block	
	define("_MB_XP2_COMM_DISP_AUTH","The comment author name is displayed.");
	define("_MB_XP2_COMM_DISP_TYPE","The comment type is displayed.");
	define("_MB_XP2_COM_TYPE","select the type of the displayed comment.");
	define("_MB_XP2_COMMENT","Comment");
	define("_MB_XP2_TRUCKBACK","Trackback");
	define("_MB_XP2_PINGBACK","Pingback");
	
// recent posts content
	define("_MB_XP2_P_EXCERPT","The post is displayed by the except.");
	define("_MB_XP2_P_EXCERPT_SIZE","Number of except characters");
	define("_MB_XP2_CATS_SELECT","Select categorie");
	define("_MB_XP2_TAGS_SELECT","Tags Select(Comma separated list of tags)");
	define("_MB_XP2_DAY_SELECT","Select Post Date");
	define("_MB_XP2_NONE","None");
	define("_MB_XP2_TODAY","Today");
	define("_MB_XP2_LATEST","Latest");
	define("_MB_XP2_DAY_BETWEEN","Between");
	define("_MB_XP2_DAYS_AND","and");
	define("_MB_XP2_DAYS_AGO","days ago");
	define("_MB_XP2_CATS_DIRECT_SELECT","Direct input of ID(Comma separated list of categorie ID)");
	
// recent posts list	
	define("_MB_XP2_REDNEW_DAYS","Passed days displayed by marking red 'New'");
	define("_MB_XP2_GREENNEW_DAYS","Passed days displayed by marking green 'New'");	

// calender		
	define("_MB_XP2_SUN_COLOR","Color on Sunday");
	define("_MB_XP2_SAT_COLOR","Color on Saturday");
	
// popular		
	define("_MB_XP2_MONTH_RANGE","The one in the number a specified month is displayed (0; all).");
	
// archives
	define("_MB_XP2_ARC_TYPE","The type of archive");
	define("_MB_XP2_ARC_YEAR","yearly");
	define("_MB_XP2_ARC_MONTH","monthly");
	define("_MB_XP2_ARC_WEEK","weekly");
	define("_MB_XP2_ARC_DAY","daily");
	define("_MB_XP2_ARC_POST","post by post");

// authors	
	define("_MB_XP2_EXCLUEDEADMIN","The manager is excluded from the list.");
	define("_MB_XP2_SHOW_FULLNAME","The author name is displayed by the full name.");

// page 	
	define("_MB_XP2_PAGE_ORDERBY","List Pages by Page Order ");
	define("_MB_XP2_PAGE_TITLE","post_title");
	define("_MB_XP2_PAGE_MENU_ORDER","menu_order");
	define("_MB_XP2_PAGE_POST_DATE","post_date");
	define("_MB_XP2_PAGE_POST_MODIFY","post_modified");
	define("_MB_XP2_PAGE_ID","ID");
	define("_MB_XP2_PAGE_AUTHOR","post_author ID");
	define("_MB_XP2_PAGE_SLUG","post_name");
	define("_MB_XP2_PAGE_EXCLUDE","Define a comma-separated list of Page IDs to be excluded from the list ");
	define("_MB_XP2_PAGE_EXCLUDE_TREE","Define a comma-separated list of parent Page IDs to be excluded. Use this parameter to exclude a parent and all of that parent's child Pages.");
	define("_MB_XP2_PAGE_INCLUDE","Only include certain Pages in the list , this parameter takes a comma-separated list of Page IDs. ");
	define("_MB_XP2_PAGE_DEPTH","how many levels in the hierarchy of pages are to be included in the list (0=all Pages and sub-pages displayedj");
	define("_MB_XP2_PAGE_CHILD_OF","Displays the sub-pages of a single Page onlyB(uses the ID for a Page as the valuej");
	define("_MB_XP2_PAGE_HIERARCHICAL","Display sub-Pages in an indented manner below their parent or list the Pages inline.");
	define("_MB_XP2_PAGE_META_KEY","Only include the Pages that have this Custom Field Key (use in conjunction with the meta_value field).");
	define("_MB_XP2_PAGE_META_VALUE","Only include the Pages that have this Custom Field Value (use in conjuntion with the meta_key field).");
	
// Search
	define("_MB_XP2_SEARCH_LENGTH","Length of search textbox");
	
// tag cloud
	define("_MB_XP2_CLOUD_SMALLEST",'The text size of the tag with the smallest count value ');
	define("_MB_XP2_CLOUD_LARGEST",'The text size of the tag with the highest count value');
	define("_MB_XP2_CLOUD_UNIT","Unit of measure as pertains to the smallest and largest values. This can be any CSS length value, e.g. pt, px, em, %;");
	define("_MB_XP2_CLOUD_NUMBER","The number of actual tags to display in the cloud. (Use '0' to display all tags.)");
	define("_MB_XP2_CLOUD_FORMAT","Format of the cloud display");
	define("_MB_XP2_CLOUD_ORDERBY","Order of the tags");
	define("_MB_XP2_CLOUD_ORDER","Sort order. Valid values('RAND' tags are in a random order. Note: this parameter was introduced with Version 2.5.)");
	define("_MB_XP2_CLOUD_EXCLUDE","Comma separated list of tags (term_id) to exclude.");
	define("_MB_XP2_CLOUD_INCLUDE","Comma separated list of tags (term_id) to include.");
	define("_MB_XP2_RAND","RAND");
	define("_MB_XP2_TAG_NAME","tag name");
	define("_MB_XP2_TAG_COUNT","count");
	
// Categorie
	define("_MB_XP2_CAT_ALL_STR","All categories link Display Title. (blank is not display)");
	define("_MB_XP2_CAT_ORDERBY","Order of the categories");
	define("_MB_XP2_CAT_NAME","name");
	define("_MB_XP2_CAT_COUNT","count");
	define("_MB_XP2_CAT_ID","ID");
	define("_MB_XP2_SHOW_LAST_UPDATE","Should the last updated timestamp for posts be displayed.");
	define("_MB_XP2_CAT_HIDE_EMPTY","hide display of categories with no posts.");
	define("_MB_XP2_DESC_FOR_TITLE","Sets whether a category's description is inserted into the title attribute of the links created");
	define("_MB_XP2_CAT_EXCLUDE","Exclude one or more categories from the results. This parameter takes a comma-separated list of categories by unique ID");
	define("_MB_XP2_CAT_INCLUDE","Only include the categories detailed in a comma-separated list by unique ID, in ascending order.");
	define("_MB_XP2_CAT_HIERARCHICAL","Display sub-categories as inner list items (below the parent list item) or inline.");
	define("_MB_XP2_CAT_DEPTH","how many levels in the hierarchy of Categories are to be included in the list of Categories.(0=All Categories and child Categories)");
	
// meta 
	define("_MB_XP2_META_WP_LINK","The link to the WordPress site is displayed.");
	define("_MB_XP2_META_XOOPS_LINK","The link to the Xoops site is displayed.");
	define("_MB_XP2_META_POST_RSS","RSS of the posts is displayed.");
	define("_MB_XP2_META_COMMENT_RSS","RSS of the comments is displayed.");
	define("_MB_XP2_META_POST_NEW","'new post' is displayed.");
	define("_MB_XP2_META_ADMIN","'admin' is displayed.");
	define("_MB_XP2_META_README","ReadMe is displayed.");
	define("_MB_XP2_META_CH_STYLE","'display mode' is displayed.");

// widget 
	define("_MB_XP2_SELECT_WIDGET","Displayed Widget is selected.");
	define("_MB_XP2_NO_WIDGET","Widget displayed on the WordPress side has not been selected. ");
	define("_MB_XP2_WIDGET_TITLE_SHOW","When only one Widget has been selected, the title of Widget is displayed.");
	
// custom 
	define("_MB_XP2_ENHACED_FILE","Input the file name used in the custom block.");
	define("_MB_XP2_MAKE_ENHACED_FILE","Please make the file specified here in the block directory of the theme.");

// blog_list
	define("_MB_XP2_BLOG_ORDERBY","Order of the blogs");
	define("_MB_XP2_BLOG_NAME","name");
	define("_MB_XP2_BLOG_COUNT","count");
	define("_MB_XP2_BLOG_ID","ID");
// global_blog_list
	define("_MB_XP2_SHOW_BLOGS_SELECT","Select Display Blogs");
	define("_MB_XP2_EXCLUSION_BLOGS_SELECT","Select Exclusion Blogs");
	define("_MB_XP2_BLOGS_DIRECT_SELECT","Direct input of ID(Comma separated list of blog ID)");
	define("_MB_XP2_SHOWN_FOR_EACH_BLOG","Shown for each blog");

}
?>