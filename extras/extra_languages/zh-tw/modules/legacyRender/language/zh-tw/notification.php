<?php
// $Id$
// XOOPS zh_TW.Big5 中文翻譯檔
// 本中文翻譯檔乃直接由英文版翻譯過來,遵照GPL授權協議. 
// 除了GPL的規範之外,沒有其它限制
// 翻譯者: OLS3 (ols3@lxer.idv.tw)
// 本中文翻譯檔採中英對照(上中下英),以方便您做比對,
// 若您覺得譯得不妥,請告知我們,以便改進.
//
// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', '通知選項');
//define ('_NOT_NOTIFICATIONOPTIONS', 'Notification Options');

define ('_NOT_UPDATENOW', '現在就更新');
//define ('_NOT_UPDATENOW', 'Update Now');

define ('_NOT_UPDATEOPTIONS', '更新通知選項');
//define ('_NOT_UPDATEOPTIONS', 'Update Notification Options');

define ('_NOT_CLEAR', '清除');
//define ('_NOT_CLEAR', 'Clear');

define ('_NOT_CHECKALL', '檢查所有的');
//define ('_NOT_CHECKALL', 'Check All');

define ('_NOT_MODULE', '模組');
//define ('_NOT_MODULE', 'Module');

define ('_NOT_CATEGORY', '類別');
//define ('_NOT_CATEGORY', 'Category');

define ('_NOT_ITEMID', '代碼');
//define ('_NOT_ITEMID', 'ID');

define ('_NOT_ITEMNAME', '名稱');
//define ('_NOT_ITEMNAME', 'Name');

define ('_NOT_EVENT', '事件');
//define ('_NOT_EVENT', 'Event');

define ('_NOT_EVENTS', '事件');
//define ('_NOT_EVENTS', 'Events');

define ('_NOT_ACTIVENOTIFICATIONS', '使用中的通知');
//define ('_NOT_ACTIVENOTIFICATIONS', 'Active Notifications');

define ('_NOT_NAMENOTAVAILABLE', '名稱無法使用');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
//define ('_NOT_NAMENOTAVAILABLE', 'Name Not Available');

define ('_NOT_ITEMNAMENOTAVAILABLE', '項目名稱無法使用');
//define ('_NOT_ITEMNAMENOTAVAILABLE', 'Item Name Not Available');

define ('_NOT_ITEMTYPENOTAVAILABLE', '項目形態無法使用');
//define ('_NOT_ITEMTYPENOTAVAILABLE', 'Item Type Not Available');

define ('_NOT_ITEMURLNOTAVAILABLE', '項目URL無法使用');
//define ('_NOT_ITEMURLNOTAVAILABLE', 'Item URL Not Available');

define ('_NOT_DELETINGNOTIFICATIONS', '刪除通知中');
//define ('_NOT_DELETINGNOTIFICATIONS', 'Deleting Notifications');

define ('_NOT_DELETESUCCESS', '個通知已成功地刪除.');
//define ('_NOT_DELETESUCCESS', 'Notification(s) deleted successfully.');

define ('_NOT_UPDATEOK', '通知選項已更新');
//define ('_NOT_UPDATEOK', 'Notification options updated');

define ('_NOT_NOTIFICATIONMETHODIS', '通知方法是');
//define ('_NOT_NOTIFICATIONMETHODIS', 'Notification method is');

define ('_NOT_EMAIL', '電子郵件');
//define ('_NOT_EMAIL', 'email');

define ('_NOT_PM', '私人訊息');
//define ('_NOT_PM', 'private message');

define ('_NOT_DISABLE', '關閉');
//define ('_NOT_DISABLE', 'disabled');

define ('_NOT_CHANGE', '修改');
//define ('_NOT_CHANGE', 'Change');

define ('_NOT_NOACCESS', '您沒有權限取用這個頁面.');
//define ('_NOT_NOACCESS', 'You do not have permission to access this page.');

// Text for module config options

define ('_NOT_ENABLE', '啟用');
//define ('_NOT_ENABLE', 'Enable');

define ('_NOT_NOTIFICATION', '通知');
//define ('_NOT_NOTIFICATION', 'Notification');

define ('_NOT_CONFIG_ENABLED', '啟用通知');
//define ('_NOT_CONFIG_ENABLED', 'Enable Notification');

define ('_NOT_CONFIG_ENABLEDDSC', '這個模組允許使用者在某一事件發生時,可以接受通知. 請選 "yes" 來啟用這個功能.');
//define ('_NOT_CONFIG_ENABLEDDSC', 'This module allows users to select to be notified when certain events occur.  Choose "yes" to enable this feature.');

define ('_NOT_CONFIG_EVENTS', '啟用特定的事件');
//define ('_NOT_CONFIG_EVENTS', 'Enable Specific Events');

define ('_NOT_CONFIG_EVENTSDSC', '選擇通知事件供您的使用者訂用.');
//define ('_NOT_CONFIG_EVENTSDSC', 'Select which notification events to which your users may subscribe.');

