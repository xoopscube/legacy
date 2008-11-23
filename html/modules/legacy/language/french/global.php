<?php
// $Id$

define('_TOKEN_ERROR', 'Attention ! Ceci vous empêche d\'exécuter une requête ou envoi mal formé. S\'il vous plaît, veuillez recommencer pour confirmer!');
define('_SYSTEM_MODULE_ERROR', 'Les modules suivants ne sont pas installés.');
define('_INSTALL','Installer');
define('_UNINSTALL','Désinstaller');
define('_SYS_MODULE_UNINSTALLED','Requit (Non Installé)');
define('_SYS_MODULE_DISABLED','Requit (Désactivé)');
define('_SYS_RECOMMENDED_MODULES','Module Recommendé');
define('_SYS_OPTION_MODULES','Module Optionnel');
define('_UNINSTALL_CONFIRM','Voulez-vous désinstaller le module System?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","Veuillez patienter");
define("_FETCHING","Chargement...");
define("_TAKINGBACK","Retour à là page précédente...");
define("_LOGOUT","Déconnexion");
define("_SUBJECT","Sujet");
define("_MESSAGEICON","Icône de message");
define("_COMMENTS","Commentaires");
define("_POSTANON","Poster en anonyme");
define("_DISABLESMILEY","Désactiver les émoticônes");
define("_DISABLEHTML","Désactiver le html");
define("_PREVIEW","Prévisualiser");

define("_GO","Ok !");
define("_NESTED","Emboîté");
define("_NOCOMMENTS","Sans commentaires");
define("_FLAT","A plat");
define("_THREADED","Par conversation");
define("_OLDESTFIRST","Les anciens en premier");
define("_NEWESTFIRST","Les récents en premier");
define("_MORE","plus...");
define("_MULTIPAGE","Pour avoir votre article sur des pages multiples, insérer le mot <font color=red>[pagebreak]</font> (avec les crochets) dans l'article.");
define("_IFNOTRELOAD","Si la page ne se recharge pas automatiquement, merci de cliquer <a href=%s>ici</a>");
define("_WARNINSTALL2","ATTENTION: Le repértoire %s existe sur votre serveur. <br />Veuillez supprimer ce repértoire pour des raisons de sécurité.");
define("_WARNINWRITEABLE","ATTENTION : Veillez Changer les permissions du fichier %s pour des raisons de sécurité.<br /> sous Unix (444), sous Win32 (lecture seule)");
define('_WARNPHPENV','ATTENTION : paramètres php.ini "%s" est réglé "%s". %s');
define('_WARNSECURITY','(Ceci peut causer des problè de sécurité)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Profil");
define("_POSTEDBY","Posté par");
define("_VISITWEBSITE","Visiter le site Web");
define("_SENDPMTO","Envoyer un message privé à %s");
define("_SENDEMAILTO","Envoyer un E-mail à %s");
define("_ADD","Ajouter");
define("_REPLY","Répondre");
define("_DATE","Date");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","Principal");
define("_MANUAL","Manuel");
define("_INFO","Info");
define("_CPHOME","Panneau de contrôle");
define("_YOURHOME","Page d'accueil");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Qui est en ligne");
define('_GUESTS', 'Invité(s)');
define('_MEMBERS', 'Membre(s)');
define("_ONLINEPHRASE","<b>%s</b> utilisateur(s) en ligne");
define("_ONLINEPHRASEX","dont <b>%s</b> sur <b>%s</b>");
define("_CLOSE","Fermer");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","Citation :");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","Désolé, vous n'avez pas les droits pour accéder à cette zone.");

//%%%%%		Common Phrases		%%%%%
define("_NO","Non");
define("_YES","Oui");
define("_EDIT","Editer");
define("_DELETE","Effacer");
define("_VIEW","Visualiser");
define("_SUBMIT","Valider");
define("_MODULENOEXIST","Le module sélectionné n'existe pas !");
define("_ALIGN","Alignement");
define("_LEFT","Gauche");
define("_CENTER","Centre");
define("_RIGHT","Droite");
define("_FORM_ENTER", "Merci d'entrer %s");

// %s represents file name
define("_MUSTWABLE","Le fichier %s doit être accessible en écriture sur le serveur !");
// Module info
define('_PREFERENCES', 'Préférences');
define("_VERSION", "Version");
define("_DESCRIPTION", "Description");
define("_ERRORS", "Erreurs");
define("_NONE", "Aucun");
define('_ON','le');
define('_READS','lectures');
define('_WELCOMETO','Bienvenue sur %s');
define('_SEARCH','Chercher');
define('_ALL', 'Tous');
define('_TITLE', 'Titre');
define('_OPTIONS', 'Options');
define('_QUOTE', 'Citation');
define('_LIST', 'Liste');
define('_LOGIN','Connexion');
define('_USERNAME','Pseudo :&nbsp;');
define('_PASSWORD','Mot de passe :&nbsp;');
define("_SELECT","Sélectionner");
define("_IMAGE","Image");
define("_SEND","Envoyer");
define("_CANCEL","Annuler");
define("_ASCENDING","Ordre ascendant");
define("_DESCENDING","Ordre déscendant");
define('_BACK', 'Retour');
define('_NOTITLE', 'Aucun titre');
define('_RETURN_TOP', 'Retour haut de la page');

/* Image manager */
define('_IMGMANAGER',"Gestionnaire d'images");
define('_NUMIMAGES', '%s images');
define('_ADDIMAGE','Ajouter un fichier image');
define('_IMAGENAME','Nom :');
define('_IMGMAXSIZE','Taille maxi autorisée (ko) :');
define('_IMGMAXWIDTH','Largeur maxi autorisée (pixels) :');
define('_IMGMAXHEIGHT','Hauteur maxi autorisée (pixels) :');
define('_IMAGECAT','Catégorie :');
define('_IMAGEFILE','Fichier image ');
define('_IMGWEIGHT',"Ordre d'affichage dans le gestionnaire d'images :");
define('_IMGDISPLAY','Afficher cette image ?');
define('_IMAGEMIME','Type MIME :');
define('_FAILFETCHIMG', "Impossible d'uploader le fichier %s");
define('_FAILSAVEIMG', "Impossible de stocker l'image %s dans la base de données");
define('_NOCACHE', 'Pas de Cache');
define('_CLONE', 'Cloner');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Commençant par");
define("_ENDSWITH", "Finissant par");
define("_MATCHES", "Correspondant à");
define("_CONTAINS", "Contenant");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Enregistrement");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","TAILLE");  // font size
define("_FONT","POLICE");  // font family
define("_COLOR","COULEUR");  // font color
define("_EXAMPLE","EXEMPLE");
define("_ENTERURL","Entrez l'URL du lien que vous voulez ajouter :");
define("_ENTERWEBTITLE","Entrez le titre du site web :");
define("_ENTERIMGURL","Entrez l'URL de l'image que vous voulez ajouter.");
define("_ENTERIMGPOS","Maintenant, entrez la position de l'image.");
define("_IMGPOSRORL","'R' ou 'r' pour droite, 'L' ou 'l' pour gauche, ou laisser vide.");
define("_ERRORIMGPOS","ERREUR ! Entrez la position de l'image.");
define("_ENTEREMAIL","Entrez l'adresse e-mail que vous voulez ajouter.");
define("_ENTERCODE","Entrez les codes que vous voulez ajouter.");
define("_ENTERQUOTE","Entrez le texte que vous voulez citer.");
define("_ENTERTEXTBOX","Merci de saisir le texte dans la boîte.");
define("_ALLOWEDCHAR","Longueur maximum autorisée de caractères :&nbsp;");
define("_CURRCHAR","Longueur de caractères actuelle :&nbsp;");
define("_PLZCOMPLETE","Merci de compléter le sujet et le champ message.");
define("_MESSAGETOOLONG","Votre message est trop long.");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 seconde');
define('_SECONDS', '%s secondes');
define('_MINUTE', '1 minute');
define('_MINUTES', '%s minutes');
define('_HOUR', '1 heure');
define('_HOURS', '%s heures');
define('_DAY', '1 jour');
define('_DAYS', '%s jours');
define('_WEEK', '1 semaine');
define('_MONTH', '1 mois');

