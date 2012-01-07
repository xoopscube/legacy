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
define( $constpref."_BNAME_A_PAGE","Mostrar pсgina  ({$mydirname})");
define( $constpref."_BDESC_A_PAGE","O conteњdo pode ser mostrado em um bloco mediante especificaчуo do nome da pсgina.");
define( $constpref."_BNAME_NOTIFICATION","Notificaчѕes ({$mydirname})");
define( $constpref."_BDESC_NOTIFICATION","Configurar notificaчѕes.");
define( $constpref."_BNAME_FUSEN","Fusen(Tag) ({$mydirname})");
define( $constpref."_BDESC_FUSEN","O Menu de controle do plugin Fusen(Tag)щ mostrado.");
define( $constpref."_BNAME_MENUBAR","Barra do menu ({$mydirname})");
define( $constpref."_BDESC_MENUBAR","Mostrar a barra do menњ");

define( $constpref.'_MODULE_DESCRIPTION' , 'Mѓdulo wiki baseado no PukiWiki.' ) ;

define( $constpref.'_PLUGIN_CONVERTER' , 'Conversor de Plugin' ) ;
define( $constpref.'_SKIN_CONVERTER' , 'Convertor de Skin ' ) ;
define( $constpref.'_ADMIN_CONF' , 'Preferъncias' ) ;
define( $constpref.'_ADMIN_TOOLS' , 'Ferramentas administrativas' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN','Idiomas');
define( $constpref.'_ADMENU_MYTPLSADMIN','Modelos');
define( $constpref.'_ADMENU_MYBLOCKSADMIN','Blocos e permissѕes');
define( $constpref.'_ADMENU_MYPREFERENCES','Preferъncias');

define( $constpref.'_COM_DIRNAME','Integraчуo de comentсrios: nome do diretѓrio do d3forum');
define( $constpref.'_COM_FORUM_ID','Integraчуo de comentсrios: ID do fѓrum');
define( $constpref.'_COM_VIEW','Vizualizaчуo da integraчуo de comentсrios');
define( $constpref.'_COM_ORDER','Ordenaчуo da integraчуo de comentсrios');
define( $constpref.'_COM_POSTSNUM','Nњmero mсximo de posts mostrados na integraчуo de comentсrios');

// Notify Replaces
define($constpref.'_NOTCAT_REPLASE2MODULENAME', 'este mѓdulo');
define($constpref.'_NOTCAT_REPLASE2FIRSTLEV', 'primeira hierarquia');
define($constpref.'_NOTCAT_REPLASE2SECONDLEV', 'segunda hierarquia');
//define($constpref.'_NOTCAT_REPLASE2PAGENAME', 'this page');

// Notify Categories
define($constpref.'_NOTCAT_PAGE', 'Estс pсgina');
define($constpref.'_NOTCAT_PAGEDSC', 'Notificaчѕes sobre estс pсgina.');
define($constpref.'_NOTCAT_PAGE1', 'primeira categoria ou inferior');
define($constpref.'_NOTCAT_PAGE1DSC', 'Notificaчѕes sobre a primeira categoria ou inferior');
define($constpref.'_NOTCAT_PAGE2', 'segunda categoria ou inferior');
define($constpref.'_NOTCAT_PAGE2DSC', 'Notificaчѕes sobre a segunda categoria ou inferior.');
define($constpref.'_NOTCAT_GLOBAL', 'este mѓdulo');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notificaчѕes sobre todo este mѓdulo');

// Each Notifications
define($constpref.'_NOTIFY_PAGE_UPDATE', 'Editar pсgina');
define($constpref.'_NOTIFY_PAGE_UPDATECAP', 'Notifique-me se estс pсgina for estitada.');
define($constpref.'_NOTIFY_PAGE1_UPDATECAP', 'Notifique-me da ediчуo da primeira hieraquia ou inferiores.');
define($constpref.'_NOTIFY_PAGE2_UPDATECAP', 'Notifique-me da ediчуo da segunada hieraquia ou inferiores.');
define($constpref.'_NOTIFY_PAGE_UPDATESBJ', '[{X_SITENAME}] {X_MODULE}:{PAGE_NAME} foi editada');
define($constpref.'_NOTIFY_GLOBAL_UPDATECAP', 'Notifique-me quando qualquer pсgina do wiki for editada.');

}

