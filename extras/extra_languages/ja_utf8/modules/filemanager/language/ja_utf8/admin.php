<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

// --------------------------------------------------------
// メイン
// --------------------------------------------------------
define('_AD_FILEMANAGER_MAIN_DSC', "現在登録されているメディアの一覧です。");
define('_AD_FILEMANAGER_PATH_HOME', "ホーム");
define('_AD_FILEMANAGER_TYPE', "タイプ");
define('_AD_FILEMANAGER_PARENT', "上へ");
define('_AD_FILEMANAGER_EDIT', "追加");
define('_AD_FILEMANAGER_DEL', "削除");
define('_AD_FILEMANAGER_RETURN', "リストへ戻る");
define('_AD_FILEMANAGER_ACTION_DELETE', "&nbsp;削除");
define('_AD_FILEMANAGER_ACTION_DEFULT', "&nbsp;-----");
define('_AD_FILEMANAGER_ACTION_SUBMIT', "&nbsp;適用&nbsp;");
define('_AD_FILEMANAGER_FILE_TOTAL', "合計");

// --------------------------------------------------------
// エラーメッセージ
// --------------------------------------------------------
define('_AD_FILEMANAGER_ERROR_REQUIRED', "{0}は必ず入力して下さい");
define('_AD_FILEMANAGER_ERROR_PERMISSION', "アクセス権限がありません。");
define('_AD_FILEMANAGER_ERROR_FILE_PERMISSION', "%s は、アクセス権限がありません。");
define('_AD_FILEMANAGER_ERROR_DELETE_FOR_PERMISSION', "%s は、削除するアクセス権限がない為ファイルマネージャーから削除できません。");
define('_AD_FILEMANAGER_NOTFOUND', "ファイルが見つかりません。");

// --------------------------------------------------------
// アップロード
// --------------------------------------------------------
define('_AD_FILEMANAGER_PREVIEW', "プレビュー");
define('_AD_FILEMANAGER_FILENAME', "ファイル");
define('_AD_FILEMANAGER_SIZE', "サイズ");
define('_AD_FILEMANAGER_DATE', "更新日時");
define('_AD_FILEMANAGER_UPLOAD', "アップロード");
define('_AD_FILEMANAGER_UPLOAD_DSC', "Uploadをクリックして、ファイルを選択するとアップロードを開始します。");
define('_AD_FILEMANAGER_UPLOAD_NOTACCESS',  "%s は、アップロードできません。FTPソフトなどで、パーミッションを変更して下さい。");
define('_AD_FILEMANAGER_NOTFOUNDURL', "アップロードパスが見つかりません。");
define('_AD_FILEMANAGER_CONFIRMMSSAGE', "%s へファイルをアップロードします。<br />アップロード可能なファイルサイズは %sまで可能です。");
define('_AD_FILEMANAGER_UPLOAD_PERMISSION', "アップロードパスが見つからないか、アップロード権限がありません。");
define('_AD_FILEMANAGER_FOLDER_ADD', "ディレクトリ追加");

