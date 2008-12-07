<?php
// $Id: comment.php,v 1.1 2007/05/15 02:35:08 minahito Exp $

if (!defined('_CM_TITLE')) {

define('_CM_TITLE','表題');
define('_CM_MESSAGE','コメント');
define('_CM_DOSMILEY','顔アイコンを有効にする');
define('_CM_DOHTML','HTMLタグを有効にする');
define('_CM_DOAUTOWRAP','改行を自動挿入する');
define('_CM_DOXCODE','XOOPSコードを有効にする');
define('_CM_REFRESH','更新');
define('_CM_PENDING','承認待ち');
define('_CM_HIDDEN','非表示');
define('_CM_ACTIVE','アクティブ');
define('_CM_STATUS','ステータス');
define('_CM_POSTCOMMENT','投稿する');
define('_CM_REPLIES','返信');
define('_CM_PARENT','親コメント');
define('_CM_TOP','上へ');
define('_CM_BOTTOM','下へ');
define('_CM_ONLINE','オンライン');
define('_CM_POSTED','投稿日時'); // Posted date
define('_CM_UPDATED', '更新日時');
define('_CM_THREAD','スレッド');
define('_CM_POSTER','投稿者');
define('_CM_JOINED','登録日');
define('_CM_POSTS','投稿数');
define('_CM_FROM','居住地');
define('_CM_COMDELETED', 'コメントを削除しました。');
define('_CM_COMDELETENG', 'コメントを削除できませんでした。');
define('_CM_DELETESELECT' , 'コメントの削除方法を選択してください。');
define('_CM_DELETEONE' , 'このコメントだけ削除する');
define('_CM_DELETEALL', 'このコメントに対する返信も全て削除する');
define('_CM_THANKSPOST', '投稿を受け付けました。');
define('_CM_NOTICE', '投稿された内容の著作権はコメントの投稿者に帰属します。');
define('_CM_COMRULES','コメント投稿に関するルール');
define('_CM_COMAPPROVEALL','コメントに承認は必要ない');
define('_CM_COMAPPROVEUSER','登録ユーザ以外のコメントは承認が必要');
define('_CM_COMAPPROVEADMIN','コメントは全て承認が必要');
define('_CM_COMANONPOST','匿名によるコメント投稿を許可しますか？');
define('_CM_COMNOCOM','コメント機能を無効にする');

}

?>