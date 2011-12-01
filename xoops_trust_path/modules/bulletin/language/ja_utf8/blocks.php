<?php
// Blocks
$constpref = '_MB_' . strtoupper( $mydirname ) ;

if( defined( "FOR_XOOPS_LANG_CHECKER" ) || ! defined( $constpref."_LOADED" ) ) {

// a flag for this language file has already been read or not.
define( $constpref."_LOADED" , 1 ) ;

define($constpref."_NOTYET","本日のトップコンテンツはありません");// ver 3.00 changed
define($constpref."_TMRSI","本日最も読まれたコンテンツは：");// ver 3.00 changed
define($constpref."_ORDER","並び順");
define($constpref."_DATE","掲載日時");
define($constpref."_HITS","ヒット数");
define($constpref."_DISP","表示件数：");
define($constpref."_ARTCLS","件");
define($constpref."_CHARS","表示件名の長さ");
define($constpref."_LENGTH"," バイト");
define($constpref."_MON","月");
define($constpref."_TUE","火");
define($constpref."_WED","水");
define($constpref."_THE","木");
define($constpref."_FRI","金");
define($constpref."_SAT","<span style=\"color:blue\">土</span>");
define($constpref."_SUN","<span style=\"color:red\">日</span>");
define($constpref."_DATE_FORMAT","Y年m月");

define($constpref."_DISP_TOPICID","表示カテゴリ※複数指定する時はカテゴリー番号をカンマ(,)で区切る。<br/>サブカテゴリーは含まないことに注意（必要なら、各サブカテゴリーを明示的に指定すること）<br/>（0で全カテゴリを表示）");
define($constpref."_DISP_HOMETEXT","本文を表示する件数");
define($constpref."_DIPS_ICON","カテゴリアイコンを表示");

define($constpref."_READMORE","続きを読む");
define($constpref."_COMMENTS","0コメント");
define($constpref."_ONECOMMENT","1コメント");
define($constpref."_BYTESMORE","残り%s字");
define($constpref."_NUMCOMMENTS","%sコメント");

define($constpref."_MORE","もっと記事を見る");

}
?>