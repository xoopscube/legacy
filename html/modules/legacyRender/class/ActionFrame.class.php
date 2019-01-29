<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define("LEGACYRENDER_FRAME_PERFORM_SUCCESS", 1);
define("LEGACYRENDER_FRAME_PERFORM_FAIL", 2);
define("LEGACYRENDER_FRAME_INIT_SUCCESS", 3);

define("LEGACYRENDER_FRAME_VIEW_NONE", 1);
define("LEGACYRENDER_FRAME_VIEW_SUCCESS", 2);
define("LEGACYRENDER_FRAME_VIEW_ERROR", 3);
define("LEGACYRENDER_FRAME_VIEW_INDEX", 4);
define("LEGACYRENDER_FRAME_VIEW_INPUT", 5);
define("LEGACYRENDER_FRAME_VIEW_PREVIEW", 6);
define("LEGACYRENDER_FRAME_VIEW_CANCEL", 7);

class LegacyRender_ActionFrame
{
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null;
    
    /**
     * @var XCube_Delegate
     */
    public $mCreateAction = null;
    // !Fix deprecated constructor for php 7.x
    public function __construct($admin)
    // public function LegacyRender_ActionFrame($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('LegacyRender_ActionFrame.CreateAction');
        $this->mCreateAction->add(array(&$this, '_createAction'));
    }

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
        $className = "LegacyRender_" . ucfirst($actionFrame->mActionName) . "Action";
        $fileName = ucfirst($actionFrame->mActionName) . "Action";
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/legacyRender/admin/actions/${fileName}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/legacyRender/actions/${fileName}.class.php";
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
        if (!preg_match("/^\w+$/", $this->mActionName)) {
            die();
        }
    
        //
        // Create action object by mActionName
        //
        $this->mCreateAction->call(new XCube_Ref($this));
    
        if (!(is_object($this->mAction) && is_a($this->mAction, 'LegacyRender_Action'))) {
            die();    //< TODO
        }

        $handler =& xoops_gethandler('config');
        $moduleConfig =& $handler->getConfigsByDirname('legacyRender');
    
        $this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $moduleConfig);

        if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser)) {
            if ($this->mAdminFlag) {
                $controller->executeForward(XOOPS_URL . "/admin.php");
            } else {
                $controller->executeForward(XOOPS_URL);
            }
        }
    
        if (xoops_getenv("REQUEST_METHOD") == "POST") {
            $viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }
    
        switch ($viewStatus) {
            case LEGACYRENDER_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        
            case LEGACYRENDER_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        
            case LEGACYRENDER_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        
            case LEGACYRENDER_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        
            case LEGACYRENDER_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        
            case LEGACYRENDER_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        }
    }
}

class LegacyRender_Action
{
    /**
     * @access private
     */
    public $_mAdminFlag = false;
    // !Fix deprecated constructor for php 7.x
    public function __construct($adminFlag = false)
    // public function LegacyRender_Action($adminFlag = false)
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
        return LEGACYRENDER_FRAME_VIEW_NONE;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return LEGACYRENDER_FRAME_VIEW_NONE;
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
}
