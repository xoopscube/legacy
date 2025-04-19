<?php

// Altsys admin menu and breadcrumbs
define( '_MD_A_MYMENU_MYTPLSADMIN' , '');
define( '_MD_A_MYMENU_MYBLOCKSADMIN' , 'アクセス権限');
define( '_MD_A_MYMENU_MYPREFERENCES' , '一般設定');

// Headings
define( '_AM_TH_DATETIME' , '日時');
define( '_AM_TH_USER' , 'ユーザ');
define( '_AM_TH_IP' , 'IP');
define( '_AM_TH_IP_BAN' , 'Banned IPs');
define( '_AM_TH_AGENT' , 'AGENT');
define( '_AM_TH_TYPE' , '種別');
define( '_AM_TH_DESC' , '詳細');
define( '_AM_TH_INFO' , '概要');
define( '_AM_TH_TIPS' , 'チップ');
define( '_AM_TH_URI' , 'URI');
define( '_AM_PAGE_PREV' , 'Previous');
define( '_AM_PAGE_NEXT' , 'Next');
define( '_AM_CLEARLOG' , 'Clear log');

define( '_AM_TH_BADIPS' , '拒否IPリスト<br><br><span style="font-weight:normal;">１行１IPアドレスで記述してください（前方一致）。空欄なら全許可。<br>IPv6 アドレスの省略表記 "::" 及び "0" の省略は使用できません。</span>');
define( '_AM_TH_GROUP1IPS' , '管理者グループ(1)の許可IP<br><br><span style="font-weight:normal;">１行１IPアドレスで記述してください（前方一致）。<br>IPv6 アドレスの省略表記 "::" 及び "0" の省略は使用できません。<br>192.168. とすれば、192.168.*からのみ管理者になれます。空欄なら全許可。</span>');

define( '_AM_LABEL_COMPACTLOG' , 'ログをコンパクト化する');
define( '_AM_BUTTON_COMPACTLOG' , 'コンパクト化実行');
define( '_AM_JS_COMPACTLOGCONFIRM' , 'IPと種別の重複したレコードを削除します');
define( '_AM_LABEL_REMOVEALL' , '全レコードを削除する:');
define( '_AM_BUTTON_REMOVEALL' , '全削除実行');
define( '_AM_JS_REMOVEALLCONFIRM' , 'ログを無条件で削除します。本当によろしいですか？');
define( '_AM_LABEL_REMOVE' , 'チェックしたレコードを削除する:');
define( '_AM_BUTTON_REMOVE' , '削除実行');
define( '_AM_JS_REMOVECONFIRM' , '本当に削除してよろしいですか？');
define( '_AM_MSG_IPFILESUPDATED' , 'IPリストファイルを書き換えました');
define( '_AM_MSG_BADIPSCANTOPEN' , '拒否IPリストファイルが開けません');
define( '_AM_MSG_GROUP1IPSCANTOPEN' , '管理者用IPリストファイルが開けません');
define( '_AM_MSG_REMOVED' , '削除しました');
define( '_AM_FMT_CONFIGSNOTWRITABLE' , 'configsディレクトリが書込許可されていません: %s');

// prefix_manager.php
define( '_AM_H3_PREFIXMAN' , 'PREFIX マネージャ');
define( '_AM_MSG_DBUPDATED' , 'データベースが更新されました');
define( '_AM_CONFIRM_DELETE' , '全テーブルが削除されますがよろしいですか?');
define( '_AM_TXT_HOWTOCHANGEDB' , "prefixを変更する場合は、%s/mainfile.php 内の以下の部分を書き換えてください<br><br>define('XOOPS_DB_PREFIX', '<b>%s</b>');");


