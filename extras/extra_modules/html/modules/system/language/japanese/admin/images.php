<?php
// $Id: images.php,v 1.1 2007/05/15 02:34:23 minahito Exp $
//%%%%%% Image Manager %%%%%


define('_MD_IMGMAIN','イメージ管理');

define('_MD_ADDIMGCAT','イメージカテゴリの追加:');
define('_MD_EDITIMGCAT','イメージカテゴリの編集:');
define('_MD_IMGCATNAME','カテゴリ名:');
define('_MD_IMGCATRGRP','イメージ・マネジャーの使用を許可するグループ:<br /><br /><span style="font-weight: normal;">イメージ・マネジャーの使用を許可するグループを指定してください。イメージの選択のみ可能でアップロードはできません。Webmasterは自動的にアクセス許可になります。</span>');
define('_MD_IMGCATWGRP','イメージのアップロードを許可するグループ:<br /><br /><span style="font-weight: normal;">一般に、モデレータと管理者グループに許可するように設定します。</span>');
define('_MD_IMGCATWEIGHT','イメージ・マネジャー内での表示順序:');
define('_MD_IMGCATDISPLAY','このカテゴリを表示する:');
define('_MD_IMGCATSTRTYPE','イメージファイルのアップロード先:');
define('_MD_STRTYOPENG','この設定を後で変更することはできません！');
define('_MD_INDB',' データベースに格納（BLOB 形式で格納します）');
define('_MD_ASFILE',' ファイルとして保存（uploadsディレクトリに保存します）<br />');
define('_MD_RUDELIMGCAT','本当にこのカテゴリとカテゴリ内の全てのイメージを削除してもよろしいですか？');
define('_MD_RUDELIMG','このイメージファイルを削除してもよろしいですか？');

define('_MD_FAILDEL', 'イメージデータ %s のデータベースからの削除ができませんでした。');
define('_MD_FAILDELCAT', 'カテゴリ %s のデータベースからの削除ができませんでした。');
define('_MD_FAILUNLINK', 'イメージファイル %s のサーバからの削除ができませんでした。');
?>