define ('_NOT_CONFIG_ENABLE', '啟用通知');
//define ('_NOT_CONFIG_ENABLE', 'Enable Notification');

define ('_NOT_CONFIG_ENABLEDSC', '這個模組允許使用者在某一事件發生時,可以接受通知. 請選擇在區塊中呈現使用者通知選項(區塊-形式), 模組中呈現 (內列-形式), 或是二者皆可.  若是區塊-形式的通知, 通知選項區塊在這個模組中就應該打開.');
//define ('_NOT_CONFIG_ENABLEDSC', 'This module allows users to be notified when certain events occur.  Select if users should be presented with notification options in a Block (Block-style), within the module (Inline-style), or both.  For block-style notification, the Notification Options block must be enabled for this module.');

define ('_NOT_CONFIG_DISABLE', '關閉通知');
//define ('_NOT_CONFIG_DISABLE', 'Disable Notification');

define ('_NOT_CONFIG_ENABLEBLOCK', '只啟用區塊-形式');
//define ('_NOT_CONFIG_ENABLEBLOCK', 'Enable only Block-style');

define ('_NOT_CONFIG_ENABLEINLINE', '只啟用內列-形式');
//define ('_NOT_CONFIG_ENABLEINLINE', 'Enable only Inline-style');

define ('_NOT_CONFIG_ENABLEBOTH', '啟用通知 (二種形式)');
//define ('_NOT_CONFIG_ENABLEBOTH', 'Enable Notification (both styles)');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', '有評論加入');
//define ('_NOT_COMMENT_NOTIFY', 'Comment Added');

define ('_NOT_COMMENT_NOTIFYCAP', '當有針對這個項目的新評論張貼時通知我.');
//define ('_NOT_COMMENT_NOTIFYCAP', 'Notify me when a new comment is posted for this item.');

define ('_NOT_COMMENT_NOTIFYDSC', '針對這個項目無論何時只要有一個新的評論張貼(或已通過審核),均接受通知.');
//define ('_NOT_COMMENT_NOTIFYDSC', 'Receive notification whenever a new comment is posted (or approved) for this item.');

define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify: Comment added to {X_ITEM_TYPE}');
//define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} 自動通知: 有新評論加入 {X_ITEM_TYPE}');

define ('_NOT_COMMENTSUBMIT_NOTIFY', '評論已送出');
//define ('_NOT_COMMENTSUBMIT_NOTIFY', 'Comment Submitted');

define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', '針對這個項目當有新的評論送出(正等待審核),就通知我.');
//define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'Notify me when a new comment is submitted (awaiting approval) for this item.');

define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', '針對這個項目無論何時只要有一個新的評論送出(正等待審核),均接受通知.');
//define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', 'Receive notification whenever a new comment is submitted (awaiting approval) for this item.');

define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify: Comment submitted for {X_ITEM_TYPE}');
//define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} 自動通知: 有新評論送出給 {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', '書籤');
//define ('_NOT_BOOKMARK_NOTIFY', 'Bookmark');

define ('_NOT_BOOKMARK_NOTIFYCAP', '將這個項目加入書籤中 (沒有通知).');
//define ('_NOT_BOOKMARK_NOTIFYCAP', 'Bookmark this item (no notification).');

define ('_NOT_BOOKMARK_NOTIFYDSC', '持續追蹤這個項目,但不接收任何事件通知.');
//define ('_NOT_BOOKMARK_NOTIFYDSC', 'Keep track of this item without receiving any event notifications.');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', '通知方法: 例如, 當您監控一個討論區時, 您喜歡什麼方式來接收更新的通知?');
//define ('_NOT_NOTIFYMETHOD', 'Notification Method: When you monitor e.g. a forum, how would you like to receive notifications of updates?');

define ('_NOT_METHOD_EMAIL', '電子郵件 (在我的設定資料中的使用位址)');
//define ('_NOT_METHOD_EMAIL', 'Email (use address in my profile)');

define ('_NOT_METHOD_PM', '私人訊息');
//define ('_NOT_METHOD_PM', 'Private Message');

define ('_NOT_METHOD_DISABLE', '暫時關閉');
//define ('_NOT_METHOD_DISABLE', 'Temporarily Disable');

define ('_NOT_NOTIFYMODE', '預設通知模式');
//define ('_NOT_NOTIFYMODE', 'Default Notification Mode');

define ('_NOT_MODE_SENDALWAYS', '所有選擇的部份更新時就通知我');
//define ('_NOT_MODE_SENDALWAYS', 'Notify me of all selected updates');

define ('_NOT_MODE_SENDONCE', '只通知我一次就好');
//define ('_NOT_MODE_SENDONCE', 'Notify me only once');

define ('_NOT_MODE_SENDONCEPERLOGIN', '只通知我一次,然後關閉這項功能直到我再次登入');
//define ('_NOT_MODE_SENDONCEPERLOGIN', 'Notify me once then disable until I log in again');

?>
