<?php
// $Id: notification.php,v 1.1 2008/03/09 02:26:04 minahito Exp $

// RMV-NOTIFY

// Text for various templates...


define ('_NOT_NOTIFICATIONOPTIONS', '提醒選項');
define ('_NOT_UPDATENOW', '馬上更新');
define ('_NOT_UPDATEOPTIONS', '更新提醒項目');

define ('_NOT_CANCEL', '取消');
define ('_NOT_CLEAR', '清除');
define ('_NOT_DELETE', '刪除');
define ('_NOT_CHECKALL', '全選');
define ('_NOT_MODULE', '模組');
define ('_NOT_CATEGORY', '分類');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', '名稱');
define ('_NOT_EVENT', '事件');
define ('_NOT_EVENTS', '事件');
define ('_NOT_ACTIVENOTIFICATIONS', '啟動提醒項目');
define ('_NOT_NAMENOTAVAILABLE', '無此名稱');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', '該項目名稱不存在');
define ('_NOT_ITEMTYPENOTAVAILABLE', '該項目型態不存在');
define ('_NOT_ITEMURLNOTAVAILABLE', '該項目連結不存在');
define ('_NOT_DELETINGNOTIFICATIONS', '刪除提醒項目');
define ('_NOT_DELETESUCCESS', '刪除完成.');
define ('_NOT_UPDATEOK', '提醒項目已更新');
define ('_NOT_NOTIFICATIONMETHODIS', '提醒方式');
define ('_NOT_EMAIL', 'email');
define ('_NOT_PM', '私人訊息');
define ('_NOT_DISABLE', '停用');
define ('_NOT_CHANGE', '變更');
define ('_NOT_RUSUREDEL', '您確定要刪除此項提醒通知嗎?');
define ('_NOT_NOACCESS', '您無權限進入本頁.');

// Text for module config options

define ('_NOT_ENABLE', '開放');
define ('_NOT_NOTIFICATION', '提醒');

define ('_NOT_CONFIG_ENABLED', '開放提醒');
define ('_NOT_CONFIG_ENABLEDDSC', '這個模組是讓使用者可以開啟一些事件的提醒項目，選擇-是-來啟用.');

define ('_NOT_CONFIG_EVENTS', '開放特殊事件');
define ('_NOT_CONFIG_EVENTSDSC', '選擇要開放給使用者選擇提醒通知的項目.');

define ('_NOT_CONFIG_ENABLE', '開放提醒');
define ('_NOT_CONFIG_ENABLEDSC', '這個模組可以開放在某些事件發生時發出通知提醒，使用者可選擇1.假如接受通知的使用者出現在區塊  (區塊模式), 2.出現在某個模組 (模組模式), 或是3.兩者. 如果使用區塊方式這個提醒項目的區塊必須是開放的.');
define ('_NOT_CONFIG_DISABLE', '關閉提醒');
define ('_NOT_CONFIG_ENABLEBLOCK', '僅區塊方式可用');
define ('_NOT_CONFIG_ENABLEINLINE', '僅開放模組方式');
define ('_NOT_CONFIG_ENABLEBOTH', '開放 (兩種方式)');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', '回應評論通知');
define ('_NOT_COMMENT_NOTIFYCAP', '在這個項目有新回應評論時通知我.');
define ('_NOT_COMMENT_NOTIFYDSC', '當有新的回應評論時通知我.');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME} 自動提醒通知 ] {X_MODULE} 有 {X_ITEM_TYPE} 的回應評論增加');

define ('_NOT_COMMENTSUBMIT_NOTIFY', '回應評論發佈通知');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', '當有新的回應評論被發佈時通知我.');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', '接受通知.');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME 自動提醒通知] {X_MODULE}模組有 {X_ITEM_TYPE} 的回應評論發佈');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', '標記');
define ('_NOT_BOOKMARK_NOTIFYCAP', '標記這個項目(不發通知).');
define ('_NOT_BOOKMARK_NOTIFYDSC', '追蹤這個項目但不接收通知.');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', '通知方式: 當您追蹤的項目，如討論區有新的事件，是否要接受更新通知，?');
define ('_NOT_METHOD_EMAIL', 'Email (您個人資料中的 email)');
define ('_NOT_METHOD_PM', '私人訊息');
define ('_NOT_METHOD_DISABLE', '暫時關閉');

define ('_NOT_NOTIFYMODE', '預設提醒模式');
define ('_NOT_MODE_SENDALWAYS', '所有選擇的更新都通知我');
define ('_NOT_MODE_SENDONCE', '只通知一次');
define ('_NOT_MODE_SENDONCEPERLOGIN', '通知我一次後關閉，直到我下次登入');

define('_NOT_NOTHINGTODELETE', '未刪除任何項目');
?>
