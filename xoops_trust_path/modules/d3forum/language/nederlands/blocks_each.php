<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","Forum");
define($constpref."_TOPIC","Onderwerp");
define($constpref."_REPLIES","Reacties");
define($constpref."_VIEWS","Bekeken");
define($constpref."_VOTESCOUNT","Stemmen");
define($constpref."_VOTESSUM","Scores");
define($constpref."_LASTPOST","Laatste bericht");
define($constpref."_LASTUPDATED","Laatste wijziging");
define($constpref."_LINKTOSEARCH","Zoek in het forum");
define($constpref."_LINKTOLISTCATEGORIES","Categorie index");
define($constpref."_LINKTOLISTFORUMS","Forum index");
define($constpref."_LINKTOLISTTOPICS","Onderwerp index");
define($constpref."_ALT_UNSOLVED","Onopgelost onderwerp");
define($constpref."_ALT_MARKED","Gemarkeerd onderwerp");

}

?>