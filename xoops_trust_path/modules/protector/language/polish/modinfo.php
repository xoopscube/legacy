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
define($constpref."_DESC","Modu³ zabezpieczaj±cy Xoopsa, przed ró¿nymi
rodzajami ataków z sieci, takich jak: DoS , SQL Injection i ska¿eniem
zmiennych.");

// Menu
define($constpref."_ADMININDEX","Centrum zabezpieczeñ");
define($constpref."_ADVISORY","Porady nt. bezpieczeñstwa");
define($constpref."_PREFIXMANAGER","Menad¿er prefiksu");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Uprawnienia') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Tymczasowo wy³±czony');
define($constpref.'_GLOBAL_DISBLDSC','Mo¿esz czasowo wy³±czyæ Protectora, je¶li masz jakie¶ problemy z jego funcjonowaniem. Nie zapomnij w³±czyæ go na powrót, gdy ju¿ naprawisz problem. Domy¶lnie ustawiony na nie.');

define($constpref.'_RELIABLE_IPS','IP godne zaufania');
define($constpref.'_RELIABLE_IPSDSC','Wpisz numery IP, które uznajesz za godne zaufania np. swoje w³asne. Te IP nie bêd± banowane przez Protectora, dziêki czemu uchronisz siê przed zablokowaniem dostêpu dla siebie. Poszczególne numery IP oddzielaj pionow± kresk±. ^ zastêpuje pocz±tek numeru, $ zastêpuje koniec numeru.');
define($constpref.'_LOG_LEVEL','Poziom logowania');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_LOGLEVEL0','¯aden');
define($constpref.'_LOGLEVEL15','Ukryty');
define($constpref.'_LOGLEVEL63','Cichy (bardziej ni¿ ukryty).');
define($constpref.'_LOGLEVEL255','Pe³ny');

