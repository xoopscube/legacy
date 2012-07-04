<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'protector' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {


// Appended by Xoops Language Checker -GIJOE- in 2009-11-17 18:12:57
define($constpref.'_FILTERS','filters enabled in this site');
define($constpref.'_FILTERSDSC','specify file names inside of filters_byconfig/ separated with LF');
define($constpref.'_MANIPUCHECK','enable manipulation checking');
define($constpref.'_MANIPUCHECKDSC','notify to admin if your root folder or index.php is modified.');
define($constpref.'_MANIPUVALUE','value for manipulation checking');
define($constpref.'_MANIPUVALUEDSC','do not edit this field');

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","ַבַֽׁ׃ בׂזָ׃");

// A brief description of this module
define($constpref."_DESC","ו׀ַ ַבָׁהַדּ םזÝׁ בדזÞÚß ַבֽדַםֹ דה ÚדבםַÊ ַבַ־ÊַׁÞ ַבד־ÊבÝֹ בדזÞÚß");

// Menu
define($constpref."_ADMININDEX","ַבֶׁם׃םֹ");
define($constpref."_ADVISORY","ÊÝֽױ ַבֽדַםֹ");
define($constpref."_PREFIXMANAGER","ַַֹֿׁ ּֿזב ÞַÚֹֿ ַבָםַהַÊ");
define($constpref.'_ADMENU_MYBLOCKSADMIN','ַבÊױַׁםֽ') ;

// Configs
define($constpref.'_GLOBAL_DISBL','ÊÚ״םב ַבדזֿםב');
define($constpref.'_GLOBAL_DISBLDSC','ÊÚ״םב ָׁהַדּ ַבַֽׁ׃ ');

define($constpref.'_DEFAULT_LANG','ַבבÛֹ');
define($constpref.'_DEFAULT_LANGDSC','common.php ֽֿֿ ַבבÛֹ ַבÊם ׃Ê׃ÊÚדב Þָב ״בָ דבÝ  ');

define($constpref.'_RELIABLE_IPS','ַבַםָםוַÊ ַבױֿםÞֹ');
define($constpref.'_RELIABLE_IPSDSC',' |ײÚ ַבַםָםוַÊ ַבÊם ÊÚÊָׁ ױֿםÞֹ זםדßה ַבַÚÊדַֿ Úבםֹ ַÝױב ַבַםָםוַÊ ָו׀ֹ ַבַװַֹׁ');

define($constpref.'_LOG_LEVEL','ֽÝÙ ַב׃ּבַÊ');
define($constpref.'_LOG_LEVELDSC','');

define($constpref.'_BANIP_TIME0','דֹֿ ַבדהÚ בבַםָם ַבדֽײזׁ - ַָבֻזַהם)');

define($constpref.'_LOGLEVEL0','ָֿזה');
define($constpref.'_LOGLEVEL15','Úַֿם');
define($constpref.'_LOGLEVEL63','Úַֿם');
define($constpref.'_LOGLEVEL255','ַבßב');

define($constpref.'_HIJACK_TOPBIT','ֽדַםֹ ַבַםָם ַֻהֱַ ַבּב׃ו-ַם ַבÊזַּֿ ַָבדזÞÚ');
define($constpref.'_HIJACK_TOPBITDSC','ַבֽדַםֹ בבַםָם דה ׃ׁÞֹ ַבßזßםׂ  . ַ׀ ßַה בß ַםָם ַָֻÊ ַ־Êַׁ 32 ַ׀ ßַה Ûםׁ ַָֻÊ ַ־Êַׁ 24 ßַÝÊַׁײם');
define($constpref.'_HIJACK_DENYGP','ַבדּדזÚַÊ ַבÛםׁ ד׃דזֽ ָהÞבוַ ַבם הÙַד ֽדַםֹ ַבּב׃ֹ');
define($constpref.'_HIJACK_DENYGPDSC','דַהÚ ֽÞה ז׃ׁÞֹ ַבßזßםׂ Ýם ַבּב׃ֹ:<br />ַ־Êַׁ ַבדּדזÚֹ ַבÛםׁ ד׃דזֽ בוַ ַָבַהÊÞַב ÊֽÊ הÙַד ַבֽדַםֹ ַֻהֱַ ַבּב׃ֹ . דה ַבדÞÊֽׁ ַ־Êםַׁ דּדזÚֹ ַבַַֹֿׁ');
define($constpref.'_SAN_NULLBYTE','null-bytes ַבÊÚÞםד בַזַדׁ דה הזÚ');
define($constpref.'_SAN_NULLBYTEDSC','"\\0" דה ַבדÞÊֽׁ ÊÝÚםב ו׀ַ ַב־םַׁ בַה ו׀ַ ַבßזֿ Ûַבַָ דַ ם׃Ê־ֿד Ýם ÚדבםַÊ ַבÊ־ׁםָ');
define($constpref.'_DIE_NULLBYTE','"\\0" ַב־ׁזּ Ýם ַֽבֹ זּזֿ  Úדבםֹ דה הזÚ הםב ַָÊ׃');
define($constpref.'_DIE_NULLBYTEDSC','"\\0" דה ַבדÞÊֽׁ ÊÝÚםב ו׀ַ ַב־םַׁ בַה ו׀ַ ַבßזֿ Ûַבַָ דַ ם׃Ê־ֿד Ýם ÚדבםַÊ ַבÊ־ׁםָ');
define($constpref.'_DIE_BADEXT','ַב־ׁזּ Ýם ַֽבֹ ׁÝÚ דבÝ ׃םֱ');
define($constpref.'_DIE_BADEXTDSC','ַ׀ ַֽזב ַֽֿ ׁÝÚ דבÝ ָױםÛֹ ָם ַÊװ ָם  ַז ױםÛֹ ַ־ׁם Ûםׁ ד׃דזֽ ָוַ<br />ַ׀ ßהÊ Ýם ַבÛַבָ ÊׁÝÚ דבÝַÊ ָױםÛֹ ָם ַÊװ ָם ÝÞד ַ׀ ָÊÚ״םב ו׀ַ ַב־םַׁ ');
define($constpref.'_CONTAMI_ACTION','דַֽזבֹ Êבזםֻ זַבÚָֻ ָדÊÛםַׁÊ ַבדּבֹ');
define($constpref.'_CONTAMI_ACTIONDS','ַ־Êַׁ ַבÚדב Ýם ַֽבֹ ַßÊװַÝ דַֽזבֹ בÊבזםֻ  זַבÚָֻ ָדÊÛםַׁÊ ַבדּבֹ ַבÚַדֹ<br />ַבדÞÊֽׁ וז  ַ־Êםַׁ ױÝֹֽ ָםײֱַ');
define($constpref.'_ISOCOM_ACTION','ַבÚדב ַֽב ַßÊװַÝ ÊÚבםÞ דבÛזד');
define($constpref.'_ISOCOM_ACTIONDSC','דַהÚ ַבֽÞ Ýם ַבÞַÚֿו:<br />"/*" ַבÚדב ַֽב ַßÊװַÝֹ ו׀ַ ַבׁדׂ Ýם ÊÚבםÞ דַ<br />ַבÊÚÞםד םÚהם ַײַÝֹ ׁדׂ ַב׃בַװ בבßזֿ בÊÚ״םבֹ - ַבÚדב ַבדÞÊֽׁ  וז ַ־Êםַׁ ÊÚÞםד ַבַדׁ');
define($constpref.'_UNION_ACTION','ַבÚדב ַֽב ַßÊװַÝ ַם דה ַזַדׁ ַבַÊַֽֿ');
define($constpref.'_UNION_ACTIONDSC','דַהÚ ַבֽÞה בבÞַÚֹֿ:<br />ַ־Êַׁ ַבÚדב ַֽב ַßÊװַÝ ַם Úדבםֹ ־ַּׁםֹ דה ÚדבםַÊ ַבַÊַֽֿ זַבÚדב ַבדÞÊֽׁ וז ÊÚÞםד ַבַדׁ<br />""union" ׃םÊד ÊÛםׁ ַבׁדׂ ָזײÚ ַֿװ  ָדהÊױÝ ַבßבדֹ');
define($constpref.'_ID_INTVAL','ID ַזַדׁ ַב״בָ זַבּבָ דה ַבÞַÚֹֿ');
define($constpref.'_ID_INTVALDSC','"*id" ßב ַבַזַדׁ ַבÊם ÊהÊום ָו׀ַ ַבׁדׂ<br />ÊÝÚםב ַב־םַׁ םֽדם דה ָÚײ ÚדבםַÊ ַבֽÞ<br />ו׀ַ ַבַ־Êםַׁ ם׃ָָ ַֽםַהַ ָÊÚ״ב ַָׁדּ ַ־ׁם ב׀בß ßד ָÊÚ״םבֹ  ַבַ ַ׀ ßהÊ ÊÚׁÝ דַ ÊÝÚב');
define($constpref.'_FILE_DOTDOT','Directory TraversalsַבדהÚ דה ÚדבםַÊ ַבÊהÞב ');
define($constpref.'_FILE_DOTDOTDSC','דהÚ ßב ַבÚדבםַÊ ַבÊם Êָֿז  Úבל ַהוַ ÊÞזד ַָ׃ÊÚַׁײ ַבדזÞÚ זַבדבÝַÊ זַבÊם Êָֻֽ Úה ֻÛַׁÊ ַָבדזÞÚ');

define($constpref.'_BF_COUNT','דַהÚ דַֽזבֹ Ê׃ּםב ַבֿ־זב ַבדÊßׁׁו');
define($constpref.'_BF_COUNTDSC','ֽֿֿ Úֿֿ ַבדַׁÊ ַבד׃דזֽ בבÚײז ָוַ בÊ׃ּםב ֿ־זבֹ ָßבדֹ ׃ׁ Ûםׁ ױֽםֹֽ זָÚֿ ַבÚֿֿ ַבדֽֿֿ ׃םÊד ״ֹֿׁ');

define($constpref.'_BWLIMIT_COUNT','Êֽֿםֿ זײָ״ ּֽד Êַָֿב ַבדבÝַÊ - ַבַָהֿזםֻֿ');
define($constpref.'_BWLIMIT_COUNTDSC','mainfile.php ײÚ ױÝׁ בבדזַÞÚ ַבÊם בֿםוַ Þֿׁו ּםֿו Úבל ַ׃ÊםÚַָ Úֿֿ בַַָ׃ ָו דה ַבׂזַׁ  זַם ׁÞד ַÞב דה 10 ׃םÊד Êַּובֹ -ֽֿֿ Úֿֿ ַבדַׁÊ ַבÊם ם׃Ê״םÚ ַבֶַׁׂ Ýםוַ ׂםַֹׁ דבÝ');

define($constpref.'_DOS_SKIPMODS',' Crawler ַבַָׁדּ ַבÛםׁ ־ַײÚֹ בהÙַד ַבדַׁÞָֹ');
define($constpref.'_DOS_SKIPMODSDSC','|Þד ָßÊַָֹ ַ׃דֱַ ַבדזֿםבַÊ ַבÊם ׃םÊד ַ׃Êֻהֱַוַ דה ַבדַׁÞָֹ  ַÝױב ָםה ַבַָׁדּ ַָבַװַׁו');

define($constpref.'_DOS_EXPIRE','דַׁÞָֹ ַבײÛ״ Úבל ַבדזÞÚ ַָבֻזַהם');
define($constpref.'_DOS_EXPIREDSC','F5ו׀ַ ַבַ־Êםַׁ בדַׁÞָֹ ַבײÛ״ ַבדֻֽֿ Úבל ַבדזÞÚ דה ־בַב ַָׁדּ ַבָֻֽ דֻבַ ַז ַֽב ַ׃Ê־ַֿד הÙַד Êֽֿםֻ ַז ׁםÝׁםװ ַבדזÞÚ ַָ׃Ê־ַֿד ַבַַֹֿ ');

define($constpref.'_DOS_F5COUNT',' F5Úֿֿ ַבדַׁÊ בַֽÊ׃ַָוַ וּזד');
define($constpref.'_DOS_F5COUNTDSC','בבֽדםַֹ דה  ַבֿז׃ זַ׃ÊהַׂÝ ַבדזÞÚ ַָÚַֹֿ Êֽדםב ױÝֹֽ ַבַָֿםֹ ַßֻׁ דה דׁו');
define($constpref.'_DOS_F5ACTION',' F5 ַבÚדב ַֽב ַßÊװַÝ וּזד דה הזÚ');

define($constpref.'_DOS_CRCOUNT','Úֿֿ דַׁÊ ַבַ׃ÊÚַׁײ דה Þָב דֽׁßַÊ ַבָֻֽ Þָב ַÚÊַָׁ ַבÚדבםֹ וּזד');
define($constpref.'_DOS_CRCOUNTDSC','בבדהÚ דה ßב ַבÚדבםַÊ ַבÊם ÊÞזד ָדַֽזבו ַ׃ÊÚַׁײ ßב דבÝַÊ זַׁזָ״ דזÞÚß זַַֻֽֿ ײÛ״ Úבםֹ');
define($constpref.'_DOS_CRACTION','ַבÚדב ַֽב ַßÊװַÝ ÚדבםַÊ ַהװֱַ ײÛ״ Úַבם Úבל ַבדזÞÚ');

define($constpref.'_DOS_CRSAFE','דֽׁßַÊ ַבָֻֽ ַבד׃דזֽ בוַ ');
define($constpref.'_DOS_CRSAFEDSC','ßב דֽׁßַÊ ַבָֻֽ ַבדײַÝֹ ַָבֽÞב בה ÊÚÊָׁ דֽׁßַÊ ָֻֽ ׃םֶֹ ַז Êֻֽֿ ײÛ״ Úבל ַבדזÞÚ<br />דֻב<br />eg) /(msnbot|Googlebot|Yahoo! Slurp)/i');

define($constpref.'_OPT_NONE','בַװםֱ ÝÞ״ ׃ּב ַבÚדבםֹ');
define($constpref.'_OPT_SAN','ÊÚÞםד ַבַדׁ');
define($constpref.'_OPT_EXIT','ױÝֹֽ ָםײֱַ');
define($constpref.'_OPT_BIP','דהÚ ַבַםָם בבַָֿ');
define($constpref.'_OPT_BIPTIME0','דהÚ ַבַםָם דִÞÊ');

define($constpref.'_DOSOPT_NONE','בַװםֱ ÝÞ״ ׃ּב ַבÚדבםֹ');
define($constpref.'_DOSOPT_SLEEP','Úֿד ַ׃Êַָֹּ-הֶַד');
define($constpref.'_DOSOPT_EXIT','ױÝֹֽ ָםײֱַ');
define($constpref.'_DOSOPT_BIP','דהÚ ַבַםָם בבַָֿ');
define($constpref.'_DOSOPT_BIPTIME0','דהÚ ַבַםָם דִÞÊ');
define($constpref.'_DOSOPT_HTA','.htaccess ַבדהÚ ָדבÝ');

define($constpref.'_BIP_EXCEPT','ַבדּדזÚֹ  ַבÊם בַ םÊד ״ֿׁוַ ַַָֿ');
define($constpref.'_BIP_EXCEPTDSC','ֽֿֿ ַםָם דÚםה   בֽדַםÊו דה ַב״ֿׁ דה ַבדזÞÚ<br />(דה ַבדÞÊֽׁ ÝÞ״ ַםָם ַבדֿםׁ');

define($constpref.'_DISABLES','XOOPS ÊÚ״םב  ־ױֶַױ ־״םֹׁ Ýם דּבֹ');

define($constpref.'_DBLAYERTRAP','ÊÝÚםב ַבÞהַÚ בײָ״ ÚדבםַÊ ַבֽÞה');
define($constpref.'_DBLAYERTRAPDSC','ו׀ַ ַבַ־Êםַׁ םדהÚ ַבÚֿםֿ דה ÚדבםַÊ ַבֽÞה . זבßה Úבםß ַבÊַßֿ דה ÊÝֽױ ַבֽדַםֹ בדÚׁÝֹ דַ ַה ßַה בֿםß ַבדַ׃ß ַז ַבÞהַÚ');
define($constpref.'_DBTRAPWOSRV','בַÊÞד ַָֿ ָÊÝֽױ ַב׃םׁÝׁ דה דַהÚ ַבֽÞה');
define($constpref.'_DBTRAPWOSRVDSC',' והַß ׃םׁÝַׁÊ בֿםוַ הÙַד דַהÚ בבֽÞה Ýם ÞַÚֹֿ ַבָםַהַÊ - בז זַּוÊ דװßבֹ ָדזÞÚß Þד ָÊÝÚםב ו׀ַ ַבַ־Êםַׁ');

define($constpref.'_BIGUMBRELLA','anti-XSS (BigUmbrella)ַבֽדַםֹ דה ַבוּזד דה הזÚ');
define($constpref.'_BIGUMBRELLADSC','ו׀ַ ַבהזÚ םÞזד ַבדוַּד ַָׁ׃ַב דֽÊזל דה ־בַבֹ םַֽזב ׃ׁÞֹ ַׁÞַד ֽ׃ַַָÊ זַםדםבַÊ זַם ָםַהַÊ ֽ׃ַ׃ֹ דה דזÞÚ ַבײֽםֹ. ַבַֽׁ׃ בַםזÝׁ ֽדַםֹ ßַדבֹ בו׀ַ ַבהזÚ  בַ־ÊבַÝ ַהזַÚ ַבוּזד ');

define($constpref.'_SPAMURI4U','דַהÚ ַב׃ַָד בבַÚײֱַ');
define($constpref.'_SPAMURI4UDSC','ַם דזײזÚ ַז ÊÚבםÞ דה Þָב ַבַÚײֱַ םֽÊזם ו׀ַ ַבÚֿֿ דה ַבׁזַָ״ ׃םÚÊָׁ ׃ַָד זײÚß ױÝׁ םÚהם ÊÚ״םב ַבַ־Êםַׁ');
define($constpref.'_SPAMURI4G','דַהÚ ַב׃ַָד בבׂזַׁ');
define($constpref.'_SPAMURI4GDSC','ַם דזײזÚ ַז ÊÚבםÞ םֽÊזם ו׀ַ ַבÚֿֿ דה ַבׁזַָ״ ׃םÚÊָׁ ׃ַָד זײÚ ױÝׁ םÚהם ÊÚ״םבß בבַ־Êםַׁ');

}

?>