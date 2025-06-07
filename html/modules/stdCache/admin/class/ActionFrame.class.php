<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// These VIEW constants ARE USED by the ActionFrame's switch statement
// and are expected to be returned by action methods
define('STDCACHE_FRAME_VIEW_NONE', 1);     // Action handles rendering or redirects, no further view needed
define('STDCACHE_FRAME_VIEW_SUCCESS', 2);  // Call executeViewSuccess()
define('STDCACHE_FRAME_VIEW_ERROR', 3);    // Call executeViewError()
define('STDCACHE_FRAME_VIEW_INDEX', 4);    // Call executeViewIndex() (for default list/dashboard views)
define('STDCACHE_FRAME_VIEW_INPUT', 5);    // Call executeViewInput() (for displaying a form)
define('STDCACHE_FRAME_VIEW_PREVIEW', 6);  // Call executeViewPreview()
define('STDCACHE_FRAME_VIEW_CANCEL', 7);   // Call executeViewCancel()

class stdCache_ActionFrame
{
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null; 
    public $mRoot = null;      

    /**
     * @var XCube_Delegate
     */
    public $mCreateAction = null; 

    public function __construct($admin)
    {
        $this->mAdminFlag = (bool)$admin; 
        $this->mRoot = XCube_Root::getSingleton();

        $this->mCreateAction = new XCube_Delegate();
        $this->mCreateAction->register('stdCache_ActionFrame.CreateAction');
        $this->mCreateAction->add([&$this, '_createAction']);
    }

    public function setActionName($name)
    {
        $this->mActionName = $name;
        $this->mRoot->mContext->setAttribute('actionName', $name);
        if (is_object($this->mRoot->mContext->mModule)) { 
            $this->mRoot->mContext->mModule->setAttribute('actionName', $name);
        }
    }

    /**
     * Creates the action object
     * default delegate implementation
     * @param stdCache_ActionFrame $actionFrame
     */
    public function _createAction(&$actionFrame) 
    {
        if (is_object($actionFrame->mAction)) {
            return; 
        }

        $className = 'stdCache_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = ucfirst($actionFrame->mActionName) . 'Action';
        $filePath = XOOPS_MODULE_PATH . "/stdCache/admin/actions/{$fileName}.class.php";

        if (!file_exists($filePath)) {
            trigger_error("Action file not found: {$filePath}", E_USER_ERROR); 
            exit(); 
        }

        require_once $filePath;

        if (class_exists($className)) { 
            $actionFrame->mAction = new $className($actionFrame->mAdminFlag);
        } else {
            trigger_error("Action class not found: {$className} in {$filePath}", E_USER_ERROR);
            exit();
        }
    }

    public function execute(&$controller)
    {
        $this->mCreateAction->call(new XCube_Ref($this));

        if (!(is_object($this->mAction) && $this->mAction instanceof stdCache_Action)) {
            trigger_error("Failed to create a valid action object for action name: " . $this->mActionName, E_USER_ERROR);
            exit();
        }

        if ($this->mAction->isSecure() && !is_object($this->mRoot->mContext->mXoopsUser)) {
            $controller->executeForward(XOOPS_URL . '/user.php'); 
            return STDCACHE_FRAME_VIEW_NONE; 
        }

        $this->mAction->prepare($controller, $this->mRoot->mContext->mXoopsUser, $this->mRoot->mContext->mModuleConfig);

        if (!$this->mAction->hasPermission($controller, $this->mRoot->mContext->mXoopsUser, $this->mRoot->mContext->mModuleConfig)) {
            $controller->executeForward(XOOPS_URL . '/admin.php', 1, _NOPERM); 
            return STDCACHE_FRAME_VIEW_NONE;
        }

        $viewStatus = null;
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $viewStatus = $this->mAction->execute($controller, $this->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $this->mRoot->mContext->mXoopsUser);
        }

        // The action's executeView* methods set the page title
        $render = $this->mRoot->mContext->mModule->getRenderTarget();

        switch ($viewStatus) {
            case STDCACHE_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_NONE:
                // Do nothing, action has already handled output
                break;
            default:
                trigger_error("Unknown view status: {$viewStatus} for action: " . $this->mActionName, E_USER_WARNING);
                // or default to an error view
                // $this->mAction->executeViewError($controller, $this->mRoot->mContext->mXoopsUser, $render);
                break;
        }
        return true;
    }
}
