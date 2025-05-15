<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/Action.class.php';  // Fixed path

define('STDCACHE_FRAME_PERFORM_SUCCESS', 1);
define('STDCACHE_FRAME_PERFORM_FAIL', 2);
define('STDCACHE_FRAME_INIT_SUCCESS', 3);

define('STDCACHE_FRAME_VIEW_NONE', 1);
define('STDCACHE_FRAME_VIEW_SUCCESS', 2);
define('STDCACHE_FRAME_VIEW_ERROR', 3);
define('STDCACHE_FRAME_VIEW_INDEX', 4);
define('STDCACHE_FRAME_VIEW_INPUT', 5);
define('STDCACHE_FRAME_VIEW_PREVIEW', 6);
define('STDCACHE_FRAME_VIEW_CANCEL', 7);

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
        $this->mAdminFlag = $admin;
        $this->mRoot = &XCube_Root::getSingleton();
        $this->mCreateAction = new XCube_Delegate();
        $this->mCreateAction->register('stdCache_ActionFrame.CreateAction');
        $this->mCreateAction->add([&$this, '_createAction']);
    }

    public function setActionName($name)
    {
        $this->mActionName = $name;
        $this->mRoot->mContext->setAttribute('actionName', $name);
        $this->mRoot->mContext->mModule->setAttribute('actionName', $name);
    }

    public function _createAction(&$actionFrame)
    {
        if (is_object($actionFrame->mAction)) {
            return;
        }

        $className = 'stdCache_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = XOOPS_MODULE_PATH . "/stdCache/admin/actions/{$fileName}.class.php";

        if (!file_exists($fileName)) {
            die();
        }

        require_once $fileName;

        if (XC_CLASS_EXISTS($className)) {
            $actionFrame->mAction = new $className($actionFrame->mAdminFlag);
        }
    }

    public function execute(&$controller)
    {
        $this->mCreateAction->call(new XCube_Ref($this));

        if (!(is_object($this->mAction) && $this->mAction instanceof stdCache_Action)) {
            die();
        }

        if ($this->mAction->isSecure() && !is_object($controller->mRoot->mContext->mXoopsUser)) {
            $controller->executeForward(XOOPS_URL . '/');
        }

        $this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig);

        if (!$this->mAction->hasPermission($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModuleConfig)) {
            $controller->executeForward(XOOPS_URL . '/');
        }

        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $viewStatus = $this->mAction->execute($controller, $controller->mRoot->mContext->mXoopsUser);
        } else {
            $viewStatus = $this->mAction->getDefaultView($controller, $controller->mRoot->mContext->mXoopsUser);
        }

        $render = $controller->mRoot->mContext->mModule->getRenderTarget();
        $render->setAttribute('xoops_pagetitle', $this->mAction->getPageTitle());

        switch ($viewStatus) {
            case STDCACHE_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
            case STDCACHE_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $render);
                break;
        }

        return true;
    }
}
