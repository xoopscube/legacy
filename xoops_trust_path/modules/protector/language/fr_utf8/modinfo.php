<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {





// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:58
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:54
define($constpref.'_DBTRAPWOSRV','Never checking _SERVER for anti-SQL-Injection');
define($constpref.'_DBTRAPWOSRVDSC','Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:54
define($constpref.'_DBLAYERTRAP','Enable DB Layer trapping anti-SQL-Injection');
define($constpref.'_DBLAYERTRAPDSC','Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page.');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:31
define($constpref.'_DEFAULT_LANG','Default language');
define($constpref.'_DEFAULT_LANGDSC','Specify the language set to display messages before processing common.php');
define($constpref.'_BWLIMIT_COUNT','Bandwidth limitation');
define($constpref.'_BWLIMIT_COUNTDSC','Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Protector apporte à votre site une sécurité supplémentaire contre les attaques du type DoS, SQL Injection, et contaminations de variables.");

// Menu
define($constpref."_ADMININDEX","Centre de protection");
define($constpref."_ADVISORY","Conseils de sécurité");
define($constpref."_PREFIXMANAGER","Gestion du préfixe");
define($constpref."_ADMENU_MYBLOCKSADMIN","Permissions") ;

// Configs
define($constpref."_GLOBAL_DISBL","Désactiver Temporairement");
define($constpref."_GLOBAL_DISBLDSC","Les diverses protections sont désactivées temporairement.<br />Reactiver Protector après avoir resolut votre problème");

define($constpref."_RELIABLE_IPS","Adresses IP autorisées");
define($constpref."_RELIABLE_IPSDSC","Ajouter les addresses IP autorisées en les séparant avec le caractère |<br /> ^ pour le début de la chaîne<br /> $ pour la fin de la chaîne.");

define($constpref."_LOG_LEVEL","Niveau de connexion");
define($constpref."_LOG_LEVELDSC","");

define($constpref."_BANIP_TIME0","Temps d'exclusion d'une adresse IP (secondes)");

define($constpref."_LOGLEVEL0","Aucun");
define($constpref."_LOGLEVEL15","Discret");
define($constpref."_LOGLEVEL63","discret");
define($constpref."_LOGLEVEL255","complet");

define($constpref."_HIJACK_TOPBIT","Nombre de bits IP à protéger par sesssion");
define($constpref."_HIJACK_TOPBITDSC","Anti Session Hi-Jacking: par défaut 32(bit). (Tous les bits sont protégés)<br />Si votre adresse IP n'est pas fixe, reglér la rangée d'adresses IP par le nombre de bits.<br />ex. pour une adresse IP qui évolue dans la rangée 192.168.0.0 à 192.168.0.255, ajouter ceci: 24 (bits)");
define($constpref."_HIJACK_DENYGP","Groupes non autorisés a modifier leur adresse IP au cours d'une session");
define($constpref."_HIJACK_DENYGPDSC","Anti Session Hi-Jacking:<br />Sélectionner le(s) groupe(s) interdit(s) de modifier leur adresse IP au cours d'une session.<br />(Conseil : ajouter les administrateurs.)");
define($constpref."_SAN_NULLBYTE","Filtrer les bits null");
define($constpref."_SAN_NULLBYTEDSC","Le caractère de terminaison '\\0' est souvent utilisé dans des attaques malveillantes.<br />un bit null sera transformé en espace .<br />(Conseil : il est fortement recommandaté d'activer cette option)");
define($constpref."_DIE_NULLBYTE","Déconnecter si des bits null sont utilisés");
define($constpref."_DIE_NULLBYTEDSC","Le caractère de terminaison '\\0' est souvent utilisé dans des attaques malveillantes.<br />(Conseil : il est fortement recommandé d'activer cette option)");
define($constpref."_DIE_BADEXT","Déconnecter si des fichiers interdits sont téléchargés");
define($constpref."_DIE_BADEXTDSC","Protector peut detécter le téléchargement de fichiers avec une extension interdite comme .php , et arreter la session.<br />(Conseil : si vous attachez fréquemment des fichiers php dans B-Wiki ou PukiWikiMod, n'activez pas cette option.)");
define($constpref."_CONTAMI_ACTION","Action lors d'une contamination");
define($constpref."_CONTAMI_ACTIONDS","Selectionner l'action lorsque protector détecte une tentative de contamination des variables globales du systéme XOOPS.<br />(Conseil : écran blanc récommendé)");
define($constpref."_ISOCOM_ACTION","Action lors d'un commentaire isolé");
define($constpref."_ISOCOM_ACTIONDSC","Anti Injection SQL:<br />Sélectionner l'action à effectuer lorsque Protector détecte '/*' .<br />(Conseil : 'filtrer')");
define($constpref."_UNION_ACTION","Action lors d'une requête UNION");
define($constpref."_UNION_ACTIONDSC","Anti Injection SQL:<br />Sélectionner l'action à effectuer lorsque Protector détecte une syntaxe sql UNION.<br />(Conseil : 'filtrer')");
define($constpref."_ID_INTVAL","Forcer la transformation en nombre entier (intval) de variables comme ID");
define($constpref."_ID_INTVALDSC","Protection contre attaques XSS et injections SQL en traitant les appels '*id' comme un nombre entier.<br />(Conseil : activer cette option. Certains modules peuvent cesser de fonctionner.)");
define($constpref."_FILE_DOTDOT","Protection contre des attaques de type traversée de répertoires");
define($constpref."_FILE_DOTDOTDSC","Elimination de «..» pour toutes les requêtes semblables à une tentative d'accés par traversée de répertoires");

define($constpref."_BF_COUNT","Anti Brute Force");
define($constpref."_BF_COUNTDSC","Détermine le nombre de tentatives de connexion autorisées pour un anonyme dans un intervale de 10 minutes. En cas d'échec l'adresse IP sera interdite.");

define($constpref."_DOS_SKIPMODS","Modules à exclure du contrôle DoS (F5)/Crawler");
define($constpref."_DOS_SKIPMODSDSC","Ajouter les noms des répertoires des modules séparés par |. Par exemple, les modules de chat.");

define($constpref."_DOS_EXPIRE","Délai en secondes pour réagir aux rechargements fréquents d'une page (attaque 'touche F5')");
define($constpref."_DOS_EXPIREDSC","Limite en secondes pour les tentatives de rechargement de page (attaque 'touche F5') et aspirateurs de site.");

define($constpref."_DOS_F5COUNT","Nombre de tentatives F5 autorisées");
define($constpref."_DOS_F5COUNTDSC","Protection contre des attaques DoS :<br/>Ajouter une valeur pour détermine le nombre de rechargements d'une connexion avant de considérer comme une attaque malicieuse.");
define($constpref."_DOS_F5ACTION","Action lors d'une attaque F5");

define($constpref."_DOS_CRCOUNT","Nombre de tentatives pour considérer un crawler comme malicieux");
define($constpref."_DOS_CRCOUNTDSC","Protection contre des crawlers-aspirateurs malicieux (par exemple, les bots chasseurs d'emails):<br/>Ajouter une valeur pour détermine le nombre d'accès du crawler avant de le considérer comme une attaque malicieuse.");
define($constpref."_DOS_CRACTION","Action lors des crawlers malicieux");

define($constpref."_DOS_CRSAFE","User-Agent autorisés");
define($constpref."_DOS_CRSAFEDSC","Regex Perl pour les User-Agents.<br /> Evite de considérer le crawler comme un aspirateur.<br/> Ex.: msnbot|Googlebot|Yahoo! Slurp");

define($constpref."_OPT_NONE","Aucune (enregistrer seulement)");
define($constpref."_OPT_SAN","Filtrer");
define($constpref."_OPT_EXIT","Ecran blanc");
define($constpref."_OPT_BIP","Interdire l'IP (indéfiniment)");
define($constpref."_OPT_BIPTIME0","Interdire l'IP (temporairement)");

define($constpref."_DOSOPT_NONE","Aucune (enregistrer seulement)");
define($constpref."_DOSOPT_SLEEP","Veille");
define($constpref."_DOSOPT_EXIT","Ecran blanc");
define($constpref."_DOSOPT_BIP","Interdire l'IP (indéfiniment)");
define($constpref."_DOSOPT_BIPTIME0","Interdire l'IP (temporairement)");;
define($constpref."_DOSOPT_HTA","Interdire via .htaccess(Experimental)");

define($constpref."_BIP_EXCEPT","Groupes jamais enregistrés avec IP interdites");
define($constpref."_BIP_EXCEPTDSC","Les utilisateurs de ces groupe ne seront jamais interdits d'accès.<br />(Conseil : administrateurs recommandé)");

define($constpref."_DISABLES","Désactiver les fonctions dangereuses dans XOOPS");

define($constpref."_BIGUMBRELLA","Activer la protection anti-XSS (Big Umbrella)");
define($constpref."_BIGUMBRELLADSC","Protection contre les attaques par l'intermédiaire des vulnérabilitées XSS. Sans garantie à 100%");

define($constpref."_SPAMURI4U","anti-SPAM: URLs par utilisateurs");
define($constpref."_SPAMURI4UDSC","Nombre limite d'URL dans les données POST d'un utilisateur qui n'est pas administrateur, pour le considérer comme du SPAM. Pour désactiver cette option, laisser sur 0 .");
define($constpref."_SPAMURI4G","anti-SPAM: URLs par anonymes");
define($constpref."_SPAMURI4GDSC","Nombre limite d'URL dans les données POST d'un visiteur anonyme, pour consider comme du SPAM. Pour désactiver cette option, laisser sur 0 .");

}

?>