// advisory.php
define( '_AM_ADV_NOTSECURE' , '非推奨');
define( '_AM_ADV_TITLE' , 'プロテクターセキュリティアドバイザー');
define( '_AM_ADV_TITLE_TIP' , 'プロテクターセキュリティアドバイザー、一連のセキュリティチェックを実行し、潜在的なセキュリティリスクを検出できます。<br>
    さらに、検出されたセキュリティリスクの推奨事項と修正方法を入手できます。');
define( '_AM_ADV_NGINX' , 'NginXはApacheのようなphpプロセスを管理しないため、php-fpmまたはphp-cgiのいずれかを構成する必要がある場合があることに注意してください。');
define( '_AM_ADV_NGINX_VAR' , 'Sサーバーソフトウェアvar_dump');
define( '_AM_ADV_SERVER' , 'サーバーソフトウェア');
define( '_AM_ADV_ENV' , 'このテーブルのエントリは、Webサーバーによって作成されます。 すべてのWebサーバーがこれらのいずれかを提供するという保証はありません。
     サーバーは一部を省略したり、ここに記載されていない他のサーバーを提供したりする場合があります。これらの変数は»CGI/1.1仕様で考慮されているため、これらを期待できるはずです。');
define( '_AM_ADV_ENV_LABEL' , 'サーバーと実行環境の情報');
define( '_AM_ADV_APACHE' , 'Apache関数は、PHPをApacheモジュールとして実行している場合にのみ使用できます。<br>
    さらに、一部のWebサーバー構成では、次の値が返されない場合があります。');

// Mainfile
define( '_AM_ADV_MAINUNPATCHED' , '事前チェックと事後チェックの両方が必要です。 mainfile.phpを編集するには、モジュールのドキュメントを参照してください');
define( '_AM_ADV_MAIN_PRECHECK' , '必要な事前チェックがありません。!');
define( '_AM_ADV_MAIN_POSTCHECK' , '必要なポストチェックがありません!');

// TRUST PATH
define( '_AM_ADV_TRUSTPATH_PUBLIC_LINK' , 'Click here !');
define( '_AM_ADV_TRUSTPATH_PUBLIC' , '上にNGという画像が表示されていたり、リンク先でエラーが出ないようならXOOPS_TRUST_PATHの設置方法に問題があります。TRUST_PATH内のPHPファイルに直アクセスできないことの確認（リンク先が404,403,500エラーなら正常）');
define( '_AM_ADV_TRUSTPATH_DESC' , 'XOOPS_TRUST_PATHはDocumentRoot外に設置するのが基本ですが。');
define( '_AM_ADV_TRUSTPATH_TIPS' , 'そうできない場合でもXOOPS_TRUST_PATH直下にDENY FROM ALLの一行を持つ.htaccessを追加するなどして、XOOPS_TRUST_PATH内に直接アクセスできないようにする必要があります。');

// allow_url_fopen
define( '_AM_ADV_FOPEN' , 'Allow url fopen');
define( '_AM_ADV_FOPEN_ON' , 'この設定だと、外部の任意のスクリプトを実行される危険性があります');
define( '_AM_ADV_FOPEN_DESC' , '<p>この設定変更にはサーバの管理者権限が必要です<br>ご自身で管理しているサーバであれば、php.iniやhttpd.confを編集して下さい<br>そうでない場合は、サーバ管理者にお願いしてみて下さい.</p>');
define( '_AM_ADV_FOPEN_TIPS' , '<p><b>.htaccess</b>または<b>php.ini</b><br>から<b>allow_url_fopen</b>を無効にできます
     mod_rewriteモジュールがApacheで有効になっている場合は、次の行をパブリックルートフォルダーの.htaccessファイルに挿入できます。<br>
     <b>php_flag allow_url_fopen off</b><br>
     または、「php.ini」でこのphp機能を無効にします。<br>
     <b>allow_url_fopen , "off"</b></p>');

// session.use_trans_sid
define( '_AM_ADV_SESSION_ERROR' , '');
define( '_AM_ADV_SESSION_ON' , '<b> session.use_trans_sid</b>をオフにすることをお勧めします<br>
    それ以外の場合、PHPはURLを介してセッションIDを渡します。');
define( '_AM_ADV_SESSION_DESC' , 'use_trans_sidが有効になっている場合、PHPはURLを介してセッションIDを渡します。 これにより、アプリケーションはセッションハイジャック攻撃に対してより脆弱になります。
     セッションハイジャックは基本的に、ハッカーがセッションIDを盗むことによって正当なユーザーになりすますID盗難の一形態です。
     セッショントークンがCookieで送信され、SSLを介して要求が行われる場合、トークンは安全です。');
define( '_AM_ADV_SESSION_TIPS' , '<b> .htaccess</b>または<b>php.ini</b>から<b>session.use_trans_sid</b>を無効にできます<br>
    mod_rewriteモジュールがApacheで有効になっている場合は、この行をパブリックルートフォルダーの.htaccessファイルに挿入できます。<br>
    <b>php_flag session.use_trans_sid off</b><br>
    または、「php.ini」でこのphp機能を無効にします:<br>
    <b>session.use_trans_sid , "off"</b>');

// Database
define( '_AM_ADV_DBPREFIX_ON' , "<b>データベースプレフィックス</b>を変更することをお勧めします !");
define( '_AM_ADV_DBPREFIX_DESC' , "この設定は、<b>SQLインジェクション攻撃</b>のセキュリティリスクです。<br>
    入力のサニタイズと検証により、最も一般的なWebサイト攻撃の一部を防ぐことができます。<br>
    モジュールの設定でSQL<b>サニタイズ</b>オプションを有効にします。");
define( '_AM_ADV_DBPREFIX_TIPS' , 'Prefix Managerを使用して、データベースプレフィックスを管理、保存、および変更できます。<br> <a class="button" href="index.php?page=prefix_manager">Prefix manager</a>');

// Database factory
define( '_AM_ADV_DBFACTORYPATCHED' , 'データベースファクトリはDBLayerTrappingAnti-SQL-Injectionの準備ができています');
define( '_AM_ADV_DBFACTORYUNPATCHED' , 'あなたのデータベースファクトリーは準備ができていません！');
define( '_AM_ADV_DBFACTORY_ON' , 'データベースファクトリは、DBLayerTrappingアンチSQLインジェクションの準備ができていません。 <br>パッチまたはアップデートが必要です！');
define( '_AM_ADV_DBFACTORY_DESC' , 'SQLインジェクション（SQLi）とは、攻撃者がWebアプリデータベースサーバーを制御する悪意のあるSQLステートメントを実行する可能性のあるインジェクション攻撃を指します。
      プロテクターは、ユーザー入力を含むSQLクエリを処理するときに、パラメーター化されたクエリを保証します。');
define( '_AM_ADV_DBFACTORY_TIPS' , 'パラメータ化されたクエリにより、データベースはSQLクエリのどの部分をユーザー入力と見なす必要があるかを理解できるため、SQLインジェクションを解決できます。
      この機能を有効にするには、更新が必要です。 または、ファイルにパッチを適用します<b> class / database / databasefactory.php </b>');

// Test Protector
define( '_AM_ADV_SUBTITLECHECK' , 'Protectorの動作チェック');
define( '_AM_ADV_CHECKCONTAMI' , '変数汚染');
define( '_AM_ADV_CHECKISOCOM' , '孤立コメント');

// Admin constants v4.0
define('_AM_PROTECTOR_EXPORT', 'Export Data');
define('_AM_PROTECTOR_DOWNLOAD', 'Download');
define('_AM_PROTECTOR_DOWNLOAD_TXT', 'Download as Text');
define('_AM_PROTECTOR_DOWNLOAD_CSV', 'Download as CSV');
define('_AM_PROTECTOR_DASHBOARD', 'Dashboard');
define('_AM_PROTECTOR_INFORMATION', 'Module Information');
define('_AM_PROTECTOR_VERSION', 'Version');
define('_AM_PROTECTOR_STATUS', 'Status');
define('_AM_PROTECTOR_ENABLED', 'Enabled');
define('_AM_PROTECTOR_DISABLED', 'Disabled');
define('_AM_PROTECTOR_QUICKLINKS', 'Quick Links');
define('_AM_PROTECTOR_IPSAFELIST', 'IP Safe List');
define('_AM_PROTECTOR_IPSAFELISTDESC', 'IPs listed here will be exempt from Protector\'s security checks');
define('_AM_PROTECTOR_IPSAFELISTFORMAT', 'Each IP should be on a separate line. You can use CIDR notation (e.g. 192.168.1.0/24)');
define('_AM_PROTECTOR_UPDATE', 'Update');
define('_AM_PROTECTOR_UPDATED', 'Settings have been updated');
define('_AM_PROTECTOR_IMPORT', 'Import Data');
define('_AM_PROTECTOR_UPLOAD', 'Upload');
define('_AM_PROTECTOR_EXPORT_TIPS', 'Export your data to a TXT or CSV file so that you can restore them later.');
define('_AM_PROTECTOR_IMPORT_TIPS', 'Upload a previously exported TXT or CSV file to restore your settings.');
define('_AM_PROTECTOR_IMPORT_ERROR', 'Error uploading file. Please try again.');
define('_AM_PROTECTOR_IMPORT_SUCCESS', 'Logs imported successfully.');
