<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Forum");

// A brief description of this module
define($constpref."_DESC","Forum module voor XOOPS");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","Onderwerpen");
define($constpref."_BDESC_LIST_TOPICS","Dit blok kan voor meerdere doelen gebruikt worden. Er kunnen meerder exemplaren van dit blok op &uuml;&uuml;n pagina staan");
define($constpref."_BNAME_LIST_POSTS","Berichten");
define($constpref."_BNAME_LIST_FORUMS","Fora");

// admin menu
define($constpref.'_ADMENU_CATEGORYACCESS','Permissies per categorie');
define($constpref.'_ADMENU_FORUMACCESS','Permissies per forum');
define($constpref.'_ADMENU_ADVANCEDADMIN','Geavanceerd');
define($constpref.'_ADMENU_POSTHISTORIES','Geschiedenis');
define($constpref.'_ADMENU_MYLANGADMIN','Talen');
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blokken/Permissies');
define($constpref.'_ADMENU_MYPREFERENCES','Voorkeuren');

// configurations
define($constpref.'_TOP_MESSAGE','Bericht boven aan forum index');
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Forum Index</h1><p class="d3f_welcome">Om berichten te bekijken, kies je een categorie en een forum uit het aanbod hieronder.</p>');
define($constpref.'_SHOW_BREADCRUMBS','Toon navigatiepad');
define($constpref.'_DEFAULT_OPTIONS','Standaard geselecteerde opties bij plaatsen bericht');
define($constpref.'_DEFAULT_OPTIONSDSC','Voer de te selecteren opties in gescheiden door een komma(,).<br />bijv. smiley,xcode,br,number_entity<br />Je kunt deze opties gebruiken: special_entity html attachsig u2t_marked');
define($constpref.'_ALLOW_HTML','Sta gebruik HTML toe');
define($constpref.'_ALLOW_HTMLDSC','Denk na voor je dit toestaat. Kwaadwillende gebruikers kunnen zo scripts in hun forumbericht plaatsen.');
define($constpref.'_ALLOW_TEXTIMG','Sta het gebruik plaatjes van externe servers toe');
define($constpref.'_ALLOW_TEXTIMGDSC','De eigenaar van de andere server kan de IP adressen van de forumgebruikers opslaan');
define($constpref.'_ALLOW_SIG','Sta gebruik handetekening toe');
define($constpref.'_ALLOW_SIGDSC','');
define($constpref.'_ALLOW_SIGIMG','Sta gebruik externe plaatjes toe in handtekening');
define($constpref.'_ALLOW_SIGIMGDSC','De eigenaar van de andere server kan de IP adressen van de forumgebruikers opslaan.');
define($constpref.'_USE_VOTE','Schakel stemmen op berichten in');
define($constpref.'_USE_SOLVED','Schakel de opgelost/onopgelost markering in');
define($constpref.'_ALLOW_MARK','Schakel de onderwerp markeren optie in');
define($constpref.'_ALLOW_HIDEUID','Geregistreerde gebruikers mogen anoniem berichten plaatsen');
define($constpref.'_POSTS_PER_TOPIC','Maximaal aantal berichten in een onderwerp');
define($constpref.'_POSTS_PER_TOPICDSC','Een onderwerp met meer berichten wordt automatisch gesloten.');
define($constpref.'_HOT_THRESHOLD','Populair onderwerp limiet');
define($constpref.'_HOT_THRESHOLDDSC','Berichten met meer reacties dan dit limiet worden gemarkeerd als populair onderwerp');
define($constpref.'_TOPICS_PER_PAGE','Aantal onderwerpen per pagina in de forum weergave');
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','Onderwerpen per pagina in de alle fora weergave');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','Tijdlimiet voor bewerken berichten (in sec.)');
define($constpref.'_SELFEDITLIMITDSC','Als gebruikers hun eigen berichten mogen bewerken, vul dan hier de tijd in waarbinnen ze dat kunnen doen. Om dit te verbieden, vul 0 in.');
define($constpref.'_SELFDELLIMIT','Tijdslimiet voor verwijderen berichten (in sec.)');
define($constpref.'_SELFDELLIMITDSC','Als gebruikers hun eigen berichten mogen verwijderen, vul dan hier de tijd in waarbinnen ze dat kunnen doen. Om dit te verbieden, vul 0 in. Als er onderliggende berichten zijn, kan een bericht nooit worden verwijderd.');
define($constpref.'_CSS_URI','Pad naar CSS bestand voor deze module');
define($constpref.'_CSS_URIDSC','Een relatief of absoluut pad kan worden opgegeven. Standaard: {mod_url}/index.php?page=main_css');
define($constpref.'_IMAGES_DIR','Map met forum-afbeeldingen');
define($constpref.'_IMAGES_DIRDSC','Een relatief pad moet worden ingevuld binnen de module map. Standaard: images');
define($constpref.'_BODY_EDITOR','Bericht editor');
define($constpref.'_BODY_EDITORDSC','WYSIWYG editor wordt alleen ingeschakeld voor fora waar HTML in berichten toegestaan is. Op andere fora wordt automatisch de bosis XOOPS editor gebruikt.');
define($constpref.'_ANONYMOUS_NAME','Anonieme naam');
define($constpref.'_ANONYMOUS_NAMEDSC','Gebruikersnaam die wordt getoond bij anoniem geplaatste berichten');
define($constpref.'_ICON_MEANINGS','Legenda van iconen');
define($constpref.'_ICON_MEANINGSDSC','Vul hier de omschrijving in van de bericht iconen, gescheiden door een verticaal streepje (|). De eerste omschrijving hoort bij "posticon0.gif".');
define($constpref.'_ICON_MEANINGSDEF','none|normal|unhappy|happy|lower it|raise it|report|question');
define($constpref.'_GUESTVOTE_IVL','Stemmen door gasten');
define($constpref.'_GUESTVOTE_IVLDSC','Vul hier het aantal seconden dat een niet-ingelogde gebruiker moet wachten tot hij weer mag stemmen. Zet dit op 0 om stemmen door gasten uit te schakelen.');
define($constpref.'_ANTISPAM_GROUPS','Spam filter groepen');
define($constpref.'_ANTISPAM_GROUPSDSC','Groepen waarvan berichten door het spamfilter moet worden gehaald. Standaard: geen.');
define($constpref.'_ANTISPAM_CLASS','Klasse voor het spamfilter');
define($constpref.'_ANTISPAM_CLASSDSC','Standaard waarde is "default". Als je de spam controle zelfs voor gasten wilt uitschakelen, laat dit veld dan leeg.');


