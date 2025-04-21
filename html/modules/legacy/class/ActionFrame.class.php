<?php
/**
 *
 * @package     Legacy
 * @author      Nobuhiro YASUTOMI, PHP8
 * @version     $Id: ActionFrame.class.php,v 1.3 2008/09/25 15:11:25 kilica Exp $
 * @copyright   (c) 2005-2025 The XOOPSCube Project
 * @license     GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

const LEGACY_FRAME_PERFORM_SUCCESS = 1;
const LEGACY_FRAME_PERFORM_FAIL = 2;
const LEGACY_FRAME_INIT_SUCCESS = 3;

const LEGACY_FRAME_VIEW_NONE = 1;
const LEGACY_FRAME_VIEW_SUCCESS = 2;
const LEGACY_FRAME_VIEW_ERROR = 3;
const LEGACY_FRAME_VIEW_INDEX = 4;
const LEGACY_FRAME_VIEW_INPUT = 5;
const LEGACY_FRAME_VIEW_PREVIEW = 6;
const LEGACY_FRAME_VIEW_CANCEL = 7;
//
// Constants for the mode of the frame.
//
const LEGACY_FRAME_MODE_MISC = 'Misc';
const LEGACY_FRAME_MODE_NOTIFY = 'Notify';
const LEGACY_FRAME_MODE_IMAGE = 'Image';
const LEGACY_FRAME_MODE_SEARCH = 'Search';


class Legacy_ActionFrame
{
    public $mActionName = null;
    public $mAction = null;
    public $mAdminFlag = null;

    /**
     * Mode. The rule refers this property to load a file and create an
     * instance in execute().
     *
     * @var string
     */
    public $mMode = null;

    /**
     * @var XCube_Delegate
     */
    public $mCreateAction = null;

    public function Legacy_ActionFrame($admin)
    {
        self::__construct($admin);
    }

    public function __construct($admin)
    {
        $this->mAdminFlag = $admin;
        $this->mCreateAction =new XCube_Delegate();
        $this->mCreateAction->register('Legacy_ActionFrame.CreateAction');
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

    /**
     * Set mode.
     *
     * @param string $mode   Use constants (LEGACY_FRAME_MODE_MISC and more...)
     */
    public function setMode($mode)
    {
        $this->mMode = $mode;
    }

    public function _createAction(&$actionFrame)
    {
        if (is_object($actionFrame->mAction)) {
            return;
        }

        //
        // Create action object by mActionName
        //
        $className = 'Legacy_' . ucfirst($actionFrame->mActionName) . 'Action';
        $fileName = ucfirst($actionFrame->mActionName) . 'Action';
        if ($actionFrame->mAdminFlag) {
            $fileName = XOOPS_MODULE_PATH . "/legacy/admin/actions/{$fileName}.class.php";
        } else {
            $fileName = XOOPS_MODULE_PATH . "/legacy/actions/{$fileName}.class.php";
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
        if (strlen($this->mActionName) > 0 && !preg_match("/^\w+$/", $this->mActionName)) {
            die();
        }

        //
        // Actions of the public side in this module are hook type. So it's
        // necessary to load catalog here.
        //
        if (!$this->mAdminFlag) {
            $controller->mRoot->mLanguageManager->loadModuleMessageCatalog('legacy');
        }

        //
        // Add mode.
        //
        $this->setActionName($this->mMode . $this->mActionName);

        //
        // Create action object by mActionName
        //
        $this->mCreateAction->call(new XCube_Ref($this));

        if (!(is_object($this->mAction) && $this->mAction instanceof \Legacy_Action)) {
            die();    //< TODO
        }

        if ($this->mAction->prepare($controller, $controller->mRoot->mContext->mXoopsUser) === false) {
            die();    //< TODO
        }

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
            case LEGACY_FRAME_VIEW_SUCCESS:
                $this->mAction->executeViewSuccess($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case LEGACY_FRAME_VIEW_ERROR:
                $this->mAction->executeViewError($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case LEGACY_FRAME_VIEW_INDEX:
                $this->mAction->executeViewIndex($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case LEGACY_FRAME_VIEW_INPUT:
                $this->mAction->executeViewInput($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case LEGACY_FRAME_VIEW_PREVIEW:
                $this->mAction->executeViewPreview($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;

            case LEGACY_FRAME_VIEW_CANCEL:
                $this->mAction->executeViewCancel($controller, $controller->mRoot->mContext->mXoopsUser, $controller->mRoot->mContext->mModule->getRenderTarget());
                break;
        }
    }
}


class Legacy_Action
{
    /**
     * @access private
     */
    public $_mAdminFlag = false;

    public function Legacy_Action($adminFlag = false)
    {
        self::__construct($adminFlag);
    }

    public function __construct($adminFlag = false)
    {
        $this->_mAdminFlag = $adminFlag;
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        if ($this->_mAdminFlag) {
            return $controller->mRoot->mContext->mUser->isInRole('Module.legacy.Admin');
        } else {
            //
            // TODO Really?
            //
            return true;
        }
    }

    public function prepare(&$controller, &$xoopsUser)
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return LEGACY_FRAME_VIEW_NONE;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return LEGACY_FRAME_VIEW_NONE;
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
