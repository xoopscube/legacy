<?php
// $Id: install.php,v 1.1 2008/07/05 08:25:13 minahito Exp $
define("_INSTALL_L0","Vítá Vás prùvodce instalací pro XOOPS Cube 2.1.1");
define("_INSTALL_L70","Je nezbytné, aby bìhem instalace byl soubor mainfile.php nastaven pro zápis serverem (napø. chmod 777 mainfile.php na UNIX/LINUX serveru nebo na Windowsech nastavte soubor mainfile.php tak, aby nebyl nastaven \"Pouze pro ètení\"). A to udìláte znovunaètìte tuto stránku.");
//define("_INSTALL_L71","Click on the button below to begin the installation.");
define("_INSTALL_L1","Otevøete soubor mainfile.php ve Vašem textovém editoru a najdìte následující kód na øádku 31:");
define("_INSTALL_L2","Zmìòte tuto øádku na toto:");
define("_INSTALL_L3","Nyní, na øádce 35, zmìòte %s na %s");
define("_INSTALL_L4","Hotovo! Nastavil jsem pøedepsané hodnoty, zkusme to znovu!");
define("_INSTALL_L5","POZOR!");
define("_INSTALL_L6","Existuje jistı rozdíl mezi Vaším nastavením XOOPS_ROOT_PATH na øádce 31 v souboru mainfile.php a tím, co detekoval instalaèní program.");
define("_INSTALL_L7","Vaše nastavení: ");
define("_INSTALL_L8","Instalaèní program detekoval: ");
define("_INSTALL_L9","(Na platformách Microsoft se obèas stává, e získáte toto chybové oznámení, aèkoliv Vaše nastavení je správné. Pokud je to tento pøípad, prosíme, pokraèujte stiknutím tlaèítka Další)");
define("_INSTALL_L10","Prosíme, stisknìte tlaèítko níe pokud je toto opravdu v poøádku.");
define("_INSTALL_L11","Cesta na serveru do koøenového adresáøe XOOPS Cube: ");
define("_INSTALL_L12","URL do koøenového adresáøe XOOPS Cube: ");
define("_INSTALL_L13","Pokud je toto nastavení v poøádku, klinìte na tlaèítko Další.");
define("_INSTALL_L14","Další");
define("_INSTALL_L15","Prosíme, otevøete soubor mainfile.php a vepište tam nezbytná nastavení databáze");
define("_INSTALL_L16","%s je název (nebo adresa) Vašeho databázového serveru.");
define("_INSTALL_L17","%s je uivatelské jméno pro pøístup do databáze.");
define("_INSTALL_L18","%s je heslo nezbytné pro pøístup do Vaší databáze.");
define("_INSTALL_L19","%s je jméno databáze, ve které bude XOOPS Cube vytváøet své tabulky.");
define("_INSTALL_L20","%s je pøedpona názvù tabulek, které budou vytvoøeny bìhem instalace.");
define("_INSTALL_L21","Následující databáze nebyla na serveru nalezena:");
define("_INSTALL_L22","Má se instalaèní program pokusit ji vytvoøit?");
define("_INSTALL_L23","Ano");
define("_INSTALL_L24","Ne");
define("_INSTALL_L25","Instalaèní program detekoval následující nastavení databáze ze souboru mainfile.php. Prosíme, pøekontrolujte je a pøípadnì je upravte.");
define("_INSTALL_L26","Nastavení databáze");
define("_INSTALL_L51","Databáze");
define("_INSTALL_L66","Vyberte databázi, kterou chcete pro Xoops Cube pouít.");
define("_INSTALL_L27","Databázovı server");
define("_INSTALL_L67","Jméno databázového serveru. Pokud si nejste jisti, 'localhost' bıvá standardním nastavením.");
define("_INSTALL_L28","Uivatelské jméno");
define("_INSTALL_L65","Vaše uivatelské jméno pro pøístup na databázovı server do databáze.");
define("_INSTALL_L29","Jméno databáze");
define("_INSTALL_L64","Jméno databáze na serveru. Pokud databáze neexistuje, instalaèní prùvodce se ji pokusí vytvoøit.");
define("_INSTALL_L52","Heslo");
define("_INSTALL_L68","Vaše heslo pro pøístup na databázovı server do databáze.");
define("_INSTALL_L30","Oznaèení tabulek");
define("_INSTALL_L63","Pøedpona (prefix) tabulek kvùli zamezení konfliktù pøi vytváøení tabulek pro Xoops Cube. Pokud si nejste jisti, prosté 'xoops' by mìlo fungovat bez potíí.");
define("_INSTALL_L54","Pouít persistentní spojení?");
define("_INSTALL_L69","Standard je 'NE'. Vyberte 'NE' pokud si nejste jisti.");
define("_INSTALL_L55","Fyzická cesta k XOOPS Cube");
define("_INSTALL_L59","Fyzická cesta na server ke koøenovému adresáøi XOOPS Cube bez lomítka na konci.");
define("_INSTALL_L56","Virtuální adresa XOOPS Cube (URL)");
define("_INSTALL_L58","Virtuální cesta ke koøenovému adresáøi XOOPS Cube bez lomítka na konci.");

