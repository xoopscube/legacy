<?php
/**
 *
 * @package XOOPS2
 * @version $Id: legacy_usermenu.php,v 1.3 2008/09/25 15:12:13 kilica Exp $
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


 /**
  * This function is called back to display the user menu.
  * 
  * [Template Variables]
  *  $block.uid ... Curent user id for the menu.
  *  $block.flagShowInbox ... If there is the pm module, set true.
  *  $block.inbox_url ... Return url to access inbox of pm.
  *  $block.new_messages ... amount of unread messages.
  */
function b_legacy_usermenu_show()
{
    $root =& XCube_Root::getSingleton();
    $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

    if (is_object($xoopsUser)) {
        $block = array();
		
        $block['uid'] = $xoopsUser->get('uid');
		$block['flagShowInbox'] = false;

		//
		// Check does this system have PrivateMessage feature.
		//
		$url = null;
		$service =& $root->mServiceManager->getService('privateMessage');
		if ($service != null) {
			$client =& $root->mServiceManager->createClient($service);
			$url = $client->call('getPmInboxUrl', array('uid' => $xoopsUser->get('uid')));
			
			if ($url != null) {
				$block['inbox_url'] = $url;
				$block['new_messages'] = $client->call('getCountUnreadPM', array('uid' => $xoopsUser->get('uid')));
				$block['flagShowInbox']=true;
			}
		}
		
		$block['show_adminlink'] = $root->mContext->mUser->isInRole('Site.Administrator');

        return $block;
    }
    return false;
}
?>
