<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'wraps' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

// Main menu
define( $constpref.'_MENU_NEWPAGE',  'New Page');
define( $constpref.'_MENU_RECENT',   'Recent Changes');
define( $constpref.'_MENU_PAGELIST', 'Page List');
define( $constpref.'_MENU_HELP',     'Help');
define( $constpref.'_MENU_RELAYTED', 'Relayted');
define( $constpref.'_MENU_EDIT',     'Edit');
define( $constpref.'_MENU_SOURCE',   'Wiki Source');
define( $constpref.'_MENU_DIFF',     'Edit History');
define( $constpref.'_MENU_BACKUPS',  'Backups');
define( $constpref.'_MENU_ATTACHES', 'Attachments');
define( $constpref.'_MENU_REFERER',  'Referers');

// Names of blocks for this module (Not all module has blocks)
define( $constpref.'_BNAME_A_PAGE','Show page  ('.$mydirname.')');
define( $constpref.'_BDESC_A_PAGE','The content can be displayed in the block by specifying page name.');
define( $constpref.'_BNAME_NOTIFICATION','Notifications ('.$mydirname.')');
define( $constpref.'_BDESC_NOTIFICATION','Set up about notifications.');
define( $constpref.'_BNAME_FUSEN','Fusen(Tag) ('.$mydirname.')');
define( $constpref.'_BDESC_FUSEN','The control menu of the Fusen(Tag) plugin is displayed.');
define( $constpref.'_BNAME_MENUBAR','MenuBar ('.$mydirname.')');
define( $constpref.'_BDESC_MENUBAR','Show MenuBar');

define( $constpref.'_MODULE_DESCRIPTION' , 'A wiki module based on PukiWiki.' ) ;

define( $constpref.'_PLUGIN_CONVERTER' , 'Plugin Converter' ) ;
define( $constpref.'_SKIN_CONVERTER' , 'Skin Converter' ) ;
define( $constpref.'_ADMIN_CONF' , 'Preference' ) ;
define( $constpref.'_ADMIN_TOOLS' , 'Admin Tools' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN','Languages');
define( $constpref.'_ADMENU_MYTPLSADMIN','Templates');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define( $constpref.'_ADMENU_MYPREFERENCES','Preferences');

define( $constpref.'_COM_DIRNAME','Comment-integration: dirname of d3forum');
define( $constpref.'_COM_FORUM_ID','Comment-integration: forum ID');
define( $constpref.'_COM_VIEW','View of comment-integration');
define( $constpref.'_COM_ORDER','Order of comment-integration');
define( $constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// Notify Replaces
define($constpref.'_NOTCAT_REPLASE2MODULENAME', 'this module');
define($constpref.'_NOTCAT_REPLASE2FIRSTLEV', 'first hierarchy');
define($constpref.'_NOTCAT_REPLASE2SECONDLEV', 'second hierarchy');
//define($constpref.'_NOTCAT_REPLASE2PAGENAME', 'this page');

// Notify Categories
define($constpref.'_NOTCAT_PAGE', 'This page');
define($constpref.'_NOTCAT_PAGEDSC', 'Notifications about this page.');
define($constpref.'_NOTCAT_PAGE1', 'first hierarchy or under');
define($constpref.'_NOTCAT_PAGE1DSC', 'Notifications about first hierarchy or under.');
define($constpref.'_NOTCAT_PAGE2', 'second hierarchy or under');
define($constpref.'_NOTCAT_PAGE2DSC', 'Notifications about second hierarchy or under.');
define($constpref.'_NOTCAT_GLOBAL', 'this module');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notifications about whole of this module');

// Each Notifications
define($constpref.'_NOTIFY_PAGE_UPDATE', 'Page edit');
define($constpref.'_NOTIFY_PAGE_UPDATECAP', 'Notify me of edited this page.');
define($constpref.'_NOTIFY_PAGE1_UPDATECAP', 'Notify me of edited first hierarchy or under.');
define($constpref.'_NOTIFY_PAGE2_UPDATECAP', 'Notify me of edited second hierarchy or under.');
define($constpref.'_NOTIFY_PAGE_UPDATESBJ', '[{X_SITENAME}] {X_MODULE}:{PAGE_NAME} edited');
define($constpref.'_NOTIFY_GLOBAL_UPDATECAP', 'Notify me of edited any page in this module.');

}
