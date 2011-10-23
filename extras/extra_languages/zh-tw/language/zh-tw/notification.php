<?php
// $Id$

// RMV-NOTIFY

// Text for various templates...

define('_NOT_NOTIFICATIONOPTIONS', '通知選項');
define('_NOT_UPDATENOW', '立即更新');
define('_NOT_UPDATEOPTIONS', '更新通知選項');

define('_NOT_CANCEL', '取消');
define('_NOT_CLEAR', '清除');
define('_NOT_DELETE', '刪除');
define('_NOT_CHECKALL', '檢查所有的');
define('_NOT_MODULE', '模組');
define('_NOT_CATEGORY', '類別');
define('_NOT_ITEMID', '代碼');
define('_NOT_ITEMNAME', '名稱');
define('_NOT_EVENT', '事件');
define('_NOT_EVENTS', '事件');
define('_NOT_ACTIVENOTIFICATIONS', '使用中的通知');
define('_NOT_NAMENOTAVAILABLE', '名稱無法使用');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define('_NOT_ITEMNAMENOTAVAILABLE', '項目名稱無法使用');
define('_NOT_ITEMTYPENOTAVAILABLE', '項目形態無法使用');
define('_NOT_ITEMURLNOTAVAILABLE', '項目網址無法使用');
define('_NOT_DELETINGNOTIFICATIONS', '刪除通知中');
define('_NOT_DELETESUCCESS', '個通知已經順利的刪除.');
define('_NOT_UPDATEOK', '通知選項已更新');
define('_NOT_NOTIFICATIONMETHODIS', '通知方法是');
define('_NOT_EMAIL', '電子郵件');
define('_NOT_PM', '私人訊息');
define('_NOT_DISABLE', '關閉');
define('_NOT_CHANGE', '修改');
define('_NOT_RUSUREDEL', '您確定要刪除這些通知選項嗎?');
define('_NOT_NOACCESS', '您沒有權限取用這個頁面.');

// Text for module config options

define('_NOT_ENABLE', '啟用');
define('_NOT_NOTIFICATION', '通知');

define('_NOT_CONFIG_ENABLED', '啟用通知');
define('_NOT_CONFIG_ENABLEDDSC', '這個模組讓使用者在某一事件發生時，可以接受通知。請選 "是" 來啟用這個功能。');

define('_NOT_CONFIG_EVENTS', '啟用特定的事件');
define('_NOT_CONFIG_EVENTSDSC', '選擇通知事件供您的使用者訂閱。');

define('_NOT_CONFIG_ENABLE', '啟用通知');
define('_NOT_CONFIG_ENABLEDSC', '這個模組讓使用者在某一事件發生時，可以接受通知。請選擇在區塊中呈現使用者通知選項（區塊-形式），模組中呈現（內列-形式），或是二者皆可。若是區塊-形式的通知，通知選項區塊在這個模組中就應該打開。');
define('_NOT_CONFIG_DISABLE', '關閉通知');
define('_NOT_CONFIG_ENABLEBLOCK', '只啟用區塊-樣式');
define('_NOT_CONFIG_ENABLEINLINE', '只啟用內列-樣式');
define('_NOT_CONFIG_ENABLEBOTH', '啟用通知（二種形式）');

// For notification about comment events

define('_NOT_COMMENT_NOTIFY', '有評論加入');
define('_NOT_COMMENT_NOTIFYCAP', '當有針對這個項目的新評論張貼時通知我.');
define('_NOT_COMMENT_NOTIFYDSC', '針對這個項目無論何時只要有一個新的評論張貼(或已通過審核),均接受通知.');
define('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} 自動通知: 有新評論加入 {X_ITEM_TYPE}');

define('_NOT_COMMENTSUBMIT_NOTIFY', '評論已送出');
define('_NOT_COMMENTSUBMIT_NOTIFYCAP', '針對這個項目當有新的評論送出(正等待審核),就通知我.');
define('_NOT_COMMENTSUBMIT_NOTIFYDSC', '針對這個項目無論何時只要有一個新的評論送出(正等待審核),均接受通知.');
define('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} 自動通知: 有新評論送出給 {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define('_NOT_BOOKMARK_NOTIFY', '書籤');
define('_NOT_BOOKMARK_NOTIFYCAP', '將這個項目加入書籤中 (沒有通知).');
define('_NOT_BOOKMARK_NOTIFYDSC', '持續追蹤這個項目,但不接收任何事件通知.');

// For user profile
// FIXME: These should be reworded a little...

define('_NOT_NOTIFYMETHOD', '例如，當您監控一個討論區時，您喜歡什麼方式來接收更新的通知？');
define('_NOT_METHOD_EMAIL', '電子郵件 (在我設定的資料中所使用位址)');
define('_NOT_METHOD_PM', '私人訊息');
define('_NOT_METHOD_DISABLE', '暫時關閉');

define('_NOT_NOTIFYMODE', '預設通知模式');
define('_NOT_MODE_SENDALWAYS', '所有選擇的部份更新時就通知我');
define('_NOT_MODE_SENDONCE', '只通知我一次就好');
define('_NOT_MODE_SENDONCEPERLOGIN', '只通知我一次，然後關閉這項功能直到我再次登入');

?>
