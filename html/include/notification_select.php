<?php
/**
 * Notification select
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

include_once XOOPS_ROOT_PATH.'/include/notification_constants.php';
include_once XOOPS_ROOT_PATH.'/include/notification_functions.php';

$xoops_notification = [];
$xoops_notification['show'] = isset($xoopsModule) && is_object($xoopsUser) && notificationEnabled('inline') ? 1 : 0;
if ($xoops_notification['show']) {
    $root =& XCube_Root::getSingleton();
    $root->mLanguageManager->loadPageTypeMessageCatalog('notification');
    $categories =& notificationSubscribableCategoryInfo();
    $event_count = 0;
    if (!empty($categories)) {
        $notification_handler =& xoops_gethandler('notification');
        foreach ($categories as $category) {
            $section['name'] = $category['name'];
            $section['title'] = $category['title'];
            $section['description'] = $category['description'];
            $section['itemid'] = $category['item_id'];
            $section['events'] = [];
            $subscribed_events =& $notification_handler->getSubscribedEvents($category['name'], $category['item_id'], $xoopsModule->getVar('mid'), $xoopsUser->getVar('uid'));
            foreach (notificationEvents($category['name'], true) as $event) {
                if (!empty($event['admin_only']) && !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                    continue;
                }
                if (!empty($event['invisible'])) {
                    continue;
                }
                $subscribed = in_array($event['name'], $subscribed_events, true) ? 1 : 0;
                $section['events'][$event['name']] = ['name' => $event['name'], 'title' => $event['title'], 'caption' => $event['caption'], 'description' => $event['description'], 'subscribed' =>$subscribed];
                $event_count ++;
            }
            $xoops_notification['categories'][$category['name']] = $section;
        }
        $xoops_notification['target_page'] = 'notification_update.php';
        $xoops_notification['redirect_script'] = xoops_getenv('PHP_SELF');
        $xoopsTpl->assign(
            ['lang_activenotifications' => _NOT_ACTIVENOTIFICATIONS, 'lang_notificationoptions' => _NOT_NOTIFICATIONOPTIONS, 'lang_updateoptions' => _NOT_UPDATEOPTIONS, 'lang_updatenow' => _NOT_UPDATENOW, 'lang_category' => _NOT_CATEGORY, 'lang_event' => _NOT_EVENT, 'lang_events' => _NOT_EVENTS, 'lang_checkall' => _NOT_CHECKALL, 'lang_notificationmethodis' => _NOT_NOTIFICATIONMETHODIS, 'lang_change' => _NOT_CHANGE, 'editprofile_url' => XOOPS_URL . '/edituser.php?uid=' . $xoopsUser->getVar('uid')]
        );
        switch ($xoopsUser->getVar('notify_method')) {
        case XOOPS_NOTIFICATION_METHOD_DISABLE:
            $xoopsTpl->assign('user_method', _NOT_DISABLE);
            break;
        case XOOPS_NOTIFICATION_METHOD_PM:
            $xoopsTpl->assign('user_method', _NOT_PM);
            break;
        case XOOPS_NOTIFICATION_METHOD_EMAIL:
            $xoopsTpl->assign('user_method', _NOT_EMAIL);
            break;
        }
    } else {
        $xoops_notification['show'] = 0;
    }
    if (0 == $event_count) {
        $xoops_notification['show'] = 0;
    }
}

if (isset($xoopsTpl)&&is_object($xoopsTpl)) {
    $xoopsTpl->assign('xoops_notification', $xoops_notification);
}
