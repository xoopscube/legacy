<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

define("USER_FRAME_PERFORM_SUCCESS", 1);
define("USER_FRAME_PERFORM_FAIL", 2);
define("USER_FRAME_INIT_SUCCESS", 3);

define("USER_FRAME_VIEW_NONE", 1);
define("USER_FRAME_VIEW_SUCCESS", 2);
define("USER_FRAME_VIEW_ERROR", 3);
define("USER_FRAME_VIEW_INDEX", 4);
define("USER_FRAME_VIEW_INPUT", 5);
define("USER_FRAME_VIEW_PREVIEW", 6);
define("USER_FRAME_VIEW_CANCEL", 7);

class User_ActionFrame
{
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null;

    /**
     * @var XCube_Delegate
     */
    public $mCreateAction = null;
    
    public function User_ActionFrame($admin)
    {
        self::__construct($admin);
    }

    public function __construct($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('User_ActionFrame.CreateAction');
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
        if (is_object($this->mAction)) {
            return;
        }
        
        //
        // Create action object by mActionName
        //
        $className = "User_" . ucfirst($actionFrame->mActionName) . "Action";
        $fileName = ucfirst($actionFrame->mActionName) . "Action";
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/user/admin/actions/${fileName}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/user/actions/${fileName}.class.php";
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
        
        if (!(is_object($this->mAction) && is_a($this->mAction, 'User_Action'))) {
            die();    //< TODO
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
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }
    
        $render = $controller->mRoot->mContext->mModule->getRenderTarget();
        $render->setAttribute('xoops_pagetitle', $this->mAction->getPagetitle());
    
        switch ($viewStatus) {
            case USER_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        
            case USER_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        
            case USER_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        
            case USER_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
                
            case USER_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
                
            case USER_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        }
    }
}

class User_Action
{
    public function User_Action()
    {
        self::__construct();
    }

    public function __construct()
    {
    }
    
    public function isSecure()
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

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_NONE;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_NONE;
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
