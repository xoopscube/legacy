<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Forum");

// A brief description of this module
define($constpref."_DESC","Module Forums pour XOOPS");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","Sujets");
define($constpref."_BDESC_LIST_TOPICS","Ce bloc peut être utilisé de manière polyvalente. Bien sûr, vous pouvez le dupliquer.");
define($constpref."_BNAME_LIST_POSTS","Posts");
define($constpref."_BNAME_LIST_FORUMS","Forums");

// admin menu
define($constpref.'_ADMENU_CATEGORYACCESS','Permissions des Catégories');
define($constpref.'_ADMENU_FORUMACCESS','Permissions des Forums');
define($constpref.'_ADMENU_ADVANCEDADMIN','Avancé');
define($constpref.'_ADMENU_POSTHISTORIES','Histories');
define($constpref.'_ADMENU_MYLANGADMIN','Langages');
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocs/Permissions');
define($constpref.'_ADMENU_MYPREFERENCES','Préférences');

// configurations
define($constpref.'_TOP_MESSAGE','Message en-tête du forum');
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Forum Top</h1><p class="d3f_welcome">Pour consulter les messages, choisissez la catégorie et le forum que vous voulez visiter ci-dessous. </p>');
define($constpref.'_SHOW_BREADCRUMBS','Afficher la navigation (breadcrumbs)');
define($constpref.'_DEFAULT_OPTIONS','Options cochées par défault dans le formulaire pour poster');
define($constpref.'_DEFAULT_OPTIONSDSC','Lister les options a cocher  séparées par  une virgule (,).<br />eg) smiley,xcode,br,number_entity<br />Vous pouvez ajouter ces options: special_entity html attachsig u2t_marked');
define($constpref.'_ALLOW_HTML','Autoriser HTML');
define($constpref.'_ALLOW_HTMLDSC','N\'activez pas cette option au hazard. Ceci peut rendre votre site vulnérable et permettre à un utilisateur malveillant \'insérer d\'un script.');
define($constpref.'_ALLOW_TEXTIMG','Autoriser l\'affichage d\'images externes dans les messages');
define($constpref.'_ALLOW_TEXTIMGDSC','Si un utilisateur malveillant poste une image externe utilisant [img], il peut connaitre les adresses IP ou navigateurs des utilisateurs de votre site.');
define($constpref.'_ALLOW_SIG','Autoriser la signature');
define($constpref.'_ALLOW_SIGDSC','Permet aux utilisateurs d\'associer une signature à leur messages');
define($constpref.'_ALLOW_SIGIMG','Autoriser l\'affichage d\'images externes dans la signature');
define($constpref.'_ALLOW_SIGIMGDSC','Si un utilisateur malveillant poste une image externe utilisant [img], il peut connaitre les adresses IP ou navigateurs des utilisateurs de votre site');
define($constpref.'_USE_VOTE','Activer l\'option, VOTE');
define($constpref.'_USE_SOLVED','Activer l\'option, RESOLU');
define($constpref.'_ALLOW_MARK','Activer l\'option, Post-it');
define($constpref.'_ALLOW_HIDEUID','Autoriser un utilisateur à poster ou pas avec son nom');
define($constpref.'_POSTS_PER_TOPIC','Maximum de messages dans un Sujet');
define($constpref.'_POSTS_PER_TOPICDSC','Un Sujet peut avoir un nombre limité de messages postés.');
define($constpref.'_HOT_THRESHOLD','Seuil d\'un Sujet Populaire');
define($constpref.'_HOT_THRESHOLDDSC','Valeur à partir de laquelle un Sujet est considéré populaire.');
define($constpref.'_TOPICS_PER_PAGE','Nombre de Sujets par page du forum');
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','Sujets par page de l\'ensemble des forums');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','Délai requit pour l\'édition par les utilisateurs, en secondes.');
define($constpref.'_SELFEDITLIMITDSC','Pour autoriser les utilisateurs à editer leurs messages, ajoutez une valeur en secondes. Et pour interdire l\'édition, ajoutez 0.');
define($constpref.'_SELFDELLIMIT','Délai requit, en secondes, pour la suppression par les utilisateurs.');
define($constpref.'_SELFDELLIMITDSC','Pour autoriser les utilisateurs à supprimer leurs messages, ajoutez une valeur en secondes. Et pour interdire la suppresion, ajoutez 0. Aucun message parent ne peut être effacé.');
define($constpref.'_CSS_URI','URI du fichier CSS pour ce module');
define($constpref.'_CSS_URIDSC','Chemin absolut ou relatif. Par défaut: index.css');
define($constpref.'_IMAGES_DIR','Repértoire pour les fichiers image');
define($constpref.'_IMAGES_DIRDSC','Le chemin relatif devrait être celui du module. Par défaut: images');
define($constpref.'_BODY_EDITOR','Editeur');
define($constpref.'_BODY_EDITORDSC','L\'éditeur WYSIWYG sera activé uniquement dans les forums permettant le code HTML. Les forums sans HTML specialchars, utilisent l\'éditeur xoopsdhtml automatiquement.');
define($constpref.'_ANONYMOUS_NAME','Nom de l\'Anonyme');
define($constpref.'_ANONYMOUS_NAMEDSC','Nom utilisé pour un message poste de manière anonyme.');
define($constpref.'_ICON_MEANINGS','Les sens des icônes');
define($constpref.'_ICON_MEANINGSDSC','Spécifier les ALT des icônes. Chaque ALT devrait être séparé par (|). Le premier ALT correspond à "posticon0.gif".');
define($constpref.'_ICON_MEANINGSDEF','aucun|normal|mécontent|heureux|augmenter|baisser|rapport|question');
define($constpref.'_GUESTVOTE_IVL','Vote des invités');
define($constpref.'_GUESTVOTE_IVLDSC','Ajoutez 0, pour neutraliser le vote des utilisateurs invités. Ou ajoutez une valeur en secondes pour permettre un deuxième vote du même IP.');
define($constpref.'_ANTISPAM_GROUPS','Les Groupes a cocher anti-SPAM');
define($constpref.'_ANTISPAM_GROUPSDSC','Laisser habituellement tout en blanc.');
define($constpref.'_ANTISPAM_CLASS','Nom de la Class anti-SPAM');
define($constpref.'_ANTISPAM_CLASSDSC','La valeur par défaut est "default". Si vous voulez désactiver l\'anti-SPAM pour les invités, laissez en blanc');


