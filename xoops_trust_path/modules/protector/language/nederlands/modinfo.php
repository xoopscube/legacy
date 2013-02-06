<?php
// Dutch Translation by Cath22: Cathelijne22@gmail.com

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

// Appended by Xoops Language Checker -GIJOE- in 2009-01-14 11:10:54
define($constpref.'_DBLAYERTRAP','Enable DB Layer trapping anti-SQL-Injection');
define($constpref.'_DBLAYERTRAPDSC','Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page.');

// Appended by Xoops Language Checker -GIJOE- in 2008-12-03 11:47:21
define($constpref.'_DEFAULT_LANG','Default language');
define($constpref.'_DEFAULT_LANGDSC','Specify the language set to display messages before processing common.php');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Deze module beschermt uw Xoops site tegen verschillende kwaadaardige aanvallen DoS (Denial of Service) , SQL Injectie en vervuiling van variabelen.");

// Menu
define($constpref."_ADMININDEX","Protect Center");
define($constpref."_ADVISORY","Beveiligings Advies");
define($constpref."_PREFIXMANAGER","Prefix Manager");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Permissies') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Tijdelijk uitgeschakeld');
define($constpref.'_GLOBAL_DISBLDSC','Alle beschermingen zijn tijdelijk uitgeschakeld.<br />Vergeet dit niet weer in te schakelen na het testen!');

define($constpref.'_RELIABLE_IPS','Betrouwbare IPs');
define($constpref.'_RELIABLE_IPSDSC','Vul betrouwbare IPs in gescheiden door | . ^ geeft het begin van een reeks aan, $ geeft het einde van een reeks aan.');

define($constpref.'_LOG_LEVEL','Loggingsniveau');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_BANIP_TIME0','Geblokte IP schorsingstijd (sec)');

define($constpref.'_LOGLEVEL0','geen');
define($constpref.'_LOGLEVEL15','enigzins');
define($constpref.'_LOGLEVEL63','redelijk');
define($constpref.'_LOGLEVEL255','volledig');

define($constpref.'_HIJACK_TOPBIT','Beschermde IP delen voor de sessie');
define($constpref.'_HIJACK_TOPBITDSC','Anti Sessie Overname (Hi-Jacking):<br />Default 32(bit). (Alle bits zijn beschermd)<br />Als u geen vast IP hebt, stel dan het IP bereik in op nummer van de bits.<br />(bijv) Als uw IP kan wisselen in het bereik 192.168.0.0-192.168.0.255, stel dan hier 24(bit) in');
define($constpref.'_HIJACK_DENYGP','Groepen die niet toegestaan zijn om IP te wisselen in een sessie');
define($constpref.'_HIJACK_DENYGPDSC','Anti Sessie Overname:<br />Selecteer groepen die niet zijn toegestaan om van IP te wisselen in een sessie.<br />(Het is aanbevolen om Administrator aan te zetten.)');
define($constpref.'_SAN_NULLBYTE','Schoonmaken null-bytes');
define($constpref.'_SAN_NULLBYTEDSC','Het afsluitend karakter "\\0" wordt vaak gebruikt bij kwaadaardige aanvallen.<br />een null-byte word veranderd in een spatie.<br />(Sterk aanbevolen om AAN te zetten)');
define($constpref.'_DIE_NULLBYTE','Sluit als null bytes worden gevonden');
define($constpref.'_DIE_NULLBYTEDSC','Het afsluitend karakter "\\0" wordt vaak gebruikt bij kwaadaardige aanvallen.<br />(Sterk aanbevolen om AAN te zetten)');
define($constpref.'_DIE_BADEXT','Sluit als ongewenste bestanden worden geupload');
define($constpref.'_DIE_BADEXTDSC','Wanneer een ongewenste extensie wordt geprobeerd te uploaden zoals .php , zal deze module XOOPS sluiten.<br />Indien u vaak php bestanden toevoegd in B-Wiki of PukiWikiMod, is het beter deze instelling UIT te schakelen.');
define($constpref.'_CONTAMI_ACTION','Handeling wanneer een vervuiling is gevonden');
define($constpref.'_CONTAMI_ACTIONDS','Selecteer de te verrichten handeling wanneer wordt geprobeerd de system global variabelen in de XOOPS site te vervuilen.<br />(Aanbevolen is een blanco scherm weergeven)');
define($constpref.'_ISOCOM_ACTION','Handeling wanneer een enkel startend commentaar teken wordt gevonden');
define($constpref.'_ISOCOM_ACTIONDSC','Anti SQL Injectie:<br />Selecteer de handeling die verricht moet worden als een enkel "/*" wordt gevonden<br />"Opschonen" betekent dat er nog een "*/" wordt geplaatst aan het einde.<br />(Aanbevolen instelling is Opschonen)');
define($constpref.'_UNION_ACTION','Handeling als een UNION wordt gevonden');
define($constpref.'_UNION_ACTIONDSC','Anti SQL Injectie:<br />Selecteer de handeling die verricht moet worden als syntax zoals UNION of SQL wordt gevonden.<br />"Opschonen" betekent dat "union" word veranderd in "uni-on".<br />(Aanbevolen instelling is Opschonen)');
define($constpref.'_ID_INTVAL','Forceer een integer waarde bij variabelen zoals id');
define($constpref.'_ID_INTVALDSC','Alle verzoeken die "*id" bevatten zullen als integer worden behandeld.<br />Deze instelling beschermt tegen bepaalde vormen van XSS en SQL Injecties.<br />Alhoewel aan te raden is om deze Aan te zetten kan het problemen geven met sommige modulen.');
define($constpref.'_FILE_DOTDOT','Verander twijfelachtige bestandsspecificaties');
define($constpref.'_FILE_DOTDOTDSC','Verwijdert ".." uit alle verzoeken die op een bestansspecificatie lijken');

