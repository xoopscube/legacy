<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {






// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:59
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
define($constpref."_DESC","Moduł zabezpieczający Xoopsa, przed różnymi
rodzajami ataków z sieci, takich jak: DoS , SQL Injection i skażeniem
zmiennych.");

// Menu
define($constpref."_ADMININDEX","Centrum zabezpieczeń");
define($constpref."_ADVISORY","Porady nt. bezpieczeństwa");
define($constpref."_PREFIXMANAGER","Menadżer prefiksu");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Uprawnienia') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Tymczasowo wyłączony');
define($constpref.'_GLOBAL_DISBLDSC','Możesz czasowo wyłączyć Protectora, jeśli masz jakieś problemy z jego funcjonowaniem. Nie zapomnij włączyć go na powrót, gdy już naprawisz problem. Domyślnie ustawiony na nie.');

define($constpref.'_RELIABLE_IPS','IP godne zaufania');
define($constpref.'_RELIABLE_IPSDSC','Wpisz numery IP, które uznajesz za godne zaufania np. swoje własne. Te IP nie będą banowane przez Protectora, dzięki czemu uchronisz się przed zablokowaniem dostępu dla siebie. Poszczególne numery IP oddzielaj pionową kreską. ^ zastępuje początek numeru, $ zastępuje koniec numeru.');
define($constpref.'_LOG_LEVEL','Poziom logowania');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_LOGLEVEL0','Żaden');
define($constpref.'_LOGLEVEL15','Ukryty');
define($constpref.'_LOGLEVEL63','Cichy (bardziej niż ukryty).');
define($constpref.'_LOGLEVEL255','Pełny');

define($constpref.'_HIJACK_TOPBIT','Chronione bity numeru IP w sesji');
define($constpref.'_HIJACK_TOPBITDSC','Ta funkcja chroni przed przechwytywaniem sesji, ograniczając ilość bitów IP, które mogą się zmienić w trakcie sesji. Domyślnie 32 bitów - wszystkie bity chronione (IP nie może się zmienić). Jeśli masz dynamiczne IP, zmieniające się w określonym zakresie, możesz ustawić ilość chronionych bitów tak, by mniej więcej dopasować do zakresu. Na przykład, jeśli twoje IP może się zmieniać w zakresie od 192.168.0.0 do 192.168.0.255, ustaw 24 bity. Gdyby cracker znał IP twojej sesji, ale próbował się wedrzeć spoza tego zakresu (powiedzmy, z 192.168.2.50), nie uda mu się. Autor modułu sugeruje wartość 16 bitów jako optymalną dla ogólnego użycia.');
define($constpref.'_HIJACK_DENYGP','Grupy nieuprawnione do zmieniania
swojego IP w trakcie sesji');
define($constpref.'_HIJACK_DENYGPDSC','WskaĽnik chroniący przed przechwytywaniem. Wybrane grupy nie mogą zmieniać IP w trakcie trwania sesji. Domyślnie wymienia grupę webmasters i poleca się tego nie zmieniać, bo konsekwencje przechwycenia sesji administratora mogłyby być naprawdę poważne.)');
define($constpref.'_SAN_NULLBYTE','Sterylizowanie pustych bajtów');
define($constpref.'_SAN_NULLBYTEDSC','Znak kończący "\\0" jest często używany we wrogich atakach. Pusty bajt zmieni się w spację, jeśli ta opcja jest włączona (co jest domyślne, i stanowczo poleca się pozostawić ją włączoną).');
define($constpref.'_DIE_NULLBYTE','WyjdĽ jeśli stwierdzone zostaną
puste bajty');
define($constpref.'_DIE_NULLBYTEDSC','Znak zakończenia "\\0" jest zwykle używany podczas atak-u na serwisy.<br />(należy suatwić tą opcję włączoną)');
define($constpref.'_DIE_BADEXT','WyjdĽ jeśli wgrywane są podejrzane
pliki (tak/nie)');
define($constpref.'_DIE_BADEXTDSC','Jeśli ktoś próbuje wgrać pliki z niebezpiecznymi rozszerzeniami, jak .php ,Protector zamknie XOOPSa. Jeśli często dołączasz pliki php do B-Wiki albo PukiWikiMod, być może będziesz musiał wyłączyć tę funkcję.');
define($constpref.'_CONTAMI_ACTION','Działanie w przypadku wykrycia
próby skażenia zmiennych');
define($constpref.'_CONTAMI_ACTIONDS','Wybierz działanie, jakie ma być podjęte, gdy ktoś próbuje skazić globalne zmienne systemu w Twoim XOOPSie. Możliwości:)');
define($constpref.'_ISOCOM_ACTION','Działanie w przypadku wykrycia
izolowanego otwarcia komentarza.');
define($constpref.'_ISOCOM_ACTIONDSC','Ochrona przed skażeniem SQL. Określ działanie wobec znalezienia izolowanego "/*". Możliwości:');
define($constpref.'_UNION_ACTION','Działanie w przypadku wykrycia próby dodania instrukcji UNION lub podobnej.');
define($constpref.'_UNION_ACTIONDSC','Ochrona przed skażeniem SQL. Określ działanie wobec znalezienia składni UNION w SQL. Możliwości:');
define($constpref.'_ID_INTVAL','Wymuszanie liczby całkowitej dla zapytań zawierających zmienne typu id');
define($constpref.'_ID_INTVALDSC','Ta opcja miała chronić przed problemem w starszej wersji modułu weblog. Teraz ten błąd został naprawiony.<br />Wszystkie żądania z nazwami takimi jak "*id" będą traktowane jak liczby całkowite. Ta opcja chroni przed niektórymi rodzajami ataków XSS i SQL. Poleca się ją włączyć, choć może się zdarzyć, że będzie powodować problemy z niektórymi modułami. Domyślnie ustawiona na off.');
define($constpref.'_FILE_DOTDOT','Ochrona przed włamywaniem się do folderów');
define($constpref.'_FILE_DOTDOTDSC','Ta funkcja eliminuje ".." z wszystkich zapytań, które wyglądają na próby włamywania się do folderów. Możliwe opcje to włączenie (tak) lub wyłączenie (nie). Domyślnie ustawiona na on (włączone).');

define($constpref.'_BF_COUNT','Ochrona przed atakami na siłę (Brute Force)');
define($constpref.'_BF_COUNTDSC','Tutaj możesz określić ilość dopuszczalnych prób zalogowania w ciągu 10 minut. Jeśli ktoś poda złe dane więcej razy, niż wynosi limit, jego IP zostanie zbanowane. Ta funkcja chroni przed próbami złamania haseł dostępu metodą prób i błędów. Domyślnie ustawiona wartość wynosi 10.');

define($constpref.'_DOS_SKIPMODS','Moduły wyłączone z ochrony przed
DoS/Crawler');
define($constpref.'_DOS_SKIPMODSDSC','Protector może banować IP inicjujące ataki DoS lub robaki, które zabierają duże zasoby (patrz niżej). Możesz jednak wyłączyć poszczególne moduły z tej ochrony, wpisując tutaj nazwy ich katalogów. Kolejne moduły oddzielaj pionową kreską. Funkcja przydaje się do modułów takich jak np. czat.');
define($constpref.'_DOS_EXPIRE','Czas dozorowania masowych odświeżań (w sek.)');
define($constpref.'_DOS_EXPIREDSC','Ta wartość określa czas obserwowania licznych/częstych odświeżań (atak F5) i robaków zajmujących transfer. Domyślnie 60 sekund. .');

define($constpref.'_DOS_F5COUNT','Próg dla ataków F5');
define($constpref.'_DOS_F5COUNTDSC','Funkcja przeciwko atakom DoS. Wpisana wartość określa liczbę odświeżeń (w okresie czasu dozorowania wpisanego powyżej), jaka musi być wykonana, zanim dane IP zostanie uznane za przeprowadzające wrogi atak. Domyślnie: 10.');
define($constpref.'_DOS_F5ACTION','Działanie w obliczu ataku F5');

define($constpref.'_DOS_CRCOUNT','Próg dla robaków');
define($constpref.'_DOS_CRCOUNTDSC','Funkcja ochrony przed robakami konsumującymi zasoby i botami. Wpisana tutaj wartość określa ilość prób dostępu, powyżej której robak zostaje uznany za Ľle zachowującego się, tzn. zajmującego zbyt wiele zasobów. Domyślnie 30 odświeżeń.');
define($constpref.'_DOS_CRACTION','Działanie przeciwko robakom konsumującym');

define($constpref.'_DOS_CRSAFE','Roboty indeksujące wyłączone spod kontroli');
define($constpref.'_DOS_CRSAFEDSC','Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','Żadne (tylko log).');
define($constpref.'_OPT_SAN','Naprawa');
define($constpref.'_OPT_EXIT','Biała Strona/Pusty ekran');
define($constpref.'_OPT_BIP','Banuj IP');
define($constpref.'_OPT_BIPTIME0','Banuj IP (moratorium)');

define($constpref.'_DOSOPT_NONE','Żadne (tylko log).');
define($constpref.'_DOSOPT_SLEEP','Uśpienie');
define($constpref.'_DOSOPT_EXIT','Biały Ekran');
define($constpref.'_DOSOPT_BIP','Banuj IP');
define($constpref.'_DOSOPT_BIPTIME0','Banuj IP (moratorium)');
define($constpref.'_DOSOPT_HTA','Odrzuć przez .htaccess (funkcja w fazie eksperymentalnej)');

define($constpref.'_BIP_EXCEPT','Grupy, których IP nigdy nie zostanie zakwalifikowane jako złe');
define($constpref.'_BIP_EXCEPTDSC','Użytkownik należący do wymienionych tutaj grup nigdy nie zostanie zbanowany. Domyślnie wpisana grupa webmasters, i zaleca się tak zostawić.');

define($constpref.'_DISABLES','Wyłącz niebezpieczne funkcje XOOPSa');

define($constpref.'_BIGUMBRELLA','Włącz anti-XSS (BigUmbrella) ');
define($constpref.'_BIGUMBRELLADSC','Ta funkcja chroni przed niektórymi atakami XSS (cross-site scripting). Nie ma jednak 100% skuteczności. Domyślnie ustawiona na nie (off), włączenie jej to raczej niezły pomysł.');

define($constpref.'_SPAMURI4U','anti-SPAM: ilość adresów URL dla normalnych użytkowników ');
define($constpref.'_SPAMURI4UDSC','Możesz określić dozwoloną liczbę adresów URL zawartych w danych formularza POST dla zarejestrowanych użytkowników (np. w postach na forum i komentarzach), nie będących administratorami. Jeśli POST zawiera zbyt wiele adresów URL, zostanie uznany za spam. Domyślnie: 10. Jeśli chcesz wyłączyć tę funkcję, ustaw wartość 0. ');
define($constpref.'_SPAMURI4G','anti-SPAM: ilość adresów URL dla gości');
define($constpref.'_SPAMURI4GDSC','Jak wyżej, ale dla anonimowych użytkowników (gości). Domyślnie: 5. Wpisz 0 jeśli chcesz wyłączyć tę funkcję.');

}

?>
