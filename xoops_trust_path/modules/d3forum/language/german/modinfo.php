<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Forum");

// A brief description of this module
////define($constpref."_DESC","Forum module for XOOPS");
define('_MI_D3FORUM_DESC','Forum Modul für Xoops');
define('_MI_D3FORUM_DESC','Forum Modul für Xoops');

// Names of blocks for this module (Not all module has blocks)
////define($constpref."_BNAME_LIST_TOPICS","Topics");
define('_MI_D3FORUM_BNAME_LIST_TOPICS','Themen');
define('_MI_D3FORUM_BNAME_LIST_TOPICS','Themen');
////define($constpref."_BDESC_LIST_TOPICS","This block can be used for multi-purpose. Of course, you can put it multiplly.");
define('_MI_D3FORUM_BDESC_LIST_TOPICS','Dies ist ein Mehrzweckblock, der mehrfach eingefügt werden kann.');
define('_MI_D3FORUM_BDESC_LIST_TOPICS','Dies ist ein Mehrzweckblock, der mehrfach eingefügt werden kann.');
////define($constpref."_BNAME_LIST_POSTS","Posts");
define('_MI_D3FORUM_BNAME_LIST_POSTS','Einträge');
define('_MI_D3FORUM_BNAME_LIST_POSTS','Einträge');
////define($constpref."_BNAME_LIST_FORUMS","Forums");
define('_MI_D3FORUM_BNAME_LIST_FORUMS','Foren');
define('_MI_D3FORUM_BNAME_LIST_FORUMS','Foren');

// admin menu
////define($constpref.'_ADMENU_CATEGORYACCESS','Permissions of Categories');
define('_MI_D3FORUM_ADMENU_CATEGORYACCESS','Kategorieberechtigungen');
define('_MI_D3FORUM_ADMENU_CATEGORYACCESS','Kategorieberechtigungen');
////define($constpref.'_ADMENU_FORUMACCESS','Permissions of Forums');
define('_MI_D3FORUM_ADMENU_FORUMACCESS','Forumberechtigungen');
define('_MI_D3FORUM_ADMENU_FORUMACCESS','Forumberechtigungen');
////define($constpref.'_ADMENU_ADVANCEDADMIN','Advanced');
define('_MI_D3FORUM_ADMENU_ADVANCEDADMIN','Erweitert');
define('_MI_D3FORUM_ADMENU_ADVANCEDADMIN','Erweitert');
////define($constpref.'_ADMENU_POSTHISTORIES','Histories');
define('_MI_D3FORUM_ADMENU_POSTHISTORIES','Verlauf');
define('_MI_D3FORUM_ADMENU_POSTHISTORIES','Verlauf');
////define($constpref.'_ADMENU_MYLANGADMIN','Languages');
define('_MI_D3FORUM_ADMENU_MYLANGADMIN','Sprachen');
define('_MI_D3FORUM_ADMENU_MYLANGADMIN','Sprachen');
////define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define('_MI_D3FORUM_ADMENU_MYTPLSADMIN','Vorlagen (Templates)');
define('_MI_D3FORUM_ADMENU_MYTPLSADMIN','Vorlagen (Templates)');
////define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define('_MI_D3FORUM_ADMENU_MYBLOCKSADMIN','Blockberechtigungen');
define('_MI_D3FORUM_ADMENU_MYBLOCKSADMIN','Blockberechtigungen');
////define($constpref.'_ADMENU_MYPREFERENCES','Preferences');
define('_MI_D3FORUM_ADMENU_MYPREFERENCES','Voreinstellungen');
define('_MI_D3FORUM_ADMENU_MYPREFERENCES','Voreinstellungen');

