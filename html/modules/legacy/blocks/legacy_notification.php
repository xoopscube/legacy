<?php
/**
 *
 * @package XOOPS2
 * @version $Id: legacy_notification.php,v 1.3 2008/09/25 15:12:15 kilica Exp $
 * @copyright Copyright (c) 2000 XOOPS.org  <http://www.xoops.org/>
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
//  This file has been modified for Legacy from XOOPS2 System module block   //
// ------------------------------------------------------------------------- //

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
    // Now build the a nested associative array of info to pass
    // to the block template.
    $block = array();
    $categories =& notificationSubscribableCategoryInfo();
    if (empty($categories)) {
        return false;
    }
    foreach ($categories as $category) {
        $section['name'] = $category['name'];
        $section['title'] = $category['title'];
        $section['description'] = $category['description'];
        $section['itemid'] = $category['item_id'];
        $section['events'] = array();
        $subscribed_events =& $notification_handler->getSubscribedEvents ($category['name'], $category['item_id'], $xoopsModule->getVar('mid'), $xoopsUser->getVar('uid'));
        foreach (notificationEvents($category['name'], true) as $event) {
            if (!empty($event['admin_only']) && !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                continue;
            }
            $subscribed = in_array($event['name'], $subscribed_events) ? 1 : 0;
            $section['events'][$event['name']] = array ('name'=>$event['name'], 'title'=>$event['title'], 'caption'=>$event['caption'], 'description'=>$event['description'], 'subscribed'=>$subscribed);
        }
        $block['categories'][$category['name']] = $section;
    }
    // Additional form data
    $block['target_page'] = "notification_update.php";
    // FIXME: better or more standardized way to do this?
    $script_url = explode('/', xoops_getenv('PHP_SELF'));
    $script_name = $script_url[count($script_url)-1];
    $block['redirect_script'] = $script_name;
    $block['submit_button'] = _NOT_UPDATENOW;
    return $block;
}
?>
