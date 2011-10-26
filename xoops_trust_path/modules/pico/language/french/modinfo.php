<?php

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

// Appended by Xoops Language Checker -GIJOE- in 2007-03-26 11:38:36
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define($constpref.'_ADMENU_MYPREFERENCES','Preferences');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-23 05:52:08
define($constpref.'_SEARCHBYUID','Enable concepts of poster');
define($constpref.'_SEARCHBYUIDDSC','Contents will be listed in user profile of its poster. If you use this module as static contents, turn this off.');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-13 04:23:23
define($constpref.'_HISTORY_P_C','How many revisions are stored in DB');
define($constpref.'_MLT_HISTORY','Minimum lifetime of each revisions (sec)');
define($constpref.'_BRCACHE','Cache life time for image files (only with wraps mode)');
define($constpref.'_BRCACHEDSC','Files other than HTML will be cached by web browser in this second (0 means disabled)');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-10 07:13:28
define($constpref.'_SUBMENU_SC','Show contents in submenu');
define($constpref.'_SUBMENU_SCDSC','Only categories are displayed in default. If you turn this on, contents marked "menu" will be displayed also');
define($constpref.'_SITEMAP_SC','Show contents in sitemap module');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-07 04:40:00
define($constpref.'_USE_REWRITE','enable mod_rewrite mode');
define($constpref.'_USE_REWRITEDSC','Depends your environment. If you turn this on, rename .htaccess.rewrite_wraps(with wraps) or htaccess.rewrite_normal(without wraps) to .htaccess under XOOPS_ROOT_PATH/modules/(dirname)/');

// Appended by Xoops Language Checker -GIJOE- in 2007-03-06 04:56:32
define($constpref.'_FILTERSF','Forced filters');
define($constpref.'_FILTERSFDSC','input filter names separated with ,(comma). filter:LAST means the filter is passed in the last phase. The other filters are passed in the first phase.');
define($constpref.'_FILTERSP','Prohibited filters');
define($constpref.'_FILTERSPDSC','input filter names separated with ,(comma).');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","un module pour gérer contenu statique");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'Liste de Contentu' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'Permissions des Catégories' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'Importer/Synchroniser' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','Activer le mode Insértion (wraps)');
define($constpref.'_WRAPSAUTOREGIST','Activer l\'auto-enregistrement des fichiers HTML insérés dans la BD comme contenu');
define($constpref.'_TOP_MESSAGE','Description de la catégorie TOP');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','Afficher menu(index) en tête de ce module');
define($constpref.'_LISTASINDEX',"Afficher l\'index de contenu dans l\'en tête des catégories");
define($constpref.'_LISTASINDEXDSC','OUI signifie que la liste est faite automatique et s\'affiche au dessus de la
catégorie. NON signifie que le contenu avec la plushaute prioroté est affiché au lieu de la liste automatique');
define($constpref.'_SHOW_BREADCRUMBS','Afficher résumés');
define($constpref.'_SHOW_PAGENAVI','Afficher page navigation');
define($constpref.'_SHOW_PRINTICON','Afficher l\'icone du format imprimable');
define($constpref.'_SHOW_TELLAFRIEND','Afficher l\'icone pour informer un ami');
define($constpref.'_USE_TAFMODULE','Utiliser le module "tellafriend"');
define($constpref.'_FILTERS','Série de filtres par défaut');
define($constpref.'_FILTERSDSC','Ajoutez les noms des filtres séparés par | ');
define($constpref.'_FILTERSDEFAULT','xcode|smiley|nl2br');
define($constpref.'_USE_VOTE','Activer l\'option de VOTE');
define($constpref.'_GUESTVOTE_IVL','Vote des visiteurs');
define($constpref.'_GUESTVOTE_IVLDSC','Ajoutez 0, pour désactiver le vote des visiteurs anonymes. Autrement ajoutez un nombre équivalent au temps (sec.) pour permettre un second vote de même IP.');
define($constpref.'_HTMLHEADER','En tête HTML commun');
define($constpref.'_CSS_URI','URI du fichier CSS pour ce module');
define($constpref.'_CSS_URIDSC','Vous pouvez indiquer une adresse relative ou absolue. Par défaut: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','Dossier pour les fichiers images');
define($constpref.'_IMAGES_DIRDSC','Vous pouvez indiquer une adresse relative ou absolue. Par défaut: images');
define($constpref.'_BODY_EDITOR','Editeur du document');
define($constpref.'_COM_DIRNAME','Intégration-commentaires: nom du dossier de d3forum');
define($constpref.'_COM_FORUM_ID','Intégration-commentaires: ID forum');

// blocks
define($constpref.'_BNAME_MENU','Menu');
define($constpref.'_BNAME_CONTENT','Contenu');
define($constpref.'_BNAME_LIST','Liste');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', 'global');
define($constpref.'_NOTCAT_GLOBALDSC', 'notifications de ce module');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', 'Contenu en attente');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifier si de nouveaux messages ou modifications sont attente   (Juste notifier aux administrations ou aux modérateurs)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: en attente');

}


?>
