<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {







// Appended by Xoops Language Checker -GIJOE- in 2009-01-18 18:29:25
define($constpref.'_COM_ORDER','Order of comment-integration');
define($constpref.'_COM_POSTSNUM','Max posts displayed in comment-integration');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-02 16:22:09
define($constpref.'_AUTOREGISTCLASS','class name to register/unregister HTML wrapped files');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-19 04:29:56
define($constpref.'_ADMENU_TAGS','Tags');

// Appended by Xoops Language Checker -GIJOE- in 2008-10-01 12:11:22
define($constpref.'_URIM_CLASS','class mapping URI');
define($constpref.'_URIM_CLASSDSC','Change it if you want to override the URI mapper. The default value is PicoUriMapper');

// Appended by Xoops Language Checker -GIJOE- in 2008-09-07 05:14:32
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

// Appended by Xoops Language Checker -GIJOE- in 2008-04-23 04:51:13
define($constpref.'_ALLOWEACHHEAD','specify HTML headers for each contents');
define($constpref.'_BNAME_TAGS','Tags');

define($constpref.'_ADMENU_EXTRAS','Extra');
define($constpref.'_HTMLPR_EXCEPT','Les groupes qui peuvent éviter la correction par HTMLPurifier');
define($constpref.'_HTMLPR_EXCEPTDSC','Les publications des utilisateurs qui n\'appartiennent pas à ces groupes seront
forcément corrigés et HTML filtré par HTMLPurifier dans Protector>=3.14. Cette correction ne peut pas fonctionner avec PHP4');
define($constpref.'_BNAME_MYWAITINGS','Mes publications en attente');
define($constpref.'_BNAME_SUBCATEGORIES','Sous-catégories');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT','nouveau contenu');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP','Notifier si une nouvelle publication a lieu. (contenu approuvé seulement)');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ','[{X_SITENAME}] {X_MODULE} : Nouvelle Publication');
define($constpref.'_COM_VIEW','Affichage des Commentaires-Intégrés');
define($constpref.'_ADMENU_MYLANGADMIN','Langages');
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocs/Permissions');
define($constpref.'_ADMENU_MYPREFERENCES','Préférences');
define($constpref.'_SEARCHBYUID','Activer le concept d\'auteur');
define($constpref.'_SEARCHBYUIDDSC','Les publications seront listées dans le profil utilisateur de l\'auteur. Si vous utilisez ce module pour publier du contenu statique, désactivez cette option.');
define($constpref.'_HISTORY_P_C','Combien de revisions sauver dans la BDD');
define($constpref.'_MLT_HISTORY','Temps minimum pour chaque revision (sec)');
define($constpref.'_BRCACHE','Temps du Cache pour les fichiers images (seulement avec le mode wraps)');
define($constpref.'_BRCACHEDSC','Temps de Cache pour le navigateur, en secondes, pour les fichiers autres que HTML (0 pour désactiver)');
define($constpref.'_SUBMENU_SC','Afficher le contenu dans le sous-menu');
define($constpref.'_SUBMENU_SCDSC','Par défaut, uniquement les Catégories seront affichées. Si vous activez cette option, le contenu marqué "menu" sera également affiché');
define($constpref.'_SITEMAP_SC','Afficher le contenu dans le module sitemap');
define($constpref.'_USE_REWRITE','Activer mod_rewrite');
define($constpref.'_USE_REWRITEDSC','Dépend de votre environnement. Si vous activez cette option, renomez .htaccess.rewrite_wraps(avec wraps) ou htaccess.rewrite_normal(sans wraps) en .htaccess dans XOOPS_ROOT_PATH/modules/(repértoire)/');
define($constpref.'_FILTERSF','Filtres Forcés');
define($constpref.'_FILTERSFDSC','ajouter les noms des filtres séparés par ,(virgule). filter:LAST signifie que le filtre est exécuté en dernier. Les autres filtres seront executés dans une premier temps.');
define($constpref.'_FILTERSP','Filtres Interdits');
define($constpref.'_FILTERSPDSC','ajoutez les noms des filtres séparés par,(virgule).');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","un module pour gérer du contenu statique");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'Liste de Contenu' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'Permissions des Catégories' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'Importer/Synchroniser' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','Activer le mode Insértion (wraps)');
define($constpref.'_WRAPSAUTOREGIST','Activer l\'auto-enregistrement des fichiers HTML insérés dans la BDD comme contenu');
define($constpref.'_TOP_MESSAGE','Description de la catégorie TOP');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','Afficher menu(index) en tête de ce module');
define($constpref.'_LISTASINDEX',"Afficher l\'index de contenu dans l\'en tête des catégories");
define($constpref.'_LISTASINDEXDSC','OUI signifie que la liste est faite automatique et s\'affiche au dessus de la
catégorie. NON signifie que le contenu avec la plus haute priorité est affiché au lieu de la liste automatique');
define($constpref.'_SHOW_BREADCRUMBS','Afficher les sommaires');
define($constpref.'_SHOW_PAGENAVI','Afficher la page navigation');
define($constpref.'_SHOW_PRINTICON','Afficher l\'icone du format imprimable');
define($constpref.'_SHOW_TELLAFRIEND','Afficher l\'icone pour informer un ami');
define($constpref.'_USE_TAFMODULE','Utiliser le module "tellafriend"');
define($constpref.'_FILTERS','Série de filtres par défaut');
define($constpref.'_FILTERSDSC','Ajoutez les noms des filtres séparés par | ');
define($constpref.'_FILTERSDEFAULT','xcode|smiley|nl2br');
define($constpref.'_USE_VOTE','Activer l\'option de VOTE');
define($constpref.'_GUESTVOTE_IVL','Vote des visiteurs');
define($constpref.'_GUESTVOTE_IVLDSC','Ajoutez 0, pour désactiver le vote des visiteurs anonymes. Autrement ajoutez un nombre équivalent au temps (sec.) pour permettre un deuxième vote depuis le même IP.');
define($constpref.'_HTMLHEADER','En tête HTML commun');
define($constpref.'_CSS_URI','URI du fichier CSS pour ce module');
define($constpref.'_CSS_URIDSC','Vous pouvez indiquer une adresse relative ou absolue. Par défaut: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','Dossier pour les fichiers images');
define($constpref.'_IMAGES_DIRDSC','Vous pouvez indiquer une adresse relative ou absolue. Par défaut: images');
define($constpref.'_BODY_EDITOR','Editeur du document');
define($constpref.'_COM_DIRNAME','Intégration-commentaires: nom du repértoire de d3forum');
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
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifier si de nouveaux messages ou modifications sont attente (notifier seulement les administrateurs ou les modérateurs)');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: en attente');

}


?>
