<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {





// Appended by Xoops Language Checker -GIJOE- in 2007-09-28 15:55:32
define($constpref.'_DEFAULT_OPTIONS','Default checked in post form');
define($constpref.'_DEFAULT_OPTIONSDSC','List checked options separated by comma(,).<br />eg) smiley,xcode,br,number_entity<br />You can add these options: special_entity html attachsig u2t_marked');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-27 16:50:41
define($constpref.'_BODY_EDITOR','Body Editor');
define($constpref.'_BODY_EDITORDSC','WYSIWYG editor will be enabled under only forums allowing HTML. With forums escaping HTML specialchars, xoopsdhtml will be displayed automatically.');

// Appended by Xoops Language Checker -GIJOE- in 2007-09-26 17:55:47
define($constpref.'_ADMENU_POSTHISTORIES','Histories');

// Appended by Xoops Language Checker -GIJOE- in 2007-06-05 03:58:10
define($constpref.'_ADMENU_MYLANGADMIN','Languages');
define($constpref.'_ADMENU_MYTPLSADMIN','Templates');
define($constpref.'_ADMENU_MYBLOCKSADMIN','Blocks/Permissions');
define($constpref.'_ADMENU_MYPREFERENCES','Preferences');
define($constpref.'_SHOW_BREADCRUMBS','Display breadcrumbs');
define($constpref.'_ANTISPAM_GROUPS','Groups should be checked anti-SPAM');
define($constpref.'_ANTISPAM_GROUPSDSC','Usually set all blank.');
define($constpref.'_ANTISPAM_CLASS','Class name of anti-SPAM');
define($constpref.'_ANTISPAM_CLASSDSC','Default value is "default". If you disable anti-SPAM against guests even, set it blank');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","Forum");

// A brief description of this module
define($constpref."_DESC","Modu³ forum dla XOOPS");

// Names of blocks for this module (Not all module has blocks)
define($constpref."_BNAME_LIST_TOPICS","Tematy");
define($constpref."_BDESC_LIST_TOPICS","This block can be used for multi-purpose. Of course, you can put it multiplly.");
define($constpref."_BNAME_LIST_POSTS","Posty");
define($constpref."_BNAME_LIST_FORUMS","Fora");

define($constpref.'_ADMENU_CATEGORYACCESS','Uprawnienia dla kategorii');
define($constpref.'_ADMENU_FORUMACCESS','Uprawnienia dla forum');
define($constpref.'_ADMENU_ADVANCEDADMIN','Opcje zaawansowane');

// configurations
define($constpref.'_TOP_MESSAGE','Wiadomo¶æ powitalna (widoczna na górze forum)');
define($constpref.'_TOP_MESSAGEDEFAULT','<h1 class="d3f_title">Forum</h1><p class="d3f_welcome">Rozpocznij przygodê na forum od wybrania kategorii któr± chcesz odwiedziæ.</p>');
define($constpref.'_ALLOW_HTML','Zezwól na HTML');
define($constpref.'_ALLOW_HTMLDSC','Nie u¿ywaæ bezmy¶lnie, mo¿e powodowaæ ataki hakerów przy pomocy skryptów.');
define($constpref.'_ALLOW_TEXTIMG','Pozwalaj na wy¶wietlanie zewnêtrznych grafik w po¶cie');
define($constpref.'_ALLOW_TEXTIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define($constpref.'_ALLOW_SIG','Zezwól na sygnatury');
define($constpref.'_ALLOW_SIGDSC','');
define($constpref.'_ALLOW_SIGIMG','Pozwala na wy¶wietlanie zewnêtrznych obrazków w sygnaturze');
define($constpref.'_ALLOW_SIGIMGDSC','If some attackers post an external image using [img], he can know IPs or User-Agents of users visited your site.');
define($constpref.'_USE_VOTE','W³±cz opcjê g³osowania');
define($constpref.'_USE_SOLVED','W³±cz opcjê oznaczania jako rozwi±zany');
define($constpref.'_ALLOW_MARK','W³±cz opcjê oznaczania jako wyró¿niony');
define($constpref.'_ALLOW_HIDEUID','Pozwól zarejestrowanym u¿ytkownikom postowaæ nie ukazuj±c swojego imienia');
define($constpref.'_POSTS_PER_TOPIC','Maksymalna liczba postów w temacie');
define($constpref.'_POSTS_PER_TOPICDSC','Temat ma limit postów.');
define($constpref.'_HOT_THRESHOLD','Ilo¶æ postów potrzebnych aby temat zosta³ oznaczony jako popularny');
define($constpref.'_HOT_THRESHOLDDSC','');
define($constpref.'_TOPICS_PER_PAGE','Ilo¶æ tematów wy¶wietlanych na jednej stronie');
define($constpref.'_TOPICS_PER_PAGEDSC','');
define($constpref.'_VIEWALLBREAK','Ilo¶æ tematów wy¶wietlanych na jednej stronie (in the view crossing forums)');
define($constpref.'_VIEWALLBREAKDSC','');
define($constpref.'_SELFEDITLIMIT','Okres mo¿liwo¶ci edycji posta (sekundy)');
define($constpref.'_SELFEDITLIMITDSC','Wpisz ilo¶æ sekund po których u¿ytkownik nie bêdzie mia³ mo¿liwo¶ci edycji swojego posta. Aby wy³±czyæ mo¿liwo¶æ edycji wpisz warto¶æ 0 (zero)');
define($constpref.'_SELFDELLIMIT','Okres mo¿liwo¶ci kasowania posta (sekundy)');
define($constpref.'_SELFDELLIMITDSC','Wpisz ilo¶æ sekund po których u¿ytkownik nie bêdzie mia³ mo¿liwo¶ci kasowania swojego posta. Aby wy³±czyæ mo¿liwo¶æ kasowania wpisz warto¶æ 0 (zero)');
define($constpref.'_CSS_URI','¦cie¿ka do pliku CSS dla forum');
define($constpref.'_CSS_URIDSC','Mo¿esz u¿yæ ¶cie¿ki relatywnej lub absolutnej. Domy¶lnie: index.css');
define($constpref.'_IMAGES_DIR','Katalog dla zdjêæ');
define($constpref.'_IMAGES_DIRDSC','¦cie¿ka relatywna, powinna byæ w katalogu modu³u. Domy¶lnie: images');
define($constpref.'_ANONYMOUS_NAME','Nazwa dla anonima');
define($constpref.'_ANONYMOUS_NAMEDSC','');
define($constpref.'_ICON_MEANINGS','Znaczenie ikon');
define($constpref.'_ICON_MEANINGSDSC','Okre¶l znaczniki ALT dla ikon. Oddzielaj pionow± kresk± (|). pierwsza ikona to "posticon0.gif".');
define($constpref.'_ICON_MEANINGSDEF','none|normal|unhappy|happy|raise it|lower it|report|question');
define($constpref.'_GUESTVOTE_IVL','Pozwól niezarejestrowanym g³osowaæ');
define($constpref.'_GUESTVOTE_IVLDSC','Ustaw warto¶æ na 0 (zero) aby wy³±czyæ mo¿liwo¶æ g³osowania dla niezarejestrowanych. Inna warto¶æ bêdzie oznacza³a czas (w sekundach) oczekiwania na ponown± mo¿liwo¶æ g³osowania z tego samego IP.');



// Notify Categories
define($constpref.'_NOTCAT_TOPIC', 'Temat');
define($constpref.'_NOTCAT_TOPICDSC', 'Powiadomienia dotycz±ce wybranego tematu');
define($constpref.'_NOTCAT_FORUM', 'Forum');
define($constpref.'_NOTCAT_FORUMDSC', 'Powiadomienia dotycz±ce wybranego forum');
define($constpref.'_NOTCAT_CAT', 'Kategoria');
define($constpref.'_NOTCAT_CATDSC', 'Powiadomienia dotycz±ce wybranej kategorii');
define($constpref.'_NOTCAT_GLOBAL', 'Modu³');
define($constpref.'_NOTCAT_GLOBALDSC', 'Powiadomienia dotycz±ce ca³ego modu³u.');

// Each Notifications
define($constpref.'_NOTIFY_TOPIC_NEWPOST', 'Nowy post w temacie');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTCAP', 'Powiadom mnie o nowych postach w tym temacie.');
define($constpref.'_NOTIFY_TOPIC_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{TOPIC_TITLE} Nowy post w temacie');

define($constpref.'_NOTIFY_FORUM_NEWPOST', 'Nowy post w forum');
define($constpref.'_NOTIFY_FORUM_NEWPOSTCAP', 'Powiadom mnie o nowych postach w tym forum.');
define($constpref.'_NOTIFY_FORUM_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nowy post w forum');

define($constpref.'_NOTIFY_FORUM_NEWTOPIC', 'Nowy tmeat w forum');
define($constpref.'_NOTIFY_FORUM_NEWTOPICCAP', 'Powiadom mnie o nowych tematach w tym forum.');
define($constpref.'_NOTIFY_FORUM_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{FORUM_TITLE} Nowy tmeat w forum');

define($constpref.'_NOTIFY_CAT_NEWPOST', 'Nowy post w kategorii');
define($constpref.'_NOTIFY_CAT_NEWPOSTCAP', 'Powiadom mnie o nowych postach w tej kategorii .');
define($constpref.'_NOTIFY_CAT_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nowy post w kategorii');

define($constpref.'_NOTIFY_CAT_NEWTOPIC', 'Nowy temat w kategorii');
define($constpref.'_NOTIFY_CAT_NEWTOPICCAP', 'Powiadom mnie o nowych tematach w tej kategorii.');
define($constpref.'_NOTIFY_CAT_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nowy temat w kategorii');

define($constpref.'_NOTIFY_CAT_NEWFORUM', 'Nowe forum w kategorii');
define($constpref.'_NOTIFY_CAT_NEWFORUMCAP', 'Powiadom mnie o nowych forach w tyj kategorii.');
define($constpref.'_NOTIFY_CAT_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} Nowe forum w kategorii');

define($constpref.'_NOTIFY_GLOBAL_NEWPOST', 'Nowy post');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTCAP', 'Powiadom mnie o nowych postach w tym module.');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTSBJ', '[{X_SITENAME}] {X_MODULE}: Nowy post');

define($constpref.'_NOTIFY_GLOBAL_NEWTOPIC', 'Nowy temat');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICCAP', 'Powiadom mnie o nowych tematach w tym module.');
define($constpref.'_NOTIFY_GLOBAL_NEWTOPICSBJ', '[{X_SITENAME}] {X_MODULE}: Nowy temat');

define($constpref.'_NOTIFY_GLOBAL_NEWFORUM', 'Nowe forum');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMCAP', 'Powiadom mnie o nowych forach w tym module.');
define($constpref.'_NOTIFY_GLOBAL_NEWFORUMSBJ', '[{X_SITENAME}] {X_MODULE}: Nowe forum');

define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULL', 'Nowy post (pe³ny tekst)');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLCAP', 'Powiadom mnie o nowych postach (zawrzyj tre¶æ posta).');
define($constpref.'_NOTIFY_GLOBAL_NEWPOSTFULLSBJ', '[{X_SITENAME}] {POST_TITLE}');
define($constpref.'_NOTIFY_GLOBAL_WAITING', 'Nowe oczekuj±ce');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCAP', 'Powiadom mnie o nowych postach oczekuj±cych na akceptacjê. Tylko dla adminów');
define($constpref.'_NOTIFY_GLOBAL_WAITINGSBJ', '[{X_SITENAME}] {X_MODULE}: Nowe oczekuj±ce');

}

?>