// configurations
////define($constpref.'_TOP_MESSAGE','Message in forum top');
define('_MI_D3FORUM_TOP_MESSAGE','Nachricht im obersten Forum');
define('_MI_D3FORUM_TOP_MESSAGE','Nachricht im obersten Forum');
////define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Forum Top</h1><p class="d3f_welcome">To start viewing messages, select the category and forum that you want to visit from the selection below.</p>');
define('_MI_D3FORUM_TOP_MESSAGEDEFAULT','<h1 class=\"d3f_title\">Forum Top</h1><p class=\"d3f_welcome\">Um die Einträge zu sehen, wählen Sie bitte Kategorie und Forum aus untenstehender Liste.</p>');
define('_MI_D3FORUM_TOP_MESSAGEDEFAULT','<h1 class=\"d3f_title\">Forum Top</h1><p class=\"d3f_welcome\">Um die Einträge zu sehen, wählen Kategorie und Forum aus untenstehender Liste.</p>');
////define($constpref.'_SHOW_BREADCRUMBS','Display breadcrumbs');
define('_MI_D3FORUM_SHOW_BREADCRUMBS','Schlagworte anzeigen');
define('_MI_D3FORUM_SHOW_BREADCRUMBS','Schlagworte anzeigen');
////define($constpref.'_DEFAULT_OPTIONS','Default checked in post form');
define('_MI_D3FORUM_DEFAULT_OPTIONS','Voreinstellung in Formular angehakt');
define('_MI_D3FORUM_DEFAULT_OPTIONS','Voreinstellung in Formular angehakt');
define($constpref.'_DEFAULT_OPTIONSDSC','List checked options separated by comma(,).<br />eg) smiley,xcode,br,number_entity<br />You can add these options: special_entity html attachsig u2t_marked');
////define($constpref.'_ALLOW_HTML','Allow HTML');
define('_MI_D3FORUM_ALLOW_HTML','HTML zulassen');
define('_MI_D3FORUM_ALLOW_HTML','HTML zulassen');
////define($constpref.'_ALLOW_HTMLDSC','Don\'t turn this on casually. It cause Script Insertion vulnerability if malicious user can post.');
define('_MI_D3FORUM_ALLOW_HTMLDSC','Sollte nicht generell zugelassen werden um SPAM-Einträge zu unterbinden!');
define('_MI_D3FORUM_ALLOW_HTMLDSC','Sollte nicht generell zugelassen werden um SPAM-Einträge zu unterbinden!');
////define($constpref.'_ALLOW_TEXTIMG','Allow to dipslay external images in the post');
define('_MI_D3FORUM_ALLOW_TEXTIMG','Anzeige externer Bilder zulassen');
define('_MI_D3FORUM_ALLOW_TEXTIMG','Anzeige externer Bilder zulassen');
////define($constpref.'_ALLOW_TEXTIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define('_MI_D3FORUM_ALLOW_TEXTIMGDSC','Sollte aus Sicherheitsgründen nicht zugelassen werden.');
define('_MI_D3FORUM_ALLOW_TEXTIMGDSC','Sollte aus Sicherheitsgründen nicht zugelassen werden.');
////define($constpref.'_ALLOW_SIG','Allow Signature');
define('_MI_D3FORUM_ALLOW_SIG','Signaturen zulassen');
define('_MI_D3FORUM_ALLOW_SIG','Signaturen zulassen');
////define($constpref.'_ALLOW_SIGDSC','');
define('_MI_D3FORUM_ALLOW_SIGDSC','Signaturen unter den Einträgen erlauben.');
define('_MI_D3FORUM_ALLOW_SIGDSC','Signaturen unter den Einträgen erlauben.');
////define($constpref.'_ALLOW_SIGIMG','Allow to display external images in the signature');
define('_MI_D3FORUM_ALLOW_SIGIMG','Anzeige externer Bilder in der Signatur zulassen');
define('_MI_D3FORUM_ALLOW_SIGIMG','Anzeige externer Bilder in der Signatur zulassen');
////define($constpref.'_ALLOW_SIGIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define('_MI_D3FORUM_ALLOW_SIGIMGDSC','Sollte aus Sicherheitsgründen nicht zugelassen werden.');
define('_MI_D3FORUM_ALLOW_SIGIMGDSC','Sollte aus Sicherheitsgründen nicht zugelassen werden.');
////define($constpref.'_USE_VOTE','use the feature of VOTE');
define('_MI_D3FORUM_USE_VOTE','Merkmal Abstimmung benutzen');
define('_MI_D3FORUM_USE_VOTE','Merkmal Abstimmung benutzen');
////define($constpref.'_USE_SOLVED','use the feature of SOLVED');
define('_MI_D3FORUM_USE_SOLVED','Merkmal "Problem gelöst" benutzen');
define('_MI_D3FORUM_USE_SOLVED','Merkmal "Problem gelöst" benutzen');
////define($constpref.'_ALLOW_MARK','use the feature of MARKING');
define('_MI_D3FORUM_ALLOW_MARK','Merkmal \'Markiert\' benutzen');
define('_MI_D3FORUM_ALLOW_MARK','Merkmal \"Markiert\" benutzen');
////define($constpref.'_ALLOW_HIDEUID','Allow a registered user can post without his/her name');
define('_MI_D3FORUM_ALLOW_HIDEUID','Lässt zu, dass registrierte Benutzer ohne Namensnennung posten dürfen');
define('_MI_D3FORUM_ALLOW_HIDEUID','Lässt zu, dass registrierte Benutzer ohne Namensnennung posten dürfen');
////define($constpref.'_POSTS_PER_TOPIC','Max posts in a topic');
define('_MI_D3FORUM_POSTS_PER_TOPIC','Max. Anzahl Einträge je Thema');
define('_MI_D3FORUM_POSTS_PER_TOPIC','Max. Anzahl Einträge je Thema');
////define($constpref.'_POSTS_PER_TOPICDSC','A topic having this number of posts will be locked automatically.');
define('_MI_D3FORUM_POSTS_PER_TOPICDSC','Erreicht ein Thema die eingestellte Anzahl Einträge, wird es automatisch gesperrt.');
define('_MI_D3FORUM_POSTS_PER_TOPICDSC','Erreicht ein Thema die eingestellte Anzahl Einträge, wird es automatisch gesperrt.');
////define($constpref.'_HOT_THRESHOLD','Hot Topic Threshold');
define('_MI_D3FORUM_HOT_THRESHOLD','Grenzwert \'Heißes Thema\'');
define('_MI_D3FORUM_HOT_THRESHOLD','Grenzwert \"Heißes Thema\"');
////define($constpref.'_HOT_THRESHOLDDSC','');
define('_MI_D3FORUM_HOT_THRESHOLDDSC','Anzahl der Einträge ab wann ein Thema als \'Heißes Thema\' gilt.');
define('_MI_D3FORUM_HOT_THRESHOLDDSC','Anzahl der Einträge ab wann ein Thema als \"Heißes Thema\" gilt.');
////define($constpref.'_TOPICS_PER_PAGE','Topics per a page in the view of a forum');
define('_MI_D3FORUM_TOPICS_PER_PAGE','Themen pro Seite in der Forumansicht');
define('_MI_D3FORUM_TOPICS_PER_PAGE','Themen pro Seite in der Forumansicht');
////define($constpref.'_TOPICS_PER_PAGEDSC','');
define('_MI_D3FORUM_TOPICS_PER_PAGEDSC','Gibt an, wieviel Themen in der Forumansicht angezeigt werden');
define('_MI_D3FORUM_TOPICS_PER_PAGEDSC','Gibt an, wieviel Themen in der Forumansicht angezeigt werden');
////define($constpref.'_VIEWALLBREAK','Topics per a page in the view crossing forums');
define('_MI_D3FORUM_VIEWALLBREAK','Themen pro Seite über alle Foren');
define('_MI_D3FORUM_VIEWALLBREAK','Themen pro Seite über alle Foren');
////define($constpref.'_VIEWALLBREAKDSC','');
define('_MI_D3FORUM_VIEWALLBREAKDSC','Gibt an, wieviel Themen über alle Foren angezeigt werden');
define('_MI_D3FORUM_VIEWALLBREAKDSC','Gibt an, wieviel Themen über alle Foren angezeigt werden');
////define($constpref.'_SELFEDITLIMIT','Time limit for users edit (sec)');
define('_MI_D3FORUM_SELFEDITLIMIT','Zeitlimit zum Ändern');
define('_MI_D3FORUM_SELFEDITLIMIT','Zeitlimit zum Ändern');
////define($constpref.'_SELFEDITLIMITDSC','To allow normal users can edit his/her posts, set plus value as seconds. To disallow normal users can edit it, set 0.');
define('_MI_D3FORUM_SELFEDITLIMITDSC','Gibt das Zeitlimit in Sekunden an, innerhalb dessen Benutzer ihre Einträge ändern können. Der Eintrag 0 untersagt das nachträgliche ändern.');
define('_MI_D3FORUM_SELFEDITLIMITDSC','Gibt das Zeitlimit in Sekunden an, innerhalb dessen Benutzer ihre Einträge ändern können. Der Eintrag 0 untersagt das nachträgliche ändern.');
////define($constpref.'_SELFDELLIMIT','Time limit for users delete (sec)');
define('_MI_D3FORUM_SELFDELLIMIT','Zeitlimit zum Löschen');
define('_MI_D3FORUM_SELFDELLIMIT','Zeitlimit zum Löschen');
////define($constpref.'_SELFDELLIMITDSC','To allow normal users can delete his/her posts, set plus value as seconds. To disallow normal users can delete it, set 0. Anyway any parent posts cannot be removed.');
define('_MI_D3FORUM_SELFDELLIMITDSC','Gibt das Zeitlimit in Sekunden an, innerhalb dessen Benutzer ihre Einträge löschen können. Der Eintrag 0 untersagt das nachträgliche löschen. Zugehörige Eltern-Einträge können nicht gelöscht werden!');
define('_MI_D3FORUM_SELFDELLIMITDSC','Gibt das Zeitlimit in Sekunden an, innerhalb dessen Benutzer ihre Einträge löschen können. Der Eintrag 0 untersagt das nachträgliche löschen. Zugehörige Eltern-Einträge können nicht gelöscht werden!');
////define($constpref.'_CSS_URI','URI of CSS file for this module');
define('_MI_D3FORUM_CSS_URI','URL zur CSS-Datei für dieses Modul');
define('_MI_D3FORUM_CSS_URI','URL zur CSS-Datei für dieses Modul');
////define($constpref.'_CSS_URIDSC','relative or absolute path can be set. default: {mod_url}/index.php?page=main_css');
define('_MI_D3FORUM_CSS_URIDSC','Es kann der relative oder absolute Pfad angegeben werden.
Voreinstellung: {mod_url}/index.php?page=main_css');
define('_MI_D3FORUM_CSS_URIDSC','Es kann der relative oder absolute Pfad angegeben werden.
Voreinstellung: {mod_url}/index.php?page=main_css');
////define($constpref.'_IMAGES_DIR','Directory for image files');
define('_MI_D3FORUM_IMAGES_DIR','Verzeichnis für die Bilddateien');
define('_MI_D3FORUM_IMAGES_DIR','Verzeichnis für die Bilddateien');
////define($constpref.'_IMAGES_DIRDSC','relative path should be set in the module directory. default: images');
define('_MI_D3FORUM_IMAGES_DIRDSC','Es sollte der relative Pfad angegeben werden.
Voreinstellung: images');
define('_MI_D3FORUM_IMAGES_DIRDSC','Es sollte der relative Pfad angegeben werden.
Voreinstellung: images');
////define($constpref.'_BODY_EDITOR','Body Editor');
define('_MI_D3FORUM_BODY_EDITOR','Editor für den Beitragstext');
define('_MI_D3FORUM_BODY_EDITOR','Editor für den Beitragstext');
////define($constpref.'_BODY_EDITORDSC','WYSIWYG editor will be enabled under only forums allowing HTML. With forums escaping HTML specialchars, xoopsdhtml will be displayed automatically.');
define('_MI_D3FORUM_BODY_EDITORDSC','Der WYSIWYG-Editor wird nur in Foren mit erlaubtem HTML angezeigt, ansonsten der DHTML-Editor.');
define('_MI_D3FORUM_BODY_EDITORDSC','Der WYSIWYG-Editor wird nur in Foren mit erlaubtem HTML angezeigt, ansonsten der DHTML-Editor.');
////define($constpref.'_ANONYMOUS_NAME','Anonymous Name');
define('_MI_D3FORUM_ANONYMOUS_NAME','Name für anonyme Benutzer');
define('_MI_D3FORUM_ANONYMOUS_NAME','Name für anonyme Benutzer');
define($constpref.'_ANONYMOUS_NAMEDSC','');
////define($constpref.'_ICON_MEANINGS','Meanings of icons');
define('_MI_D3FORUM_ICON_MEANINGS','Bedeutung von Icons');
define('_MI_D3FORUM_ICON_MEANINGS','Bedeutung von Icons');
////define($constpref.'_ICON_MEANINGSDSC','Specify ALTs of icons. each alts should be separated by pipe(|). The first alt corresponds "posticon0.gif".');
define('_MI_D3FORUM_ICON_MEANINGSDSC','Legt den ALT-Wert für Icons fest. Jeder Eintrag muss durch | getrennt werden.');
define('_MI_D3FORUM_ICON_MEANINGSDSC','Legt den ALT-Wert für Icons fest. Jeder Eintrag muss durch | getrennt werden.');
////define($constpref.'_ICON_MEANINGSDEF','none|normal|unhappy|happy|lower it|raise it|report|question');
define('_MI_D3FORUM_ICON_MEANINGSDEF','Kein|Normal|Ärgerlich|Glücklich|Herabsetzen|Heraufsetzen|Berichten|Frage');
define('_MI_D3FORUM_ICON_MEANINGSDEF','Kein|Normal|Ärgerlich|Glücklich|Herabsetzen|Heraufsetzen|Berichten|Frage');
////define($constpref.'_GUESTVOTE_IVL','Vote from guests');
define('_MI_D3FORUM_GUESTVOTE_IVL','Abstimmung für Gäste');
define('_MI_D3FORUM_GUESTVOTE_IVL','Abstimmung für Gäste');
////define($constpref.'_GUESTVOTE_IVLDSC','Set this 0, to disable voting from guest. The other this number means time(sec.) to allow second post from the same IP.');
define('_MI_D3FORUM_GUESTVOTE_IVLDSC','0 schaltet die Abstimmung für Gäste ab. Ansonsten gibt der Eintrag die Zeit an, nach der ein 2. Eintrag von der gleichen IP-Adresse möglich ist.');
define('_MI_D3FORUM_GUESTVOTE_IVLDSC','0 schaltet die Abstimmung für Gäste ab. Ansonsten gibt der Eintrag die Zeit an, nach der ein 2. Eintrag von der gleichen IP-Adresse möglich ist.');
////define($constpref.'_ANTISPAM_GROUPS','Groups should be checked anti-SPAM');
define('_MI_D3FORUM_ANTISPAM_GROUPS','Gruppen sollten auf anti-SPAM gesetzt sein');
define('_MI_D3FORUM_ANTISPAM_GROUPS','Gruppen sollten auf anti-SPAM gesetzt sein');
////define($constpref.'_ANTISPAM_GROUPSDSC','Usually set all blank.');
define('_MI_D3FORUM_ANTISPAM_GROUPSDSC','(Bleibt für gewöhnlich ohne Eintrag)');
define('_MI_D3FORUM_ANTISPAM_GROUPSDSC','(Bleibt für gewöhnlich ohne Eintrag)');
////define($constpref.'_ANTISPAM_CLASS','Class name of anti-SPAM');
define('_MI_D3FORUM_ANTISPAM_CLASS','Klassen-Name für anti-SPAM');
define('_MI_D3FORUM_ANTISPAM_CLASS','Klassen-Name für anti-SPAM');
////define($constpref.'_ANTISPAM_CLASSDSC','Default value is "default". If you disable anti-SPAM against guests even, set it blank');
define('_MI_D3FORUM_ANTISPAM_CLASSDSC','Voreinstellung ist \'default\'. Soll anti-SPAM auch für Gäste abgeschaltet sein, dann Feld leer lassen.');
define('_MI_D3FORUM_ANTISPAM_CLASSDSC','Voreinstellung ist \"default\". Soll anti-SPAM auch für Gäste abgeschaltet sein, dann Feld leer lassen.');


// Notify Categories
////define($constpref.'_NOTCAT_TOPIC', 'This topic'); 
define('_MI_D3FORUM_NOTCAT_TOPIC','Dieses Thema');
define('_MI_D3FORUM_NOTCAT_TOPIC','Dieses Thema');
////define($constpref.'_NOTCAT_TOPICDSC', 'Notifications about the targetted topic');
define('_MI_D3FORUM_NOTCAT_TOPICDSC','Benachrichtigungen für das gewählte Thema');
define('_MI_D3FORUM_NOTCAT_TOPICDSC','Benachrichtigungen für das gewählte Thema');
////define($constpref.'_NOTCAT_FORUM', 'This forum'); 
define('_MI_D3FORUM_NOTCAT_FORUM','Dieses Forum');
define('_MI_D3FORUM_NOTCAT_FORUM','Dieses Forum');
////define($constpref.'_NOTCAT_FORUMDSC', 'Notifications about the targetted forum');
define('_MI_D3FORUM_NOTCAT_FORUMDSC','Benachrichtigungen für das gewählte Forum');
define('_MI_D3FORUM_NOTCAT_FORUMDSC','Benachrichtigungen für das gewählte Forum');
////define($constpref.'_NOTCAT_CAT', 'This category');
define('_MI_D3FORUM_NOTCAT_CAT','Diese Kategorie');
define('_MI_D3FORUM_NOTCAT_CAT','Diese Kategorie');
////define($constpref.'_NOTCAT_CATDSC', 'Notifications about the targetted category');
define('_MI_D3FORUM_NOTCAT_CATDSC','Benachrichtigungen für die gewählte Kategorie');
define('_MI_D3FORUM_NOTCAT_CATDSC','Benachrichtigungen für die gewählte Kategorie');
////define($constpref.'_NOTCAT_GLOBAL', 'This module');
define('_MI_D3FORUM_NOTCAT_GLOBAL','Dieses Modul');
define('_MI_D3FORUM_NOTCAT_GLOBAL','Dieses Modul');
////define($constpref.'_NOTCAT_GLOBALDSC', 'Notifications about whole of the module');
define('_MI_D3FORUM_NOTCAT_GLOBALDSC','Benachrichtigungen für das ganze Modul');
define('_MI_D3FORUM_NOTCAT_GLOBALDSC','Benachrichtigungen für das ganze Modul');

// Each Notifications
////define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'New post in the topic');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOST','Neuer Eintrag im Thema');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOST','Neuer Eintrag im Thema');
////define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Notify me of new posts in the current topic.');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen im aktuellen Thema');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen im aktuellen Thema');
////define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} New post in topic');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Neuer Eintrag im Thema');
define('_MI_D3FORUM_NOTIFY_TOPIC_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Neuer Eintrag im Thema');

////define($constpref.'_NOTIFY_FORUM_NEWPOST', 'New post in the forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOST','Neuer Eintrag im Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOST','Neuer Eintrag im Forum');
////define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Notify me of new posts in the current forum.');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen im aktuellen Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen im aktuellen Forum');
////define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New post in forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Neuer Eintrag im Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Neuer Eintrag im Forum');

////define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'New topic in the forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPIC','Neues Thema im Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPIC','Neues Thema im Forum');
////define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Notify me of new topics in the current forum.');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPICCAP','Benachrichtigen Sie mich bei neuen Themen im aktuellen Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPICCAP','Benachrichtigen Sie mich bei neuen Themen im aktuellen Forum');
////define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} New topic in forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Neues Thema im Forum');
define('_MI_D3FORUM_NOTIFY_FORUM_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Neues Thema im Forum');

////define($constpref.'_NOTIFY_CAT_NEWPOST', 'New post in the category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOST','Neuer Eintrag in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOST','Neuer Eintrag in Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Notify me of new posts in the current category.');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen in der aktuellen Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOSTCAP','Benachrichtigen Sie mich bei neuen Einträgen in der aktuellen Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New post in category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neuer Eintrag in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neuer Eintrag in Kategorie');

////define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'New topic in the category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPIC','Neues Thema in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPIC','Neues Thema in Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Notify me of new topics in the current category.');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPICCAP','Benachrichtigen Sie mich bei neuen Themen in der aktuellen Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPICCAP','Benachrichtigen Sie mich bei neuen Themen in der aktuellen Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New topic in category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neues Thema in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neues Thema in Kategorie');

////define($constpref.'_NOTIFY_CAT_NEWFORUM', 'New forum in the category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUM','Neues Forum in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUM','Neues Forum in Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Notify me of new forums in the current category.');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUMCAP','Benachrichtigen Sie mich bei neuen Foren in der aktuellen Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUMCAP','Benachrichtigen Sie mich bei neuen Foren in der aktuellen Kategorie');
////define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} New forum in category');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUMSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neues Forum in Kategorie');
define('_MI_D3FORUM_NOTIFY_CAT_NEWFORUMSBJ','[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Neues Forum in Kategorie');

////define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'New post in the module');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOST','Neuer Eintrag (generell)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOST','Neuer Eintrag (generell)');
////define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Notify me of new posts in the module.');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTCAP','Benachrichtigen Sie mich bei jedem neuen Eintrag');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTCAP','Benachrichtigen Sie mich bei jedem neuen Eintrag');
////define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: New post');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}: Neuer Eintrag');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTSBJ','[{X_SITENAME}] {X_MODULE}: Neuer Eintrag');

////define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'New topic in the module');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPIC','Neues Thema (generell)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPIC','Neues Thema (generell)');
////define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Notify me of new topics in the module.');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPICCAP','Benachrichtigen Sie mich bei jedem neuen Thema');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPICCAP','Benachrichtigen Sie mich bei jedem neuen Thema');
////define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: New topic');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}: Neues Thema');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWTOPICSBJ','[{X_SITENAME}] {X_MODULE}: Neues Thema');

////define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'New forum in the module');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUM','Neues Forum (generell)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUM','Neues Forum (generell)');
////define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Notify me of new forums in the module.');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUMCAP','Benachrichtigen Sie mich bei jedem neuen Forum');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUMCAP','Benachrichtigen Sie mich bei jedem neuen Forum');
////define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: New forum');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUMSBJ','[{X_SITENAME}] {X_MODULE}: Neues Forum');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWFORUMSBJ','[{X_SITENAME}] {X_MODULE}: Neues Forum');

////define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'New Post (Full Text)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTFULL','Neuer Eintrag (Vollständiger Text)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTFULL','Neuer Eintrag (Vollständiger Text)');
////define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Notify me of any new posts (include full text in message).');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTFULLCAP','Benachrichtigen Sie mich bei jedem neuen Eintrag (einschl. vollem Text)');
define('_MI_D3FORUM_NOTIFY_GLOBAL_NEWPOSTFULLCAP','Benachrichtigen Sie mich bei jedem neuen Eintrag (einschl. vollem Text)');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
////define($constpref.'_NOTIFY_GLOBAL_WAITING', 'New waiting');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITING','Wartende Einträge');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITING','Wartende Einträge');
////define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Notify me of new posts waiting approval. For admins only');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITINGCAP','Benachrichtigung über wartende Einträge. Nur für Administratoren');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITINGCAP','Benachrichtigung über wartende Einträge. Nur für Administratoren');
////define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: New waiting');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITINGSBJ','[{X_SITENAME}] {X_MODULE}: Wartender Eintrag');
define('_MI_D3FORUM_NOTIFY_GLOBAL_WAITINGSBJ','[{X_SITENAME}] {X_MODULE}: Wartender Eintrag');

}

?>
