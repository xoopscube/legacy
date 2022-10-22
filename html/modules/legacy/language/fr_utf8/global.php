<?php

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
define( '_WARNINSTALL2' , "ATTENTION: Le repértoire %s existe sur votre serveur.<br>Ajoutez um mot-de-passe au fichier 'install/passwd.php' ou supprimez ce repértoire pour des raisons de sécurité.");
define( '_WARNINWRITEABLE' , "ATTENTION : Veillez Changer les permissions du fichier %s pour des raisons de sécurité - sous Unix (444), sous Win32 (lecture seule)");
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

define( '_HELP' , "Aide");
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

define( '_SYS_OS' , 'OS');
define( '_SYS_SERVER' , 'Server');
define( '_SYS_USERAGENT' , 'User agent');
define( '_SYS_PHPVERSION' , 'PHP version');
define( '_SYS_MYSQLVERSION' , 'MySQL version');