// --------------------------------------------------------
// フォルダ
// --------------------------------------------------------
define('_AD_FILEMANAGER_FOLDER', "フォルダ");
define('_AD_FILEMANAGER_FOLDERNAME', "フォルダ名");
define('_AD_FILEMANAGER_FOLDER_UPLOAD', "このフォルダにアップロード");
define('_AD_FILEMANAGER_ERROR_FOLDERNAME', "フォルダ名が正しくありません。フォルダ名を確認して下さい。<br />フォルダ名で使える文字は、半角英数字-~_のみが利用出来ます。<br />英字は小文字のみが利用出来ます。");
define('_AD_FILEMANAGER_ERROR_PATH', "フォルダ名の指定が正しくありません。フォルダ名を確認して下さい。");
define('_AD_FILEMANAGER_ADD', "ﾌｫﾙﾀﾞ追加");
define('_AD_FILEMANAGER_ADDFOLDER', "フォルダの追加");
define('_AD_FILEMANAGER_ADDFOLDER_DSC', "新しいフォルダを追加します。作成されたフォルダはファイルマネージャーから操作できます。");
define('_AD_FILEMANAGER_ADDFOLDER_SUCCESS', "フォルダを追加しました。");
define('_AD_FILEMANAGER_ADDFOLDER_ERROR', "フォルダがないか、アクセス権限がない為、フォルダを追加出来ません。");
define('_AD_FILEMANAGER_ADDFOLDER_CONFIRMMSSAGE', "%s の下にフォルダを作成します。<br />作成するフォルダ名を入力して下さい。");
define('_AD_FILEMANAGER_DELET', "ﾌｫﾙﾀﾞ削除");
define('_AD_FILEMANAGER_DELFOLDER', "フォルダの削除");
define('_AD_FILEMANAGER_DELFOLDER_DSC', "指定したフォルダを削除します。");
define('_AD_FILEMANAGER_DELFOLDER_CONFIRMMSSAGE', "フォルダ %s を削除します。");
define('_AD_FILEMANAGER_DELFOLDER_FILE_EXISTS', "フォルダにファイルがある為 %s は削除できません。フォルダの内容を確認して下さい。");
define('_AD_FILEMANAGER_DELFOLDER_SUCCESS', "フォルダを削除しました。");
define('_AD_FILEMANAGER_DELFOLDER_ERROR', "フォルダを削除出来ません。指定したフォルダが空でないか、適切なパーミッションでありません。");
define('_AD_FILEMANAGER_DELFOLDER_ISDIR', "フォルダ %s は、フォルダでない為ファイルマネージャーから削除できません。");
define('_AD_FILEMANAGER_DELFOLDER_NOTACCESS', "フォルダ %s は、ファイルマネージャーから削除できません。FTPソフトなどで、パーミッションを変更して下さい。");
define('_AD_FILEMANAGER_FILECOUNT', "ファイル合計");

// --------------------------------------------------------
// SWFUpload
// --------------------------------------------------------
define('_AD_FILEMANAGER_SWF_UPLOAD_QUEUE', "アップロード");
define('_AD_FILEMANAGER_SWF_UPLOAD_CNACEL', "すべてのアップロードをキャンセルする");
define('_AD_FILEMANAGER_SWF_COULD_NOT_LOAD', "SWFUpload ライブラリをロード出来ません。JavaScript の利用を許可して下さい。");
define('_AD_FILEMANAGER_SWF_LOADING', "SWFUpload ライブラリを読み込んでいます。しばらくお待ちください...");
define('_AD_FILEMANAGER_SWF_LOAD_HAS_FAILED', "SWFUpload ライブラリをロード出来ません。ライブラリがセットされているか確認するか、Flash プレイヤーをインストールして下さい。");
define('_AD_FILEMANAGER_SWF_INSTALL_FLASH', "SWFUpload  ライブラリをロード出来ません。ライブラリがセットされているか確認するか、Flash プレイヤーをインストールして下さい。<br />こちらから <a href=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\">Adobe website</a>Flash プレイヤーをインストールして下さい");

// --------------------------------------------------------
// アップロード
// --------------------------------------------------------
define('_AD_FILEMANAGER_OPTION', "オプション");
define('_AD_FILEMANAGER_OPTION_DSC', "オプション");

// --------------------------------------------------------
// reserved  options setting
// --------------------------------------------------------
define('_AD_FILEMANAGER_FILTER', "フィルター");
define('_AD_FILEMANAGER_FILTER_ALL', "---");
define('_AD_FILEMANAGER_FILTER_IMGAE', "画像");
define('_AD_FILEMANAGER_FILTER_MOVIE', "動画");
define('_AD_FILEMANAGER_FILTER_SOUND', "音楽");
define('_AD_FILEMANAGER_FILTER_APLICATION', "アプリ");
define('_AD_FILEMANAGER_ACTION_CONVERT', "FLV変換");
define('_AD_FILEMANAGER_ACTION_CAPTURE', "キャプチャー画像作成");
define('_AD_FILEMANAGER_CONVERT_DSC', "指定された動画ファイルをFLV形式に変換中です。");
define('_AD_FILEMANAGER_CONVERT_NOW', "FLVファイルに変換作業中です。変換が完了するまでしばらくお待ちください。<br />変換が終わったら元の画面へ戻ります。 ");

?>