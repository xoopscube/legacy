<?php

/**
 * @brief This site preload bans access to userinfo. (A user can access self page.) 
 * 
 * If you need more expressions, you may modify a template.
 * 
 * @see http://sourceforge.net/tracker/index.php?func=detail&aid=1718508&group_id=159211&atid=943472
 */
class UserInfoProtector extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		$root =& XCube_Root::getSingleton();
		$delegateMgr =& $root->getDelegateManager();
		
		$delegateMgr->add('Legacypage.Userinfo.Access',
			"UserInfoProtector::rightCheck",
			XCUBE_DELEGATE_PRIORITY_2);
	}
	
	function rightCheck()
	{
		$root =& XCube_Root::getSingleton();
		if (!$root->mContext->mUser->mIdentity->isAuthenticated()) {
			$root->mController->executeForward(XOOPS_URL);
		}
		
		$uid = $root->mContext->mXoopsUser->get('uid');
		$requestUid = $root->mContext->mRequest->getRequest('uid');
		if ($uid != null && $uid != $requestUid) {
			$root->mController->executeForward(XOOPS_URL);
		}
	}
}

?>