define("_INSTALL_L31","Nepodaøilo se vytvoøit databázi. Kontaktujte administrátora serveru pro další pomoc.");
define("_INSTALL_L32","Instalace Dokonèena");
define("_INSTALL_L33","Kliknìtøe <a href='../index.php'>TADY</a> a uvidíte titulní stránku Vašeho serveru.");
define("_INSTALL_L35","Pokud jste narazili na nìjaké chyby, prosíme, oznamte to vıvojovému tımu na <a href='http://www.xoopscube.org/' target='_blank'>XOOPSCUBE.org</a>");
define("_INSTALL_L36","Zadejte administrátorské jméno a heslo pro Váš XOOPS Cube server.");
define("_INSTALL_L37","Jméno administrátora");
define("_INSTALL_L38","Email administrátora");
define("_INSTALL_L39","Heslo administrátora");
define("_INSTALL_L74","Znovu heslo");
define("_INSTALL_L40","Vytvoøit tabulky");
define("_INSTALL_L41","Prosím, vrate se zpìt a dopište veškeré potøebné informace.");
define("_INSTALL_L42","Zpìt");
define("_INSTALL_L57","Prosím zadejte %s");

// %s is database name
define("_INSTALL_L43","Databáze %s vytvoøena!");

// %s is table name
define("_INSTALL_L44","Nepodaøilo se vytvoøit tabulku %s");
define("_INSTALL_L45","Tabulka %s vytvoøena.");

define("_INSTALL_L46","Kvùli provtnímu nastavení modulù obsaenıch v systému XOOPS Cube musejí bıt následující soubory nastaveny pro zápis serverem. Je nezbytné, aby bìhem instalace byl soubor mainfile.php nastaven pro zápis serverem (napø. chmod 777 mainfile.php na UNIX/LINUX serveru nebo na Windowsech nastavte soubor mainfile.php tak, aby nebyl nastaven \"Pouze pro ètení\"). A to udìláte znovunaètìte tuto stránku.");
define("_INSTALL_L47","Další");

define("_INSTALL_L53","Prosím, potvrïte tyto vloené údaje:");

define("_INSTALL_L60","Nepodaøilo se zapsat data do souboru mainfile.php. Prosíme, pøekontrolujte pøístupová práva, pøíp. nastavení \"Pouze pro ètení\", a zkuste znovu data uloit.");
define("_INSTALL_L61","Nepodaøilo se zapsat data do souboru mainfile.php. Kontaktujte administrátora serveru pro další pomoc.");
define("_INSTALL_L62","Nastavení bylo v poøádku uloeno do souboru mainfile.php.");
define("_INSTALL_L72","Následující adresáøe musejí bıt vytvoøeny s právem zápisu serverem. (napø. 'chmod 777 jmeno_adresare' na UNIX/LINUX serveru)");
define("_INSTALL_L73","Chybnı email");

