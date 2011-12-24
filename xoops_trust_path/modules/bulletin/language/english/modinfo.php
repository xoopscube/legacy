<?php /* English Translation by Marcelo Yuji Himoro <http://yuji.ws> & Suin <http://xoops.suinyeze.com/>*/
// Module Info

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'bulletin' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","News");

// A brief description of this module
define($constpref."_DESC","Creates a Slashdot-like news system, where users can post comments freely.");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME1","News categories");
define($constpref."_BDESC1","");
define($constpref."_BNAME2","Today's top story");
define($constpref."_BDESC2","");
define($constpref."_BNAME3","Calendar");
define($constpref."_BDESC3","");
define($constpref."_BNAME4","Recent news");
define($constpref."_BDESC4","");
define($constpref."_BNAME5","Recent news by categories");
define($constpref."_BDESC5","");
define($constpref."_BNAME6","Bulletin recent comments");
define($constpref."_BDESC6","");

// Sub menu
define($constpref."_SMNAME1","Submit news");
define($constpref."_SMNAME2","Archive");

// Admin
define($constpref."_ADMENU2","Categories manager");
define($constpref."_ADMENU3","Post a new story");
define($constpref."_ADMENU4","Posting permission manager");
define($constpref."_ADMENU5","News manager");
define($constpref."_ADMENU7","Import from news");
define($constpref.'_ADMENU_MYLANGADMIN','languages');
define($constpref.'_ADMENU_MYTPLSADMIN','templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','blocks/permissions');

// Title of config items
define($constpref."_CONFIG1","Number of news to display on the index page");
define($constpref."_CONFIG1_D","Set the number of news to display on the index page.");
define($constpref."_CONFIG2","Display navigation box?");
define($constpref."_CONFIG2_D","Select 'Yes' to display a navigation box for category select at the top of each news page.");
define($constpref."_CONFIG3","Submit/edit textarea height");
define($constpref."_CONFIG3_D","Set the number of lines of textarea on submit.php page.");
define($constpref."_CONFIG4","Submit/edit textarea width");
define($constpref."_CONFIG4_D","Set the number of columns of textarea on submit.php page.");
define($constpref."_CONFIG5","Timestamp");
define($constpref."_CONFIG5_D","Use PHP date/XOOPS formatTimestamp functions as reference.");
define($constpref."_CONFIG6","Reflect posts to user's post count");
define($constpref."_CONFIG6_D","When a story posted from submit.php is approved, user's 'Posts' will be increased.");
define($constpref."_CONFIG7","Path to category image directory");
define($constpref."_CONFIG7_D","Set the absolute path.");
define($constpref."_CONFIG8","Print friendly page image URL");
define($constpref."_CONFIG8_D","Set the URL for the logo image shown on print friendly page.");
define($constpref."_CONFIG9","Change site name to story name");
define($constpref."_CONFIG9_D","Replaces the site name for the story subject. It's said to be effective for SEO.");
define($constpref."_CONFIG10","assign RSS URL on xoops_module_header");
define($constpref."_CONFIG10_D","");
// 1.01 added
define($constpref."_CONFIG11","Display 'Print' icon?");
define($constpref."_CONFIG11_D","");
define($constpref."_CONFIG12","Display 'Tell a frind' icon?");
define($constpref."_CONFIG12_D","");
define($constpref."_CONFIG13","Use Tell A Friend module?");
define($constpref."_CONFIG13_D","");
define($constpref."_CONFIG14","Display RSS link?");
define($constpref."_CONFIG14_D","");
define($constpref.'_CONFIG145','feed RSS into backend.php (only for XCL)');
define($constpref.'_CONFIG145_D', '');
// 2.00 added
define($constpref."_CONFIG15","Enable related articles feature?");
define($constpref."_CONFIG15_D","");
define($constpref."_CONFIG16","Display recent stories in the same category?");
define($constpref."_CONFIG16_D","Displays a list of articles in the same category at the bottom of each story.");
define($constpref."_CONFIG17","Number of recent storeis in the same category.");
define($constpref."_CONFIG17_D","");
define($constpref."_CONFIG18","Display category bread crumb?");
define($constpref."_CONFIG18_D","A category tree is displayed in each articles.");
define($constpref.'_CONFIG19','use common/fckeditor');
define($constpref.'_CONFIG19_D', 'Posters can use FCKeditor on XOOPS if he/she is allowed to use HTML');

define($constpref.'_COM_DIRNAME','Comment-integration: dirname of d3forum');
define($constpref.'_COM_FORUM_ID','Comment-integration: forum ID');
define($constpref.'_COM_VIEW','View of comment-integration');
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// by yoshis
define($constpref.'_ADMENU_CATEGORYACCESS' , 'Permissions of Categories' ) ;
define($constpref.'_IMAGES_DIR','Directory for image files');
define($constpref.'_IMAGES_DIRDSC','relative path should be set in the module directory. default: images');

// Text for notifications
define($constpref."_GLOBAL_NOTIFY","Global");
define($constpref."_GLOBAL_NOTIFYDSC","Global news notification options.");

define($constpref."_STORY_NOTIFY","Current story");
define($constpref."_STORY_NOTIFYDSC","Notification options that apply to the current story.");

define($constpref."_GLOBAL_NEWCATEGORY_NOTIFY","New category");
define($constpref."_GLOBAL_NEWCATEGORY_NOTIFYCAP","Notify me when a new category is created.");
define($constpref."_GLOBAL_NEWCATEGORY_NOTIFYDSC","Notify me when a new category is created.");
define($constpref."_GLOBAL_NEWCATEGORY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}: New category created");

define($constpref."_GLOBAL_STORYSUBMIT_NOTIFY","New story submitted(awaiting approval)");
define($constpref."_GLOBAL_STORYSUBMIT_NOTIFYCAP","Notify me when a new story is submitted(awaiting approval).");
define($constpref."_GLOBAL_STORYSUBMIT_NOTIFYDSC","Notify me when a new story is submitted(awaiting approval).");
define($constpref."_GLOBAL_STORYSUBMIT_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}: New story submitted(awaiting approval)");

define($constpref."_GLOBAL_NEWSTORY_NOTIFY","New story published");
define($constpref."_GLOBAL_NEWSTORY_NOTIFYCAP","Notify me when a new story is published.");
define($constpref."_GLOBAL_NEWSTORY_NOTIFYDSC","Notify me when a new story is published.");
define($constpref."_GLOBAL_NEWSTORY_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}: New news published");

define($constpref."_STORY_APPROVE_NOTIFY","News approved");
define($constpref."_STORY_APPROVE_NOTIFYCAP","Notify me when this news is approved.");
define($constpref."_STORY_APPROVE_NOTIFYDSC","Notify me when this news is approved.");
define($constpref."_STORY_APPROVE_NOTIFYSBJ","[{X_SITENAME}] {X_MODULE}: News approved");

// added 2.01
define($constpref."_NOTIFY5_TITLE", "New comment posted");
define($constpref."_NOTIFY5_CAPTION", "Notify me when a new comment is posted.");
define($constpref."_NOTIFY5_DESC", "Notify me when a new comment is posted.");
define($constpref."_NOTIFY5_SUBJECT", "[{X_SITENAME}] {X_MODULE}: New comment posted");

}
?>