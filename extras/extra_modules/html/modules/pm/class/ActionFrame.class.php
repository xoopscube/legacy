<?php
/**
 * @package pm
 * @version $Id: ActionFrame.class.php,v 1.1 2007/05/15 02:35:26 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

define ("PM_FRAME_PERFORM_SUCCESS", 1);
define ("PM_FRAME_PERFORM_FAIL", 2);
define ("PM_FRAME_INIT_SUCCESS", 3);

define ("PM_FRAME_VIEW_NONE", 1);
define ("PM_FRAME_VIEW_SUCCESS", 2);
define ("PM_FRAME_VIEW_ERROR", 3);
define ("PM_FRAME_VIEW_INDEX", 4);
define ("PM_FRAME_VIEW_INPUT", 5);
define ("PM_FRAME_VIEW_PREVIEW", 6);
define ("PM_FRAME_VIEW_CANCEL", 7);

class Pm_ActionFrame
{
	var $mActionName = null;
	var $mAction = null;
	var $mAdminFlag = null;

	function Pm_ActionFrame($admin)
	{
		$this->mAdminFlag = $admin;
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

	function execute(&$controller)
	{
		if (!preg_match("/^\w+$/", $this->mActionName)) {
			die();
		}
	
		//
		// Create action object by mActionName
		//
		$className = "Pm_" . ucfirst($this->mActionName) . "Action";
		$fileName = ucfirst($this->mActionName) . "Action";
		if ($this->mAdminFlag) {
			$fileName = XOOPS_MODULE_PATH . "/pm/admin/actions/${fileName}.class.php";
		}
		else {
			$fileName = XOOPS_MODULE_PATH . "/pm/actions/${fileName}.class.php";
		}
	
		if (!file_exists($fileName)) {
			die();
		}
	
		require_once $fileName;
	
		if (XC_CLASS_EXISTS($className)) {
			$this->mAction =& new $className();
		}
	
		if (!is_object($this->mAction)) {
			$this->doActionNotFoundError($controller);
			return;
		}
	
		$handler =& xoops_gethandler('config');
		$moduleConfig =& $handler->getConfigsByDirname('pm');
	
		$this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $moduleConfig);
	
		if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser, $moduleConfig)) {
			$this->doPermissionError($controller);
			return;
		}
	
		if (xoops_getenv("REQUEST_METHOD") == "POST") {
			$viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
		}
		else {
			$viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
		}
	
		switch($viewStatus) {
			case PM_FRAME_VIEW_SUCCESS:
				$this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;
		
			case PM_FRAME_VIEW_ERROR:
				$this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;
		
			case PM_FRAME_VIEW_INDEX:
				$this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;
		
			case PM_FRAME_VIEW_INPUT:
				$this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;

			case PM_FRAME_VIEW_PREVIEW:
				$this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;

			case PM_FRAME_VIEW_CANCEL:
				$this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
				break;
		}
	}

	function doPermissionError(&$controller)
	{
		$errorMessages = array(_MD_PM_MESSAGE_SORRY, _MD_PM_MESSAGE_PLZREG);
		$controller->executeRedirect(XOOPS_URL . '/', 2, $errorMessages);
	}

	function doActionNotFoundError($controller)
	{
		$controller->executeForward(XOOPS_URL);
		return;
	}

	function checkPermission($name, $itemIds)
	{
	}
}

class Pm_AbstractAction
{
	function Pm_AbstractAction()
	{
	}

	function prepare(&$controller, &$xoopsUser)
	{
	}

	function hasPermission(&$controller, &$xoopsUser, &$moduleConfig)
	{
		return is_object($xoopsUser);
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		return PM_FRAME_VIEW_NONE;
	}

	function execute(&$controller, &$xoopsUser)
	{
		return PM_FRAME_VIEW_NONE;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
	}

	function executeViewPreview(&$controller, &$xoopsUser, &$render)
	{
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
	}
}

?>
