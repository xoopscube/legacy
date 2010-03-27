<?php
/**
 * @file
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserName extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	public function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add('Legacy_User.GetUserName',array(&$this, 'get'));
	}

	/**
	 *	@public
	*/
	public function get(/*** string ***/ &$userName, /*** int ***/ $uid)
	{
		$handler = xoops_gethandler('member');
		$user = $handler->getUser($uid);
		$name = $user->getShow('name');
		$userName = $name ? $name : $user->getShow('uname');
	}

}

?>
