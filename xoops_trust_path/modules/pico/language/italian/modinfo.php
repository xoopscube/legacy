<?php
//traduzione italiana di evoc cadelsanto@gmail.com www.cadelsanto.org
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {













// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 18:29:24
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-02 16:22:08
define($constpref.'_AUTOREGISTCLASS','class name to register/unregister HTML wrapped files');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-19 04:29:54
define($constpref.'_ADMENU_TAGS','Tags');

// Appended by Xoops Language Checker -GIJOE- in 2008-10-01 12:11:21
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

// Appended by Xoops Language Checker -GIJOE- in 2007-09-12 17:00:58
define($constpref.'_BNAME_MYWAITINGS','My waiting posts');

// Appended by Xoops Language Checker -GIJOE- in 2007-06-15 05:03:01
define($constpref.'_BNAME_SUBCATEGORIES','Subcategories');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT','new content');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP','Notify if a new content is registered. (approved contents only)');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE} : New content');

// Appended by Xoops Language Checker -GIJOE- in 2007-05-29 16:39:06
define($constpref.'_COM_VIEW','View of Comment-integration');

// Appended by Xoops Language Checker -GIJOE- in 2007-05-07 17:48:20
define($constpref.'_ADMENU_MYLANGADMIN','Languages');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","Un modulo per contenuti statici");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'Lista dei contenuti' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'Permessi delle categorie' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'Importa/Sincronizza' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , 'Templates' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'Blocchi/Permessi' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , 'Preferenze' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','Abilita modalità mascheramento');
define($constpref.'_USE_REWRITE','Abilita modalità mod_rewrite');
define($constpref.'_USE_REWRITEDSC','Dipende dal tuo ambiente. Se attivi, rinomina .htaccess.rewrite_wraps(with wraps) o htaccess.rewrite_normal(without wraps) in  .htaccess nella cartella XOOPS_ROOT_PATH/modules/(dirname)/');
define($constpref.'_WRAPSAUTOREGIST','Abilita HTML l\'auto registrazione dei files HTML mascherati nel DB come contenuti');
define($constpref.'_TOP_MESSAGE','Descrizione della categoria PRINCIPALE');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','Mostra menu(indice) in cima a questo modulo');
define($constpref.'_LISTASINDEX',"Mostra indice contenuti in cima alla categoria");
define($constpref.'_LISTASINDEXDSC','SI significa che l\'indice autocompilato è mostrato in cima alla categoria. No significa che un contenuto con una più alta priorità è mostrato invece che l\'indice autocompilato');
define($constpref.'_SHOW_BREADCRUMBS','Mostra breadcrumbs');
define($constpref.'_SHOW_PAGENAVI','Mostra pagina di navigazione');
define($constpref.'_SHOW_PRINTICON','Mostra icona per la stampa amichevole');
define($constpref.'_SHOW_TELLAFRIEND','Mostra icone per invia a un\'amico');
define($constpref.'_SEARCHBYUID','Abilita concepts of poster');
define($constpref.'_SEARCHBYUIDDSC','I contenuti saranno messi fra i posts nel profilo utente. Se tu usi questo modulo per contenuti statici, scegli no.');
define($constpref.'_USE_TAFMODULE','Usa modulo "tellafriend"');
define($constpref.'_FILTERS','Default filter set');
define($constpref.'_FILTERSDSC','Inserisci nomi nel filtro, separati da un | (pipe)');
define($constpref.'_FILTERSDEFAULT','xcode|smiley|nl2br');
define($constpref.'_FILTERSF','Filtri forzati');
define($constpref.'_FILTERSFDSC','Immetti nomi da filtrare separati con ,(virgola). filtro: LAST significa che il filtro è passato nell\'ultima fase. Gli altri filtri sono passati nella prima fase.');
define($constpref.'_FILTERSP','Filtri proibiti');
define($constpref.'_FILTERSPDSC','Inserisci i nomi filtro separati da ,(virgola).');
define($constpref.'_SUBMENU_SC','Mostra contenuti nel submenu');
define($constpref.'_SUBMENU_SCDSC','Solo le categorie sono mostrate di default. Se tu cambi questo su SI i contenuti marcati a "menu" saranno mostrati');
define($constpref.'_SITEMAP_SC','Mostra contenuti nel modulo sitemap');
define($constpref.'_USE_VOTE','usa la funzione del Voto');
define($constpref.'_GUESTVOTE_IVL','Voto da anonimi');
define($constpref.'_GUESTVOTE_IVLDSC','Imposta a 0 per disabilitare il voto degli ospiti. Gli altri numeri significano i secondi che devono trascorrere prima di un altro post dallo stesso IP.');
define($constpref.'_HTMLHEADER','Header HTML comuni');
define($constpref.'_CSS_URI','URI del file CSS per questo modulo');
define($constpref.'_CSS_URIDSC','Può essere settato un path relativo o assoluto. Default: {mod_url}/index.css');
define($constpref.'_IMAGES_DIR','Cartella per i files di immagini');
define($constpref.'_IMAGES_DIRDSC','path relativa che dovrebbe essere settata nella cartella del modulo. Default: images');
define($constpref.'_BODY_EDITOR','Editor per il testo');
define($constpref.'_HISTORY_P_C','Quante revisioni sono conservate nel DB');
define($constpref.'_MLT_HISTORY','Tempo di durata minima di ciascuna revisione (sec)');
define($constpref.'_BRCACHE','Durata della cache per le immagini (solo per il modo wraps)');
define($constpref.'_BRCACHEDSC','Tutti i files escluso gli HTML saranno messi in cache dal web browser in questo secondo (0 significa disabilitato)');
define($constpref.'_COM_DIRNAME','Comment-integration: Cartella del d3forum');
define($constpref.'_COM_FORUM_ID','Comment-integration: forum ID');

// blocks
define($constpref.'_BNAME_MENU','Menu');
define($constpref.'_BNAME_CONTENT','Contenuto');
define($constpref.'_BNAME_LIST','Lista');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', 'global');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notifica circa questo modulo');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', 'in attesa');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifica se nuovi posts o modifiche in attesa di approvazione (Notifica agli amministratori o moderatori)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: in attesa');

}


?>
