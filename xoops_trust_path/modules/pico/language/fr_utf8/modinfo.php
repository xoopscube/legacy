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
    define( $constpref . '_ADMENU_ACTIVITY', 'Activité' );
    define( $constpref . '_ADMENU_CONTENTSADMIN', 'Liste de Contenu' );
    define( $constpref . '_ADMENU_CATEGORYACCESS', 'Catégories' );
    define( $constpref . '_ADMENU_IMPORT', 'Importer/Sync' );
    define( $constpref . '_ADMENU_TAGS', 'Tags' );
    define( $constpref . '_ADMENU_EXTRAS', 'Extra Forms' );
    define( $constpref . '_ADMENU_MYLANGADMIN', 'Language' );
    define( $constpref . '_ADMENU_MYTPLSADMIN', 'Templates' );
    define( $constpref . '_ADMENU_MYBLOCKSADMIN', 'Blocs/Permissions' );
    define( $constpref . '_ADMENU_MYPREFERENCES', 'Préférences' );

    // configurations
    define( $constpref . '_USE_WRAPSMODE', 'Activer le mode insértion des documents dans le dossier (wraps).' );
    define( $constpref . '_ERR_DOCUMENT_404', 'Page personnalisée pour erreur : 404 Not Found ' );
    define( $constpref . '_ERR_DOCUMENT_404DSC', 'Example: <b>root_path/404.html</b> redirige vers le chemin racine public <br><i>' . XOOPS_ROOT_PATH . '/404.html</i> <br><b>trust_path/404.html</b> redirige vers le chemin Trust Path<br><i>' . XOOPS_TRUST_PATH . '/404.html</i>' );
    define( $constpref . '_USE_REWRITE', 'Activer mod_rewrite' );
    define( $constpref . '_USE_REWRITEDSC', 'Dépend de votre environnement. Si vous activez cette option, renomez .htaccess.rewrite_wraps (avec wraps) ou htaccess.rewrite_normal (sans wraps) en .htaccess dans XOOPS_ROOT_PATH/modules/(repértoire)/' );
    define( $constpref . '_WRAPSAUTOREGIST', 'Activer la fonction "auto-enregistrement" des fichiers HTML insérés dans la BDD comme contenu' );
    define( $constpref . '_AUTOREGISTCLASS', 'Nom de la classe pour enregistrer/désenregistrer les fichiers encapsulés HTML' );
    define( $constpref . '_TOP_MESSAGE', 'Description de la catégorie TOP' );
    define( $constpref . '_TOP_MESSAGEDEFAULT', '' );
    define( $constpref . '_MENUINMODULETOP', 'Afficher ke Menu (index) comme page de ce module' );
    define( $constpref . '_LISTASINDEX', "Afficher la table des matières (TOC) ou une page personnalisée" );
    define( $constpref . '_LISTASINDEXDSC', 'OUI - une table des matières (TOC) est générée automatiquement et affichée sur la page principale.<br> NON - le contenu ayant la priorité la plus élevée (ordre, poids) est affiché à la place de la table des matières.' );
    define( $constpref . '_SHOW_BREADCRUMBS', 'Activer le fil RSS' );
    define( $constpref . '_SHOW_RSS', 'Activer RSS' );
    define( $constpref . '_SHOW_PAGENAVI', 'Activer la navigation dans les pages' );
    define( $constpref . '_SHOW_PRINTICON', "Activer l'icône d'impression" );
    define( $constpref . '_SHOW_TELLAFRIEND', "Activer l'icône Parler à un ami" );
    define( $constpref . '_SEARCHBYUID', "Activer la fonction auteur" );
    define( $constpref . '_SEARCHBYUIDDSC', 'Les publications seront listées dans le profil utilisateur de l\'auteur. Si vous utilisez ce module pour publier du contenu statique, désactivez cette option.' );
    define( $constpref . '_USE_TAFMODULE', 'Utilisez le module "tellafriend".<br>Veuillez vous référer à X-Update Manager pour le téléchargement et le déploiement.' );
    define( $constpref . '_FILTERS', 'Ensemble de filtres par défaut' );
    define( $constpref . '_FILTERSDSC', 'Spécifiez les noms de filtres séparés par une barre verticale "|". Exemple : xcode|smiley|nl2br|textwiki' );
    define( $constpref . '_FILTERSDEFAULT', '' );
    define( $constpref . '_FILTERSF', 'Filtres Forcés' );
    define( $constpref . '_FILTERSFDSC', 'ajouter les noms des filtres séparés par une virgule (,).<br> filter:LAST - signifie que le filtre est exécuté en dernier. Les autres filtres seront executés dans une premier temps.' );
    define( $constpref . '_FILTERSP', 'Filtres Interdits' );
    define( $constpref . '_FILTERSPDSC', 'ajoutez les noms des filtres séparés par une virgule.' );
    define( $constpref . '_SUBMENU_SC', 'Afficher le contenu dans le sous-menu' );
    define( $constpref . '_SUBMENU_SCDSC', 'Par défaut, uniquement les Catégories seront affichées. Si vous activez cette option, le contenu marqué "menu" sera également affiché' );
    define( $constpref . '_SITEMAP_SC', 'Afficher le contenu dans le module Sitemap' );
    define( $constpref . '_USE_VOTE', 'Activer la fonctionnalité de vote' );
    define( $constpref . '_GUESTVOTE_IVL', 'Activer le vote des invités' );
    define( $constpref . '_GUESTVOTE_IVLDSC', 'Autorisez les votes à partir de la même adresse IP avec le délai requis en secondes. Désactiver avec la valeur définie sur 0. Valeur par défaut : 86400' );
    define( $constpref . '_HTMLHEADER', 'En-tête HTML commun [ CSS, JS ]' );
    define( $constpref . '_ALLOWEACHHEAD', 'Spécifier des en-têtes HTML pour chaque contenu' );
    define( $constpref . '_CSS_URI', 'Fichier CSS pour ce module' );
    define( $constpref . '_CSS_URIDSC', 'Un chemin relatif ou absolu peut être défini. Valeur par défaut: {mod_url}/index.php?page=main_css' );
    define( $constpref . '_IMAGES_DIR', 'Répertoire des fichiers images' );
    define( $constpref . '_IMAGES_DIRDSC', 'Chemin relatif vers le module dans le répertoire public, par ex. répertoire/images. Valeur par défaut : IMAGES' );
    define( $constpref . '_BODY_EDITOR', 'Éditeur HTML WYSIWYG pour simplifier la création de contenu' );
    define( $constpref . '_HTMLPR_EXCEPT', 'Les groupes qui peuvent éviter la correction par HTMLPurifier' );
    define( $constpref . '_HTMLPR_EXCEPTDSC', 'Les publications des utilisateurs qui ne sont pas dans les groupes cibles seront filtrées par HTMLPurifier et le module Protector.' );
    define( $constpref . '_HISTORY_P_C', 'Nombre de revisions du document à sauvegarder dans la BDD' );
    define( $constpref . '_MLT_HISTORY', 'Temps minimum pour chaque revision (sec)' );
    define( $constpref . '_BRCACHE', 'Temps du Cache pour les fichiers images (seulement avec le mode wraps)' );
    define( $constpref . '_BRCACHEDSC', 'Temps de Cache pour le navigateur, en secondes, pour les fichiers autres que HTML (0 pour désactiver)' );
    define( $constpref . '_EF_CLASS', 'Classe pour extra_fields' );
    define( $constpref . '_EF_CLASSDSC', 'Les développeurs peuvent remplacer les paramètres de nom de classe et de nom de méthode du gestionnaire pour extra_fields. La valeur par défaut est PicoExtraFields' );
    define( $constpref . '_URIM_CLASS', 'Classe DE mappage URI' );
    define( $constpref . '_URIM_CLASSDSC', 'Les développeurs peuvent remplacer le mappeur URI. La valeur par défaut est PicoUriMapper' );
    define( $constpref . '_EFIMAGES_DIR', 'Répertoire pour extra_fields' );
    define( $constpref . '_EFIMAGES_DIRDSC', 'Chemin relatif vers le répertoire public, par ex. public_html/.<br>Tout d’abord, créez et chmod 777 le répertoire. Valeur par défaut: uploads/dirname' );
    define( $constpref . '_EFIMAGES_SIZE', 'Images supplémentaires générées à partir du fichier de téléchargement' );
    define( $constpref . '_EFIMAGES_SIZEDSC', 'Utilisé dans l’en-tête de l’article et le thème Open Graph. Valeurs par défaut en pixels, largeur x hauteur : 1200x627 820x312 640x360' );
    define( $constpref . '_EFIMAGES_QUALITY', 'Compression de qualité dES images supplémentaires' );
    define( $constpref . '_EFIMAGES_QUALITYDSC', 'Définissez la qualité des images générées. Réduisez la taille de vos fichiers. Valeur par défaut: 75' );
    define( $constpref . '_IMAGICK_PATH', 'Chemin pour ImageMagick binaries' );
    define( $constpref . '_IMAGICK_PATHDSC', 'Laisser vide normal, ou définissez par ex. /usr/X11R6/bin/' );
    define( $constpref . '_COM_DIRNAME', 'Intégration des commentaires : nom du répertoire de d3forum' );
    define( $constpref . '_COM_FORUM_ID', 'Intégration des commentaires : identifiant du forum' );
    define( $constpref . '_COM_VIEW', 'Affichage des Commentaires-Intégrés' );
    define( $constpref . '_COM_ORDER', 'Ordre pour afficher les commenntaires.' );
    define( $constpref . '_COM_POSTSNUM', 'Intégration des commentaires : Nombre maximum de commentaires par page' );
    
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
