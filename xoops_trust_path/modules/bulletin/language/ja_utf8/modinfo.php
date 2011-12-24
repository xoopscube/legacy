<?php
// Module Info

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'bulletin' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref.'_NAME','ニュース');

// A brief description of this module
define($constpref.'_DESC','ユーザが自由にコメントできる、スラッシュドット風のニュース記事システムを構築します');

// Names of blocks for this module (Not all module has blocks)
define($constpref.'_BNAME1','ニュースカテゴリ');
define($constpref.'_BDESC1','');
define($constpref.'_BNAME2','本日のトップニュース');
define($constpref.'_BDESC2','');
define($constpref.'_BNAME3','カレンダー');
define($constpref.'_BDESC3','');
define($constpref.'_BNAME4','最新ニュース');
define($constpref.'_BDESC4','');
define($constpref.'_BNAME5','カテゴリ別最新ニュース');
define($constpref.'_BDESC5','');
define($constpref.'_BNAME6','ブリティン新着コメント');
define($constpref.'_BDESC6','');

// Sub menu
define($constpref.'_SMNAME1','ニュース投稿');
define($constpref.'_SMNAME2','アーカイブ');

// Admin
define($constpref.'_ADMENU2','カテゴリ管理');
define($constpref.'_ADMENU3','新しいニュース記事の投稿');
define($constpref.'_ADMENU4','投稿権限の管理');
define($constpref.'_ADMENU5','ニュース記事の管理');
define($constpref.'_ADMENU7','newsからインポート');
define($constpref.'_ADMENU_MYLANGADMIN','言語定数管理');
define($constpref.'_ADMENU_MYTPLSADMIN','テンプレート管理');
define($constpref.'_ADMENU_MYBLOCKSADMIN','ブロック/権限管理');

// Title of config items
define($constpref.'_CONFIG1', 'トップページに掲載する記事数');
define($constpref.'_CONFIG1_D', 'トップページに表示する記事の数を指定してください。');
define($constpref.'_CONFIG2', 'ナビゲーションボックスを表示する');
define($constpref.'_CONFIG2_D', 'カテゴリを選択するナビゲーションボックスを記事の上部に表示するには「はい」を選択してください。');
define($constpref.'_CONFIG3','投稿・編集用テキストエリアの高さ');
define($constpref.'_CONFIG3_D', 'submit.phpページのテキストエリアの行数を設定します。');
define($constpref.'_CONFIG4','投稿・編集用テキストエリアの幅');
define($constpref.'_CONFIG4_D', 'submit.phpページのテキストエリアのカラム数を設定します。');
define($constpref.'_CONFIG5','日付・日時の書式');
define($constpref.'_CONFIG5_D', '文字の書式はPHPのdate関数・XOOPSのformatTimestamp関数を参照してください。');
define($constpref.'_CONFIG6','投稿をユーザーの投稿数に反映');
define($constpref.'_CONFIG6_D', 'submit.phpから投稿された記事が承認された際に、そのユーザの「投稿数」に加算します。');
define($constpref.'_CONFIG7','カテゴリアイコンがあるディレクトリのパス');
define($constpref.'_CONFIG7_D', '絶対パスで指定します。');
define($constpref.'_CONFIG8','印刷ページの画像のURL');
define($constpref.'_CONFIG8_D', '印刷用ページに表示されるロゴ画像をURLで指定します。');
define($constpref.'_CONFIG9','記事名をサイトのタイトルにする');
define($constpref.'_CONFIG9_D', '記事の題名をサイトのタイトルに置き換えます。SEOの面で有効だと言われています。');
define($constpref.'_CONFIG10','xoops_module_headerにRSSのURLをassignする');
define($constpref.'_CONFIG10_D', '');
// 1.01 added
define($constpref.'_CONFIG11','「印刷する」アイコンを表示する');
define($constpref.'_CONFIG11_D', '');
define($constpref.'_CONFIG12','「友達に知らせる」アイコンを表示する');
define($constpref.'_CONFIG12_D', '');
define($constpref.'_CONFIG13','Tell A Friendモジュールを利用する');
define($constpref.'_CONFIG13_D', '');
define($constpref.'_CONFIG14','RSSのリンクを表示する');
define($constpref.'_CONFIG14_D', '');
define($constpref.'_CONFIG145','RSSをbackend.phpにもfeedする(XCLのみ)');
define($constpref.'_CONFIG145_D', '');
// 2.00 added
define($constpref.'_CONFIG15','関連記事機能を有効にする');
define($constpref.'_CONFIG15_D', '');
define($constpref.'_CONFIG16','カテゴリの最新記事を表示する');
define($constpref.'_CONFIG16_D', '各記事の下に同一カテゴリの最新記事一覧が表示されます。');
define($constpref.'_CONFIG17','カテゴリの最新記事の記事数');
define($constpref.'_CONFIG17_D', '各記事の下に表示する同一カテゴリの最新記事一覧の記事数を指定します。');
define($constpref.'_CONFIG18','カテゴリのパンくずリストを表示する');
define($constpref.'_CONFIG18_D', '');
define($constpref.'_CONFIG19','common/fckeditorを利用する');
define($constpref.'_CONFIG19_D', 'HTMLが許可されている編集者にはFCKeditor on XOOPSを利用できるようにします。');

