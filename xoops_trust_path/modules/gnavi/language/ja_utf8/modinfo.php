<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'gnavi' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

define( $constpref.'_LOADED' , 1 ) ;

define($constpref."_NAME","gnavi");

// A brief description of this module
define($constpref."_DESC","GoogleMapを用いたエリアガイド作成モジュール");

// Names of blocks for this module (Not all module has blocks)
define( $constpref."_BNAME_RECENT","最近の画像");
define( $constpref."_BNAME_HITS","人気画像");
define( $constpref."_BNAME_RANDOM","ピックアップ画像");
define( $constpref."_BNAME_RECENT_P","最近の画像(画像付)");
define( $constpref."_BNAME_HITS_P","人気画像(画像付)");
define( $constpref."_BNAME_MENU","メニュー");
define( $constpref."_BNAME_ARCHIVE","アーカイブ");

// Config Items
define( $constpref."_CFG_PHOTOSPATH" , "画像ファイルの保存先ディレクトリ" ) ;
define( $constpref."_CFG_DESCPHOTOSPATH" , "XOOPSインストール先からのパスを指定（最初の'/'は必要、最後の'/'は不要）<br />Unixではこのディレクトリへの書込属性をONにして下さい" ) ;
define( $constpref."_CFG_THUMBSPATH" , "サムネイルファイルの保存先ディレクトリ" ) ;
define( $constpref."_CFG_DESCTHUMBSPATH" , "「画像ファイルの保存先ディレクトリ」と同じです" ) ;
define( $constpref."_CFG_IMAGINGPIPE" , "画像処理を行わせるパッケージ選択" ) ;
define( $constpref."_CFG_DESCIMAGINGPIPE" , "ほとんどのPHP環境で標準的に利用可能なのはGDですが機能的に劣ります<br />可能であればImageMagickかNetPBMの使用をお勧めします" ) ;
define( $constpref."_CFG_FORCEGD2" , "強制GD2モード" ) ;
define( $constpref."_CFG_DESCFORCEGD2" , "強制的にGD2モードで動作させます<br />一部のPHPでは強制GD2モードでサムネイル作成に失敗します<br />画像処理パッケージとしてGDを選択した時のみ意味を持ちます" ) ;
define( $constpref."_CFG_IMAGICKPATH" , "ImageMagickの実行パス" ) ;
define( $constpref."_CFG_DESCIMAGICKPATH" , "convertの存在するディレクトリをフルパスで指定しますが、空白でうまく行くことが多いでしょう。<br />画像処理パッケージとしてImageMagickを選択した時のみ意味を持ちます" ) ;
define( $constpref."_CFG_NETPBMPATH" , "NetPBMの実行パス" ) ;
define( $constpref."_CFG_DESCNETPBMPATH" , "pnmscale等の存在するディレクトリをフルパスで指定しますが、空白でうまく行くことが多いでしょう。<br />画像処理パッケージとしてNetPBMを選択した時のみ意味を持ちます" ) ;
define( $constpref."_CFG_POPULAR" , "'POP'アイコンがつくために必要なヒット数" ) ;
define( $constpref."_CFG_NEWDAYS" , "'new'や'update'アイコンが表示される日数" ) ;
define( $constpref."_CFG_NEWPHOTOS" , "トップページで新規画像として表示する数" ) ;
define( $constpref."_CFG_DEFAULTORDER" , "カテゴリ表示でのデフォルト表示順" ) ;
define( $constpref."_CFG_PERPAGE" , "1ページに表示される画像数" ) ;
define( $constpref."_CFG_DESCPERPAGE" , "選択可能な数字を | で区切って下さい<br />例: 10|20|50|100" ) ;
define( $constpref."_CFG_ALLOWNOIMAGE" , "画像のない投稿を許可する" ) ;
define( $constpref."_CFG_MAKETHUMB" , "サムネイルを作成する" ) ;
define( $constpref."_CFG_DESCMAKETHUMB" , "「生成しない」から「生成する」に変更した時には、「サムネイルの再構築」が必要です。" ) ;
define( $constpref."_CFG_THUMBSIZE" , "サムネイル画像サイズ(pixel)" ) ;
define( $constpref."_CFG_THUMBRULE" , "サムネイル生成法則" ) ;
define( $constpref."_CFG_WIDTH" , "最大画像幅" ) ;
define( $constpref."_CFG_DESCWIDTH" , "画像アップロード時に自動調整されるメイン画像の最大幅。<br />GDモードでTrueColorを扱えない時には単なるサイズ制限" ) ;
define( $constpref."_CFG_HEIGHT" , "最大画像高" ) ;
define( $constpref."_CFG_DESCHEIGHT" , "最大幅と同じ意味です" ) ;
define( $constpref."_CFG_FSIZE" , "最大ファイルサイズ" ) ;
define( $constpref."_CFG_DESCFSIZE" , "アップロード時のファイルサイズ制限(byte)" ) ;
define( $constpref."_CFG_MIDDLEPIXEL" , "シングルビューでの最大画像サイズ" ) ;
define( $constpref."_CFG_DESCMIDDLEPIXEL" , "幅x高さ で指定します。<br />（例 480x480）" ) ;
define( $constpref."_CFG_LIQUIDIMG" , "複数画像表示時の縮小表示" ) ;
define( $constpref."_CFG_DESCLIQUIDIMG" , "指定画像が２，３枚の時に上記シングルビューでの最大画像サイズに合わせ、それぞれの画像を縮小表示します。" ) ;
define( $constpref."_CFG_ADDPOSTS" , "投稿した時にカウントアップされる投稿数" ) ;
define( $constpref."_CFG_DESCADDPOSTS" , "常識的には0か1です。負の値は0と見なされます" ) ;
define( $constpref."_CFG_CATONSUBMENU" , "サブメニューへのトップカテゴリーの登録" ) ;
define( $constpref."_CFG_NAMEORUNAME" , "投稿者名の表示" ) ;
define( $constpref."_CFG_DESCNAMEORUNAME" , "ログイン名かハンドル名か選択して下さい" ) ;
define( $constpref."_CFG_INDEXPAGE" , "モジュールのトップページ" ) ;
define( $constpref."_CFG_VIEWCATTYPE" , "一覧表示の表示タイプ" ) ;
define( $constpref."_CFG_COLSOFTABLEVIEW" , "テーブル表示時のカラム数" ) ;

