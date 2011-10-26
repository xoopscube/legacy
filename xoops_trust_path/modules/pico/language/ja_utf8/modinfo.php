<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'pico' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","pico");

// A brief description of this module
define($constpref."_DESC","静的コンテンツ作成モジュール");

// admin menus
define( $constpref.'_ADMENU_CONTENTSADMIN' , 'コンテンツ一括管理' ) ;
define( $constpref.'_ADMENU_CATEGORYACCESS' , 'カテゴリーアクセス権限' ) ;
define( $constpref.'_ADMENU_IMPORT' , 'インポート/同期' ) ;
define( $constpref.'_ADMENU_TAGS' , 'タグ管理' ) ;
define( $constpref.'_ADMENU_EXTRAS' , '拡張機能' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN' , '言語定数管理' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , 'テンプレート管理' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'ブロック管理/モジュールアクセス権限' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , '一般設定' ) ;

// configurations
define($constpref.'_USE_WRAPSMODE','wrapsモードを有効にする');
define($constpref.'_USE_REWRITE','mod_rewriteモードを有効にする');
define($constpref.'_USE_REWRITEDSC','これを有効にする場合、XOOPS_ROOT_PATH/modules/(dirname)/ 下にある.htaccess.rewrite_wraps（wrapsモード有効時）または.htaccess.rewrite_normal（wrapsモード無効時）を、.htaccessにリネームする必要があります。この機能は、XOOPSを運用しているサーバがApacheのmod_rewriteをサポートしていて、.htaccessでの指定が可能でなければ利用できません。');
define($constpref.'_WRAPSAUTOREGIST','HTMLラップファイルの自動DB登録');
define($constpref.'_AUTOREGISTCLASS','HTMLラップファイルの自動DB登録処理クラス');
define($constpref.'_TOP_MESSAGE','モジュールトップのメッセージ');
define($constpref.'_TOP_MESSAGEDEFAULT','');
define($constpref.'_MENUINMODULETOP','モジュールトップでは自動生成メニューを表示する');
define($constpref.'_LISTASINDEX','カテゴリートップでリストを表示する');
define($constpref.'_LISTASINDEXDSC','「はい」の場合、カテゴリートップではサブカテゴリーと直下のコンテンツがリスト式に表示されます。「いいえ」の場合、そのカテゴリー内で最も表示優先度の高いコンテンツが表示されます。');
define($constpref.'_SHOW_BREADCRUMBS','パンくずを表示する');
define($constpref.'_SHOW_PAGENAVI','ページナビゲーションを表示する');
define($constpref.'_SHOW_PRINTICON','印刷画面へのリンクを表示する');
define($constpref.'_SHOW_TELLAFRIEND','友達に紹介するリンクを表示する');
define($constpref.'_SEARCHBYUID','検索で「投稿者」という概念を有効にする');
define($constpref.'_SEARCHBYUIDDSC','ONにすると、検索やユーザプロフィール画面などで、「投稿」扱いで表示されます。純粋な静的コンテンツの場合はOFFにすることを勧めます。');
define($constpref.'_USE_TAFMODULE','tellafriendモジュールを利用する');
define($constpref.'_FILTERS','デフォルトフィルターセット');
define($constpref.'_FILTERSDSC','コンテンツ作成時に最初からチェックされているフィルター名を|で区切って入力します。ここに書かれた順番通り適用されます。');
define($constpref.'_FILTERSDEFAULT','xcode|smiley|nl2br');
define($constpref.'_FILTERSF','強制フィルター');
define($constpref.'_FILTERSFDSC','必ず通過するフィルター名を,で区切って入力します。フィルター名の後ろに:LASTをつけた場合はそのフィルターを最後に通過します。指定がなければ、最初に通過します。');
define($constpref.'_FILTERSP','禁止フィルターセット');
define($constpref.'_FILTERSPDSC','利用できないフィルター名を,で区切って入力します');
define($constpref.'_SUBMENU_SC','サブメニューにコンテンツも表示する');
define($constpref.'_SUBMENU_SCDSC','表示しない場合はカテゴリーのみが表示されます。表示する場合は、メニュー表示指定されたコンテンツもカテゴリーと同列に表示されます');
define($constpref.'_SITEMAP_SC','sitemapにコンテンツも同列表示する');
define($constpref.'_USE_VOTE','投票機能を利用する');
define($constpref.'_GUESTVOTE_IVL','ゲスト投票の時間制限');
define($constpref.'_GUESTVOTE_IVLDSC','同一のIPからは、この時間（秒数）内は投票することができません');
define($constpref.'_HTMLHEADER','コンテンツ共通HTMLヘッダ');
define($constpref.'_ALLOWEACHHEAD','コンテンツ毎のHTMLヘッダを許可する');
define($constpref.'_CSS_URI','モジュール用CSSのURI');
define($constpref.'_CSS_URIDSC','このモジュール専用のCSSファイルのURIを相対パスまたは絶対パスで指定します。デフォルトは{mod_url}/index.php?page=main_cssです。');
define($constpref.'_IMAGES_DIR','イメージファイルディレクトリ');
define($constpref.'_IMAGES_DIRDSC','このモジュール用のイメージが格納されたディレクトリをモジュールディレクトリからの相対パスで指定します。デフォルトはimagesです。');
define($constpref.'_BODY_EDITOR','本文編集エディタ');
define($constpref.'_HTMLPR_EXCEPT','HTMLPurifierによる強制書き換えをしないグループ');
define($constpref.'_HTMLPR_EXCEPTDSC','ここに指定されて「いない」グループによるHTML投稿は、Protector3.14以上に付属しているHTMLPurifierによって強制的に正しく無毒なHTMLに書き換えられます。ただし、HTMLPurifier自体、PHPバージョンが5以上でないと機能しません。');
define($constpref.'_HISTORY_P_C','履歴機能を何世代まで保存するか');
define($constpref.'_MLT_HISTORY','履歴の一世代として保存する最小時間(sec)');
define($constpref.'_BRCACHE','画像ファイルのブラウザキャッシュ (wrapsモード時のみ)');
define($constpref.'_BRCACHEDSC' , 'HTML以外のファイルをブラウザにキャッシュする時間を秒で指定（0で無効化）');
define($constpref.'_EF_CLASS' , 'extra_fields処理クラス名');
define($constpref.'_EF_CLASSDSC' , 'extra_fields処理をオーバーライドしたい時に指定。デフォルトはPicoExtraFields');
define($constpref.'_URIM_CLASS' , 'URIマッピング処理クラス名');
define($constpref.'_URIM_CLASSDSC' , 'URIマッパーをオーバーライドしたい時に指定。デフォルトはPicoUriMapper');
define($constpref.'_EFIMAGES_DIR' , 'extra_fields画像ファイルのパス');
define($constpref.'_EFIMAGES_DIRDSC' , 'XOOPS_ROOT_PATHからの相対パスを指定する。この機能を利用する場合には、指定されたフォルダを先に作っておき、さらに書込可能としておく必要がある。デフォルトは uploads/(モジュールdirname)');
define($constpref.'_EFIMAGES_SIZE' , 'extra_fields画像ファイルのサイズ(pixel)');
define($constpref.'_EFIMAGES_SIZEDSC' , '(メイン画像横幅)x(メイン画像高さ) (サムネイル横幅)x(サムネイル高さ) というフォーマットで記入。デフォルトは 480x480 150x150');
define($constpref.'_IMAGICK_PATH' , 'ImageMagick実行ファイルのパス');
define($constpref.'_IMAGICK_PATHDSC' , '空欄では動かない時のみ、実行ファイルのパスを指定する 例) /usr/X11R6/bin/');
define($constpref.'_COM_DIRNAME','コメント統合するd3forumのdirname');
define($constpref.'_COM_FORUM_ID','コメント統合するフォーラムの番号');
define($constpref.'_COM_ORDER','コメント統合の表示順序');
define($constpref.'_COM_VIEW','コメント統合の表示方法');
define($constpref.'_COM_POSTSNUM','コメント統合のフラット表示における最大表示件数');

// blocks
define($constpref.'_BNAME_MENU','メニュー');
define($constpref.'_BNAME_CONTENT','コンテンツ内容');
define($constpref.'_BNAME_LIST','コンテンツ一覧');
define($constpref.'_BNAME_SUBCATEGORIES','サブカテゴリー一覧');
define($constpref.'_BNAME_MYWAITINGS','自身の承認待ち');
define($constpref.'_BNAME_TAGS','タグ一覧');

// Notify Categories
define($constpref.'_NOTCAT_GLOBAL', '全カテゴリー共通');
define($constpref.'_NOTCAT_GLOBALDSC', '全カテゴリー共通の通知オプション');
define($constpref.'_NOTCAT_CATEGORY', 'カテゴリー内');
define($constpref.'_NOTCAT_CATEGORYDSC', 'このカテゴリーにおける通知オプション');
define($constpref.'_NOTCAT_CONTENT', 'コンテンツ');
define($constpref.'_NOTCAT_CONTENTDSC', 'このコンテンツにおける通知オプション');

// Each Notifications
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENT', '承認待ち');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTCAP', 'コンテンツの新規登録・変更などで、承認が必要な投稿があった場合に通知します（モデレータ以外には通知されません）');
define($constpref.'_NOTIFY_GLOBAL_WAITINGCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : 承認待ち');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENT', '新規コンテンツ');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTCAP', 'コンテンツの新規登録があった場合に通知します（未承認であれば通知しません）');
define($constpref.'_NOTIFY_GLOBAL_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE} : 新規コンテンツ {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENT', '新規コンテンツ');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTCAP', 'コンテンツの新規登録があった場合に通知します（未承認であれば通知しません）');
define($constpref.'_NOTIFY_CATEGORY_NEWCONTENTSBJ', '[{X_SITENAME}] {X_MODULE}:{CAT_TITLE} カテゴリ内新規コンテンツ {CONTENT_SUBJECT}');
define($constpref.'_NOTIFY_CONTENT_COMMENT', '新規コメント');
define($constpref.'_NOTIFY_CONTENT_COMMENTCAP', 'このコンテンツへのコメント登録があった場合に通知します（未承認であれば通知しません）');
define($constpref.'_NOTIFY_CONTENT_COMMENTSBJ', '[{X_SITENAME}] {X_MODULE} : コメントの投稿がありました');

}


?>