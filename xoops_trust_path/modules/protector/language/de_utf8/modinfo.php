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

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Diese Module schutzt Ihre Seite vor verschiedenen Angriffen z.B. DoS , SQL Injection , ....");

// Menu
define($constpref."_ADMININDEX","Protect Center");
define($constpref."_ADVISORY","Sicherheitsberatung");
define($constpref."_PREFIXMANAGER","Prefix Manager");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Berechtigungen') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Vorubergehend deaktiviert');
define($constpref.'_GLOBAL_DISBLDSC','Alle Sicherheitsfunktionen sind vorubergehend deaktiviert!<br />Vergessen Sie nicht diese wieder einzuschalten, wenn Sie eine Storung beseitigt haben!');

define($constpref.'_DEFAULT_LANG','Voreingestellte Sprache');
define($constpref.'_DEFAULT_LANGDSC','Geben Sie die Sprache fur die Anzeige von Nachrichten bei der Verarbeitung der common.php an.');

define($constpref.'_RELIABLE_IPS','Sichere IPs');
define($constpref.'_RELIABLE_IPSDSC','Sie konnen IP Adressen mit einem | trennen. ^ setzt den Kopf des String, $ setzt das Ende des Strings.');

define($constpref.'_LOG_LEVEL','Berichtsstufe');
define($constpref.'_LOG_LEVELDSC','Stufe wie genau der Bericht verfasst wird');

define($constpref.'_BANIP_TIME0','Blockadezeit von gesperrten IPs, zeit in Sekunden');

define($constpref.'_LOGLEVEL0','nichts');
define($constpref.'_LOGLEVEL15','still');
define($constpref.'_LOGLEVEL63','still');
define($constpref.'_LOGLEVEL255','voll');

define($constpref.'_HIJACK_TOPBIT','Geschutzte IP bits fur dieses Session');
define($constpref.'_HIJACK_TOPBITDSC','Anti Session Hi-Jacking:<br />Default 32(bit). (Alle Bits sind geschutzt)<br />Wenn Sie keine statische IP Adresse haben, setzen Sie den IP Bereich mit Nummer der einzelnen Bits.<br />(eg) Wenn sich Ihre IP im Bereich von 192.168.0.0 bis 192.168.0.255 befindet, setzen Sie 24(bit) hier');
define($constpref.'_HIJACK_DENYGP','Gruppen denen das andern der IP innerhalb einer Session untersagt wird.');
define($constpref.'_HIJACK_DENYGPDSC','Anti Session Hi-Jacking:<br />Wahlen sie Gruppen aus, denen es untersagt ist, ihre IP wahrend einer Session zu andern..<br />(Mindestens Administrator-Gruppe wird empfohlen.)');
define($constpref.'_SAN_NULLBYTE','Sanitizing (Sauberung) null-bytes');
define($constpref.'_SAN_NULLBYTEDSC','Das Abschluss-Zeichen "\\0" wird oft in Attacken verwendet.<br />Dieses Null-Byte wird in ein Leerzeichen konvertiert.<br />(Einschalten wird dringendst empfohlen!)');
define($constpref.'_DIE_NULLBYTE','Beenden, wenn Null-Bytes gefunden werden');
define($constpref.'_DIE_NULLBYTEDSC','Das Abschluss-Zeichen "\\0" wird oft in Attacken verwendet.<br />(Dringendst empfohlen)');
define($constpref.'_DIE_BADEXT','Beenden, wenn unzulassgige Dateien hochgeladen werden');
define($constpref.'_DIE_BADEXTDSC','Wenn jemand versucht, Dateien mit unzulassigen Endungen wie .php hochzuladen, beendet diese Modul den Zugriff fur XOOPS.<br />Wenn Sie oft Dateien in B-Wiki oder PukiWikiMod einstellen, schalten Sie diese Option aus.');
define($constpref.'_CONTAMI_ACTION','Masnahmen, wenn eine Verunreinigung gefunden wurde:');
define($constpref.'_CONTAMI_ACTIONDS','Wahlen Sie eine Aktion aus, wenn jemand versucht, globale XOOPS-Variablen zu verunreinigen.<br />(Empfohlen wird "Weiser Bildschirm")');
define($constpref.'_ISOCOM_ACTION','Masnahmen, wenn eine isolierte Einkommentierung gefunden wurde:');
define($constpref.'_ISOCOM_ACTIONDSC','Anti SQL Injection:<br />Wahlen Sie eine Massnahme aus, die ergriffen wird, wenn ein  isoliertes "/*" gefunden wird.<br />"Sanitizing (Sauberung)" bedeutet, ein zusatzliches  "*/" anzuhangen.<br />(Empfohlen wird "Sanitizing - Sauberung)" )');
define($constpref.'_UNION_ACTION','Massnahme wenn ein UNION gefunden wurde.');
define($constpref.'_UNION_ACTIONDSC','Anti SQL Injection:<br />Wahlen sie eine Massnahme, wenn ein SQL-Befehl wie UNION gefunden wurde.<br />"Sanitizing (Sauberung)" bedeutet die Anderung von "union" nach "uni-on".<br />(Empfohlen wird Sanitizing - Sauberung)');
define($constpref.'_ID_INTVAL','Erzwinge intval fur Variablen wie IDs');
define($constpref.'_ID_INTVALDSC','Alle Anfragen mit Namen "*id" Werden als Integer behandelt.<br />Diese Option beschutzt sie vor einigen Arten der XSS-(Cross Site Scripting-) und SQL-Injection-Attacken.<br />Obwohl empfohlen wird, diese Option einzuschalten, kann es in einigen Modulen Probleme damit geben.');
define($constpref.'_FILE_DOTDOT','Behebe zweifelhafte Dateiangaben');
define($constpref.'_FILE_DOTDOTDSC','Eliminiertalle ".." aus Anfragen, die nach Dateien suchen');

