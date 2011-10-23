<?php
//%%%%%%	File Name  modulesadmin.php 	%%%%%
define("_MD_AM_MODADMIN","モジュール管理");
define("_MD_AM_MODULE","モジュール");
define("_MD_AM_VERSION","バージョン");
define("_MD_AM_LASTUP","最終更新日");
define("_MD_AM_DEACTIVATED","非アクティブ");
define("_MD_AM_ACTION","操作");
define("_MD_AM_DEACTIVATE","非アクティブにする");
define("_MD_AM_ACTIVATE","アクティブにする");
define("_MD_AM_UPDATE","アップデート");
define("_MD_AM_DUPEN","モジュールがDB内に2重登録されています！");
define("_MD_AM_DEACTED","選択されたモジュールを非アクティブにしました。このモジュールを安全に削除することができます。");
define("_MD_AM_ACTED","選択されたモジュールをアクティブにしました");
define("_MD_AM_UPDTED","選択されたモジュールをアップデートしました");
define("_MD_AM_SYSNO","システムモジュールを非アクティブにすることはできません");
define("_MD_AM_STRTNO","このモジュールは当サイトの開始モジュールとして登録されています。このモジュールを非アクティブにするには、一般設定メニューにおいて開始モジュールの変更を行ってください。");

// added in RC2
define("_MD_AM_PCMFM","以下の内容で更新を行います");

// added in RC3
define("_MD_AM_ORDER","表示順");
define("_MD_AM_ORDER0","（0 = 非表示）");
define("_MD_AM_ACTIVE","アクティブ");
define("_MD_AM_INACTIVE","非アクティブ");
define("_MD_AM_NOTINSTALLED","未インストール");
define("_MD_AM_NOCHANGE","変更なし");
define("_MD_AM_INSTALL","インストール");
define("_MD_AM_UNINSTALL","アンインストール");
define("_MD_AM_SUBMIT","送信");
define("_MD_AM_CANCEL","キャンセル");
define("_MD_AM_DBUPDATE","データベースを更新しました");
define("_MD_AM_BTOMADMIN","モジュール管理メニューへ戻る");

// %s represents module name
define("_MD_AM_FAILINS","%sモジュールをインストールできませんでした");
define("_MD_AM_FAILACT","%sモジュールをアクティブに設定することができませんでした");
define("_MD_AM_FAILDEACT","%sモジュールを非アクティブに設定することができませんでした");
define("_MD_AM_FAILUPD","%sモジュールをアップデートすることができませんでした。");
define("_MD_AM_FAILUNINS","%sモジュールをアンインストールできませんでした");
define("_MD_AM_FAILORDER","%sモジュールの表示順を変更できませんでした");
define("_MD_AM_FAILWRITE","メインメニューファイルへの書き込みに失敗しました");
define("_MD_AM_ALEXISTS","%sモジュールは既に存在します");
define("_MD_AM_ERRORSC", "エラー：");
define("_MD_AM_OKINS","%sモジュールのインストールが完了しました");
define("_MD_AM_OKACT","%sモジュールをアクティブに設定しました");
define("_MD_AM_OKDEACT","%sモジュールを非アクティブに設定しました");
define("_MD_AM_OKUPD","%sモジュールのアップデートが完了しました");
define("_MD_AM_OKUNINS","%sモジュールのアンインストールが完了しました");
define("_MD_AM_OKORDER","%sモジュールの表示順を変更しました");
define('_MD_AM_RUSUREINS', 'このモジュールをインストールするには下のボタンをクリックしてください');
define('_MD_AM_RUSUREUPD', 'このモジュールのアップデートを行うには下のボタンをクリックしてください');
define('_MD_AM_RUSUREUNINS', '本当にこのモジュールをアンインストールしてもよろしいですか？');
define('_MD_AM_LISTUPBLKS', 'モジュールのアップデートを行います。<br />選択のブロックの中身(テンプレートとオプション)は上書きされます。<br />');
define('_MD_AM_NEWBLKS', '新規ブロック');
define('_MD_AM_DEPREBLKS', 'Deprecated Blocks');  //-no use //[MADA]
?>