define('_HELP', "Aide");

define("_DATESTRING","j/n/Y G:i:s");
define("_MEDIUMDATESTRING","j/n/Y G:i");
define("_SHORTDATESTRING","j/n/Y");
/*
The following characters are recognized in the format string:
a - "am" or "pm"
A - "AM" or "PM"
d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"
D - day of the week, textual, 3 letters; i.e. "Fri"
F - month, textual, long; i.e. "January"
h - hour, 12-hour format; i.e. "01" to "12"
H - hour, 24-hour format; i.e. "00" to "23"
g - hour, 12-hour format without leading zeros; i.e. "1" to "12"
G - hour, 24-hour format without leading zeros; i.e. "0" to "23"
i - minutes; i.e. "00" to "59"
j - day of the month without leading zeros; i.e. "1" to "31"
l (lowercase 'L') - day of the week, textual, long; i.e. "Friday"
L - boolean for whether it is a leap year; i.e. "0" or "1"
m - month; i.e. "01" to "12"
n - month without leading zeros; i.e. "1" to "12"
M - month, textual, 3 letters; i.e. "Jan"
s - seconds; i.e. "00" to "59"
S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"
t - number of days in the given month; i.e. "28" to "31"
T - Timezone setting of this machine; i.e. "MDT"
U - seconds since the epoch
w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)
Y - year, 4 digits; i.e. "1999"
y - year, 2 digits; i.e. "99"
z - day of the year; i.e. "0" to "365"
Z - timezone offset in seconds (i.e. "-43200" to "43200")
*/


//%%%%%		LANGUAGE SPECIFIC SETTINGS   %%%%%
if (!defined('_CHARSET')) {
	define('_CHARSET', 'ISO-8859-1');
	//define('_CHARSET', 'utf-8');
}

if (!defined('_LANGCODE')) {
	define('_LANGCODE', 'fr');
}

// change 0 to 1 if this language is a multi-bytes language
define("XOOPS_USE_MULTIBYTES", "0");
?>
