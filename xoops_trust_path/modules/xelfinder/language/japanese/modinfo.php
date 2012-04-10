<?php

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) $mydirname = 'xelfinder' ;
$constpref = '_MI_' . strtoupper( $mydirname ) ;

if( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref.'_LOADED' ) ) {

// a flag for this language file has already been read or not.
define( $constpref.'_LOADED' , 1 ) ;

define( $constpref.'_DESC' , 'Webベースのファイルマネージャ elFinder をイメージマネージャとして利用するモジュール');

// admin menu
define($constpref.'_ADMENU_MYLANGADMIN' ,   '言語定数管理' ) ;
define($constpref.'_ADMENU_MYTPLSADMIN' ,   'テンプレート管理' ) ;
define($constpref.'_ADMENU_MYBLOCKSADMIN' , 'ブロック管理/アクセス権限' ) ;
define($constpref.'_ADMENU_MYPREFERENCES' , '一般設定' ) ;

// configurations
define( $constpref.'_VOLUME_SETTING' ,          'ボリュームドライバ' );
define( $constpref.'_VOLUME_SETTING_DESC' ,     '[モジュールディレクトリ名]:[プラグイン名]:[ファイル格納ディレクトリ]:[表示名]<br />行単位で記述。先頭に # を置くと無視されます。' );
define( $constpref.'_SHARE_HOLDER' ,            '共有ホルダ' );
define( $constpref.'_THUMBNAIL_SIZE' ,          '画像挿入時のサムネイルサイズ' );
define( $constpref.'_THUMBNAIL_SIZE_DESC' ,     'BBコードでの画像挿入時のサムネイルサイズの規定値(px)' );
define( $constpref.'_DEFAULT_ITEM_PERM' ,       '作成されるアイテムのパーミッション' );
define( $constpref.'_DEFAULT_ITEM_PERM_DESC' ,  'パーミッションは3桁で[ファイルオーナー][グループ][ゲスト]<br />各桁 2進数4bitで [非表示(h)][読み込み(r)][書き込み(w)][ロック解除(u)]<br />744: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 4 = -r--' );
define( $constpref.'_USE_USERS_DIR' ,           'ユーザー別ホルダの使用' );
define( $constpref.'_USE_USERS_DIR_DESC' ,      '' );
define( $constpref.'_USERS_DIR_PERM' ,          'ユーザー別ホルダのパーミッション' );
define( $constpref.'_USERS_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 7cc: オーナー 7 = -rwu, グループ c = hr--, ゲスト c = hr--' );
define( $constpref.'_USERS_DIR_ITEM_PERM' ,     'ユーザー別ホルダに作成されるアイテムのパーミッション' );
define( $constpref.'_USERS_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 7cc: オーナー 7 = -rwu, グループ c = hr--, ゲスト c = hr--' );
define( $constpref.'_USE_GUEST_DIR' ,           'ゲスト用ホルダの使用' );
define( $constpref.'_USE_GUEST_DIR_DESC' ,      '' );
define( $constpref.'_GUEST_DIR_PERM' ,          'ゲスト用ホルダのパーミッション' );
define( $constpref.'_GUEST_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 766: オーナー 7 = -rwu, グループ 6 = -rw-, ゲスト 6 = -rw-' );
define( $constpref.'_GUEST_DIR_ITEM_PERM' ,     'ゲスト用ホルダに作成されるアイテムのパーミッション' );
define( $constpref.'_GUEST_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 744: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 4 = -r--' );
define( $constpref.'_USE_GROUP_DIR' ,           'グループ別ホルダの使用' );
define( $constpref.'_USE_GROUP_DIR_DESC' ,      '' );
define( $constpref.'_GROUP_DIR_PARENT' ,        'グループ別ホルダの親ホルダ名' );
define( $constpref.'_GROUP_DIR_PARENT_DESC' ,   '' );
define( $constpref.'_GROUP_DIR_PARENT_NAME' ,   'グループ毎閲覧');
define( $constpref.'_GROUP_DIR_PERM' ,          'グループ別ホルダのパーミッション' );
define( $constpref.'_GROUP_DIR_PERM_DESC' ,     'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 768: オーナー 7 = -rwu, グループ 6 = -rw-, ゲスト 8 = h---' );
define( $constpref.'_GROUP_DIR_ITEM_PERM' ,     'グループ別ホルダに作成されるアイテムのパーミッション' );
define( $constpref.'_GROUP_DIR_ITEM_PERM_DESC' ,'ここでの設定は作成時のみ参照されます。作成後は elFinder で直接変更してください。<br />例: 748: オーナー 7 = -rwu, グループ 4 = -r--, ゲスト 8 = h---' );

define( $constpref.'_UPLOAD_ALLOW_ADMIN' ,      '管理者にアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_ADMIN_DESC' , 'MIME タイプを半角スペース区切りで記述。<br />all: 全て許可, none: 何も許可しない<br />例: image text/plain' );
define( $constpref.'_AUTO_RESIZE_ADMIN' ,       '管理者用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_ADMIN_DESC' ,  '画像をアップロード時、指定した矩形サイズに収まるように自動リサイズする値(px)。<br />何も入力しないと自動リサイズは行われません。' );

define( $constpref.'_SPECIAL_GROUPS' ,          '特定グループ' );
define( $constpref.'_SPECIAL_GROUPS_DESC' ,     '特定グループとするグループを選択 (複数選択可)' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS' ,   '特定グループにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_SPGROUPS_DESC','' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS' ,    '特定グループ用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_SPGROUPS_DESC','' );

define( $constpref.'_UPLOAD_ALLOW_USER' ,       '登録ユーザーにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_USER_DESC' ,  '' );
define( $constpref.'_AUTO_RESIZE_USER' ,        '登録ユーザー用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_USER_DESC',    '' );

define( $constpref.'_UPLOAD_ALLOW_GUEST' ,      'ゲストにアップロードを許可する MIME タイプ' );
define( $constpref.'_UPLOAD_ALLOW_GUEST_DESC' , '' );
define( $constpref.'_AUTO_RESIZE_GUEST' ,       'ゲスト用自動リサイズ (px)' );
define( $constpref.'_AUTO_RESIZE_GUEST_DESC',   '' );

define( $constpref.'_DISABLE_PATHINFO' ,        'ファイル参照URLの PathInfo を無効にする' );
define( $constpref.'_DISABLE_PATHINFO_DESC' ,   '' );

define( $constpref.'_EDIT_DISABLE_LINKED' ,     'リンク済みファイルの書き込み禁止' );
define( $constpref.'_EDIT_DISABLE_LINKED_DESC' ,'リンク切れや不用意な上書きを防止するためにリンク・参照されたファイルを自動的に書き込み禁止に設定します。' );

define( $constpref.'_UNZIP_LANG_VALUE' ,        'unzip 実行時のロケール' );
define( $constpref.'_UNZIP_LANG_VALUE_DESC' ,   'アーカイブ解凍のコマンド unzip 使用時の言語ロケール設定。<br />通常は指定なしで問題ないと思われるが、解凍後のファイル名が文字化けする場合には ja_JP.Shift_JIS などとすると解消される場合がある。' );

define( $constpref.'_DEBUG' ,                   'デバッグモードを有効にする' );
define( $constpref.'_DEBUG_DESC' ,              'デバッグモードにすると elFinder の "elfinder.min.css", "elfinder.min.js" ではなく個別のファイルを読み込みます。<br />また、JavaScript のレスポンスにデバグ情報を含めます。<br />パフォーマンス向上のために、通常はデバッグモードは無効にして運用することをお勧めします。' );

}