// add by haruki
define("_INSTALL_L80","úvod");
define("_INSTALL_L81","provìøení pøístupovıch práv");
define("_INSTALL_L82","Provìøení pøístupovıch práv k souborùm a adresáøùm..");
define("_INSTALL_L83","Soubor %s NENÍ nastaven pro zápis.");
define("_INSTALL_L84","Soubor %s je nastaven pro zápis.");
define("_INSTALL_L85","Adresáø %s NENÍ nastaven pro zápis.");
define("_INSTALL_L86","Adresáø %s je nastaven pro zápis.");
define("_INSTALL_L87","ádné problémy nebyly zjištìny.");
define("_INSTALL_L89","hlavní nastavení");
define("_INSTALL_L90","Hlavní nastavení");
define("_INSTALL_L91","potvrdit");
define("_INSTALL_L92","uloit nastavení");
define("_INSTALL_L93","upravit nastavení");
define("_INSTALL_L88","Ukládání nastavení..");
define("_INSTALL_L94","provìøení cesty & URL");
define("_INSTALL_L127","Provìøení cest a adresy..");
define("_INSTALL_L95","Nepodaøilo se detekovat fyzickou cestu do Vašeho koøenového adresáøe XOOPS Cube.");
define("_INSTALL_L96","Je urèitı konfikt mezi detekovanou fyzickou cestou (%s) a tou, kterou jste zadali.");
define("_INSTALL_L97","<b>Fyzická cesta</b> je v poøádku.");

define("_INSTALL_L99","<b>Fyzická cesta</b> musí bıt adresáø.");
define("_INSTALL_L100","<b>Virtuální cesta</b> je funkèní adresa.");
define("_INSTALL_L101","<b>Virtuální cestah</b> není správná adresa.");
define("_INSTALL_L102","Potvrzení nastavení databáze");
define("_INSTALL_L103","znovu od zaèátku");
define("_INSTALL_L104","Provìøení databáze");
define("_INSTALL_L105","pokusit se vytvoøit databázi");
define("_INSTALL_L106","Nepodaøilo se pøipojit na databázovı server.");
define("_INSTALL_L107","Prosíme, zkontrolujte databázovı server a jeho nastavení.");
define("_INSTALL_L108","Spojení na databázovı server je v poøádku.");
define("_INSTALL_L109","Databáze %s neexistuje.");
define("_INSTALL_L110","Databáze %s existuje a je pøipravena ke spojení.");
define("_INSTALL_L111","Spojení do databáze je v poøádku.<br />Klinìte na tlaèítko Další pro vytvoøení tabulek.");
define("_INSTALL_L112","nastavení administrátorského úètu");
define("_INSTALL_L113","Tabulka %s vymazána.");
define("_INSTALL_L114","Nepodaøilo se vytvoøit tabulky v databázi.");
define("_INSTALL_L115","Tabulky v databázi vytvoøeny.");
define("_INSTALL_L116","vloit údaje");
define("_INSTALL_L117","dokonèit");

define("_INSTALL_L118","Nepodaøilo se vytvoøit tabulku %s.");
define("_INSTALL_L119","%d øádkù vloeno do tabulky %s.");
define("_INSTALL_L120","Nepodaøilo se vloit %d øádkù do tabulky %s.");

define("_INSTALL_L121","Promìnná %s uloena jako %s.");
define("_INSTALL_L122","Nepodaøilo se uloit promìnnou %s.");

define("_INSTALL_L123","Soubor %s uloen do adresáøe cache/.");
define("_INSTALL_L124","Nepodaøilo se uloit soubor %s do adresáøe cache/.");

define("_INSTALL_L125","Soubor %s pøepsán souborem %s.");
define("_INSTALL_L126","Nebylo moné zapsat do souboru %s.");

