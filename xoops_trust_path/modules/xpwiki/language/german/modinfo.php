<?php

//
// German Translation Version 1.0 (11.03.2008)
// Translation English --> German: Octopus (hunter0815@googlemail.com)
// sicherlich steckt hier noch reichlich Qualitätspotential in den Übersetzungen ;-)

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
define( $constpref."_BNAME_A_PAGE","Zeige Seite  ({$mydirname})");
define( $constpref."_BDESC_A_PAGE","Der Inhalt kann im Block angezeigt werden, indem der Seiten Name spezifiziert wird.");
define( $constpref."_BNAME_NOTIFICATION","Benachrichtigungen ({$mydirname})");
define( $constpref."_BDESC_NOTIFICATION","Benachrichtigungs-Einstellungen");
define( $constpref."_BNAME_FUSEN","Fusen(Tag) ({$mydirname})");
define( $constpref."_BDESC_FUSEN","Das Kontroll-Menü für den das Fusen(Tag)Plugin wird angezeigt.");
define( $constpref."_BNAME_MENUBAR","Menüleiste ({$mydirname})");
define( $constpref."_BDESC_MENUBAR","Zeige Menüleiste");

define( $constpref.'_MODULE_DESCRIPTION' , 'Ein Wiki-Modul basierend auf PukiWiki.' ) ;

define( $constpref.'_PLUGIN_CONVERTER' , 'Plugin Converter' ) ;
define( $constpref.'_SKIN_CONVERTER' , 'Skin Converter' ) ;
define( $constpref.'_ADMIN_CONF' , 'Einstellung' ) ;
define( $constpref.'_ADMIN_TOOLS' , 'Admin Tools' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN','Sprachen');
define( $constpref.'_ADMENU_MYTPLSADMIN','Vorlagen (Templates)');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Blockberechtigungen');
define( $constpref.'_ADMENU_MYPREFERENCES','Voreinstellungen');

define( $constpref.'_COM_DIRNAME','Kommentar-Integration: Ordnername des d3forum');
define( $constpref.'_COM_FORUM_ID','Kommentar-Integration: Forum ID');
define( $constpref.'_COM_VIEW','View of comment-integration');
define( $constpref.'_COM_ORDER','Order of comment-integration');
define( $constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// Notify Replaces
define($constpref.'_NOTCAT_REPLASE2MODULENAME', 'dieses Modul');
define($constpref.'_NOTCAT_REPLASE2FIRSTLEV', 'erste Hierarchie');
define($constpref.'_NOTCAT_REPLASE2SECONDLEV', 'zweite Hierarchie');
//define($constpref.'_NOTCAT_REPLASE2PAGENAME', 'diese Seite');

// Notify Categories
define($constpref.'_NOTCAT_PAGE', 'diese Seite');
define($constpref.'_NOTCAT_PAGEDSC', 'Benachrichtigungen über diese Seite.');
define($constpref.'_NOTCAT_PAGE1', 'erste Hierarchie oder tiefer');
define($constpref.'_NOTCAT_PAGE1DSC', 'Benachrichtigungen über due erste Hierarchie oder tiefer.');
define($constpref.'_NOTCAT_PAGE2', 'zweite Hierarchie oder tiefer');
define($constpref.'_NOTCAT_PAGE2DSC', 'Benachrichtigungen über die zweite Hierarchie oder tiefer.');
define($constpref.'_NOTCAT_GLOBAL', 'dieses Modul');
define($constpref.'_NOTCAT_GLOBALDSC', 'Benachrichtigungen über alles in diesem Modul.');

// Each Notifications
define($constpref.'_NOTIFY_PAGE_UPDATE', 'bearbeitete Seite');
define($constpref.'_NOTIFY_PAGE_UPDATECAP', 'Benachrichtige mich über Änderungen auf dieser Seite.');
define($constpref.'_NOTIFY_PAGE1_UPDATECAP', 'Benachrichtige mich über Änderungen auf dieser oder einer tieferen Hierarchiestufe.');
define($constpref.'_NOTIFY_PAGE2_UPDATECAP', 'Benachrichtige mich über Änderungen auf der zweiten oder einer tieferen Hierarchiestufe.');
define($constpref.'_NOTIFY_PAGE_UPDATESBJ', '[{X_SITENAME}] {X_MODULE}:{PAGE_NAME} geändert');
define($constpref.'_NOTIFY_GLOBAL_UPDATECAP', 'Benachrichtige mich über sämtliche Änderungen in diesem Modul.');

}
