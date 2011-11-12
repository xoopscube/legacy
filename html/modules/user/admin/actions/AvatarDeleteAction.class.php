<?php
/**
 * @package user
 * @version $Id: AvatarDeleteAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/AvatarAdminDeleteForm.class.php";

class User_AvatarDeleteAction extends User_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('avatar_id');
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('avatar');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new User_AvatarAdminDeleteForm();
		$this->mActionForm->prepare();
	}
	
	function _doExecute()
	{
		$linkHandler =& xoops_getmodulehandler('avatar_user_link');
		$criteria =new Criteria('avatar_id', $this->mObject->get('avatar_id'));
		$linkArr =& $linkHandler->getObjects($criteria);
		
		if ($this->mObjectHandler->delete($this->mObject)) {
			//
			// Clear all user who set the avatar deleted with blank.gif
			//
			if (count($linkArr) > 0) {
				$userHandler =& xoops_gethandler('user');
				foreach ($linkArr as $link) {
					$user =& $userHandler->get($link->get('user_id'));

					if (is_object($user)) {
						$user->set('user_avatar', 'blank.gif');
						$userHandler->insert($user);
					}
					unset($user);
				}
			}
			
			return true;
		}
		else {
			return false;
		}
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("avatar_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=AvatarList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=AvatarList", 1, _MD_USER_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=AvatarList");
	}
}

?>
