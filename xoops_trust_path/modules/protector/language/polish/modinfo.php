<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {






// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:56
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

// Appended by Xoops Language Checker -GIJOE- in 2009-07-06 05:46:52
define($constpref.'_DBTRAPWOSRV','Never checking _SERVER for anti-SQL-Injection');
define($constpref.'_DBTRAPWOSRVDSC','Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:52
define($constpref.'_DBLAYERTRAP','Enable DB Layer trapping anti-SQL-Injection');
define($constpref.'_DBLAYERTRAPDSC','Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page.');

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:30
define($constpref.'_DEFAULT_LANG','Default language');
define($constpref.'_DEFAULT_LANGDSC','Specify the language set to display messages before processing common.php');
define($constpref.'_BWLIMIT_COUNT','Bandwidth limitation');
define($constpref.'_BWLIMIT_COUNTDSC','Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

// Appended by Xoops Language Checker -GIJOE- in 2007-11-13 03:43:32
define($constpref.'_BANIP_TIME0','Banned IP suspension time (sec)');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Modu� zabezpieczaj�cy Xoopsa, przed r�nymi
rodzajami atak�w z sieci, takich jak: DoS , SQL Injection i ska�eniem
zmiennych.");

// Menu
define($constpref."_ADMININDEX","Centrum zabezpiecze�");
define($constpref."_ADVISORY","Porady nt. bezpiecze�stwa");
define($constpref."_PREFIXMANAGER","Menad�er prefiksu");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Uprawnienia') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Tymczasowo wy��czony');
define($constpref.'_GLOBAL_DISBLDSC','Mo�esz czasowo wy��czy� Protectora, je�li masz jakie� problemy z jego funcjonowaniem. Nie zapomnij w��czy� go na powr�t, gdy ju� naprawisz problem. Domy�lnie ustawiony na nie.');

define($constpref.'_RELIABLE_IPS','IP godne zaufania');
define($constpref.'_RELIABLE_IPSDSC','Wpisz numery IP, kt�re uznajesz za godne zaufania np. swoje w�asne. Te IP nie b�d� banowane przez Protectora, dzi�ki czemu uchronisz si� przed zablokowaniem dost�pu dla siebie. Poszczeg�lne numery IP oddzielaj pionow� kresk�. ^ zast�puje pocz�tek numeru, $ zast�puje koniec numeru.');
define($constpref.'_LOG_LEVEL','Poziom logowania');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_LOGLEVEL0','�aden');
define($constpref.'_LOGLEVEL15','Ukryty');
define($constpref.'_LOGLEVEL63','Cichy (bardziej ni� ukryty).');
define($constpref.'_LOGLEVEL255','Pe�ny');

define($constpref.'_HIJACK_TOPBIT','Chronione bity numeru IP w sesji');
define($constpref.'_HIJACK_TOPBITDSC','Ta funkcja chroni przed przechwytywaniem sesji, ograniczaj�c ilo�� bit�w IP, kt�re mog� si� zmieni� w trakcie sesji. Domy�lnie 32 bit�w - wszystkie bity chronione (IP nie mo�e si� zmieni�). Je�li masz dynamiczne IP, zmieniaj�ce si� w okre�lonym zakresie, mo�esz ustawi� ilo�� chronionych bit�w tak, by mniej wi�cej dopasowa� do zakresu. Na przyk�ad, je�li twoje IP mo�e si� zmienia� w zakresie od 192.168.0.0 do 192.168.0.255, ustaw 24 bity. Gdyby cracker zna� IP twojej sesji, ale pr�bowa� si� wedrze� spoza tego zakresu (powiedzmy, z 192.168.2.50), nie uda mu si�. Autor modu�u sugeruje warto�� 16 bit�w jako optymaln� dla og�lnego u�ycia.');
define($constpref.'_HIJACK_DENYGP','Grupy nieuprawnione do zmieniania
swojego IP w trakcie sesji');
define($constpref.'_HIJACK_DENYGPDSC','Wska�nik chroni�cy przed przechwytywaniem. Wybrane grupy nie mog� zmienia� IP w trakcie trwania sesji. Domy�lnie wymienia grup� webmasters i poleca si� tego nie zmienia�, bo konsekwencje przechwycenia sesji administratora mog�yby by� naprawd� powa�ne.)');
define($constpref.'_SAN_NULLBYTE','Sterylizowanie pustych bajt�w');
define($constpref.'_SAN_NULLBYTEDSC','Znak ko�cz�cy "\\0" jest cz�sto u�ywany we wrogich atakach. Pusty bajt zmieni si� w spacj�, je�li ta opcja jest w��czona (co jest domy�lne, i stanowczo poleca si� pozostawi� j� w��czon�).');
define($constpref.'_DIE_NULLBYTE','Wyjd� je�li stwierdzone zostan�
puste bajty');
define($constpref.'_DIE_NULLBYTEDSC','Znak zako�czenia "\\0" jest zwykle u�ywany podczas atak-u na serwisy.<br />(nale�y suatwi� t� opcj� w��czon�)');
define($constpref.'_DIE_BADEXT','Wyjd� je�li wgrywane s� podejrzane
pliki (tak/nie)');
define($constpref.'_DIE_BADEXTDSC','Je�li kto� pr�buje wgra� pliki z niebezpiecznymi rozszerzeniami, jak .php ,Protector zamknie XOOPSa. Je�li cz�sto do��czasz pliki php do B-Wiki albo PukiWikiMod, by� mo�e b�dziesz musia� wy��czy� t� funkcj�.');
define($constpref.'_CONTAMI_ACTION','Dzia�anie w przypadku wykrycia
pr�by ska�enia zmiennych');
define($constpref.'_CONTAMI_ACTIONDS','Wybierz dzia�anie, jakie ma by� podj�te, gdy kto� pr�buje skazi� globalne zmienne systemu w Twoim XOOPSie. Mo�liwo�ci:)');
define($constpref.'_ISOCOM_ACTION','Dzia�anie w przypadku wykrycia
izolowanego otwarcia komentarza.');
define($constpref.'_ISOCOM_ACTIONDSC','Ochrona przed ska�eniem SQL. Okre�l dzia�anie wobec znalezienia izolowanego "/*". Mo�liwo�ci:');
define($constpref.'_UNION_ACTION','Dzia�anie w przypadku wykrycia pr�by dodania instrukcji UNION lub podobnej.');
define($constpref.'_UNION_ACTIONDSC','Ochrona przed ska�eniem SQL. Okre�l dzia�anie wobec znalezienia sk�adni UNION w SQL. Mo�liwo�ci:');
define($constpref.'_ID_INTVAL','Wymuszanie liczby ca�kowitej dla zapyta� zawieraj�cych zmienne typu id');
define($constpref.'_ID_INTVALDSC','Ta opcja mia�a chroni� przed problemem w starszej wersji modu�u weblog. Teraz ten b��d zosta� naprawiony.<br />Wszystkie ��dania z nazwami takimi jak "*id" b�d� traktowane jak liczby ca�kowite. Ta opcja chroni przed niekt�rymi rodzajami atak�w XSS i SQL. Poleca si� j� w��czy�, cho� mo�e si� zdarzy�, �e b�dzie powodowa� problemy z niekt�rymi modu�ami. Domy�lnie ustawiona na off.');
define($constpref.'_FILE_DOTDOT','Ochrona przed w�amywaniem si� do folder�w');
define($constpref.'_FILE_DOTDOTDSC','Ta funkcja eliminuje ".." z wszystkich zapyta�, kt�re wygl�daj� na pr�by w�amywania si� do folder�w. Mo�liwe opcje to w��czenie (tak) lub wy��czenie (nie). Domy�lnie ustawiona na on (w��czone).');

define($constpref.'_BF_COUNT','Ochrona przed atakami na si�� (Brute Force)');
define($constpref.'_BF_COUNTDSC','Tutaj mo�esz okre�li� ilo�� dopuszczalnych pr�b zalogowania w ci�gu 10 minut. Je�li kto� poda z�e dane wi�cej razy, ni� wynosi limit, jego IP zostanie zbanowane. Ta funkcja chroni przed pr�bami z�amania hase� dost�pu metod� pr�b i b��d�w. Domy�lnie ustawiona warto�� wynosi 10.');

define($constpref.'_DOS_SKIPMODS','Modu�y wy��czone z ochrony przed
DoS/Crawler');
define($constpref.'_DOS_SKIPMODSDSC','Protector mo�e banowa� IP inicjuj�ce ataki DoS lub robaki, kt�re zabieraj� du�e zasoby (patrz ni�ej). Mo�esz jednak wy��czy� poszczeg�lne modu�y z tej ochrony, wpisuj�c tutaj nazwy ich katalog�w. Kolejne modu�y oddzielaj pionow� kresk�. Funkcja przydaje si� do modu��w takich jak np. czat.');
define($constpref.'_DOS_EXPIRE','Czas dozorowania masowych od�wie�a� (w sek.)');
define($constpref.'_DOS_EXPIREDSC','Ta warto�� okre�la czas obserwowania licznych/cz�stych od�wie�a� (atak F5) i robak�w zajmuj�cych transfer. Domy�lnie 60 sekund. .');

define($constpref.'_DOS_F5COUNT','Pr�g dla atak�w F5');
define($constpref.'_DOS_F5COUNTDSC','Funkcja przeciwko atakom DoS. Wpisana warto�� okre�la liczb� od�wie�e� (w okresie czasu dozorowania wpisanego powy�ej), jaka musi by� wykonana, zanim dane IP zostanie uznane za przeprowadzaj�ce wrogi atak. Domy�lnie: 10.');
define($constpref.'_DOS_F5ACTION','Dzia�anie w obliczu ataku F5');

define($constpref.'_DOS_CRCOUNT','Pr�g dla robak�w');
define($constpref.'_DOS_CRCOUNTDSC','Funkcja ochrony przed robakami konsumuj�cymi zasoby i botami. Wpisana tutaj warto�� okre�la ilo�� pr�b dost�pu, powy�ej kt�rej robak zostaje uznany za �le zachowuj�cego si�, tzn. zajmuj�cego zbyt wiele zasob�w. Domy�lnie 30 od�wie�e�.');
define($constpref.'_DOS_CRACTION','Dzia�anie przeciwko robakom konsumuj�cym');

define($constpref.'_DOS_CRSAFE','Roboty indeksuj�ce wy��czone spod kontroli');
define($constpref.'_DOS_CRSAFEDSC','Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','�adne (tylko log).');
define($constpref.'_OPT_SAN','Naprawa');
define($constpref.'_OPT_EXIT','Bia�a Strona/Pusty ekran');
define($constpref.'_OPT_BIP','Banuj IP');
define($constpref.'_OPT_BIPTIME0','Banuj IP (moratorium)');

define($constpref.'_DOSOPT_NONE','�adne (tylko log).');
define($constpref.'_DOSOPT_SLEEP','U�pienie');
define($constpref.'_DOSOPT_EXIT','Bia�y Ekran');
define($constpref.'_DOSOPT_BIP','Banuj IP');
define($constpref.'_DOSOPT_BIPTIME0','Banuj IP (moratorium)');
define($constpref.'_DOSOPT_HTA','Odrzu� przez .htaccess (funkcja w fazie eksperymentalnej)');

define($constpref.'_BIP_EXCEPT','Grupy, kt�rych IP nigdy nie zostanie zakwalifikowane jako z�e');
define($constpref.'_BIP_EXCEPTDSC','U�ytkownik nale��cy do wymienionych tutaj grup nigdy nie zostanie zbanowany. Domy�lnie wpisana grupa webmasters, i zaleca si� tak zostawi�.');

define($constpref.'_DISABLES','Wy��cz niebezpieczne funkcje XOOPSa');

define($constpref.'_BIGUMBRELLA','W��cz anti-XSS (BigUmbrella) ');
define($constpref.'_BIGUMBRELLADSC','Ta funkcja chroni przed niekt�rymi atakami XSS (cross-site scripting). Nie ma jednak 100% skuteczno�ci. Domy�lnie ustawiona na nie (off), w��czenie jej to raczej niez�y pomys�.');

define($constpref.'_SPAMURI4U','anti-SPAM: ilo�� adres�w URL dla normalnych u�ytkownik�w ');
define($constpref.'_SPAMURI4UDSC','Mo�esz okre�li� dozwolon� liczb� adres�w URL zawartych w danych formularza POST dla zarejestrowanych u�ytkownik�w (np. w postach na forum i komentarzach), nie b�d�cych administratorami. Je�li POST zawiera zbyt wiele adres�w URL, zostanie uznany za spam. Domy�lnie: 10. Je�li chcesz wy��czy� t� funkcj�, ustaw warto�� 0. ');
define($constpref.'_SPAMURI4G','anti-SPAM: ilo�� adres�w URL dla go�ci');
define($constpref.'_SPAMURI4GDSC','Jak wy�ej, ale dla anonimowych u�ytkownik�w (go�ci). Domy�lnie: 5. Wpisz 0 je�li chcesz wy��czy� t� funkcj�.');

}

?>
