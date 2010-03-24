<?php
/**
 * @brief Sample preload to use XoopsGenericHandler insert()/update()/delete()
 *		  raise Delegate Module.{dirname}.Event.{Action}.{tablename}
 *		  When you update your profile, user's post count is 1 up.
 *		  Move this file to modules/profile/preload to try this preload.
 *		  XCL2.2 or later is required.
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_ProfileCount extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		XCube_Root::getSingleton()->mDelegateManager->add('Module.profile.Event.Add.data',array(&$this, 'count'));
		XCube_Root::getSingleton()->mDelegateManager->add('Module.profile.Event.Update.data',array(&$this, 'count'));
	}

	/**
	 * @private
	 */
	public function count(&$obj)
	{
		$handler = xoops_gethandler('member');
		$user = $handler->getUser(Legacy_Utils::getUid());
		$user->incrementPost();
	}
}

?>
