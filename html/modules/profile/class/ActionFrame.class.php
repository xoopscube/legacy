<?php

if (!defined('XOOPS_ROOT_PATH')) exit();
/*
define ("PROFILE_FRAME_PERFORM_SUCCESS", 1);
define ("PROFILE_FRAME_PERFORM_FAIL", 2);
define ("PROFILE_FRAME_INIT_SUCCESS", 3);

define ("PROFILE_FRAME_VIEW_NONE", 1);
define ("PROFILE_FRAME_VIEW_SUCCESS", 2);
define ("PROFILE_FRAME_VIEW_ERROR", 3);
define ("PROFILE_FRAME_VIEW_INDEX", 4);
define ("PROFILE_FRAME_VIEW_INPUT", 5);
define ("PROFILE_FRAME_VIEW_PREVIEW", 6);
define ("PROFILE_FRAME_VIEW_CANCEL", 7);
*/
class Profile_ActionFrame
{
	var $mActionName = null;
	var $mAction = null;
	var $mAdminFlag = null;

	/**
	 * @var XCube_Delegate
	 */
	var $mCreateAction = null;
	
	function Profile_ActionFrame($admin)
	{
		$this->mAdminFlag = $admin;
		$this->mCreateAction =new XCube_Delegate();
		$this->mCreateAction->register('Profile_ActionFrame.CreateAction');
		$this->mCreateAction->add(array(&$this, '_createAction'));
	}

	function setActionName($name)
	{
		$this->mActionName = $name;
		
		//
		// Temp FIXME!
		//
		$root =& XCube_Root::getSingleton();
		$root->mContext->setAttribute('actionName', $name);
		$root->mContext->mModule->setAttribute('actionName', $name);
	}

	function _createAction(&$actionFrame)
	{
		if (is_object($this->mAction)) {
			return;
		}
		
		//
		// Create action object by mActionName
		//
		$className = "Profile_" . ucfirst($actionFrame->mActionName) . "Action";
		$fileName = ucfirst($actionFrame->mActionName) . "Action";
		if ($actionFrame->mAdminFlag) {
			$fileName = XOOPS_MODULE_PATH . "/profile/admin/actions/${fileName}.class.php";
		}
		else {
			$fileName = XOOPS_MODULE_PATH . "/profile/actions/${fileName}.class.php";
		}
	
		if (!file_exists($fileName)) {
			die("file_exists on _createAction");
		}
		
		require_once $fileName;
	
		if (XC_CLASS_EXISTS($className)) {
			$actionFrame->mAction =new $className($actionFrame->mAdminFlag);
		}
	}
	
	function execute(&$controller)
	{
		if (!preg_match("/^\w+$/", $this->mActionName)) {
			die();
		}
		
		//
		// Create action object by mActionName
		//
		$this->mCreateAction->call(new XCube_Ref($this));
		
		if (!(is_object($this->mAction) && is_a($this->mAction, 'Profile_Action'))) {
			die();	//< TODO
		}
	
		if ($this->mAction->isSecure() && !is_object($controller->mRoot->mContext->mXoopsUser)) {
			//
			// error
			//
			
			$controller->executeForward(XOOPS_URL . '/');
		}
		
		$this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig);
	
		if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig)) {
			//
			// error
			//
			
			$controller->executeForward(XOOPS_URL . '/');
		}
	
		if (xoops_getenv("REQUEST_METHOD") == "POST") {
			$viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
		}
		else {
			$viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
		}
	
        $render = $controller->mRoot->mContext->mModule->getRenderTarget();
        $render->setAttribute('xoops_pagetitle', $this->mAction->getPagetitle());
 		echo ($viewStatus); die;
		switch($viewStatus) {
			case PROFILE_FRAME_VIEW_SUCCESS:
				$this->mAction->executeViewSuccess($render);
				break;
		
			case PROFILE_FRAME_VIEW_ERROR:
				$this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $render);
				break;
		
			case PROFILE_FRAME_VIEW_INDEX:
				$this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $render);
				break;
		
			case PROFILE_FRAME_VIEW_INPUT:
				$this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $render);
				break;
				
			case PROFILE_FRAME_VIEW_PREVIEW:
				$this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $render);
				break;
				
			case PROFILE_FRAME_VIEW_CANCEL:
				$this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $render);
				break;
		}
	}
}

class Profile_Action
{
	function Profile_Action()
	{
	}
	
	function isSecure()
	{
		return false;
	}

	/**
	 * _getPageAction
	 * 
	 * @param	void
	 * 
	 * @return	string
	**/
	protected function _getPageAction()
	{
		return null;
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
		return null;
	}

	public function getPageTitle()
	{
		return Legacy_Utils::formatPagetitle(XCube_Root::getSingleton()->mContext->mModule->mXoopsModule->get('name'), $this->_getPagetitle(), $this->_getPageAction());
	}

	function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
	{
		return true;
	}

	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		return PROFILE_FRAME_VIEW_NONE;
	}

	function execute(&$controller, &$xoopsUser)
	{
		return PROFILE_FRAME_VIEW_NONE;
	}

	function executeViewSuccess( &$render)
	{
	}

	function executeViewError(&$render)
	{
	}

	function executeViewInde(&$render)
	{
	}

	function executeViewInput(&$render)
	{
	}

	function executeViewPreview(&$render)
	{
	}

	function executeViewCancel(&$render)
	{
	}
}

?>
