<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {


// Appended by Xoops Language Checker -GIJOE- in 2007-04-05 12:11:22
define($constpref.'_ALT_UNSOLVED','Unsolved topic');
define($constpref.'_ALT_MARKED','Marked topic');

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","Форум");
define($constpref."_TOPIC","Тема");
define($constpref."_REPLIES","Ответы");
define($constpref."_VIEWS","Просмотры");
define($constpref."_VOTESCOUNT","Голоса");
define($constpref."_VOTESSUM","Очки");
define($constpref."_LASTPOST","Последнее сообщение");
define($constpref."_LASTUPDATED","Последнее обновление");
define($constpref."_LINKTOSEARCH","Поиск по форуму");
define($constpref."_LINKTOLISTCATEGORIES","Список категорий");
define($constpref."_LINKTOLISTFORUMS","Список форумов");
define($constpref."_LINKTOLISTTOPICS","Список тем");

}

?>
