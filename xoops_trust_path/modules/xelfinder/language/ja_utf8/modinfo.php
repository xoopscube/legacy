<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}

$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

define( $constpref.'_DESC' , 'Webベースのファイルマネージャ elFinder をイメージマネージャとして利用するモジュール');

// admin menu
define( $constpref.'_ADMENU_INDEX_CHECK' ,   '設定の確認' ) ;
define( $constpref.'_ADMENU_GOTO_MODULE' ,   'モジュール画面' ) ;
define( $constpref.'_ADMENU_GOTO_MANAGER' ,  'ファイルマネージャ' ) ;
define( $constpref.'_ADMENU_DROPBOX' ,       'Dropbox App Token 取得' ) ;
define( $constpref.'_ADMENU_GOOGLEDRIVE' ,   'GoogleDrive Token 取得' ) ;
define( $constpref.'_ADMENU_VENDORUPDATE' ,  'vendor アップデート' ) ;
define( $constpref.'_ADMENU_MYLANGADMIN' ,   '言語定数管理' ) ;
define( $constpref.'_ADMENU_MYTPLSADMIN' ,   'テンプレート管理' ) ;
define( $constpref.'_ADMENU_MYBLOCKSADMIN' , 'ブロック管理/アクセス権限' ) ;
define( $constpref.'_ADMENU_MYPREFERENCES' , '一般設定' ) ;