define($constpref.'_BF_COUNT','Anti Brute Force');
define($constpref.'_BF_COUNTDSC','Zet de telling die je iemand geeft om in te loggen binnen 10 minuten. Als iemand er langer over doet om in te loggen dan dit nummer, dan zal zijn/haar IP verbannen/geblokt worden.');

define($constpref.'_BWLIMIT_COUNT','Bandwijdte beperking');
define($constpref.'_BWLIMIT_COUNTDSC','Geef de maximum toegang aan tot mainfile.php gedurende de controleertijd. Deze waarde dient 0 te zijn voor normale omgevingen die genoeg CPU bandwijdte hebben. Een waarde minder dan 10 wordt genegeerd.');

define($constpref.'_DOS_SKIPMODS','Modulen vrijstellen van DoS/Crawler controle');
define($constpref.'_DOS_SKIPMODSDSC','Geef de directorynamen van de modulen gescheiden met |. Deze instelling is bijv. handig met chat modulen.');

define($constpref.'_DOS_EXPIRE','Tijd om snel-laders in de gaten te houden (sec)');
define($constpref.'_DOS_EXPIREDSC','Deze specificeert de tijd in seconden dat frequente verversers (F5 Aanval) en snel aanvragende web-crawlers in de gaten gehouden worden.');

define($constpref.'_DOS_F5COUNT','Kritiek aantal verversingen F5 Aanval');
define($constpref.'_DOS_F5COUNTDSC','Het voorkomen van DoS aanvallen.<br />Deze waarde specificeert het aantal verversingen dat wordt gezien als een kwaadaardige aanval.');
define($constpref.'_DOS_F5ACTION','Handeling tegen F5 Aanval');

define($constpref.'_DOS_CRCOUNT','Kritiek aantal voor web-crawlers');
define($constpref.'_DOS_CRCOUNTDSC','Blokkeren van snel aanvragende web-crawlers.<br />Deze waarde specificeert het aantal aanvragen dat wordt gezien als kenmerkend voor een ongewenste web-crawler.');
define($constpref.'_DOS_CRACTION','Handeling tegen snel aanvragende web-crawlers');

define($constpref.'_DOS_CRSAFE','Gewenste user-agents');
define($constpref.'_DOS_CRSAFEDSC','Een perl regex patroon om de user-agent te herkennen.<br />Als het overeenkomt, zal de web-crawler nooit als ongewenst worden beschouwd.<br />eg) /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','Geen (alleen vastleggen in logbestand)');
define($constpref.'_OPT_SAN','Opschonen');
define($constpref.'_OPT_EXIT','Blanco Scherm');
define($constpref.'_OPT_BIP','Verban het IP adres (Geen limiet)');
define($constpref.'_OPT_BIPTIME0','Verban het IP adres (tijdelijk)');

define($constpref.'_DOSOPT_NONE','Geen (alleen vastleggen in logbestand)');
define($constpref.'_DOSOPT_SLEEP','Slapen');
define($constpref.'_DOSOPT_EXIT','Blanco Scherm');
define($constpref.'_DOSOPT_BIP','Verban het IP adres (Geen limiet)');
define($constpref.'_DOSOPT_BIPTIME0','Verban het IP adres (tijdelijk)');
define($constpref.'_DOSOPT_HTA','Tegenhouden (DENY) via .htaccess(Experimenteel)');

define($constpref.'_BIP_EXCEPT','Groepen wiens IP adres nooit verbannen wordt');
define($constpref.'_BIP_EXCEPTDSC','Een gebruiker die behoort tot de hier gespecificeerde groepen zal nooit verbannen worden.<br />(Aanbevolen om administrator in te schakelen.)');

define($constpref.'_DISABLES','Uitschakelen gevaarlijke instellingen in XOOPS');

define($constpref.'_BIGUMBRELLA','Aanzetten anti-XSS (BigUmbrella)');
define($constpref.'_BIGUMBRELLADSC','Dit beschermt tegen de meeste aanvallen via XSS kwetsbaarheden. Maar het is niet 100%.');

define($constpref.'_SPAMURI4U','Anti-SPAM: URLs voor normale gebruikers');
define($constpref.'_SPAMURI4UDSC','Het aantal URLs gevonden in POST data van andere gebruikers dan de admin, dan wordt deze aangemerkt als SPAM. 0 betekent uitschakeling van deze functie.');
define($constpref.'_SPAMURI4G','Anti-SPAM: URLs voor gasten');
define($constpref.'_SPAMURI4GDSC','Het aantal URLs gevonden in POST data van gasten (anonieme gebruikers), dan wordt deze aangemerkt als SPAM. 0 betekent uitschakeling van deze functie.');

}

?>
