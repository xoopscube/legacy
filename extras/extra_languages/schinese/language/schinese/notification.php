<?php
// $Id$

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', '提醒选项');
define ('_NOT_UPDATENOW', '马上更新');
define ('_NOT_UPDATEOPTIONS', '更新提醒项目');

define ('_NOT_CANCEL', '取消');
define ('_NOT_CLEAR', '清除');
define ('_NOT_DELETE', '删除');
define ('_NOT_CHECKALL', '全选');
define ('_NOT_MODULE', '模块');
define ('_NOT_CATEGORY', '分类');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', '名称');
define ('_NOT_EVENT', '事件');
define ('_NOT_EVENTS', '事件');
define ('_NOT_ACTIVENOTIFICATIONS', '启动时提醒项目');
define ('_NOT_NAMENOTAVAILABLE', '无此名称');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', '该项目名称不存在');
define ('_NOT_ITEMTYPENOTAVAILABLE', '该项目类型不存在');
define ('_NOT_ITEMURLNOTAVAILABLE', 'URL不可用');
define ('_NOT_DELETINGNOTIFICATIONS', '删除提醒项目');
define ('_NOT_DELETESUCCESS', '删除完成');
define ('_NOT_UPDATEOK', '提醒项目已更新');
define ('_NOT_NOTIFICATIONMETHODIS', '提醒方式');
define ('_NOT_EMAIL', 'email');
define ('_NOT_PM', '个人信息');
define ('_NOT_DISABLE', '停用');
define ('_NOT_CHANGE', '变更');
define ('_NOT_RUSUREDEL', '您确认要删除此项提醒通知吗？');
define ('_NOT_NOACCESS', '您无权限进入本页');

// Text for module config options

define ('_NOT_ENABLE', '开启');
define ('_NOT_NOTIFICATION', '提醒');

define ('_NOT_CONFIG_ENABLED', '开启提醒功能');
define ('_NOT_CONFIG_ENABLEDDSC', '这个模块是让用户开启一些事件的提醒项目，选择－是－来开启项目');

define ('_NOT_CONFIG_EVENTS', '开启特定事件');
define ('_NOT_CONFIG_EVENTSDSC', '选择要开放给用户选择的提醒项目');

define ('_NOT_CONFIG_ENABLE', '开启提醒');
define ('_NOT_CONFIG_ENABLEDSC', '这个模块可以开启以便在模型事件发生时发出通知提醒，用户可以选择1.例如接收通知的用户出现在区域(区域模式)，2.出现在某个模块(模块模式)，或是3.两者。如果使用区域方式提醒项目的区域必须是开启的');
define ('_NOT_CONFIG_DISABLE', '关闭提醒');
define ('_NOT_CONFIG_ENABLEBLOCK', '仅区域方式可用');
define ('_NOT_CONFIG_ENABLEINLINE', '仅模块方式可用');
define ('_NOT_CONFIG_ENABLEBOTH', '开放(两种方式)');

// For notification about comment events

define ('_NOT_COMMENT_NOTIFY', '回应评论通知');
define ('_NOT_COMMENT_NOTIFYCAP', '在这个项目有新回应评论时通知我');
define ('_NOT_COMMENT_NOTIFYDSC', '当有新的回应评论时通知我');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}自动提醒通知] {X_MODULE} 有 {X_ITEM_TYPE}的回应评论增加');

define ('_NOT_COMMENTSUBMIT_NOTIFY', '回应评论发布通知');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', '当有新的回应评论被发布时通知我');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', '接收通知');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}自动提醒通知] {X_MODULE} 模块有 {X_ITEM_TYPE}的回应评论发布');

// For notification bookmark feature
// (Not really notification, but easy to do with this module)

define ('_NOT_BOOKMARK_NOTIFY', '标记');
define ('_NOT_BOOKMARK_NOTIFYCAP', '标记这个项目(不发通知)');
define ('_NOT_BOOKMARK_NOTIFYDSC', '追踪这个项目但不接收通知');

// For user profile
// FIXME: These should be reworded a little...

define ('_NOT_NOTIFYMETHOD', '通知方式：当您追踪的项目，如讨论区有新的事件，是否要接收更新通知？');
define ('_NOT_METHOD_EMAIL', 'E-MAIL (您个人资料中的E-MAIL)');
define ('_NOT_METHOD_PM', '个人信息');
define ('_NOT_METHOD_DISABLE', '暂时关闭');

define ('_NOT_NOTIFYMODE', '预设提醒模式');
define ('_NOT_MODE_SENDALWAYS', '所有选择的更新都通知我');
define ('_NOT_MODE_SENDONCE', '只通知一次');
define ('_NOT_MODE_SENDONCEPERLOGIN', '通知我一次后关闭，直到我下次登录');

define('_NOT_NOTHINGTODELETE','未删除任何项目');
?>
