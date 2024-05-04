<?php
/**
 * legacy_notification.php
 * XOOPS2
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This file has been modified for Legacy from XOOPS2 System module block
 */

function b_legacy_notification_show()
{
    global $xoopsConfig, $xoopsUser, $xoopsModule;
    include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

    $root =& XCube_Root::getSingleton();
    $root->mLanguageManager->loadPageTypeMessageCatalog('notification');

    // Notification must be enabled, and user must be logged in
    if (empty($xoopsUser) || !notificationEnabled('block')) {
        return false; // do not display block
    }
    $notification_handler =& xoops_gethandler('notification');
    // Now build a nested associative array of info to pass
    // to the block template.
    $block = [];
    $categories =& notificationSubscribableCategoryInfo();
    if (empty($categories)) {
        return false;
    }
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
            $subscribed = in_array($event['name'], $subscribed_events, true) ? 1 : 0;
            $section['events'][$event['name']] = ['name' => $event['name'], 'title' => $event['title'], 'caption' => $event['caption'], 'description' => $event['description'], 'subscribed' =>$subscribed];
        }
        $block['categories'][$category['name']] = $section;
    }
    // Additional form data
    $block['target_page'] = 'notification_update.php';
    // FIXME: better or more standardized way to do this?
    $script_url = explode('/', xoops_getenv('PHP_SELF'));
    $script_name = $script_url[count($script_url)-1];
    $block['redirect_script'] = $script_name;
    $block['submit_button'] = _NOT_UPDATENOW;
    return $block;
}