// Notify Categories
define($constpref.'_NOTCAT_TOPIC', 'Ce Sujet'); 
define($constpref.'_NOTCAT_TOPICDSC', 'Notifications sur le Sujet actuel');
define($constpref.'_NOTCAT_FORUM', 'Ce Forum'); 
define($constpref.'_NOTCAT_FORUMDSC', 'Notifications sur le Forum actuel');
define($constpref.'_NOTCAT_CAT', 'Cette Catégorie');
define($constpref.'_NOTCAT_CATDSC', 'Notifications sur la Catégorie actuelle');
define($constpref.'_NOTCAT_GLOBAL', 'Ce module');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notifications globales sur le module forums');

// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'Nouveau message dans ce Sujet');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Notifiez-moi des nouveaux messages dans le Sujet actuel.');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Nouveau message dans le Sujet');

define($constpref.'_NOTIFY_FORUM_NEWPOST', 'Nouveau message dans ce Forum');
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Notifiez-moi des nouveaux messages dans le Forum actuel.');
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nouveau message dans ce Forum');

define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'Nouveau Sujet dans ce forum');
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Notifiez-moi des nouveaux Sujets dans le Forum actuel.');
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nouveau Sujet dans ce Forum');

define($constpref.'_NOTIFY_CAT_NEWPOST', 'Nouveau message dans cette Catégorie');
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Notifiez-moi des nouveaux messages dans la Catégorie actuelle.');
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nouveau message dans cette Catégorie');

define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'Nouveau Sujet dans cette Catégorie');
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Notifiez-moi des nouveaux Sujets dans la Catégorie actuelle.');
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nouveau Sujet dans cette Catégorie');

define($constpref.'_NOTIFY_CAT_NEWFORUM', 'Nouveau Forum dans cette Catégorie');
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Notifiez-moi des nouveaux Forums dans la Catégorie actuelle.');
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nouveau Forum dans cette Catégorie');

define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'Nouveau message dans ce module');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Notifiez-moi des nouveaux messages dans ce module.');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: Nouveau message');

define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'Nouveau Sujet dans ce module');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Notifiez-moi des nouveaux Sujets dans ce module.');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: Nouveau Sujet');

define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'Nouveau Forum dans ce module');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Notifiez-moi des nouveaux Forums dans ce module.');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: Nouveau forum');

define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'Nouveau Message (Texte Complet)');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Notifiez-moi de tout nouveau message (avec texte intégral du message).');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
define($constpref.'_NOTIFY_GLOBAL_WAITING', 'Nouveau message en attente');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Informez-moi de nouveaux messages attendant l\'approbation. Pour des admins seulement');
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Nouveau message en attente');

}

?>