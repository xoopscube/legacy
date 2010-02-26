<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractAction.class.php";

define('PROFILE_FRAME_PERFORM_SUCCESS', 1);
define('PROFILE_FRAME_PERFORM_FAIL', 2);
define('PROFILE_FRAME_INIT_SUCCESS', 3);

define('PROFILE_FRAME_VIEW_NONE', "none");
define('PROFILE_FRAME_VIEW_SUCCESS', "success");
define('PROFILE_FRAME_VIEW_ERROR', "error");
define('PROFILE_FRAME_VIEW_INDEX', "index");
define('PROFILE_FRAME_VIEW_INPUT', "input");
define('PROFILE_FRAME_VIEW_PREVIEW', "preview");
define('PROFILE_FRAME_VIEW_CANCEL', "cancel");

class Profile_Module extends Legacy_ModuleAdapter
{
	var $mActionName = null;
	var $mAction = null;
	var $mAdminFlag = false;
	var $mAssetManager = null;

	/**
	 * @public
	 */
	function startup()
	{
		parent::startup();
	
		XCube_DelegateUtils::call('Module.profile.Event.GetAssetManager', new XCube_Ref($this->mAssetManager));
	
		$root =& XCube_Root::getSingleton();
		$root->mController->mExecute->add(array(&$this, "execute"));
	
		//
		// TODO/Insert your initialization code.
		//
	}

	/**
	 * @public
	 */
	function setAdminMode($flag)
	{
		$this->mAdminFlag = $flag;
	}

	/**
	 * @public
	 */
	function setActionName($name)
	{
		$this->mActionName = $name;
	}

	/**
	 * @private
	 */
	function execute(&$controller)
	{
		if ($this->mActionName == null) {
			$this->mActionName = xoops_getrequest('action');
			if ($this->mActionName == null) {
				$this->mActionName = "DataEdit";
			}
		}
	
		if (!preg_match("/^\w+$/", $this->mActionName)) {
			$this->doActionNotFoundError();
			die();
		}
	
		//
		// Create action object by mActionName
		//
		$fileName = ucfirst($this->mActionName) . "Action";
		if ($this->mAdminFlag) {
			$className = "Profile_Admin_" . ucfirst($this->mActionName) . "Action";
			$fileName = XOOPS_MODULE_PATH . "/profile/admin/actions/${fileName}.class.php";
		}
		else {
			$className = "Profile_" . ucfirst($this->mActionName) . "Action";
			$fileName = XOOPS_MODULE_PATH . "/profile/actions/${fileName}.class.php";
		}
	
		if (!file_exists($fileName)) {
			$this->doActionNotFoundError();
			die();
		}
	
		require_once $fileName;
	
		if (class_exists($className)) {
			$this->mAction =new $className();
		}
	
		if (!is_object($this->mAction)) {
			$this->doActionNotFoundError();
			die();
		}
	
		if ($this->mAction->isMemberOnly() && !$controller->mRoot->mContext->mUser->isInRole('Site.RegisteredUser')) {
			$this->doPermissionError();
			die();
		}
	
		if ($this->mAction->isAdminOnly() && !$controller->mRoot->mContext->mUser->isInRole('Module.profile.Admin')) {
			$this->doPermissionError();
			die();
		}
	
		if ($this->mAction->prepare() === false) {
			$this->doPreparationError();
			die();
		}
	
		if (!$this->mAction->hasPermission()) {
			$this->doPermissionError();
			die();
		}
	
		if (xoops_getenv("REQUEST_METHOD") == "POST") {
			$viewStatus = $this->mAction->execute();
		}
		else {
			$viewStatus = $this->mAction->getDefaultView();
		}
	
		switch ($viewStatus) {
			case PROFILE_FRAME_VIEW_SUCCESS:
				$this->mAction->executeViewSuccess($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
			case PROFILE_FRAME_VIEW_ERROR:
				$this->mAction->executeViewError($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
			case PROFILE_FRAME_VIEW_INDEX:
				$this->mAction->executeViewIndex($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
			case PROFILE_FRAME_VIEW_INPUT:
				$this->mAction->executeViewInput($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
			case PROFILE_FRAME_VIEW_PREVIEW:
				$this->mAction->executeViewPreview($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
			case PROFILE_FRAME_VIEW_CANCEL:
				$this->mAction->executeViewCancel($controller->mRoot->mContext->mModule->getRenderTarget());
				break;
		}
	}

	/**
	 * @private
	 */
	function doPermissionError()
	{
		XCube_DelegateUtils::call("Module.Profile.Event.Exception.Permission");
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward(XOOPS_URL);
		return;
	}

	/**
	 * @private
	 */
	function doActionNotFoundError()
	{
		XCube_DelegateUtils::call("Module.Profile.Event.Exception.ActionNotFound");
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward(XOOPS_URL);
		return;
	}

	/**
	 * @private
	 */
	function doPreparationError()
	{
		XCube_DelegateUtils::call("Module.Profile.Event.Exception.Preparation");
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward(XOOPS_URL);
		return;
	}
}

?>
