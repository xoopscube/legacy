<?php
/**
 * @package    profile
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractAction
{
    public $mRoot = null;
    public $mModule = null;
    public $mAsset = null;

    /**
     * @public
     */
    public function &_getHandler()
    {
    }
    public function Profile_AbstractAction()
    {
        self::__construct();
    }
    public function __construct()
    {
        $this->mRoot =& XCube_Root::getSingleton();
        $this->mModule =& $this->mRoot->mContext->mModule;
        $this->mAsset =& $this->mModule->mAssetManager;
    }
    public function isMemberOnly()
    {
        return false;
    }
    public function isAdminOnly()
    {
        return false;
    }
    public function prepare()
    {
        return true;
    }

    public function hasPermission()
    {
        return true;
    }

    public function getDefaultView()
    {
        return Profile_FRAME_VIEW_NONE;
    }

    public function execute()
    {
        return Profile_FRAME_VIEW_NONE;
    }

    public function executeViewSuccess(&$controller, &$render)
    {
    }

    public function executeViewError(&$render)
    {
    }

    public function executeViewIndex(&$render)
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
