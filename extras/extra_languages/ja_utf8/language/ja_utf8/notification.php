<?php
// $Id: notification.php,v 1.1 2007/05/25 06:05:04 minahito Exp $

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', 'イベントの選択');
define ('_NOT_UPDATENOW', '今すぐ更新');
define ('_NOT_UPDATEOPTIONS', 'イベントの更新');

define ('_NOT_CANCEL', '中止');
define ('_NOT_CLEAR', 'クリア');
define ('_NOT_DELETE', '削除');
define ('_NOT_CHECKALL', '全てチェック');
define ('_NOT_MODULE', 'モジュール');
define ('_NOT_CATEGORY', 'カテゴリ');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', '名称');
define ('_NOT_EVENT', 'イベント');
define ('_NOT_EVENTS', 'イベント');
define ('_NOT_ACTIVENOTIFICATIONS', '選択可能なイベント');
define ('_NOT_NAMENOTAVAILABLE', '無題');
define ('_NOT_ITEMNAMENOTAVAILABLE', '項目名が無効です');
define ('_NOT_ITEMTYPENOTAVAILABLE', '項目タイプが無効です');
define ('_NOT_ITEMURLNOTAVAILABLE', '項目URLが無効です');
define ('_NOT_DELETINGNOTIFICATIONS', '選択イベントの削除');
define ('_NOT_DELETESUCCESS', '選択されたイベントを削除しました');
define ('_NOT_UPDATEOK', 'イベントを更新しました');
define ('_NOT_NOTIFICATIONMETHODIS', '通知方法：');
define ('_NOT_EMAIL', 'メール');
define ('_NOT_PM', 'プライベート・メッセージ');
define ('_NOT_DISABLE', '無効にする');
define ('_NOT_CHANGE', '変更');
define ('_NOT_RUSUREDEL', '選択したイベントを削除してもいいですか？');
define ('_NOT_NOACCESS', 'このページにアクセスする権限がありません。');

// Text for module config options

define ('_NOT_ENABLE', '有効にする');
define ('_NOT_NOTIFICATION', 'イベント通知機能');

define ('_NOT_CONFIG_ENABLED', 'イベント通知機能の設定');
define ('_NOT_CONFIG_ENABLEDDSC', 'このモジュールでは、ある特定のイベントが発生した際に、当該イベント購読者に対し通知メッセージが送られるように設定できます。この機能を有効にするには「はい」を選択してください。');

define ('_NOT_CONFIG_EVENTS', '特定イベントを有効にする');
define ('_NOT_CONFIG_EVENTSDSC', 'ユーザが選択可能なイベントを設定してください。');

define ('_NOT_CONFIG_ENABLE', 'イベント通知機能の設定');
define ('_NOT_CONFIG_ENABLEDSC', 'このモジュールでは、ある特定のイベントが発生した際に、当該イベント購読者に対し通知メッセージが送られるように設定できます。この機能を有効にするための形式を選択してください。');
define ('_NOT_CONFIG_DISABLE', 'この機能を無効にする');
define ('_NOT_CONFIG_ENABLEBLOCK', 'イベント選択オプションをブロックに表示する');
define ('_NOT_CONFIG_ENABLEINLINE', 'イベント選択オプションをメインコンテンツ下部に表示する');
define ('_NOT_CONFIG_ENABLEBOTH', 'イベント選択オプションをブロックおよびメインコンテンツ下部の両方に表示する');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', 'コメントが追加されました');
define ('_NOT_COMMENT_NOTIFYCAP', 'このページにコメントが追加された際に通知する');
define ('_NOT_COMMENT_NOTIFYDSC', '');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_MODULE}] 「{X_ITEM_TYPE}」に対してコメントが追加されました （自動通知）');

define ('_NOT_COMMENTSUBMIT_NOTIFY', '新規コメントの投稿がありました');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'このページに承認が必要なコメントが投稿された際に通知する');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', '');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_MODULE}] 「{X_ITEM_TYPE}」に対して新規コメントの投稿がありました （自動通知）');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', 'ブックマーク');
define ('_NOT_BOOKMARK_NOTIFYCAP', 'このページをブックマークする');
define ('_NOT_BOOKMARK_NOTIFYDSC', 'このページをブックマークします。通知メッセージは送られません。');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', 'イベント更新通知メッセージの受取方法');
define ('_NOT_METHOD_EMAIL', 'メール');
define ('_NOT_METHOD_PM', 'プライベート・メッセージ');
define ('_NOT_METHOD_DISABLE', '一時的に中止');

define ('_NOT_NOTIFYMODE', 'イベント通知のタイミング');
define ('_NOT_MODE_SENDALWAYS', 'イベント更新時に必ず通知する');
define ('_NOT_MODE_SENDONCE', '一度だけ通知する');
define ('_NOT_MODE_SENDONCEPERLOGIN', '一度通知した後、再度ログインするまで通知しない');

define('_NOT_NOTHINGTODELETE', '削除するイベントがありません');

?>
