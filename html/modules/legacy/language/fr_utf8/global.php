<?php

// ADMIN QUICK LINKS
define( '_AD_ACCOUNT' , 'Compte');
define( '_AD_AVATAR' , 'Avatar');
define( '_AD_BANNERS' , 'Bannières');
define( '_AD_BLOCKS' , 'Blocs');
define( '_AD_CENSOR' , 'Censurer');
define( '_AD_CLOSE_SITE' , 'Fermer le site');
define( '_AD_DEBUG' , 'Déboguer');
define( '_AD_EMOTICON' , 'Émoticône');
define( '_AD_LOCALE' , 'Langue'); /* localization Localize your app Translation Translation (language )*/
define( '_AD_MAIL_SETUP' , 'Messagerie');
define( '_AD_MAILING' , 'Publipostage');
define( '_AD_META_SEO' , 'Meta SEO');
define( '_AD_MODULES' , 'Modules');
define( '_AD_PROFILE' , 'Profil');
define( '_AD_RANKS' , 'Rangs');
define( '_AD_SEARCH' , 'Recherche');
define( '_AD_SETTINGS' , 'Paramètres');
define( '_AD_TEMPLATES' , 'Templates');
define( '_AD_TEMPLATE_SET' , 'Lot Template');
define( '_AD_THEMES' , 'Theme');
define( '_AD_URL_REWRITE' , 'URL Rewrite');
define( '_AD_USER_SEARCH' , 'Rechercher'); /* ( find user) */

// ADMIN BLOCKS
define( '_AD_BLOCK_ACCOUNT' , "Menu de l'admin");
define( '_AD_BLOCK_ADMIN' , 'Options du thème');
define( '_AD_BLOCK_ASIDE' , "Menu admin");
define( '_AD_BLOCK_MENU' , 'Menu des Modules');
define( '_AD_BLOCK_ONLINE' , 'Utilisateurs en ligne');
define( '_AD_BLOCK_OVERVIEW' , 'Statistiques générales');
define( '_AD_BLOCK_PHP' , 'Paramètres PHP');
define( '_AD_BLOCK_SEARCH' , 'Recherche de fonctions');
define( '_AD_BLOCK_SERVER' , 'Environnement serveur');
define( '_AD_BLOCK_THEME' , 'Sélectionne un thème');
define( '_AD_BLOCK_TIPS' , 'Aide sur les modules');
define( '_AD_BLOCK_TOOLTIP' , 'Désactiver info-bulles');

// ADMIN NAV
define( '_LINKS_TIP' , 'Admin Quick Links');
define( '_THEME_TIP' , 'Thème clair ou sombre');
define( '_TIME_TIP' , 'Current timestamp');

// ADMIN DASHBOARD - TABS
define( '_ABOUT' , 'À propos de XOOPSCube');
define( '_START' , 'Démarrer');
define( '_SOURCE' , 'Code source');

