<?php
/**
 * legacy_usermenu.php
 * XOOPS2
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This file has been modified for Legacy from XOOPS2 System module block
 * This function is called back to display the user menu.
 * [Template Variables]
 *  $block.uid ... Current user id for the menu.
 *  $block.flagShowInbox ... If there is the pm module, set true.
 *  $block.inbox_url ... Return url to access inbox of pm.
 *  $block.new_messages ... amount of unread messages.
 */

function b_legacy_usermenu_show()
{
    $root =& XCube_Root::getSingleton();
    $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

    if (is_object($xoopsUser)) {
        $block = [];

        $block['uid'] = $xoopsUser->get('uid');
        $block['flagShowInbox'] = false;

        //
        // Check does this system have PrivateMessage feature.
        //
        $url = null;
        $service =& $root->mServiceManager->getService('privateMessage');
        if (null != $service) {
            $client =& $root->mServiceManager->createClient($service);
            $url = $client->call('getPmInboxUrl', ['uid' => $xoopsUser->get('uid')]);

            if (null != $url) {
                $block['inbox_url'] = $url;
                $block['new_messages'] = $client->call('getCountUnreadPM', ['uid' => $xoopsUser->get('uid')]);
                $block['flagShowInbox']=true;
            }
        }

        $block['show_adminlink'] = $root->mContext->mUser->isInRole('Site.Administrator');

        return $block;
    }
    return false;
}