define("_INSTALL_L130","Instalaèní program detekoval v databázi tabulky pro XOOPS 1.3.x.<br />Program se nyní pokusí tyto tabulky adaptovat pro systém XOOPS Cube.");
define("_INSTALL_L131","Tabulky pro XOOPS Cube ji ve Vaší databázi existují.");
define("_INSTALL_L132","adaptovat tabulky");
define("_INSTALL_L133","Tabulka %s adaptována.");
define("_INSTALL_L134","Nepodaøilo se adaptovat tabulku %s.");
define("_INSTALL_L135","Nepodaøilo se adaptovat tabulky.");
define("_INSTALL_L136","Tabulky byly adaptovány.");
define("_INSTALL_L137","adaptovat moduly");
define("_INSTALL_L138","adaptovat komentáøe");
define("_INSTALL_L139","adaptovat ikony");
define("_INSTALL_L140","adaptovat emotikony");
define("_INSTALL_L141","Instalaèní program nyní pøevede všechny moduly, aby fungovaly v systému XOOPS Cube.<br />Ujistìte se, e jste na server nahráli všechna data z balíku XOOPS Cube.<br />Je moné, e to chvilku potrvá.");
define("_INSTALL_L142","Adaptování modulù..");
define("_INSTALL_L143","Instalaèní program nyní pøevede nastavení z XOOPS 1.3.x do nového XOOPS Cube.");
define("_INSTALL_L144","pøevést nastavení");
define("_INSTALL_L145","Komentáø (ID: %s) vloen do databáze.");
define("_INSTALL_L146","Nepodaèilo se vloit komentáø (ID: %s) do databáze.");
define("_INSTALL_L147","Adaptování komentáøù..");
define("_INSTALL_L148","Pøevod dokonèen.");
define("_INSTALL_L149","Instalaèní program nyní pøevede komentáøe z XOOPS 1.3.x do nového XOOPS Cube.<br />Je moné, e to chvilku potrvá.");
define("_INSTALL_L150","Instalaèní program nyní pøevede emotikony a uivatelská zaøazení do nového XOOPS Cube.<br />Je moné, e to chvilku potrvá.");
define("_INSTALL_L151","Instalaèní program nyní pøevede ikony do nového XOOPS Cube.<br />Je moné, e to chvilku potrvá.");
define("_INSTALL_L155","Pøevod emotikonù/obrázkù uivatelskıch zaøazení..");
define("_INSTALL_L156","Pøevod uivatelskıch ikon..");
define("_INSTALL_L157","Pro kadou skupinu vyberte odpovídající druh uivatelské skupiny");
define("_INSTALL_L158","Skupiny v 1.3.x");
define("_INSTALL_L159","Administrátoøi");
define("_INSTALL_L160","Registrovaní uivatelé");
define("_INSTALL_L161","Anonymní uivatelé");
define("_INSTALL_L162","Musíte vybrat pro kadou skupinu odpovídající druh uivatelské skupiny.");
define("_INSTALL_L163","Tabulky %s odstranìna.");
define("_INSTALL_L164","Nepodaøilo se odstranit tabulku %s.");
define("_INSTALL_L165","Server je z dùvodù úprav prozatímnì uzavøen. Prosíme, navštivte nás pozdìji.");

// %s is filename
define("_INSTALL_L152","Nepodaøilo se otevøít %s.");
define("_INSTALL_L153","Nepodaøilo se aktualizovat %s.");
define("_INSTALL_L154","%s aktualizován.");

define('_INSTALL_L128', 'Vyberte si jazyk pro pouití bìhem instalace');
define('_INSTALL_L200', 'Obnovit');
define("_INSTALL_L210","Druhı krok instalace");


define('_INSTALL_CHARSET','WINDOWS-1250');

define('_INSTALL_LANG_XOOPS_SALT', "DOPLNÌNÍ");
define('_INSTALL_LANG_XOOPS_SALT_DESC', "Toto hraje doplòkovou roli pro generování tajnıch kódù a znakù. Vıchozí hodnotu není tøeba mìnit.");

define('_INSTALL_HEADER_MESSAGE','Bìhem instalace pozornì ètìte a vyplòujte poadované údaje.');
?>
