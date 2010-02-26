<?php
/**
 * @package user
 * @version $Id: LostPassAction.class.php,v 1.3 2008/07/20 05:55:52 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/forms/LostPassEditForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/class/LostPassMailBuilder.class.php";

/***
 * @internal
 * @public
 * The process of lostpass. This action sends a mail even if the input mail
 * address isn't registered in the site. Because displaying error message in
 * such case shows the part of the personal information. We will discuss about
 * this spec.
 */
class User_LostPassAction extends User_Action
{
	/***
	 * @var User_LostPassEditForm
	 */
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mActionForm =new User_LostPassEditForm();
		$this->mActionForm->prepare();
	}
	
	function isSecure()
	{
		return false;
	}
	
	//// Allow anonymous users only.
	function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
	{
		return !$controller->mRoot->mContext->mUser->mIdentity->isAuthenticated();
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$root =& XCube_Root::getSingleton();
		$code = $root->mContext->mRequest->getRequest('code');	// const $code
		$email = $root->mContext->mRequest->getRequest('email');	// const $email
		if (strlen($code) == 0 || strlen($email) == 0) {
			return USER_FRAME_VIEW_INPUT;
		} else {
			return $this->_updatePassword($controller);
		}
	}

	function _updatePassword(&$controller) {
		$this->mActionForm->fetch();

		$userHandler =& xoops_gethandler('user');
		$criteria =new CriteriaCompo(new Criteria('email', $this->mActionForm->get('email')));
		$criteria->add(new Criteria('pass', $this->mActionForm->get('code'), '=', '', 'LEFT(%s, 5)'));
		$lostUserArr =& $userHandler->getObjects($criteria);
		
		if (is_array($lostUserArr) && count($lostUserArr) > 0) {
			$lostUser =& $lostUserArr[0];
		}
		else {
			return USER_FRAME_VIEW_ERROR;
		}

		$newpass = xoops_makepass();
		$extraVars['newpass'] = $newpass;
		$builder =new User_LostPass2MailBuilder();
		$director =new User_LostPassMailDirector($builder, $lostUser, $controller->mRoot->mContext->getXoopsConfig(), $extraVars);
		$director->contruct();
		$xoopsMailer =& $builder->getResult();
		if (!$xoopsMailer->send()) {
			// $xoopsMailer->getErrors();
			return USER_FRAME_VIEW_ERROR;
		}
		$lostUser->set('pass',md5($newpass), true);
		$userHandler->insert($lostUser, true);

		return USER_FRAME_VIEW_SUCCESS;
	}

	function execute(&$controller, &$xoopsUser)	
	{
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return USER_FRAME_VIEW_INPUT;
		}
		
		$userHandler =& xoops_gethandler('user');
		$lostUserArr =& $userHandler->getObjects(new Criteria('email', $this->mActionForm->get('email')));

		if (is_array($lostUserArr) && count($lostUserArr) > 0) {
			$lostUser =& $lostUserArr[0];
		}
		else {
			return USER_FRAME_VIEW_ERROR;
		}

		$builder =new User_LostPass1MailBuilder();
		$director =new User_LostPassMailDirector($builder, $lostUser, $controller->mRoot->mContext->getXoopsConfig());
		$director->contruct();
		$xoopsMailer =& $builder->getResult();

		if (!$xoopsMailer->send()) {
			// $xoopsMailer->getErrors();
			return USER_FRAME_VIEW_ERROR;
		}

		return USER_FRAME_VIEW_SUCCESS;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_lostpass.html");
		$render->setAttribute("actionForm", $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . '/', 3, _MD_USER_MESSAGE_SEND_PASSWORD);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . '/', 3, _MD_USER_ERROR_SEND_MAIL);
	}
}

?>
