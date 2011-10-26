<?php
/**
 * @package user
 * @version $Id
 */

if (!defined('XOOPS_ROOT_PATH')) die();

class User_PrimaryFilter extends XCube_ActionFilter
{
	function preFilter()
	{
		$root =& XCube_Root::getSingleton();
		$this->mController->mSetupUser->add("User_Utils::setupUser");
		$this->mController->_mNotifyRedirectToUser->add("User_Utils::convertUrlToUser");

		$file = XOOPS_ROOT_PATH . "/modules/user/kernel/LegacypageFunctions.class.php";
		
		$root->mDelegateManager->add("Legacypage.Userinfo.Access", "User_LegacypageFunctions::userinfo", $file);
		$root->mDelegateManager->add("Legacypage.Edituser.Access", "User_LegacypageFunctions::edituser", $file);
		$root->mDelegateManager->add("Legacypage.Register.Access", "User_LegacypageFunctions::register", $file);
		$root->mDelegateManager->add("Legacypage.User.Access", "User_LegacypageFunctions::user", $file);
		$root->mDelegateManager->add("Legacypage.Lostpass.Access", "User_LegacypageFunctions::lostpass", $file);
		$root->mDelegateManager->add("Site.CheckLogin", "User_LegacypageFunctions::checkLogin", $file);
		$root->mDelegateManager->add("Site.CheckLogin.Success", "User_LegacypageFunctions::checkLoginSuccess", $file);
		$root->mDelegateManager->add("Site.Logout", "User_LegacypageFunctions::logout", $file);
		
		$root->mDelegateManager->add("Legacypage.Misc.Access", "User_LegacypageFunctions::misc", XCUBE_DELEGATE_PRIORITY_NORMAL - 5, $file);
	}
}

/***
 * @internal
 * This static class has a static member function for login process. Because
 * this process is always called, this class is always loaded. We may move this
 * class to other file. This file is a preload and no good for normal class
 * definition. 
 * 
 * @todo We may move this class to other file.
 */
class User_Utils
{
	function setupUser(&$principal, &$controller, &$context)
	{
		if (is_object($context->mXoopsUser)) {
			return;
		}
		
		if (!empty($_SESSION['xoopsUserId'])) {
			$memberHandler = xoops_gethandler('member');
			$user =& $memberHandler->getUser($_SESSION['xoopsUserId']);
			$context->mXoopsUser =& $user;
			if (is_object($context->mXoopsUser)) {
				$context->mXoopsUser->setGroups($_SESSION['xoopsUserGroups']);
				
				$roles = array();
				$roles[] = "Site.RegisteredUser";
				if ($context->mXoopsUser->isAdmin(-1)) {
					$roles[] = "Site.Administrator";
				}
				if (in_array(XOOPS_GROUP_ADMIN, $_SESSION['xoopsUserGroups'])) {
					$roles[] = "Site.Owner";
				}
				
				$identity =new Legacy_Identity($context->mXoopsUser);
				$principal = new Legacy_GenericPrincipal($identity, $roles);
				return;
			} else {
				$context->mXoopsUser = null;
				$_SESSION = array();
			}
		}
		$identity =new Legacy_AnonymousIdentity();
		$principal = new Legacy_GenericPrincipal($identity, array("Site.GuestUser"));
	}
	
	function convertUrlToUser(&$url)
	{
		global $xoopsRequestUri;
		if (!strstr($url, '?')) {
			$url .= "?xoops_redirect=" . urlencode($xoopsRequestUri);
		}
		else {
			$url .= "&amp;xoops_redirect=" . urlencode($xoopsRequestUri);
		}
	}
}

?>
