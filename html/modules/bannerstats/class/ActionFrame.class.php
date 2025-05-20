<?php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

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
     * @param bool $admin Whether this is an admin action
     */
    public $mCreateAction = null;
    public function __construct($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('Bannerstats_ActionFrame.CreateAction');
        $this->mCreateAction->add([&$this, '_createAction']);
                // Set a default action name if none is provided later
        // This ensures mActionName is not null when execute() is called
/*         if ($this->mAdminFlag) {
            $this->mActionName = 'BannerList'; // Default admin action
        } else {
            $this->mActionName = 'Default'; // Or whatever your default public action is
        } */
    }

    /**
     * Set the action name
     * @param string $name Action name
     */
    public function setActionName($name)
    {
        $this->mActionName = $name;

        //
        // Temp FIXME!
        //
        $root =& XCube_Root::getSingleton();
        $root->mContext->setAttribute('actionName', $name);
        $root->mContext->mModule->setAttribute('actionName', $name);
    }

    public function _createAction(&$actionFrame)
    {
        if (is_object($actionFrame->mAction)) {
            return;
        }

        //
        // Create action object by mActionName
        //
        $className = 'Bannerstats_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = ucfirst($actionFrame->mActionName) . 'Action';
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/bannerstats/admin/actions/{$fileName}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/bannerstats/actions/{$fileName}.class.php";
        }

        if (!file_exists($fileName)) {
            die();
        }

        require_once $fileName;

        if (XC_CLASS_EXISTS($className)) {
            $actionFrame->mAction =new $className($actionFrame->mAdminFlag);
        }
    }

    public function execute(&$controller)
    {
        // If mActionName hasn't been set externally via setActionName(),
        // determine it now from the request or use a default.
        if ($this->mActionName === null) {
            $requestedAction = isset($_REQUEST['action']) ? trim(xoops_getrequest('action')) : '';
            if (!empty($requestedAction)) {
                $this->setActionName($requestedAction);
            } else {
                // Set a default if no action is in the request
                $this->setActionName($this->mAdminFlag ? 'BannerList' : 'Default');
            }
        }
        
        // Now $this->mActionName is guaranteed to be a string.
        // The preg_match validates its format.
        if (!preg_match("/^\w+$/", $this->mActionName)) {
            error_log("Bannerstats_ActionFrame: Invalid action name format: '" . $this->mActionName . "'");
            // You might want to redirect to a default valid action here instead of die()
            die("Invalid action name format.");
        }
        //
        // Create action object by mActionName
        //
        $this->mCreateAction->call(new XCube_Ref($this));

        if (!(is_object($this->mAction) && $this->mAction instanceof \Bannerstats_Action)) {
            die();    //< TODO
        }

        $handler =& xoops_gethandler('config');
        $moduleConfig =& $handler->getConfigsByDirname('bannerstats');

        $this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $moduleConfig);

        if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser)) {
            if ($this->mAdminFlag) {
                $controller->executeForward(XOOPS_URL . '/admin.php');
            } else {
                $controller->executeForward(XOOPS_URL);
            }
        }

        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }

        switch ($viewStatus) {
            case BANNERSTATS_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case BANNERSTATS_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case BANNERSTATS_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case BANNERSTATS_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case BANNERSTATS_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case BANNERSTATS_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        }
    }
}
/* 
class Bannerstats_Action
{

    public $_mAdminFlag = false;

    public function __construct($adminFlag = false)
    {
        $this->_mAdminFlag = $adminFlag;
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return BANNERSTATS_FRAME_VIEW_NONE;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return BANNERSTATS_FRAME_VIEW_NONE;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewPreview(&$controller, &$xoopsUser, &$render)
    {
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
    }
} */