define( $constpref."_CFG_SHOWPARENT" , "親カテゴリにも記事を表示する" ) ;
define( $constpref."_CFG_DESCSHOWPARENT" , "カテゴリビューの時にサブカテゴリの記事も表示する場合は有効にして下さい。" ) ;

define( $constpref."_CFG_ALLOWEDEXTS" , "アップロード許可するファイル拡張子" ) ;
define( $constpref."_CFG_DESCALLOWEDEXTS" , "ファイルの拡張子を、jpg|jpeg|gif|png のように、'|' で区切って入力して下さい。<br />すべて小文字で指定し、ピリオドや空白は入れないで下さい。<br />意味の判っている方以外は、phpやphtmlなどを追加しないで下さい" ) ;
define( $constpref."_CFG_ALLOWEDMIME" , "アップロード許可するMIMEタイプ" ) ;
define( $constpref."_CFG_DESCALLOWEDMIME" , "MIMEタイプを、image/gif|image/jpeg|image/png のように、'|' で区切って入力して下さい。<br />MIMEタイプによるチェックを行わない時には、ここを空欄にします" ) ;

define( $constpref."_CFG_BODY_EDITOR" , "本文編集エディタ" ) ;
define( $constpref."_CFG_DESCBODY_EDITOR" , "管理権限で使用できます。別途 html/commonフォルダ内にエディタのアップロードが必要です。" ) ;
define( $constpref."_CFG_ADDINFO" , "記事毎の項目追加機能を有効に" ) ;
define( $constpref."_CFG_DESCADDINFO" , "記事に項目を追加できます。（例えば、「料金：5,000円」,「定休日：土・日・祝日」等の情報）" ) ;


