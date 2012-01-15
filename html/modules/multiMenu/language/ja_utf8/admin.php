<?php
define('_AD_MULTIMENU_ADMIN', 	'設定:マルチメニュー');
define('_AD_MULTIMENU_ADMIN_01', 	'設定:メニュー01');
define('_AD_MULTIMENU_ADMIN_02', 	'設定:メニュー02');
define('_AD_MULTIMENU_ADMIN_03', 	'設定:メニュー03');
define('_AD_MULTIMENU_ADMIN_04', 	'設定:メニュー04');
define('_AD_MULTIMENU_ADMIN_05', 	'設定:メニュー05');
define('_AD_MULTIMENU_ADMIN_06', 	'設定:メニュー06');
define('_AD_MULTIMENU_ADMIN_07', 	'設定:メニュー07');
define('_AD_MULTIMENU_ADMIN_08', 	'設定:メニュー08');
define('_AD_MULTIMENU_ADMIN_99', 	'設定:フロー');
define('_AD_MULTIMENU_EDITIMENU', 	'編集');
define('_AD_MULTIMENU_NEWIMENU', 	'新しいリンク');
define('_AD_MULTIMENU_NEW',		'リンク追加');
define('_AD_MULTIMENU_TITLE',		'タイトル');
define('_AD_MULTIMENU_HIDE',		'隠す');
define('_AD_MULTIMENU_TARGET',	'表示先(Target)');
define('_AD_MULTIMENU_GROUPS',	'グループ');
define('_AD_MULTIMENU_LINK',		'リンク');
define('_AD_MULTIMENU_OPERATION',	'機能');
define('_AD_MULTIMENU_UP',		'上へ');
define('_AD_MULTIMENU_DOWN',		'下へ');
define('_AD_MULTIMENU_TARG_SELF',	'self');
define('_AD_MULTIMENU_TARG_BLANK',	'blank');
define('_AD_MULTIMENU_TARG_PARENT',	'parent');
define('_AD_MULTIMENU_TARG_TOP',	'top');
define('_AD_MULTIMENU_SUREDELETE',	'このリンクを削除してもいいですか?');
define('_AD_MULTIMENU_UPDATED',	'データベースを更新しました!');
define('_AD_MULTIMENU_NOTUPDATED',	'データベースを更新できませんでした!');
define('_AD_MULTIMENU_SUBMIT', 	'実行');
if ( !defined('_AM_BADMIN') ) {
  define('_AM_BADMIN','ブロック管理');
  define('_AM_ADDBLOCK','新規ブロック作成');
  define('_AM_LISTBLOCK','全てのブロックを表示');
  define('_AM_SIDE','表示サイド');
  define('_AM_BLKDESC','ブロックの説明');
  define('_AM_TITLE','タイトル');
  define('_AM_WEIGHT','並び順');
  define('_AM_ACTION','操作');
  define('_AM_BLKTYPE','ブロックのタイプ');
  define('_AM_LEFT','左側');
  define('_AM_RIGHT','右側');
  define('_AM_CENTER','中央');
  define('_AM_VISIBLE','表示 / 非表示');
  define('_AM_POSCONTT','追加コンテンツを挿入する位置');
  define('_AM_ABOVEORG','元のコンテンツの上');
  define('_AM_AFTERORG','元のコンテンツの下');
  define('_AM_EDIT','編集');
  define('_AM_DELETE','削除');
  define('_AM_SBLEFT','サイドブロック - 左');
  define('_AM_SBRIGHT','サイドブロック - 右');
  define('_AM_CBLEFT','中央ブロック - 左');
  define('_AM_CBRIGHT','中央ブロック - 右');
  define('_AM_CBCENTER','中央ブロック - 中央');
  define('_AM_CONTENT','コンテンツ');
  define('_AM_OPTIONS','オプション');
  define('_AM_CTYPE','コンテンツのタイプ');
  define('_AM_HTML','HTMLタグ');
  define('_AM_PHP','PHPスクリプト');
  define('_AM_AFWSMILE','自動フォーマット（顔アイコン有効）');
  define('_AM_AFNOSMILE','自動フォーマット（顔アイコン無効）');
  define('_AM_SUBMIT','送信');
  define('_AM_CUSTOMHTML','カスタム（HTML）');
  define('_AM_CUSTOMPHP','カスタム（PHP）');
  define('_AM_CUSTOMSMILE','カスタム（顔アイコン無効）');
  define('_AM_CUSTOMNOSMILE','カスタム（顔アイコン無効）');
  define('_AM_DISPRIGHT','右ブロックのみ表示');
  define('_AM_SAVECHANGES','変更を保存');
  define('_AM_EDITBLOCK','ブロックを編集');
  define('_AM_SYSTEMCANT','システムブロックは削除できません');
  define('_AM_MODULECANT','このブロックを直接削除することはできません。先にこのブロックのモジュールを非アクティブにしてください');
  define('_AM_RUSUREDEL','<b>%s</b>ブロックを本当に削除してもいいですか？');
  define('_AM_NAME','名称');
  define('_AM_USEFULTAGS','使用可能なタグ');
  define('_AM_BLOCKTAG1','%s は %s を表示します');
  define('_AM_SVISIBLEIN', '表示する画面： %s ');//Show blocks visible in %s');
  define('_AM_TOPPAGE', 'トップページ');
  define('_AM_VISIBLEIN', '表示する画面');
  define('_AM_ALLPAGES', 'すべてのページ');
  define('_AM_TOPONLY', 'トップページのみ');
  define('_AM_ADVANCED', '高度な設定');
  define('_AM_BCACHETIME', 'キャッシュの寿命');
  define('_AM_BALIAS', 'エリアス名');
  define('_AM_CLONE', '複製');  // clone a block
  define('_AM_CLONEBLK', '複製'); // cloned block
  define('_AM_CLONEBLOCK', '複製ブロックの作成');
  define('_AM_NOTSELNG', "'%s' は選択されていません!"); // error message
  define('_AM_EDITTPL', 'テンプレートを編集');
  define('_AM_MODULE', 'モジュール');
  define('_AM_GROUP', 'グループ');
  define('_AM_UNASSIGNED', '未割り当て');
}
if ( !defined('_MD_AM_ADGS') ) {
  define('_MD_AM_DBUPDATED','データベースを更新しました');
  define('_MD_AM_ADGS','グループ管理');
  define('_MD_AM_BANS','バナー管理');
  define('_MD_AM_BKAD','ブロック管理');
  define('_MD_AM_MDAD','モジュール管理');
  define('_MD_AM_SMLS','顔アイコン設定');
  define('_MD_AM_RANK','ユーザランキング設定');
  define('_MD_AM_USER','ユーザ管理');
  define('_MD_AM_FINDUSER', 'ユーザ検索');
  define('_MD_AM_PREF','一般設定');
  define('_MD_AM_VRSN','バージョン');
  define('_MD_AM_MLUS', 'ユーザ宛にメール送信');
  define('_MD_AM_IMAGES', 'イメージ・マネジャー');
  define('_MD_AM_AVATARS', 'アバター・マネジャー');
  define('_MD_AM_TPLSETS', 'テンプレート');
  define('_MD_AM_COMMENTS', 'コメント');
}
if ( !defined('_AM_ACTIVERIGHTS') ) {
  define('_AM_ACTIVERIGHTS','モジュール管理者権限');
  define('_AM_ACCESSRIGHTS','モジュールアクセス権限');
}
?>