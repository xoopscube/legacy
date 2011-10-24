<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'd3pipes' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

// The name of this module
define($constpref."_NAME","D3 PIPES");

// A brief description of this module
define($constpref."_DESC","RSS等のシンジケーションを自由自在に扱うためのモジュール");

// admin menus
define($constpref.'_ADMENU_PIPE','パイプ管理') ;
define($constpref.'_ADMENU_CACHE','キャッシュ管理') ;
define($constpref.'_ADMENU_CLIPPING','切り抜き管理') ;
define($constpref.'_ADMENU_JOINT','ジョイント初期設定') ;
define($constpref.'_ADMENU_JOINTCLASS','ジョイントクラス初期設定') ;
define($constpref.'_ADMENU_MYLANGADMIN','言語定数管理') ;
define($constpref.'_ADMENU_MYTPLSADMIN','テンプレート管理') ;
define($constpref.'_ADMENU_MYBLOCKSADMIN','ブロック管理/アクセス権限') ;
define($constpref.'_ADMENU_MYPREFERENCES','一般設定') ;

// blocks
define($constpref.'_BNAME_ASYNC','非同期パイプ一覧ブロック') ;
define($constpref.'_BNAME_SYNC','同期パイプ一覧ブロック') ;

// configs
define($constpref.'_INDEXTOTAL','モジュールトップで表示する最新ヘッドラインの総数');
define($constpref.'_INDEXEACH','モジュールトップで表示する最新ヘッドラインに１パイプから引っ張ってくる最大数');
define($constpref.'_INDEXKEEPPIPE','モジュールトップでは可能な限り上位のパイプ名を表示する');
define($constpref.'_ENTRIESAPIPE','個々のパイプページで表示するエントリ数');
define($constpref.'_ENTRIESAPAGE','各パイプの切り抜き一覧表示１ページに表示するエントリ数');
define($constpref.'_ENTRIESARSS','各パイプのRSS/ATOMで出力するエントリ数');
define($constpref.'_ENTRIESSMAP','サイトマップXML出力での最大エントリ数');
define($constpref.'_ARCB_FETCHED','切り抜きを自動削除する日数（取得日ベース）');
define($constpref.'_ARCB_FETCHEDDSC','エントリを切り抜きとして保存した日から何日で削除するかを指定します。自動削除しない場合は0を指定します。また、コメントやハイライト属性がついたコメントは削除されません。あえて削除する場合は切り抜き管理から明示的に削除してください。');
define($constpref.'_INTERNALENC','内部エンコーディング');
define($constpref.'_FETCHCACHELT','外部取得キャッシュ期間 (秒)');
define($constpref.'_REDIRECTWARN','取得先URIのリダイレクトについて警告する');
define($constpref.'_SNP_MAXREDIRS','取得先URIの最大リダイレクト回数');
define($constpref.'_SNP_MAXREDIRSDSC','意図しないリダイレクトを避けるためにも、ある程度運用が安定してきたら、ここを0にすることをお勧めします');
define($constpref.'_SNP_PROXYHOST','外部取得に経由するProxyのホスト名');
define($constpref.'_SNP_PROXYHOSTDSC','FQDNで指定。Proxyを利用しない場合は空欄にしてください');
define($constpref.'_SNP_PROXYPORT','外部取得に経由するProxyのポート番号');
define($constpref.'_SNP_PROXYUSER','外部取得に経由するProxyのユーザ名');
define($constpref.'_SNP_PROXYPASS','外部取得に経由するProxyのパスワード');
define($constpref.'_SNP_CURLPATH','curlのパス (デフォルトは/usr/bin/curl)');
define($constpref.'_TIDY_PATH','tidyのパス (デフォルトは/usr/bin/tidy)');
define($constpref.'_XSLTPROC_PATH','xsltprocのパス (デフォルトは/usr/bin/xsltproc)');
define($constpref.'_UPING_SERVERS','更新Pingサーバ');
define($constpref.'_UPING_SERVERSDSC','httpから始まるRPCエンドポイントを１行に１つずつ記述します<br />URLの最後に、スペースで区切ったEを入れた場合、extendedPingで送信します。');
define($constpref.'_UPING_SERVERSDEF',"http://blogsearch.google.co.jp/ping/RPC2 E\nhttp://api.my.yahoo.co.jp/RPC2\nhttp://rpc.technorati.com/rpc/ping\nhttp://ping.bloggers.jp/rpc/\nhttp://www.blogpeople.net/servlet/weblogUpdates E");
define($constpref.'_CSS_URI','モジュール用CSSのURI');
define($constpref.'_CSS_URIDSC','このモジュール専用のCSSファイルのURIを相対パスまたは絶対パスで指定します。デフォルトは{mod_url}/index.php?page=main_cssです。');
define($constpref.'_IMAGES_DIR','イメージファイルディレクトリ');
define($constpref.'_IMAGES_DIRDSC','このモジュール用のイメージが格納されたディレクトリをモジュールディレクトリからの相対パスで指定します。デフォルトはimagesです。');
define($constpref.'_COM_DIRNAME','コメント統合するd3forumのdirname');
define($constpref.'_COM_FORUM_ID','コメント統合するフォーラムの番号');
define($constpref.'_COM_VIEW','コメント統合の表示方法');
define($constpref.'_COM_ORDER','コメント統合の表示順序');
define($constpref.'_COM_POSTSNUM','コメント統合のフラット表示における最大表示件数');

}


?>