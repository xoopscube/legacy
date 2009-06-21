<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

if (!defined('XOOPS_ROOT_PATH')) exit();

define('CONTENTS_FRAME_PERFORM_SUCCESS', 1);
define('CONTENTS_FRAME_PERFORM_FAIL', 2);
define('CONTENTS_FRAME_INIT_SUCCESS', 3);

define('CONTENTS_FRAME_VIEW_NONE', 1);
define('CONTENTS_FRAME_VIEW_SUCCESS', 2);
define('CONTENTS_FRAME_VIEW_ERROR', 3);
define('CONTENTS_FRAME_VIEW_INDEX', 4);
define('CONTENTS_FRAME_VIEW_INPUT', 5);
define('CONTENTS_FRAME_VIEW_PREVIEW', 6);
define('CONTENTS_FRAME_VIEW_CANCEL', 7);

class FileManager_ActionFrame
{
	var $mActionName = null;
	var $mAction     = null;
	var $mAdminFlag  = null;

	/**
	 * @var XCube_Delegate
	 */
	var $mCreateAction = null;

	function FileManager_ActionFrame($admin)
	{
		$this->mAdminFlag = $admin;
		$this->mCreateAction =& new XCube_Delegate();
		$this->mCreateAction->register('FileManager_ActionFrame.CreateAction');
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
		if (is_object($actionFrame->mAction)) {
			return;
		}

		//
		// Create action object by mActionName
		//
		$className = 'FileManager_' . ucfirst($actionFrame->mActionName) . 'Action';
		$fileName = ucfirst($actionFrame->mActionName) . 'Action';
		if ($actionFrame->mAdminFlag) {
			$fileName = XOOPS_MODULE_PATH . '/fileManager/admin/actions/' . $fileName . '.class.php';
		}
		else {
			$fileName = XOOPS_MODULE_PATH . '/fileManager/actions/' . $fileName . '.class.php';
		}

		if (!file_exists($fileName)) {
			die( 'Error - contact module -file_exists :'. $fileName );
		}
		require_once $fileName;

		if (class_exists($className)) {
			$actionFrame->mAction =& new $className($actionFrame->mAdminFlag);
		}
	}

	function execute(&$controller)
	{
		if (strlen($this->mActionName) > 0 && !preg_match('/^\w+$/', $this->mActionName)) {
			die('ERORR - ActionName');
		}

		//
		// Create action object by mActionName
		//
		$this->mCreateAction->call(new XCube_Ref($this));

		if (!(is_object($this->mAction) && is_a($this->mAction, 'FileManager_Action'))) {
			if (!(is_object($this->mAction))) {
				die( 'Error - /class/ActionFrame.class.php mAction is exists<br>mAction error.
				');
			}
			if (is_a($this->mAction, 'FileManager_Action')) {
				die( 'Error -  not action object' );
			}
		}

		if ($this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig) === false) {
			//
			// error
			//
			die("ERORR - $this->mAction->prepare");
		}

		if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig)) {
			if ($this->mAdminFlag) {
				$controller->executeForward(XOOPS_URL . "/admin.php");
			}
			else {
				$controller->executeForward(XOOPS_URL);
			}
		}


		if (xoops_getenv('REQUEST_METHOD') == 'POST') {
			$viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
		}
		else {
			$viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
		}

//echo " viewStatus ".$viewStatus."<br />";

		switch ($viewStatus)
		{
			case CONTENTS_FRAME_VIEW_SUCCESS:
				$this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;

			case CONTENTS_FRAME_VIEW_ERROR:
				$this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;

			case CONTENTS_FRAME_VIEW_INDEX:
				$this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;

			case CONTENTS_FRAME_VIEW_INPUT:
				$this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;

			case CONTENTS_FRAME_VIEW_PREVIEW:
				$this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;

			case CONTENTS_FRAME_VIEW_CANCEL:
				$this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
			break;
		}
	}
}

class FileManager_Action
{
	function FileManager_Action()
	{
	}

	function isSecure()
	{
		return false;
	}
	function hasPermission(&$controller, &$xoopsUser, &$moduleConfig)
	{
		return true;
	}

	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		return CONTENTS_FRAME_VIEW_NONE;
	}

	function execute(&$controller, &$xoopsUser)
	{
		return CONTENTS_FRAME_VIEW_NONE;
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
