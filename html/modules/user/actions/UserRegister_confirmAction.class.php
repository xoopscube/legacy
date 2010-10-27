<?php
/**
 * @package user
 * @version $Id: UserRegister_confirmAction.class.php,v 1.3 2007/12/15 15:45:35 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/forms/UserConfirmForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/forms/UserRegisterEditForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/class/RegistMailBuilder.class.php";

/***
 * @internal
 * This action uses the special technic to realize confirming. It gets the
 * register action form which has been inputted in UserRegister, through
 * unserialize(). And, it uses a simple action form to confirm lastly.
 */
class User_UserRegister_confirmAction extends User_Action
{
	var $mActionForm = null;
	var $mRegistForm = null;
	var $mConfig = null;
	
	var $mNewUser = null;
	
	var $mRedirectMessage = null;
	
	/***
	 * TODO this member function uses the old style delegate.
	 */
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;

		$this->_getRegistForm($controller);
		$this->_processActionForm();
	}

	function execute(&$controller, &$xoopsUser)
	{
		if (XCube_Root::getSingleton()->mContext->mRequest->getRequest('_form_control_cancel') != null)
		{
			return USER_FRAME_VIEW_CANCEL;
		}

		$memberHandler =& xoops_gethandler('member');
		$this->mNewUser =& $memberHandler->createUser();
		$this->mRegistForm->update($this->mNewUser);
		$this->mNewUser->set('uorder', $controller->mRoot->mContext->getXoopsConfig('com_order'), true);
		$this->mNewUser->set('umode', $controller->mRoot->mContext->getXoopsConfig('com_mode'), true);
		if ($this->mConfig['activation_type'] == 1) {
			$this->mNewUser->set('level', 1, true);
		}

		if (!$memberHandler->insertUser($this->mNewUser)) {
			$this->mRedirectMessage = _MD_USER_LANG_REGISTERNG;
			return USER_FRAME_VIEW_ERROR;
		}

        if (!$memberHandler->addUserToGroup(XOOPS_GROUP_USERS, $this->mNewUser->get('uid'))) {
			$this->mRedirectMessage = _MD_USER_LANG_REGISTERNG;
			return USER_FRAME_VIEW_ERROR;
		}

		$this->_clearRegistForm($controller);

		$this->_processMail($controller);
		$this->_eventNotifyMail($controller);
		
		XCube_DelegateUtils::call('Legacy.Event.RegistUser.Success', new XCube_Ref($this->mNewUser));
		
		return USER_FRAME_VIEW_SUCCESS;
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		return USER_FRAME_VIEW_INPUT;
	}
	
	/***
	 * Get regist actionform from Session and set it to the member property.
	 * @access private
	 */
	function _getRegistForm(&$controller)
	{
		$this->mRegistForm = unserialize($_SESSION['user_register_actionform']);
		if (!is_object($this->mRegistForm)) {
			$controller->executeForward('./register.php?action=UserRegister');
		}
	}

	/***
	 * Clear session.
	 * @access private
	 */
	function _clearRegistForm(&$controller)
	{
		unset($_SESSION['user_register_actionform']);
	}
	
	function _processMail(&$controller)
	{
		$activationType = $this->mConfig['activation_type'];
		
		if($activationType == 1) {
			return;
		}

		// Wmm..
		$builder = ($activationType == 0) ? new User_RegistUserActivateMailBuilder()
		                                  : new User_RegistUserAdminActivateMailBuilder();

		$director =new User_UserRegistMailDirector($builder, $this->mNewUser, $controller->mRoot->mContext->getXoopsConfig(), $this->mConfig);
		$director->contruct();
		$mailer =& $builder->getResult();
		
		if (!$mailer->send()) {
		}	// TODO CHECKS and use '_MD_USER_ERROR_YOURREGMAILNG'
	}
	
	function _eventNotifyMail(&$controller)
	{
		if($this->mConfig['new_user_notify'] == 1 && !empty($this->mConfig['new_user_notify_group'])) {
			$builder =new User_RegistUserNotifyMailBuilder();
			$director =new User_UserRegistMailDirector($builder, $this->mNewUser, $controller->mRoot->mContext->getXoopsConfig(), $this->mConfig);
			$director->contruct();
			$mailer =& $builder->getResult();
			$mailer->send();
		}
	}

	function _processActionForm()
	{
		$this->mActionForm =new User_UserConfirmForm();
		$this->mActionForm->prepare();
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . '/', 1, $this->mRedirectMessage);
	}

	/**
	 * executeViewCancel
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL.'/register.php');
	}

	function executeViewInput(&$controller,&$xoopsUser,&$render)
	{
		$render->setTemplateName("user_register_confirm.html");
		$render->setAttribute("actionForm", $this->mActionForm);
		$render->setAttribute("registForm", $this->mRegistForm);
	}
	
	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$activationType = $this->mConfig['activation_type'];

		if ($activationType == 0) {
			$render->setTemplateName("user_register_finish.html");
			$render->setAttribute("complete_message", _MD_USER_MESSAGE_YOURREGISTERED);
		}
		elseif ($activationType == 1) {
			$controller->executeRedirect(XOOPS_URL . '/', 4, _MD_USER_MESSAGE_ACTLOGIN);
		}
		elseif($activationType == 2) {
			$render->setTemplateName("user_register_finish.html");
			$render->setAttribute("complete_message", _MD_USER_MESSAGE_YOURREGISTERED2);
		}
		else {
			//
			// This case is never.
			//
			$render->setTemplateName("user_register_finish.html");
			$render->setAttribute("complete_message", _MD_USER_MESSAGE_YOURREGISTERED2);
		}
	}
}
?>
