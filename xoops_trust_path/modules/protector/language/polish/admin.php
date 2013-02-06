<?php

// mymenu


// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:52
define('_AM_ADV_DBFACTORYPATCHED','Your databasefactory is ready for DBLayer Trapping anti-SQL-Injection');
define('_AM_ADV_DBFACTORYUNPATCHED','Your databasefactory is not ready for DBLayer Trapping anti-SQL-Injection. Some patches are required.');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-03 11:47:19
define('_AM_ADV_TRUSTPATHPUBLIC','If you can look an image -NG- or the link returns normal page, your XOOPS_TRUST_PATH is not placed properly. The best place for XOOPS_TRUST_PATH is outside of DocumentRoot. If you cannot do that, you have to put .htaccess (DENY FROM ALL) just under XOOPS_TRUST_PATH as the second best way.');
define('_AM_ADV_TRUSTPATHPUBLICLINK','Check php files inside TRUST_PATH are private (it must be 404,403 or 500 error');

define('_MD_A_MYMENU_MYTPLSADMIN','');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Uprawnienia');
define('_MD_A_MYMENU_MYPREFERENCES','Preferencje');

// index.php
define("_AM_TH_DATETIME","Data");
define("_AM_TH_USER","U¿ytkownik");
define("_AM_TH_IP","IP");
define("_AM_TH_AGENT","Przegl±darka");
define("_AM_TH_TYPE","Typ");
define("_AM_TH_DESCRIPTION","Szczegó³y");

define( "_AM_TH_BADIPS" , 'Zbanowane IP<br /><br /><span style="font-weight:normal;">Wpisz ka¿de IP w osobnej linii.<br />Pozostaw puste aby wy³±czyæ blokowanie IP.</span>' ) ;

define( "_AM_TH_GROUP1IPS" , 'Dozwolone IP dla grupy=1<br /><br /><span style="font-weight:normal;">Wpisz ka¿de IP w osobnej linii.<br />192.168. oznacza 192.168.*</span>' ) ;

define( "_AM_LABEL_COMPACTLOG" , "Compact log" ) ;
define( "_AM_BUTTON_COMPACTLOG" , "Compact it!" ) ;
define( "_AM_JS_COMPACTLOGCONFIRM" , "Duplicated (IP,Type) records will be removed" ) ;
define( "_AM_LABEL_REMOVEALL" , "Remove all records" ) ;
define( "_AM_BUTTON_REMOVEALL" , "Remove all!" ) ;
define( "_AM_JS_REMOVEALLCONFIRM" , "All logs are removed absolutely. Are you really OK?" ) ;
define( "_AM_LABEL_REMOVE" , "Usuñ zaznaczone wpisy:" ) ;
define( "_AM_BUTTON_REMOVE" , "Usuñ!" ) ;
define( "_AM_JS_REMOVECONFIRM" , "Na pewno?" ) ;
define( "_AM_MSG_IPFILESUPDATED" , "Pliki z adresami IP zosta³y uaktualnione" ) ;
define( "_AM_MSG_BADIPSCANTOPEN" , "Plik z zablokowanymi adresami IP nie mo¿e zostaæ odczytany" ) ;
define( "_AM_MSG_GROUP1IPSCANTOPEN" , "Plik z adresami IP dla grupy=1 nie mo¿e zostaæ odczytany" ) ;
define( "_AM_MSG_REMOVED" , "Zaznaczone wpisy zosta³y usuniête" ) ;
define( "_AM_FMT_CONFIGSNOTWRITABLE" , "Nadaj prawa zapisu dla katalogu: %s" ) ;


// prefix_manager.php
define( "_AM_H3_PREFIXMAN" , "Manager prefixu" ) ;
define( "_AM_MSG_DBUPDATED" , "Baza danych zosta³a uaktualniona!" ) ;
define( "_AM_CONFIRM_DELETE" , "Wszystkie dane zostan± zrzucone. OK?" ) ;
define( "_AM_TXT_HOWTOCHANGEDB" , "Je¶li chcesz zmieniæ prefix w bazie,<br /> wyedytuj %s/mainfile.php za pomoc± dowolnego edytora.<br /><br />define('XOOPS_DB_PREFIX', '<b>%s</b>');" ) ;


// advisory.php
define("_AM_ADV_NOTSECURE","Niebezpieczne");

define("_AM_ADV_REGISTERGLOBALS","Takie ustawienie pozwala na wiele ataków typu injections.<br />Je¶li to mo¿liwe umie¶æ plik .htaccess, wyedytuj lub utwórz...");
define("_AM_ADV_ALLOWURLFOPEN","To ustawienie pozwala na wykonanie niechcianych skryptów na zdalnych serwerach.<br />tylko administrator serwera mo¿e zmieniæ t± opcje.<br />Je¿eli nim jeste¶, wyedytuj php.ini lub httpd.conf.<br /><b>Przyk³ad edycji httpd.conf:<br /> &nbsp; php_admin_flag &nbsp; allow_url_fopen &nbsp; off</b><br />Je¶li nie jeste¶ adminem serwera, popro¶ go o to!.");
define("_AM_ADV_USETRANSSID","Twoje ID sesji bêdzie widoczne w tagach odno¶ników.<br />Aby zabezpieczyæ siê przed kradzie¿± sesji, dodaj nastêpuj±c± liniê w pliku .htaccess w katalogu XOOPS_ROOT_PATH.<br /><b>php_flag session.use_trans_sid off</b>");
define("_AM_ADV_DBPREFIX","Takie ustawienie pozwala na atak typu 'SQL Injections'.<br />Nie zapomnij uaktywniæ w ustawieniach opcji 'Wymuszone czyszczanie *'.");
define("_AM_ADV_LINK_TO_PREFIXMAN","Przejd¼ do managera prefixu");
define("_AM_ADV_MAINUNPATCHED","Powiniene¶ wyedytowaæ plik mainfile.php tak jak napisano w pliku README.");

define("_AM_ADV_SUBTITLECHECK","Sprawd¼, czy Protector jest skuteczny.");
define("_AM_ADV_CHECKCONTAMI","Zanieczyszczenie danych");
define("_AM_ADV_CHECKISOCOM","Odseparowanie komentarzy");



?>