<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref . '_FORUM', 'Форум');
define($constpref . '_TOPIC', 'Тема');
define($constpref . '_REPLIES', 'Ответы');
define($constpref . '_VIEWS', 'Просмотры');
define($constpref . '_VOTESCOUNT', 'Голоса');
define($constpref . '_VOTESSUM', 'Очки');
define($constpref . '_LASTPOST', 'Последнее сообщение');
define($constpref . '_LASTUPDATED', 'Последнее обновление');
define($constpref . '_LINKTOSEARCH', 'Поиск по форуму');
define($constpref . '_LINKTOLISTCATEGORIES', 'Список категорий');
define($constpref . '_LINKTOLISTFORUMS', 'Список форумов');
define($constpref . '_LINKTOLISTTOPICS', 'Список тем');
define($constpref . '_ALT_UNSOLVED', 'нерешенная тема');
define($constpref . '_ALT_MARKED', 'Отмеченная тема');

}