define( $constpref."_CFG_USEVOTE" , "投票機能を利用する" ) ;
define( $constpref."_CFG_DESCUSEVOTE" , "各記事にユーザーが評価をつけることができます。評価順でのソート機能が有効になります。" ) ;
define( $constpref."_CFG_USEGMAP" , "GoogleMap機能を利用する" ) ;
define( $constpref."_CFG_DESCGMAP" , "コンテンツにマップ管理機能を追加します。各ページに位置情報を追加できます。" ) ;
define( $constpref."_CFG_GMAPKEY" , "GoogleMapAPI Key" ) ;
define( $constpref."_CFG_DESCGMAPKEY" , "GoogleMapを使用する際にはGoogleMapAPI Keyが必要になります。下記URLからkeyを取得してください。<br /><a href='http://www.google.com/apis/maps/signup.html'>http://www.google.com/apis/maps/signup.html</a>" ) ;
define( $constpref."_CFG_DEFLAT" , "GoogleMapの初期表示：緯度" ) ;
define( $constpref."_CFG_DESCDEFLAT" , "" ) ;
define( $constpref."_CFG_DEFLNG" , "GoogleMapの初期表示：経度" ) ;
define( $constpref."_CFG_DESCDEFLNG" , "" ) ;
define( $constpref."_CFG_DEFZOOM" , "GoogleMapの初期表示：ズーム" ) ;
define( $constpref."_CFG_DESCDEFZOOM" , "" ) ;
define( $constpref."_CFG_DEFMTYPE" , "GoogleMapの初期表示：地図の種類" ) ;
define( $constpref."_CFG_DESCDEFMTYPE" , "衛星写真や地形図を選択できます。さらに火星や月、星空のマップも選択できます。" ) ;
define( $constpref."_ICON_BYLID" , "記事毎にアイコンを指定できる。（通常はカテゴリ毎）" ) ;
define( $constpref."_CFG_USE_RSS" , "記事に外部取得したRSSフィードを表示する" ) ;
define( $constpref."_CFG_DESC_USE_RSS" , "表示するフィードの数を入力して下さい<br />この機能を追加すると入力ページにRSSリンクを入力するテキストボックスが表示され、記事ページ内には概要が表示されます。(Powerd By <a href='http://code.google.com/intl/ja/apis/ajaxfeeds/'>GoogleAjaxFeedAPI</a>)" ) ;
define( $constpref."_CFG_PE_APPKEY" , "PlaceEngineAPIを使用する" ) ;
define( $constpref."_CFG_DESC_PE_APPKEY" , "PlaceEngineは、Wifiで現在地を推定するサービスです。この機能を有効にするには下記アドレスでアプリケーションキーを取得して右に入力して下さい。<br /><a href='http://www.placeengine.com/appk' target='_blank'>http://www.placeengine.com/appk</a><br />※URLの項目にはモジュールのアドレスまで記入して下さい<br />(例:http://xoops.iko-ze.net/modules/gnavi)<br />(Powerd By <a href='http://www.koozyt.com/'>Koozyt</a>)" ) ;

define( $constpref."_CFG_MOBILEMAPSIZE" , "携帯端末で表示するGoogleMapサイズ（widthxHeight）" ) ;
define( $constpref."_CFG_DESCMOBILEMAPSIZE" , "240x180 のように入力して下さい。未入力の場合携帯用Mapを作成しません。" ) ;
define( $constpref."_CFG_MOBILEAGENT" , "携帯端末判別用文字列（正規表現）" ) ;
define( $constpref."_CFG_DESCMOBILEAGENT" , "エージェント情報から携帯端末を判別するための正規表現を記入して下さい。<BR>この機能は試験的な実装です。GETパラメータに「agent=mobile」を指定するとブラウザで携帯の画面を表示させることができます（デバグ用）。" ) ;
define( $constpref."_CFG_MOBILEENCORDING" , "携帯ページの文字エンコード" ) ;
define( $constpref."_CFG_DESCMOBILEENCORDING" , "携帯に出力するエンコードを指定して下さい。日本の場合は推奨 <B>SJIS</B> です。" ) ;
define( $constpref."_CFG_MOBILEUSEQRC" , "QRコードを使用する（サイズを指定）" ) ;
define( $constpref."_CFG_DESCMOBILEUSEQRC" , "１以上の値を入力すると記事に携帯で読み取るためのQRコードが作成されます。0で無効となり、入力値はQRコードのサイズとなります。（推奨値は <B>3</B> 又は <B>4</B> です。）<br />QRコードは「画像ファイルの保存先ディレクトリ」で指定したパス以下の「qr」というディレクトリに保存されます。QRコードの作成は記事の初期表示１回だけ行われます。そのため、ここでサイズを変更した場合は「qr」ディレクトリを削除して適用してください。" ) ;


