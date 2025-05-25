<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Ensure the base classes are loaded
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/handler.php';

// Load the required module classes
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php'; // Or whatever you name the file containing Bannerstats_Action

define('BANNERSTATS_FRAME_PERFORM_SUCCESS', 1);
define('BANNERSTATS_FRAME_PERFORM_FAIL', 2);
define('BANNERSTATS_FRAME_INIT_SUCCESS', 3);

define('BANNERSTATS_FRAME_VIEW_NONE', 0);
define('BANNERSTATS_FRAME_VIEW_SUCCESS', 1);
define('BANNERSTATS_FRAME_VIEW_ERROR', 2);
define('BANNERSTATS_FRAME_VIEW_INDEX', 3);
define('BANNERSTATS_FRAME_VIEW_INPUT', 4);
define('BANNERSTATS_FRAME_VIEW_PREVIEW', 5);
define('BANNERSTATS_FRAME_VIEW_CANCEL', 6);

class Bannerstats_ActionFrame
{
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null;

    /**
     * Constructor
     * @param bool $admin action
     */
    public $mCreateAction = null;
    public function __construct($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('Bannerstats_ActionFrame.CreateAction');
        $this->mCreateAction->add([&$this, '_createAction']);
    }

    /**
     * Set the action name
     * @param string $name Action name
     */
    public function setActionName($name)
    {
        $this->mActionName = $name;

        $root =& XCube_Root::getSingleton();
        $root->mContext->setAttribute('actionName', $name);
        if (is_object($root->mContext->mModule)) {
            $root->mContext->mModule->setAttribute('actionName', $name);
        }
    }

    public function _createAction(&$actionFrame)
    {
        if (is_object($actionFrame->mAction)) {
            return;
        }

        $className = 'Bannerstats_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileNamePart = ucfirst($actionFrame->mActionName) . 'Action';
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/bannerstats/admin/actions/{$fileNamePart}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/bannerstats/actions/{$fileNamePart}.class.php";
        }

        if (!file_exists($fileName)) {
            error_log("Bannerstats_ActionFrame: Action file not found: " . $fileName . " for action " . $actionFrame->mActionName);
            $actionFrame->mAction = null;
            return;
        }

        require_once $fileName;

        if (class_exists($className)) {
            $actionFrame->mAction =new $className($actionFrame->mAdminFlag);
        } else {
            error_log("Bannerstats_ActionFrame: Action class not found: " . $className . " in file " . $fileName);
            $actionFrame->mAction = null;
        }
    }

    public function execute(&$controller)
    {
        if ($this->mActionName === null) {
            $requestedAction = isset($_REQUEST['action']) ? trim(xoops_getrequest('action')) : '';
            if (!empty($requestedAction) && preg_match("/^\w+$/", $requestedAction)) {
                $this->setActionName($requestedAction);
            } else {
                $this->setActionName($this->mAdminFlag ? 'BannerList' : 'Login');
            }
        }
        
        if (!preg_match("/^\w+$/", $this->mActionName)) {
            error_log("Bannerstats_ActionFrame: Invalid action name format after determination: '" . $this->mActionName . "'");
            $defaultSafeAction = $this->mAdminFlag ? 'BannerList' : 'Login';
            $moduleDirname = $controller->mRoot->mContext->mModule ? $controller->mRoot->mContext->mModule->getVar('dirname') : 'bannerstats';
            $controller->executeForward(XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=" . $defaultSafeAction);
            return;
        }

        $this->mCreateAction->call(new XCube_Ref($this));

        if (!(is_object($this->mAction) && $this->mAction instanceof \Bannerstats_Action)) {
            error_log("Bannerstats_ActionFrame: Action object is not valid for action name: " . $this->mActionName . ". mAction is: " . gettype($this->mAction));
            $errorAction = $this->mAdminFlag ? 'BannerList' : 'Login';
            $moduleDirname = $controller->mRoot->mContext->mModule ? $controller->mRoot->mContext->mModule->getVar('dirname') : 'bannerstats';
            $controller->executeForward(XOOPS_URL . "/modules/" . $moduleDirname . "/index.php?action=" . $errorAction . "&err=invalid_action_obj");
            return;
        }

        $handler =& xoops_gethandler('config');
        $moduleConfig =& $handler->getConfigsByDirname('bannerstats');

        $this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $moduleConfig);

        if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser)) {
            if ($this->mAdminFlag) {
                $controller->executeForward(XOOPS_URL . '/admin.php');
            }
            return; 
        }

        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }

        $renderTarget = $controller->mRoot->mContext->mModule->getRenderTarget();
        if (!$renderTarget instanceof XCube_RenderTarget) {
            error_log("Bannerstats_ActionFrame: RenderTarget is not available. View status: " . $viewStatus);
            if ($viewStatus !== BANNERSTATS_FRAME_VIEW_NONE) {
                echo "Critical Error: Page rendering system not initialized.";
                exit;
            }
        }

        switch ($viewStatus) {
            case BANNERSTATS_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
            case BANNERSTATS_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
            case BANNERSTATS_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
            case BANNERSTATS_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
            case BANNERSTATS_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
            case BANNERSTATS_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $renderTarget);
                break;
        }
    }
}
