<?php

// mymenu



// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:53
define('_AM_ADV_DBFACTORYPATCHED','Your databasefactory is ready for DBLayer Trapping anti-SQL-Injection');
define('_AM_ADV_DBFACTORYUNPATCHED','Your databasefactory is not ready for DBLayer Trapping anti-SQL-Injection. Some patches are required.');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-03 11:47:20
define('_AM_ADV_TRUSTPATHPUBLIC','If you can look an image -NG- or the link returns normal page, your XOOPS_TRUST_PATH is not placed properly. The best place for XOOPS_TRUST_PATH is outside of DocumentRoot. If you cannot do that, you have to put .htaccess (DENY FROM ALL) just under XOOPS_TRUST_PATH as the second best way.');
define('_AM_ADV_TRUSTPATHPUBLICLINK','Check php files inside TRUST_PATH are private (it must be 404,403 or 500 error');

// Appended by Xoops Language Checker -GIJOE- in 2007-10-18 05:36:25
define('_AM_LABEL_COMPACTLOG','Compact log');
define('_AM_BUTTON_COMPACTLOG','Compact it!');
define('_AM_JS_COMPACTLOGCONFIRM','Duplicated (IP,Type) records will be removed');
define('_AM_LABEL_REMOVEALL','Remove all records');
define('_AM_BUTTON_REMOVEALL','Remove all!');
define('_AM_JS_REMOVEALLCONFIRM','All logs are removed absolutely. Are you really OK?');

define('_MD_A_MYMENU_MYTPLSADMIN','');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Permissions');
define('_MD_A_MYMENU_MYPREFERENCES','Preferences');

// index.php
define("_AM_TH_DATETIME","Date");
define("_AM_TH_USER","Membre");
define("_AM_TH_IP","IP");
define("_AM_TH_AGENT","Agent");
define("_AM_TH_TYPE","Type");
define("_AM_TH_DESCRIPTION","Description");

define( "_AM_TH_BADIPS" , 'IPs bannies<br /><br /><span style="font-weight:normal;">Ecrivez chaque ip sur une ligne.<br />Ne rien mettre signifie que toutes les IPs sont autoris&eacute;es</span>' ) ;

define( "_AM_TH_GROUP1IPS" , 'IPs autoris&eacute;es pour le groupe administrateurs<br /><br /><span style="font-weight:normal;">Ecrivez chaque ip sur une ligne.<br />192.168. signifie 192.168.*<br />Ne rien mettre signifie que toutes les IPs sont autoris&eacute;es</span>' ) ;

define( "_AM_LABEL_REMOVE" , "Supprimer les enregistrements coch&eacute;s :" ) ;
define( "_AM_BUTTON_REMOVE" , "Supprimer!" ) ;
define( "_AM_JS_REMOVECONFIRM" , "Confirmation de la suppression ?" ) ;
define( "_AM_MSG_IPFILESUPDATED" , "Les fichiers pour IPs ont &eacute;t&eacute; mis &agrave; jour" ) ;
define( "_AM_MSG_BADIPSCANTOPEN" , "Le fichier des IPs bannies ne peut &ecirc;tre ouvert" ) ;
define( "_AM_MSG_GROUP1IPSCANTOPEN" , "Le fichier pour le groupe=1 ne peut &ecirc;tre ouvert" ) ;
define( "_AM_MSG_REMOVED" , "Les enregistrements ont &eacute;t&eacute; supprim&eacute;s" ) ;
define( "_AM_FMT_CONFIGSNOTWRITABLE" , "Configurez le r&eacute;pertoire en lecture-&eacute;criture : %s" ) ;


// prefix_manager.php
define( "_AM_H3_PREFIXMAN" , "Gestionnaire de pr&eacute;fixe" ) ;
define( "_AM_MSG_DBUPDATED" , "Base de donn&eacute;es mise &agrave; jour avec succ&egrave;s!" ) ;
define( "_AM_CONFIRM_DELETE" , "Toutes les donn&eacute;es vont &ecirc;tre supprim&eacute;es. OK ?" ) ;
define( "_AM_TXT_HOWTOCHANGEDB" , "Si vous voulez changer le pr&eacute;fixe, &eacute;diter manuellement le fichier %s/mainfile.php pour cette ligne :<br />  define('XOOPS_DB_PREFIX', '<b>%s</b>');" ) ;


// advisory.php
define("_AM_ADV_NOTSECURE","Non s&eacute;curis&eacute;");

define("_AM_ADV_REGISTERGLOBALS","Ce param&egrave;tre attire une vari&eacute;t&eacute; d'attaques par injection.<br />Si vous pouvez, mettez un fichier .htaccess (&eacute;ditez le ou cr&eacute;ez le selon le cas...");
define("_AM_ADV_ALLOWURLFOPEN","Ce param&egrave;tre permet l'ex&eacute;cution de scripts arbitraires sur des serveurs distants.<br />Seul l'administrateur du serveur peut changer cette option.<br />Si vous &ecirc;tes dans ce cas, &eacute;ditez php.ini ou httpd.conf.<br /><b>Exemple pour httpd.conf:<br /> &nbsp; php_admin_flag &nbsp; allow_url_fopen &nbsp; off</b><br />Sinon demandez le &agrave; vos administrateurs.");
define("_AM_ADV_USETRANSSID","Votre ID de session est affich&eacute; dans les balises ancre etc.<br />Pour pr&eacute;venir des vols de sessions (hi-jacking&agrave;, ajouter cette ligne &agrave; votre .htaccess situ&eacute; dans le XOOPS_ROOT_PATH.<br /><b>php_flag session.use_trans_sid off</b>");
define("_AM_ADV_DBPREFIX","Ce param&egrave;tre attire les 'injections SQL'.<br />N'oubliez pas d'activer l'option 'Forcer la sanitisation *' dans les pr&eacute;f&eacute;rences du module.");
define("_AM_ADV_LINK_TO_PREFIXMAN","Allez au gestionnaire de pr&eacute;fixe");
define("_AM_ADV_MAINUNPATCHED","Vous devez modifiez votre fichier mainfile.php comme &eacute;crit dans le fichier README.");

define("_AM_ADV_SUBTITLECHECK","Contr&ocirc;le de l'action de Protector");
define("_AM_ADV_CHECKCONTAMI","Contaminations");
define("_AM_ADV_CHECKISOCOM","Commentaires isol&eacute;s");



?>
