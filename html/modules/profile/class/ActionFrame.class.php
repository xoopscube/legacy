<?php
/**
 * @package    profile
 * @version    2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

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
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null;

    /**
     * @var XCube_Delegate
     */
    public $mCreateAction = null;
    
    public function __construct($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('Profile_ActionFrame.CreateAction');
        $this->mCreateAction->add([&$this, '_createAction']);
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
        $className = 'Profile_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = ucfirst($actionFrame->mActionName) . 'Action';
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/profile/admin/actions/{$fileName}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/profile/actions/{$fileName}.class.php";
        }
    
        if (!file_exists($fileName)) {
            die('file_exists on _createAction');
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
        
        if (!(is_object($this->mAction) && $this->mAction instanceof \Profile_Action)) {
            die();    //< TODO
        }
    
        if ($this->mAction->isSecure() && !is_object($controller->mRoot->mContext->mXoopsUser)) {
            //
            // TODO error redirect
            //

            $controller->executeForward(XOOPS_URL . '/');
        }
        
        $this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig);
    
        if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig)) {
            //
            // TODO error redirect
            //

            $controller->executeForward(XOOPS_URL . '/');
        }
    
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }
    
        $render = $controller->mRoot->mContext->mModule->getRenderTarget();
        $render->setAttribute('xoops_pagetitle', $this->mAction->getPagetitle());
        echo($viewStatus);
        die;
        switch ($viewStatus) {
            case PROFILE_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($render);
                break;
        
            case PROFILE_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller);
                break;
        
            case PROFILE_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        
            case PROFILE_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller);
                break;
                
            case PROFILE_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller);
                break;
                
            case PROFILE_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller);
                break;
        }
    }
}

class Profile_Action
{
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

    public function prepare(&$controller, &$xoopsUser, &$moduleConfig)
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return PROFILE_FRAME_VIEW_NONE;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return PROFILE_FRAME_VIEW_NONE;
    }

    public function executeViewSuccess(&$render)
    {
    }

    public function executeViewError(&$render)
    {
    }

    public function executeViewInde(&$render)
    {
    }

    public function executeViewInput(&$render)
    {
    }

    public function executeViewPreview(&$render)
    {
    }

    public function executeViewCancel(&$render)
    {
    }
}
