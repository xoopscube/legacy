<?php
// $Id: global.php,v 1.1 2008/07/05 08:25:13 minahito Exp $

define('_TOKEN_ERROR', 'Varování ! Toto je prevence pøed pøípadnými poruchami èi poškozeními. Prosím, znovu potvrïte!');
define('_SYSTEM_MODULE_ERROR', 'Následující moduly nejsou nainstalovány.');
define('_INSTALL','Nainstalovat');
define('_UNINSTALL','Odinstalovat');
define('_SYS_MODULE_UNINSTALLED','Povinný (není instalován)');
define('_SYS_MODULE_DISABLED','Povinný (Vypnut)');
define('_SYS_RECOMMENDED_MODULES','Doporuèený modul');
define('_SYS_OPTION_MODULES','Volitelný modul');
define('_UNINSTALL_CONFIRM','Pøejete si odinstalovat systémový modul?');

//%%%%%%	File Name mainfile.php 	%%%%%
define("_PLEASEWAIT","Èekejte prosím");
define("_FETCHING","Nahrávám...");
define("_TAKINGBACK","Zpìt na pøedchozí stránku....");
define("_LOGOUT","Odhlásit");
define("_SUBJECT","Pøedmìt");
define("_MESSAGEICON","Ikona");
define("_COMMENTS","Komentáøe");
define("_POSTANON","Poslat anonymnì");
define("_DISABLESMILEY","Zakázat smajlíky");
define("_DISABLEHTML","Zakázat HTML");
define("_PREVIEW","Náhled");

define("_GO","Uložit");
define("_NESTED","Vložené");
define("_NOCOMMENTS","Nejsou komentáøe");
define("_FLAT","Ploché");
define("_THREADED","Strom");
define("_OLDESTFIRST","Starší døíve");
define("_NEWESTFIRST","Novìjší døíve");
define("_MORE","více...");
define("_MULTIPAGE","Aby byl Váš èlánek rozložen na více stránek, vložte na pøíslušné místo <b><font color=red>[pagebreak]</font></b> (vèetnì hranatých závorek).");
define("_IFNOTRELOAD","Pokud nebudete automaticky pøesmìrováni bìhem nìkolika sekund,<br />kliknìte prosím <a href='%s'>zde</a>");
define("_WARNINSTALL2","POZOR: Adresáø <b>%s</b> ještì existuje na vašem serveru. <br />Z bezpeènostních dùvodù doporuèujeme jej smazat.");
define("_WARNINWRITEABLE","POZOR: Soubor %s je pøístupný pro zápis serverem. Zmìòte práva pøístupu k tomuto souboru. na Unixu (444), na Win32 (read-only)");
define('_WARNPHPENV','POZOR: Parametr v php.ini "%s" je nastaven na "%s". %s');
define('_WARNSECURITY','(Mùže to být bezpeènostní problém)');

//%%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Profil");
define("_POSTEDBY","Zasláno od");
define("_VISITWEBSITE","Navštívit stránku");
define("_SENDPMTO","Poslat soukromou zprávu pro %s");
define("_SENDEMAILTO","Poslat email pro %s");
define("_ADD","Pøidat");
define("_REPLY","Odpovìï");
define("_DATE","Datum");   // Posted date

//%%%%%%	File Name admin_functions.php 	%%%%%
define("_MAIN","Hlavní");
define("_MANUAL","Manuál");
define("_INFO","Info");
define("_CPHOME","Panel administrace");
define("_YOURHOME","Hlavní stránka");

//%%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Kdo je pøítomen");
define('_GUESTS', 'Návštìvníci');
define('_MEMBERS', 'Reg. uživatelé');
define("_ONLINEPHRASE","Uživatelù online: <b>%s</b><br />");
define("_ONLINEPHRASEX","<b>%s</b> v sekci <b>%s</b>");
define("_CLOSE","Zavøít");  // Close window

//%%%%%%	File Name module.textsanitizer.php 	%%%%%
define("_QUOTEC","Cituji:");

//%%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","Nejste oprávnìni pro vstup do této oblasti.");