define( $constpref.'_COM_DIRNAME','コメント統合するd3forumのdirname');
define( $constpref.'_COM_FORUM_ID','コメント統合するフォーラムの番号');
define( $constpref.'_COM_VIEW','コメント統合の表示方法');

define( $constpref.'_MAP_DRAW','マーカーをGeoXMLで描画');
define( $constpref.'_DESC_MAP_DRAW','（推奨：いいえ）地図表示をKMLで描画させます。処理が重い場合などに試してみてください。多少違った動きになります。');
define( $constpref.'_INCLUDE_KML','外部 KML ファイルの表示');
define( $constpref.'_DESC_INCLUDE_KML','GoogleEarthで表示可能なKMLファイル(.kml,.kmz)を指定できます。この情報は常に表示されます。<br />１行につき１件ずつ、"http://"から始まるURLで入力してください。<br />入力例）<br />http://xoops.iko-ze.net/modules/gnavi/kml.php');




define( $constpref."_OPT_USENAME" , "ハンドル名" ) ;
define( $constpref."_OPT_USEUNAME" , "ログイン名" ) ;

define( $constpref."_OPT_CALCFROMWIDTH" , "指定数値を幅として、高さを自動計算" ) ;
define( $constpref."_OPT_CALCFROMHEIGHT" , "指定数値を高さとして、幅を自動計算" ) ;
define( $constpref."_OPT_CALCWHINSIDEBOX" , "幅か高さの大きい方が指定数値になるよう自動計算" ) ;

define( $constpref."_OPT_VIEWLIST" , "説明文付リスト表示" ) ;
define( $constpref."_OPT_VIEWTABLE" , "テーブル表示" ) ;


// Sub menu titles
define( $constpref."_TEXT_SMNAME1","投稿");
define( $constpref."_TEXT_SMNAME2","高人気");
define( $constpref."_TEXT_SMNAME3","トップランク");
define( $constpref."_TEXT_SMNAME4","自分の投稿");
define( $constpref."_TEXT_SMNAME5","地図を表示");
define( $constpref."_TEXT_SMNAME6","記事の一覧");

// Names of admin menu items
define( $constpref."_ADMENU_MYCATEGOLY","カテゴリ管理");
define( $constpref."_ADMENU_MYICON","アイコン管理");
define( $constpref."_ADMENU_MYPHOTOMANAGER","画像管理");
define( $constpref."_ADMENU_MYLADMISSION","投稿された画像の承認");
define( $constpref."_ADMENU_MYGROUPPERM","各グループの権限");
define( $constpref."_ADMENU_MYCHECKCONFIGS","動作チェッカー");
define( $constpref."_ADMENU_MYBATCH","画像一括登録");
define( $constpref."_ADMENU_MYREDOTHUMBS","サムネイルの再構築");

define( $constpref.'_ADMENU_MYLANGADMIN' , '言語定数管理' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' , 'テンプレート管理' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'ブロック管理/モジュールアクセス権限' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , '一般設定' ) ;

// Text for notifications
define( $constpref.'_GLOBAL', 'モジュール全体');
define( $constpref.'_GLOBALDSC', 'モジュール全体における通知オプション');
define( $constpref.'_CATEGORY', 'カテゴリー');
define( $constpref.'_CATEGORYDSC', '選択中のカテゴリーに対する通知オプション');
define( $constpref.'_ITEM', '記事');
define( $constpref.'_ITEMDSC', '表示中の記事に対する通知オプション');

define( $constpref.'_NOTIFY_GLOBAL_NEWITEM', '新規投稿');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMCAP', '新規に投稿された時に通知する');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMCONTENTCAP', '新規に投稿された時に通知する');
define( $constpref.'_NOTIFY_GLOBAL_NEWITEMBJ', '[{X_SITENAME}] {X_MODULE}: 新たに投稿されました');

define( $constpref.'_NOTIFY_CATEGORY_NEWITEM', 'カテゴリ毎の新投稿');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMCAP', 'このカテゴリに新たに投稿された時に通知する');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMCONTENTCAP', 'このカテゴリに新たに投稿された時に通知する');
define( $constpref.'_NOTIFY_CATEGORY_NEWITEMBJ', '[{X_SITENAME}] {X_MODULE}: 新たに投稿されました');

}

?>