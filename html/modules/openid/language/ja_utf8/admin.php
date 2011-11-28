<?php
// $Rev$
// $URL$
define('_AD_OPENID_LANG_NEW', '新規作成');
define('_AD_OPENID_LANG_EDIT', '編集');
define('_AD_OPENID_LANG_DELETE', '削除');
define('_AD_OPENID_LANG_ALLOW', '許可');
define('_AD_OPENID_LANG_DENY', '拒否');
define('_AD_OPENID_LANG_CHECKPOINT', 'フィルターに一致させる対象');
define('_AD_OPENID_LANG_PATTERN', '認証サーバーのURL');
define('_AD_OPENID_LANG_AUTH', '許可しますか？');
define('_AD_OPENID_LANG_GROUP', 'この認証サーバーで認証されたユーザーを（新規登録時に）割り当てるグループ');
define('_AD_OPENID_LANG_GENERATOR', '許可フィルター簡単作成');
define('_AD_OPENID_LANG_GENERATOR_KEY', '許可の追加');
define('_AD_OPENID_LANG_SPECIFIED', '許可パターンの生成に個別のIDが必要な場合、それを指定します。<br />
例： マイミクシィ認証の場合自分のミクシィID');
define('_AD_OPENID_LANG_CLEANUP', '期限切れアソシエーションの削除');
define('_AD_OPENID_LANG_DO_CLEANUP', '右のボタンをクリックして実行してください');
define('_AD_OPENID_LANG_INACTIVE', '無効');
define('_AD_OPENID_LANG_PRIVATE', '非公開');
define('_AD_OPENID_LANG_OPEN2MEMBER', 'ユーザー間公開');
define('_AD_OPENID_LANG_PUBLIC', '公開');
define('_AD_OPENID_LANG_USER', 'ユーザー名');
define('_AD_OPENID_LANG_MODE', '公開レベル');
define('_AD_OPENID_LANG_CREATED', '登録日時');
define('_AD_OPENID_LANG_MODIFIED', '修正日時');
define('_AD_OPENID_LANG_IDENTIFIER', '登録されているOpenID一覧');
define('_AD_OPENID_LANG_GROUPS', '登録グループ');
define('_AD_OPENID_LANG_FILTER_0', 'ブラックリスト');
define('_AD_OPENID_LANG_FILTER_1', 'ホワイトリスト');
define('_AD_OPENID_LANG_FILTERLEVEL_0', '現在フィルターを使用しない設定です。');
define('_AD_OPENID_LANG_FILTERLEVEL_1', '現在いずれのルールにも合致しないOpenIDを許可する設定となっています。必要に応じてブラックリストを登録します。特別な権限グループに割り当てる対象をホワイトリストに登録することもできます。');
define('_AD_OPENID_LANG_FILTERLEVEL_2', '現在いずれのルールにも合致しないOpenIDを拒否する設定となっています。少なくとも１つ以上のホワイトリストを登録する必要があります。');
define('_AD_OPENID_LANG_FILTER_DEFAULT', '指定しない');
define('_AD_OPENID_LANG_ASSOC', 'アソシエーション確立済サーバーリスト');
define('_AD_OPENID_LANG_ASSOC_DESC', 'サーバーをホワイトリストまたはブラックリストに追加するには、右の許可または拒否をクリックします。<br />
期限切れのアソシエーションは定期的に消去しましょう。');
define('_AD_OPENID_LANG_ISSUED', '有効期限');
define('_AD_OPENID_LANG_EXTENSION', '拡張機能一覧');
define('_AD_OPENID_LANG_EXTENSION_DESC', '拡張機能の設定は各拡張モジュールの設定画面で行います。通常この画面では何も行う必要はありません。拡張モジュールをアンインストールしてもここに残ってしまった場合は削除します。');
define('_AD_OPENID_LANG_MODNAME', '拡張モジュール');
define('_AD_OPENID_LANG_DIRNAME', '導入先ディレクトリ');
define('_AD_OPENID_LANG_BUTTONS', 'ログインボタン');
define('_AD_OPENID_LANG_BUTTONS_DESC', 'ユーザーが簡単にわかりやすくログインできるように、各OPの専用ログインボタンを表示できます。');
define('_AD_OPENID_LANG_DESCRIPTION', 'タイトル');
define('_AD_OPENID_LANG_IMAGE', 'ボタン画像');
define('_AD_OPENID_LANG_TYPE', '種別');
define('_AD_OPENID_LANG_TYPE_SERVER', 'OP-Identifier');
define('_AD_OPENID_LANG_TYPE_SINON', 'OpenIDフォーマット');
define('_AD_OPENID_LANG_RANGE', 'ユーザー固有ID範囲(start,size)');

define('_AD_OPENID_LANG_UPDATE_CERT', 'CURLの証明書(PEM)ファイルの更新');
define('_AD_OPENID_LANG_UPDATE_CERT_CAUTION', '一般設定で指定された場所にCURLの追加の証明書(PEM)ファイルをダウンロードします。PHPによって書き込み可能な場所が指定されている必要があります。同名のファイルが既にあった場合は上書きされます。');
define('_AD_OPENID_ERROR_NO_RAND_SOURCE', '一般設定で指定された乱数生成デバイスが使用できません。正しいパスを指定するか、使用できない場合は空欄にしてください。');
define('_AD_OPENID_ERROR_NO_CAINFO', '一般設定で指定された場所にCURLの追加の証明書(PEM)ファイルが見つかりません。');
define('_AD_OPENID_ERROR_NOT_WRITABLE', '指定されたファイルは書き込みできません');
define('_AD_OPENID_ERROR_NOT_WRITABLE_DIR', '指定されたディレクトリは書き込みできません');
define('_AD_OPENID_ERROR_NOT_EXIST_DIR', '指定されたディレクトリは存在しません');
define('_AD_OPENID_ERROR_NOT_CONECT', '配布サイトに接続できませんでした');
define('_AD_OPENID_ERROR_NOT_UPDATE_CONFIG', 'ファイルの更新に成功しました。一般設定に以下の値を設定してください。<br>');
define('_AD_OPENID_ERROR_NO_SSL', 'Web サーバーの PHP で OpenSSL 拡張機能が有効になっていないため、この環境では HTTPS URL の探索ができません。');
?>