<?php
#@ gigamaster 

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
    $mydirname = 'pico';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {

    define( $constpref . '_LOADED', 1 );

    // The name of this module
    define( $constpref . '_NAME', 'pico' );

    // A brief description of this module
    define( $constpref . '_DESC', 'Content management with CKEditor, versioning, revision history, diff and granular permissions.' );

    // admin menus
    define( $constpref . '_ADMENU_CONTENTSADMIN', 'Liste de Contenu' );
    define( $constpref . '_ADMENU_CATEGORYACCESS', 'Catégories' );
    define( $constpref . '_ADMENU_IMPORT', 'Importer/Synchroniser' );
    define( $constpref . '_ADMENU_TAGS', 'Tags' );
    define( $constpref . '_ADMENU_EXTRAS', 'Extra Forms' );
    define( $constpref . '_ADMENU_MYLANGADMIN', 'Language' );
    define( $constpref . '_ADMENU_MYTPLSADMIN', 'Templates' );
    define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Blocs/Permissions' );
    define( $constpref . '_ADMENU_MYPREFERENCES', 'Préférences' );

    // configurations
    define( $constpref . '_USE_WRAPSMODE', 'Activer le mode insértion des documents dans le dossier (wraps).' );
    define( $constpref . '_ERR_DOCUMENT_404', 'Custom page for error : 404 Not Found ' );
    define( $constpref . '_ERR_DOCUMENT_404DSC', 'Example: <b>root_path/404.html</b> redirects to public root path <br><i>' . XOOPS_ROOT_PATH . '/404.html</i> <br><b>trust_path/404.html</b> redirects to trust path<br><i>' . XOOPS_TRUST_PATH . '/404.html</i>' );
    define( $constpref . '_USE_REWRITE', 'Activer mod_rewrite' );
    define( $constpref . '_USE_REWRITEDSC', 'Dépend de votre environnement. Si vous activez cette option, renomez .htaccess.rewrite_wraps (avec wraps) ou htaccess.rewrite_normal (sans wraps) en .htaccess dans XOOPS_ROOT_PATH/modules/(repértoire)/' );
    define( $constpref . '_WRAPSAUTOREGIST', 'Activer la fonction "auto-enregistrement" des fichiers HTML insérés dans la BDD comme contenu' );
    define( $constpref . '_AUTOREGISTCLASS', 'Class name to register/unregister HTML wrapped files' );
    define( $constpref . '_TOP_MESSAGE', 'Description de la catégorie TOP' );
    define( $constpref . '_TOP_MESSAGEDEFAULT', '' );
    define( $constpref . '_MENUINMODULETOP', 'Display menu (index) in the top of this module' );
    define( $constpref . '_LISTASINDEX', "Display table of contents (TOC) or custom page" );
    define( $constpref . '_LISTASINDEXDSC', 'YES - a table of contents (TOC) is auto-generated and displayed on the main page.<br> NO - the content with the highest priority (order, weight) is displayed instead of TOC.' );
    define( $constpref . '_SHOW_BREADCRUMBS', 'Enable breadcrumbs' );
    define( $constpref . '_SHOW_RSS', 'Enable RSS' );
    define( $constpref . '_SHOW_PAGENAVI', 'Enable page navigation' );
    define( $constpref . '_SHOW_PRINTICON', 'Enable printer friendly icon' );
    define( $constpref . '_SHOW_TELLAFRIEND', 'Enable tell a friend icon' );
    define( $constpref . '_SEARCHBYUID', 'Activer la fonction auteur' );
    define( $constpref . '_SEARCHBYUIDDSC', 'Les publications seront listées dans le profil utilisateur de l\'auteur. Si vous utilisez ce module pour publier du contenu statique, désactivez cette option.' );
    define( $constpref . '_USE_TAFMODULE', 'Use the module "tellafriend".<br>Please refer to X-Update Manager for download and deploy.' );
    define( $constpref . '_FILTERS', 'Default filter set' );
    define( $constpref . '_FILTERSDSC', 'Specify filter names separated by pipe "|". Example : xcode|smiley|nl2br|textwiki' );
    define( $constpref . '_FILTERSDEFAULT', '' );
    define( $constpref . '_FILTERSF', 'Filtres Forcés' );
    define( $constpref . '_FILTERSFDSC', 'ajouter les noms des filtres séparés par une virgule (,).<br> filter:LAST - signifie que le filtre est exécuté en dernier. Les autres filtres seront executés dans une premier temps.' );
    define( $constpref . '_FILTERSP', 'Filtres Interdits' );
    define( $constpref . '_FILTERSPDSC', 'ajoutez les noms des filtres séparés par une virgule.' );
    define( $constpref . '_SUBMENU_SC', 'Afficher le contenu dans le sous-menu' );
    define( $constpref . '_SUBMENU_SCDSC', 'Par défaut, uniquement les Catégories seront affichées. Si vous activez cette option, le contenu marqué "menu" sera également affiché' );
    define( $constpref . '_SITEMAP_SC', 'Afficher le contenu dans le module Sitemap' );
    define( $constpref . '_USE_VOTE', 'Enable Voting feature' );
    define( $constpref . '_GUESTVOTE_IVL', 'Enable Voting from guests' );
    define( $constpref . '_GUESTVOTE_IVLDSC', 'Allow votes from the same IP with required delay in seconds. Disable with value set to 0. Default value: 86400' );
    define( $constpref . '_HTMLHEADER', 'Common HTML header [ CSS, JS ]' );
    define( $constpref . '_ALLOWEACHHEAD', 'Spécifier des en-têtes HTML pour chaque contenu' );
    define( $constpref . '_CSS_URI', 'Fichier CSS pour ce module' );
    define( $constpref . '_CSS_URIDSC', 'Relative or absolute path can be defined. Default value : {mod_url}/index.php?page=main_css' );
    define( $constpref . '_IMAGES_DIR', 'Directory for image files' );
    define( $constpref . '_IMAGES_DIRDSC', 'Relative path to module in the public directory e.g. dirname/images. Default value : images' );
    define( $constpref . '_BODY_EDITOR', 'WYSIWYG HTML editor to simplify content creation' );
    define( $constpref . '_HTMLPR_EXCEPT', 'Les groupes qui peuvent éviter la correction par HTMLPurifier' );
    define( $constpref . '_HTMLPR_EXCEPTDSC', 'Les publications des utilisateurs qui ne sont pas dans les groupes cibles seront filtrées par HTMLPurifier et le module Protector.' );
    define( $constpref . '_HISTORY_P_C', 'Nombre de revisions du document à sauvegarder dans la BDD' );
    define( $constpref . '_MLT_HISTORY', 'Temps minimum pour chaque revision (sec)' );
    define( $constpref . '_BRCACHE', 'Temps du Cache pour les fichiers images (seulement avec le mode wraps)' );
    define( $constpref . '_BRCACHEDSC', 'Temps de Cache pour le navigateur, en secondes, pour les fichiers autres que HTML (0 pour désactiver)' );
    define( $constpref . '_EF_CLASS', 'Class for extra_fields' );
    define( $constpref . '_EF_CLASSDSC', 'Developers can override the class name and method name parameters of the handler for extra_fields. The default value is PicoExtraFields' );
    define( $constpref . '_URIM_CLASS', 'Class mapping URI' );
    define( $constpref . '_URIM_CLASSDSC', 'Developers can override the URI mapper. The default value is PicoUriMapper' );
    define( $constpref . '_EFIMAGES_DIR', 'Directory for extra_fields' );
    define( $constpref . '_EFIMAGES_DIRDSC', 'Relative path to the public directory e.g. public_html/.<br>First, create and chmod 777 the directory. Default value: uploads/dirname' );
    define( $constpref . '_EFIMAGES_SIZE', 'Extra images generated from the upload file' );
    define( $constpref . '_EFIMAGES_SIZEDSC', 'Used in article header and theme Open Graph. Default values in pixels, width x height : 1200x627 820x312 640x360' );
    define( $constpref . '_EFIMAGES_QUALITY', 'Extra Image quality compression' );
    define( $constpref . '_EFIMAGES_QUALITYDSC', 'minimize' );
    define( $constpref . '_IMAGICK_PATH', 'Chemin pour ImageMagick binaries' );
    define( $constpref . '_IMAGICK_PATHDSC', 'Laisser vide normal, ou définissez par ex. /usr/X11R6/bin/' );
    define( $constpref . '_COM_DIRNAME', 'Comment-integration: dirname of d3forum' );
    define( $constpref . '_COM_FORUM_ID', 'Comment-integration: forum ID' );
    define( $constpref . '_COM_VIEW', 'Affichage des Commentaires-Intégrés' );
    define( $constpref . '_COM_ORDER', 'Comment-integration : Order' );
    define( $constpref . '_COM_POSTSNUM', 'Comment-integration : Maximum number of comments per page' );
    
    // blocks
    define( $constpref . '_BNAME_MENU', 'Menu' );
    define( $constpref . '_BNAME_CONTENT', 'Contenu' );
    define( $constpref . '_BNAME_LIST', 'Liste' );
    define( $constpref . '_BNAME_SUBCATEGORIES', 'Sous-catégories' );
    define( $constpref . '_BNAME_MYWAITINGS', 'Mes publications en attente' );
    define( $constpref . '_BNAME_TAGS', 'Tags' );

    // Notify Categories
    define( $constpref . '_NOTCAT_GLOBAL', 'global' );
    define( $constpref . '_NOTCAT_GLOBALDSC', 'notifications de ce module' );
    define( $constpref . '_NOTCAT_CATEGORY', 'catégorie' );
    define( $constpref . '_NOTCAT_CATEGORYDSC', 'notifications dans cette catégorie' );
    define( $constpref . '_NOTCAT_CONTENT', 'contenu' );
    define( $constpref . '_NOTCAT_CONTENTDSC', 'notifications sur ce contenu' );
    
    // Each Notifications
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENT', 'Contenu en attente' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifier si de nouveaux messages ou modifications sont attente (notifier seulement les administrateurs ou les modérateurs)' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: en attente' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENT', 'Nouveau contenu' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTCAP', 'Notifier si une nouvelle publication a lieu. (contenu approuvé seulement)' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : Nouvelle Publication' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENT', 'Nouveau contenu' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTCAP', 'Notifier si un nouveau contenu est enregistré. (contenu approuvé uniquement)' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} un nouveau contenu {CONTENT_SUBJECT}' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENT', 'nouveau commentaire' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTCAP', 'Notifier si un nouveau commentaire est publié. (commentaires approuvés uniquement)' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTSBJ', '[{X_SITENAME}] {X_MODULE} : un nouveau commentaire' );
}
