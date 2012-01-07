<?php
// Module Info

// The name of this module
define("_MI_SEARCH_NAME","XOOPS検索");

// A brief description of this module
define("_MI_SEARCH_DESC","XOOPSの検索機能を日本語向けに改変しモジュールに移植したものです。");

// SubMenu
define("_MI_SEARCH_SUB1","コメント一覧");

// Blocks
define("_MI_SEARCH_BLICK1","サイト内検索");
define("_MI_SEARCH_BLICK_DESC1","検索フォームをブロックに表示します。");
define("_MI_SEARCH_BLICK2","サイト内検索[転送用]");
define("_MI_SEARCH_BLICK_DESC2", "このブロックをオンにしていると".XOOPS_URL."/search.phpへのリクエストが有った際に自動的にこのモジュールに転送できます。");

// Templates
define("_MI_SEARCH_TEMPLATE_DESC1","検索結果");
define("_MI_SEARCH_TEMPLATE_DESC2","モジュール別検索結果");
define("_MI_SEARCH_TEMPLATE_DESC3","検索トップページ");

// Admin menu
define("_MI_SEARCH_MENU1","トップページ");
define("_MI_SEARCH_MENU_DESC1","トップページ");
define("_MI_SEARCH_MENU2","グループ/ブロック管理");
define("_MI_SEARCH_MENU_DESC2","このモジュールのアクセス権とブロックの管理");
define("_MI_SEARCH_MENU3","テンプレート管理");
define("_MI_SEARCH_MENU_DESC3","このモジュールのテンプレートを管理");
define("_MI_SEARCH_MENU4","除外モジュール管理");
define("_MI_SEARCH_MENU_DESC4","検索の対象から除外するモジュールの管理");
define("_MI_SEARCH_MENU5","XOOPS検索について");
define("_MI_SEARCH_MENU_DESC5","このモジュールについての説明");

// Title of config items
define("_MI_SEARCH_CONFIG1","本文を表示する");
define("_MI_SEARCH_CONFIG_DESC1","検索結果に本文の該当する部分を表示します。");
?>