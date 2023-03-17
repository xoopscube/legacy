<?php
#@ gigamaster 

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
    $mydirname = 'pico';
}
$constpref = '_MI_' . strtoupper( $mydirname );

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED' ) ) {

    define( $constpref . '_COM_ORDER', 'Ordre des commentaires-intégrés' );
    define( $constpref . '_COM_POSTSNUM', 'Nombre maximum de commentaires-intégrés affichés' );
    define( $constpref . '_AUTOREGISTCLASS', 'Nom de la classe pour ajouter/extraire les fichiers HTML intégrés (wrap)' );
    define( $constpref . '_ADMENU_TAGS', 'Etiquettes - mots clés (Tags)' );
    define( $constpref . '_URIM_CLASS', 'URI de mappage de classe' );
    define( $constpref . '_URIM_CLASSDSC', 'Modifiez les templates si vous souhaitez remplacer le mappeur. La valeur par défaut est PicoUriMapper' );

    define( $constpref . '_EF_CLASS', 'Classe pour identifier les champs supplémentaires (extra_fields)' );
    define( $constpref . '_ERR_DOCUMENT_404', 'Rediriger vers la page personnalisée - 404 Error, Page Not Found.' );
    define( $constpref . '_ERR_DOCUMENT_404DSC', 'Le serveur Web renvoie le message - Erreur HTTP 404 Fichier introuvable - lorsque la page demandée est introuvable.<br>Cette option peut remplacer une erreur par ex. avec "' . XOOPS_ROOT_PATH . '"/404.html' );
    define( $constpref . '_EF_CLASSDSC', 'Change it if you want to override the handler for extra_fields. default value is PicoExtraFields' );
    define( $constpref . '_EFIMAGES_DIR', 'Répertoire pour les champs supplémentaires (extra_fields)' );
    define( $constpref . '_EFIMAGES_DIRDSC', 'set relative path from XOOPS_ROOT_PATH. Create and chmod 777 the directory first. default) uploads/(module dirname)' );
    define( $constpref . '_EFIMAGES_SIZE', 'Extra images generated from the upload file' );
    define( $constpref . '_EFIMAGES_SIZEDSC', 'Used in article header and theme Open Graph. Default values in pixels, width x height : 1200x627 820x312 640x360' );
    define( $constpref . '_EFIMAGES_QUALITY', 'Extra Image quality compression' );
    define( $constpref . '_EFIMAGES_QUALITYDSC', 'minimize' );
    define( $constpref . '_IMAGICK_PATH', 'Chemin pour ImageMagick binaries' );
    define( $constpref . '_IMAGICK_PATHDSC', 'Laisser vide normal, ou définissez par ex. /usr/X11R6/bin/' );
    define( $constpref . '_NOTCAT_CATEGORY', 'catégorie' );
    define( $constpref . '_NOTCAT_CATEGORYDSC', 'notifications dans cette catégorie' );
    define( $constpref . '_NOTCAT_CONTENT', 'contenu' );
    define( $constpref . '_NOTCAT_CONTENTDSC', 'notifications sur ce contenu' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENT', 'Nouveau contenu' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTCAP', 'Notifier si un nouveau contenu est enregistré. (contenu approuvé uniquement)' );
    define( $constpref . '_NOTIFY_CATEGORY_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} un nouveau contenu {CONTENT_SUBJECT}' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENT', 'nouveau commentaire' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTCAP', 'Notifier si un nouveau commentaire est publié. (commentaires approuvés uniquement)' );
    define( $constpref . '_NOTIFY_CONTENT_COMMENTSBJ', '[{X_SITENAME}] {X_MODULE} : un nouveau commentaire' );

    define( $constpref . '_ALLOWEACHHEAD', 'Spécifier des en-têtes HTML pour chaque contenu' );
    define( $constpref . '_BNAME_TAGS', 'Tags' );

    define( $constpref . '_ADMENU_EXTRAS', 'Extra' );
    define( $constpref . '_HTMLPR_EXCEPT', 'Les groupes qui peuvent éviter la correction par HTMLPurifier' );
    define( $constpref . '_HTMLPR_EXCEPTDSC', 'Les publications des utilisateurs qui ne sont pas dans les groupes cibles seront filtrées par HTMLPurifier et le module Protector.' );
    define( $constpref . '_BNAME_MYWAITINGS', 'Mes publications en attente' );
    define( $constpref . '_BNAME_SUBCATEGORIES', 'Sous-catégories' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENT', 'Nouveau contenu' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTCAP', 'Notifier si une nouvelle publication a lieu. (contenu approuvé seulement)' );
    define( $constpref . '_NOTIFY_GLOBAL_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : Nouvelle Publication' );
    define( $constpref . '_COM_VIEW', 'Affichage des Commentaires-Intégrés' );
    define( $constpref . '_ADMENU_MYLANGADMIN', 'Langages' );
    define( $constpref . '_ADMENU_MYTPLSADMIN', 'Templates' );
    define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Blocs/Permissions' );
    define( $constpref . '_ADMENU_MYPREFERENCES', 'Préférences' );
    define( $constpref . '_SEARCHBYUID', 'Activer la fonction auteur' );
    define( $constpref . '_SEARCHBYUIDDSC', 'Les publications seront listées dans le profil utilisateur de l\'auteur. Si vous utilisez ce module pour publier du contenu statique, désactivez cette option.' );
    define( $constpref . '_HISTORY_P_C', 'Nombre de revisions du document à sauvegarder dans la BDD' );
    define( $constpref . '_MLT_HISTORY', 'Temps minimum pour chaque revision (sec)' );
    define( $constpref . '_BRCACHE', 'Temps du Cache pour les fichiers images (seulement avec le mode wraps)' );
    define( $constpref . '_BRCACHEDSC', 'Temps de Cache pour le navigateur, en secondes, pour les fichiers autres que HTML (0 pour désactiver)' );
    define( $constpref . '_SUBMENU_SC', 'Afficher le contenu dans le sous-menu' );
    define( $constpref . '_SUBMENU_SCDSC', 'Par défaut, uniquement les Catégories seront affichées. Si vous activez cette option, le contenu marqué "menu" sera également affiché' );
    define( $constpref . '_SITEMAP_SC', 'Afficher le contenu dans le module Sitemap' );
    define( $constpref . '_USE_REWRITE', 'Activer mod_rewrite' );
    define( $constpref . '_USE_REWRITEDSC', 'Dépend de votre environnement. Si vous activez cette option, renomez .htaccess.rewrite_wraps (avec wraps) ou htaccess.rewrite_normal (sans wraps) en .htaccess dans XOOPS_ROOT_PATH/modules/(repértoire)/' );
    define( $constpref . '_FILTERSF', 'Filtres Forcés' );
    define( $constpref . '_FILTERSFDSC', 'ajouter les noms des filtres séparés par une virgule (,).<br> filter:LAST - signifie que le filtre est exécuté en dernier. Les autres filtres seront executés dans une premier temps.' );
    define( $constpref . '_FILTERSP', 'Filtres Interdits' );
    define( $constpref . '_FILTERSPDSC', 'ajoutez les noms des filtres séparés par une virgule.' );

    define( $constpref . '_LOADED', 1 );