define($constpref.'_COM_DIRNAME','コメント統合するd3forumのdirname');
define($constpref.'_COM_FORUM_ID','コメント統合するフォーラムの番号');
define($constpref.'_COM_VIEW','コメント統合の表示方法');
define($constpref.'_COM_ORDER','コメント統合の表示順序');
define($constpref.'_COM_POSTSNUM','コメント統合のフラット表示における最大表示件数');

// by yoshis
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'カテゴリーアクセス権限' ) ;
define($constpref.'_IMAGES_DIR','イメージファイルディレクトリ');
define($constpref.'_IMAGES_DIRDSC','このモジュール用のイメージが格納されたディレクトリをモジュールディレクトリからの相対パスで指定します。デフォルトはimagesです。');

// Text for notifications
define($constpref.'_GLOBAL_NOTIFY', 'モジュール全体');
define($constpref.'_GLOBAL_NOTIFYDSC', 'ニュースモジュール全体における通知オプション');

define($constpref.'_STORY_NOTIFY', '表示中のニュース記事');
define($constpref.'_STORY_NOTIFYDSC', '表示中のニュース記事に対する通知オプション');

define($constpref.'_GLOBAL_NEWCATEGORY_NOTIFY', '新規カテゴリ');
define($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYCAP', '新規カテゴリが作成された場合に通知する');
define($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYDSC', '新規カテゴリが作成された場合に通知する');
define($constpref.'_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規カテゴリが作成されました');

define($constpref.'_GLOBAL_STORYSUBMIT_NOTIFY', '新規ニュース投稿承認待ち');
define($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYCAP', '新規承認待ちニュースの投稿があった場合に通知する');
define($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYDSC', '新規承認待ちニュースの投稿があった場合に通知する');
define($constpref.'_GLOBAL_STORYSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規承認待ちニュースの投稿がありました');

define($constpref.'_GLOBAL_NEWSTORY_NOTIFY', '新規ニュース記事掲載');
define($constpref.'_GLOBAL_NEWSTORY_NOTIFYCAP', '新規ニュース記事が掲載された場合に通知する');
define($constpref.'_GLOBAL_NEWSTORY_NOTIFYDSC', '新規ニュース記事が掲載された場合に通知する');
define($constpref.'_GLOBAL_NEWSTORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規ニュースが掲載されました');

define($constpref.'_STORY_APPROVE_NOTIFY', 'ニュース記事の承認');
define($constpref.'_STORY_APPROVE_NOTIFYCAP', 'このニュース記事が承認された場合に通知する');
define($constpref.'_STORY_APPROVE_NOTIFYDSC', 'このニュース記事が承認された場合に通知する');
define($constpref.'_STORY_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: ニュース記事が承認されました');

// added 2.01
define($constpref.'_NOTIFY5_TITLE', '新規コメント投稿');
define($constpref.'_NOTIFY5_CAPTION', 'この記事にコメントがついた場合通知する');
define($constpref.'_NOTIFY5_DESC', 'この記事にコメントがついた場合通知する');
define($constpref.'_NOTIFY5_SUBJECT', '[{X_SITENAME}] {X_MODULE}: コメントの投稿がありました');

}
?>