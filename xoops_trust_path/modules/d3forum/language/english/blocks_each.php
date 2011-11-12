<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","Forum");
define($constpref."_TOPIC","Topic");
define($constpref."_REPLIES","Replies");
define($constpref."_VIEWS","Views");
define($constpref."_VOTESCOUNT","Votes");
define($constpref."_VOTESSUM","Scores");
define($constpref."_LASTPOST","Last Post");
define($constpref."_LASTUPDATED","Last Updated");
define($constpref."_LINKTOSEARCH","Search in the forum");
define($constpref."_LINKTOLISTCATEGORIES","Category Index");
define($constpref."_LINKTOLISTFORUMS","Forum Index");
define($constpref."_LINKTOLISTTOPICS","Topic Index");
define($constpref."_ALT_UNSOLVED","Unsolved topic");
define($constpref."_ALT_MARKED","Marked topic");

}

?>