//%%%%%		Common Phrases		%%%%%
define("_NO","Ne");
define("_YES","Ano");
define("_EDIT","Upravit");
define("_DELETE","Smazat");
define("_VIEW","Zobrazit");
define("_SUBMIT","Potvrdit");
define("_MODULENOEXIST","Vybraný modul neexistuje!");
define("_ALIGN","Zarovnat");
define("_LEFT","Vlevo");
define("_CENTER","Støed");
define("_RIGHT","Vpravo");
define("_FORM_ENTER", "Vložte %s");
// %s represents file name
define("_MUSTWABLE","Do souboru <b>%s</b> musí mít server právo zápisu!");
// Module info
define('_PREFERENCES', 'Nastavení');
define("_VERSION", "Verze");
define("_DESCRIPTION", "Popis");
define("_ERRORS", "Chyby");
define("_NONE", "Nic");
define('_ON','dne');
define('_READS','otevøení');
define('_WELCOMETO','Vítejte na %s');
define('_SEARCH','Hledat');
define('_ALL', 'Vše');
define('_TITLE', 'Titulek');
define('_OPTIONS', 'Možnosti');
define('_QUOTE', 'Citace');
define('_LIST', 'Seznam');
define('_LOGIN','Pøihlášení');
define('_USERNAME','Uživatel: ');
define('_PASSWORD','Heslo: ');
define("_SELECT","Vybrat");
define("_IMAGE","Obrázek");
define("_SEND","Odeslat");
define("_CANCEL","Storno");
define("_ASCENDING","Vzestupné øazení");
define("_DESCENDING","Sestupné øazení");
define('_BACK', 'Zpìt');
define('_NOTITLE', 'Bez titulku');
define('_RETURN_TOP', 'pøejít na zaèátek stránky');

/* Image manager */
define('_IMGMANAGER','Správce obrázkù');
define('_NUMIMAGES', '<b>%s</b> obrázkù');
define('_ADDIMAGE','Pøidat obrázek');
define('_IMAGENAME','Název:');
define('_IMGMAXSIZE','Max. velikost (kb):');
define('_IMGMAXWIDTH','Max. šíøka (pixely):');
define('_IMGMAXHEIGHT','Max. výška (pixely):');
define('_IMAGECAT','Kategorie:');
define('_IMAGEFILE','Soubor');
define('_IMGWEIGHT','Váha ve správci obrázkù:');
define('_IMGDISPLAY','Zobrazit obrázek?');
define('_IMAGEMIME','MIME type:');
define('_FAILFETCHIMG', 'Nelze získat nahraný soubor %s');
define('_FAILSAVEIMG', 'Selhalo vložení obrázku %s do databáze');
define('_NOCACHE', 'Bez Cache');
define('_CLONE', 'Klonovat');

//%%%%%	File Name class/xoopsform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Zaèíná na");
define("_ENDSWITH", "Konèí na");
define("_MATCHES", "Je rovno");
define("_CONTAINS", "Obsahuje");

//%%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Registrace");

//%%%%%%	File Name xoopscodes.php 	%%%%%
define("_SIZE","VELIKOST");  // font size
define("_FONT","FONT");  // font family
define("_COLOR","BARVA");  // font color
define("_EXAMPLE","NÁHLED");
define("_ENTERURL","URL odkazu, který chcete pøidat:");
define("_ENTERWEBTITLE","Titulek serveru:");
define("_ENTERIMGURL","URL orázku.");
define("_ENTERIMGPOS","Pozice obrázku.");
define("_IMGPOSRORL","'R' nebo 'r' pro 'vpravo', 'L' nebo 'l' pro 'vlevo', nebo nechte prázdné.");
define("_ERRORIMGPOS","CHYBA! Zadejte pozici obrázku.");
define("_ENTEREMAIL","Zadejte emailovou adresu.");
define("_ENTERCODE","Zadejte XOOPS kód.");
define("_ENTERQUOTE","Zadejte text, který chcete citovat.");
define("_ENTERTEXTBOX","Napište text do textového pole.");
define("_ALLOWEDCHAR","Max. poèet znakù: ");
define("_CURRCHAR","Souèasný poèet znakù: ");
define("_PLZCOMPLETE","Vyplòte prosím pøedmìt a text zprávy.");
define("_MESSAGETOOLONG","Vaše zpráva je pøíliš dlouhá.");

//%%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 vteøina');
define('_SECONDS', '%s vteøin');
define('_MINUTE', '1 minuta');
define('_MINUTES', '%s minut');
define('_HOUR', '1 hodina');
define('_HOURS', '%s hodin');
define('_DAY', '1 den');
define('_DAYS', '%s dní');
define('_WEEK', '1 týden');
define('_MONTH', '1 mìsíc');

define('_HELP', "Nápovìda");

?>
