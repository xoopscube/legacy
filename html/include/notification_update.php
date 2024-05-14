<?php
/**
 * Notification update
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


// RMV-NOTIFY

// This module expects the following arguments:
//
// not_submit
// not_redirect (to return back after update)
// not_mid (TODO)
// not_uid (TODO)
// not_list[1][params] = {category},{itemid},{event}
// not_list[1][status] = 1 if selected; 0 or missing if not selected
// etc...

// TODO: can we put arguments in the not_redirect argument??? do we need
// to specially encode them first???

// TODO: allow 'GET' also so we can process 'unsubscribe' requests??

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    exit();
}

include_once XOOPS_ROOT_PATH.'/include/notification_constants.php';
include_once XOOPS_ROOT_PATH.'/include/notification_functions.php';

$root =& XCube_Root::getSingleton();
$root->mLanguageManager->loadPageTypeMessageCatalog('notification');

if (!isset($_POST['not_submit'])) {
    exit();
}

// NOTE: in addition to the templates provided in the block and view
// modes, we can have buttons, etc. which load the arguments to be
// read by this script.  That way a module can really customize its
// look as to where/how the notification options are made available.

$update_list = $_POST['not_list'];

$module_id = $xoopsModule->getVar('mid');
$user_id = !empty($xoopsUser) ? $xoopsUser->getVar('uid') : 0;

// For each event, update the notification depending on the status.
// If status=1, subscribe to the event; otherwise, unsubscribe.

// FIXME: right now I just ignore database errors (e.g. if already
//  subscribed)... deal with this more gracefully?

$notification_handler =& xoops_gethandler('notification');

foreach ($update_list as $update_item) {
    [$category, $item_id, $event] = explode(',', $update_item['params']);
    $status = !empty($update_item['status']) ? 1 : 0;

    if (!$status) {
        $notification_handler->unsubscribe($category, $item_id, $event, $module_id, $user_id);
    } else {
        $notification_handler->subscribe($category, $item_id, $event);
    }
}

// TODO: something like grey box summary of actions (like multiple comment deletion), with a button to return back...
// NOTE: we need some arguments to help us get back to where we were...

// TODO: finish integration with comments... i.e. need calls to
// notifyUsers at appropriate places... (need to figure out where
// comment submit occurs and where comment approval occurs)...

include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

$redirect_args = [];
foreach ($update_list as $update_item) {
    [$category, $item_id, $event] = explode(',', $update_item['params']);
    $category_info =& notificationCategoryInfo($category);
    if (!empty($category_info['item_name'])) {
        $redirect_args[$category_info['item_name']] = $item_id;
    }
}

// TODO: write a central function to put together args with '?' and '&amp;'
// symbols...
$argstring = '';
$first_arg = 1;
foreach (array_keys($redirect_args) as $arg) {
    if ($first_arg) {
        $argstring .= '?' . $arg . '=' . $redirect_args[$arg];
        $first_arg = 0;
    } else {
        $argstring .= '&amp;' . $arg . '=' . $redirect_args[$arg];
    }
}

redirect_header($_POST['not_redirect'].$argstring, 2, _NOT_UPDATEOK);
exit();
