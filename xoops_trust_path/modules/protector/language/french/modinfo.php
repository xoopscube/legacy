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

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:53
define($constpref.'_DBTRAPWOSRV','Never checking _SERVER for anti-SQL-Injection');
define($constpref.'_DBTRAPWOSRVDSC','Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:53
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
define($constpref."_DESC","Ce module prot&egrave;ge votre site xoops d'attaques vari&eacute;es comme DoS , SQL Injection , et des contaminations par variables.");

// Menu
define($constpref."_ADMININDEX","Centre de protection");
define($constpref."_ADVISORY","Conseils de sécurité");
define($constpref."_PREFIXMANAGER","Gestionnaire de préfixe");
define($constpref."_ADMENU_MYBLOCKSADMIN","Permissions") ;

// Configs
define($constpref."_GLOBAL_DISBL","Temporairement d&eacute;sactiv&eacute;");
define($constpref."_GLOBAL_DISBLDSC","Toutes les protections sont d&eacute;sactiv&eacute;es temporairement.<br />N'oubliez pas de les r&eacute;activer apr&egrave;s correction de votre anomalie");

define($constpref."_RELIABLE_IPS","IPs fiables");
define($constpref."_RELIABLE_IPSDSC","Saisissez les IPs fiables s&eacute;par&eacute;es avec le caract&egrave;re |<br /> ^ correspond au d&eacute;but de la cha&icirc;ne<br /> $ correspond &agrave; la fin de la cha&icirc;ne.");

define($constpref."_LOG_LEVEL","Niveau de connexion");
define($constpref."_LOG_LEVELDSC","");

define($constpref."_BANIP_TIME0","Temps d'exclusion d'une IP bannie (secondes)");

define($constpref."_LOGLEVEL0","Aucun");
define($constpref."_LOGLEVEL15","Serein");
define($constpref."_LOGLEVEL63","serein");
define($constpref."_LOGLEVEL255","complet");

define($constpref."_HIJACK_TOPBIT","Nombre de bits de protection de la sesssion IP");
define($constpref."_HIJACK_TOPBITDSC","Anti session Hi-Jacking: par d&eacute;faut 32(bit). (Tous les bits sont prot&eacute;g&eacute;s)<br />Quand votre adresse IP n'est pas fixe, fixer la rang&eacute;e d'adresses IP par le nombre de bits.<br />i.e. si votre adresse IP peut &eacute;voluer dans la rang&eacute;e  192.168.0.0 &agrave; 192.168.0.255, indiquez 24 (bits) ici");
define($constpref."_HIJACK_DENYGP","Groupes non autoris&eacute;s &agrave; modifier leur adresse IP au cours d'une session");
define($constpref."_HIJACK_DENYGPDSC","Anti Session Hi-Jacking:<br />S&eacute;lectionner le(s) groupe(s) qui ne pourront pas modifier leur adresse IP au cours d'une session.<br />(Conseil : recommand&eacute; pour les administrateurs.)");
define($constpref."_SAN_NULLBYTE","Sanitiser les bits null");
define($constpref."_SAN_NULLBYTEDSC","Le caract&egrave;re de terminaison '\\0' est souvent utilis&eacute; dans des attaques malveillantes.<br />un bit null sera transform&eacute; en espace .<br />(Conseil : forte recommandation d'activer cette option)");
define($constpref."_DIE_NULLBYTE","Ejecter si des bits null sont trouv&eacute;s");
define($constpref."_DIE_NULLBYTEDSC","Le caract&egrave;re de terminaison '\\0' est employ&eacute; souvent dans des attaques malveillantes.<br />(Conseil : forte recommandation d'activer cette option)");
define($constpref."_DIE_BADEXT","Ejecter si des fichiers interdits sont upload&eacute;s");
define($constpref."_DIE_BADEXTDSC","Si quelqu'un tente d'uploader des fichiers avec une extension non souhait&eacute;e comme .php , il sera &eacute;ject&eacute;.<br />(Conseil : si vous attachez r&eacute;guli&egrave;rement des fichiers php dans B-Wiki ou PukiWikiMod, n'activez pas cette option.)");
define($constpref."_CONTAMI_ACTION","Action si une contamination est trouv&eacute;e");
define($constpref."_CONTAMI_ACTIONDS","S&eacute;lectionner une action lorsque quelqu'un essaiera de contaminer des variables globales syst&egrave;me XOOPS.<br />(Conseil : l'option &eacute;cran blanc est recommand&eacute;)");
define($constpref."_ISOCOM_ACTION","Action si un commentaire isol&eacute; est d&eacute;tect&eacute;");
define($constpref."_ISOCOM_ACTIONDSC","Anti SQL Injection:<br />S&eacute;lectionner l'action &agrave; effectuer quand '/*' est trouv&eacute;.<br />'Sanitiser' signifie ajouter d'autres '*/' &agrave; la suite.<br />(Conseil : 'sanitiser')");
define($constpref."_UNION_ACTION","Action si une requ&ecirc;te UNION est d&eacute;tect&eacute;e ");
define($constpref."_UNION_ACTIONDSC","Anti SQL Injection:<br />S&eacute;lectionner l'action &agrave; effectuer quand il y a syntaxe d'UNION de SQL. <br />'Sanitiser' signifie Changer 'union' en 'uni-on'.<br />(Conseil : 'sanitiser')");
define($constpref."_ID_INTVAL","Transformation forc&eacute;e en nombre entier (intval) de variables comme ID");
define($constpref."_ID_INTVALDSC","Tous les appels '*id' seront trait&eacute;s comme un nombre entier. Cette option vous prot&egrave;gera contre certaines attaques XSS et injections SQL.<br />(Conseil : activer cette option, cependant celle-ci peut perturber le fonctionnement de certains modules.)");
define($constpref."_FILE_DOTDOT","Protection contre la travers&eacute;e de r&eacute;pertoires");
define($constpref."_FILE_DOTDOTDSC","Elimination de « .. » pour toutes les demandes qui ressemblent &agrave; une tentative d'acc&egrave;s par travers&eacute;e de r&eacute;pertoires");

define($constpref."_BF_COUNT","Anti Brute Force");
define($constpref."_BF_COUNTDSC","D&eacute;finit le nombre de tentatives de connexion autoris&eacute;es pour un invit&eacute; dans un intervale de 10 minutes. Si quelqu'un &eacute;choue dans sa tentative au del&agrave; de ce nombre, son adresse IP sera bannie.");

define($constpref."_DOS_SKIPMODS","Modules &agrave; exclure du contr&ocirc;le DoS (F5)/Crawler");
define($constpref."_DOS_SKIPMODSDSC","Mettre les noms des r&eacute;pertoires des modules s&eacute;par&eacute;s par |. Cette option sera utile avec les modules de chat par exemple.");

define($constpref."_DOS_EXPIRE","D&eacute;lai, en secondes, de r&eacute;action aux rechargements fr&eacute;quents de page (attaque 'touche F5')");
define($constpref."_DOS_EXPIREDSC","Dur&eacute;e admise en sec. pour les tentatives par rechargement de page (attaque 'touche F5') et les aspirateurs.");

define($constpref."_DOS_F5COUNT","Nombre autoris&eacute; de tentatives F5 ");
define($constpref."_DOS_F5COUNTDSC","D&eacute;fense contre des attaques DoS :<br/>Cette valeur d&eacute;termine le nombre de rechargement au del&agrave; duquel la connexion est consid&eacute;r&eacute;e comme une attaque malicieuse.");
define($constpref."_DOS_F5ACTION","Action si une attaque F5 est d&eacute;tect&eacute;e");

define($constpref."_DOS_CRCOUNT","Nombre de fois pour qu'un crawler soit reconnu comme malicieux");
define($constpref."_DOS_CRCOUNTDSC","D&eacute;fense contre des crawlers-aspirateurs malicieux (comme bots chasseurs d'e-mails):<br/>La valeur d&eacute;termine le nombre d'acc&egrave;s au del&agrave; duquel le crawler est consid&eacute;r&eacute; comme malicieux.");
define($constpref."_DOS_CRACTION","Action si des crawlers malicieux sont d&eacute;tect&eacute;s");

define($constpref."_DOS_CRSAFE","User-Agent autoris&eacute;s");
define($constpref."_DOS_CRSAFEDSC","Regex Perl pour les User-Agents.<br /> Si il coincide, le crawler n'est plus consid&eacute;r&eacute; comme un aspirateur.<br/> Ex.: msnbot|Googlebot|Yahoo! Slurp");

define($constpref."_OPT_NONE","Aucune (enregistrer seulement)");
define($constpref."_OPT_SAN","Sanitiser");
define($constpref."_OPT_EXIT","Ecran blanc");
define($constpref."_OPT_BIP","Bannir l'IP (ind&eacute;finiment)");
define($constpref."_OPT_BIPTIME0","Bannir l'IP (temporairement)");

define($constpref."_DOSOPT_NONE","Aucune (enregistrer seulement)");
define($constpref."_DOSOPT_SLEEP","Sleep");
define($constpref."_DOSOPT_EXIT","Ecran blanc");
define($constpref."_DOSOPT_BIP","Bannir l'IP (ind&eacute;finiment)");
define($constpref."_DOSOPT_BIPTIME0","Bannir l'IP (temporairement)");;
define($constpref."_DOSOPT_HTA","DENY by .htaccess(Experimental)");

define($constpref."_BIP_EXCEPT","Groupes &agrave; ne jamais enregistrer comme IP bannie");
define($constpref."_BIP_EXCEPTDSC","Un membre qui appartient au groupe sp&eacute;cifi&eacute; ne sera jamais banni.<br />(Conseil : recommand&eacute; pour les administrateurs)");

define($constpref."_DISABLES","D&eacute;sactiver les fonctions dangereuses dans XOOPS");

define($constpref."_BIGUMBRELLA","Activer la protection anti-XSS (Big Umbrella)");
define($constpref."_BIGUMBRELLADSC","Ceci vous prot&egrave;ge contre presque toutes les attaques par l'interm&eacute;diaire des vuln&eacute;rabilit&eacute;s de XSS. Mais il n'est pas sur &agrave; 100%");

define($constpref."_SPAMURI4U","anti-SPAM: nombre d'URLs pour les membres");
define($constpref."_SPAMURI4UDSC","Si un nombre &eacute;quivalent (ou sup&eacute;rieur) d'URLs est trouv&eacute; dans les donn&eacute;es d'un POST par un membre qui n'est pas administrateur, le POST sera consid&eacute;r&eacute; comme du SPAM. 0 &eacute;quivaut &agrave; d&eacute;sactiver cette fonction.");
define($constpref."_SPAMURI4G","anti-SPAM: nombre d'URLs pour les anonymes");
define($constpref."_SPAMURI4GDSC","Si un nombre &eacute;quivalent (ou sup&eacute;rieur) d'URLs est trouv&eacute; dans les donn&eacute;es d'un POST par un visiteur anonyme, le POST sera consid&eacute;r&eacute; comme du SPAM. 0 &eacute;quivaut &agrave; d&eacute;sactiver cette fonction.");

}

?>
