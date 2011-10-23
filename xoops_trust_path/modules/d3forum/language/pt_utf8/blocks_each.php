<?php
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum';
$constpref = '_MB_' . strtoupper( $mydirname );
if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// Appended by Xoops Language Checker -GIJOE- in 2007-09-26 17:55:48
define($constpref.'_ALT_UNSOLVED','Unsolved topic');
define($constpref.'_ALT_MARKED','Marked topic');

define( $constpref.'_LOADED' , 1 );
// definitions for displaying blocks
define($constpref."_FORUM","Forum");
define($constpref."_TOPIC","Topico");
define($constpref."_REPLIES","Respostas");
define($constpref."_VIEWS","Visualizadas");
define($constpref."_VOTESCOUNT","Votos");
define($constpref."_VOTESSUM","Contagens");
define($constpref."_LASTPOST","Ultimo post");
define($constpref."_LASTUPDATED","Ultima atualização");
define($constpref."_LINKTOSEARCH","Busca no Forum");
define($constpref."_LINKTOLISTCATEGORIES","Categotia Principal");
define($constpref."_LINKTOLISTFORUMS","Forum Principal");
define($constpref."_LINKTOLISTTOPICS","Topico Principal");
}
?>