// The name of this module
    define( $constpref . "_NAME", "pico" );

// A brief description of this module
    define( $constpref . "_DESC", "un module pour créer et gérer du contenu statique" );

// admin menus
    define( $constpref . '_ADMENU_CONTENTSADMIN', 'Liste de Contenu' );
    define( $constpref . '_ADMENU_CATEGORYACCESS', 'Permissions des Catégories' );
    define( $constpref . '_ADMENU_IMPORT', 'Importer/Synchroniser' );

// configurations
    define( $constpref . '_USE_WRAPSMODE', 'Activer le mode insértion des documents dans le dossier (wraps).' );
    define( $constpref . '_WRAPSAUTOREGIST', 'Activer la fonction "auto-enregistrement" des fichiers HTML insérés dans la BDD comme contenu' );
    define( $constpref . '_TOP_MESSAGE', 'Description de la catégorie TOP' );
    define( $constpref . '_TOP_MESSAGEDEFAULT', '' );
    define( $constpref . '_MENUINMODULETOP', 'Afficher un menu (index) en tête de ce module' );
    define( $constpref . '_LISTASINDEX', "Afficher un index de contenu en tête des catégories" );
    define( $constpref . '_LISTASINDEXDSC', 'OUI - affiche une liste automatique au dessus de la catégorie.<br>NON - affiche le contenu (page) avec la plus haute priorité au lieu de la liste automatique' );
    define( $constpref . '_SHOW_BREADCRUMBS', 'Afficher les sommaires' );
    define( $constpref . '_SHOW_RSS', 'Afficher lien RSS' );
    define( $constpref . '_SHOW_PAGENAVI', 'Afficher la page navigation' );
    define( $constpref . '_SHOW_PRINTICON', 'Afficher une icône pour le format imprimable' );
    define( $constpref . '_SHOW_TELLAFRIEND', 'Afficher une icône pour informer un ami' );
    define( $constpref . '_USE_TAFMODULE', 'Utiliser le module "tellafriend"' );
    define( $constpref . '_FILTERS', 'Série de filtres par défaut' );
    define( $constpref . '_FILTERSDSC', 'Ajoutez les noms des filtres séparés par | ' );
    define( $constpref . '_FILTERSDEFAULT', 'xcode|smiley|nl2br' );
    define( $constpref . '_USE_VOTE', 'Activer la fonction de VOTE' );
    define( $constpref . '_GUESTVOTE_IVL', 'Vote des visiteurs' );
    define( $constpref . '_GUESTVOTE_IVLDSC', 'Ajoutez 0, pour désactiver le vote des visiteurs anonymes. Autrement ajoutez un nombre équivalent au temps (sec.) pour permettre un deuxième vote depuis la même adresse IP.' );
    define( $constpref . '_HTMLHEADER', 'En tête HTML commun' );
    define( $constpref . '_CSS_URI', 'URI du fichier CSS pour ce module' );
    define( $constpref . '_CSS_URIDSC', 'Vous pouvez indiquer une adresse relative ou absolue. Par défaut: {mod_url}/index.php?page=main_css' );
    define( $constpref . '_IMAGES_DIR', 'Dossier pour les fichiers images' );
    define( $constpref . '_IMAGES_DIRDSC', 'Vous pouvez indiquer une adresse relative ou absolue. Par défaut: images' );
    define( $constpref . '_BODY_EDITOR', 'Editeur du document' );
    define( $constpref . '_COM_DIRNAME', 'Intégration-commentaires: nom du repértoire de d3forum' );
    define( $constpref . '_COM_FORUM_ID', 'Intégration-commentaires: ID forum' );

// blocks
    define( $constpref . '_BNAME_MENU', 'Menu' );
    define( $constpref . '_BNAME_CONTENT', 'Contenu' );
    define( $constpref . '_BNAME_LIST', 'Liste' );

// Notify Categories
    define( $constpref . '_NOTCAT_GLOBAL', 'global' );
    define( $constpref . '_NOTCAT_GLOBALDSC', 'notifications de ce module' );

// Each Notifications
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENT', 'Contenu en attente' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'Notifier si de nouveaux messages ou modifications sont attente (notifier seulement les administrateurs ou les modérateurs)' );
    define( $constpref . '_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}: en attente' );

}
