<?php

//Italian translation: Defkon1 - defkon1(at)gmail(dot)com - www.xoopsitalia.org

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

// Appended by Xoops Language Checker -GIJOE- in 2008-11-21 04:44:31
define($constpref.'_DEFAULT_LANG','Default language');
define($constpref.'_DEFAULT_LANGDSC','Specify the language set to display messages before processing common.php');
define($constpref.'_BWLIMIT_COUNT','Bandwidth limitation');
define($constpref.'_BWLIMIT_COUNTDSC','Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Protector");

// A brief description of this module
define($constpref."_DESC","Questo modulo protegge il tuo sito Xoops da diversi tipi di attacchi, come i Denial Of Service, Iniezione SQL, e contaminazione delle variabili.");

// Menu
define($constpref."_ADMININDEX","Centro di protezione");
define($constpref."_ADVISORY","Organo di sicurezza");
define($constpref."_PREFIXMANAGER","Gestore prefissi");
define($constpref.'_ADMENU_MYBLOCKSADMIN','Permessi') ;

// Configs
define($constpref.'_GLOBAL_DISBL','Temporaneamente disabilitato');
define($constpref.'_GLOBAL_DISBLDSC','Tutte le protezioni sono disabilitate temporaneamente.<br />Non dimenticare di impostare su No, dopo aver risolto il problema');

define($constpref.'_RELIABLE_IPS','IP affidabili');
define($constpref.'_RELIABLE_IPSDSC','Imposta gli IP affidabili separandoli con | . ^ abbina la testa della stringa, $ abbina la coda della stringa.');

define($constpref.'_LOG_LEVEL','Livello del log');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_BANIP_TIME0','Tempo di espulsione degli IP (sec)');

define($constpref.'_LOGLEVEL0','Nessuno');
define($constpref.'_LOGLEVEL15','Silenzioso');
define($constpref.'_LOGLEVEL63','Basso');
define($constpref.'_LOGLEVEL255','Totale');

define($constpref.'_HIJACK_TOPBIT','Bit dell\'IP protetti per questa sessione');
define($constpref.'_HIJACK_TOPBITDSC','Anti dirottamento della Sessione:<br />Default 32(bit). (Tutti i bit protetti)<br />Quando il tuo IP non &egrave; statico, imposta un range di tolleranza sul numero di bit protetti dell\'IP.<br />(es.) Se il tuo IP pu&ograve; muoversi nel range 192.168.0.0-192.168.0.255, impostare una protezione di 24(bit)');
define($constpref.'_HIJACK_DENYGP','Gruppi non autorizzati a cambiare IP durante una sessione');
define($constpref.'_HIJACK_DENYGPDSC','Anti dirottamento sessione:<br />Selezionare i gruppi a cui non &egrave; permesso cambiare IP durante una sessione.<br />(Raccomandato: tutti i gruppi di Amministrazione.)');
define($constpref.'_SAN_NULLBYTE','Sterilizza null-bytes');
define($constpref.'_SAN_NULLBYTEDSC','Il carattere terminale "\\0" &egrave; spesso utilizzato negli attacchi malevolis.<br />Ogni null-byte verr&agrave; sostituito con uno spazio.<br />(Raccomandato: S&igrave;)');
define($constpref.'_DIE_NULLBYTE','Esci se viene identificato un null-bytes');
define($constpref.'_DIE_NULLBYTEDSC','Il carattere terminale "\\0" &egrave; spesso utilizzato negli attacchi malevolis.<br />(Raccomandato: S&igrave;)');
define($constpref.'_DIE_BADEXT','Esci se vengono inviati file malevoli');
define($constpref.'_DIE_BADEXTDSC','Se qualcuno cerca di effettuare l\'upload di file con estensione potenzialmente pericolosa (ad es. .php), il modulo esce da Xoops.<br />Se carichi spesso file php in moduli tipo B-Wiki o PukiWikiMod, disattivare questa funzione.');
define($constpref.'_CONTAMI_ACTION','Azione se rilevata contaminazione');
define($constpref.'_CONTAMI_ACTIONDS','Seleziona l\'azione da intraprendere qualora venga identificata una contaminazione delle variabili globali di sistema in Xoops.<br />(Raccomandato: schermata bianca)');
define($constpref.'_ISOCOM_ACTION','Azione se rilevato commento isolato');
define($constpref.'_ISOCOM_ACTIONDSC','Anti iniezione SQL:<br />Seleziona l\'azione da intraprendere qualora venga identificato un commento isolato ("/*").<br />"Sterilizzazione" significa aggiungere un altro "*/" in coda.<br />(Raccomandato: Sterilizzazione)');
define($constpref.'_UNION_ACTION','Azioen se rilevato UNION');
define($constpref.'_UNION_ACTIONDSC','Anti iniziezione SQL:<br />Seleziona l\'azione da intraprendere qualora venga identificata una sintassi di tipo UNION.<br />"Sterilizza" significa sostituire la parola chiave "union" in "uni-on".<br />(Raccomandato: Sterilizza)');
define($constpref.'_ID_INTVAL','Forza valori interi per le variabili tipo id');
define($constpref.'_ID_INTVALDSC','Tutte le richieste di parametri "*id" verranno trattate come numeri interi.<br />Questa opzione protegge da alcuni tipi di attacchi XSS e a iniezione SQL.<br />(Raccomandato: S&igrave; - Pu&ograve; causare problemi con alcuni oduli)');
define($constpref.'_FILE_DOTDOT','Protezioni da Attraversamento Directory');
define($constpref.'_FILE_DOTDOTDSC','Elimina dai percorsi il ".." da tutte le richieste che assomigliano ad attacchi da Attraversamento Directory');

define($constpref.'_BF_COUNT','Anti Forza Bruta');
define($constpref.'_BF_COUNTDSC','Conteggia il numero di tentativi di login di un utente anonimo in 10 minuti. Se il login fallisce pi&&ugrave; volte di quanto specificato qui, il suo IP viene espulso (Ban).');

define($constpref.'_DOS_SKIPMODS','Moduli esclusi dal controllo DoS/Crawler');
define($constpref.'_DOS_SKIPMODSDSC','Impostare i nomi delle cartelle dei moduli separate da |. Questa opzione &egrave; utile sui moduli chat, ecc...');

define($constpref.'_DOS_EXPIRE','Tempo di controllo per caricamenti frequenti (sec)');
define($constpref.'_DOS_EXPIREDSC','Questo valore specifica il tempo di controllo per i frequenti caricamenti del sito (attacchi da F5) e crawler troppo invasivi.');

define($constpref.'_DOS_F5COUNT','Contatore Attacchi da F5');
define($constpref.'_DOS_F5COUNTDSC','Previene gli attacchi Denial Of Service da F5.<br />Questo valore specifica il numero di caricamenti consecutivi da considerare come attacco malevolo.');
define($constpref.'_DOS_F5ACTION','Azione contro Attacchi da F5');

define($constpref.'_DOS_CRCOUNT','Contatore Crawler');
define($constpref.'_DOS_CRCOUNTDSC','Previene l\'esaurimento delle risorse server da parte di crawlers troppo invasivi.<br />Questo valore specifica il numero di accessi da considerare eccessivi per un crawler.');
define($constpref.'_DOS_CRACTION','Azione contro Crawler troppo invasivi');

define($constpref.'_DOS_CRSAFE','User-Agent benvenuti');
define($constpref.'_DOS_CRSAFEDSC','Un pattern regex per gli User-Agent.<br />Se coincidente, il crawler non verr&agrave; mai considerato troppo invasivo.<br />(es.) /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','Nessuna (solo log)');
define($constpref.'_OPT_SAN','Sterilizzazione');
define($constpref.'_OPT_EXIT','Schermata bianca');
define($constpref.'_OPT_BIP','Espulsione IP (Nessun limite)');
define($constpref.'_OPT_BIPTIME0','Espulsione IP (Moratoria)');

define($constpref.'_DOSOPT_NONE','Nessuna (solo log)');
define($constpref.'_DOSOPT_SLEEP','Sospensione');
define($constpref.'_DOSOPT_EXIT','Schermata bianca');
define($constpref.'_DOSOPT_BIP','Espulsione IP (Nessun limite)');
define($constpref.'_DOSOPT_BIPTIME0','Espulsione IP (Moratoria)');
define($constpref.'_DOSOPT_HTA','DENY da .htaccess (Sperimentale)');

define($constpref.'_BIP_EXCEPT','Gruppi da non registrare come IP malevoli');
define($constpref.'_BIP_EXCEPTDSC','Un utente appartenente ai gruppi specificati non verr&agrave; mai espulso.<br />(Raccomandato: tutti i gruppi di Amministrazione)');

define($constpref.'_DISABLES','Disabilita funzionalit&agrave; pericolose di XOOPS');

define($constpref.'_BIGUMBRELLA','Abilita sistema anti-XSS (BigUmbrella)');
define($constpref.'_BIGUMBRELLADSC','Questo protegge dalla maggior parte degli attacchi che sfruttano vulnerabilit&agrave; XSS. Ma non funziona al 100%');

define($constpref.'_SPAMURI4U','Anti-SPAM: numero di indirizzi per gli utenti normali');
define($constpref.'_SPAMURI4UDSC','Se in un invio di dati POST da parte di un utente (ad eccezione degli amministratori) vengono rilevati pi&ugrave; indirizzi URL di quanto consentito qui, l\'invio viene considerato SPAM. Impostare 0 per disabilitare questa funzionalit&agrave;.');
define($constpref.'_SPAMURI4G','Anti-SPAM: numero di indirizzi per gli utenti anonimi');
define($constpref.'_SPAMURI4GDSC','Se in un invio di dati POST da parte di un utente anonimi vengono rilevati pi&ugrave; indirizzi URL di quanto consentito qui, l\'invio viene considerato SPAM. Impostare 0 per disabilitare questa funzionalit&agrave;..');

}

?>
