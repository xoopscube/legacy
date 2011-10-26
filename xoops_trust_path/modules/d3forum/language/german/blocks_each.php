<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","Forum");
//define($constpref."_TOPIC","Topic");
define('_MB_D3FORUM_TOPIC','Thema');
//define($constpref."_REPLIES","Replies");
define('_MB_D3FORUM_REPLIES','Antworten');
//define($constpref."_VIEWS","Views");
define('_MB_D3FORUM_VIEWS','Ansichten');
//define($constpref."_VOTESCOUNT","Votes");
define('_MB_D3FORUM_VOTESCOUNT','Bewertungen');
//define($constpref."_VOTESSUM","Scores");
define('_MB_D3FORUM_VOTESSUM','Punkte');
//define($constpref."_LASTPOST","Last Post");
define('_MB_D3FORUM_LASTPOST','Letzter Eintrag');
//define($constpref."_LASTUPDATED","Last Updated");
define('_MB_D3FORUM_LASTUPDATED','Letztes Update');
//define($constpref."_LINKTOSEARCH","Search in the forum");
define('_MB_D3FORUM_LINKTOSEARCH','Suchen im Forum');
//define($constpref."_LINKTOLISTCATEGORIES","Category Index");
define('_MB_D3FORUM_LINKTOLISTCATEGORIES','Kategorieindex');
//define($constpref."_LINKTOLISTFORUMS","Forum Index");
define('_MB_D3FORUM_LINKTOLISTFORUMS','Forumindex');
//define($constpref."_LINKTOLISTTOPICS","Topic Index");
define('_MB_D3FORUM_LINKTOLISTTOPICS','Themenindex');
//define($constpref."_ALT_UNSOLVED","Unsolved topic");
define('_MB_D3FORUM_ALT_UNSOLVED','Ungeklärtes Thema');
//define($constpref."_ALT_MARKED","Marked topic");
define('_MB_D3FORUM_ALT_MARKED','Markiertes Thema');

}

?>