define( '_WAP_LICENSE' , 'Licences Open Source');
define( '_WAP_LICENSE_DSC' , "Les logiciels sous licence open source sont généralement disponibles gratuitement, mais ce n'est pas toujours le cas. Le code source de XCL est conçu pour être accessible au public. N'importe qui peut visualiser, modifier et distribuer le code comme bon lui semble.
Les modules et les thèmes sont publiés sous les licences BSD, GPL et MIT.<br>
La licence BSD de XCube permet une utilisation propriétaire et permet l'incorporation du logiciel dans des produits propriétaires.");

define( '_WAP_BUNDLE' , 'XCL Bundle Package');
define( '_WAP_BUNDLE_DSC' , "XCL v.2.3.x est une application Web à usage général avec une approche de développement à faible code maintenue sur GitHub.
L'un des principaux avantages du package groupé XCL réside dans les nombreuses options qu'il offre prêtes à l'emploi.
Vous pouvez l'étendre vous-même avec le stockage en nuage en suivant les instructions simples du compositeur basé sur l'interface graphique.
Vous n'avez plus besoin de télécharger les archives séparément et de les télécharger manuellement. X-Update Manager fournit une fonctionnalité en un clic pour vous aider à obtenir et à déployer des modules complémentaires.
Il n'y a pas de contrats, de coûts cachés, de limitations ou de restrictions.");

define( '_WAP_B2C' , 'B2B and B2C Services');
define( '_WAP_B2C_DSC' , "Par exemple, les développeurs individuels et les agences peuvent créer leur propre package gratuit ou payant,
personnaliser et offrir des fonctionnalités spécifiques pour un large éventail d'industries. Les distributeurs professionnels peuvent facturer le service
des frais pour couvrir les frais administratifs ou de traitement, le support technique ou les services de maintenance.");

// System
define( '_TOKEN_ERROR' , "Attention ! A fin d'éviter une erreur dans la requête faite au serveur, la validation du formulaire requiert une confirmation!");
define( '_SYSTEM_MODULE_ERROR' , "Les modules suivants sont requis.");
define( '_INSTALL' , "Installer");
define( '_UNINSTALL' , "Désinstaller");
define( '_SYS_MODULE_UNINSTALLED' , "Requis (Non Installé)");
define( '_SYS_MODULE_DISABLED' , "Requis (Désactivé)");
define( '_SYS_RECOMMENDED_MODULES' , "Module Recommendé");
define( '_SYS_OPTION_MODULES' , "Module Optionnel");
define( '_UNINSTALL_CONFIRM' , "Voulez-vous désinstaller le module System?");

//%%%%%% File Name mainfile.php %%%%%
define( '_PLEASEWAIT' , "Veuillez patienter");
define( '_FETCHING' , "Chargement...");
define( '_TAKINGBACK' , "Retour à là page précédente...");
define( '_LOGOUT' , "Déconnexion");
define( '_SUBJECT' , "Sujet");
define( '_MESSAGEICON' , "Icône de message");
define( '_COMMENTS' , "Commentaires");
define( '_POSTANON' , "Poster en anonyme");
define( '_DISABLESMILEY' , "Désactiver les émoticônes");
define( '_DISABLEHTML' , "Désactiver le html");
define( '_PREVIEW' , "Prévisualiser");

define( '_GO' , "Ok !");
define( '_NESTED' , "Emboîté");
define( '_NOCOMMENTS' , "Sans commentaires");
define( '_FLAT' , "A plat");
define( '_THREADED' , "Par conversation");
define( '_OLDESTFIRST' , "Les anciens en premier");
define( '_NEWESTFIRST' , "Les récents en premier");
define( '_MORE' , "plus...");
define( '_MULTIPAGE' , "Pour avoir votre article sur des pages multiples, insérer le mot <span style='color:red'>[pagebreak]</span> (avec les crochets) dans l'article.");
define( '_IFNOTRELOAD' , "Si la page ne se recharge pas automatiquement, merci de cliquer <a href=%s>ici</a>");
define( '_WARNINSTALL2' , '<span>ATTENTION: le dossier <b>Install</b> existe!<br><span class="alert-install">{0}</span><br>Editez le fichier <span class="alert-install">install/passwd.php</span> et ajoutez um mot-de-passe ou supprimez ce dossier pour des raisons de sécurité.</span>');
define( '_WARNINWRITEABLE' , "<span>ATTENTION: <b>Mainfile</b> est accessible en écriture !<br><span class='alert-install'>{0}</span><br>Changez les permissions du fichier pour des raisons de sécurité - sous Unix (444), sous Win32 (lecture seule)</span>");

define( '_WARNPHPENV' , "ATTENTION : paramètres php.ini \"%s\" est réglé \"%s\". %s");
define( '_WARNSECURITY' , "(Ceci peut causer des problèmes de sécurité)");
define( '_WARN_INSTALL_TIP' , "Activer la précharge - A des fins de développement uniquement!<br>Utilisez le 'preload' pour conserver le fichier 'mainfile'' et le répertoire d'installation.<br>Souvenez-vous de changer les permissions de 'mainfile' (lecture) et de supprimer '/install' pour des raisons de sécurité.");

//%%%%%% File Name themeuserpost.php %%%%%
define( '_PROFILE' , "Profil");
define( '_POSTEDBY' , "Posté par");
define( '_VISITWEBSITE' , "Visiter le site Web");
define( '_SENDPMTO' , "Envoyer un message privé à %s");
define( '_SENDEMAILTO' , "Envoyer un E-mail à %s");
define( '_ADD' , "Ajouter");
define( '_REPLY' , "Répondre");
define( '_DATE' , "Date");

//%%%%%% File Name admin_functions.php %%%%%
define( '_MAIN' , "Principal");
define( '_MANUAL' , "Manuel");
define( '_INFO' , "Info");
define( '_CPHOME' , "Panneau de contrôle");
define( '_YOURHOME' , "Page d'accueil");

//%%%%%% File Name misc.php (who's-online popup) %%%%%
define( '_WHOSONLINE' , "Qui est en ligne");
define( '_GUESTS' , 'Invité(s)');
define( '_MEMBERS' , 'Membre(s)');
define( '_ONLINEPHRASE' , "<b>%s</b> utilisateur(s) en ligne");
define( '_ONLINEPHRASEX' , "dont <b>%s</b> sur <b>%s</b>");
define( '_CLOSE' , "Fermer"); // Close window

//%%%%%% File Name module.textsanitizer.php %%%%%
define( '_QUOTEC' , "Citation :");

//%%%%%% File Name admin.php %%%%%
define( '_NOPERM' , "Désolé, vous n'avez pas les droits d'accès à cette zone.");

//%%%%% Common Phrases %%%%%
define( '_NO' , "Non");
define( '_YES' , "Oui");
define( '_EDIT' , "Editer");
define( '_DELETE' , "Effacer");
define( '_VIEW' , "Visualiser");
define( '_SAVE' , 'Enregistrer');
define( '_SUBMIT' , "Valider");
define( '_MODULENOEXIST' , "Le module sélectionné n'existe pas !");
define( '_ALIGN' , "Alignement");
define( '_LEFT' , "Gauche");
define( '_CENTER' , "Centre");
define( '_RIGHT' , "Droite");
define( '_FORM_ENTER' , "Merci d'entrer %s");
// %s represents file name
define( '_MUSTWABLE' , "Le fichier %s doit être accessible en écriture sur le serveur !");
// Module info
define( '_PREFERENCES' , 'Préférences');
define( '_VERSION' , "Version");
define( '_DESCRIPTION' , "Description");
define( '_ERRORS' , "Erreurs");
define( '_NONE' , "Aucun");
define( '_ON' , 'le');
define( '_READS' , 'lectures');
define( '_WELCOMETO' , 'Bienvenue sur %s');
define( '_SEARCH' , 'Chercher');
define( '_ALL' , 'Tous');
define( '_TITLE' , 'Titre');
define( '_OPTIONS' , 'Options');
define( '_QUOTE' , 'Citation');
define( '_LIST' , 'Liste');
define( '_LOGIN' , 'Connexion');
define( '_USERNAME' , 'Pseudo : ');
define( '_PASSWORD' , 'Mot de passe : ');
define( '_SELECT' , "Sélectionner");
define( '_IMAGE' , "Image");
define( '_SEND' , "Envoyer");
define( '_CANCEL' , "Annuler");
define( '_ASCENDING' , "Ordre ascendant");
define( '_DESCENDING' , "Ordre déscendant");
define( '_BACK' , 'Retour');
define( '_NOTITLE' , 'Aucun titre');
define( '_RETURN_TOP' , '↑ Top');
/* Image manager */
define( '_IMGMANAGER' , "Gestionnaire d'images");
define( '_NUMIMAGES' , '%s images');
define( '_ADDIMAGE' , 'Ajouter un fichier image');
define( '_IMAGENAME' , 'Nom :');
define( '_IMGMAXSIZE' , 'Taille maxi autorisée (ko) :');
define( '_IMGMAXWIDTH' , 'Largeur maxi autorisée (pixels) :');
define( '_IMGMAXHEIGHT' , 'Hauteur maxi autorisée (pixels) :');
define( '_IMAGECAT' , 'Catégorie :');
define( '_IMAGEFILE' , 'Fichier image ');
define( '_IMGWEIGHT' , "Ordre d'affichage dans le gestionnaire d'images :");
define( '_IMGDISPLAY' , 'Afficher cette image ?');
define( '_IMAGEMIME' , 'Type MIME :');
define( '_FAILFETCHIMG' , "Impossible de télécherger le fichier %s");
define( '_FAILSAVEIMG' , "Impossible de stocker l'image %s dans la base de données");
define( '_NOCACHE' , 'Pas de Cache');
define( '_CLONE' , 'Cloner');

//%%%%% File Name class/xoopsform/formmatchoption.php %%%%%
define( '_STARTSWITH' , "Commençant par");
define( '_ENDSWITH' , "Finissant par");
define( '_MATCHES' , "Correspondant à");
define( '_CONTAINS' , "Contenant");

//%%%%%% File Name commentform.php %%%%%
define( '_REGISTER' , "Enregistrement");

//%%%%%% File Name xoopscodes.php %%%%%
define( '_SIZE' , "TAILLE"); // font size
define( '_FONT' , "POLICE"); // font family
define( '_COLOR' , "COULEUR"); // font color
define( '_EXAMPLE' , "EXEMPLE");
define( '_ENTERURL' , "Entrez l'URL du lien que vous voulez ajouter :");
define( '_ENTERWEBTITLE' , "Entrez le titre du site web :");
define( '_ENTERIMGURL' , "Entrez l'URL de l'image que vous voulez ajouter.");
define( '_ENTERIMGPOS' , "Maintenant, entrez l'alignement de l'image.");
define( '_IMGPOSRORL' , "'R' ou 'r' pour droite, 'L' ou 'l' pour gauche, ou laisser vide.");
define( '_ERRORIMGPOS' , "ERREUR ! Entrez l'alignement de l'image.");
define( '_ENTEREMAIL' , "Entrez l'adresse e-mail que vous voulez ajouter.");
define( '_ENTERCODE' , "Entrez les codes que vous voulez ajouter.");
define( '_ENTERQUOTE' , "Entrez le texte que vous voulez citer.");
define( '_ENTERTEXTBOX' , "Merci de saisir le texte dans la boîte.");
define( '_ALLOWEDCHAR' , "Longueur maximum de caractères autorisés : ");
define( '_CURRCHAR' , "Longueur de caractères actuelle : ");
define( '_PLZCOMPLETE' , "Merci de compléter le sujet et le champ message.");
define( '_MESSAGETOOLONG' , "Votre message est trop long.");

//%%%%% TIME FORMAT SETTINGS %%%%%
define( '_SECOND' , '1 seconde');
define( '_SECONDS' , '%s secondes');
define( '_MINUTE' , '1 minute');
define( '_MINUTES' , '%s minutes');
define( '_HOUR' , '1 heure');
define( '_HOURS' , '%s heures');
define( '_DAY' , '1 jour');
define( '_DAYS' , '%s jours');
define( '_WEEK' , '1 semaine');
define( '_MONTH' , '1 mois');

define( '_ACTION' , 'Action');
define( '_HELP' , "Aide");
define( '_MENU' , 'Menu');

// Added interface Enum

//%%%%%		   %%%%%
define( '_CATEGORY' , 'Category');
define( '_TAG' , 'Tag');
define( '_STATUS' , 'Status');
define( '_STATUS_DELETED' , 'Deleted');
define( '_STATUS_REJECTED' , 'Rejected');
define( '_STATUS_POSTED' , 'Posted');
define( '_STATUS_PUBLISHED' , 'Published');

//%%%%% Group %%%%%
define( '_GROUP' , 'Group');
define( '_MEMBER' , 'Member');
define( '_GROUP_RANK_GUEST' , 'Guest');
define( '_GROUP_RANK_ASSOCIATE' , 'Associate');
define( '_GROUP_RANK_REGULAR' , 'Regular');
define( '_GROUP_RANK_STAFF' , 'Staff');
define( '_GROUP_RANK_OWNER' , 'Owner');

//%%%%% System %%%%%
define( '_DEBUG_MODE' , 'Debug');
define( '_DEBUG_MODE_PHP' , 'PHP');
define( '_DEBUG_MODE_SQL' , 'SQL');
define( '_DEBUG_MODE_SMARTY' , 'Smarty');
define( '_DEBUG_MODE_DESC' , 'Disable debug mode in production. Admin > Settings > Debug mode [Off].');

//%%%%% System Control Panel %%%%%
define( '_ACCOUNT' , 'Account');
define( '_BANNERS' , 'Banners');
define( '_BLOCKS' , 'Blocks');
define( '_GROUPS' , 'Groups');
define( '_MAILING', 'Mailing');
define( '_MODULES' , 'Modules');
define( '_RANKS' , 'Ranks');
define( '_TRANSLATION' , 'Translation');
define( '_USERS' , 'Users');

define( '_SYS_OS' , 'OS');
define( '_SYS_SERVER' , 'Server');
define( '_SYS_USERAGENT' , 'User agent');
define( '_SYS_PHPVERSION' , 'PHP version');
define( '_SYS_MYSQLVERSION' , 'MySQL version');
