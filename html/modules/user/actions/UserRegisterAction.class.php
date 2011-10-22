<?php
/**
 * @package user
 * @version $Id: UserRegisterAction.class.php,v 1.2 2007/06/07 02:58:44 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/forms/UserRegisterEditForm.class.php";

/***
 * @internal
 * @public
 * This action uses the special technic to realize confirming. It set the 
 * register action form to Session through serialize(), then forward to the
 * confirm action. Because the confirm action can't work without the register
 * action form which it fetches from Session, the confim action doesn't need
 * to check the permission to register.
 */
class User_UserRegisterAction extends User_Action
{
	var $mActionForm = null;
	var $mConfig;
	var $mEnableAgreeFlag = false;

	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;
		
		if(is_object($xoopsUser)) { 
			//
			// If user is registered, kick to his information page.
			//
			$controller->executeForward(XOOPS_URL . "/user.php");
		}
		if (empty($this->mConfig['allow_register'])) {
		    $controller->executeRedirect(XOOPS_URL . '/', 6, _MD_USER_LANG_NOREGISTER);
		}
	}

	function execute(&$controller, &$xoopsUser)
	{
		$this->_processActionForm();
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return USER_FRAME_VIEW_INPUT;
		} else {
			$_SESSION['user_register_actionform'] = serialize($this->mActionForm);
			$controller->executeForward('./register.php?action=confirm');
		}
	}

	function getDefaultView(&$controller,&$xoopsUser)
	{
		$this->_processActionForm();
		return USER_FRAME_VIEW_INPUT;
	}
	
	function _processActionForm()
	{
		if(isset($_SESSION['user_register_actionform'])){
			$this->mActionForm = unserialize($_SESSION['user_register_actionform']);
			unset($_SESSION['user_register_actionform']);
			return;
		}
	
		if ($this->mConfig['reg_dispdsclmr'] != 0 && $this->mConfig['reg_disclaimer'] != null) {
			$this->mEnableAgreeFlag = true;
			$this->mActionForm =new User_RegisterAgreeEditForm($this->mConfig);
		} else {
			$this->mActionForm =new User_RegisterEditForm($this->mConfig);
		}
		
		$this->mActionForm->prepare();
		
		$root =& XCube_Root::getSingleton();
		$this->mActionForm->set('timezone_offset', $root->mContext->getXoopsConfig('default_TZ'));
	}

	function executeViewInput(&$controller,&$xoopsUser,&$renderSystem)
	{
		$renderSystem->setTemplateName("user_register_form.html");
		//
		// Get some objects for input form.
		//
		$tzoneHandler =& xoops_gethandler('timezone');
		$timezones =& $tzoneHandler->getObjects();
		$renderSystem->setAttribute('timezones', $timezones);
		$renderSystem->setAttribute("actionForm", $this->mActionForm);
		$renderSystem->setAttribute("enableAgree", $this->mEnableAgreeFlag);
		if($this->mEnableAgreeFlag) {
			$renderSystem->setAttribute("disclaimer", $this->mConfig['reg_disclaimer']);
		}
	}
}

?>
