<?php
/**
 * @package user
 * @version $Id: UserDeleteAction.class.php,v 1.3 2007/12/15 13:59:03 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/forms/UserDeleteForm.class.php";

/***
 * @internal
 * This action is for self delete function.
 * 
 * Site owner want various procedure to this action. Therefore, this action may
 * have to implement main logic with Delegate only.
 */
class User_UserDeleteAction extends User_Action
{
	var $mActionForm = null;
	var $mObject = null;
	
	var $mSelfDelete = false;
	var $mSelfDeleteConfirmMessage = "";
	
	var $_mDoDelete;

	/**
	 * _getPageAction
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getPageAction()
	{
		return _DELETE;
	}

	/**
	 * _getPageTitle
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getPagetitle()
	{
		return Legacy_Utils::getUserName(Legacy_Utils::getUid());
	}

	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mSelfDelete = $moduleConfig['self_delete'];
		$this->mSelfDeleteConfirmMessage = $moduleConfig['self_delete_confirm'];
		
		$this->mActionForm =new User_UserDeleteForm();
		$this->mActionForm->prepare();
		
		$this->_mDoDelete =new XCube_Delegate('bool &', 'Legacy_Controller', 'XoopsUser');
		$this->_mDoDelete->register('User_UserDeleteAction._doDelete');
		
		$this->_mDoDelete->add(array(&$this, "_doDelete"));
		
		//
		// pre condition check
		//
		if (!$this->mSelfDelete) {
			$controller->executeForward(XOOPS_URL . '/');
		}
		
		if (is_object($xoopsUser)) {
			$handler =& xoops_getmodulehandler('users', 'user');
			$this->mObject =& $handler->get($xoopsUser->get('uid'));
		}
	}
	
	function isSecure()
	{
		return true;
	}

	function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
	{
		if ($xoopsUser->get('uid') == 1) {
			return false;
		}
		
		return true;
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		return USER_FRAME_VIEW_INPUT;
	}
	
	/**
	 * FIXME: Need FORCE LOGOUT here?
	 */
	function execute(&$controller, &$xoopsUser)
	{
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		$flag = false;
		$this->_mDoDelete->call(new XCube_Ref($flag), $controller, $xoopsUser);
		
		if ($flag) {
			XCube_DelegateUtils::call('Legacy.Event.UserDelete', new XCube_Ref($this->mObject));
			
			return USER_FRAME_VIEW_SUCCESS;
		}
		
		return USER_FRAME_VIEW_ERROR;
	}
	
	/**
	 * Exection deleting.
	 * 
	 * @return bool
	 */
	function _doDelete(&$flag, $controller, $xoopsUser)
	{
		$handler =& xoops_gethandler('member');
		if ($handler->deleteUser($xoopsUser)) {
			$handler =& xoops_gethandler('online');
			$handler->destroy($this->mObject->get('uid'));
			xoops_notification_deletebyuser($this->mObject->get('uid'));
			
			$flag = true;
		}
		
		$flag |= false;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_delete.html");
		$render->setAttribute('object', $this->mObject);
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('self_delete_message', $this->mSelfDeleteConfirmMessage);
	}
	
	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_delete_success.html");
		$render->setAttribute("object", $this->mObject);
	}
	
	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . '/', 3, _MD_USER_ERROR_DBUPDATE_FAILED);
	}
}

?>