// configurations
define( $constpref.'_MANAGER_TITLE' ,           'マネージャのページタイトル' );
define( $constpref.'_MANAGER_TITLE_DESC' ,      '' );
define( $constpref.'_VOLUME_SETTING' ,          'ボリュームドライバ' );
define( $constpref.'_VOLUME_SETTING_DESC' ,     '<button class="help-admin button" type="button" data-module="xelfinder" data-help-article="#help-volume" aria-label="Help Volume"><b>?</b></button> 設定オプションは改行で区切られる。<br><pre>[モジュールディレクトリ名]:[プラグイン名]:[ファイル格納ディレクトリ]:[表示名]:[オプション]</pre>' );
define( $constpref.'_SHARE_FOLDER' ,            '共有フォルダ' );
define( $constpref.'_DISABLED_CMDS_BY_GID' ,    'グループ毎無効コマンド' );
define( $constpref.'_DISABLED_CMDS_BY_GID_DESC','グループ毎(管理者を除く)に無効にするコマンドを [グループID]=[無効コマンド(カンマ区切り)] として ":" で区切って指定する。<br>コマンド名: archive, chmod, cut, duplicate, edit, empty, extract, mkdir, mkfile, paste, perm, put, rename, resize, rm, upload など' );
define( $constpref.'_DISABLE_WRITES_GUEST' ,    'ゲスト書き込み無効' );
define( $constpref.'_DISABLE_WRITES_GUEST_DESC','ゲスト向けにグループ毎無効コマンドに指定した無効コマンドに合わせ、書き込み系コマンドをすべて追加します。' );
define( $constpref.'_DISABLE_WRITES_USER' ,     '登録ユーザー書き込み無効' );
define( $constpref.'_DISABLE_WRITES_USER_DESC', '登録ユーザー向けにグループ毎無効コマンドに指定した無効コマンドに合わせ、書き込み系コマンドをすべて追加します。' );
define( $constpref.'_MAIL_NOTIFY_GUEST' ,       'メール通知(ゲスト)' );
define( $constpref.'_MAIL_NOTIFY_GUEST_DESC',   'ゲストによるファイル追加を管理グループメンバーにメール通知します。' );
define( $constpref.'_ENABLE_IMAGICK_PS' ,       'ImageMagickのPostScript処理有効' );
define( $constpref.'_ENABLE_IMAGICK_PS_DESC',   '<a href="https://www.kb.cert.org/vuls/id/332928" target="_blank">Ghostscriptの脆弱性</a>が修正されている場合は、「はい」を選択することでImageMagickでPostScript関連の処理を有効にできます。' );
define( $constpref.'_USE_SHARECAD_PREVIEW' ,    'ShareCAD プレビュー有効' );
define( $constpref.'_USE_SHARECAD_PREVIEW_DESC','ShareCAD.org を利用しプレビュー可能なファイルタイプを拡大します。ShareCAD プレビュー利用時は ShareCAD.org へコンテンツ URL を通知します。' );
define( $constpref.'_USE_GOOGLE_PREVIEW' ,      'Google Docs プレビュー有効' );
define( $constpref.'_USE_GOOGLE_PREVIEW_DESC',  'Google Docs を利用しプレビュー可能なファイルタイプを拡大します。Google Docs プレビュー利用時に Google Docs へコンテンツ URL を通知します。' );
define( $constpref.'_USE_OFFICE_PREVIEW' ,      'Office Online プレビュー有効' );
define( $constpref.'_USE_OFFICE_PREVIEW_DESC',  '注：Microsoftは、組み込みのテレメトリクライアントを介して使用データを収集するだけでなく、ConnectedServicesの個々の使用を記録および保存します。 コンテンツのURLはproducts.office.comによって収集されます' );
define( $constpref.'_MAIL_NOTIFY_GROUP' ,       'メール通知(グループ)' );
define( $constpref.'_MAIL_NOTIFY_GROUP_DESC',   '選択したグループに属するユーザーによるファイル追加を管理グループメンバーにメール通知します。' );
define( $constpref.'_FTP_NAME' ,                'FTP ネットボリューム表示名' );
define( $constpref.'_FTP_NAME_DESC' ,           '管理者用の FTP 接続ネットボリュームの表示名' );
define( $constpref.'_FTP_HOST' ,                'FTP ホスト名' );
define( $constpref.'_FTP_HOST_DESC' ,           '' );
define( $constpref.'_FTP_PORT' ,                'FTP ポート番号' );
define( $constpref.'_FTP_PORT_DESC' ,           'FTP は通常 21 番ポートです' );
define( $constpref.'_FTP_PATH' ,                'ルートディレクトリ' );
define( $constpref.'_FTP_PATH_DESC' ,           'FTP設定はボリュームドライバの "ftp" プラグインにも使用されます。<br>"ftp" プラグイン用のみに設定する場合はルートディレクトリを空欄にしてください。' );
define( $constpref.'_FTP_USER' ,                'FTP ユーザー名' );
define( $constpref.'_FTP_USER_DESC' ,           '' );
define( $constpref.'_FTP_PASS' ,                'FTP パスワード' );
define( $constpref.'_FTP_PASS_DESC' ,           '' );
define( $constpref.'_FTP_SEARCH' ,              'FTP ボリュームを検索対象にする' );
define( $constpref.'_FTP_SEARCH_DESC' ,         'FTP ネットボリュームを検索対象にすると、検索に時間がかかりタイムアウトすることがあります。<br>有効にした場合は問題なく検索できるかの確認をお忘れなく。' );
define( $constpref.'_BOXAPI_ID' ,               'Box API OAuth2 client_id' );
define( $constpref.'_BOXAPI_ID_DESC' ,          'Box API Console [ https://app.box.com/developers/services ]' );
define( $constpref.'_BOXAPI_SECRET' ,           'Box API OAuth2 client_secret' );
define( $constpref.'_BOXAPI_SECRET_DESC' ,      'Boxをネットワークボリュームとして利用する場合はバックエンドとの接続を https に設定し Box API アプリケーションの設定 - redirect_uri に "'.str_replace('http://','https://',XOOPS_URL).'/modules/'.$mydirname.'/connector.php" を追加してください。(ドメイン以降のパスは省略可)' );
define( $constpref.'_GOOGLEAPI_ID' ,            'Google API クライアント ID' );
define( $constpref.'_GOOGLEAPI_ID_DESC' ,       'Google API Console [ https://console.developers.google.com ]' );
define( $constpref.'_GOOGLEAPI_SECRET' ,        'Google API クライアント シークレット' );
define( $constpref.'_GOOGLEAPI_SECRET_DESC' ,   'Googleドライブをネットワークボリュームとして利用する場合(PHP 5.4 以上が必須)は Google API コンソールの認証情報 - 承認済みのリダイレクト URL に "'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php?cmd=netmount&protocol=googledrive&host=1" を追加してください。' );
define( $constpref.'_ONEDRIVEAPI_ID' ,          'OneDrive API アプリケーション ID' );
define( $constpref.'_ONEDRIVEAPI_ID_DESC' ,     'Azure Active Directory [ https://portal.azure.com/#blade/Microsoft_AAD_IAM/ActiveDirectoryMenuBlade/RegisteredApps ]' );
define( $constpref.'_ONEDRIVEAPI_SECRET' ,      'OneDrive API パスワード' );
define( $constpref.'_ONEDRIVEAPI_SECRET_DESC' , 'OneDriveをネットワークボリュームとして利用する場合は OneDrive API アプリケーションの設定 - リダイレクト URL に "'.XOOPS_URL.'/modules/'.$mydirname.'/connector.php/netmount/onedrive/1" を追加してください。' );
define( $constpref.'_DROPBOX_TOKEN' ,           'Dropbox.com アプリケーション Key' );
define( $constpref.'_DROPBOX_TOKEN_DESC' ,      'Developers - Dropbox [ https://www.dropbox.com/developers ]' );
define( $constpref.'_DROPBOX_SECKEY' ,          'Dropbox.com アプリケーション Secret key' );
define( $constpref.'_DROPBOX_SECKEY_DESC' ,     '' );
define( $constpref.'_DROPBOX_ACC_TOKEN' ,       '共有Dropboxのアクセストークン' );
define( $constpref.'_DROPBOX_ACC_TOKEN_DESC' ,  '共有のDropboxボリュームで使用するためのアクセストークンは https://www.dropbox.com/developers/apps にて取得できます。' );
define( $constpref.'_DROPBOX_ACC_SECKEY' ,      '共有Dropboxのアクセストークン・シークレットキー' );
define( $constpref.'_DROPBOX_ACC_SECKEY_DESC' , '古い OAuth1 のための設定です。新しい OAuth2 のアクセストークンを設定する場合は値を空にする必要があります。OAuth1 を利用している場合は早めに OAuth2 に移行してください。' );
define( $constpref.'_DROPBOX_NAME' ,            '共有のDropboxボリューム表示名' );
define( $constpref.'_DROPBOX_NAME_DESC' ,       '共有のDropboxボリュームは、ネットワークボリュームのマウントと違い、すべてのユーザーに表示されます。' );
define( $constpref.'_DROPBOX_PATH' ,            '共有Dropboxのルートパス' );
define( $constpref.'_DROPBOX_PATH_DESC' ,       '共有のDropboxボリュームで一般に開示してもよい階層のパスを指定します。(設定例: "/Public")<br>Dropbox 設定はボリュームドライバの "dropbox" プラグインにも使用されます。<br>"dropbox" プラグイン用のみに設定する場合はルートパスを空欄にしてください。' );
define( $constpref.'_DROPBOX_HIDDEN_EXT' ,      '共有のDropbox非表示ファイル' );
define( $constpref.'_DROPBOX_HIDDEN_EXT_DESC' , '管理者にのみ表示するファイル（ファイル名の後方一致）をカンマ区切りで指定します。<br>末尾を「/」とした場合はフォルダを対象とします。' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS' , '共有Dropbox書き込み許可グループ' );
define( $constpref.'_DROPBOX_WRITABLE_GROUPS_DESC' , 'ここに設定したグループには、ファイル・フォルダの書き込みが許可されます。ただし、次の設定項目「共有のDropboxアップロード可能な MIME タイプ」、「共有のDropbox書き込みを許可ファイル」と「共有のDropbox非ロックファイル」で、何を許可するかをコントロールできます。その他のグループは読み取りのみ可能です。' );
define( $constpref.'_DROPBOX_UPLOAD_MIME' ,     '共有のDropboxアップロード可能な MIME タイプ') ;
define( $constpref.'_DROPBOX_UPLOAD_MIME_DESC' ,'書き込みを許可するグループがアップロード可能な MIME タイプをカンマ区切りで設定します。管理者はこの制限を受けません。') ;
define( $constpref.'_DROPBOX_WRITE_EXT' ,       '共有のDropbox書き込み許可ファイル') ;
define( $constpref.'_DROPBOX_WRITE_EXT_DESC' ,  '書き込みを許可するグループに書き込みを許可するファイル(ファイル名の後方一致)をカンマ区切りで指定します。<br>末尾を「/」とした場合はフォルダを対象とします。<br>管理者はこの制限を受けません。') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT' ,      '共有のDropbox非ロックファイル') ;
define( $constpref.'_DROPBOX_UNLOCK_EXT_DESC' , 'ファイルをロックしない(非ロック)と、削除・移動・リネームが可能になります。<br>ロックをしないファイル(ファイル名の後方一致)をカンマ区切りで指定します。<br>末尾を「/」とした場合はフォルダを対象とします。<br>管理者には全てのファイルがロックされません。') ;
define( $constpref.'_JQUERY' ,                  'jQuery の URL' );
define( $constpref.'_JQUERY_DESC' ,             'Google の CDN を利用しない場合に、jQuery の js の  URL を指定します。' );
define( $constpref.'_JQUERY_UI' ,               'jQuery UI の URL' );
define( $constpref.'_JQUERY_UI_DESC' ,          'Google の CDN を利用しない場合に、jQueryUI の js の  URL を指定します。' );
define( $constpref.'_JQUERY_UI_CSS' ,           'jQuery UI の CSS URL' );
define( $constpref.'_JQUERY_UI_CSS_DESC' ,      'Google の CDN を利用しない場合に、jQueryUI の css の  URL を指定します。' );
define( $constpref.'_JQUERY_UI_THEME' ,         'jQuery UI のテーマ' );
define( $constpref.'_JQUERY_UI_THEME_DESC' ,    'Google の CDN を利用する場合の jQuery UI のテーマをテーマ名、又は CSS の URL で指定します。 (デフォルト: smoothness)' );
define( $constpref.'_GMAPS_APIKEY' ,            'Google Maps API キー' );
define( $constpref.'_GMAPS_APIKEY_DESC' ,       'KML プレビューで使用する Google Maps の API キー' );
define( $constpref.'_ZOHO_APIKEY' ,             'Zoho office editor API キー' );
define( $constpref.'_ZOHO_APIKEY_DESC' ,        'Office アイテム編集時に Zoho office editor を使用する場合に API キーを指定します。<br/>API キーは <a href=""https://www.zoho.com/docs/help/office-apis.html#get-started" target="_blank">www.zoho.com/docs/help/office-apis.html</a> で取得できます。' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY' ,   'Creative SDK APIキー' );
define( $constpref.'_CREATIVE_CLOUD_APIKEY_DESC','Creative Cloud の Creative SDK のイメージエディターを利用する場合の Creative Cloud APIキーを指定します。<br>APIキー は https://console.adobe.io/ で取得できます。' );
define( $constpref.'_ONLINE_CONVERT_APIKEY' ,   'ONLINE-CONVERT.COM APIキー' );
define( $constpref.'_ONLINE_CONVERT_APIKEY_DESC','ONLINE-CONVERT.COM のコンテンツコンバーター API を利用する場合の ONLINE-CONVERT.COM APIキーを指定します。<br>APIキー は https://apiv2.online-convert.com/docs/getting_started/api_key.html で取得できます。' );
define( $constpref.'_EDITORS_JS',               'editors.js の URL' );
define( $constpref.'_EDITORS_JS_DESC',          'common/elfinder/js/extras/editors.default.js をカスタマイズした場合の JavaScript の URL を指定します。' );
define( $constpref.'_UI_OPTIONS_JS',            'xelfinderUiOptions.js の URL' );
define( $constpref.'_UI_OPTIONS_JS_DESC',       'modules/'.$mydirname.'/include/js/xelfinderUiOptions.default.js をカスタマイズした場合の JavaScript の URL を指定します。' );
define( $constpref.'_THUMBNAIL_SIZE' ,          '[xelfinder_db] 画像挿入時のサムネイルサイズ' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'BBコードでの画像挿入時のサムネイルサイズの規定値(px)' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       '[xelfinder_db] 作成されるアイテムのパーミッション' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'パーミッションは3桁で[ファイルオーナー][グループ][ゲスト]<br>各桁 2進数4bitで [非表示(h)][読み込み(r)][書き込み(w)][ロック解除(u)]<br>744: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 4 = -r--' );
define( $constpref.'_USE_USERS_DIR' ,           '[xelfinder_db] ユーザー別フォルダの使用' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          '[xelfinder_db] ユーザー別フォルダのパーミッション' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 7cc: オーナー 7 = -rwu, グループ c = hr--, ゲスト c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     '[xelfinder_db] ユーザー別フォルダに作成されるアイテムのパーミッション' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 7cc: オーナー 7 = -rwu, グループ c = hr--, ゲスト c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           '[xelfinder_db] ゲスト用フォルダの使用' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          '[xelfinder_db] ゲスト用フォルダのパーミッション' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 766: オーナー 7 = -rwu, グループ 6 = -rw-, ゲスト 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     '[xelfinder_db] ゲスト用フォルダに作成されるアイテムのパーミッション' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 744: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           '[xelfinder_db] グループ別フォルダの使用' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        '[xelfinder_db] グループ別フォルダの親フォルダ名' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'グループ毎閲覧');
define( $constpref.'_GROUP_DIR_PERM' ,          '[xelfinder_db] グループ別フォルダのパーミッション' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 768: オーナー 7 = -rwu, グループ 6 = -rw-, ゲスト 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     '[xelfinder_db] グループ別フォルダに作成されるアイテムのパーミッション' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br>例: 748: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      '[xelfinder_db] 管理者にアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'MIME タイプを半角スペース区切りで記述。<br>all: 全て許可, none: 何も許可しない<br>例: image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       '[xelfinder_db] 管理者用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  '画像をアップロード時、指定した矩形サイズに収まるように自動リサイズする値(px)。<br>何も入力しないと自動リサイズは行われません。' );
define( $constpref.'_UPLOAD_MAX_ADMIN' ,        '[xelfinder_db] 管理者用最大ファイルサイズ' );
define( $constpref.'_UPLOAD_MAX_ADMIN_DESC',    '管理者がアップロード可能な最大ファイルサイズを指定します。無指定または 0 で無制限となります。(例 10M)' );

define( $constpref.'_SPECIAL_GROUPS' ,          '[xelfinder_db] 特定グループ' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     '特定グループとするグループを選択 (複数選択可)' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   '[xelfinder_db] 特定グループにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    '[xelfinder_db] 特定グループ用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS' ,     '[xelfinder_db] 特定グループ用最大ファイルサイズ' );
define( $constpref.'_UPLOAD_MAX_SPGROUPS_DESC', '' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       '[xelfinder_db] 登録ユーザーにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        '[xelfinder_db] 登録ユーザー用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );
define( $constpref.'_UPLOAD_MAX_USER' ,         '[xelfinder_db] 登録ユーザー用最大ファイルサイズ' );
define( $constpref.'_UPLOAD_MAX_USER_DESC',     '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      '[xelfinder_db] ゲストにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       '[xelfinder_db] ゲスト用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );
define( $constpref.'_UPLOAD_MAX_GUEST' ,        '[xelfinder_db] ゲスト用最大ファイルサイズ' );
define( $constpref.'_UPLOAD_MAX_GUEST_DESC',    '' );

define( $constpref.'_DISABLE_PATHINFO' ,        '[xelfinder_db] ファイル参照URLの PathInfo を使用しない' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   '環境変数 "PATH_INFO" が利用できないサーバーは「はい」を選択してください。' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     '[xelfinder_db] リンク済みファイルの書き込み禁止' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'リンク切れや不用意な上書きを防止するためにリンク・参照されたファイルを自動的に書き込み禁止に設定します。' );

define( $constpref.'_CHECK_NAME_VIEW' ,         '[xelfinder_db] ファイル参照URLのファイル名の照合' );
define( $constpref.'_CHECK_NAME_VIEW_DESC' ,    'ファイル参照用URLのファイル名を照合し登録されたファイル名と合致しない場合は "404 Not Found" エラーを返します。' );

define( $constpref.'_CONNECTOR_URL' ,           '外部またはセキュア接続のコネクタURL（任意）' );
define( $constpref.'_CONNECTOR_URL_DESC' ,      '外部サイトのコネクタに接続する場合やバックエンドとの通信のみセキュアな環境を利用する場合に connector.php の URL を指定してください。' );

define( $constpref.'_CONN_URL_IS_EXT',          '外部のコネクタURL' );
define( $constpref.'_CONN_URL_IS_EXT_DESC',     '任意指定したコネクタURLが外部サイトの場合に「はい」、コネクタURLがバックエンド通信のみSSL接続するURLの場合は「いいえ」を選択してください。<br>外部サイトのコネクタに接続する場合は相手先サイトにて、当サイトのオリジンドメインが許可されている必要があります。' );

define( $constpref.'_ALLOW_ORIGINS',            '許可するドメインオリジン' );
define( $constpref.'_ALLOW_ORIGINS_DESC',       '当サイトのコネクタに接続を許可する外部サイトのドメインオリジン（例:"http://example.com" 最後のスラッシュは不要）を行区切りで設定します。<br>コネクタURLがバックエンド通信のみSSL接続するURLの場合は「 <strong>'.preg_replace('#^(https?://[^/]+).*$#', '$1', XOOPS_URL).'</strong> 」を指定する必要があります。' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'unzip 実行時のロケール' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   'アーカイブ解凍のコマンド unzip 使用時の言語ロケール設定。<br>通常は指定なしで問題ないと思われるが、解凍後のファイル名が文字化けする場合には ja_JP.Shift_JIS などとすると解消される場合がある。' );

define( $constpref.'_AUTOSYNC_SEC_ADMIN',       '自動更新間隔(管理者):秒' );
define( $constpref.'_AUTOSYNC_SEC_ADMIN_DESC',  '自動で更新チェックをする間隔を秒数で指定します。' );

define( $constpref.'_AUTOSYNC_SEC_SPGROUPS',    '自動更新間隔(特定グループ):秒' );
define( $constpref.'_AUTOSYNC_SEC_SPGROUPS_DESC', '' );

define( $constpref.'_AUTOSYNC_SEC_USER',        '自動更新間隔(登録ユーザー):秒' );
define( $constpref.'_AUTOSYNC_SEC_USER_DESC',   '' );

define( $constpref.'_AUTOSYNC_SEC_GUEST',       '自動更新間隔(ゲスト):秒' );
define( $constpref.'_AUTOSYNC_SEC_GUEST_DESC',  '' );

define( $constpref.'_AUTOSYNC_START',           'すぐに自動更新を開始する' );
define( $constpref.'_AUTOSYNC_START_DESC',      'コンテキストメニューの「リロード」で自動更新の開始・停止ができます。' );

define( $constpref.'_FFMPEG_PATH',              'ffmpeg コマンドのパス' );
define( $constpref.'_FFMPEG_PATH_DESC',         'ffmpeg コマンドのパスが必要な場合に指定してください。' );

define( $constpref.'_DEBUG' ,                   'デバッグモードを有効にする' );
define( $constpref.'_DEBUG_DESC' ,              'デバッグモードにすると elFinder の "elfinder.min.css", "elfinder.min.js" ではなく個別のファイルを読み込みます。<br>また、JavaScript のレスポンスにデバグ情報を含めます。<br>パフォーマンス向上のために、通常はデバッグモードは無効にして運用することをお勧めします。' );

// admin/dropbox.php
define( $constpref.'_DROPBOX_STEP1' ,        'Step 1: App の作成');
define( $constpref.'_DROPBOX_GOTO_APP' ,     '次のリンク先 (Dropbox.com) で App を作成し、 App key と App secre を取得し、一般設定の「%s」と「%s」へ設定してください。');
define( $constpref.'_DROPBOX_GET_TOKEN' ,    'Dropbox App Token の取得');
define( $constpref.'_DROPBOX_STEP2' ,        'Step 2: Dropbox へ行き、アプリを認可');
define( $constpref.'_DROPBOX_GOTO_CONFIRM' , '次のリンク先 (Dropbox.com) へ進み、アプリを認可してください。');
define( $constpref.'_DROPBOX_CONFIRM_LINK' , 'Dropbox.com へ行き、アプリを認可する');
define( $constpref.'_DROPBOX_STEP3' ,        'Step 3: 取得完了。一般設定へ設定');
define( $constpref.'_DROPBOX_SET_PREF' ,     '次の値を一般設定の各項目に設定してください。');

// admin/googledrive.php
define( $constpref.'_GOOGLEDRIVE_GET_TOKEN', 'Google Drive API' );

// admin/composer_update.php
define( $constpref.'_COMPOSER_UPDATE' ,       'Vendor アップデート- Composer' );
define( $constpref.'_COMPOSER_RUN_UPDATE' ,    'アップデートを実行する' );
define( $constpref.'_COMPOSER_UPDATE_STARTED','アップデートを開始しました。「アップデートが完了しました。」と表示されるまでお待ち下さい...' );
define( $constpref.'_COMPOSER_DONE_UPDATE' ,  'アップデートが完了しました。' );
define( $constpref.'_COMPOSER_UPDATE_ERROR' , 'ドライバがインストールされていないか、正しくインストールされていない可能性があります。' );
define( $constpref.'_COMPOSER_UPDATE_FAIL',   'ファイルが存在しません : %s ' );
define( $constpref.'_COMPOSER_UPDATE_SUCCESS','ベンダーファイルが存在します : %s ' );
define( $constpref.'_COMPOSER_UPDATE_TIME' ,  'インターネット接続によっては時間がかかる場合があります' );

}
