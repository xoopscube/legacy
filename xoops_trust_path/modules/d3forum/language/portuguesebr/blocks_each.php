<?php

// site par contúdo traduzidos para o CMS XOOPS
// PORTAL X-TRAD - http://www.x-trad.org/
// traduzido por artsgeral

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

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
define($constpref."_ALT_UNSOLVED","Tópico não foi salvo");
define($constpref."_ALT_MARKED","Sinalizar tópico");

}

?>