// Notify Categories
define($constpref.'_NOTCAT_TOPIC', 'Dit onderwerp'); 
define($constpref.'_NOTCAT_TOPICDSC', 'Notificaties voor dit onderwerp');
define($constpref.'_NOTCAT_FORUM', 'Dit forum'); 
define($constpref.'_NOTCAT_FORUMDSC', 'Notificaties voor dit forum');
define($constpref.'_NOTCAT_CAT', 'Deze categorie');
define($constpref.'_NOTCAT_CATDSC', 'Notificaties voor deze categorie');
define($constpref.'_NOTCAT_GLOBAL', 'Deze module');
define($constpref.'_NOTCAT_GLOBALDSC', 'Notificaties voor de hele module (alle fora)');

// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'Nieuwe reactie op dit onderwerp');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Hou me op de hoogte van nieuwe reacties op dit onderwerp.');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Nieuwe reactie op: {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWPOST', 'Nieuw bericht in dit forum');
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Hou me op de hoogte van nieuwe berichten in dit forum.');
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nieuw bericht: {POST_TITLE}');

define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'Nieuw onderwerp in dit forum');
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Hou me op de hoogte van nieuwe onderwerpen in dit forum.');
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nieuw onderwerp: {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWPOST', 'Nieuwe bericht in deze categorie');
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Hou me op de hoogte van nieuwe berichten in deze categorie.');
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nieuw bericht: {POST_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'Nieuw onderwerp in deze categorie');
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Hou me op de hoogte van nieuwe onderwerpen in deze categorie.');
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nieuw onderwerp: {TOPIC_TITLE}');

define($constpref.'_NOTIFY_CAT_NEWFORUM', 'Nieuw forum in deze categorie');
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Hou me op de hoogte van nieuwe fora in deze categorie');
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nieuw forum');

define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'Nieuwe berichten in deze module');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Hou me op de hoogte van nieuwe berichten in deze module (alle fora).');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: Nieuw bericht: {POST_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'Nieuw onderwerp in deze module');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Hou me op de hoogte van nieuwe onderwerpen in deze module (alle fora).');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: Nieuw onderwerp {TOPIC_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'Nieuw forum in deze module');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Hou me op de hoogte van nieuwe fora in deze module (alle categorie&euml;n).');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: Nieuw forum');

define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'Nieuwe berichten (met inhoud)');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Hou me op de hoogte van nieuwe berichten en stuur de inhoud van het bericht mee');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');

define($constpref.'_NOTIFY_GLOBAL_WAITING', 'Nieuw goed te keuren');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Hou me op de hoogte van nieuwe berichten die wachten op goedkeuring. Alleen voor beheerders.');
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Nieuw te keuren: {POST_TITLE}');

}

?>