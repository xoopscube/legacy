<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {






define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","Forum");
define($constpref."_TOPIC","Sujet");
define($constpref."_REPLIES","Réponses");
define($constpref."_VIEWS","Lectures");
define($constpref."_VOTESCOUNT","Votes");
define($constpref."_VOTESSUM","Points");
define($constpref."_LASTPOST","Dernières contributions");
define($constpref."_LASTUPDATED","Dérnières mises à jour");
define($constpref."_LINKTOSEARCH","Rechercher dans le forum");
define($constpref."_LINKTOLISTCATEGORIES","Index Catégories");
define($constpref."_LINKTOLISTFORUMS","Index Forum");
define($constpref."_LINKTOLISTTOPICS","Index Sujet");
define($constpref.'_ALT_UNSOLVED','Sujet Non résolu');
define($constpref.'_ALT_MARKED','Sujet Marqué');

}

?>