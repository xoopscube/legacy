=================================================
Title:  Check Block Table
Date:   2007-10-10
Author: Kenichi OHWADA
URL:    http://linux.ohwada.jp/
Email:  webmaster@ohwada.jp
=================================================

XOOPS のモジュールに対して、
xoops_version.php で定義したものと、block テーブルに格納されているものが、
一致しているかを検査します。

● インストール
XOOPS_ROOT_PATH の下に "check_blocks.php" をコピーする

● 使用方法
WEB管理者でログインする
check_blocks.php にアクセスする

不一致があれば、
まずは、モジュール・アップデートを実行する

それで直らないときは、
いったん "Remove Block" を実行して block テーブル内のレコードを削除したあとに、
モジュール・アップデートを実行する

● 適用バージョン
下記のバージョンで動作確認をしています

- XOOPS 2.0.16aJP
- XOOPS Cube 2.1.2
- XOOPS 2.0.17

● 同封したファイル
- check_blocks.php
