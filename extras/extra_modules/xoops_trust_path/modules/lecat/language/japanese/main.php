<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

define('_MD_LECAT_ERROR_REQUIRED', '{0}は必ず入力して下さい');
define('_MD_LECAT_ERROR_MINLENGTH', '{0}は半角{1}文字以上にして下さい');
define('_MD_LECAT_ERROR_MAXLENGTH', '{0}は半角{1}文字以内で入力して下さい');
define('_MD_LECAT_ERROR_EXTENSION', 'アップロードされたファイルは許可された拡張子と一致しません');
define('_MD_LECAT_ERROR_INTRANGE', '{0}の入力値が不正です');
define('_MD_LECAT_ERROR_MIN', '{0}は{1}以上の数値を指定して下さい');
define('_MD_LECAT_ERROR_MAX', '{0}は{1}以下の数値を指定して下さい');
define('_MD_LECAT_ERROR_OBJECTEXIST', '{0}の入力値が不正です');
define('_MD_LECAT_ERROR_DBUPDATE_FAILED', 'データベースの更新に失敗しました');
define('_MD_LECAT_ERROR_EMAIL', '{0}は不正なメールアドレスです');
define('_MD_LECAT_ERROR_HAS_CHILDREN', '子カテゴリが存在しているため、削除できません。');
define('_MD_LECAT_ERROR_HAS_CLIENT_DATA', 'このカテゴリを使っているデータがあるため、削除できません。');
define('_MD_LECAT_MESSAGE_CONFIRM_DELETE', '以下のデータを本当に削除しますか？');
define('_MD_LECAT_LANG_ADD_A_NEW_CAT', 'カテゴリの追加');
define('_MD_LECAT_LANG_CAT_ID', 'カテゴリID');
define('_MD_LECAT_LANG_TITLE', 'カテゴリ名');
define('_MD_LECAT_LANG_P_ID', '親カテゴリ');
define('_MD_LECAT_LANG_MODULES', 'モジュール');
define('_MD_LECAT_LANG_DESCRIPTION', '説明');
define('_MD_LECAT_LANG_DEPTH', 'カテゴリレベル');
define('_MD_LECAT_LANG_WEIGHT', '表示順');
define('_MD_LECAT_LANG_OPTIONS', 'オプション');
define('_MD_LECAT_LANG_CONTROL', '操作');
define('_MD_LECAT_LANG_CAT_EDIT', 'カテゴリ編集');
define('_MD_LECAT_LANG_CAT_DELETE', 'カテゴリ削除');
define('_MD_LECAT_ERROR_CONTENT_IS_NOT_FOUND', 'データが見つかりませんでした');
define('_MD_LECAT_LANG_LEVEL', 'レベル');
define('_MD_LECAT_LANG_ACTIONS', 'アクション');
define('_MD_LECAT_LANG_ADD_A_NEW_PERMIT', '権限の追加');
define('_MD_LECAT_LANG_PERMIT_ID', '権限ID');
define('_MD_LECAT_LANG_UID', 'ユーザー');
define('_MD_LECAT_LANG_GROUPID', 'ユーザーグループ');
define('_MD_LECAT_LANG_PERMISSIONS', '権限');
define('_MD_LECAT_LANG_PERMIT_EDIT', '権限の編集');
define('_MD_LECAT_LANG_PERMIT_DELETE', '権限の削除');
define('_MD_LECAT_ERROR_NO_CATEGORY_REQUESTED', 'カテゴリの指定がありません。');
define('_MD_LECAT_LANG_PARENT', '親カテゴリ');
define('_MD_LECAT_LANG_CAT', 'カテゴリ');
define('_MD_LECAT_LANG_AUTH_SETTING', '権限の設定');
define('_MD_LECAT_LANG_AUTH_KEY', '権限キー名');
define('_MD_LECAT_LANG_AUTH_TITLE', '権限名');
define('_MD_LECAT_LANG_AUTH_DEFAULT', '初期値');
define('_MD_LECAT_LANG_EDIT_ACTOR', '権限キーの設定');
define('_MD_LECAT_LANG_MODULES_CONFINEMENT', 'モジュールの限定');
define('_MD_LECAT_LANG_PERMISSION_TYPE', 'アクセス権タイプ');
define('_MD_LECAT_LANG_DEFAULT_PERMISSIONS', 'アクセス権の初期設定');
define('_MD_LECAT_DESC_PERMISSION_TYPE', 'アクセス権のタイプと初期設定をします');
define('_MD_LECAT_LANG_ADD_A_NEW_PERMISSION_TYPE', 'アクセス権のタイプの追加');
define('_MD_LECAT_LANG_VIEWER', '閲覧者権限');
define('_MD_LECAT_LANG_POSTER', '投稿者権限');
define('_MD_LECAT_LANG_MANAGER', '管理者権限');
define('_MD_LECAT_LANG_CATEGORY', 'カテゴリ');
define('_MD_LECAT_LANG_TOP_CAT', 'トップカテゴリ');
define('_MD_LECAT_LANG_DELEET_ALL_PERMIT', 'このカテゴリのアクセス権を初期値に戻す');
define('_MD_LECAT_LANG_PERMISSION_ON', 'アクセス権あり');
define('_MD_LECAT_LANG_LEVEL_UNLIMITED', '無制限');
define('_MD_LECAT_TIPS_CATEGORY_SET', '<p>Lecat は共通カテゴリ管理モジュールです。<br />ニュースやフォーラム、コンテンツなど、複数のモジュールでLecatのカテゴリ管理機能を利用することができます。</p><p>Lecatは、主に二つの機能を持ちます。<ul><li>ツリー（階層）構造でのカテゴリ管理</li><li>カテゴリごとのアクセス権限管理</li></ul></p><h3>複数のカテゴリセット</h3><p>モジュールによって、別のカテゴリをもちたいことはよくあります。例えば、ニュースと掲示板で全く別のカテゴリ分けをしたいという要望があったりします。</p><p>その場合、モジュールを複製することでこの要望に応えることができます。</p>');
define('_MD_LECAT_TIPS_LEVEL', 'カテゴリ階層の制限です。「0」を設定すると無制限となります。');
define('_MD_LECAT_TIPS_MODULE_CONFINEMENT', 'このカテゴリを利用するモジュールを "," 区切りで指定します。すべてのモジュールでこのカテゴリを利用する場合は入力しないでください。');
define('_MD_LECAT_TIPS_PERMISSIONS', '初期状態では親カテゴリのアクセス権を引き継ぎます。親カテゴリと違うアクセス権を設定したい場合にのみチェックの状態を変更してください。');

?>
