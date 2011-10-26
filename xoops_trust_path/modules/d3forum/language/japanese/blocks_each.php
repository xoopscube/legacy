<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3forum' ;
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// definitions for displaying blocks 
define($constpref."_FORUM","フォーラム");
define($constpref."_TOPIC","トピック");
define($constpref."_REPLIES","返信");
define($constpref."_VIEWS","閲覧");
define($constpref."_VOTESCOUNT","投票");
define($constpref."_VOTESSUM","得票");
define($constpref."_LASTPOST","最終投稿");
define($constpref."_LASTUPDATED","最終更新");
define($constpref."_LINKTOSEARCH","フォーラム内検索へ");
define($constpref."_LINKTOLISTCATEGORIES","カテゴリー一覧へ");
define($constpref."_LINKTOLISTFORUMS","フォーラム一覧へ");
define($constpref."_LINKTOLISTTOPICS","トピック一覧へ");
define($constpref."_ALT_UNSOLVED","未解決トピック");
define($constpref."_ALT_MARKED","注目トピック");

}

?>