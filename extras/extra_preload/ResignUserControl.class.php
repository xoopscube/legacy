<?php

/// * YOU MUST SET THE FOLLOWING CONSIST * 
define("RESIGN_USER_GROUP_ID", 0);

/**
 * @brief This preload controls 'UserDelete' action of the user module in the public side.
 * 
 * Basically, Package_Legacy deletes all informations of the user who want to delete self
 * account. But, if you want to control it, this file is good sample. This sample doesn't
 * delete the user's informations from DB. Instead, this removes the user from groups and
 * moves the user to the special group indicated by RESIGN_USER_GROUP_ID. 
 * 
 * At the beginning, you have to prepare the special group that doesn't have login-right.
 * Next, you input GROUP-ID of the special group into RESIGN_USER_GROUP_ID.
 * 
 * define("RESIGN_USER_GROUP_ID", {here});
 * 
 * Finally, you move this preload file to /preload of your site.
 * 
 * Notes that this preload doesn't give effect when you delete a user in the control panel.
 */
class ResignUserControl extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		if (RESIGN_USER_GROUP_ID > 0) {
			$root =& XCube_Root::getSingleton();
			$delegateMgr =& $root->getDelegateManager();
		
			$delegateMgr->add('User_UserDeleteAction._doDelete',
				"ResignUserControl::resign",
				XCUBE_DELEGATE_PRIORITY_2);
		}
	}
	
	function resign(&$flag, &$controller, &$xoopsUser)
	{
		$handler =& xoops_gethandler('member');
		$groups = $handler->getGroupsByUser($xoopsUser->get('uid'));
		foreach ($groups as $group) {
			$handler->removeUserFromGroup($group, $xoopsUser->get('uid'));
		}
		
		$handler->addUserToGroup(RESIGN_USER_GROUP_ID, $xoopsUser->get('uid'));
		xoops_notification_deletebyuser($xoopsUser->get('uid'));
		
		XCube_DelegateUtils::call('Legacy.Event.UserDelete', new XCube_Ref($xoopsUser));
		
		$flag = true;
		
		$root =& XCube_Root::getSingleton();

		// Reset session
		$_SESSION = array();
		$root->mSession->destroy(true);

		// reset online
		$handler =& xoops_gethandler('online');
		$handler->destroy($xoopsUser->get('uid'));
		xoops_notification_deletebyuser($xoopsUser->get('uid'));

		// Redirect not to call behind delegates.
		$langMgr =& $root->getLanguageManager();
		$langMgr->loadPageTypeMessageCatalog('user');

		$controller =& $root->getController();
		$controller->executeRedirect(XOOPS_URL, 3, _US_BEENDELED);
	}
}

?>