define($constpref.'_BF_COUNT','Anti Brute Force');
define($constpref.'_BF_COUNTDSC','Setzt die Anzahl der Loginversuchen von Gasten innerhalb 10 minuten. Wenn die Anzahl von Loginversuchen erreicht ist, wird die IP auf die Liste der schlechten IPs gesetzt.');

define($constpref.'_BWLIMIT_COUNT','Bandbreitenbegrenzung');
define($constpref.'_BWLIMIT_COUNTDSC','Geben Sie die maximalen Zugriffe zur mainfile.php an wahrend der Uberwachungszeit. Dieser Wert sollte 0 sein fur eine normale Umgebung, die uber genugend CPU-Bandbreite verfugen. Die Zahl wenniger als 10 werden ignoriert.');

define($constpref.'_DOS_SKIPMODS','Module die nicht auf DoS/Crawler gepruft werden');
define($constpref.'_DOS_SKIPMODSDSC','setzt die Verzeichnisnamen der Module, getrennt durch ein |. Diese Option ist bei Chatmodulen etc. hilfreich');

define($constpref.'_DOS_EXPIRE','','Zeitlimit fur hohe Serverlast (Sekunden)');
define($constpref.'_DOS_EXPIREDSC','Dieser Wert gibt das Zeitlimit fur rasch wiederholte Reloads der Seite (F5 Attacke) und fur Suchmaschinen mit hoher Last an.');

define($constpref.'_DOS_F5COUNT','Anzahl als schadlich eingestufter Reloads F5');
define($constpref.'_DOS_F5COUNTDSC','verhindert DoS Attacken.<br />Der Wert gibt an, wieviele Reloads (F5) als Attacke eingestuft werden.');
define($constpref.'_DOS_F5ACTION','Masnahmen gegen F5 Attacke');

define($constpref.'_DOS_CRCOUNT','Anzahl als schadlich eingestufter Suchmaschinen-Abfragen');
define($constpref.'_DOS_CRCOUNTDSC','Schutzt vor Server-intensiven Abfragen durch Suchmaschinen.<br />Dieser Wert gibt an, wieviele Zugriffe als Server-intensiv eingestuft werden.');
define($constpref.'_DOS_CRACTION','Masnahmen gegen Server-intensive Suchmaschinen');

define($constpref.'_DOS_CRSAFE','Zugelassene User-Agents');
define($constpref.'_DOS_CRSAFEDSC','Ein regulaeer Perl-Ausdruck fur User-Agents.<br />Wenn der Ausdruck zutrifft, wird die Suchmaschine niemals als Server-intensiv eingestuft.<br />Bsp: (msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','Keine (nur logging)');
define($constpref.'_OPT_SAN','Sanitizing (Sauberung)');
define($constpref.'_OPT_EXIT','Weiser Bildschirm');
define($constpref.'_OPT_BIP','IP sperren');
define($constpref.'_OPT_BIPTIME0','Ban the IP (moratorium)');

define($constpref.'_DOSOPT_NONE','Keine (nur logging)');
define($constpref.'_DOSOPT_SLEEP','schlafen');
define($constpref.'_DOSOPT_EXIT','Weiser Bildschirm');
define($constpref.'_DOSOPT_BIP','IP sperren');
define($constpref.'_DOSOPT_BIPTIME0','Sperre die IP (moratorium)');
define($constpref.'_DOSOPT_HTA','DENY by .htaccess(Experimental)');

define($constpref.'_BIP_EXCEPT','Gruppen, die niemals als "schlechte IP" eingestuft werden.');
define($constpref.'_BIP_EXCEPTDSC','Ein User, der in dieser Gruppe ist, wird niemals eine IP-Sperre erfahren.<br />(Empfohlen wird, die Administartor-Gruppe anzugeben.)');

define($constpref.'_DISABLES','Deaktiviert die Sicherheitsfeatures von Protector in XOOPS');

define($constpref.'_DBLAYERTRAP','Enable DB Layer trapping anti-SQL-Injection');
define($constpref.'_DBLAYERTRAPDSC','Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page.');

define($constpref.'_BIGUMBRELLA','aktiviere anti-XSS (BigUmbrella)');
define($constpref.'_BIGUMBRELLADSC','Dies schutzt vor Angriffenvia XSS vulnerabilities. Schutzt nicht zu 100%');

define($constpref.'_SPAMURI4U','anti-SPAM: Anzahl URLs fur normale Users');
define($constpref.'_SPAMURI4UDSC','Wenn diese Anzahl von URLs in Beitragen von Usern (nicht Admins) gefunden wird, ist der Beitrag als Spam eingestuft. 0 bedeutet dieses Feature ist deaktiviert.');
define($constpref.'_SPAMURI4G','anti-SPAM: Anzahl URLs fur Gaste');
define($constpref.'_SPAMURI4GDSC','Wenn diese Anzahl von URLs in Beitragen von Gasten gefunden wird, ist der Beitrag als Spam eingestuft. 0 bedeutet dieses Feature ist deaktiviert.');
}
?>
