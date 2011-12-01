<?php /* English Translation by Marcelo Yuji Himoro <http://yuji.ws> */
// Blocks
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

define($constpref."_NOTYET","There is no top story today, yet.");
define($constpref."_TMRSI","Today's most read story is:");
define($constpref."_ORDER","Order by");
define($constpref."_DATE","published date");
define($constpref."_HITS","number of hits");
define($constpref."_DISP","Display");
define($constpref."_ARTCLS","articles");
define($constpref."_CHARS","Length of the title at");
define($constpref."_LENGTH","bytes");
define($constpref."_MON","MO");
define($constpref."_TUE","TU");
define($constpref."_WED","WE");
define($constpref."_THE","TH");
define($constpref."_FRI","FR");
define($constpref."_SAT","<span style=\"color:blue\">SA</span>");
define($constpref."_SUN","<span style=\"color:red\">SU</span>");
define($constpref."_DATE_FORMAT","m/Y");

define($constpref."_DISP_TOPICID","Display category ! Subcategories directly belonging to this parent category will be displayed. <br/>you can specify parent categories multiply by numbers separated with comma. <br/>(0=show all)");
define($constpref."_DISP_HOMETEXT","Number of news showing hometext");
define($constpref."_DIPS_ICON","Display category icon");

define($constpref."_READMORE","Read more...");
define($constpref."_COMMENTS","0 comments");
define($constpref."_ONECOMMENT","1 comment");
define($constpref."_BYTESMORE","%s bytes to go");
define($constpref."_NUMCOMMENTS","%s comments");

define($constpref."_MORE","See more news");

}
?>