define($constpref.'_HIJACK_TOPBIT','Chronione bity numeru IP w sesji');
define($constpref.'_HIJACK_TOPBITDSC','Ta funkcja chroni przed przechwytywaniem sesji, ograniczaj±c ilo¶æ bitów IP, które mog± siê zmieniæ w trakcie sesji. Domy¶lnie 32 bitów - wszystkie bity chronione (IP nie mo¿e siê zmieniæ). Je¶li masz dynamiczne IP, zmieniaj±ce siê w okre¶lonym zakresie, mo¿esz ustawiæ ilo¶æ chronionych bitów tak, by mniej wiêcej dopasowaæ do zakresu. Na przyk³ad, je¶li twoje IP mo¿e siê zmieniaæ w zakresie od 192.168.0.0 do 192.168.0.255, ustaw 24 bity. Gdyby cracker zna³ IP twojej sesji, ale próbowa³ siê wedrzeæ spoza tego zakresu (powiedzmy, z 192.168.2.50), nie uda mu siê. Autor modu³u sugeruje warto¶æ 16 bitów jako optymaln± dla ogólnego u¿ycia.');
define($constpref.'_HIJACK_DENYGP','Grupy nieuprawnione do zmieniania
swojego IP w trakcie sesji');
define($constpref.'_HIJACK_DENYGPDSC','Wska¼nik chroni±cy przed przechwytywaniem. Wybrane grupy nie mog± zmieniaæ IP w trakcie trwania sesji. Domy¶lnie wymienia grupê webmasters i poleca siê tego nie zmieniaæ, bo konsekwencje przechwycenia sesji administratora mog³yby byæ naprawdê powa¿ne.)');
define($constpref.'_SAN_NULLBYTE','Sterylizowanie pustych bajtów');
define($constpref.'_SAN_NULLBYTEDSC','Znak koñcz±cy "\\0" jest czêsto u¿ywany we wrogich atakach. Pusty bajt zmieni siê w spacjê, je¶li ta opcja jest w³±czona (co jest domy¶lne, i stanowczo poleca siê pozostawiæ j± w³±czon±).');
define($constpref.'_DIE_NULLBYTE','Wyjd¼ je¶li stwierdzone zostan±
puste bajty');
define($constpref.'_DIE_NULLBYTEDSC','Znak zakoñczenia "\\0" jest zwykle u¿ywany podczas atak-u na serwisy.<br />(nale¿y suatwiæ t± opcjê w³±czon±)');
define($constpref.'_DIE_BADEXT','Wyjd¼ je¶li wgrywane s± podejrzane
pliki (tak/nie)');
define($constpref.'_DIE_BADEXTDSC','Je¶li kto¶ próbuje wgraæ pliki z niebezpiecznymi rozszerzeniami, jak .php ,Protector zamknie XOOPSa. Je¶li czêsto do³±czasz pliki php do B-Wiki albo PukiWikiMod, byæ mo¿e bêdziesz musia³ wy³±czyæ tê funkcjê.');
define($constpref.'_CONTAMI_ACTION','Dzia³anie w przypadku wykrycia
próby ska¿enia zmiennych');
define($constpref.'_CONTAMI_ACTIONDS','Wybierz dzia³anie, jakie ma byæ podjête, gdy kto¶ próbuje skaziæ globalne zmienne systemu w Twoim XOOPSie. Mo¿liwo¶ci:)');
define($constpref.'_ISOCOM_ACTION','Dzia³anie w przypadku wykrycia
izolowanego otwarcia komentarza.');
define($constpref.'_ISOCOM_ACTIONDSC','Ochrona przed ska¿eniem SQL. Okre¶l dzia³anie wobec znalezienia izolowanego "/*". Mo¿liwo¶ci:');
define($constpref.'_UNION_ACTION','Dzia³anie w przypadku wykrycia próby dodania instrukcji UNION lub podobnej.');
define($constpref.'_UNION_ACTIONDSC','Ochrona przed ska¿eniem SQL. Okre¶l dzia³anie wobec znalezienia sk³adni UNION w SQL. Mo¿liwo¶ci:');
define($constpref.'_ID_INTVAL','Wymuszanie liczby ca³kowitej dla zapytañ zawieraj±cych zmienne typu id');
define($constpref.'_ID_INTVALDSC','Ta opcja mia³a chroniæ przed problemem w starszej wersji modu³u weblog. Teraz ten b³±d zosta³ naprawiony.<br />Wszystkie ¿±dania z nazwami takimi jak "*id" bêd± traktowane jak liczby ca³kowite. Ta opcja chroni przed niektórymi rodzajami ataków XSS i SQL. Poleca siê j± w³±czyæ, choæ mo¿e siê zdarzyæ, ¿e bêdzie powodowaæ problemy z niektórymi modu³ami. Domy¶lnie ustawiona na off.');
define($constpref.'_FILE_DOTDOT','Ochrona przed w³amywaniem siê do folderów');
define($constpref.'_FILE_DOTDOTDSC','Ta funkcja eliminuje ".." z wszystkich zapytañ, które wygl±daj± na próby w³amywania siê do folderów. Mo¿liwe opcje to w³±czenie (tak) lub wy³±czenie (nie). Domy¶lnie ustawiona na on (w³±czone).');

define($constpref.'_BF_COUNT','Ochrona przed atakami na si³ê (Brute Force)');
define($constpref.'_BF_COUNTDSC','Tutaj mo¿esz okre¶liæ ilo¶æ dopuszczalnych prób zalogowania w ci±gu 10 minut. Je¶li kto¶ poda z³e dane wiêcej razy, ni¿ wynosi limit, jego IP zostanie zbanowane. Ta funkcja chroni przed próbami z³amania hase³ dostêpu metod± prób i b³êdów. Domy¶lnie ustawiona warto¶æ wynosi 10.');

define($constpref.'_DOS_SKIPMODS','Modu³y wy³±czone z ochrony przed
DoS/Crawler');
define($constpref.'_DOS_SKIPMODSDSC','Protector mo¿e banowaæ IP inicjuj±ce ataki DoS lub robaki, które zabieraj± du¿e zasoby (patrz ni¿ej). Mo¿esz jednak wy³±czyæ poszczególne modu³y z tej ochrony, wpisuj±c tutaj nazwy ich katalogów. Kolejne modu³y oddzielaj pionow± kresk±. Funkcja przydaje siê do modu³ów takich jak np. czat.');
define($constpref.'_DOS_EXPIRE','Czas dozorowania masowych od¶wie¿añ (w sek.)');
define($constpref.'_DOS_EXPIREDSC','Ta warto¶æ okre¶la czas obserwowania licznych/czêstych od¶wie¿añ (atak F5) i robaków zajmuj±cych transfer. Domy¶lnie 60 sekund. .');

define($constpref.'_DOS_F5COUNT','Próg dla ataków F5');
define($constpref.'_DOS_F5COUNTDSC','Funkcja przeciwko atakom DoS. Wpisana warto¶æ okre¶la liczbê od¶wie¿eñ (w okresie czasu dozorowania wpisanego powy¿ej), jaka musi byæ wykonana, zanim dane IP zostanie uznane za przeprowadzaj±ce wrogi atak. Domy¶lnie: 10.');
define($constpref.'_DOS_F5ACTION','Dzia³anie w obliczu ataku F5');

define($constpref.'_DOS_CRCOUNT','Próg dla robaków');
define($constpref.'_DOS_CRCOUNTDSC','Funkcja ochrony przed robakami konsumuj±cymi zasoby i botami. Wpisana tutaj warto¶æ okre¶la ilo¶æ prób dostêpu, powy¿ej której robak zostaje uznany za ¼le zachowuj±cego siê, tzn. zajmuj±cego zbyt wiele zasobów. Domy¶lnie 30 od¶wie¿eñ.');
define($constpref.'_DOS_CRACTION','Dzia³anie przeciwko robakom konsumuj±cym');

define($constpref.'_DOS_CRSAFE','Roboty indeksuj±ce wy³±czone spod kontroli');
define($constpref.'_DOS_CRSAFEDSC','Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','¯adne (tylko log).');
define($constpref.'_OPT_SAN','Naprawa');
define($constpref.'_OPT_EXIT','Bia³a Strona/Pusty ekran');
define($constpref.'_OPT_BIP','Banuj IP');
define($constpref.'_OPT_BIPTIME0','Banuj IP (moratorium)');

define($constpref.'_DOSOPT_NONE','¯adne (tylko log).');
define($constpref.'_DOSOPT_SLEEP','U¶pienie');
define($constpref.'_DOSOPT_EXIT','Bia³y Ekran');
define($constpref.'_DOSOPT_BIP','Banuj IP');
define($constpref.'_DOSOPT_BIPTIME0','Banuj IP (moratorium)');
define($constpref.'_DOSOPT_HTA','Odrzuæ przez .htaccess (funkcja w fazie eksperymentalnej)');

define($constpref.'_BIP_EXCEPT','Grupy, których IP nigdy nie zostanie zakwalifikowane jako z³e');
define($constpref.'_BIP_EXCEPTDSC','U¿ytkownik nale¿±cy do wymienionych tutaj grup nigdy nie zostanie zbanowany. Domy¶lnie wpisana grupa webmasters, i zaleca siê tak zostawiæ.');

define($constpref.'_DISABLES','Wy³±cz niebezpieczne funkcje XOOPSa');

define($constpref.'_BIGUMBRELLA','W³±cz anti-XSS (BigUmbrella) ');
define($constpref.'_BIGUMBRELLADSC','Ta funkcja chroni przed niektórymi atakami XSS (cross-site scripting). Nie ma jednak 100% skuteczno¶ci. Domy¶lnie ustawiona na nie (off), w³±czenie jej to raczej niez³y pomys³.');

define($constpref.'_SPAMURI4U','anti-SPAM: ilo¶æ adresów URL dla normalnych u¿ytkowników ');
define($constpref.'_SPAMURI4UDSC','Mo¿esz okre¶liæ dozwolon± liczbê adresów URL zawartych w danych formularza POST dla zarejestrowanych u¿ytkowników (np. w postach na forum i komentarzach), nie bêd±cych administratorami. Je¶li POST zawiera zbyt wiele adresów URL, zostanie uznany za spam. Domy¶lnie: 10. Je¶li chcesz wy³±czyæ tê funkcjê, ustaw warto¶æ 0. ');
define($constpref.'_SPAMURI4G','anti-SPAM: ilo¶æ adresów URL dla go¶ci');
define($constpref.'_SPAMURI4GDSC','Jak wy¿ej, ale dla anonimowych u¿ytkowników (go¶ci). Domy¶lnie: 5. Wpisz 0 je¶li chcesz wy³±czyæ tê funkcjê.');

}

?>
