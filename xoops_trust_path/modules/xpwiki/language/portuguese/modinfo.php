<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'wraps' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

// Names of blocks for this module (Not all module has blocks)
define( $constpref."_BNAME_A_PAGE","Mostrar página  ({$mydirname})");
define( $constpref."_BDESC_A_PAGE","O conteúdo pode ser mostrado em um bloco mediante especificação do nome da página.");
define( $constpref."_BNAME_NOTIFICATION","Notificações ({$mydirname})");
define( $constpref."_BDESC_NOTIFICATION","Configurar notificações.");
define( $constpref."_BNAME_FUSEN","Fusen(Tag) ({$mydirname})");
define( $constpref."_BDESC_FUSEN","O Menu de controle do plugin Fusen(Tag)é mostrado.");
define( $constpref."_BNAME_MENUBAR","Barra do menu ({$mydirname})");
define( $constpref."_BDESC_MENUBAR","Mostrar a barra do menú");

define( $constpref.'_MODULE_DESCRIPTION' , 'Módulo wiki baseado no PukiWiki.' ) ;

define( $constpref.'_PLUGIN_CONVERTER' , 'Conversor de Plugin' ) ;
define( $constpref.'_SKIN_CONVERTER' , 'Convertor de Skin ' ) ;
define( $constpref.'_ADMIN_CONF' , 'Preferências' ) ;
define( $constpref.'_ADMIN_TOOLS' , 'Ferramentas administrativas' ) ;

define( $constpref.'_COM_DIRNAME','Integração de comentários: nome do diretório do d3forum');
define( $constpref.'_COM_FORUM_ID','Integração de comentários: ID do fórum');
define( $constpref.'_COM_VIEW','Vizualização da integração de comentários');
define( $constpref.'_COM_ORDER','Ordenação da integração de comentários');
define( $constpref.'_COM_POSTSNUM','Número máximo de posts mostrados na integração de comentários');

// Notify Replaces
define($constpref.'_NOTCAT_REPLASE2MODULENAME', 'este módulo');
define($constpref.'_NOTCAT_REPLASE2FIRSTLEV', 'primeira hierarquia');
define($constpref.'_NOTCAT_REPLASE2SECONDLEV', 'segunda hierarquia');
//define($constpref.'_NOTCAT_REPLASE2PAGENAME', 'this page');

// Notify Categories
define($constpref.'_NOTCAT_PAGE', 'Está página');
define($constpref.'_NOTCAT_PAGEDSC', 'Notificações sobre está página.');
define($constpref.'_NOTCAT_PAGE1', 'primeira categoria ou inferior');
define($constpref.'_NOTCAT_PAGE1DSC', 'Notificações sobre a primeira categoria ou inferior');
define($constpref.'_NOTCAT_PAGE2', 'segunda categoria ou inferior');
define($constpref.'_NOTCAT_PAGE2DSC', 'Notificações sobre a segunda categoria ou inferior.');
define($constpref.'_NOTCAT_GLOBAL', 'este módulo');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notificações sobre todo este módulo');

// Each Notifications
define($constpref.'_NOTIFY_PAGE_UPDATE', 'Editar página');
define($constpref.'_NOTIFY_PAGE_UPDATECAP', 'Notifique-me se está página for estitada.');
define($constpref.'_NOTIFY_PAGE1_UPDATECAP', 'Notifique-me da edição da primeira hieraquia ou inferiores.');
define($constpref.'_NOTIFY_PAGE2_UPDATECAP', 'Notifique-me da edição da segunada hieraquia ou inferiores.');
define($constpref.'_NOTIFY_PAGE_UPDATESBJ', '[{X_SITENAME}] {X_MODULE}:{PAGE_NAME} foi editada');
define($constpref.'_NOTIFY_GLOBAL_UPDATECAP', 'Notifique-me quando qualquer página do wiki for editada.');

}

?>
