<?php
// $Rev$
// $URL$
define("_MI_OPENID_NAME","OpenID認可モジュール");
define("_MI_OPENID_DESC","ユーザーがOpenIDをつかってログインできるようにします。");
define("_MI_OPENID_BNAME1","OpenIDログイン");
define('_MI_OPENID_ADMENU', '登録されているOpenID');
define('_MI_OPENID_ADMENU_FILTER_0', 'ブラックリスト');
define('_MI_OPENID_ADMENU_FILTER_1', 'ホワイトリスト');
define('_MI_OPENID_ADMENU_ASSOC', 'アソシエーション');
define('_MI_OPENID_ADMENU_EXTENSION', '拡張機能');
define('_MI_OPENID_ADMENU_BUTTONS', 'ログインボタン');
define('_MI_OPENID_RAND_SOURCE', '乱数生成デバイス');
define('_MI_OPENID_RAND_SOURCE_DESC', '乱数生成デバイスが使用可能かどうかはサーバーの管理者に問い合わせてください。使用できない場合は空欄にします。初期値は"/dev/urandom"です');
define('_MI_OPENID_FILTER_LEVEL', 'フィルターの使用について');
define('_MI_OPENID_FILTER_NON', 'フィルターを使用せず全て許可する');
define('_MI_OPENID_FILTER_ALLOW_DEFAULT', 'どのフィルターにも一致しないOpenIDを許可する');
define('_MI_OPENID_FILTER_DENY_DEFAULT', 'どのフィルターにも一致しないOpenIDを拒否する');
define('_MI_OPENID_DEFAULT_GROUP', '初期登録グループ');
define('_MI_OPENID_DEFAULT_GROUP_DESC', 'フィルターを使用しない場合およびフィルターに一致しないOpenID（それを許可するなら）は登録時にこのユーザーグループに割当てられます。');
define('_MI_OPENID_CAINFO_FILE', 'CURLの追加のPEMファイルのパス');
define('_MI_OPENID_CAINFO_FILE_DESC', 'HTTPクライアントライブラリのルート証明書が古い場合、OPのサーバ証明書の検証に失敗する場合があります。その場合追加のPEMファイルをサーバーに置いてそのパスを指定します。');
define('_MI_OPENID_BLOCK_LABEL', 'OpenIDを入力');
define('_MI_OPENID_CONF_ALLOW_REGISTER', 'OpenIDによる新規ユーザー登録を許可する');
define('_MI_OPENID_CONF_ALW_RG_DESC', '「いいえ」を選択するとOpenIDを既存ユーザーに紐付ける事だけを許可します。');
define('_MI_OPENID_CONF_MPOLICY', 'OpenID公開レベル');
define('_MI_OPENID_CONF_MPOLICY_DESC', 'ユーザーのOpenIDの公開ポリシーを設定します');
define('_MI_OPENID_CONF_USERS_CHOICE', 'ユーザーに選択させる');
define('_MI_OPENID_CONF_PRIVATE', '全て非公開');
define('_MI_OPENID_CONF_OPEN2MEMBER', '全て登録ユーザーにのみ公開');
define('_MI_OPENID_CONF_PUBLIC', '全て公開');
?>