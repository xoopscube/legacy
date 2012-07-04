<?php

// mymenu



// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:54
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
define('_MD_A_MYMENU_MYPREFERENCES','Préférences');

// index.php
define("_AM_TH_DATETIME","Date");
define("_AM_TH_USER","Utilisateur");
define("_AM_TH_IP","IP");
define("_AM_TH_AGENT","Agent");
define("_AM_TH_TYPE","Type");
define("_AM_TH_DESCRIPTION","Description");

define( "_AM_TH_BADIPS" , 'Adresses IP interdites<br /><br /><span style="font-weight:normal;">Ajoutez une adresse IP par ligne.</span><br />Laisser ce champ vide pour autoriser toute adresse IP.' ) ;

define( "_AM_TH_GROUP1IPS" , 'Adresses IP autorisées pour le groupe=1<br /><br /><span style="font-weight:normal;">Ajoutez une IP par ligne.<br />Ex. 192.168. signifie que la régle s\'applique à toute adresse avec le prefixe 192.168.*<br />Laisser ce champ vide pour autoriser toute adresse IP</span>' ) ;

define( "_AM_LABEL_REMOVE" , "Supprimer les enregistrements selectionnés:" ) ;
define( "_AM_BUTTON_REMOVE" , "Supprimer!" ) ;
define( "_AM_JS_REMOVECONFIRM" , "Veuillez confirmer la suppression?" ) ;
define( "_AM_MSG_IPFILESUPDATED" , "Mise à jour des adresses IP" ) ;
define( "_AM_MSG_BADIPSCANTOPEN" , "Accès impossible aux adresses IP interdites" ) ;
define( "_AM_MSG_GROUP1IPSCANTOPEN" , "Accès impossible au fichier du groupe=1" ) ;
define( "_AM_MSG_REMOVED" , "Suppression des messages" ) ;
define( "_AM_FMT_CONFIGSNOTWRITABLE" , "Changez le repertoire configs en mode écriture : %s" ) ;


// prefix_manager.php
define( "_AM_H3_PREFIXMAN" , "Gestion du préfixe" ) ;
define( "_AM_MSG_DBUPDATED" , "Mise à Jour de la BDD avec succès!" ) ;
define( "_AM_CONFIRM_DELETE" , "Supprimer toutes les données?" ) ;
define( "_AM_TXT_HOWTOCHANGEDB" , "Pour changer le préfixe, veuillez éditer dans le fichier %s/mainfile.php la ligne suivante :<br />  define('XOOPS_DB_PREFIX', '<b>%s</b>');" ) ;


// advisory.php
define("_AM_ADV_NOTSECURE","Non sécurisé");

define("_AM_ADV_REGISTERGLOBALS","Protection des attaques par injection.<br />Placez un fichier .htaccess pour limiter l'accès.");
define("_AM_ADV_ALLOWURLFOPEN","Protection contre l\'exécution de scripts sur des serveurs distants.<br />Éditer php.ini ou httpd.conf.<br /><b>Exemple pour httpd.conf:<br /> php_admin_flag  allow_url_fopen off</b><br />Autrement contacter l\'administrateur du serveur.");
define("_AM_ADV_USETRANSSID","Protection contre l\'hijacking ou vol de la clé de votre session.<br />Ajouter la ligne suivante à votre fichier .htaccess dans XOOPS_ROOT_PATH.<br /><b>php_flag session.use_trans_sid off</b>");
define("_AM_ADV_DBPREFIX","Protection contre les Injections SQL.<br />Activer l'option 'Forcer le filtrage *' dans les préférences du module.");
define("_AM_ADV_LINK_TO_PREFIXMAN","Lien pour la gestion du préfixe");
define("_AM_ADV_MAINUNPATCHED","Attention ! Modifier le fichier mainfile.php comme indiquez dans les instructions du README.");
define("_AM_ADV_SUBTITLECHECK","Contrôler Protector");
define("_AM_ADV_CHECKCONTAMI","Contaminations");
define("_AM_ADV_CHECKISOCOM","Commentaires Isolés");



?>
